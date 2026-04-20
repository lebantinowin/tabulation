@extends('layouts.app')

@section('title', 'Audit Logs - Admin')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-clipboard-list"></i> Audit Logs</h1>
</div>

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

@isset($error)
<div class="alert alert-danger">
    {{ $error }}
</div>
@endisset

    @if($auditLogs->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($auditLogs as $log)
                <tr>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td>
                        <span class="badge">{{ $log->action }}</span>
                    </td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->ip_address ?? 'N/A' }}</td>
                    <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; flex-wrap: wrap; gap: 1rem;">
            <div class="pagination-info">
                Showing {{ $auditLogs->firstItem() ?? 0 }} to {{ $auditLogs->lastItem() ?? 0 }} of {{ $auditLogs->total() ?? 0 }} results
            </div>
            <div>
                {{ $auditLogs->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @else
        <div class="text-center" style="padding: 2rem;">
            <p style="color: var(--color-muted); font-size: 1.1rem;">
                <i class="fas fa-info-circle"></i> No audit logs found. The table may not exist yet.
            </p>
            <p style="color: var(--color-muted);">Please run <code>php artisan migrate</code> to create the necessary tables.</p>
        </div>
    @endif

@endsection
