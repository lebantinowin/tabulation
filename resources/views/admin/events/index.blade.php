@extends('layouts.app')

@section('title', 'Events - Admin')

@section('content')
<div class="page-header">
    <h1>Events Management</h1>
    @if(auth()->user()->isSuperAdmin())
    <a href="{{ route('events.create') }}" class="btn btn-primary" title="Create New Event">
        <i class="fas fa-plus"></i> Create Event
    </a>
    @endif
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
                @if($event->is_archived)
                    <span class="badge" style="background: var(--color-primary); color: white; margin-left: 0.5rem;">Archived</span>
                @endif
            </td>
            <td>
                <div class="actions">
                    <a href="{{ route('events.show', $event->id) }}" class="btn-icon btn-icon-view" title="View Event Details">
                        <i class="fas fa-eye"></i>
                    </a>
                    @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('events.edit', $event->id) }}" class="btn-icon btn-icon-edit" title="Edit Event">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('events.destroy', $event->id) }}" method="POST" style="display: inline;" id="deleteEventForm{{ $event->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-icon btn-icon-delete" onclick="confirmForm(document.getElementById('deleteEventForm{{ $event->id }}'), 'This event and all its data will be permanently deleted.', {title: 'Delete Event?'})" title="Delete Event">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @if($event->is_archived)
                    <form action="{{ route('events.unarchive', $event->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-icon" style="background: var(--color-secondary);" title="Unarchive Event">
                            <i class="fas fa-box-open"></i>
                        </button>
                    </form>
                    @else
                    <form action="{{ route('events.archive', $event->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-icon" style="background: var(--color-secondary);" title="Archive Event">
                            <i class="fas fa-archive"></i>
                        </button>
                    </form>
                    @endif
                    @endif
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

@if($events->hasPages())
<div class="mt-4 flex justify-between items-center" style="flex-wrap: wrap; gap: 1rem;">
    <div class="pagination-info" style="font-size: 0.85rem; color: var(--color-muted);">
        Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} events
    </div>
    {{ $events->links('pagination::custom') }}
</div>
@endif
@endsection
