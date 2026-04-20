@extends('layouts.app')

@section('title', 'Edit Event - Admin')

@section('content')
<div class="page-header">
    <h1>Edit Event</h1>
    <a href="{{ route('events.index') }}" class="btn">Back to List</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('events.update', $event->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="name">Event Name</label>
            <input type="text" id="name" name="name" value="{{ $event->name }}" required>
        </div>
        
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" value="{{ $event->date }}" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4">{{ $event->description }}</textarea>
        </div>
        
        <div class="form-group">
            <label for="banner">Banner Image</label>
            @if($event->banner)
                @php
                $bannerPath = $event->banner;
                $fullBannerPath = '';
                $bannerFound = false;
                
                $possibleBannerPaths = [];
                
                if ($bannerPath) {
                    if (str_contains($bannerPath, 'storage/')) {
                        $possibleBannerPaths[] = $bannerPath;
                    }
                    elseif (str_contains($bannerPath, 'banners/')) {
                        $possibleBannerPaths[] = 'storage/' . $bannerPath;
                        $possibleBannerPaths[] = $bannerPath;
                    }
                    else {
                        $possibleBannerPaths[] = 'storage/banners/' . $bannerPath;
                        $possibleBannerPaths[] = 'storage/' . $bannerPath;
                        $possibleBannerPaths[] = 'banners/' . $bannerPath;
                        $possibleBannerPaths[] = $bannerPath;
                    }
                }
                
                foreach ($possibleBannerPaths as $path) {
                    if (file_exists(public_path($path))) {
                        $fullBannerPath = $path;
                        $bannerFound = true;
                        break;
                    }
                }
                @endphp
                @if($bannerFound && $fullBannerPath)
                <div style="margin-bottom: 10px;">
                    <img src="{{ asset($fullBannerPath) }}" alt="Current Banner" style="max-width: 300px; border-radius: 8px;">
                </div>
                @endif
            @endif
            <input type="file" id="banner" name="banner" accept="image/*">
            <small style="color: var(--color-muted);">Recommended size: 1920x400 pixels. Leave empty to keep current image.</small>
        </div>
        
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="upcoming" {{ $event->status == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                <option value="ongoing" {{ $event->status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                <option value="completed" {{ $event->status == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
        
        <div class="actions">
            <button type="submit" class="btn">Update Event</button>
            <a href="{{ route('events.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
