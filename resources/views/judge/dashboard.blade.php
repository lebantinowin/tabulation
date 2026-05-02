@extends('layouts.app')

@section('title', 'Judge Dashboard')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-tachometer-alt"></i> Judge Dashboard</h1>
</div>

@if(Session::has('login_success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> Welcome back, {{ Auth::user()->name }}! You have successfully logged in.
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($event)

{{-- ─── Event Banner ─── --}}
@php
    $bannerPath = $event->banner;
    $bannerFound = false;
    $bannerSrc = '';
    if ($bannerPath) {
        $tries = [
            public_path('storage/' . $bannerPath) => asset('storage/' . $bannerPath),
            public_path($bannerPath)               => asset($bannerPath),
        ];
        foreach ($tries as $disk => $url) {
            if (file_exists($disk)) { $bannerSrc = $url; $bannerFound = true; break; }
        }
    }
@endphp

<div class="card" style="padding: 0; overflow: hidden; margin-bottom: 1.5rem;">
    @if($bannerFound)
        <div style="position: relative;">
            <img src="{{ $bannerSrc }}" alt="{{ $event->name }}"
                 style="width: 100%; max-height: 240px; object-fit: cover; display: block;">
            <div style="position: absolute; bottom: 0; left: 0; right: 0;
                        background: linear-gradient(transparent, rgba(0,0,0,0.72));
                        padding: 1.5rem 1.5rem 1rem; color: #fff;">
                <h2 style="margin: 0 0 0.25rem; font-size: 1.5rem;">{{ $event->name }}</h2>
                <p style="margin: 0; opacity: 0.8; font-size: 0.9rem;">
                    <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}
                </p>
            </div>
        </div>
    @else
        <div style="background: linear-gradient(135deg, var(--color-btn) 0%, #1a2634 100%);
                    padding: 2rem 1.5rem; color: #fff;">
            <h2 style="margin: 0 0 0.25rem;">{{ $event->name }}</h2>
            <p style="margin: 0; opacity: 0.7; font-size: 0.9rem;">
                <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}
            </p>
        </div>
    @endif
    @if($event->description)
        <div style="padding: 0.75rem 1.25rem; font-size: 0.9rem; color: var(--color-muted);">
            {{ $event->description }}
        </div>
    @endif
</div>

{{-- ─── Contestants Grid ─── --}}
<div class="card">
    <div class="flex justify-between items-center mb-3">
        <h2 class="mb-0"><i class="fas fa-users"></i> Contestants</h2>
        <span class="badge badge-secondary">{{ $contestants->count() }}</span>
    </div>

    @if($contestants->count() > 0)
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 1rem;">
        @foreach($contestants as $contestant)
        <div onclick="showContestantDetails({{ $contestant->id }})"
             style="text-align: center; border: 1px solid var(--color-border); border-radius: 12px;
                    overflow: hidden; cursor: pointer; transition: all 0.25s ease;"
             onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.18)';"
             onmouseout="this.style.transform=''; this.style.boxShadow='';">
            @if($contestant->image_url)
                <img src="{{ $contestant->image_url }}" alt="{{ $contestant->name }}"
                     style="width: 100%; aspect-ratio: 3/4; object-fit: cover; display: block;">
            @else
                <div class="user-avatar" style="width: 100%; aspect-ratio: 3/4; font-size: 3rem;
                            display: flex; align-items: center; justify-content: center;
                            border: none; border-radius: 0;">
                    {{ strtoupper(substr($contestant->name, 0, 1)) }}
                </div>
            @endif
            <div style="padding: 0.6rem 0.5rem; background: var(--color-white);">
                <p style="font-weight: 600; margin: 0; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $contestant->name }}
                </p>
                @if($contestant->number)
                <p class="text-muted" style="font-size: 0.8rem; margin: 0.2rem 0 0;">#{{ $contestant->number }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <p class="text-center text-muted">No contestants found for this event.</p>
    @endif
</div>

{{-- ─── Contestant Detail Modals (Portrait) ─── --}}
@foreach($contestants as $contestant)
<div id="contestantModal{{ $contestant->id }}" class="modal">
    <div class="modal-content" style="padding: 0; max-width: 380px; width: 90%; overflow: hidden;">

        {{-- Portrait image top half --}}
        <div style="position: relative; width: 100%;">
            @if($contestant->image_url)
                <img src="{{ $contestant->image_url }}" alt="{{ $contestant->name }}"
                     style="width: 100%; aspect-ratio: 3/4; object-fit: cover; display: block;">
            @else
                <div class="user-avatar" style="width: 100%; height: 260px; font-size: 5rem;
                            display: flex; align-items: center; justify-content: center;
                            border: none; border-radius: 0;">
                    {{ strtoupper(substr($contestant->name, 0, 1)) }}
                </div>
            @endif

            {{-- Number badge --}}
            @if($contestant->number)
            <div style="position: absolute; top: 0.75rem; left: 0.75rem;
                        background: var(--color-btn); color: #fff; font-size: 1rem; font-weight: 700;
                        padding: 0.3rem 0.75rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                #{{ $contestant->number }}
            </div>
            @endif

            {{-- Close button --}}
            <button type="button" class="modal-close" onclick="closeContestantModal({{ $contestant->id }})"
                    style="top: 0.75rem; right: 0.75rem; position: absolute;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Info below image --}}
        <div style="padding: 1.25rem 1.5rem 1.5rem; background: var(--color-white);">
            <h2 style="font-size: 1.2rem; font-weight: 700; margin: 0 0 0.25rem;">{{ $contestant->name }}</h2>
            @if($contestant->category)
            <p class="text-muted" style="font-size: 0.85rem; margin: 0 0 0.75rem;">{{ $contestant->category }}</p>
            @endif
            @if($contestant->description)
            <p style="font-size: 0.9rem; line-height: 1.6; margin: 0; color: var(--color-muted);">{{ $contestant->description }}</p>
            @endif
        </div>
    </div>
</div>
@endforeach

@else
<div class="card">
    <p class="text-center text-muted">No event assigned to you yet. Please contact the administrator.</p>
</div>
@endif

<script>
function showContestantDetails(id) {
    document.getElementById('contestantModal' + id).classList.add('active');
}
function closeContestantModal(id) {
    document.getElementById('contestantModal' + id).classList.remove('active');
}
window.onclick = function(e) {
    if (e.target.classList.contains('modal')) e.target.classList.remove('active');
}
</script>
@endsection
