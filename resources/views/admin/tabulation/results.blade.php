@extends('layouts.app')

@section('title', 'Tabulation Results')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-trophy"></i> Tabulation Results</h1>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<style>
.table-responsive {
    overflow-x: auto;
    max-width: 100%;
    padding-bottom: 12px;
    scroll-behavior: smooth; /* For smooth scrolling with buttons */
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
.sticky-col-right-2 { position: -webkit-sticky; position: sticky; right: 120px; z-index: 2; background-clip: padding-box; border-left: 2px solid var(--color-border); width: 120px; min-width: 120px; max-width: 120px; box-shadow: -5px 0 8px -4px rgba(0,0,0,0.08); }
.sticky-col-right-1 { position: -webkit-sticky; position: sticky; right: 0; z-index: 2; background-clip: padding-box; border-left: 1px solid var(--color-border); width: 120px; min-width: 120px; max-width: 120px; }
th.sticky-col-left-1, th.sticky-col-left-2, th.sticky-col-right-2, th.sticky-col-right-1 { background-color: var(--color-btn); z-index: 3; color: white; }

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
</style>

<div class="card">
    <form method="GET" action="{{ route('tabulation.results') }}">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="event_id">Select Event</label>
            <select id="event_id" name="event_id" onchange="this.form.submit()">
                <option value="">-- Select Event --</option>
                @foreach($events ?? [] as $evt)
                    <option value="{{ $evt->id }}" {{ (isset($event) && $event && $event->id == $evt->id) ? 'selected' : '' }}>
                        {{ $evt->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>
</div>

@if(isset($event) && $event)
<div class="card">
    <div class="flex justify-between items-center mb-3" style="flex-wrap: wrap; gap: 0.75rem;">
        <div class="flex items-center gap-3">
            <h2 class="mb-0">{{ $event->name }} — Results</h2>
            <span id="autoRefreshTimer" class="badge badge-secondary" style="font-size: 0.8rem; font-weight: normal;">
                <i class="fas fa-sync-alt fa-spin"></i> Refreshing in 60s
            </span>
        </div>
        @if(auth()->user()->isSuperAdmin())
        <div class="flex gap-2" style="flex-wrap: wrap;">
            <button type="button" onclick="openPdfModal('{{ route('tabulation.print', ['event_id' => $event->id]) }}')" class="btn" style="background: var(--color-info);">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
            @if(count($criterias) > 0)
                <div class="dropdown" style="position: relative; display: inline-block;">
                    <button class="btn" style="background: var(--color-secondary);" onclick="toggleDropdown('print')">
                        <i class="fas fa-file-pdf"></i> Export PDF by Category <i class="fas fa-caret-down"></i>
                    </button>
                    <div id="categoryDropdownPrint" class="dropdown-menu" style="display: none; position: absolute; right: 0; top: 100%; background: var(--color-white); border: 1px solid var(--color-border); border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 100; min-width: 200px; overflow: hidden;">
                        @foreach($criterias as $criteria)
                            <a href="javascript:void(0)" onclick="openPdfModal('{{ route('tabulation.print-category', ['criteriaId' => $criteria->id]) }}')" style="display: block; padding: 0.75rem 1rem; color: var(--color-text); text-decoration: none; font-size: 0.9rem; border-bottom: 1px solid var(--color-border); transition: background 0.2s;" onmouseover="this.style.background='var(--color-main)'" onmouseout="this.style.background='transparent'">
                                {{ $criteria->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Judges Progress Table -->
    <div class="mb-4">
        <h3 style="font-size: 1.1rem; margin-bottom: 0.75rem;"><i class="fas fa-tasks text-muted"></i> Judges' Progress</h3>
        <div class="table-responsive" style="padding-bottom: 0; box-shadow: none; border: 1px solid var(--color-border);">
            <table style="box-shadow: none; border-radius: 0;">
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
                    <th class="sticky-col-left-1">Rank</th>
                    <th class="sticky-col-left-2">Contestant</th>
                    @foreach($criterias as $criteria)
                        <th style="min-width: 120px; text-align: center;">C{{ $loop->iteration }}<br><small style="font-weight: 400; opacity: 0.8;">{{ $criteria->name }}<br>({{ $criteria->weight }}%)</small></th>
                    @endforeach
                    <th class="sticky-col-right-2" style="text-align: center;">Total Score</th>
                    @if(auth()->user()->isSuperAdmin())
                    <th class="sticky-col-right-1" style="text-align: center;">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody id="resultsTableBody">
                @foreach($results as $result)
                <tr data-contestant-id="{{ $result['contestant']->id }}" style="{{ $result['is_overridden'] ? 'background-color: #fffbea;' : '' }} {{ $event->current_contestant_id == $result['contestant']->id ? 'box-shadow: inset 0 0 0 2px var(--color-success); background-color: #f0fdf4;' : 'background-color: #ffffff;' }}">
                    <td class="sticky-col-left-1" style="background-color: inherit; text-align: center;">
                        <span class="badge {{ $result['rank'] <= 3 ? 'badge-success' : 'badge-secondary' }}">#{{ $result['rank'] }}</span>
                    </td>
                    <td class="sticky-col-left-2" style="background-color: inherit;">
                        <strong>{{ $result['contestant']->name }}</strong>
                        @if($result['contestant']->number)
                            <br><small class="text-muted">#{{ $result['contestant']->number }}</small>
                        @endif
                        <br>
                        @if($result['completed_judges'] == $result['total_assigned_judges'] && $result['total_assigned_judges'] > 0)
                            <span class="badge badge-success" style="font-size: 0.65rem; margin-top: 0.25rem;"><i class="fas fa-check-circle"></i> All Judges Scored</span>
                        @else
                            <span class="badge badge-warning" style="font-size: 0.65rem; margin-top: 0.25rem;">Judges: {{ $result['completed_judges'] }} / {{ max(1, $result['total_assigned_judges']) }}</span>
                        @endif
                        @if($result['message'])
                            <br><small style="color: var(--color-danger);"><em>Note: {{ $result['message'] }}</em></small>
                        @endif
                    </td>
                    @foreach($criterias as $criteria)
                        <td style="text-align: center;">{{ number_format($result['criteria_scores'][$criteria->id]['average'] ?? 0, 2) }}</td>
                    @endforeach
                    <td class="sticky-col-right-2" style="background-color: inherit; text-align: center;">
                        <strong style="font-size: 1.1rem;">{{ number_format($result['total_score'], 2) }}</strong>
                        @if($result['is_overridden'])
                            <br><span class="badge badge-warning" style="font-size: 0.7rem;">Overridden</span>
                        @endif
                    </td>
                    @if(auth()->user()->isSuperAdmin())
                    <td class="sticky-col-right-1" style="background-color: inherit; text-align: center;">
                        <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">
                            <button type="button" class="btn-icon btn-icon-edit" title="Override Score / Add Note" onclick="openOverrideModal({{ $result['contestant']->id }}, '{{ addslashes($result['contestant']->name) }}', {{ $result['total_score'] }}, '{{ addslashes($result['message'] ?? '') }}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn-icon" style="background: {{ $event->current_contestant_id == $result['contestant']->id ? '#22c55e' : '#64748b' }}; color: white; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; transition: background 0.2s;" title="{{ $event->current_contestant_id == $result['contestant']->id ? 'Currently Performing' : 'Set as Performing' }}" onclick="setPerforming({{ $result['contestant']->id }})">
                                <i class="fas fa-microphone"></i>
                            </button>
                        </div>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Override Modal -->
<div id="overrideModal" class="modal">
    <div class="modal-content">
        <button type="button" class="modal-close" onclick="document.getElementById('overrideModal').classList.remove('active')">
            <i class="fas fa-times"></i>
        </button>
        <h3 class="mb-1">Override Score & Note</h3>
        <p id="overrideContestantName" class="text-muted mb-3"></p>

        <form method="POST" action="{{ route('tabulation.override') }}" class="mb-4">
            @csrf
            <input type="hidden" name="contestant_id" id="overrideScoreContestantId">
            <div class="form-group">
                <label>Total Score Override</label>
                <input type="number" name="total_score" id="overrideScoreInput" step="0.01" min="0" required>
            </div>
            <button type="submit" class="btn w-full" style="background: var(--color-warning);">
                <i class="fas fa-check"></i> Apply Score Override
            </button>
        </form>

        <form method="POST" action="{{ route('tabulation.message') }}">
            @csrf
            <input type="hidden" name="contestant_id" id="overrideMessageContestantId">
            <div class="form-group">
                <label>Message / Note</label>
                <textarea name="message" id="overrideMessageInput" rows="3"></textarea>
            </div>
            <button type="submit" class="btn w-full">
                <i class="fas fa-save"></i> Save Note
            </button>
        </form>
    </div>
</div>

<script>
    function toggleDropdown(type) {
        var menuPrint = document.getElementById('categoryDropdownPrint');
        if (menuPrint) menuPrint.style.display = menuPrint.style.display === 'block' ? 'none' : 'block';
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            var menuCsv = document.getElementById('categoryDropdownCsv');
            var menuPrint = document.getElementById('categoryDropdownPrint');
            if (menuCsv) menuCsv.style.display = 'none';
            if (menuPrint) menuPrint.style.display = 'none';
        }
    });

    function openOverrideModal(contestantId, contestantName, currentScore, currentMessage) {
        document.getElementById('overrideScoreContestantId').value = contestantId;
        document.getElementById('overrideMessageContestantId').value = contestantId;
        document.getElementById('overrideContestantName').innerText = contestantName;
        document.getElementById('overrideScoreInput').value = currentScore;
        document.getElementById('overrideMessageInput').value = currentMessage;
        document.getElementById('overrideModal').classList.add('active');
    }

    // Close modal on backdrop click
    document.getElementById('overrideModal').addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('active');
    });

    function setPerforming(contestantId) {
        fetch(`{{ route('events.setPerforming', $event->id) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ contestant_id: contestantId })
        }).then(res => res.json()).then(data => {
            if (data.success) {
                refreshResultsTable(); // immediately trigger refresh to show UI change
            }
        });
    }

    // Auto-refresh and FLIP animation
    function refreshResultsTable() {
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
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
@endif

@include('partials.pdf_signature_modal')

@endsection
