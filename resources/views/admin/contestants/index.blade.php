@extends('layouts.app')

@section('title', 'Contestants - Admin')

@section('content')
<div class="page-header">
    <h1>Contestants Management</h1>
    <a href="{{ route('contestants.create', $selectedEventId ? ['event_id' => $selectedEventId] : []) }}" class="btn btn-primary" title="Add New Contestant">
        <i class="fas fa-user-plus"></i> Add Contestant
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card" style="padding: 1rem 1.5rem; margin-bottom: 1rem;">
    <form method="GET" action="{{ route('contestants.index') }}" style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
        <label style="font-weight: 600; white-space: nowrap;"><i class="fas fa-calendar-alt" style="margin-right: 6px;"></i> Filter by Event:</label>
        <select name="event_id" onchange="this.form.submit()" style="flex: 1; min-width: 220px; max-width: 400px;">
            <option value="">— All Events —</option>
            @foreach($events as $event)
                <option value="{{ $event->id }}" {{ $selectedEventId == $event->id ? 'selected' : '' }}>
                    {{ $event->name }} ({{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }})
                </option>
            @endforeach
        </select>
        @if($selectedEventId)
            <a href="{{ route('contestants.index') }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.9rem;">Clear</a>
        @endif
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>Photo</th>
            <th>Name</th>
            <th>Number</th>
            <th>Event</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($contestants as $contestant)
        <tr>
            <td>
                <img src="{{ $contestant->image_url }}" alt="{{ $contestant->name }}" class="profile-image">
            </td>
            <td>{{ $contestant->name }}</td>
            <td>{{ $contestant->number }}</td>
            <td>{{ $contestant->event->name ?? 'N/A' }}</td>
            <td>
                <div class="actions" style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('contestants.show', $contestant->id) }}" class="btn-icon btn-icon-view" title="View Contestant Details">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('contestants.edit', $contestant->id) }}" class="btn-icon btn-icon-edit" title="Edit Contestant">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('contestants.destroy', $contestant->id) }}" method="POST" style="display: inline;" id="deleteContestantForm{{ $contestant->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-icon btn-icon-delete" onclick="confirmForm(document.getElementById('deleteContestantForm{{ $contestant->id }}'), 'This contestant and all their scores will be deleted.', {title: 'Delete Contestant?'})" title="Delete Contestant">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">No contestants found{{ $selectedEventId ? ' for this event' : '' }}.</td>
        </tr>
        @endforelse
    </tbody>
</table>


@endsection
