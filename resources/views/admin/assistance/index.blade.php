@extends('layouts.app')

@section('title', 'Assistance Requests - Admin')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-hands-helping"></i> Assistance Requests</h1>
    <span class="text-muted" style="font-size: 0.9rem;">Auto-refresh in: <span id="countdown">60</span>s</span>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<table>
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
                    <span class="badge badge-success">Confirmed</span>
                @else
                    <span class="badge badge-warning">Pending</span>
                @endif
            </td>
            <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
            <td>
                <div class="actions">
                    @if(!$request->is_confirmed)
                        <form action="{{ route('assistance.confirm', $request->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-icon btn-icon-view" title="Confirm Request">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('assistance.destroy', $request->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-icon btn-icon-delete" onclick="confirmForm(this.closest('form'), 'This assistance request will be permanently removed.', {title: 'Delete Request?'})" title="Delete Request">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center text-muted p-4">No assistance requests at this time.</td>
        </tr>
        @endforelse
    </tbody>
</table>

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
