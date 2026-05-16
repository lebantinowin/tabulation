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

/* ── Heatmap Styles ── */
.heatmap-container {
    background: var(--color-white);
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid var(--color-border);
    margin-top: 1.5rem;
}
.heatmap-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}
.heatmap-header h2 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--color-text);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.heatmap-scroll-wrapper {
    width: 100%;
}
.heatmap-grid {
    display: grid;
    grid-template-rows: repeat(7, 1fr);
    grid-auto-flow: column;
    grid-gap: 3px;
    width: 100%;
}
.heatmap-cell {
    width: 100%;
    aspect-ratio: 1/1;
    border-radius: 2px;
    background-color: rgba(0, 0, 0, 0.05);
    position: relative;
    cursor: pointer;
    transition: transform 0.1s;
}
.heatmap-cell:hover {
    transform: scale(1.2);
    z-index: 10;
    box-shadow: 0 0 5px rgba(0,0,0,0.2);
}
/* Levels using dark/green aesthetic */
.heatmap-cell[data-level="1"] { background-color: #c6e48b; }
.heatmap-cell[data-level="2"] { background-color: #7bc96f; }
.heatmap-cell[data-level="3"] { background-color: #239a3b; }
.heatmap-cell[data-level="4"] { background-color: #196127; }

.heatmap-wrapper {
    display: flex;
    width: 100%;
}
.heatmap-days {
    display: grid;
    grid-template-rows: repeat(7, 1fr);
    grid-gap: 3px;
    margin-right: 8px;
    font-size: 10px;
    color: var(--color-muted);
    text-align: right;
    align-items: center;
}
.heatmap-day { visibility: hidden; }
.heatmap-day:nth-child(2),
.heatmap-day:nth-child(4),
.heatmap-day:nth-child(6) {
    visibility: visible;
}

.heatmap-legend {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    color: var(--color-muted);
    justify-content: flex-end;
    margin-top: 15px;
    padding-right: 1rem;
}
.legend-cell {
    width: 11px;
    height: 11px;
    border-radius: 2px;
}
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
    
    <a href="{{ route('documents.index') }}" class="card-clickable">
        <div class="card mb-0">
            <h3><i class="fas fa-folder-open"></i> Documents</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--color-text);">{{ $eventCount }}</p>
        </div>
    </a>
</div>

<!-- Activity Heatmap -->
<div class="heatmap-container">
    <div class="heatmap-header">
        <h2>
            <i class="fas fa-chart-area text-muted"></i> 
            {{ auth()->user()->isSuperAdmin() ? 'Tabulation Activity' : 'Contribution Activity' }}
            @if(count($availableYears) > 1)
                <form action="{{ url()->current() }}" method="GET" style="display: inline-block; margin-left: 8px;">
                    <select name="year" onchange="this.form.submit()" style="border: none; background: transparent; font-size: 1.1rem; color: var(--color-muted); cursor: pointer; outline: none; padding: 0; font-weight: 500; font-family: inherit;">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </form>
            @else
                <span style="color: var(--color-muted); font-size: 1rem; font-weight: normal; margin-left: 8px;">{{ $selectedYear }}</span>
            @endif
        </h2>
    </div>
    
    <div class="heatmap-scroll-wrapper">
        @php
            $monthLabels = [];
            $colIndex = 0;
            $lastMonth = '';
            
            // Generate columns (weeks)
            $weeks = array_chunk($heatmap, 7);
            foreach($weeks as $week) {
                $month = null;
                foreach($week as $day) {
                    if ($day['is_first_of_month']) {
                        $month = $day['month'];
                        break;
                    }
                }
                
                if ($colIndex === 0) {
                    $month = 'Jan';
                }
                
                if ($month && $month != $lastMonth) {
                    $monthLabels[] = ['name' => $month, 'col' => $colIndex];
                    $lastMonth = $month;
                }
                $colIndex++;
            }
        @endphp
        
        <div style="position: relative; height: 16px; margin-left: 32px; font-size: 10px; color: var(--color-muted);">
            @foreach($monthLabels as $label)
                <span style="position: absolute; left: {{ ($label['col'] / count($weeks)) * 100 }}%;">{{ $label['name'] }}</span>
            @endforeach
        </div>
        
        <div class="heatmap-wrapper">
            <div class="heatmap-days" style="width: 24px; flex-shrink: 0;">
                <div class="heatmap-day">Sun</div>
                <div class="heatmap-day">Mon</div>
                <div class="heatmap-day">Tue</div>
                <div class="heatmap-day">Wed</div>
                <div class="heatmap-day">Thu</div>
                <div class="heatmap-day">Fri</div>
                <div class="heatmap-day">Sat</div>
            </div>
            <div class="heatmap-grid" style="grid-template-columns: repeat({{ count($weeks) }}, 1fr);">
                @foreach($heatmap as $day)
                    <div class="heatmap-cell" 
                         data-level="{{ $day['level'] }}" 
                         title="{{ $day['in_year'] ? $day['count'] . ' activities on ' . date('M j, Y', strtotime($day['date'])) : '' }}"
                         style="{{ !$day['in_year'] ? 'visibility: hidden;' : '' }}">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="heatmap-legend">
        <span>Less</span>
        <div class="legend-cell" style="background-color: rgba(0,0,0,0.05);"></div>
        <div class="legend-cell" style="background-color: #c6e48b;"></div>
        <div class="legend-cell" style="background-color: #7bc96f;"></div>
        <div class="legend-cell" style="background-color: #239a3b;"></div>
        <div class="legend-cell" style="background-color: #196127;"></div>
        <span>More</span>
    </div>
</div>


@endsection
