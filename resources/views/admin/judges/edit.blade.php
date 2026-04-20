@extends('layouts.app')

@section('title', 'Edit Judge - Admin')

@section('content')
<div class="page-header">
    <h1>Edit Judge</h1>
    <a href="{{ route('judges.index') }}" class="btn">Back to List</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('judges.update', $judge->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="name">Judge Name</label>
            <input type="text" id="name" name="name" value="{{ $judge->name }}" required>
        </div>
        
        {{-- <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="{{ $judge->email }}" required>
        </div> --}}
        
        <div class="form-group">
            <label for="event_id">Assigned Event</label>
            <select id="event_id" name="event_id">
                <option value="">-- Select Event --</option>
                @foreach(\App\Models\Event::all() as $event)
                    <option value="{{ $event->id }}" {{ $judge->event_id == $event->id ? 'selected' : '' }}>
                        {{ $event->name }} ({{ $event->date }})
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="image">Profile Photo</label>
            @php
            $imagePath = $judge->image;
            $fullPath = '';
            $imageFound = false;
            
            // Build list of possible paths based on stored image path
            $possiblePaths = [];
            
            if ($imagePath) {
                // If the path already contains 'storage', use as-is
                if (str_contains($imagePath, 'storage/')) {
                    $possiblePaths[] = $imagePath;
                }
                // If the path has judges prefix
                elseif (str_contains($imagePath, 'judges/')) {
                    $possiblePaths[] = 'storage/' . $imagePath;
                    $possiblePaths[] = $imagePath;
                }
                // Just the filename
                else {
                    $possiblePaths[] = 'storage/judges/' . $imagePath;
                    $possiblePaths[] = 'storage/' . $imagePath;
                    $possiblePaths[] = 'judges/' . $imagePath;
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
                <img src="{{ asset($fullPath) }}" alt="{{ $judge->name }}" class="img-thumbnail" style="margin-bottom: 0.5rem; max-width: 200px;">
            @endif
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        
        <div class="form-group">
            <label>Login Code</label>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="font-size: 1.25rem; font-weight: bold; letter-spacing: 4px; background: #f5f5f5; padding: 0.5rem 1rem; border-radius: 8px; border: 2px dashed var(--color-btn);">
                    {{ $judge->login_code ?? 'N/A' }}
                </span>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="regenerate_code" value="1" style="width: auto;">
                    <span>Regenerate Code</span>
                </label>
            </div>
            <small style="color: var(--color-muted);">Check to generate a new login code</small>
        </div>
        
        <div class="actions">
            <button type="submit" class="btn btn-primary">Update Judge</button>
            <a href="{{ route('judges.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
