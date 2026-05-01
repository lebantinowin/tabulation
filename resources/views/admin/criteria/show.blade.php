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
        <a href="{{ route('criteria.edit', $criteria->id) }}" class="btn btn-secondary" title="Edit Criteria">
            <i class="fas fa-edit"></i> Edit
        </a>
        <form action="{{ route('criteria.destroy', $criteria->id) }}" method="POST" style="display: inline;" id="deleteCriteriaShowForm">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-danger" onclick="confirmForm(document.getElementById('deleteCriteriaShowForm'), 'This criteria will be permanently removed from its event.', {title: 'Delete Criteria?'})" title="Delete Criteria">
                <i class="fas fa-trash"></i> Delete
            </button>
        </form>
    </div>
</div>


@endsection
