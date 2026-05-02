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

{{-- ─── Criteria Filter ─── --}}
<div class="card" style="margin-bottom: 1rem; padding: 1rem 1.5rem;">
    <div class="flex items-center gap-3" style="flex-wrap: wrap;">
        <label style="font-weight: 700; font-size: 1rem; white-space: nowrap;">
            <i class="fas fa-filter"></i> Criteria
        </label>
        <select id="criteriaFilter" onchange="filterByCriteria()"
                style="flex: 1; min-width: 200px; max-width: 400px; font-weight: bold; font-size: 1.05rem;">
            <option value="" style="font-weight: normal;">Choose Criteria</option>
            @foreach($criterias as $criteria)
                <option value="{{ $criteria->id }}" style="font-weight: bold;">{{ $criteria->name }} ({{ $criteria->weight }}%)</option>
            @endforeach
        </select>
    </div>
</div>

<div style="overflow-x: auto; margin-bottom: 2rem;">
    <table id="scoresTable">
        <thead>
            <tr>
                <th style="width: 70px;">No.</th>
                <th style="width: 60px;">Photo</th>
                <th>Contestant</th>
                <th style="width: 80px;">Score</th>
                <th style="width: 100px; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @if($criterias->isEmpty() || $contestants->isEmpty())
                <tr>
                    <td colspan="5" class="text-center text-muted">No criterias or contestants available for this event.</td>
                </tr>
            @else
                <tr id="instructionRow">
                    <td colspan="5" style="padding: 3rem 1rem;">
                        <div style="max-width: 900px; margin: 0 auto; background: var(--color-white); padding: 2.5rem; border-radius: 16px; border: 1px dashed var(--color-border); box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                            <h4 style="text-align: center; margin-bottom: 2.5rem; font-weight: 700; color: var(--color-primary); font-size: 1.5rem;">
                                <i class="fas fa-magic" style="color: #D4A574;"></i> How to Score
                            </h4>
                            <div class="steps-container">
                                <!-- Connector Line -->
                                <div class="steps-line"></div>
                                
                                <!-- Step 1 -->
                                <div class="step-box">
                                    <div class="step-circle">1</div>
                                    <h5 class="step-title">Choose Criteria</h5>
                                    <p class="step-desc">Select a criteria from the dropdown above.</p>
                                </div>

                                <!-- Step 2 -->
                                <div class="step-box">
                                    <div class="step-circle">2</div>
                                    <h5 class="step-title">Score Contestant</h5>
                                    <p class="step-desc">Click the <span style="background: var(--color-primary); color: white; border-radius: 4px; padding: 2px 6px; font-size: 0.7rem;"><i class="fas fa-plus"></i></span> button.</p>
                                </div>

                                <!-- Step 3 -->
                                <div class="step-box">
                                    <div class="step-circle">3</div>
                                    <h5 class="step-title">Submit</h5>
                                    <p class="step-desc">Enter your score and click Submit.</p>
                                </div>

                                <!-- Step 4 -->
                                <div class="step-box">
                                    <div class="step-circle">4</div>
                                    <h5 class="step-title">Edit Later</h5>
                                    <p class="step-desc">Update scores anytime using <i class="fas fa-edit" style="color: var(--color-warning);"></i>.</p>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @foreach($criterias as $criteria)
                    @foreach($contestants as $contestant)
                        @php
                            $score = $scores->where('contestant_id', $contestant->id)->where('criteria_id', $criteria->id)->first();
                        @endphp
                        <tr class="score-row" data-criteria="{{ $criteria->id }}" style="display: none;">
                            <td>{{ $contestant->number ?? '—' }}</td>
                            <td>
                                @if($contestant->image_url)
                                    <img src="{{ $contestant->image_url }}"
                                         alt="{{ $contestant->name }}"
                                         class="table-photo">
                                @else
                                    <div class="user-avatar table-photo" style="font-size: 1rem; border-radius: 8px;">
                                        {{ strtoupper(substr($contestant->name ?? '?', 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td><strong>{{ $contestant->name }}</strong></td>
                            <td>
                                @if($score)
                                    @if((float)$score->score == 0.00)
                                        <span class="badge badge-danger" title="Score is Zero">{{ number_format($score->score, 2) }}%</span>
                                    @else
                                        <span class="badge badge-success">{{ number_format($score->score, 2) }}%</span>
                                    @endif
                                @else
                                    <span class="badge badge-secondary" style="opacity: 0.5;">N/A</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div class="actions" style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    @if($score)
                                        <button type="button" class="btn-icon btn-icon-edit" title="Edit Score"
                                                onclick="openEditScoreModal(
                                                    {{ $score->id }},
                                                    '{{ addslashes($contestant->name ?? '') }}',
                                                    '{{ $contestant->image_url ?? '' }}',
                                                    '{{ $contestant->number ?? '' }}',
                                                    {{ $criteria->id }},
                                                    '{{ addslashes($criteria->name ?? '') }}',
                                                    {{ $criteria->max_points ?? 100 }},
                                                    {{ $score->score }},
                                                    '{{ addslashes($score->remarks ?? '') }}',
                                                    {{ $contestant->id }}
                                                )">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn-icon" style="background: var(--color-primary); color: white;" onclick="openScoreModal({{ $contestant->id }}, {{ $criteria->id }})" title="Submit New Score">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
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
                <p id="scoreModalName" class="text-muted mb-3" style="font-size: 0.9rem;"></p>

                <form method="POST" action="{{ route('scores.store') }}" id="submitScoreForm">
                    @csrf

                    {{-- Criteria first (Hidden as per request) --}}
                    <div class="form-group" style="display: none;">
                        <label for="sm_criteria_id">Criteria</label>
                        <select id="sm_criteria_id" name="criteria_id" required onchange="updateScoreMax(this)">
                            <option value="">Select Criteria</option>
                            @foreach($criterias as $criteria)
                                <option value="{{ $criteria->id }}" data-max="{{ $criteria->max_points ?? 100 }}">
                                    {{ $criteria->name }} ({{ $criteria->weight }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Contestant (Hidden as per request) --}}
                    <div class="form-group" style="display: none;">
                        <label for="sm_contestant_id">Contestant</label>
                        <select id="sm_contestant_id" name="contestant_id" required onchange="updateContestantPreview(this)">
                            <option value="">Select Contestant</option>
                            @foreach($contestants as $c)
                                <option value="{{ $c->id }}"
                                        data-name="{{ $c->name }}"
                                        data-number="{{ $c->number ?? '' }}"
                                        data-img="{{ $c->image_url ?? '' }}">
                                    #{{ $c->number }} - {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sm_score">Score (0–<span id="sm_max_label">100</span>)</label>
                        <input type="number" id="sm_score" name="score" min="0" max="100" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="sm_remarks">Remarks <span class="text-muted">(Optional)</span></label>
                        <textarea id="sm_remarks" name="remarks" rows="2"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-check"></i> Submit Score
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ─── Edit Score Modal ─── --}}
<div id="editScoreModal" class="modal">
    <div class="modal-content" style="max-width: 640px; padding: 0; overflow: hidden;">
        <div style="display: grid; grid-template-columns: 220px 1fr;">

            {{-- Left: Contestant portrait --}}
            <div id="editModalImage" style="position: relative; background: var(--color-main); min-height: 340px; display: flex; align-items: center; justify-content: center;">
                <div id="editModalAvatarPlaceholder" class="user-avatar"
                     style="width: 80px; height: 80px; font-size: 2rem; border: none;">?</div>
                <img id="editModalImg" src="" alt=""
                     style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; display: none;">
                <div id="editModalNumBadge"
                     style="display:none; position: absolute; top: 0.75rem; left: 0.75rem;
                            background: var(--color-btn); color: #fff; font-weight: 700;
                            padding: 0.3rem 0.75rem; border-radius: 8px; font-size: 0.95rem; z-index:2;"></div>
            </div>

            {{-- Right: Form --}}
            <div style="padding: 1.5rem;">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="mb-0">Edit Score</h3>
                    <button type="button" class="modal-close" onclick="closeEditModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p id="editModalContestantName" class="text-muted mb-3" style="font-size: 0.9rem;"></p>

                <form method="POST" id="editScoreForm" action="">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Criteria</label>
                        <input type="text" id="editModalCriteriaName" readonly
                               style="background: var(--color-main); cursor: default; opacity: 0.7;">
                        <input type="hidden" name="criteria_id" id="editModalCriteriaId">
                        <input type="hidden" name="contestant_id" id="editModalContestantId">
                    </div>

                    <div class="form-group">
                        <label for="editModalScore">Score (0–<span id="editModalMaxLabel">100</span>)</label>
                        <input type="number" id="editModalScore" name="score" min="0" max="100" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="editModalRemarks">Remarks <span class="text-muted">(Optional)</span></label>
                        <textarea id="editModalRemarks" name="remarks" rows="2"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
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

// ─── Submit Score Modal ───
function openScoreModal(contestantId = null, criteriaId = null) {
    const contestantSelect = document.getElementById('sm_contestant_id');
    const criteriaSelect = document.getElementById('sm_criteria_id');
    
    if (contestantId) {
        contestantSelect.value = contestantId;
    } else {
        contestantSelect.value = "";
    }
    
    if (criteriaId) {
        criteriaSelect.value = criteriaId;
        updateScoreMax(criteriaSelect);
    } else {
        const filterVal = document.getElementById('criteriaFilter').value;
        if (filterVal) {
            criteriaSelect.value = filterVal;
            updateScoreMax(criteriaSelect);
        } else {
            criteriaSelect.value = "";
        }
    }
    
    updateContestantPreview(contestantSelect);
    document.getElementById('scoreModal').classList.add('active');
}
function closeScoreModal() {
    document.getElementById('scoreModal').classList.remove('active');
}

function updateScoreMax(sel) {
    const max = sel.options[sel.selectedIndex]?.getAttribute('data-max') || 100;
    document.getElementById('sm_max_label').innerText = max;
    document.getElementById('sm_score').max = max;
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

// ─── Edit Score Modal ───
function openEditScoreModal(id, name, img, number, criteriaId, criteriaName, maxPoints, score, remarks, contestantId) {
    const form = document.getElementById('editScoreForm');
    form.action = '{{ url("scores") }}/' + id;

    document.getElementById('editModalContestantName').innerText = name;
    document.getElementById('editModalCriteriaName').value = criteriaName + ' (max: ' + maxPoints + ')';
    document.getElementById('editModalCriteriaId').value = criteriaId;
    document.getElementById('editModalContestantId').value = contestantId;
    document.getElementById('editModalMaxLabel').innerText = maxPoints;
    document.getElementById('editModalScore').max = maxPoints;
    document.getElementById('editModalScore').value = score;
    document.getElementById('editModalRemarks').value = remarks;

    const imgEl  = document.getElementById('editModalImg');
    const avatar = document.getElementById('editModalAvatarPlaceholder');
    const badge  = document.getElementById('editModalNumBadge');

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

    document.getElementById('editScoreModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editScoreModal').classList.remove('active');
}

// Close on backdrop click
document.querySelectorAll('.modal').forEach(function(m) {
    m.addEventListener('click', function(e) {
        if (e.target === m) m.classList.remove('active');
    });
});
</script>

<style>
.table-photo {
    width: 42px; 
    height: 42px; 
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
    transform: scale(2.5);
    z-index: 10;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
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
