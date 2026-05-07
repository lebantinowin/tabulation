<?php

namespace App\Http\Controllers;

use App\Models\Contestant;
use App\Models\Event;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ContestantController extends Controller
{
    // Display public contestants list
    public function publicIndex(Request $request)
    {
        $eventId = $request->get('event_id');
        
        $contestants = Contestant::with('event');
        
        if ($eventId) {
            $contestants = $contestants->where('event_id', $eventId);
        }
        
        $contestants = $contestants->orderBy('number')->get();
        $events = Event::where('is_archived', false)->get();
        
        return view('contestants.public', compact('contestants', 'events'));
    }

    // Display a listing of the resource.
    public function index(Request $request)
    {
        $events = Event::orderBy('date', 'desc')->get();
        $selectedEventId = $request->get('event_id');

        $query = Contestant::with('event');
        
        if ($selectedEventId) {
            $query->where('event_id', $selectedEventId);
        }
        
        $contestants = $query->orderBy('number')->get();

        return view('admin.contestants.index', compact('contestants', 'events', 'selectedEventId'));
    }

    // Show the form for creating a new resource.
    public function create(Request $request)
    {
        $events = Event::where('is_archived', false)->get();
        $defaultEventId = $request->get('event_id');
        return view('admin.contestants.create', compact('events', 'defaultEventId'));
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'number' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('contestants', 'public');
            $data['image'] = $path;
        }

        $contestant = Contestant::create($data);

        AuditLog::log('contestant_created', "Created contestant: {$contestant->name}");

        return redirect()->route('contestants.index', ['event_id' => $request->event_id])
            ->with('success', 'Contestant created successfully.');
    }

    // Display the specified resource.
    public function show(Contestant $contestant)
    {
        return view('admin.contestants.show', compact('contestant'));
    }

    // Show the form for editing the specified resource.
    public function edit(Contestant $contestant)
    {
        $events = Event::where('is_archived', false)->get();
        return view('admin.contestants.edit', compact('contestant', 'events'));
    }

    // Update the specified resource in storage.
    public function update(Request $request, Contestant $contestant)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'number' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($contestant->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($contestant->image);
            }
            $path = $request->file('image')->store('contestants', 'public');
            $data['image'] = $path;
        }

        $contestant->update($data);

        AuditLog::log('contestant_updated', "Updated contestant: {$contestant->name}");

        return redirect()->route('contestants.index', ['event_id' => $request->event_id])
            ->with('success', 'Contestant updated successfully.');
    }

    // Remove the specified resource from storage.
    public function destroy(Contestant $contestant)
    {
        // Delete image if exists
        if ($contestant->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($contestant->image);
        }
        
        $name = $contestant->name;
        $contestant->delete();

        AuditLog::log('contestant_deleted', "Deleted contestant: {$name}");

        return redirect()->route('contestants.index', ['event_id' => $contestant->event_id])
            ->with('success', 'Contestant deleted successfully.');
    }
}
