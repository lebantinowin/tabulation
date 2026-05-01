@extends('layouts.app')

@section('title', 'Edit Score - Judge')

@section('content')
<div class="page-header">
    <h1>Edit Score</h1>
    <a href="{{ route('scores.index') }}" class="btn" title="Back to Scores">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <form method="POST" action="{{ route('scores.update', $score->id) }}">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="contestant_id">Contestant</label>
            <select id="contestant_id" name="contestant_id" required>
                @foreach($contestants as $contestant)
                    <option value="{{ $contestant->id }}" {{ $score->contestant_id == $contestant->id ? 'selected' : '' }}>
                        #{{ $contestant->number }} - {{ $contestant->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="criteria_id">Criteria</label>
            <select id="criteria_id" name="criteria_id" required>
                @foreach($criterias as $criteria)
                    <option value="{{ $criteria->id }}" data-max="{{ $criteria->max_points ?? 100 }}" {{ $score->criteria_id == $criteria->id ? 'selected' : '' }}>
                        {{ $criteria->name }} ({{ $criteria->weight }}%)
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="score">Score (0-<span id="score-max-label">100</span>)</label>
            <input type="number" id="score" name="score" value="{{ $score->score }}" min="0" max="100" step="0.01" required>
        </div>
        
        <div class="form-group">
            <label for="remarks">Remarks (Optional)</label>
            <textarea id="remarks" name="remarks" rows="3">{{ $score->remarks }}</textarea>
        </div>
        
        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary">Update Score</button>
            <a href="{{ route('scores.index') }}" class="btn">Cancel</a>
        </div>
    </form>
</div>

<script>
function updateMaxScore() {
    var select = document.getElementById('criteria_id');
    if (!select || select.selectedIndex < 0) return;
    
    var selectedOption = select.options[select.selectedIndex];
    var maxPoints = selectedOption.getAttribute('data-max') || 100;
    var scoreInput = document.getElementById('score');
    var maxLabel = document.getElementById('score-max-label');
    
    scoreInput.max = maxPoints;
    maxLabel.innerText = maxPoints;
    
    if (parseFloat(scoreInput.value) > maxPoints) {
        scoreInput.value = maxPoints;
    }
}

document.getElementById('criteria_id').addEventListener('change', updateMaxScore);
// Run on load
updateMaxScore();
</script>
@endsection
