@extends('layouts.app')

@section('title', 'Criteria - Admin')

@section('content')
<div class="page-header">
    <h1>Criteria Management</h1>
    <a href="{{ route('criteria.create') }}" class="btn btn-primary" title="Create New Criteria">
        <i class="fas fa-plus"></i> Create Criteria
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Weight</th>
            <th>Event</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($criterias as $criteria)
        <tr>
            <td>{{ $criteria->name }}</td>
            <td>{{ $criteria->weight }}%</td>
            <td>{{ $criteria->event->name ?? 'N/A' }}</td>
            <td>
                <div class="actions" style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('criteria.show', $criteria->id) }}" class="btn-action btn-action-view" title="View Criteria Details">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('criteria.edit', $criteria->id) }}" class="btn-action btn-action-edit" title="Edit Criteria">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('criteria.destroy', $criteria->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-action-delete" onclick="return confirm('Are you sure you want to delete this criteria?')" title="Delete Criteria">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">No criteria found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

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

.btn-action-delete {
    background: #8B4513;
    color: white;
}

.btn-action-delete:hover {
    background: #6B3410;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
</style>
@endsection
