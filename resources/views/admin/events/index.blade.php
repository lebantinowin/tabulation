@extends('layouts.app')

@section('title', 'Events - Admin')

@section('content')
<div class="page-header">
    <h1>Events Management</h1>
    <a href="{{ route('events.create') }}" class="btn btn-primary" title="Create New Event">
        <i class="fas fa-plus"></i> Create Event
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($events as $event)
        <tr>
            <td>{{ $event->name }}</td>
            <td>{{ $event->date }}</td>
            <td>
                @php
                    $statusBadge = '';
                    $statusLabel = '';
                    if($event->status == 'upcoming') {
                        $statusBadge = 'badge-warning';
                        $statusLabel = 'Upcoming';
                    } elseif($event->status == 'ongoing') {
                        $statusBadge = 'badge-success';
                        $statusLabel = 'Ongoing';
                    } elseif($event->status == 'completed') {
                        $statusBadge = 'badge-secondary';
                        $statusLabel = 'Completed';
                    } else {
                        $statusBadge = 'badge-secondary';
                        $statusLabel = $event->status;
                    }
                @endphp
                <span class="badge {{ $statusBadge }}">
                    {{ $statusLabel }}
                </span>
            </td>
            <td>
                <div class="actions" style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('events.show', $event->id) }}" class="btn-action btn-action-view" title="View Event Details">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('events.edit', $event->id) }}" class="btn-action btn-action-edit" title="Edit Event">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('events.destroy', $event->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-action-delete" onclick="return confirm('Are you sure you want to delete this event?')" title="Delete Event">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">No events found.</td>
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
