@extends('layouts.app')

@section('title', 'Add Judge - Admin')

@section('content')
<div class="page-header">
    <h1>Add New Judge</h1>
    <a href="{{ route('judges.index', $defaultEventId ? ['event_id' => $defaultEventId] : []) }}" class="btn">Back to List</a>
</div>

<div class="card">
    <form method="POST" action="{{ route('judges.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="event_id">Event <span style="color: var(--color-danger);">*</span></label>
            <select id="event_id" name="event_id" required onchange="updateTakenNumbers()">
                <option value="">— Select Event —</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ ($defaultEventId == $event->id || old('event_id') == $event->id) ? 'selected' : '' }}>
                        {{ $event->name }} ({{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }})
                    </option>
                @endforeach
            </select>
            <small style="color: var(--color-muted);">The event this judge will be scoring for</small>
        </div>

        <div class="form-group">
            <label for="name">Judge Name <span style="color: var(--color-danger);">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Full name">
        </div>

        <div class="form-group">
            <label for="judge_number">Judge Number <small style="color: var(--color-muted);">(e.g. 1 = "Judge 1" for this event)</small></label>
            <input type="number" id="judge_number" name="judge_number" value="{{ old('judge_number') }}"
                   min="1" max="99" placeholder="e.g. 1" oninput="checkJudgeNumber()">
            {{-- Server-side error --}}
            @error('judge_number')
                <small style="color: #e53e3e; display: block; margin-top: 4px;">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </small>
            @enderror
            {{-- JS live warning --}}
            <div id="judgeNumberWarning" style="display:none; margin-top: 6px; padding: 6px 10px; background: #fff3cd; border: 1px solid #f39c12; border-radius: 6px; color: #856404; font-size: 0.85rem;">
                <i class="fas fa-exclamation-triangle"></i> <span id="judgeNumberWarningText"></span>
            </div>
            <small style="color: var(--color-muted);">Numbers are per-event — Judge 1 in one event is separate from Judge 1 in another.</small>
            <div id="takenNumbersHint" style="margin-top: 4px; font-size: 0.82rem; color: var(--color-muted);"></div>
        </div>

        <div class="form-group">
            <label for="image">Profile Photo</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>

        <div class="actions">
            <button type="submit" id="submitBtn" class="btn btn-primary">Add Judge</button>
            <a href="{{ route('judges.index', $defaultEventId ? ['event_id' => $defaultEventId] : []) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
const takenNumbers = @json($takenNumbers);

function updateTakenNumbers() {
    const eventId = document.getElementById('event_id').value;
    const hint = document.getElementById('takenNumbersHint');
    const taken = takenNumbers[eventId] || [];

    if (taken.length > 0) {
        hint.innerHTML = '<i class="fas fa-info-circle"></i> Taken numbers for this event: <strong>' + taken.sort((a,b)=>a-b).map(n => 'J' + n).join(', ') + '</strong>';
    } else {
        hint.innerHTML = '';
    }
    checkJudgeNumber();
}

function checkJudgeNumber() {
    const eventId = document.getElementById('event_id').value;
    const num = parseInt(document.getElementById('judge_number').value);
    const warning = document.getElementById('judgeNumberWarning');
    const warningText = document.getElementById('judgeNumberWarningText');
    const submitBtn = document.getElementById('submitBtn');

    if (!eventId || !num) {
        warning.style.display = 'none';
        submitBtn.disabled = false;
        return;
    }

    const taken = takenNumbers[eventId] || [];
    if (taken.includes(num)) {
        warningText.textContent = 'Judge ' + num + ' is already taken for this event. Please choose a different number.';
        warning.style.display = 'block';
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.5';
    } else {
        warning.style.display = 'none';
        submitBtn.disabled = false;
        submitBtn.style.opacity = '';
    }
}

// Run on page load in case of old() values
document.addEventListener('DOMContentLoaded', function() {
    updateTakenNumbers();
});
</script>
@endsection
