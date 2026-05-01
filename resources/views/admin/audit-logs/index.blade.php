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
        
        <div class="flex justify-between items-center mt-3 gap-3" style="flex-wrap: wrap;">
            <div class="pagination-info">
                Showing {{ $auditLogs->firstItem() ?? 0 }} to {{ $auditLogs->lastItem() ?? 0 }} of {{ $auditLogs->total() ?? 0 }} results
            </div>
            <div>
            <div class="custom-pagination" style="display: flex; gap: 0.5rem;">
                @if ($auditLogs->onFirstPage())
                    <button class="btn btn-icon" style="background: var(--color-muted); cursor: not-allowed; opacity: 0.5;" disabled title="Previous Page">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                @else
                    <a href="{{ $auditLogs->previousPageUrl() }}" class="btn btn-icon" style="background: var(--color-btn);" title="Previous Page">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif

                @if ($auditLogs->hasMorePages())
                    <a href="{{ $auditLogs->nextPageUrl() }}" class="btn btn-icon" style="background: var(--color-btn);" title="Next Page">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <button class="btn btn-icon" style="background: var(--color-muted); cursor: not-allowed; opacity: 0.5;" disabled title="Next Page">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                @endif
            </div>
            </div>
        </div>
        @else
        <div class="text-center p-4">
            <p class="mb-2 text-muted" style="font-size: 1.1rem;">
                <i class="fas fa-info-circle"></i> No audit logs found. The table may not exist yet.
            </p>
            <p class="text-muted">Please run <code>php artisan migrate</code> to create the necessary tables.</p>
        </div>
    @endif

@endsection
