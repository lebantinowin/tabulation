@extends('layouts.app')

@section('title', 'Edit Contestant - Admin')

@section('content')
<div class="page-header">
    <h1>Edit Contestant</h1>
    <a href="{{ route('contestants.index') }}" class="btn">Back to List</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('contestants.update', $contestant->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="event_id">Event</label>
            <select id="event_id" name="event_id" required>
                @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ $contestant->event_id == $event->id ? 'selected' : '' }}>
                        {{ $event->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="name">Contestant Name</label>
            <input type="text" id="name" name="name" value="{{ $contestant->name }}" required>
        </div>
        
        <div class="form-group">
            <label for="number">Contestant Number</label>
            <input type="number" id="number" name="number" value="{{ $contestant->number }}" required>
        </div>
        
        <div class="form-group">
            <label for="category">Category</label>
            <input type="text" id="category" name="category" value="{{ $contestant->category }}">
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3">{{ $contestant->description }}</textarea>
        </div>
        
        <div class="form-group">
            <label for="image">Profile Picture</label>
            @php
            $imagePath = $contestant->image;
            $fullPath = '';
            $imageFound = false;
            
            // Build list of possible paths based on stored image path
            $possiblePaths = [];
            
            if ($imagePath) {
                // If the path already contains 'storage', use as-is
                if (str_contains($imagePath, 'storage/')) {
                    $possiblePaths[] = $imagePath;
                }
                // If the path has contestants prefix
                elseif (str_contains($imagePath, 'contestants/')) {
                    $possiblePaths[] = 'storage/' . $imagePath;
                    $possiblePaths[] = $imagePath;
                }
                // Just the filename
                else {
                    $possiblePaths[] = 'storage/contestants/' . $imagePath;
                    $possiblePaths[] = 'storage/' . $imagePath;
                    $possiblePaths[] = 'contestants/' . $imagePath;
                    $possiblePaths[] = $imagePath;
                }
            }
            
            foreach ($possiblePaths as $path) {
                if (file_exists(public_path($path))) {
                    $fullPath = $path;
                    $imageFound = true;
                    break;
                }
            }
            @endphp
            @if($imageFound && $fullPath)
                <img src="{{ asset($fullPath) }}" alt="{{ $contestant->name }}" class="img-thumbnail" style="margin-bottom: 0.5rem; max-width: 200px;">
            @endif
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        
        <div class="actions">
            <button type="submit" class="btn btn-primary">Update Contestant</button>
            <a href="{{ route('contestants.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
