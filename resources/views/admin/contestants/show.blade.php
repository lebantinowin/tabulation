@extends('layouts.app')

@section('title', 'Contestant Details - Admin')

@section('content')
<div class="page-header">
    <h1>Contestant Details</h1>
    <a href="{{ route('contestants.index') }}" class="btn" title="Back to Contestants List">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="contestant-details-container" style="display: flex; min-height: 500px;">

        {{-- LEFT: Image column --}}
        <div class="contestant-image" style="flex: 0 0 350px; position: relative;">
            @php
                $imagePath = $contestant->image;
                $fullPath = '';
                $imageFound = false;
                $possiblePaths = [];

                if ($imagePath) {
                    if (str_contains($imagePath, 'storage/')) {
                        $possiblePaths[] = $imagePath;
                    } elseif (str_contains($imagePath, 'contestants/')) {
                        $possiblePaths[] = 'storage/' . $imagePath;
                        $possiblePaths[] = $imagePath;
                    } else {
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
                <img src="{{ asset($fullPath) }}" alt="{{ $contestant->name }}"
                     style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0;">
            @else
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
                            background: var(--color-muted); color: white; font-size: 5rem;
                            position: absolute; top: 0; left: 0;">
                    {{ strtoupper(substr($contestant->name, 0, 1)) }}
                </div>
            @endif
        </div>

        {{-- RIGHT: Details column --}}
        <div class="contestant-info"
             style="flex: 1; padding: 2rem; display: flex; flex-direction: column;
                    justify-content: center; position: relative;">

            {{-- Action buttons: top-right of the details panel --}}
            @if(auth()->user()->isSuperAdmin())
            <div class="actions"
                 style="position: absolute; top: 1rem; right: 1rem; display: flex; gap: 0.5rem;">
                <a href="{{ route('contestants.edit', $contestant->id) }}"
                   class="btn-icon btn-icon-edit" title="Edit Contestant">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('contestants.destroy', $contestant->id) }}"
                      method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn-icon btn-icon-delete"
                            onclick="confirmForm(this.closest('form'), 'This contestant and all their scores will be deleted.', {title: 'Delete Contestant?'})"
                            title="Delete Contestant">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
            @endif

            <h2>{{ $contestant->name }}</h2>

            <div class="form-group">
                <label>Number:</label>
                <p>
                    <span style="font-size: 1.5rem; font-weight: bold; letter-spacing: 2px;
                                 background: var(--color-btn); color: white;
                                 padding: 0.5rem 1rem; border-radius: 8px;">
                        #{{ $contestant->number }}
                    </span>
                </p>
            </div>

            <div class="form-group">
                <label>Category:</label>
                <p>{{ $contestant->category ?? 'N/A' }}</p>
            </div>

            <div class="form-group">
                <label>Event:</label>
                <p>{{ $contestant->event->name ?? 'N/A' }}</p>
            </div>

            <div class="form-group">
                <label>Description:</label>
                <p>{{ $contestant->description ?? 'No description provided' }}</p>
            </div>

        </div>
    </div>
</div>

<style>
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        color: white;
    }

    .btn-icon-edit {
        background: #D4A574;
    }

    .btn-icon-edit:hover {
        background: #b8956a;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .btn-icon-delete {
        background: #8B4513;
    }

    .btn-icon-delete:hover {
        background: #6B3410;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    @media (max-width: 768px) {
        .contestant-details-container {
            flex-direction: column !important;
        }
        .contestant-image {
            flex: 0 0 300px !important;
            min-height: 300px;
        }
    }
</style>
@endsection