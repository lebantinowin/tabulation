@extends('layouts.app')

@section('title', 'Create Criteria - Admin')

@section('content')
<div class="page-header">
    <h1>Create New Criteria</h1>
    <a href="{{ $selectedEventId ? route('events.show', $selectedEventId) : route('criteria.index') }}" class="btn">Back</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('criteria.store') }}" id="criteriaForm">
        @csrf
        
        <input type="hidden" name="event_id" value="{{ $selectedEventId }}">
        
        @if(!$selectedEventId)
        <div class="form-group">
            <label for="event_id">Event</label>
            <select id="event_id" name="event_id" required>
                <option value="">Select Event</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ $selectedEventId == $event->id ? 'selected' : '' }}>
                        {{ $event->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @else
        <div class="form-group">
            <label>Event</label>
            <p>{{ $events->find($selectedEventId)->name ?? 'N/A' }}</p>
        </div>
        @endif
        
        <div class="form-group">
            <label for="name">Criteria Name</label>
            <input type="text" id="name" name="name" required>
        </div>
        
        <div class="form-group">
            <label for="weight">Weight (%)</label>
            <input type="number" id="weight" name="weight" min="0" max="100" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"></textarea>
        </div>
        
        <h3>Sub-Criteria (Optional)</h3>
        <p style="color: var(--color-muted); margin-bottom: 1rem; font-size: 0.9rem;">
            Add sub-criteria if you want to break down this criteria into smaller parts. Leave empty if not needed.
        </p>
        <div id="sub-criteria-container">
            <div class="sub-criteria-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="flex: 2; margin-bottom: 0;">
                    <label>Sub-Criteria Name</label>
                    <input type="text" name="sub_criteria[0][name]" placeholder="e.g., Talent">
                </div>
                <div class="form-group" style="flex: 1; margin-bottom: 0;">
                    <label>Percentage (%)</label>
                    <input type="number" name="sub_criteria[0][percentage]" min="0" max="100" placeholder="e.g., 50">
                </div>
                <div class="form-group" style="display: flex; align-items: flex-end; margin-bottom: 0;">
                    <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.sub-criteria-row').remove()">×</button>
                </div>
            </div>
        </div>
        
        <button type="button" class="btn btn-sm" onclick="addSubCriteria()" style="margin-bottom: 1.5rem;">+ Add Sub-Criteria</button>
        
        <div class="actions" style="margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Create Criteria</button>
            <a href="{{ $selectedEventId ? route('events.show', $selectedEventId) : route('criteria.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
let subCriteriaCount = 1;
function addSubCriteria() {
    const container = document.getElementById('sub-criteria-container');
    const html = `
        <div class="sub-criteria-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
            <div class="form-group" style="flex: 2; margin-bottom: 0;">
                <label>Sub-Criteria Name</label>
                <input type="text" name="sub_criteria[` + subCriteriaCount + `][name]" placeholder="e.g., Talent">
            </div>
            <div class="form-group" style="flex: 1; margin-bottom: 0;">
                <label>Percentage (%)</label>
                <input type="number" name="sub_criteria[` + subCriteriaCount + `][percentage]" min="0" max="100" placeholder="e.g., 50">
            </div>
            <div class="form-group" style="display: flex; align-items: flex-end; margin-bottom: 0;">
                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.sub-criteria-row').remove()">×</button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    subCriteriaCount++;
}
</script>
@endsection
