@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<style>
/* ── Admin Welcome Popup ── */
#admin-welcome-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(4, 13, 18, 0.7);
    backdrop-filter: blur(8px);
    align-items: center;
    justify-content: center;
}
#admin-welcome-overlay.active { display: flex; animation: fadeInBg 0.35s ease; }
@keyframes fadeInBg  { from { opacity: 0; } to { opacity: 1; } }
@keyframes fadeOutBg { from { opacity: 1; } to { opacity: 0; } }
.adm-popup {
    background: #fff;
    border-radius: 20px;
    width: 92%;
    max-width: 340px;
    overflow: hidden;
    box-shadow: 0 24px 64px rgba(0,0,0,0.28);
    animation: popIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
@keyframes popIn  { from { opacity:0; transform:scale(0.82); } to { opacity:1; transform:scale(1); } }
@keyframes popOut { from { opacity:1; transform:scale(1); } to { opacity:0; transform:scale(0.88); } }
.adm-header {
    background: linear-gradient(160deg, var(--color-btn,#0f2027) 0%, #1c3a4d 100%);
    padding: 2rem 1.5rem 1.5rem;
    text-align: center;
    color: #fff;
}
.adm-avatar {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    border: 3px solid rgba(255,255,255,0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.9rem;
    font-weight: 700;
    color: #fff;
    margin: 0 auto 0.85rem;
}
.adm-name { font-size: 1.2rem; font-weight: 700; margin: 0 0 0.2rem; }
.adm-role { font-size: 0.78rem; color: rgba(255,255,255,0.55); margin: 0; }
.adm-body { padding: 1.25rem 1.5rem 0; }
.adm-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.65rem 0;
    border-bottom: 1px solid #f2f2f2;
    font-size: 0.87rem;
}
.adm-row:last-child { border-bottom: none; }
.adm-label { color: #aaa; }
.adm-value { font-weight: 600; color: #1a1a1a; }
.adm-footer { padding: 1.25rem 1.5rem 1.5rem; }
.adm-btn {
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
.adm-btn:hover { opacity: 0.85; }
</style>

<div class="page-header">
    <h1>Admin Dashboard</h1>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(Session::has('login_success'))
<div id="admin-welcome-overlay" class="active">
    <div class="adm-popup">
        <div class="adm-header">
            <div class="adm-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <p class="adm-name">{{ Auth::user()->name }}</p>
            <p class="adm-role">System Administrator</p>
        </div>
        <div class="adm-body">
            <div class="adm-row">
                <span class="adm-label">Welcome back!</span>
                <span class="adm-value">You're logged in</span>
            </div>
            <div class="adm-row">
                <span class="adm-label">Email</span>
                <span class="adm-value">{{ Auth::user()->email }}</span>
            </div>
        </div>
        <div class="adm-footer">
            <button class="adm-btn" onclick="dismissAdminWelcome()">
                Go to Dashboard &nbsp;<i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>
</div>
<script>
function dismissAdminWelcome() {
    const ov = document.getElementById('admin-welcome-overlay');
    if (!ov) return;
    ov.style.animation = 'fadeOutBg 0.3s ease forwards';
    ov.querySelector('.adm-popup').style.animation = 'popOut 0.3s ease forwards';
    setTimeout(() => ov.remove(), 320);
}
</script>
@endif


<div class="grid gap-4" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
    <a href="{{ route('events.index') }}" class="card-clickable">
        <div class="card mb-0">
            <h3><i class="fas fa-calendar-alt"></i> Events</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--color-text);">{{ $eventCount }}</p>
        </div>
    </a>
    
    <a href="{{ route('contestants.index') }}" class="card-clickable">
        <div class="card mb-0">
            <h3><i class="fas fa-users"></i> Contestants</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--color-text);">{{ $contestantCount }}</p>
        </div>
    </a>
    
    <a href="{{ route('judges.index') }}" class="card-clickable">
        <div class="card mb-0">
            <h3><i class="fas fa-user-tie"></i> Judges</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--color-text);">{{ $judgeCount }}</p>
        </div>
    </a>
    
    <a href="{{ route('auditLogs.index') }}" class="card-clickable">
        <div class="card mb-0">
            <h3><i class="fas fa-clipboard-list"></i> Audit Logs</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--color-text);">{{ $auditLogCount }}</p>
        </div>
    </a>
</div>

<div class="card mt-4">
    <h2>Quick Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="{{ route('events.create') }}" class="btn" title="Create New Event">
            <i class="fas fa-plus"></i> Create Event
        </a>
        <a href="{{ route('contestants.create') }}" class="btn" title="Add New Contestant">
            <i class="fas fa-user-plus"></i> Add Contestant
        </a>
        <a href="{{ route('judges.create') }}" class="btn" title="Add New Judge">
            <i class="fas fa-user-tie"></i> Add Judge
        </a>
        <a href="{{ route('results.index') }}" class="btn" title="View Tabulation Results">
            <i class="fas fa-chart-bar"></i> View Results
        </a>
        <a href="{{ route('auditLogs.index') }}" class="btn" title="View Audit Logs">
            <i class="fas fa-clipboard-list"></i> Audit Logs
        </a>
    </div>
</div>


@endsection
