@extends('layouts.app')

@section('title', 'Judge Dashboard')

@section('content')
<div class="page-header">
    <h1>Judge Dashboard</h1>
</div>

@if(Session::has('login_success'))
    <div class="alert alert-success" style="animation: fadeIn 0.5s ease;">
        <i class="fas fa-check-circle"></i> Welcome back, {{ Auth::user()->name }}! You have successfully logged in.
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@php
$judge = Auth::user();
$event = \App\Models\Event::find($judge->event_id);
$contestants = $event ? \App\Models\Contestant::where('event_id', $event->id)->get() : collect();
@endphp

@if($event)
<!-- Event Card -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="margin-bottom: 0.5rem;">{{ $event->name }}</h2>
            <p style="color: var(--color-muted); margin: 0;">
                <i class="fas fa-calendar-alt"></i> {{ $event->date }}
            </p>
        </div>
        <button type="button" class="btn-icon btn-icon-view" onclick="showEventDetails()" title="View Event Details">
            <i class="fas fa-eye"></i>
        </button>
    </div>
</div>

<!-- Event Details Modal -->
<div id="eventModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div class="modal-content" style="background: var(--color-white); padding: 2rem; border-radius: 12px; max-width: 500px; width: 90%; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-shrink: 0;">
            <h2 style="margin: 0;">Event Details</h2>
            <button type="button" onclick="closeEventModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div style="overflow-y: auto; flex: 1;">
            @if($event->banner)
            <div class="form-group">
                <img src="{{ asset('storage/' . $event->banner) }}" alt="{{ $event->name }}" style="width: 100%; max-height: 180px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;">
            </div>
            @endif
            <div class="form-group">
                <label>Event Name:</label>
                <p>{{ $event->name }}</p>
            </div>
            <div class="form-group">
                <label>Date:</label>
                <p>{{ $event->date }}</p>
            </div>
            @if($event->description)
            <div class="form-group">
                <label>Description:</label>
                <p>{{ $event->description }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Contestants Card -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2 style="margin: 0;">Contestants</h2>
        <span class="badge">{{ $contestants->count() }}</span>
    </div>
    
    @if($contestants->count() > 0)
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
        @foreach($contestants as $contestant)
        @php
        $imagePath = $contestant->image;
        $fullPath = '';
        $imageFound = false;
        
        // Build list of possible paths based on stored image path
        $possiblePaths = [];
        
        if ($imagePath) {
            if (str_contains($imagePath, 'storage/')) {
                $possiblePaths[] = $imagePath;
            }
            elseif (str_contains($imagePath, 'contestants/')) {
                $possiblePaths[] = 'storage/' . $imagePath;
                $possiblePaths[] = $imagePath;
            }
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
        <div onclick="showContestantDetails({{ $contestant->id }})" style="text-align: center; border: 1px solid var(--color-border); border-radius: 12px; overflow: hidden; cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
            @if($imageFound && $fullPath)
            <img src="{{ asset($fullPath) }}" alt="{{ $contestant->name }}" style="width: 100%; height: 200px; object-fit: cover;">
            @else
            <div class="user-avatar" style="width: 100%; height: 200px; font-size: 4rem; display: flex; align-items: center; justify-content: center;">
                {{ strtoupper(substr($contestant->name, 0, 1)) }}
            </div>
            @endif
            <div style="padding: 0.75rem; background: var(--color-white);">
                <p style="font-weight: 600; margin: 0; font-size: 1rem;">{{ $contestant->name }}</p>
                @if($contestant->number)
                <p style="color: var(--color-muted); font-size: 0.85rem; margin: 0.25rem 0 0;">#{{ $contestant->number }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <p class="text-center" style="color: var(--color-muted);">No contestants found for this event.</p>
    @endif
</div>

<!-- Contestant Details Modals -->
@foreach($contestants as $contestant)
<div id="contestantModal{{ $contestant->id }}"
     class="modal"
     style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.55); z-index: 9999;
            align-items: center; justify-content: center;">

    {{-- Card wrapper --}}
    <div class="modal-content"
         style="background: var(--color-white); border-radius: 16px;
                max-width: 540px; width: 90%; max-height: 85vh;
                overflow: hidden; position: relative;
                display: grid; grid-template-columns: 200px 1fr;">

        {{-- ── LEFT COLUMN: Image + number overlay ── --}}
        @php
            $modalImagePath   = $contestant->image;
            $modalFullPath    = '';
            $modalImageFound  = false;
            $modalPossiblePaths = [];

            if ($modalImagePath) {
                if (str_contains($modalImagePath, 'storage/')) {
                    $modalPossiblePaths[] = $modalImagePath;
                } elseif (str_contains($modalImagePath, 'contestants/')) {
                    $modalPossiblePaths[] = 'storage/' . $modalImagePath;
                    $modalPossiblePaths[] = $modalImagePath;
                } else {
                    $modalPossiblePaths[] = 'storage/contestants/' . $modalImagePath;
                    $modalPossiblePaths[] = 'storage/' . $modalImagePath;
                    $modalPossiblePaths[] = 'contestants/' . $modalImagePath;
                    $modalPossiblePaths[] = $modalImagePath;
                }
            }

            foreach ($modalPossiblePaths as $path) {
                if (file_exists(public_path($path))) {
                    $modalFullPath   = $path;
                    $modalImageFound = true;
                    break;
                }
            }
        @endphp

        <div style="position: relative; min-height: 280px; overflow: hidden;">

            {{-- Contestant image --}}
            @if($modalImageFound && $modalFullPath)
                <img src="{{ asset($modalFullPath) }}"
                     alt="{{ $contestant->name }}"
                     style="width: 100%; height: 100%; object-fit: cover;
                            position: absolute; top: 0; left: 0; bottom: 0;">
            @else
                <div class="user-avatar"
                     style="width: 100%; height: 100%; min-height: 280px;
                            font-size: 4rem; position: absolute;
                            top: 0; left: 0;
                            display: flex; align-items: center; justify-content: center;">
                    {{ strtoupper(substr($contestant->name, 0, 1)) }}
                </div>
            @endif

            {{-- Number badge overlay (top-left of image) --}}
            @if($contestant->number)
                <div style="position: absolute; top: 0.65rem; left: 0.65rem;
                            background: var(--color-btn); color: #fff;
                            font-size: 1rem; font-weight: 700;
                            padding: 0.35rem 0.75rem;
                            border-radius: 8px;
                            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
                            z-index: 2;">
                    #{{ $contestant->number }}
                </div>
            @endif
        </div>

        {{-- ── RIGHT COLUMN: Details panel ── --}}
        <div style="padding: 1.5rem 1.5rem 1.5rem 1.25rem;
                    display: flex; flex-direction: column; gap: 1rem;
                    overflow-y: auto; max-height: 85vh;">

            {{-- Header row --}}
            <div>
                <h2 style="margin: 0; font-size: 1.15rem; font-weight: 700;
                           color: var(--color-text);">
                    Contestant Details
                </h2>
            </div>

            {{-- Name --}}
            <div>
                <p style="font-size: 0.75rem; color: var(--color-muted);
                          text-transform: uppercase; letter-spacing: 1px; margin: 0 0 0.25rem;">
                    Name
                </p>
                <p style="font-size: 1.35rem; font-weight: 700; margin: 0;
                          color: var(--color-text); line-height: 1.2;">
                    {{ $contestant->name }}
                </p>
            </div>

            {{-- Category --}}
            @if($contestant->category)
            <div>
                <p style="font-size: 0.75rem; color: var(--color-muted);
                          text-transform: uppercase; letter-spacing: 1px; margin: 0 0 0.25rem;">
                    Category
                </p>
                <p style="font-size: 1rem; margin: 0; color: var(--color-text);">
                    {{ $contestant->category }}
                </p>
            </div>
            @endif

            {{-- Description --}}
            @if($contestant->description)
            <div>
                <p style="font-size: 0.75rem; color: var(--color-muted);
                          text-transform: uppercase; letter-spacing: 1px; margin: 0 0 0.25rem;">
                    Description
                </p>
                <p style="font-size: 0.95rem; margin: 0; line-height: 1.6;
                          color: var(--color-text);">
                    {{ $contestant->description }}
                </p>
            </div>
            @endif

        </div>{{-- end right column --}}

        {{-- ── CLOSE BUTTON — absolute top-right of card ── --}}
        <button type="button"
                onclick="closeContestantModal({{ $contestant->id }})"
                style="position: absolute; top: 0.75rem; right: 0.75rem;
                       background: #1a1a1a; color: #fff;
                       border: none; width: 32px; height: 32px;
                       border-radius: 50%; font-size: 1rem;
                       cursor: pointer; z-index: 10;
                       display: flex; align-items: center; justify-content: center;
                       box-shadow: 0 2px 6px rgba(0,0,0,0.3);
                       transition: background 0.2s ease;"
                onmouseover="this.style.background='#444'"
                onmouseout="this.style.background='#1a1a1a'">
            <i class="fas fa-times"></i>
        </button>

    </div>{{-- end card --}}
</div>{{-- end modal backdrop --}}
@endforeach

@else
<div class="card">
    <p class="text-center" style="color: var(--color-muted);">No event assigned to you yet. Please contact the administrator.</p>
</div>
@endif

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-content {
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.btn-icon-view {
    background: #697565;
}

.btn-icon-view:hover {
    background: #3C3D37;
}

@media (max-width: 480px) {
    .modal-content[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
    .modal-content[style*="grid-template-columns"] > div:first-child {
        min-height: 220px !important;
    }
}
</style>

<script>
function showEventDetails() {
    document.getElementById('eventModal').style.display = 'flex';
}

function closeEventModal() {
    document.getElementById('eventModal').style.display = 'none';
}

function showContestantDetails(id) {
    document.getElementById('contestantModal' + id).style.display = 'flex';
}

function closeContestantModal(id) {
    document.getElementById('contestantModal' + id).style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>
@endsection
