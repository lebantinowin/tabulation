<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $events = Event::all();
        return view('admin.events.index', compact('events'));
    }

    // Show the form for creating a new resource.
    public function create()
    {
        return view('admin.events.create');
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'status' => 'required|in:upcoming,ongoing,completed',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();

        // Handle banner image upload
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('banners', 'public');
            $data['banner'] = $bannerPath;
        }

        $event = Event::create($data);

        // Log the event creation
        AuditLog::log('event_created', 'Created event: ' . $event->name . ' (Status: ' . $event->status . ')');

        return redirect()->route('events.index')
            ->with('success', 'Event created successfully.');
    }

    // Display the specified resource.
    public function show(Event $event)
    {
        return view('admin.events.show', compact('event'));
    }

    // Show the form for editing the specified resource.
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    // Update the specified resource in storage.
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'status' => 'required|in:upcoming,ongoing,completed',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $oldData = $event->toArray();
        $data = $request->all();

        // Handle banner image upload
        if ($request->hasFile('banner')) {
            // Delete old banner if exists
            if ($event->banner) {
                Storage::disk('public')->delete($event->banner);
            }
            $bannerPath = $request->file('banner')->store('banners', 'public');
            $data['banner'] = $bannerPath;
        }

        $event->update($data);

        // Build description of changes
        $changes = [];
        if ($oldData['name'] !== $event->name) {
            $changes[] = 'name: "' . $oldData['name'] . '" to "' . $event->name . '"';
        }
        if ($oldData['date'] !== $event->date) {
            $changes[] = 'date: "' . $oldData['date'] . '" to "' . $event->date . '"';
        }
        if ($oldData['status'] !== $event->status) {
            $changes[] = 'status: "' . $oldData['status'] . '" to "' . $event->status . '"';
        }
        if ($oldData['description'] !== $event->description) {
            $changes[] = 'description changed';
        }
        if ($event->wasChanged('banner')) {
            $changes[] = 'banner image updated';
        }

        $changeDescription = implode(', ', $changes);
        
        // Log the event update
        if (!empty($changeDescription)) {
            AuditLog::log('event_updated', 'Updated event: ' . $event->name . ' (' . $changeDescription . ')');
        } else {
            AuditLog::log('event_updated', 'Updated event: ' . $event->name);
        }

        return redirect()->route('events.index')
            ->with('success', 'Event updated successfully.');
    }

    // Remove the specified resource from storage.
    public function destroy(Event $event)
    {
        // Delete banner if exists
        if ($event->banner) {
            Storage::disk('public')->delete($event->banner);
        }

        $eventName = $event->name;
        $event->delete();

        // Log the event deletion
        AuditLog::log('event_deleted', 'Deleted event: ' . $eventName);

        return redirect()->route('events.index')
            ->with('success', 'Event deleted successfully.');
    }

    // Show the form for assigning judges to an event
    public function assignJudges(Event $event)
    {
        // Get all judges
        $allJudges = \App\Models\User::where('role', 'judge')->get();
        
        // Get judges already assigned to this event
        $assignedJudgeIds = \App\Models\User::where('event_id', $event->id)
            ->where('role', 'judge')
            ->pluck('id')
            ->toArray();

        return view('admin.events.assign-judges', compact('event', 'allJudges', 'assignedJudgeIds'));
    }

    // Store the assigned judges for an event
    public function storeAssignedJudges(Request $request, Event $event)
    {
        $request->validate([
            'judges' => 'required|array',
            'judges.*' => 'exists:users,id'
        ]);

        // First, unassign all judges from this event
        \App\Models\User::where('role', 'judge')
            ->where('event_id', $event->id)
            ->update(['event_id' => null]);

        // Then assign selected judges to this event
        foreach ($request->judges as $judgeId) {
            \App\Models\User::where('id', $judgeId)->update(['event_id' => $event->id]);
        }

        // Log the judge assignment
        $judgeNames = \App\Models\User::whereIn('id', $request->judges)->pluck('name')->implode(', ');
        AuditLog::log('judges_assigned', 'Assigned judges to event ' . $event->name . ': ' . $judgeNames);

        return redirect()->route('events.show', $event->id)
            ->with('success', 'Judges assigned successfully.');
    }

    public function archive(Event $event)
    {
        $event->update(['is_archived' => true]);
        AuditLog::log('event_archived', 'Archived event: ' . $event->name);
        return redirect()->back()->with('success', 'Event archived successfully.');
    }

    public function unarchive(Event $event)
    {
        $event->update(['is_archived' => false]);
        AuditLog::log('event_unarchived', 'Unarchived event: ' . $event->name);
        return redirect()->back()->with('success', 'Event unarchived successfully.');
    }

    public function resetScores(Event $event)
    {
        $contestantIds = \App\Models\Contestant::where('event_id', $event->id)->pluck('id');
        
        if ($contestantIds->isNotEmpty()) {
            \App\Models\Score::whereIn('contestant_id', $contestantIds)->forceDelete();
            \App\Models\Tabulation::whereIn('contestant_id', $contestantIds)->delete();
        }
        
        AuditLog::log('event_scores_reset', 'Reset all scores to zero for event: ' . $event->name);
        return redirect()->back()->with('success', 'All scores for this event have been successfully reset to zero.');
    }
}
