<?php

namespace App\Http\Controllers;

use App\Models\Tabulation;
use App\Models\Event;
use App\Models\Contestant;
use App\Models\Score;
use App\Models\Criteria;
use Illuminate\Http\Request;

class TabulationController extends Controller
{
    // Display tabulation results
    public function results(Request $request)
    {
        $eventId = $request->get('event_id');
        
        if ($eventId) {
            $event = Event::findOrFail($eventId);
            $contestants = Contestant::where('event_id', $eventId)->get();
            $criterias = Criteria::where('event_id', $eventId)->get();
            
            $results = [];
            
            foreach ($contestants as $contestant) {
                $scores = Score::where('contestant_id', $contestant->id)->get();
                $totalScore = $scores->sum('score');
                $averageScore = $scores->avg('score');
                
                // Calculate score per criteria
                $criteriaScores = [];
                foreach ($criterias as $criteria) {
                    $criteriaScore = Score::where('contestant_id', $contestant->id)
                        ->where('criteria_id', $criteria->id)
                        ->get();
                    
                    $criteriaScores[$criteria->id] = [
                        'criteria' => $criteria,
                        'total' => $criteriaScore->sum('score'),
                        'average' => $criteriaScore->avg('score'),
                        'scores' => $criteriaScore,
                    ];
                }
                
                $results[] = [
                    'contestant' => $contestant,
                    'total_score' => $totalScore,
                    'average_score' => $averageScore,
                    'criteria_scores' => $criteriaScores,
                    'scores' => $scores,
                ];
            }
            
            // Sort by total score descending
            usort($results, function($a, $b) {
                return $b['total_score'] - $a['total_score'];
            });
            
            // Add rank
            foreach ($results as $index => $result) {
                $result['rank'] = $index + 1;
            }
            
            return view('admin.tabulation.results', compact('event', 'results', 'criterias'));
        }
        
        $events = Event::all();
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
        $contestants = Contestant::where('event_id', $eventId)->get();
        $criterias = Criteria::where('event_id', $eventId)->get();
        
        $results = [];
        
        foreach ($contestants as $contestant) {
            $scores = Score::where('contestant_id', $contestant->id)->get();
            $totalScore = $scores->sum('score');
            $averageScore = $scores->avg('score');
            
            $results[] = [
                'contestant' => $contestant,
                'total_score' => $totalScore,
                'average_score' => $averageScore,
            ];
        }
        
        // Sort by total score descending
        usort($results, function($a, $b) {
            return $b['total_score'] - $a['total_score'];
        });
        
        // Add rank
        foreach ($results as $index => $result) {
            $results[$index]['rank'] = $index + 1;
        }
        
        return view('admin.tabulation.print', compact('event', 'results'));
    }

    // Print results by category (criteria)
    public function printCategory(Request $request, $criteriaId)
    {
        $criteria = Criteria::findOrFail($criteriaId);
        $event = Event::findOrFail($criteria->event_id);
        $contestants = Contestant::where('event_id', $event->id)->get();
        
        $results = [];
        
        foreach ($contestants as $contestant) {
            $scores = Score::where('contestant_id', $contestant->id)
                ->where('criteria_id', $criteriaId)
                ->get();
            
            $totalScore = $scores->sum('score');
            $averageScore = $scores->avg('score');
            
            $results[] = [
                'contestant' => $contestant,
                'total_score' => $totalScore,
                'average_score' => $averageScore,
            ];
        }
        
        // Sort by total score descending
        usort($results, function($a, $b) {
            return $b['total_score'] - $a['total_score'];
        });
        
        // Add rank
        foreach ($results as $index => $result) {
            $results[$index]['rank'] = $index + 1;
        }
        
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

        return back()->with('success', 'Score overridden successfully.');
    }

    // Lock tabulation
    public function lock(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);

        $tabulations = Tabulation::whereHas('contestant', function($query) use ($request) {
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

        $tabulations = Tabulation::whereHas('contestant', function($query) use ($request) {
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

        $tabulation = Tabulation::updateOrCreate(
            ['contestant_id' => $request->contestant_id],
            ['message' => $request->message]
        );

        return back()->with('success', 'Message updated successfully.');
    }
    
    // Public index - shows list of events with their status (uses stored status)
    public function publicIndex()
    {
        // Use the stored status from the database instead of calculating
        $events = Event::orderBy('date', 'desc')->get();
        
        return view('results.index', compact('events'));
    }
    
    // Public results - shows results for a specific event (uses stored status)
    public function publicResults(Event $event)
    {
        $contestants = Contestant::where('event_id', $event->id)->get();
        $criterias = Criteria::where('event_id', $event->id)->get();
        
        $results = [];
        
        foreach ($contestants as $contestant) {
            $scores = Score::where('contestant_id', $contestant->id)->get();
            $totalScore = $scores->sum('score');
            $averageScore = $scores->avg('score');
            
            // Calculate score per criteria
            $criteriaScores = [];
            foreach ($criterias as $criteria) {
                $criteriaScore = Score::where('contestant_id', $contestant->id)
                    ->where('criteria_id', $criteria->id)
                    ->get();
                
                $criteriaScores[$criteria->id] = [
                    'criteria' => $criteria,
                    'total' => $criteriaScore->sum('score'),
                    'average' => $criteriaScore->avg('score'),
                ];
            }
            
            $results[] = [
                'contestant' => $contestant,
                'total_score' => $totalScore,
                'average_score' => $averageScore,
                'criteria_scores' => $criteriaScores,
            ];
        }
        
        // Sort by total score descending
        usort($results, function($a, $b) {
            return $b['total_score'] - $a['total_score'];
        });
        
        // Add rank
        foreach ($results as $index => $result) {
            $results[$index]['rank'] = $index + 1;
        }
        
        // Use stored status from database - no override needed
        return view('results.show', compact('event', 'results', 'criterias'));
    }
}
