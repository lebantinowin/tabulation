<?php

namespace App\Http\Controllers;

use App\Models\Tabulation;
use App\Models\Event;
use App\Models\Contestant;
use App\Models\Score;
use App\Models\Criteria;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TabulationController extends Controller
{
    /**
     * Helper to compute and sort results efficiently.
     */
    private function computeResults(Event $event, $singleCriteriaId = null, $specificJudgeId = null)
    {
        $contestants = Contestant::where('event_id', $event->id)->get();
        
        $criteriasQuery = Criteria::where('event_id', $event->id);
        if ($singleCriteriaId) {
            $criteriasQuery->where('id', $singleCriteriaId);
        }
        $criterias = $criteriasQuery->get();
        
        // Eager load all scores for this event to avoid N+1 queries
        $allScoresQuery = Score::whereHas('contestant', function ($q) use ($event) {
            $q->where('event_id', $event->id);
        });

        if (Auth::check() && Auth::user()->isJudge()) {
            $allScoresQuery->where('judge_id', Auth::id());
        } elseif ($specificJudgeId) {
            $allScoresQuery->where('judge_id', $specificJudgeId);
        }

        $allScores = $allScoresQuery->get();

        // Get tabulation overrides
        $overrides = Tabulation::whereIn('contestant_id', $contestants->pluck('id'))->get()->keyBy('contestant_id');

        $results = [];

        $totalAssignedJudges = User::where('role', 'judge')->where('event_id', $event->id)->count();

        foreach ($contestants as $contestant) {
            $contestantScores = $allScores->where('contestant_id', $contestant->id);
            $hasScores = $contestantScores->count() > 0;
            
            $totalWeightedScore = 0;
            $criteriaScores = [];
            
            foreach ($criterias as $criteria) {
                $scoresForCriteria = $contestantScores->where('criteria_id', $criteria->id);
                
                $average = $scoresForCriteria->avg('score') ?? 0;
                $total = $scoresForCriteria->sum('score') ?? 0;
                
                // Weight calculation
                $weightedScore = $average * ($criteria->weight / 100);
                
                if (!$singleCriteriaId) {
                    $totalWeightedScore += $weightedScore;
                } else {
                    $totalWeightedScore = $average; 
                }

                $criteriaScores[$criteria->id] = [
                    'criteria' => $criteria,
                    'average' => $average,
                    'total' => $total,
                    'scores' => $scoresForCriteria,
                ];
            }

            // Check for override
            $tabulationRecord = $overrides->get($contestant->id);
            if ($tabulationRecord && $tabulationRecord->total_score !== null && !$singleCriteriaId) {
                $totalWeightedScore = $tabulationRecord->total_score;
                $isOverridden = true;
                $hasScores = true; // treat overridden as scored
            } else {
                $isOverridden = false;
            }
            
            // Calculate how many judges have completely scored this contestant
            $judgeScoreCounts = $contestantScores->groupBy('judge_id')->map->count();
            $completedJudges = 0;
            foreach ($judgeScoreCounts as $cCount) {
                if ($cCount == $criterias->count() && $criterias->count() > 0) {
                    $completedJudges++;
                }
            }
            
            $results[] = [
                'contestant'    => $contestant,
                'total_score'   => $totalWeightedScore,
                'average_score' => $contestantScores->avg('score') ?? 0,
                'criteria_scores' => $criteriaScores,
                'scores'        => $contestantScores,
                'is_overridden' => $isOverridden,
                'message'       => $tabulationRecord->message ?? null,
                'has_scores'    => $hasScores,
                'completed_judges' => $completedJudges,
                'total_assigned_judges' => $totalAssignedJudges,
            ];
        }

        // Split into scored and unscored groups
        $scored   = array_filter($results, fn($r) => $r['has_scores']);
        $unscored = array_filter($results, fn($r) => !$r['has_scores']);

        // Sort scored: highest score first
        usort($scored, fn($a, $b) => $b['total_score'] <=> $a['total_score']);

        // Sort unscored: by contestant number ascending (null numbers go last)
        usort($unscored, function($a, $b) {
            $na = $a['contestant']->number;
            $nb = $b['contestant']->number;
            if ($na === null && $nb === null) return 0;
            if ($na === null) return 1;
            if ($nb === null) return -1;
            return $na <=> $nb;
        });

        // Add rank using Standard Competition Ranking (1224) — only for scored
        $currentRank = 1;
        $previousScore = null;
        $sameScoreCount = 0;

        foreach ($scored as &$result) {
            if ($previousScore !== null && abs($result['total_score'] - $previousScore) < 0.0001) {
                $result['rank'] = $currentRank;
                $sameScoreCount++;
            } else {
                $currentRank += $sameScoreCount;
                $result['rank'] = $currentRank;
                $previousScore = $result['total_score'];
                $sameScoreCount = 1;
            }
        }
        unset($result);

        // Unscored rows have no rank
        foreach ($unscored as &$result) {
            $result['rank'] = null;
        }
        unset($result);

        // Merge: scored first, then unscored
        $results = array_values(array_merge($scored, $unscored));

        // Get all judges assigned to this event
        $judges = User::where('role', 'judge')->where('event_id', $event->id)->orderBy('judge_number')->orderBy('name')->get();

        return [$results, $criterias, $judges];
    }

    // Display tabulation results
    public function results(Request $request)
    {
        $eventId = $request->get('event_id');
        
        if ($eventId) {
            $event = Event::findOrFail($eventId);
            [$results, $criterias, $judges] = $this->computeResults($event);
            return view('admin.tabulation.results', compact('event', 'results', 'criterias', 'judges'));
        }
        
        $events = Event::where('is_archived', false)->get();
        return view('admin.tabulation.results', compact('events'));
    }

    // Download overall results as PDF
    public function print(Request $request)
    {
        $eventId = $request->get('event_id');
        
        if (!$eventId) {
            return back()->with('error', 'Please select an event.');
        }
        
        $event = Event::findOrFail($eventId);
        [$results, $criterias, $eventJudges] = $this->computeResults($event);

        $adminName = $request->input('admin_name');
        $judges = $request->input('judges', []);

        $orientation = count($criterias) > 3 ? 'landscape' : 'portrait';

        $pdf = Pdf::loadView('admin.tabulation.print', compact('event', 'results', 'criterias', 'adminName', 'judges', 'eventJudges', 'orientation'))
            ->setPaper('a4', $orientation);

        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $event->name) . '_Overall_Results.pdf';
        
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    // Download results by category as PDF
    public function printCategory(Request $request, $criteriaId)
    {
        $criteria = Criteria::findOrFail($criteriaId);
        $event = Event::findOrFail($criteria->event_id);
        
        [$results, $criterias, $eventJudges] = $this->computeResults($event, $criteria->id);

        $adminName = $request->input('admin_name');
        $judges = $request->input('judges', []);

        $pdf = Pdf::loadView('admin.tabulation.print-category', compact('event', 'criteria', 'results', 'criterias', 'adminName', 'judges', 'eventJudges'))
            ->setPaper('a4', 'portrait');

        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $event->name) . '_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $criteria->name) . '_Results.pdf';
        
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    // Download results by specific judge as PDF
    public function printJudge(Request $request, $eventId, $judgeId)
    {
        $event = Event::findOrFail($eventId);
        $judge = User::where('role', 'judge')->findOrFail($judgeId);
        
        // Compute results but filter ONLY for this judge
        [$results, $criterias, $eventJudges] = $this->computeResults($event, null, $judge->id);

        $adminName = Auth::user()->name; // Auto-fill admin name

        $pdf = Pdf::loadView('admin.tabulation.print-judge', compact('event', 'judge', 'results', 'criterias', 'adminName'))
            ->setPaper('a4', 'portrait');

        $judgeLabel = $judge->judge_number ? 'J' . $judge->judge_number . '_' : '';
        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $event->name)
            . '_' . $judgeLabel
            . preg_replace('/[^A-Za-z0-9_\-]/', '_', $judge->name)
            . '_Scores.pdf';
        
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    // Export all results (overall) to CSV
    public function export(Request $request)
    {
        $eventId = $request->get('event_id');
        if (!$eventId) {
            return back()->with('error', 'Please select an event.');
        }
        
        $event = Event::findOrFail($eventId);
        [$results, $criterias, $eventJudges] = $this->computeResults($event);

        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $event->name) . "_Overall_Results.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=\"{$filename}\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Rank', 'Contestant Number', 'Contestant Name'];
        foreach ($criterias as $criteria) {
            $columns[] = $criteria->name . ' (' . $criteria->weight . '%)';
        }
        $columns[] = 'Total Score';
        $columns[] = 'Remarks';

        $callback = function() use ($results, $criterias, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($results as $result) {
                $row = [
                    $result['rank'] ?? 'N/A',
                    $result['contestant']->number ?? '',
                    $result['contestant']->name,
                ];
                foreach ($criterias as $criteria) {
                    $row[] = number_format($result['criteria_scores'][$criteria->id]['average'] ?? 0, 2);
                }
                $row[] = number_format($result['total_score'], 2);
                $row[] = $result['is_overridden'] ? 'Overridden' : '';

                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Export results by category to CSV
    public function exportCategory(Request $request, $criteriaId)
    {
        $criteria = Criteria::findOrFail($criteriaId);
        $event = Event::findOrFail($criteria->event_id);
        
        [$results, $criterias, $eventJudges] = $this->computeResults($event, $criteria->id);

        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $event->name) . "_" . preg_replace('/[^A-Za-z0-9_\-]/', '_', $criteria->name) . "_Results.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=\"{$filename}\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Rank', 'Contestant Number', 'Contestant Name', 'Score'];

        $callback = function() use ($results, $criteria, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $criteriaResults = [];
            foreach($results as $result) {
                $criteriaResults[] = [
                    'contestant' => $result['contestant'],
                    'average' => $result['criteria_scores'][$criteria->id]['average'] ?? 0,
                ];
            }
            usort($criteriaResults, function($a, $b) {
                return $b['average'] <=> $a['average'];
            });

            $rank = 1;
            foreach ($criteriaResults as $cr) {
                $row = [
                    $rank++,
                    $cr['contestant']->number ?? '',
                    $cr['contestant']->name,
                    number_format($cr['average'], 2)
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Override a contestant's score
    public function override(Request $request)
    {
        $request->validate([
            'contestant_id' => 'required|exists:contestants,id',
            'total_score' => 'required|numeric|min:0',
        ]);

        $tabulation = Tabulation::updateOrCreate(
            ['contestant_id' => $request->contestant_id],
            ['total_score' => $request->total_score]
        );

        $contestant = Contestant::find($request->contestant_id);
        AuditLog::log('score_override', "Admin overridden total score for contestant {$contestant->name} to {$request->total_score}");

        return back()->with('success', 'Score overridden successfully.');
    }

    // Lock tabulation
    public function lock(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);

        Tabulation::whereHas('contestant', function($query) use ($request) {
            $query->where('event_id', $request->event_id);
        })->update(['is_locked' => true]);

        return back()->with('success', 'Tabulation locked successfully.');
    }

    // Unlock tabulation
    public function unlock(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);

        Tabulation::whereHas('contestant', function($query) use ($request) {
            $query->where('event_id', $request->event_id);
        })->update(['is_locked' => false]);

        return back()->with('success', 'Tabulation unlocked successfully.');
    }

    // Set message
    public function message(Request $request)
    {
        $request->validate([
            'contestant_id' => 'required|exists:contestants,id',
            'message' => 'nullable|string',
        ]);

        Tabulation::updateOrCreate(
            ['contestant_id' => $request->contestant_id],
            ['message' => $request->message]
        );

        return back()->with('success', 'Message updated successfully.');
    }
    
    // Public index - shows list of events
    public function publicIndex()
    {
        if (Auth::check() && Auth::user()->isJudge()) {
            $judge = Auth::user();
            if ($judge->event_id) {
                return redirect()->route('results.show', $judge->event_id);
            }
        }
        
        $events = Event::where('is_archived', false)->orderBy('date', 'desc')->paginate(6);
        return view('results.index', compact('events'));
    }
    
    // Public results - shows results for a specific event
    public function publicResults(Event $event)
    {
        if (Auth::check() && Auth::user()->isJudge()) {
            if (Auth::user()->event_id !== $event->id) {
                abort(403, 'Unauthorized action.');
            }
        }
        
        [$results, $criterias, $judges] = $this->computeResults($event);
        
        return view('results.show', compact('event', 'results', 'criterias', 'judges'));
    }
}
