@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>My Assistance Requests</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Request Assistance Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Request Assistance</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('judge.assistance.request') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="event_id" class="form-label">Select Event</label>
                    <select name="event_id" id="event_id" class="form-control" required>
                        <option value="">-- Select Event --</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea name="message" id="message" class="form-control" rows="3" required placeholder="Describe what you need help with..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Request</button>
            </form>
        </div>
    </div>

    <!-- My Requests List -->
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    <tr>
                        <td>{{ $request->id }}</td>
                        <td>{{ $request->event->name }}</td>
                        <td>{{ $request->message }}</td>
                        <td>
                            @if($request->is_confirmed)
                                <span class="badge bg-success">Confirmed</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                        <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No assistance requests.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
