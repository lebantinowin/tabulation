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
        
        <div class="form-group" style="display: flex; gap: 1rem;">
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: baseline;">
                    <label for="weight">Weight (%)</label>
                    <span id="weightRemainingDisplay" class="text-muted" style="font-size: 0.8rem; font-weight: 600;">Max: 100%</span>
                </div>
                <input type="number" id="weight" name="weight" min="1" max="100" required>
            </div>
            <div style="flex: 1;">
                <label for="max_points">Max Points</label>
                <input type="number" id="max_points" name="max_points" min="1" value="100" required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"></textarea>
        </div>
        
        <div class="actions" style="margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Create Criteria</button>
            <a href="{{ $selectedEventId ? route('events.show', $selectedEventId) : route('criteria.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    const eventWeights = @json($eventWeights);
    const eventSelect = document.getElementById('event_id');
    const weightInput = document.getElementById('weight');
    const weightRemainingDisplay = document.getElementById('weightRemainingDisplay');
    const selectedEventId = "{{ $selectedEventId }}";

    function updateMaxWeight() {
        let currentEventId = null;
        
        if (eventSelect) {
            currentEventId = eventSelect.value;
        } else if (selectedEventId) {
            currentEventId = selectedEventId;
        }

        if (currentEventId && eventWeights[currentEventId] !== undefined) {
            let maxWeight = eventWeights[currentEventId];
            weightInput.max = maxWeight;
            
            if (maxWeight === 0) {
                weightRemainingDisplay.innerHTML = `<span style="color: var(--color-danger);">0% left (Full)</span>`;
                weightRemainingDisplay.title = "This event already has 100% total criteria weight.";
                weightInput.disabled = true;
                weightInput.value = "";
                weightInput.placeholder = "0";
            } else {
                weightRemainingDisplay.innerHTML = `Available: <span style="color: var(--color-success);">${maxWeight}%</span>`;
                weightInput.disabled = false;
                weightInput.placeholder = `Max ${maxWeight}`;
                if (parseFloat(weightInput.value) > maxWeight) {
                    weightInput.value = maxWeight;
                }
            }
        } else {
            weightInput.max = 100;
            weightRemainingDisplay.innerText = "Max: 100%";
            weightInput.disabled = false;
            weightInput.placeholder = "";
        }
    }

    if (eventSelect) {
        eventSelect.addEventListener('change', updateMaxWeight);
    }
    
    // Validate on input
    weightInput.addEventListener('input', function() {
        const max = parseFloat(this.max);
        const val = parseFloat(this.value);
        if (val > max) {
            this.value = max;
        }
    });

    // Initial call
    updateMaxWeight();
</script>
@endsection
