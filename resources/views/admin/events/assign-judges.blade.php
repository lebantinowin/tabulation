@extends('layouts.app')

@section('title', 'Assign Judges - ' . $event->name)

@section('content')
<div class="page-header">
    <h1>Assign Judges to Event</h1>
    <a href="{{ route('events.show', $event->id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Event
    </a>
</div>

<div class="card">
    <h2>{{ $event->name }}</h2>
    <p style="color: #666;">
        <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}
    </p>
    
    <p style="margin-top: 1rem;">Select the judges that will be assigned to this event. Assigned judges will only be able to see and score this event in their dashboard.</p>
</div>

@if($allJudges->count() == 0)
    <div class="alert alert-warning">
        No judges found. Please create judges first from the <a href="{{ route('judges.create') }}">Judges Management</a> page.
    </div>
@else
    <form action="{{ route('events.storeAssignedJudges', $event->id) }}" method="POST">
        @csrf
        
        <div class="card">
            <h3>Available Judges</h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; margin-top: 1rem;">
                @foreach($allJudges as $judge)
                    <label style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.2s ease; {{ in_array($judge->id, $assignedJudgeIds) ? 'border-color: var(--color-btn); background-color: rgba(212, 165, 116, 0.1);' : '' }}">
                        <input type="checkbox" name="judges[]" value="{{ $judge->id }}" {{ in_array($judge->id, $assignedJudgeIds) ? 'checked' : '' }} style="width: 20px; height: 20px; cursor: pointer;">
                        <div>
                            <strong>{{ $judge->name }}</strong>
                            @if($judge->email)
                                <br><small style="color: #666;">{{ $judge->email }}</small>
                            @endif
                            @if(in_array($judge->id, $assignedJudgeIds))
                                <br><span class="badge badge-success" style="margin-top: 0.25rem;">Assigned</span>
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
        
        <div style="margin-top: 1.5rem; display: flex; gap: 0.5rem;">
            <button type="submit" class="btn-icon" style="background: #D4A574;" title="Save">
                <i class="fas fa-save"></i>
            </button>
            <a href="{{ route('events.show', $event->id) }}" class="btn-icon" style="background: #6c757d;" title="Cancel">
                <i class="fas fa-times"></i>
            </a>
        </div>
    </form>
@endif

<style>
    label:hover {
        border-color: var(--color-btn) !important;
    }
</style>
@endsection

