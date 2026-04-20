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

    // Show the form for creating a new resource.
    public function create()
    {
        $events = Event::where('status', 'active')->get();
        $selectedEventId = request()->get('event_id');
        return view('admin.criteria.create', compact('events', 'selectedEventId'));
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0|max:100',
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
    public function show(Criteria $criterium)
    {
        return view('admin.criteria.show', compact('criterium'));
    }

    // Show the form for editing the specified resource.
    public function edit(Criteria $criterium)
    {
        $events = Event::where('status', 'active')->get();
        return view('admin.criteria.edit', compact('criterium', 'events'));
    }

    // Update the specified resource in storage.
    public function update(Request $request, Criteria $criterium)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        $criterium->update($request->all());

        return redirect()->route('criteria.index')
            ->with('success', 'Criteria updated successfully.');
    }

    // Remove the specified resource from storage.
    public function destroy(Criteria $criterium)
    {
        $eventId = $criterium->event_id;
        $criterium->delete();

        // Redirect back to the event show page if it was from an event
        if ($eventId) {
            return redirect()->route('events.show', $eventId)
                ->with('success', 'Criteria deleted successfully.');
        }

        return redirect()->route('criteria.index')
            ->with('success', 'Criteria deleted successfully.');
    }
}
