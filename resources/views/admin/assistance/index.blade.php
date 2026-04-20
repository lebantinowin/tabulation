@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Assistance Requests</h1>
        <div>
            <span class="text-muted">Auto-refresh in: <span id="countdown">60</span>s</span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Judge</th>
                        <th>Event</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    <tr>
                        <td>{{ $request->id }}</td>
                        <td>{{ $request->judge->name }}</td>
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
                        <td>
                            @if(!$request->is_confirmed)
                                <form action="{{ route('assistance.confirm', $request->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Confirm</button>
                                </form>
                            @endif
                            <form action="{{ route('assistance.destroy', $request->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No assistance requests.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
let countdown = 60;
setInterval(function() {
    countdown--;
    document.getElementById('countdown').textContent = countdown;
    if (countdown <= 0) {
        countdown = 60;
        location.reload();
    }
}, 1000);
</script>
@endsection
