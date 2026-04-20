<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Score - Judge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Score</h2>
        
        <form method="POST" action="{{ route('scores.update', $score->id) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="contestant_id" class="form-label">Contestant</label>
                <select class="form-select" id="contestant_id" name="contestant_id" required>
                    @foreach($contestants as $contestant)
                        <option value="{{ $contestant->id }}" {{ $score->contestant_id == $contestant->id ? 'selected' : '' }}>
                            #{{ $contestant->number }} - {{ $contestant->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-3">
                <label for="criteria_id" class="form-label">Criteria</label>
                <select class="form-select" id="criteria_id" name="criteria_id" required>
                    @foreach($criterias as $criteria)
                        <option value="{{ $criteria->id }}" {{ $score->criteria_id == $criteria->id ? 'selected' : '' }}>
                            {{ $criteria->name }} ({{ $criteria->weight }}%)
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-3">
                <label for="score" class="form-label">Score (0-100)</label>
                <input type="number" class="form-control" id="score" name="score" value="{{ $score->score }}" min="0" max="100" step="0.01" required>
            </div>
            
            <div class="mb-3">
                <label for="remarks" class="form-label">Remarks (Optional)</label>
                <textarea class="form-control" id="remarks" name="remarks" rows="3">{{ $score->remarks }}</textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Score</button>
            <a href="{{ route('scores.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
