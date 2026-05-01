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
                    <a href="{{ route('criteria.show', $criteria->id) }}" class="btn-icon btn-icon-view" title="View Criteria Details">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('criteria.edit', $criteria->id) }}" class="btn-icon btn-icon-edit" title="Edit Criteria">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('criteria.destroy', $criteria->id) }}" method="POST" style="display: inline;" id="deleteCriteriaForm{{ $criteria->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-icon btn-icon-delete" onclick="confirmForm(document.getElementById('deleteCriteriaForm{{ $criteria->id }}'), 'This criteria will be removed from all events.', {title: 'Delete Criteria?'})" title="Delete Criteria">
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


@endsection
