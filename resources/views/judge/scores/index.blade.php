@extends('layouts.app')

@section('title', 'My Scores - Judge')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-star"></i> My Scores</h1>
    <div class="flex gap-2">
        <a href="{{ route('judge.dashboard') }}" class="btn" title="Back to Dashboard">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif


<div style="overflow-x: auto; margin-bottom: 2rem;">
    <table id="scoresTable">
        <thead>
            <tr>
                <th style="width: 70px;">No.</th>
                <th style="width: 70px;">Photo</th>
                <th>Contestant</th>
                <th style="width: 140px; white-space: nowrap;">Score</th>
                <th style="width: 100px; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @if($criterias->isEmpty() || $contestants->isEmpty())
                <tr>
                    <td colspan="5" class="text-center text-muted">No criterias or contestants available for this event.</td>
                </tr>
            @else
                @foreach($contestants as $contestant)
                    @php
                        $contestantScores = $scores->where('contestant_id', $contestant->id);
                        $scoredCount = $contestantScores->count();
                        $totalCriteria = count($criterias);
                        $isComplete = $scoredCount == $totalCriteria && $totalCriteria > 0;
                    @endphp
                    <tr class="score-row" data-contestant="{{ $contestant->id }}" data-original-index="{{ $loop->index }}">
                        <td>{{ $contestant->number ?? '—' }}</td>
                        <td>
                            @if($contestant->image_url)
                                <img src="{{ $contestant->image_url }}" alt="{{ $contestant->name }}" class="table-photo">
                            @else
                                <div class="user-avatar table-photo" style="font-size: 1rem; border-radius: 8px;">
                                    {{ strtoupper(substr($contestant->name ?? '?', 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td><strong>{{ $contestant->name }}</strong></td>
                        <td>
                            @if($isComplete)
                                <span class="badge badge-success"><i class="fas fa-check-circle"></i> Complete</span>
                            @else
                                <span class="badge badge-warning">{{ $scoredCount }} / {{ $totalCriteria }} Scored</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div class="actions" style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <button type="button" class="btn-icon {{ $isComplete ? 'btn-icon-edit' : '' }}" 
                                        style="{{ !$isComplete ? 'background: var(--color-primary); color: white;' : '' }}" 
                                        onclick="openScoreModal({{ $contestant->id }})" 
                                        title="{{ $isComplete ? 'Edit Scores' : 'Submit Scores' }}">
                                    <i class="fas {{ $isComplete ? 'fa-edit' : 'fa-plus' }}"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

{{-- ─── Submit Score Modal ─── --}}
<div id="scoreModal" class="modal">
    <div class="modal-content" style="max-width: 640px; padding: 0; overflow: hidden;">
        <div style="display: grid; grid-template-columns: 220px 1fr;">

            {{-- Left: Contestant portrait --}}
            <div id="scoreModalImage" style="position: relative; background: var(--color-main); min-height: 340px; display: flex; align-items: center; justify-content: center;">
                <div id="scoreModalAvatarPlaceholder" class="user-avatar"
                     style="width: 80px; height: 80px; font-size: 2rem; border: none;">?</div>
                <img id="scoreModalImg" src="" alt=""
                     style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; display: none;">
                <div id="scoreModalNumBadge"
                     style="display:none; position: absolute; top: 0.75rem; left: 0.75rem;
                            background: var(--color-btn); color: #fff; font-weight: 700;
                            padding: 0.3rem 0.75rem; border-radius: 8px; font-size: 0.95rem; z-index:2;"></div>
            </div>

            {{-- Right: Form --}}
            <div style="padding: 1.5rem;">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="mb-0">Submit Score</h3>
                    <button type="button" class="modal-close" onclick="closeScoreModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <h4 id="scoreModalCriteriaNameHeader" style="color: var(--color-primary); font-size: 1.1rem; margin-top: -10px; margin-bottom: 10px;"></h4>
                <p id="scoreModalName" class="text-muted mb-3" style="font-size: 0.9rem;"></p>

                <div id="offlineNotice" style="display: none; background: var(--color-warning); color: #fff; padding: 0.5rem; border-radius: 4px; margin-bottom: 1rem; font-size: 0.85rem;">
                    <i class="fas fa-wifi"></i> Offline - Score will be saved locally
                </div>

                <form id="submitScoreForm">
                    {{-- Hidden Contestant Selection --}}
                    <input type="hidden" id="sm_contestant_id" name="contestant_id">

                    <div id="inlineSuccessMsg" style="display: none; background: #dcfce7; color: #166534; padding: 0.5rem; border-radius: 4px; margin-bottom: 1rem; font-size: 0.85rem;">
                        <i class="fas fa-check-circle"></i> <span id="inlineSuccessText">Score saved successfully!</span>
                    </div>

                    <table style="width: 100%; border-collapse: collapse; margin-top: 1rem; font-size: 0.95rem;">
                        <thead>
                            <tr>
                                <th style="text-align: left; padding-bottom: 0.5rem; border-bottom: 2px solid var(--color-border);">Criteria</th>
                                <th style="text-align: center; padding-bottom: 0.5rem; border-bottom: 2px solid var(--color-border); width: 100px;">Score</th>
                                <th style="text-align: center; padding-bottom: 0.5rem; border-bottom: 2px solid var(--color-border); width: 60px;"></th>
                            </tr>
                        </thead>
                        <tbody id="modalCriteriaTableBody">
                            <!-- Rendered by JS -->
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
// ─── Criteria filter ───
function filterByCriteria() {
    const val = document.getElementById('criteriaFilter').value;
    const instructionRow = document.getElementById('instructionRow');
    if (instructionRow) {
        instructionRow.style.display = val ? 'none' : '';
    }
    document.querySelectorAll('.score-row').forEach(function(row) {
        row.style.display = (val && row.dataset.criteria === val) ? '' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Restore selected criteria
    const savedCriteria = sessionStorage.getItem('judge_selected_criteria');
    if (savedCriteria) {
        document.getElementById('criteriaFilter').value = savedCriteria;
    }
    filterByCriteria();

    // Restore scroll position
    const savedScroll = sessionStorage.getItem('judge_scroll_pos');
    if (savedScroll) {
        // Need a slight timeout to ensure rendering is complete before scroll
        setTimeout(() => window.scrollTo(0, parseInt(savedScroll)), 50);
        sessionStorage.removeItem('judge_scroll_pos'); // clear after restoring
    }

    // Update saved criteria when changed
    document.getElementById('criteriaFilter').addEventListener('change', function() {
        sessionStorage.setItem('judge_selected_criteria', this.value);
    });

    // Save scroll position when any form is submitted
    const submitForm = document.getElementById('submitScoreForm');
    if (submitForm) {
        submitForm.addEventListener('submit', function() {
            sessionStorage.setItem('judge_scroll_pos', window.scrollY);
        });
    }

    const editForm = document.getElementById('editScoreForm');
    if (editForm) {
        editForm.addEventListener('submit', function() {
            sessionStorage.setItem('judge_scroll_pos', window.scrollY);
        });
    }
});

const judgeScores = @json($scores);
const eventCriterias = @json($criterias);

function openScoreModal(contestantId) {
    document.getElementById('sm_contestant_id').value = contestantId || "";
    document.getElementById('inlineSuccessMsg').style.display = 'none';

    const tbody = document.getElementById('modalCriteriaTableBody');
    tbody.innerHTML = '';

    eventCriterias.forEach(criteria => {
        const existing = judgeScores.find(s => s.contestant_id == contestantId && s.criteria_id == criteria.id);
        const scoreVal = existing ? existing.score : '';
        const icon = existing ? 'fa-edit' : 'fa-check';
        const btnColor = existing ? '#f39c12' : '#22c55e';

        tbody.innerHTML += `
            <tr>
                <td style="padding: 0.75rem 0; border-bottom: 1px solid var(--color-border);">
                    <strong style="display: block; font-size: 1rem; margin-bottom: 0.2rem;">${criteria.name}</strong>
                    <small style="color: #666; font-weight: 500;">Max: ${criteria.max_points}</small>
                </td>
                <td style="padding: 0.75rem 0; border-bottom: 1px solid var(--color-border); text-align: center;">
                    <input type="number" id="score_input_${criteria.id}" value="${scoreVal}" max="${criteria.max_points}" step="0.01" style="width: 80px; text-align: center; padding: 0.4rem; border: 1px solid var(--color-border); border-radius: 4px; font-weight: bold;" required>
                </td>
                <td style="padding: 0.75rem 0; border-bottom: 1px solid var(--color-border); text-align: right;">
                    <button type="button" class="btn-icon" style="background: ${btnColor}; color: white; width: 36px; height: 36px; border-radius: 8px;" onclick="submitSingleScore(${contestantId}, ${criteria.id}, this)">
                        <i class="fas ${icon}"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    // Populate generic preview image/name (we use the row data)
    const row = document.querySelector(`.score-row[data-contestant="${contestantId}"]`);
    if (row) {
        const nameText = row.querySelector('td:nth-child(3) strong').innerText;
        document.getElementById('scoreModalName').innerText = nameText;
        const imgEl = document.getElementById('scoreModalImg');
        const avatar = document.getElementById('scoreModalAvatarPlaceholder');
        const imgTag = row.querySelector('img.table-photo');
        
        if (imgTag) {
            imgEl.src = imgTag.src;
            imgEl.style.display = 'block';
            avatar.style.display = 'none';
        } else {
            imgEl.style.display = 'none';
            avatar.style.display = 'flex';
            avatar.innerText = nameText.charAt(0).toUpperCase();
        }
    }
    
    document.getElementById('scoreModal').classList.add('active');
}

function updateContestantPreview(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;
    const name   = opt.getAttribute('data-name') || '';
    const number = opt.getAttribute('data-number') || '';
    const img    = opt.getAttribute('data-img') || '';

    document.getElementById('scoreModalName').innerText = name;

    const imgEl   = document.getElementById('scoreModalImg');
    const avatar  = document.getElementById('scoreModalAvatarPlaceholder');
    const badge   = document.getElementById('scoreModalNumBadge');

    if (img) {
        imgEl.src = img;
        imgEl.style.display = 'block';
        avatar.style.display = 'none';
    } else {
        imgEl.style.display = 'none';
        avatar.style.display = 'flex';
        avatar.innerText = name.charAt(0).toUpperCase();
    }

    if (number) {
        badge.innerText = '#' + number;
        badge.style.display = 'block';
    } else {
        badge.style.display = 'none';
    }
}

// Removed Edit Modal functions as we unified into one modal

let needsRefresh = false;

function closeScoreModal() {
    document.getElementById('scoreModal').classList.remove('active');
    if (needsRefresh) {
        window.location.reload();
    }
}

function submitSingleScore(contestantId, criteriaId, btn) {
    const input = document.getElementById(`score_input_${criteriaId}`);
    if (!input.value) return;
    
    const data = {
        contestant_id: contestantId,
        criteria_id: criteriaId,
        score: input.value,
        remarks: ''
    };

    if (!navigator.onLine) {
        let pending = JSON.parse(localStorage.getItem('pendingScores') || '[]');
        pending.push(data);
        localStorage.setItem('pendingScores', JSON.stringify(pending));
        document.getElementById('inlineSuccessMsg').style.display = 'block';
        document.getElementById('inlineSuccessMsg').style.background = '#fef08a';
        document.getElementById('inlineSuccessMsg').style.color = '#854d0e';
        document.getElementById('inlineSuccessText').innerHTML = 'Offline: Saved locally.';
        needsRefresh = true;
        
        btn.innerHTML = '<i class="fas fa-edit"></i>';
        btn.style.background = '#f39c12';
        
        const existingIdx = judgeScores.findIndex(s => s.contestant_id == data.contestant_id && s.criteria_id == data.criteria_id);
        if (existingIdx !== -1) {
            judgeScores[existingIdx].score = data.score;
        } else {
            judgeScores.push(data);
        }
        return;
    }

    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    fetch('{{ route("scores.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(response => {
        btn.innerHTML = '<i class="fas fa-edit"></i>';
        btn.style.background = '#f39c12';
        btn.disabled = false;

        if (response.success) {
            document.getElementById('inlineSuccessMsg').style.display = 'block';
            document.getElementById('inlineSuccessMsg').style.background = '#dcfce7';
            document.getElementById('inlineSuccessMsg').style.color = '#166534';
            document.getElementById('inlineSuccessText').innerHTML = response.message;
            needsRefresh = true;

            const existingIdx = judgeScores.findIndex(s => s.contestant_id == data.contestant_id && s.criteria_id == data.criteria_id);
            if (existingIdx !== -1) {
                judgeScores[existingIdx].score = data.score;
            } else {
                judgeScores.push(data);
            }
        }
    })
    .catch(err => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        console.error(err);
    });
}

// Close on backdrop click
document.querySelectorAll('.modal').forEach(function(m) {
    m.addEventListener('click', function(e) {
        if (e.target === m) {
            m.classList.remove('active');
            if (needsRefresh) window.location.reload();
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {

    window.addEventListener('online', () => {
        let pending = JSON.parse(localStorage.getItem('pendingScores') || '[]');
        if (pending.length > 0) {
            // Ideally we loop and post them. For now, just alert and reload so normal submission can happen if they retry,
            // or we post via fetch.
            Promise.all(pending.map(data => {
                return fetch('{{ route("scores.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });
            })).then(() => {
                localStorage.removeItem('pendingScores');
                alert('Pending offline scores synced successfully!');
                window.location.reload();
            }).catch(err => console.error(err));
        }
    });

    setInterval(() => {
        if (!navigator.onLine) {
            document.getElementById('offlineNotice').style.display = 'block';
        } else {
            document.getElementById('offlineNotice').style.display = 'none';
        }
    }, 2000);

    // 2. Polling for Current Performing
    let currentPerformingId = null;
    setInterval(() => {
        fetch('{{ route("judge.currentPerforming") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })
            .then(res => res.json())
            .then(data => {
                const newId = data.current_contestant_id;
                if (newId != currentPerformingId) {
                    currentPerformingId = newId;
                    
                    const tbody = document.querySelector('#scoresTable tbody');
                    const rows = Array.from(tbody.querySelectorAll('.score-row'));
                    
                    // FLIP: First
                    const firstRects = {};
                    rows.forEach(row => {
                        firstRects[row.getAttribute('data-contestant')] = row.getBoundingClientRect();
                        row.classList.remove('performing-glowing');
                    });
                    
                    if (newId) {
                        // Move target to top
                        const targetRow = rows.find(r => r.getAttribute('data-contestant') == newId);
                        if (targetRow) {
                            tbody.prepend(targetRow);
                            targetRow.classList.add('performing-glowing');
                        }
                    } else {
                        // Re-sort to original order
                        rows.sort((a, b) => {
                            return parseInt(a.getAttribute('data-original-index')) - parseInt(b.getAttribute('data-original-index'));
                        }).forEach(row => tbody.appendChild(row));
                    }

                    // FLIP: Last, Invert, Play
                    rows.forEach(row => {
                        const id = row.getAttribute('data-contestant');
                        const lastRect = row.getBoundingClientRect();
                        const firstRect = firstRects[id];
                        
                        if (firstRect) {
                            const deltaY = firstRect.top - lastRect.top;
                            if (deltaY !== 0) {
                                row.style.transform = `translateY(${deltaY}px)`;
                                row.style.transition = 'none';
                                
                                requestAnimationFrame(() => {
                                    row.style.transform = '';
                                    row.style.transition = 'transform 0.5s cubic-bezier(0.4, 0.0, 0.2, 1)';
                                });
                            }
                        }
                    });
                }
            }).catch(err => {});
    }, 5000);
});
</script>

<style>
.table-photo {
    width: 56px; 
    height: 56px; 
    border-radius: 8px; /* square with slight rounded corners */
    object-fit: cover;
    transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    transform-origin: center left;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    z-index: 1;
}

.table-photo:hover {
    transform: scale(2) translateX(10px);
    z-index: 10;
    position: relative;
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}

@keyframes glow-bg {
    0% { background-color: rgba(255, 215, 0, 0.05); }
    50% { background-color: rgba(255, 215, 0, 0.20); }
    100% { background-color: rgba(255, 215, 0, 0.05); }
}

.performing-glowing td {
    animation: glow-bg 2s infinite;
    border-top: 2px solid #FFD700 !important;
    border-bottom: 2px solid #FFD700 !important;
    transition: all 0.3s ease;
}

.performing-glowing td:first-child {
    border-left: 4px solid #FFD700 !important;
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
}

.performing-glowing td:last-child {
    border-right: 4px solid #FFD700 !important;
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
}

/* Progressive Instructions */
.steps-container {
    display: flex; 
    justify-content: space-between; 
    align-items: flex-start; 
    position: relative;
    gap: 1.5rem;
}
.steps-line {
    position: absolute; 
    top: 25px; 
    left: 10%; 
    right: 10%; 
    height: 3px; 
    background: var(--color-border); 
    z-index: 1;
}
.step-box {
    flex: 1; 
    text-align: center; 
    position: relative; 
    z-index: 2;
}
.step-circle {
    width: 50px; 
    height: 50px; 
    background: var(--color-primary); 
    color: white; 
    border-radius: 50%; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    margin: 0 auto 1rem; 
    font-size: 1.25rem; 
    font-weight: bold; 
    border: 4px solid var(--color-white);
    box-shadow: 0 0 0 1px var(--color-border);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.step-box:hover .step-circle {
    transform: scale(1.1);
    box-shadow: 0 0 0 2px var(--color-primary);
}
.step-title {
    margin-bottom: 0.5rem; 
    font-weight: 600;
    font-size: 1.05rem;
}
.step-desc {
    font-size: 0.85rem; 
    color: var(--color-muted); 
    margin: 0;
    line-height: 1.5;
}

@media (max-width: 768px) {
    .steps-container {
        flex-direction: column;
        align-items: center;
        gap: 2rem;
    }
    .steps-line {
        display: none;
    }
}
</style>
@endsection
