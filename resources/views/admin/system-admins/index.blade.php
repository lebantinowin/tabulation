@extends('layouts.app')

@section('title', 'Admin Accounts')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-user-shield text-muted"></i> Admin Accounts</h1>
    <a href="{{ route('system-admins.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Admin
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Created</th>
            <th>Status</th>
            <th style="text-align: right;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($admins as $admin)
        <tr>
            <td>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--color-btn); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.95rem; flex-shrink: 0;">
                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                    </div>
                    <strong>{{ $admin->name }}</strong>
                </div>
            </td>
            <td>{{ $admin->email ?? '—' }}</td>
            <td>{{ $admin->created_at->format('M d, Y') }}</td>
            <td>
                <span class="badge badge-success">Active</span>
            </td>
            <td style="text-align: right;">
                <div class="actions" style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                    <a href="{{ route('system-admins.edit', $admin->id) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('system-admins.destroy', $admin->id) }}" method="POST" onsubmit="return confirm('Delete this admin account?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center text-muted" style="padding: 2rem;">
                No admin accounts yet. <a href="{{ route('system-admins.create') }}">Create one</a>.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

@if($admins->hasPages())
<div style="margin-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div class="pagination-info">
        Showing {{ $admins->firstItem() }} to {{ $admins->lastItem() }} of {{ $admins->total() }} admins
    </div>
    {{ $admins->links('pagination::custom') }}
</div>
@endif
@endsection
