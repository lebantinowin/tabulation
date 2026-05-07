@extends('layouts.app')

@section('title', 'Edit Criteria - Admin')

@section('content')
<div class="page-header">
    <h1>Edit Criteria</h1>
    @if($criteria->event_id)
        <a href="{{ route('events.show', $criteria->event_id) }}#criteria" class="btn">Back to Event</a>
    @else
        <a href="{{ route('events.index') }}" class="btn">Back to Events</a>
    @endif
</div>

<div class="card">
    <form method="POST" action="{{ route('criteria.update', $criteria->id) }}">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="event_id">Event</label>
            <select id="event_id" name="event_id" required>
                @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ $criteria->event_id == $event->id ? 'selected' : '' }}>
                        {{ $event->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="name">Criteria Name</label>
            <input type="text" id="name" name="name" value="{{ $criteria->name }}" required>
        </div>
        
        <div class="form-group" style="display: flex; gap: 1rem;">
            <div style="flex: 1;">
                <label for="weight">Weight (%)</label>
                <input type="number" id="weight" name="weight" value="{{ $criteria->weight }}" min="0" max="100" required>
            </div>
            <div style="flex: 1;">
                <label for="max_points">Max Points</label>
                <input type="number" id="max_points" name="max_points" value="{{ $criteria->max_points ?? 100 }}" min="1" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3">{{ $criteria->description }}</textarea>
        </div>
        
        <div class="actions" style="margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Update Criteria</button>
            @if($criteria->event_id)
                <a href="{{ route('events.show', $criteria->event_id) }}#criteria" class="btn btn-secondary">Cancel</a>
            @else
                <a href="{{ route('events.index') }}" class="btn btn-secondary">Cancel</a>
            @endif
        </div>
@endsection
