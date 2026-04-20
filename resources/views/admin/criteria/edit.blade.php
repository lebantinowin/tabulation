@extends('layouts.app')

@section('title', 'Edit Criteria - Admin')

@section('content')
<div class="page-header">
    <h1>Edit Criteria</h1>
    <a href="{{ route('criteria.index') }}" class="btn">Back to List</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('criteria.update', $criteria->id) }}">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="event_id">Event</label>
            <select id="event_id" name="event_id" required>
                @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ $criteria->event_id == $event->id ? 'selected' : '' }}>
                        {{ $event->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="name">Criteria Name</label>
            <input type="text" id="name" name="name" value="{{ $criteria->name }}" required>
        </div>
        
        <div class="form-group">
            <label for="weight">Weight (%)</label>
            <input type="number" id="weight" name="weight" value="{{ $criteria->weight }}" min="0" max="100" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3">{{ $criteria->description }}</textarea>
        </div>
        
        <h3>Sub-Criteria (Optional)</h3>
        <div id="sub-criteria-container">
            @forelse($criteria->subCriteria as $index => $subCriteria)
            <div class="sub-criteria-row" style="display: flex; gap: 1rem; margin-top: 1rem;">
                <div class="form-group" style="flex: 2;">
                    <label>Sub-Criteria Name</label>
                    <input type="text" name="sub_criteria[{{ $index }}][name]" value="{{ $subCriteria->name }}">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Percentage (%)</label>
                    <input type="number" name="sub_criteria[{{ $index }}][percentage]" value="{{ $subCriteria->percentage }}" min="0" max="100">
                </div>
                <div class="form-group" style="display: flex; align-items: flex-end;">
                    <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.sub-criteria-row').remove()">Remove</button>
                </div>
            </div>
            @empty
            <div class="sub-criteria-row">
                <div class="form-group" style="flex: 2;">
                    <label>Sub-Criteria Name</label>
                    <input type="text" name="sub_criteria[0][name]" placeholder="e.g., Talent">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Percentage (%)</label>
                    <input type="number" name="sub_criteria[0][percentage]" min="0" max="100" placeholder="e.g., 50">
                </div>
            </div>
            @endforelse
        </div>
        
        <button type="button" class="btn btn-sm" onclick="addSubCriteria()">+ Add Sub-Criteria</button>
        
        <div class="actions" style="margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Update Criteria</button>
            <a href="{{ route('criteria.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
let subCriteriaCount = {{ $criteria->subCriteria->count() }};
function addSubCriteria() {
    const container = document.getElementById('sub-criteria-container');
    const html = `
        <div class="sub-criteria-row" style="display: flex; gap: 1rem; margin-top: 1rem;">
            <div class="form-group" style="flex: 2;">
                <label>Sub-Criteria Name</label>
                <input type="text" name="sub_criteria[${subCriteriaCount}][name]" placeholder="e.g., Talent">
            </div>
            <div class="form-group" style="flex: 1;">
                <label>Percentage (%)</label>
                <input type="number" name="sub_criteria[${subCriteriaCount}][percentage]" min="0" max="100" placeholder="e.g., 50">
            </div>
            <div class="form-group" style="display: flex; align-items: flex-end;">
                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.sub-criteria-row').remove()">Remove</button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    subCriteriaCount++;
}
</script>
@endsection
