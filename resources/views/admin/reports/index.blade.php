@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-clipboard-list text-muted"></i> Reports</h1>
    <a href="{{ route('admin.reports.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Report
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div style="margin-bottom: 1.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
    <div style="flex: 1; min-width: 180px; background: var(--color-white); border: 1px solid var(--color-border); border-radius: 10px; padding: 1rem 1.25rem;">
        <div style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-muted); margin-bottom: 4px;">Contribution Reports</div>
        <div style="font-size: 1.6rem; font-weight: 700;">{{ $contributions->total() }}</div>
    </div>
</div>

@forelse($contributions as $report)
<div style="border: 1px solid var(--color-border); border-radius: 10px; padding: 1.25rem 1.5rem; margin-bottom: 0.75rem; background: var(--color-white);">
    <div style="display: flex; align-items: flex-start; gap: 1rem;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: {{ $report->type === 'bug' ? '#dc2626' : 'var(--color-btn)' }}; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 0.85rem;">
            <i class="fas fa-{{ $report->type === 'bug' ? 'bug' : 'clipboard-check' }}"></i>
        </div>
        <div style="flex: 1;">
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 4px; align-items: center;">
                <span class="badge {{ $report->type === 'bug' ? 'badge-danger' : 'badge-success' }}" style="{{ $report->type === 'bug' ? 'background:#dc2626;' : '' }} font-size: 0.7rem;">
                    {{ $report->type === 'bug' ? 'Bug Report' : 'Contribution' }}
                </span>
                <strong>{{ $report->title }}</strong>
            </div>
            <p style="margin: 0 0 8px; font-size: 0.9rem; color: var(--color-text); line-height: 1.5;">{{ $report->body }}</p>
            <div style="display: flex; gap: 1rem; font-size: 0.8rem; color: var(--color-muted);">
                <span><i class="fas fa-user" style="margin-right: 4px;"></i>{{ $report->admin->name ?? 'Unknown' }}</span>
                <span><i class="fas fa-clock" style="margin-right: 4px;"></i>{{ $report->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>
</div>
@empty
<div style="text-align: center; padding: 3rem; color: var(--color-muted); border: 1px dashed var(--color-border); border-radius: 10px;">
    <i class="fas fa-inbox" style="font-size: 2.5rem; margin-bottom: 0.75rem;"></i>
    <p style="margin: 0; font-size: 0.95rem;">No reports yet. <a href="{{ route('admin.reports.create') }}">Submit your first report</a>.</p>
</div>
@endforelse

@if($contributions->hasPages())
<div style="margin-top: 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <span style="font-size: 0.85rem; color: var(--color-muted);">Showing {{ $contributions->firstItem() }} to {{ $contributions->lastItem() }} of {{ $contributions->total() }} reports</span>
    {{ $contributions->links('pagination::custom') }}
</div>
@endif
@endsection
