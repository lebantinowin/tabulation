@extends('layouts.app')

@section('title', 'Add Contestant - Admin')

@section('content')
<div class="page-header">
    <h1>Add New Contestant</h1>
    <a href="{{ route('contestants.index', $defaultEventId ? ['event_id' => $defaultEventId] : []) }}" class="btn">Back to List</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('contestants.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label for="event_id">Event</label>
            <select id="event_id" name="event_id" required>
                <option value="">Select Event</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ $defaultEventId == $event->id ? 'selected' : '' }}>{{ $event->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="name">Contestant Name</label>
            <input type="text" id="name" name="name" required>
        </div>
        
        <div class="form-group">
            <label for="number">Contestant Number</label>
            <input type="number" id="number" name="number" required>
        </div>
        
        <div class="form-group">
            <label for="category">Category</label>
            <input type="text" id="category" name="category" placeholder="e.g., Miss, Mister, Teen">
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"></textarea>
        </div>
        
        <div class="form-group">
            <label for="image">Profile Picture</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        
        <div class="actions">
            <button type="submit" class="btn btn-primary">Add Contestant</button>
            <a href="{{ route('contestants.index', $defaultEventId ? ['event_id' => $defaultEventId] : []) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
