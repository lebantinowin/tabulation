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
            <option value="" style="font-weight: normal;">All Criteria</option>
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
                    <td colspan="6" class="text-center text-muted">No criterias or contestants available for this event.</td>
                </tr>
            @else
                @foreach($criterias as $criteria)
                    @foreach($contestants as $contestant)
                        @php
                            $score = $scores->where('contestant_id', $contestant->id)->where('criteria_id', $criteria->id)->first();
                        @endphp
                        <tr class="score-row" data-criteria="{{ $criteria->id }}">
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
                                    <span class="badge badge-success">{{ $score->score }}</span>
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
                                                    '{{ addslashes($score->remarks ?? '') }}'
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

                    {{-- Criteria first --}}
                    <div class="form-group">
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

                    {{-- Contestant --}}
                    <div class="form-group">
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
    document.querySelectorAll('.score-row').forEach(function(row) {
        row.style.display = (!val || row.dataset.criteria === val) ? '' : 'none';
    });
}

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
function openEditScoreModal(id, name, img, number, criteriaId, criteriaName, maxPoints, score, remarks) {
    const form = document.getElementById('editScoreForm');
    form.action = '/tabulation/public/scores/' + id;

    document.getElementById('editModalContestantName').innerText = name;
    document.getElementById('editModalCriteriaName').value = criteriaName + ' (max: ' + maxPoints + ')';
    document.getElementById('editModalCriteriaId').value = criteriaId;
    document.getElementById('editModalContestantId').value = 0; // filled via score record
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
</style>
@endsection
