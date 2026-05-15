@extends('layouts.app')

@section('title', 'Judge Dashboard')

@section('content')
<style>
/* ── Judge Welcome Popup ── */
#judge-welcome-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(4, 13, 18, 0.7);
    backdrop-filter: blur(8px);
    align-items: center;
    justify-content: center;
}
#judge-welcome-overlay.active { display: flex; animation: fadeInBg 0.35s ease; }
@keyframes fadeInBg  { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeOutBg { from { opacity: 1; } to { opacity: 0; } }

.welcome-popup {
    background: #fff;
    border-radius: 20px;
    width: 92%;
    max-width: 360px;
    overflow: hidden;
    box-shadow: 0 24px 64px rgba(0,0,0,0.3);
    animation: popIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
@keyframes popIn  { from { opacity:0; transform:scale(0.82) translateY(16px); } to { opacity:1; transform:scale(1) translateY(0); } }
@keyframes popOut { from { opacity:1; transform:scale(1); } to { opacity:0; transform:scale(0.88); } }

/* ── Photo strip ── */
.wp-photo-strip {
    background: linear-gradient(160deg, var(--color-btn,#0f2027) 0%, #1c3a4d 100%);
    padding: 2rem 1.5rem 1.25rem;
    text-align: center;
}
.wp-avatar {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255,255,255,0.25);
    margin-bottom: 0.9rem;
    display: block;
    margin-left: auto;
    margin-right: auto;
}
.wp-avatar-init {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    border: 3px solid rgba(255,255,255,0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    font-weight: 700;
    color: #fff;
    margin: 0 auto 0.9rem;
}
.wp-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: #fff;
    margin: 0 0 0.2rem;
    letter-spacing: -0.3px;
}
.wp-role {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.6);
    margin: 0;
}

/* ── Details ── */
.wp-details {
    padding: 1.25rem 1.5rem 0;
}
.wp-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.7rem 0;
    border-bottom: 1px solid #f2f2f2;
    font-size: 0.88rem;
}
.wp-row:last-child { border-bottom: none; }
.wp-label { color: #aaa; }
.wp-value { font-weight: 600; color: #1a1a1a; text-align: right; }
.wp-code {
    font-family: 'Courier New', monospace;
    letter-spacing: 3px;
    background: #f5f5f0;
    padding: 0.2rem 0.6rem;
    border-radius: 5px;
    font-size: 0.9rem;
    color: #040D12;
}

/* ── Footer button ── */
.wp-footer {
    padding: 1.25rem 1.5rem 1.5rem;
}
.wp-btn {
    width: 100%;
    padding: 0.8rem;
    background: var(--color-btn, #040D12);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s;
}
.wp-btn:hover { opacity: 0.85; }
</style>

<div class="page-header">
    <h1><i class="fas fa-tachometer-alt"></i> Judge Dashboard</h1>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(Session::has('login_success'))
@php
    $wJudge = Auth::user();
    $wEvent = $wJudge->event_id ? \App\Models\Event::find($wJudge->event_id) : null;
    $wImg   = $wJudge->image ? asset('storage/' . $wJudge->image) : null;
@endphp
<div id="judge-welcome-overlay" class="active">
    <div class="welcome-popup">

        {{-- Photo strip --}}
        <div class="wp-photo-strip">
            @if($wImg)
                <img src="{{ $wImg }}" alt="{{ $wJudge->name }}" class="wp-avatar">
            @else
                <div class="wp-avatar-init">{{ strtoupper(substr($wJudge->name, 0, 1)) }}</div>
            @endif
            <p class="wp-name">{{ $wJudge->name }}</p>
            <p class="wp-role">@if($wJudge->judge_number) Judge {{ $wJudge->judge_number }} &nbsp;·&nbsp; @endif Official Judge</p>
        </div>

        {{-- Details --}}
        <div class="wp-details">
            @if($wEvent)
            <div class="wp-row">
                <span class="wp-label">Event</span>
                <span class="wp-value">{{ $wEvent->name }}</span>
            </div>
            @endif
            <div class="wp-row">
                <span class="wp-label">Login Code</span>
                <span class="wp-value"><span class="wp-code">{{ $wJudge->login_code }}</span></span>
            </div>
        </div>

        {{-- Button --}}
        <div class="wp-footer">
            <button class="wp-btn" onclick="dismissWelcome()">
                Let's Go! &nbsp;<i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>
</div>
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
function dismissWelcome() {
    const overlay = document.getElementById('judge-welcome-overlay');
    if (overlay) {
        overlay.style.animation = 'fadeOutBg 0.3s ease forwards';
        overlay.querySelector('.welcome-popup').style.animation = 'popOut 0.3s ease forwards';
        setTimeout(() => {
            overlay.remove();
            @if(Session::has('needs_agreement') || !$judge->agreement_accepted)
                window.location.href = "{{ route('agreement') }}";
            @endif
        }, 320);
    }
}

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
