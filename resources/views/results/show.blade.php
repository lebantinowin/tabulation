@extends('layouts.app')

@section('title', $event->name . ' - Results')

@section('content')
<div class="page-header flex justify-between items-center" style="flex-wrap: wrap;">
    <div class="flex items-center gap-3">
        <div>
            <h1 style="margin-bottom: 0;">{{ $event->name }}</h1>
            <p style="color: #666; margin-top: 0.2rem;">
                <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}

            @php
                $statusClass = '';
                $statusText = '';

                if($event->status == 'ongoing') {
                    $statusClass = 'badge-success';
                    $statusText = 'Ongoing';
                } elseif($event->status == 'upcoming') {
                    $statusClass = 'badge-warning';
                    $statusText = 'Upcoming';
                } elseif($event->status == 'completed') {
                    $statusClass = 'badge-secondary';
                    $statusText = 'Completed';
                } elseif($event->status == 'active') {
                    $statusClass = 'badge-success';
                    $statusText = 'Active';
                } elseif($event->status == 'inactive') {
                    $statusClass = 'badge-warning';
                    $statusText = 'Inactive';
                } else {
                    $statusClass = 'badge-secondary';
                    $statusText = $event->status;
                }
            @endphp

            <span class="badge {{ $statusClass }}" style="margin-left: 10px;">{{ $statusText }}</span>
        </p>
        </div>
    </div>
    <div class="flex items-center gap-3">
        @auth
        @if(auth()->user()->isAdmin())
        <span id="autoRefreshTimer" class="badge badge-secondary" style="font-size: 0.8rem; font-weight: normal;">
            <i class="fas fa-sync-alt fa-spin"></i> Refreshing in 60s
        </span>
        @endif
        @endauth
        <a href="{{ route('results.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Events
        </a>
    </div>
</div>

@if(count($results) == 0)
    <div class="alert alert-danger">
        No results available yet.
    </div>
@else
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">
            {{ session('success') }}
        </div>
    @endif

    @auth
    @if(auth()->user()->isAdmin())
    <!-- Judges Progress Table -->
    <div class="mb-4">
        <h3 style="font-size: 1.1rem; margin-bottom: 0.75rem;"><i class="fas fa-tasks text-muted"></i> Judges' Progress</h3>
        <div class="table-responsive" style="padding-bottom: 0; box-shadow: none; border: 1px solid var(--color-border); background: var(--color-white); border-radius: 12px; overflow: hidden;">
            <table style="box-shadow: none; border-radius: 0; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th>Judge</th>
                        <th style="text-align: center;">Progress</th>
                        <th style="text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalContestants = count($results);
                        $totalCriteria = count($criterias);
                    @endphp
                    @foreach($judges as $judge)
                        @php
                            $completed = 0;
                            if($totalCriteria > 0) {
                                foreach($results as $result) {
                                    $judgeScores = $result['scores']->where('judge_id', $judge->id)->count();
                                    if($judgeScores >= $totalCriteria) {
                                        $completed++;
                                    }
                                }
                            }
                            $percent = $totalContestants > 0 ? round(($completed / $totalContestants) * 100) : 0;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $judge->judge_number ? 'Judge ' . $judge->judge_number . ' - ' : '' }}{{ $judge->name }}</strong>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                                    <div style="flex-grow: 1; max-width: 200px; background: rgba(0,0,0,0.05); height: 8px; border-radius: 4px; overflow: hidden; border: 1px solid rgba(0,0,0,0.05);">
                                        <div style="width: {{ $percent }}%; height: 100%; background: {{ $percent == 100 ? 'var(--color-success)' : 'var(--color-warning)' }};"></div>
                                    </div>
                                    <span style="font-weight: 600; font-size: 0.9rem; min-width: 40px;">{{ $completed }} / {{ $totalContestants }}</span>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                @if($completed == $totalContestants && $totalContestants > 0)
                                    <span class="badge badge-success">Completed</span>
                                @else
                                    <span class="badge badge-warning">In Progress</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if(count($judges) == 0)
                        <tr><td colspan="3" class="text-center text-muted">No judges assigned to this event.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endauth

    <div class="card">
        <div class="flex justify-between items-center mb-4" style="flex-wrap: wrap; gap: 1rem;">
            <h2 style="margin-bottom: 0;">Overall Rankings</h2>

            @auth
            @if(auth()->user()->isSuperAdmin())
            <div class="flex gap-2" style="flex-wrap: wrap; align-items: center;">
                <button type="button" onclick="openPdfModal('{{ route('tabulation.print', ['event_id' => $event->id]) }}')" class="btn" style="background: var(--color-info);">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
                @if(count($judges) > 0)
                <div style="position: relative; display: inline-block;" id="exportJudgeWrap">
                    <button type="button" onclick="toggleDropdown('exportJudgeMenu')" class="btn"
                            style="background: var(--color-primary); display:flex; align-items:center; gap:0.4rem;">
                        <i class="fas fa-print"></i> Export Judge
                        <i class="fas fa-chevron-down" style="font-size:0.7rem;"></i>
                    </button>
                    <div id="exportJudgeMenu"
                         style="display:none; position:absolute; top:110%; right:0; background:#fff; border:1px solid #ddd; border-radius:8px; box-shadow:0 4px 16px rgba(0,0,0,0.12); min-width:180px; z-index:999; overflow:hidden;">
                        @foreach($judges as $j)
                            <a href="{{ route('tabulation.print-judge', ['eventId' => $event->id, 'judgeId' => $j->id]) }}"
                               target="_blank"
                               style="display:block; padding:0.6rem 1rem; font-size:0.85rem; color:#333; text-decoration:none; border-bottom:1px solid #f0f0f0;"
                               onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='#fff'">
                                <i class="fas fa-user" style="margin-right:6px; color:var(--color-primary);"></i>
                                {{ $j->judge_number ? 'Judge ' . $j->judge_number : $j->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
                <form action="{{ route('events.resetScores', $event->id) }}" method="POST" style="margin:0;" id="resetScoresForm">
                    @csrf
                    <button type="button" onclick="startResetScoresCountdown(document.getElementById('resetScoresForm'))" class="btn" style="background: var(--color-danger); color: white;">
                        <i class="fas fa-trash-alt"></i> Reset Scores
                    </button>
                </form>
            </div>
            @endif
            @endauth
        </div>

        <style>
        .table-responsive {
            overflow-x: auto;
            max-width: 100%;
            padding-bottom: 12px;
            scroll-behavior: smooth;
        }
        .table-responsive::-webkit-scrollbar {
            height: 12px;
        }
        .table-responsive::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.04);
            border-radius: 8px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--color-secondary);
            border-radius: 8px;
            border: 3px solid var(--color-white);
        }
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: var(--color-btn);
        }
        .table-responsive table {
            white-space: nowrap;
            border-collapse: separate;
            border-spacing: 0;
            overflow: visible !important;
            width: 100%;
        }
        /* Fixed widths to avoid overlap */
        .sticky-col-left-1 { position: -webkit-sticky; position: sticky; left: 0; z-index: 2; background-clip: padding-box; border-right: 1px solid var(--color-border); width: 80px; min-width: 80px; max-width: 80px; }
        .sticky-col-left-2 { position: -webkit-sticky; position: sticky; left: 80px; z-index: 2; background-clip: padding-box; border-right: 2px solid var(--color-border); width: 250px; min-width: 250px; max-width: 250px; box-shadow: 5px 0 8px -4px rgba(0,0,0,0.08); }
        .sticky-col-right-2 { position: -webkit-sticky; position: sticky; right: 100px; z-index: 2; background-clip: padding-box; border-left: 2px solid var(--color-border); width: 120px; min-width: 120px; max-width: 120px; box-shadow: -5px 0 8px -4px rgba(0,0,0,0.08); }
        .sticky-col-right-1 { position: -webkit-sticky; position: sticky; right: 0; z-index: 2; background-clip: padding-box; border-left: 1px solid var(--color-border); width: 100px; min-width: 100px; max-width: 100px; }
        .sticky-col-right-only { position: -webkit-sticky; position: sticky; right: 0; z-index: 2; background-clip: padding-box; border-left: 2px solid var(--color-border); width: 120px; min-width: 120px; max-width: 120px; box-shadow: -5px 0 8px -4px rgba(0,0,0,0.08); }

        th.sticky-col-left-1, th.sticky-col-left-2, 
        th.sticky-col-right-2, th.sticky-col-right-1, th.sticky-col-right-only {
            background-color: var(--color-btn);
            z-index: 3;
            color: white;
        }

        .scroll-btns-container {
            display: none; /* hidden by default, JS will show if scrollable */
            justify-content: center;
            gap: 16px;
            margin-bottom: 8px;
            opacity: 0.6;
            transition: opacity 0.2s;
        }
        .scroll-btns-container:hover {
            opacity: 1;
        }
        .scroll-btn {
            background: transparent;
            border: none;
            color: var(--color-muted);
            padding: 4px 8px;
            cursor: pointer;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .scroll-btn:hover {
            color: var(--color-text);
        }
        
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        </style>

        <div class="scroll-btns-container">
            <button type="button" class="scroll-btn" onclick="document.getElementById('overallTableWrapper').scrollBy({left: -200, behavior: 'smooth'})">
                <i class="fas fa-chevron-left"></i> Scroll Left
            </button>
            <button type="button" class="scroll-btn" onclick="document.getElementById('overallTableWrapper').scrollBy({left: 200, behavior: 'smooth'})">
                Scroll Right <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <div class="table-responsive" id="overallTableWrapper">
            <table>
                <thead>
                    <tr>
                        <th class="sticky-col-left-1" style="text-align: center;">Rank</th>
                        <th class="sticky-col-left-2">Contestant</th>
                        @if(count($criterias) > 0)
                            @foreach($criterias as $criteria)
                                <th style="text-align: center; min-width: 120px;">C{{ $loop->iteration }}<br><small style="font-weight: 400; opacity: 0.75;">{{ $criteria->name }}<br>({{ $criteria->weight }}%)</small></th>
                            @endforeach
                        @endif
                        @auth
                        @if(auth()->user()->isSuperAdmin())
                            <th class="sticky-col-right-2" style="text-align: center; line-height: 1.2;">Overall<br>Weighted<br>Score</th>
                            <th class="sticky-col-right-1" style="text-align: center;">Actions</th>
                        @else
                            <th class="sticky-col-right-only" style="text-align: center; line-height: 1.2;">Overall<br>Weighted<br>Score</th>
                        @endif
                        @else
                            <th class="sticky-col-right-only" style="text-align: center; line-height: 1.2;">Overall<br>Weighted<br>Score</th>
                        @endauth
                    </tr>
                </thead>
                <tbody id="resultsTableBody">
                    @foreach($results as $result)
                        <tr data-contestant-id="{{ $result['contestant']->id }}" style="{{ $event->current_contestant_id == $result['contestant']->id ? 'box-shadow: inset 0 0 0 2px var(--color-success); background-color: #f0fdf4;' : 'background-color: #ffffff;' }}">
                            <td class="sticky-col-left-1" style="background-color: inherit; text-align: center;">
                                @if($result['rank'] == 1)
                                    <span style="display: inline-block; width: 30px; height: 30px; line-height: 30px; background: #FFD700; color: #000; border-radius: 50%; font-weight: bold;">1</span>
                                @elseif($result['rank'] == 2)
                                    <span style="display: inline-block; width: 30px; height: 30px; line-height: 30px; background: #C0C0C0; color: #000; border-radius: 50%; font-weight: bold;">2</span>
                                @elseif($result['rank'] == 3)
                                    <span style="display: inline-block; width: 30px; height: 30px; line-height: 30px; background: #CD7F32; color: #fff; border-radius: 50%; font-weight: bold;">3</span>
                                @else
                                    <span style="font-weight: bold;">{{ $result['rank'] }}</span>
                                @endif
                            </td>
                            <td class="sticky-col-left-2" style="background-color: inherit;">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    @if($result['contestant']->image_url)
                                        <img src="{{ $result['contestant']->image_url }}" alt="{{ $result['contestant']->name }}" class="profile-image">
                                    @else
                                        <div class="user-avatar">
                                            {{ substr($result['contestant']->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ $result['contestant']->name }}</strong>
                                        @if($result['contestant']->number)
                                            <br><small style="color: #666;">#{{ $result['contestant']->number }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            @if(count($criterias) > 0)
                                @foreach($criterias as $criteria)
                                    <td style="text-align: center;">
                                        @if(count($result['criteria_scores'][$criteria->id]['scores'] ?? []) == 0)
                                            <span class="badge badge-warning" style="background: #f39c12; color: #fff; font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 4px;" title="Not yet scored by any judge">Pending</span>
                                        @else
                                            {{ number_format(($result['criteria_scores'][$criteria->id]['average'] ?? 0) * ($criteria->weight / 100), 2) }}%
                                        @endif
                                    </td>
                                @endforeach
                            @endif
                            @auth
                            @if(auth()->user()->isSuperAdmin())
                            <td class="sticky-col-right-2" style="background-color: inherit; text-align: center;">
                                <strong style="font-size: 1.1rem;">{{ number_format($result['total_score'], 2) }}%</strong>
                            </td>
                            <td class="sticky-col-right-1" style="background-color: inherit; text-align: center; vertical-align: middle;">
                                <button type="button" class="btn-icon" style="background: {{ $event->current_contestant_id == $result['contestant']->id ? '#22c55e' : '#64748b' }}; color: white; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; transition: background 0.2s;" title="{{ $event->current_contestant_id == $result['contestant']->id ? 'Currently Performing' : 'Set as Performing' }}" onclick="setPerforming({{ $result['contestant']->id }}, {{ $event->current_contestant_id == $result['contestant']->id ? 'true' : 'false' }})">
                                    <i class="fas fa-microphone"></i>
                                </button>
                            </td>
                            @else
                            <td class="sticky-col-right-only" style="background-color: inherit; text-align: center;">
                                <strong style="font-size: 1.1rem;">{{ number_format($result['total_score'], 2) }}%</strong>
                            </td>
                            @endif
                            @else
                            <td class="sticky-col-right-only" style="background-color: inherit; text-align: center;">
                                <strong style="font-size: 1.1rem;">{{ number_format($result['total_score'], 2) }}%</strong>
                            </td>
                            @endauth
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if(count($criterias) > 0)
        <!-- Criteria Tabs Navigation -->
        <ul class="nav-tabs" style="margin-top: 2rem;">
            @foreach($criterias as $index => $criteria)
                <li>
                    <button class="tab-btn {{ $index == 0 ? 'active' : '' }}" onclick="switchCriteriaTab('criteria_{{ $criteria->id }}', this)">
                        {{ $criteria->name }}
                    </button>
                </li>
            @endforeach
        </ul>

        <!-- Criteria Tab Contents -->
        @foreach($criterias as $index => $criteria)
            <div id="criteria_{{ $criteria->id }}" class="tab-content {{ $index == 0 ? 'active' : '' }}">
                <div class="card">
                    <div class="flex justify-between items-center mb-4" style="flex-wrap: wrap; gap: 1rem;">
                        <h3 style="margin-bottom: 0;">{{ $criteria->name }} - Breakdown (Weight: {{ $criteria->weight }}%)</h3>

                        @auth
                        @if(auth()->user()->isSuperAdmin())
                        <button type="button" onclick="openPdfModal('{{ route('tabulation.print-category', ['criteriaId' => $criteria->id]) }}')" class="btn btn-sm" style="background: var(--color-info); padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>
                        @endif
                        @endauth
                    </div>

                    <div class="scroll-btns-container">
                        <button type="button" class="scroll-btn" onclick="document.getElementById('criteriaTableWrapper_{{ $criteria->id }}').scrollBy({left: -200, behavior: 'smooth'})">
                            <i class="fas fa-chevron-left"></i> Scroll Left
                        </button>
                        <button type="button" class="scroll-btn" onclick="document.getElementById('criteriaTableWrapper_{{ $criteria->id }}').scrollBy({left: 200, behavior: 'smooth'})">
                            Scroll Right <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>

                    <div class="table-responsive" id="criteriaTableWrapper_{{ $criteria->id }}">
                        <table>
                            <thead>
                                <tr>
                                    <th class="sticky-col-left-1" style="text-align: center;">Rank</th>
                                    <th class="sticky-col-left-2">Contestant</th>
                                    @auth
                                    @if(auth()->user()->isAdmin())
                                        @foreach($judges as $judge)
                                            <th style="text-align: center; min-width: 40px; width: 60px; padding: 0.5rem 0.25rem;">{{ $judge->judge_number ? 'J' . $judge->judge_number : 'J' . $loop->iteration }}</th>
                                        @endforeach
                                    @endif
                                    @endauth
                                    <th class="sticky-col-right-only" style="text-align: center; line-height: 1.2;">Weighted<br>Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $criteriaResults = [];
                                    foreach($results as $result) {
                                        $criteriaResults[] = [
                                            'contestant'   => $result['contestant'],
                                            'average'      => $result['criteria_scores'][$criteria->id]['average'] ?? 0,
                                            'total'        => $result['criteria_scores'][$criteria->id]['total'] ?? 0,
                                            'scores_count' => count($result['criteria_scores'][$criteria->id]['scores'] ?? []),
                                            'scores'       => $result['criteria_scores'][$criteria->id]['scores'] ?? collect(),
                                            'weight'       => $criteria->weight,
                                        ];
                                    }
                                    usort($criteriaResults, function($a, $b) {
                                        return $b['average'] <=> $a['average'];
                                    });
                                @endphp

                                @foreach($criteriaResults as $idx => $cr)
                                    <tr>
                                        <td class="sticky-col-left-1" style="background-color: inherit; text-align: center;">{{ $idx + 1 }}</td>
                                        <td class="sticky-col-left-2" style="background-color: inherit;">
                                            <div style="display: flex; align-items: center; gap: 1rem;">
                                                @if($cr['contestant']->image_url)
                                                    <img src="{{ $cr['contestant']->image_url }}" alt="{{ $cr['contestant']->name }}" class="profile-image-sm">
                                                @else
                                                    <div class="user-avatar">
                                                        {{ substr($cr['contestant']->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    {{ $cr['contestant']->name }}
                                                    @if($cr['contestant']->number)
                                                        <br><small style="color: #666;">#{{ $cr['contestant']->number }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        @auth
                                        @if(auth()->user()->isAdmin())
                                            @foreach($judges as $judge)
                                                <td style="text-align: center; padding: 0.5rem 0.25rem;">
                                                    @php
                                                        $judgeScore = $cr['scores']->where('judge_id', $judge->id)->first();
                                                    @endphp
                                                    @if($judgeScore)
                                                        <div>{{ number_format($judgeScore->score, 2) }}</div>
                                                        <small style="color: var(--color-muted); font-size: 0.75rem;">{{ number_format($judgeScore->score * ($cr['weight'] / 100), 2) }}%</small>
                                                    @else
                                                        <span style="color: #ccc;">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        @endif
                                        @endauth

                                        <td class="sticky-col-right-only" style="background-color: inherit; text-align: center;">
                                            @if($cr['scores_count'] == 0)
                                                <span class="badge badge-warning" style="background: #f39c12; color: #fff; font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 4px;">Pending</span>
                                            @else
                                                <strong style="font-size: 1.05rem;">{{ number_format($cr['average'] * ($cr['weight'] / 100), 2) }}%</strong>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endif

@include('partials.pdf_signature_modal')

<script>
function switchCriteriaTab(tabId, btnEl) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    // Remove active class from buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabId).classList.add('active');
    btnEl.classList.add('active');
}

function toggleDropdown(id) {
    const menu = document.getElementById(id);
    const isOpen = menu.style.display !== 'none';
    document.querySelectorAll('[id^="exportJudgeMenu"], [id^="printJudgeMenu_"]').forEach(m => m.style.display = 'none');
    menu.style.display = isOpen ? 'none' : 'block';
}

document.addEventListener('click', function(e) {
    const isInside = e.target.closest('#exportJudgeWrap') || e.target.closest('[id^="printJudgeWrap_"]');
    if (!isInside) {
        document.querySelectorAll('[id^="exportJudgeMenu"], [id^="printJudgeMenu_"]').forEach(m => m.style.display = 'none');
    }
});

function startResetScoresCountdown(formEl) {
    confirmAction('Are you sure you want to reset ALL scores to zero? This action cannot be undone.', function() {
        
        // The confirmProceed() function closes the modal, so we must re-open it for the countdown
        document.getElementById('confirmModal').classList.add('active');
        
        // Start countdown
        const titleEl = document.getElementById('confirmTitle');
        const messageEl = document.getElementById('confirmMessage');
        const okBtn = document.getElementById('confirmOkBtn');
        const cancelBtn = document.getElementById('confirmCancelBtn');
        const iconEl = document.getElementById('confirmIcon');

        titleEl.innerText = 'Resetting Scores';
        iconEl.innerHTML = '<i class="fas fa-exclamation-triangle" style="color: var(--color-danger);"></i>';
        
        // Hide Confirm button, only show Cancel
        okBtn.style.display = 'none';
        cancelBtn.style.display = 'inline-flex';
        cancelBtn.style.alignItems = 'center';
        cancelBtn.style.justifyContent = 'center';
        cancelBtn.innerHTML = '<i class="fas fa-times" style="margin-right: 6px;"></i> Cancel Reset';
        
        let seconds = 5;
        messageEl.innerHTML = `All scores will be permanently deleted in <br><strong style="font-size: 3rem; color: var(--color-danger); display: block; margin: 1rem 0;">${seconds}</strong>`;

        // Reuse _logoutTimer from app.blade.php so closeConfirmModal automatically clears it
        if (typeof _logoutTimer !== 'undefined' && _logoutTimer) {
            clearInterval(_logoutTimer);
        }

        window._logoutTimer = setInterval(() => {
            seconds--;
            if (seconds <= 0) {
                clearInterval(window._logoutTimer);
                formEl.submit();
            } else {
                messageEl.innerHTML = `All scores will be permanently deleted in <br><strong style="font-size: 3rem; color: var(--color-danger); display: block; margin: 1rem 0;">${seconds}</strong>`;
            }
        }, 1000);
        
    }, {title: 'Reset All Scores?'});
}

function setPerforming(contestantId, isActive) {
    const payload = isActive ? null : contestantId;
    fetch(`{{ route('events.setPerforming', $event->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ contestant_id: payload })
    }).then(res => res.json()).then(data => {
        if (data.success) {
            refreshResultsTable(); // immediately trigger refresh to show UI change
        }
    }).catch(err => console.error('Error setting performing contestant:', err));
}

// Auto-refresh and FLIP animation
function refreshResultsTable() {
    // Add cache-busting parameter to bypass browser caching
    const url = new URL(window.location.href);
    url.searchParams.set('_t', Date.now());
    
    fetch(url.toString(), { 
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        cache: 'no-store'
    })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTbody = doc.getElementById('resultsTableBody');
            if (!newTbody) return;

            const currentTbody = document.getElementById('resultsTableBody');
            
            // FLIP First: Measure current bounds
            const firstRects = {};
            Array.from(currentTbody.children).forEach(row => {
                const id = row.getAttribute('data-contestant-id');
                if (id) firstRects[id] = row.getBoundingClientRect();
            });

            // Apply new HTML content
            currentTbody.innerHTML = newTbody.innerHTML;

            // FLIP Last: Measure new bounds
            const lastRects = {};
            Array.from(currentTbody.children).forEach(row => {
                const id = row.getAttribute('data-contestant-id');
                if (id) {
                    lastRects[id] = row.getBoundingClientRect();
                    
                    // FLIP Invert: Calculate difference and translate
                    if (firstRects[id]) {
                        const deltaY = firstRects[id].top - lastRects[id].top;
                        
                        // Only animate if position changed
                        if (deltaY !== 0) {
                            row.style.transform = `translateY(${deltaY}px)`;
                            row.style.transition = 'none';
                            
                            // FLIP Play: Remove transform to animate to natural position
                            requestAnimationFrame(() => {
                                row.style.transform = '';
                                row.style.transition = 'transform 0.5s cubic-bezier(0.4, 0.0, 0.2, 1)';
                            });
                        }
                    }
                }
            });
        });
}

function updateScrollButtonsVisibility() {
    document.querySelectorAll('.table-responsive').forEach(wrapper => {
        const table = wrapper.querySelector('table');
        const btnsContainer = wrapper.previousElementSibling;
        
        if (btnsContainer && btnsContainer.classList.contains('scroll-btns-container')) {
            // Check if table is wider than its wrapper
            if (table.offsetWidth > wrapper.offsetWidth) {
                btnsContainer.style.display = 'flex';
                
                // Center exactly over the scrollable criteria area by padding out the sticky columns
                const leftSticky = wrapper.querySelector('.sticky-col-left-2');
                const rightSticky = wrapper.querySelector('.sticky-col-right-2') || wrapper.querySelector('.sticky-col-right-only');
                
                if (wrapper.offsetWidth > 600) {
                    if (leftSticky) {
                        const leftWidth = leftSticky.getBoundingClientRect().right - wrapper.getBoundingClientRect().left;
                        btnsContainer.style.paddingLeft = Math.max(0, leftWidth) + 'px';
                    }
                    if (rightSticky) {
                        const rightWidth = wrapper.getBoundingClientRect().right - rightSticky.getBoundingClientRect().left;
                        btnsContainer.style.paddingRight = Math.max(0, rightWidth) + 'px';
                    }
                } else {
                    btnsContainer.style.paddingLeft = '0px';
                    btnsContainer.style.paddingRight = '0px';
                }
            } else {
                btnsContainer.style.display = 'none';
            }
        }
    });
}

window.addEventListener('load', updateScrollButtonsVisibility);
window.addEventListener('resize', updateScrollButtonsVisibility);
// Also update when changing tabs
document.addEventListener('click', function(e) {
    if (e.target.closest('.tab-btn')) {
        setTimeout(updateScrollButtonsVisibility, 50);
    }
});

// Auto refresh every 60 seconds
const REFRESH_INTERVAL = 60;
let secondsLeft = REFRESH_INTERVAL;

setInterval(() => {
    secondsLeft--;
    const timerEl = document.getElementById('autoRefreshTimer');
    if (timerEl) {
        timerEl.innerHTML = `<i class="fas fa-sync-alt fa-spin"></i> Refreshing in ${secondsLeft}s`;
    }
    
    if (secondsLeft <= 0) {
        refreshResultsTable();
        secondsLeft = REFRESH_INTERVAL;
    }
}, 1000);
</script>

@endsection
