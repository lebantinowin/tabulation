<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Models\Event;
use Illuminate\Http\Request;

class CriteriaController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $criterias = Criteria::with('event')->get();
        return view('admin.criteria.index', compact('criterias'));
    }

    public function create()
    {
        $events = Event::whereIn('status', ['upcoming', 'ongoing'])->get();
        $selectedEventId = request()->get('event_id');
        
        $eventWeights = [];
        foreach ($events as $event) {
            $currentWeight = Criteria::where('event_id', $event->id)->sum('weight');
            $eventWeights[$event->id] = max(0, 100 - $currentWeight);
        }

        return view('admin.criteria.create', compact('events', 'selectedEventId', 'eventWeights'));
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0|max:100',
            'max_points' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        Criteria::create($request->all());

        // Redirect back to the event show page if event_id was provided
        if ($request->has('event_id')) {
            return redirect()->route('events.show', $request->event_id)
                ->with('success', 'Criteria created successfully.');
        }

        return redirect()->route('criteria.index')
            ->with('success', 'Criteria created successfully.');
    }

    // Display the specified resource.
    public function show(Criteria $criteria)
    {
        return view('admin.criteria.show', compact('criteria'));
    }

    // Show the form for editing the specified resource.
    public function edit(Criteria $criteria)
    {
        $events = Event::whereIn('status', ['upcoming', 'ongoing'])->get();
        return view('admin.criteria.edit', compact('criteria', 'events'));
    }

    // Update the specified resource in storage.
    public function update(Request $request, Criteria $criteria)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0|max:100',
            'max_points' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $criteria->update($request->all());

        return redirect()->route('criteria.index')
            ->with('success', 'Criteria updated successfully.');
    }

    // Remove the specified resource from storage.
    public function destroy(Criteria $criteria)
    {
        $eventId = $criteria->event_id;
        $criteria->delete();

        if ($eventId) {
            return redirect()->route('events.show', $eventId)
                ->with('success', 'Criteria deleted successfully.');
        }

        return redirect()->route('criteria.index')
            ->with('success', 'Criteria deleted successfully.');
    }
}
