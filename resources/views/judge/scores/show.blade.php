<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Score - Judge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Score Details</h2>
        
        <div class="card">
            <div class="card-body">
                <p><strong>Contestant:</strong> {{ $score->contestant->name ?? 'N/A' }}</p>
                <p><strong>Criteria:</strong> {{ $score->criteria->name ?? 'N/A' }}</p>
                <p><strong>Score:</strong> {{ $score->score }}</p>
                <p><strong>Remarks:</strong> {{ $score->remarks ?? 'N/A' }}</p>
                <p><strong>Date:</strong> {{ $score->created_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
        
        <a href="{{ route('scores.edit', $score->id) }}" class="btn btn-warning mt-3">Edit</a>
        <a href="{{ route('scores.index') }}" class="btn btn-secondary mt-3">Back to List</a>
    </div>
</body>
</html>
