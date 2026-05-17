@extends('layouts.app')

@section('title', 'Create Part 2 - ' . $event->name)

@section('content')
<div class="page-header">
    <h1>Create Part 2 / Advance Round</h1>
    <a href="{{ route('events.show', $event->id) }}" class="btn" title="Back to Event">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <p style="margin-bottom: 1.5rem; color: var(--color-muted);">
        This tool allows you to easily create a follow-up round (like Top 3 or Finals). It will create a <strong>new event</strong> carrying over the selected contestants and judges. Your existing event's scores and setup will remain completely untouched.
    </p>

    <form action="{{ route('events.storePart2', $event->id) }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="name">New Event Name</label>
            <input type="text" name="name" id="name" value="{{ $event->name }} - Part 2" required class="form-control">
        </div>

        <div style="margin-top: 2rem;">
            <h3><i class="fas fa-users"></i> Select Contestants to Advance</h3>
            <p style="font-size: 0.9rem; color: var(--color-muted); margin-bottom: 1rem;">Choose which contestants should proceed to the next round.</p>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
                @foreach($contestants as $contestant)
                <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; cursor: pointer;">
                    <input type="checkbox" name="contestants[]" value="{{ $contestant->id }}">
                    <span>#{{ $contestant->number }} - {{ $contestant->name }}</span>
                </label>
                @endforeach
            </div>
            @error('contestants')
                <div style="color: var(--color-danger); margin-top: 0.5rem; font-size: 0.9rem;">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-top: 2rem;">
            <h3><i class="fas fa-gavel"></i> Judges to Carry Over</h3>
            <p style="font-size: 0.9rem; color: var(--color-muted); margin-bottom: 1rem;">Select judges to move to the new event. (Note: Judges can only be active in one event at a time).</p>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
                @foreach($judges as $judge)
                <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; border: 1px solid var(--color-border); border-radius: 8px; cursor: pointer;">
                    <input type="checkbox" name="judges[]" value="{{ $judge->id }}" checked>
                    <span>{{ $judge->name }} (J{{ $judge->judge_number }})</span>
                </label>
                @endforeach
            </div>
        </div>

        <div class="form-actions" style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
            <a href="{{ route('events.show', $event->id) }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Create Part 2</button>
        </div>
    </form>
</div>
@endsection
