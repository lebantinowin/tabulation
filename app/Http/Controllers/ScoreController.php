<?php

namespace App\Http\Controllers;

use App\Models\Score;
use App\Models\Contestant;
use App\Models\Criteria;
use App\Models\Event;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScoreController extends Controller
{
    // Display a listing of the resource - only current judge's scores
    public function index(Request $request)
    {
        $eventId = $request->get('event_id');
        
        $scores = Score::with(['contestant', 'criteria', 'judge'])
            ->where('judge_id', Auth::id());
        
        if ($eventId) {
            $scores = $scores->whereHas('contestant', function ($query) use ($eventId) {
                $query->where('event_id', $eventId);
            });
        }
        
        $scores = $scores->get();
        
        $events = Event::where('is_archived', false)->get();
        $judges = \App\Models\User::where('role', 'judge')->get();
        
        return view('judge.scores.index', compact('scores', 'events', 'judges', 'eventId'));
    }

    // Show the form for creating a new resource.
    public function create(Request $request)
    {
        $eventId = $request->get('event_id');
        
        $contestants = $eventId ? Contestant::where('event_id', $eventId)->get() : Contestant::all();
        $criterias = $eventId ? Criteria::where('event_id', $eventId)->get() : Criteria::all();
        $events = Event::where('is_archived', false)->get();
        
        return view('judge.scores.create', compact('contestants', 'criterias', 'events', 'eventId'));
    }

    // Store a newly created resource in storage (auto-save).
    public function store(Request $request)
    {
        $request->validate([
            'contestant_id' => 'required|exists:contestants,id',
            'criteria_id' => 'required|exists:criterias,id',
        ]);

        $criteria = Criteria::findOrFail($request->criteria_id);

        $request->validate([
            'score' => 'required|numeric|min:0|max:' . ($criteria->max_points ?? 100),
        ]);

        $data = $request->all();
        $data['judge_id'] = Auth::id();

        $contestant = Contestant::find($request->contestant_id);
        $criteria = Criteria::find($request->criteria_id);

        if ($contestant->event_id !== Auth::user()->event_id) {
            return back()->with('error', 'Unauthorized: Contestant does not belong to your assigned event.');
        }

        // Check if score already exists for this judge, contestant, and criteria
        $existingScore = Score::where('judge_id', Auth::id())
            ->where('contestant_id', $request->contestant_id)
            ->where('criteria_id', $request->criteria_id)
            ->first();

        if ($existingScore) {
            if ($existingScore->is_locked) {
                return back()->with('error', 'Cannot update a locked score.');
            }
            $existingScore->update($data);
            
            // Audit log for score update
            AuditLog::log('score_update', 'Updated score for contestant "' . $contestant->name . '" criteria "' . $criteria->name . '"');
            
            return redirect()->route('scores.index')
                ->with('success', 'Score auto-saved successfully.');
        }

        Score::create($data);

        // Audit log for new score
        AuditLog::log('score_created', 'Submitted score for contestant "' . $contestant->name . '" criteria "' . $criteria->name . '"');

        return redirect()->route('scores.index')
            ->with('success', 'Score submitted successfully.');
    }

    // Lock scores - with "Are you sure?" validation
    public function lock(Request $request)
    {
        $scoreIds = $request->input('score_ids', []);
        
        if (empty($scoreIds)) {
            return back()->with('error', 'No scores selected to lock.');
        }

        // Check if all selected scores have values
        $scores = Score::whereIn('id', $scoreIds)->get();
        
        // Get all criteria for the event
        $criteriaIds = $scores->pluck('criteria_id')->unique();
        
        // Check if all contestants have scores for all criteria
        $contestantIds = $scores->pluck('contestant_id')->unique();
        
        foreach ($contestantIds as $contestantId) {
            foreach ($criteriaIds as $criteriaId) {
                $hasScore = Score::where('contestant_id', $contestantId)
                    ->where('criteria_id', $criteriaId)
                    ->where('judge_id', Auth::id())
                    ->exists();
                    
                if (!$hasScore) {
                    return back()->with('error', 'Cannot lock: Missing score for contestant and criteria.');
                }
            }
        }

        // Lock all scores
        Score::whereIn('id', $scoreIds)->update([
            'is_locked' => true,
            'locked_at' => now(),
        ]);

        // Audit log for locking scores
        AuditLog::log('score_locked', 'Locked ' . count($scoreIds) . ' scores');

        return back()->with('success', 'Scores locked successfully.');
    }

    // Unlock scores (admin only or judge can unlock their own within grace period)
    public function unlock(Score $score)
    {
        // Only allow judge to unlock their own scores
        if ($score->judge_id != Auth::id() && !Auth::user()->isAdmin()) {
            return back()->with('error', 'Unauthorized to unlock this score.');
        }

        $contestant = $score->contestant;
        $criteria = $score->criteria;

        $score->update([
            'is_locked' => false,
            'locked_at' => null,
        ]);

        // Audit log for unlocking score
        AuditLog::log('score_unlocked', 'Unlocked score for contestant "' . $contestant->name . '" criteria "' . $criteria->name . '"');

        return back()->with('success', 'Score unlocked successfully.');
    }

    // Display the specified resource.
    public function show(Score $score)
    {
        if ($score->judge_id != Auth::id() && !Auth::user()->isAdmin()) {
            return back()->with('error', 'Unauthorized to view this score.');
        }

        return view('judge.scores.show', compact('score'));
    }

    // Show the form for editing the specified resource.
    public function edit(Score $score)
    {
        if ($score->judge_id != Auth::id() && !Auth::user()->isAdmin()) {
            return back()->with('error', 'Unauthorized to edit this score.');
        }

        if ($score->is_locked) {
            return back()->with('error', 'Cannot edit a locked score.');
        }

        $contestants = Contestant::all();
        $criterias = Criteria::all();
        return view('judge.scores.edit', compact('score', 'contestants', 'criterias'));
    }

    // Update the specified resource in storage.
    public function update(Request $request, Score $score)
    {
        if ($score->judge_id != Auth::id() && !Auth::user()->isAdmin()) {
            return back()->with('error', 'Unauthorized to update this score.');
        }

        if ($score->is_locked) {
            return back()->with('error', 'Cannot update a locked score.');
        }

        $request->validate([
            'contestant_id' => 'required|exists:contestants,id',
            'criteria_id' => 'required|exists:criterias,id',
        ]);

        $criteria = Criteria::findOrFail($request->criteria_id);

        $request->validate([
            'score' => 'required|numeric|min:0|max:' . ($criteria->max_points ?? 100),
        ]);

        $contestant = Contestant::find($request->contestant_id);

        if ($contestant->event_id !== Auth::user()->event_id) {
            return back()->with('error', 'Unauthorized: Contestant does not belong to your assigned event.');
        }

        $score->update($request->all());

        // Audit log for score update
        AuditLog::log('score_update', 'Updated score for contestant "' . $contestant->name . '" criteria "' . $criteria->name . '"');

        return redirect()->route('scores.index')
            ->with('success', 'Score updated successfully.');
    }

    // Remove the specified resource from storage.
    public function destroy(Score $score)
    {
        if ($score->judge_id != Auth::id() && !Auth::user()->isAdmin()) {
            return back()->with('error', 'Unauthorized to delete this score.');
        }

        if ($score->is_locked) {
            return back()->with('error', 'Cannot delete a locked score.');
        }

        $contestant = $score->contestant;
        $criteria = $score->criteria;

        $score->delete();

        // Audit log for score deletion
        AuditLog::log('score_deleted', 'Deleted score for contestant "' . $contestant->name . '" criteria "' . $criteria->name . '"');

        return redirect()->route('scores.index')
            ->with('success', 'Score deleted successfully.');
    }
}
