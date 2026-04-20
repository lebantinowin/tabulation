@extends('layouts.app')

@section('title', 'Create Event - Admin')

@section('content')
<div class="page-header">
    <h1>Create New Event</h1>
    <a href="{{ route('events.index') }}" class="btn">Back to List</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label for="name">Event Name</label>
            <input type="text" id="name" name="name" required>
        </div>
        
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"></textarea>
        </div>
        
        <div class="form-group">
            <label for="banner">Banner Image</label>
            <input type="file" id="banner" name="banner" accept="image/*">
            <small style="color: var(--color-muted);">Recommended size: 1920x400 pixels</small>
        </div>
        
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="upcoming">Upcoming</option>
                <option value="ongoing">Ongoing</option>
                <option value="completed">Completed</option>
            </select>
        </div>
        
        <div class="actions">
            <button type="submit" class="btn">Create Event</button>
            <a href="{{ route('events.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
