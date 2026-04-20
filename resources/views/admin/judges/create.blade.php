@extends('layouts.app')

@section('title', 'Add Judge - Admin')

@section('content')
<div class="page-header">
    <h1>Add New Judge</h1>
    <a href="{{ route('judges.index') }}" class="btn">Back to List</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('judges.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label for="name">Judge Name</label>
            <input type="text" id="name" name="name" required>
        </div>
        
        {{-- <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>
        </div> --}}
        
        <div class="form-group">
            <label for="event_id">Assigned Event</label>
            <select id="event_id" name="event_id">
                <option value="">-- Select Event --</option>
                @foreach(\App\Models\Event::all() as $event)
                    <option value="{{ $event->id }}">{{ $event->name }} ({{ $event->date }})</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="image">Profile Photo</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        
        <div class="actions">
            <button type="submit" class="btn btn-primary">Add Judge</button>
            <a href="{{ route('judges.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
