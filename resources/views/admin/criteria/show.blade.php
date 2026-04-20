@extends('layouts.app')

@section('title', 'View Criteria - Admin')

@section('content')
<div class="page-header">
    <h1>Criteria Details</h1>
    <a href="{{ route('criteria.index') }}" class="btn" title="Back to Criteria List">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <h2>{{ $criteria->name }}</h2>
    
    <div class="form-group">
        <label>Event:</label>
        <p>{{ $criteria->event->name ?? 'N/A' }}</p>
    </div>
    
    <div class="form-group">
        <label>Weight:</label>
        <p>{{ $criteria->weight }}%</p>
    </div>
    
    <div class="form-group">
        <label>Description:</label>
        <p>{{ $criteria->description }}</p>
    </div>
    
    @if($criteria->subCriteria->count() > 0)
    <div class="form-group">
        <label>Sub-Criteria:</label>
        <table style="margin-top: 1rem;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($criteria->subCriteria as $subCriteria)
                <tr>
                    <td>{{ $subCriteria->name }}</td>
                    <td>{{ $subCriteria->percentage }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <div class="actions" style="margin-top: 1.5rem; display: flex; gap: 0.5rem;">
        <a href="{{ route('criteria.edit', $criteria->id) }}" class="btn-action btn-action-edit" title="Edit Criteria">
            <i class="fas fa-edit"></i> Edit
        </a>
        <form action="{{ route('criteria.destroy', $criteria->id) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-action btn-action-delete" onclick="return confirm('Are you sure you want to delete this criteria?')" title="Delete Criteria">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
    </div>
</div>

<style>
.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    color: white;
    font-size: 0.9rem;
}

.btn-action-edit {
    background: #D4A574;
}

.btn-action-edit:hover {
    background: #b8956a;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-action-delete {
    background: #8B4513;
}

.btn-action-delete:hover {
    background: #6B3410;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
</style>
@endsection
