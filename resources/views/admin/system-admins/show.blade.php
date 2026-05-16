@extends('layouts.app')

@section('title', 'Admin Details')

@section('content')
<div class="page-header" style="margin-bottom: 2rem;">
    <h1><i class="fas fa-user-shield text-muted"></i> Admin Details: {{ $admin->name }}</h1>
    <a href="{{ route('system-admins.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card" style="margin-bottom: 2rem;">
    <div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
        <div style="width: 100px; height: 100px; border-radius: 50%; background: var(--color-btn); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 3rem; flex-shrink: 0;">
            {{ strtoupper(substr($admin->name, 0, 1)) }}
        </div>
        <div style="flex: 1; min-width: 300px;">
            <h2 style="margin-bottom: 0.5rem; font-size: 1.5rem;">{{ $admin->name }}</h2>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <div style="color: var(--color-muted);">
                    <i class="fas fa-envelope" style="width: 20px;"></i> {{ $admin->email }}
                </div>
                <div style="color: var(--color-muted);">
                    <i class="fas fa-calendar-alt" style="width: 20px;"></i> Created: {{ $admin->created_at->format('M d, Y h:i A') }}
                </div>
                <div>
                    <span class="badge {{ $admin->is_active ? 'badge-success' : 'badge-secondary' }}">
                        {{ $admin->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    @if(!$admin->password_changed)
                        <span class="badge badge-warning" style="margin-left: 0.5rem;">Pending Password Setup</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="flex justify-between items-center mb-3">
        <h2><i class="fas fa-clipboard-list text-muted"></i> Contribution & Bug Reports</h2>
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
        <p style="margin: 0;">No reports filed by this admin yet.</p>
    </div>
    @endforelse

    @if($reports->hasPages())
    <div style="margin-top: 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <span style="font-size: 0.85rem; color: var(--color-muted);">Showing {{ $reports->firstItem() }} to {{ $reports->lastItem() }} of {{ $reports->total() }} reports</span>
        {{ $reports->links('pagination::custom') }}
    </div>
    @endif
</div>
@endsection
