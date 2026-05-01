@extends('layouts.app')

@section('title', 'Judge Dashboard')

@section('content')
<div class="page-header">
    <h1>Judge Dashboard</h1>
</div>

@if(Session::has('login_success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> Welcome back, {{ Auth::user()->name }}! You have successfully logged in.
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($event)
<!-- Event Card -->
<div class="card">
    <div class="flex justify-between items-center flex-wrap gap-3">
        <div>
            <h2 class="mb-1">{{ $event->name }}</h2>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-alt"></i> {{ $event->date }}
            </p>
        </div>
        <button type="button" class="btn-icon btn-icon-view" onclick="showEventDetails()" title="View Event Details">
            <i class="fas fa-eye"></i>
        </button>
    </div>
</div>

<!-- Event Details Modal -->
<div id="eventModal" class="modal">
    <div class="modal-content flex flex-col">
        <div class="flex justify-between items-center mb-3">
            <h2 class="mb-0">Event Details</h2>
            <button type="button" class="modal-close" onclick="closeEventModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div style="overflow-y: auto; flex: 1;">
            @if($event->banner)
            <div class="form-group">
                <img src="{{ asset('storage/' . $event->banner) }}" alt="{{ $event->name }}" class="w-full" style="max-height: 180px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;">
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
    <div class="flex justify-between items-center mb-3">
        <h2 class="mb-0">Contestants</h2>
        <span class="badge badge-secondary">{{ $contestants->count() }}</span>
    </div>
    
    @if($contestants->count() > 0)
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
        @foreach($contestants as $contestant)
        <div onclick="showContestantDetails({{ $contestant->id }})" style="text-align: center; border: 1px solid var(--color-border); border-radius: 12px; overflow: hidden; cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
            @if($contestant->image_url)
            <img src="{{ $contestant->image_url }}" alt="{{ $contestant->name }}" class="w-full" style="height: 200px; object-fit: cover;">
            @else
            <div class="user-avatar w-full" style="height: 200px; font-size: 4rem; display: flex; align-items: center; justify-content: center; border: none; border-radius: 0;">
                {{ strtoupper(substr($contestant->name, 0, 1)) }}
            </div>
            @endif
            <div class="p-3" style="background: var(--color-white);">
                <p style="font-weight: 600; margin: 0; font-size: 1rem;">{{ $contestant->name }}</p>
                @if($contestant->number)
                <p class="text-muted mt-1" style="font-size: 0.85rem; margin-bottom: 0;">#{{ $contestant->number }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <p class="text-center text-muted">No contestants found for this event.</p>
    @endif
</div>

<!-- Contestant Details Modals -->
@foreach($contestants as $contestant)
<div id="contestantModal{{ $contestant->id }}" class="modal">
    <div class="modal-content" style="padding: 0; display: grid; grid-template-columns: 200px 1fr;">

        {{-- LEFT COLUMN: Image + number overlay --}}
        <div style="position: relative; min-height: 280px; overflow: hidden; border-top-left-radius: 12px; border-bottom-left-radius: 12px;">
            @if($contestant->image_url)
                <img src="{{ $contestant->image_url }}" alt="{{ $contestant->name }}" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; bottom: 0;">
            @else
                <div class="user-avatar w-full" style="height: 100%; min-height: 280px; font-size: 4rem; position: absolute; top: 0; left: 0; display: flex; align-items: center; justify-content: center; border: none; border-radius: 0;">
                    {{ strtoupper(substr($contestant->name, 0, 1)) }}
                </div>
            @endif

            @if($contestant->number)
                <div style="position: absolute; top: 0.65rem; left: 0.65rem; background: var(--color-btn); color: #fff; font-size: 1rem; font-weight: 700; padding: 0.35rem 0.75rem; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.3); z-index: 2;">
                    #{{ $contestant->number }}
                </div>
            @endif
        </div>

        {{-- RIGHT COLUMN: Details panel --}}
        <div class="flex flex-col gap-3" style="padding: 1.5rem 1.5rem 1.5rem 1.25rem; overflow-y: auto; max-height: 85vh;">
            <div>
                <h2 class="mb-0" style="font-size: 1.15rem; font-weight: 700;">Contestant Details</h2>
            </div>
            <div>
                <p class="text-muted mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">Name</p>
                <p class="mb-0" style="font-size: 1.35rem; font-weight: 700; line-height: 1.2;">{{ $contestant->name }}</p>
            </div>
            @if($contestant->category)
            <div>
                <p class="text-muted mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">Category</p>
                <p class="mb-0" style="font-size: 1rem;">{{ $contestant->category }}</p>
            </div>
            @endif
            @if($contestant->description)
            <div>
                <p class="text-muted mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">Description</p>
                <p class="mb-0" style="font-size: 0.95rem; line-height: 1.6;">{{ $contestant->description }}</p>
            </div>
            @endif
        </div>

        {{-- CLOSE BUTTON --}}
        <button type="button" class="modal-close" onclick="closeContestantModal({{ $contestant->id }})" style="top: 0.75rem; right: 0.75rem;">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endforeach

@else
<div class="card">
    <p class="text-center text-muted">No event assigned to you yet. Please contact the administrator.</p>
</div>
@endif

<style>
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
    document.getElementById('eventModal').classList.add('active');
}

function closeEventModal() {
    document.getElementById('eventModal').classList.remove('active');
}

function showContestantDetails(id) {
    document.getElementById('contestantModal' + id).classList.add('active');
}

function closeContestantModal(id) {
    document.getElementById('contestantModal' + id).classList.remove('active');
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
}
</script>
@endsection
