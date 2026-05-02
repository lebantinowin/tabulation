@extends('layouts.app')

@section('title', 'Judges - Admin')

@section('content')
<div class="page-header">
    <h1>Judges Management</h1>
    <a href="{{ route('judges.create', $selectedEventId ? ['event_id' => $selectedEventId] : []) }}" class="btn btn-primary" title="Add New Judge">
        <i class="fas fa-plus"></i> Add Judge
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card" style="padding: 1rem 1.5rem; margin-bottom: 1rem;">
    <form method="GET" action="{{ route('judges.index') }}" style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
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
            <a href="{{ route('judges.index') }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.9rem;">Clear</a>
        @endif
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>Photo</th>
            <th>Name</th>
            <th style="text-align: center;">Judge #</th>
            <th>Event</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($judges as $judge)
        <tr>
            <td>
                <img src="{{ $judge->image_url }}" alt="{{ $judge->name }}" class="user-avatar" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
            </td>
            <td>
                {{ $judge->name }}
                @if($judge->judge_number)
                    <br><small style="color: var(--color-muted);">Judge {{ $judge->judge_number }}</small>
                @endif
            </td>
            <td style="text-align: center;">
                @if($judge->judge_number)
                    <span class="badge badge-info" style="background: var(--color-btn); color: #fff; font-size: 0.85rem; padding: 0.3rem 0.6rem;">J{{ $judge->judge_number }}</span>
                @else
                    <span style="color: #ccc;">—</span>
                @endif
            </td>
            <td>
                @php $judgeEvent = \App\Models\Event::find($judge->event_id); @endphp
                @if($judgeEvent)
                    <span class="badge badge-info" style="font-size: 0.8rem;">{{ $judgeEvent->name }}</span>
                @else
                    <span style="color: #ccc;">—</span>
                @endif
            </td>
            <td>
                <span class="badge {{ $judge->is_active ? 'badge-success' : 'badge-secondary' }}">
                    {{ $judge->is_active ? 'Active' : 'Inactive' }}
                </span>
            </td>
            <td>
                <div class="actions" style="display: flex; gap: 0.5rem;">
                    <form action="{{ route('judges.toggleActive', $judge->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="button"
                            class="btn-icon {{ $judge->is_active ? 'btn-icon-delete' : 'btn-icon-view' }}"
                            onclick="confirmForm(this.closest('form'), 'Are you sure you want to {{ $judge->is_active ? 'deactivate' : 'activate' }} this judge?', {title: '{{ $judge->is_active ? 'Deactivate' : 'Activate' }} Judge?', danger: '{{ $judge->is_active ? 'high' : 'medium' }}'})"
                            title="{{ $judge->is_active ? 'Deactivate' : 'Activate' }} Judge">
                            <i class="fas fa-{{ $judge->is_active ? 'ban' : 'check' }}"></i>
                        </button>
                    </form>
                    <a href="{{ route('judges.show', $judge->id) }}" class="btn-icon btn-icon-view" title="View Judge Details">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('judges.edit', $judge->id) }}" class="btn-icon btn-icon-edit" title="Edit Judge">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('judges.destroy', $judge->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-icon btn-icon-delete" onclick="confirmForm(this.closest('form'), 'This judge will be deleted. This action cannot be undone.', {title: 'Delete Judge?'})" title="Delete Judge">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">No judges found{{ $selectedEventId ? ' for this event' : '' }}.</td>
        </tr>
        @endforelse
    </tbody>
</table>


@endsection
