@extends('layouts.app')

@section('title', 'Submit Score - Judge')

@section('content')
<div class="page-header">
    <h1>Submit New Score</h1>
    <a href="{{ route('scores.index') }}" class="btn" title="Back to Scores">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <form method="POST" action="{{ route('scores.store') }}">
        @csrf
        
        <div class="form-group">
            <label for="contestant_id">Contestant</label>
            <select id="contestant_id" name="contestant_id" required>
                <option value="">Select Contestant</option>
                @foreach($contestants as $contestant)
                    <option value="{{ $contestant->id }}">#{{ $contestant->number }} - {{ $contestant->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="criteria_id">Criteria</label>
            <select id="criteria_id" name="criteria_id" required>
                <option value="">Select Criteria</option>
                @foreach($criterias as $criteria)
                    <option value="{{ $criteria->id }}">{{ $criteria->name }} ({{ $criteria->weight }}%)</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="score">Score (0-100)</label>
            <input type="number" id="score" name="score" min="0" max="100" step="0.01" required>
        </div>
        
        <div class="form-group">
            <label for="remarks">Remarks (Optional)</label>
            <textarea id="remarks" name="remarks" rows="3"></textarea>
        </div>
        
        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary">Submit Score</button>
            <a href="{{ route('scores.index') }}" class="btn">Cancel</a>
        </div>
    </form>
</div>
@endsection
