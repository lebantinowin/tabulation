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
            <th style="text-align: center;">Status</th>
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
                    <div>
                        <strong>{{ $admin->name }}</strong>
                        @if(!$admin->password_changed)
                            <br><small style="color: var(--color-warning, #d97706);">Pending setup</small>
                        @endif
                    </div>
                </div>
            </td>
            <td>{{ $admin->email ?? '—' }}</td>
            <td>{{ $admin->created_at->format('M d, Y') }}</td>
            <td style="text-align: center;">
                <span class="badge {{ $admin->is_active ? 'badge-success' : 'badge-secondary' }}">
                    {{ $admin->is_active ? 'Active' : 'Inactive' }}
                </span>
            </td>
            <td style="text-align: right;">
                <div style="display: flex; gap: 0.4rem; justify-content: flex-end;">
                    <!-- Toggle Active -->
                    <form action="{{ route('system-admins.toggleActive', $admin->id) }}" method="POST" id="toggleAdmin{{ $admin->id }}">
                        @csrf
                        <button type="button" 
                            class="btn-icon {{ $admin->is_active ? 'btn-icon-delete' : 'btn-icon-view' }}"
                            title="{{ $admin->is_active ? 'Deactivate Admin' : 'Activate Admin' }}"
                            onclick="confirmForm(document.getElementById('toggleAdmin{{ $admin->id }}'), 'Are you sure you want to {{ $admin->is_active ? 'deactivate' : 'activate' }} this admin?', {title: '{{ $admin->is_active ? 'Deactivate' : 'Activate' }} Admin?', danger: '{{ $admin->is_active ? 'high' : 'medium' }}'})">
                            <i class="fas fa-{{ $admin->is_active ? 'ban' : 'check' }}"></i>
                        </button>
                    </form>
                    <a href="{{ route('system-admins.show', $admin->id) }}" class="btn-icon btn-icon-view" title="View Details">
                        <i class="fas fa-eye"></i>
                    </a>
                    <form action="{{ route('system-admins.destroy', $admin->id) }}" method="POST" id="deleteAdmin{{ $admin->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-icon btn-icon-delete" title="Delete" onclick="confirmForm(document.getElementById('deleteAdmin{{ $admin->id }}'), 'This admin account will be permanently deleted.', {title: 'Delete Admin?'})">
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
<div style="margin-top: 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <span style="font-size: 0.85rem; color: var(--color-muted);">Showing {{ $admins->firstItem() }} to {{ $admins->lastItem() }} of {{ $admins->total() }} admins</span>
    {{ $admins->links('pagination::custom') }}
</div>
@endif

{{-- ─── Contribution Reports Section ─── --}}
<div style="margin-top: 2.5rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
        <h2 style="font-size: 1.2rem; font-weight: 700; margin: 0;">
            <i class="fas fa-clipboard-list" style="margin-right: 8px; color: var(--color-muted);"></i>
            Contribution Reports
            @php $unread = $reports->where('is_read', false)->count(); @endphp
            @if($unread > 0)
                <span class="badge badge-danger" style="font-size: 0.75rem; margin-left: 6px; background: #dc2626;">{{ $unread }} new</span>
            @endif
        </h2>
    </div>

    @forelse($reports as $report)
    <div style="border: 1px solid var(--color-border); border-radius: 10px; padding: 1.25rem 1.5rem; margin-bottom: 0.75rem; background: {{ $report->is_read ? 'var(--color-white)' : 'rgba(59,130,246,0.04)' }}; position: relative;">
        <div style="display: flex; align-items: flex-start; gap: 1rem;">
            <div style="width: 36px; height: 36px; border-radius: 50%; background: {{ $report->type === 'bug' ? '#dc2626' : 'var(--color-btn)' }}; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 0.85rem;">
                <i class="fas fa-{{ $report->type === 'bug' ? 'bug' : 'clipboard-check' }}"></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 4px;">
                    <span class="badge {{ $report->type === 'bug' ? 'badge-danger' : 'badge-success' }}" style="{{ $report->type === 'bug' ? 'background:#dc2626;' : '' }} font-size: 0.7rem;">
                        {{ $report->type === 'bug' ? 'Bug Report' : 'Contribution' }}
                    </span>
                    <strong style="font-size: 0.95rem;">{{ $report->title }}</strong>
                    @if(!$report->is_read)
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6; display: inline-block;"></span>
                    @endif
                </div>
                <p style="margin: 0 0 8px; color: var(--color-text); font-size: 0.9rem; line-height: 1.5;">{{ $report->body }}</p>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span style="font-size: 0.8rem; color: var(--color-muted);">
                        <i class="fas fa-user" style="margin-right: 4px;"></i>{{ $report->admin->name ?? 'Unknown' }}
                    </span>
                    <span style="font-size: 0.8rem; color: var(--color-muted);">
                        <i class="fas fa-clock" style="margin-right: 4px;"></i>{{ $report->created_at->diffForHumans() }}
                    </span>
                    @if(!$report->is_read)
                        <form action="{{ route('system-admins.reports.read', $report->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm" style="padding: 0.2rem 0.7rem; font-size: 0.78rem; background: var(--color-btn);">
                                Mark as Read
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div style="text-align: center; padding: 2rem; color: var(--color-muted); border: 1px dashed var(--color-border); border-radius: 10px;">
        <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
        <p style="margin: 0;">No reports yet.</p>
    </div>
    @endforelse

    @if($reports->hasPages())
    <div style="margin-top: 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <span style="font-size: 0.85rem; color: var(--color-muted);">Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} reports</span>
        {{ $reports->appends(request()->query())->links('pagination::custom') }}
    </div>
    @endif
</div>
@endsection
