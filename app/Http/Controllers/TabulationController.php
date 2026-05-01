<?php

namespace App\Http\Controllers;

use App\Models\Tabulation;
use App\Models\Event;
use App\Models\Contestant;
use App\Models\Score;
use App\Models\Criteria;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class TabulationController extends Controller
{
    /**
     * Helper to compute and sort results efficiently.
     */
    private function computeResults(Event $event, $singleCriteriaId = null)
    {
        $contestants = Contestant::where('event_id', $event->id)->get();
        
        $criteriasQuery = Criteria::where('event_id', $event->id);
        if ($singleCriteriaId) {
            $criteriasQuery->where('id', $singleCriteriaId);
        }
        $criterias = $criteriasQuery->get();
        
        // Eager load all scores for this event to avoid N+1 queries
        $allScores = Score::whereHas('contestant', function ($q) use ($event) {
            $q->where('event_id', $event->id);
        })->get();

        // Get tabulation overrides
        $overrides = Tabulation::whereIn('contestant_id', $contestants->pluck('id'))->get()->keyBy('contestant_id');

        $results = [];

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
            
            $results[] = [
                'contestant'    => $contestant,
                'total_score'   => $totalWeightedScore,
                'average_score' => $contestantScores->avg('score') ?? 0,
                'criteria_scores' => $criteriaScores,
                'scores'        => $contestantScores,
                'is_overridden' => $isOverridden,
                'message'       => $tabulationRecord->message ?? null,
                'has_scores'    => $hasScores,
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

        return [$results, $criterias];
    }

    // Display tabulation results
    public function results(Request $request)
    {
        $eventId = $request->get('event_id');
        
        if ($eventId) {
            $event = Event::findOrFail($eventId);
            [$results, $criterias] = $this->computeResults($event);
            return view('admin.tabulation.results', compact('event', 'results', 'criterias'));
        }
        
        $events = Event::where('is_archived', false)->get();
        return view('admin.tabulation.results', compact('events'));
    }

    // Print all results (overall)
    public function print(Request $request)
    {
        $eventId = $request->get('event_id');
        
        if (!$eventId) {
            return back()->with('error', 'Please select an event.');
        }
        
        $event = Event::findOrFail($eventId);
        [$results, $criterias] = $this->computeResults($event);
        
        return view('admin.tabulation.print', compact('event', 'results'));
    }

    // Print results by category (criteria)
    public function printCategory(Request $request, $criteriaId)
    {
        $criteria = Criteria::findOrFail($criteriaId);
        $event = Event::findOrFail($criteria->event_id);
        
        [$results, $criterias] = $this->computeResults($event, $criteria->id);
        
        return view('admin.tabulation.print-category', compact('event', 'criteria', 'results'));
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
        $events = Event::where('is_archived', false)->orderBy('date', 'desc')->get();
        return view('results.index', compact('events'));
    }
    
    // Public results - shows results for a specific event
    public function publicResults(Event $event)
    {
        [$results, $criterias] = $this->computeResults($event);
        
        return view('results.show', compact('event', 'results', 'criterias'));
    }
}
