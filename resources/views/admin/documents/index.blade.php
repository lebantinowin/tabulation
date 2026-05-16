@extends('layouts.app')

@section('title', 'Documents')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-folder-open text-muted"></i> Documents</h1>
</div>

<div style="width: 100%; padding-bottom: 120px;">
    <div style="border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); background: var(--color-white);">
        <table style="overflow: visible;">
            <thead>
                <tr>
                    <th style="border-top-left-radius: 12px;">Event Name</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th style="text-align: right; border-top-right-radius: 12px;">Downloads</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                <tr>
                    <td @if($loop->last && count($events) > 0) style="border-bottom-left-radius: 12px;" @endif><strong>{{ $event->name }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</td>
                    <td>
                        <span class="badge {{ $event->status == 'completed' ? 'badge-secondary' : ($event->status == 'ongoing' ? 'badge-success' : 'badge-warning') }}">
                            {{ ucfirst($event->status) }}
                        </span>
                    </td>
                    <td style="text-align: right; @if($loop->last && count($events) > 0) border-bottom-right-radius: 12px; @endif">
                        <a href="{{ route('tabulation.print', ['event_id' => $event->id]) }}" target="_blank" class="btn btn-sm" style="background: var(--color-info); color: white; display: inline-flex; align-items: center; gap: 0.3rem;">
                            <i class="fas fa-file-pdf"></i> Overall Results
                        </a>
                        
                        @if($event->criteria->count() > 0)
                        <div style="position: relative; display: inline-block; text-align: left;" class="doc-dropdown-wrap">
                            <button type="button" onclick="toggleDropdown('docMenu_{{ $event->id }}')" class="btn btn-sm" style="background: var(--color-secondary); color: white; display: inline-flex; align-items: center; gap: 0.3rem;">
                                <i class="fas fa-file-alt"></i> Category Results <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i>
                            </button>
                            <div id="docMenu_{{ $event->id }}" style="display:none; position:absolute; top:110%; right:0; background:#fff; border:1px solid #ddd; border-radius:8px; box-shadow:0 4px 16px rgba(0,0,0,0.12); min-width:180px; max-height: 250px; overflow-y:auto; z-index:999;">
                                @foreach($event->criteria as $criteria)
                                    <a href="{{ route('tabulation.print-category', ['criteriaId' => $criteria->id]) }}" target="_blank" style="display:block; padding:0.6rem 1rem; font-size:0.85rem; color:#333; text-decoration:none; border-bottom:1px solid #f0f0f0; white-space: nowrap;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='#fff'">
                                        <i class="fas fa-file-pdf" style="margin-right:6px; color:var(--color-info);"></i> {{ $criteria->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
                @if(count($events) == 0)
                <tr>
                    <td colspan="4" class="text-center text-muted">No events found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if($events->hasPages())
    <div class="mt-4 flex justify-between items-center" style="flex-wrap: wrap; gap: 1rem;">
        <div class="pagination-info">
            Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} events
        </div>
        {{ $events->links('pagination::custom') }}
    </div>
    @endif
</div>

<script>
function toggleDropdown(id) {
    const menu = document.getElementById(id);
    const isOpen = menu.style.display !== 'none';
    document.querySelectorAll('[id^="docMenu_"]').forEach(m => m.style.display = 'none');
    menu.style.display = isOpen ? 'none' : 'block';
}

document.addEventListener('click', function(e) {
    const isInside = e.target.closest('.doc-dropdown-wrap');
    if (!isInside) {
        document.querySelectorAll('[id^="docMenu_"]').forEach(m => m.style.display = 'none');
    }
});
</script>
@endsection
