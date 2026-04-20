@extends('layouts.app')

@section('title', 'My Scores - Judge')

@section('content')
<div class="page-header">
    <h1>My Scores</h1>
    <a href="{{ route('scores.create') }}" class="btn btn-primary" title="Submit New Score">
        <i class="fas fa-plus"></i> Submit New Score
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Contestant</th>
            <th>Criteria</th>
            <th>Score</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($scores as $score)
        <tr>
            <td>{{ $score->id }}</td>
            <td>{{ $score->contestant->name ?? 'N/A' }}</td>
            <td>{{ $score->criteria->name ?? 'N/A' }}</td>
            <td>{{ $score->score }}</td>
            <td>{{ $score->created_at->format('M d, Y') }}</td>
            <td>
                <div class="actions" style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('scores.show', $score->id) }}" class="btn-action btn-action-view" title="View Score">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('scores.edit', $score->id) }}" class="btn-action btn-action-edit" title="Edit Score">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">No scores submitted yet.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<a href="{{ route('judge.dashboard') }}" class="btn">Back to Dashboard</a>

<style>
.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-action-view {
    background: #697565;
    color: white;
}

.btn-action-view:hover {
    background: #3C3D37;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-action-edit {
    background: #D4A574;
    color: white;
}

.btn-action-edit:hover {
    background: #b8956a;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
</style>
@endsection
