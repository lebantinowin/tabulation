@extends('layouts.app')

@section('title', 'Recycle Bin - Admin')

@section('content')
<div class="page-header">
    <h1>Recycle Bin</h1>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif


<div class="card">
    <ul class="nav-tabs">
        <li><button class="tab-btn active" onclick="openTab(event, 'events')">Events</button></li>
        <li><button class="tab-btn" onclick="openTab(event, 'contestants')">Contestants</button></li>
        <li><button class="tab-btn" onclick="openTab(event, 'judges')">Judges</button></li>
        <li><button class="tab-btn" onclick="openTab(event, 'scores')">Scores</button></li>
    </ul>

    <!-- Events Tab -->
    <div id="events" class="tab-content" style="display: block;">
        @if($deletedEvents->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deletedEvents as $event)
                    <tr>
                        <td>{{ $event->name }}</td>
                        <td>{{ $event->deleted_at->format('M d, Y H:i') }}</td>
                        <td>
                            <div class="actions">
                                <form method="POST" action="{{ route('trash.restore.event', $event->id) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-icon btn-icon-view" title="Restore Event">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('trash.force-delete.event', $event->id) }}" style="display:inline;" id="forceDelEvent{{ $event->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-icon btn-icon-delete" title="Permanently Delete Event" onclick="confirmForm(document.getElementById('forceDelEvent{{ $event->id }}'), 'This cannot be undone. The record will be permanently erased.', {title: 'Permanently Delete?'})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color: var(--color-muted);">No deleted events.</p>
        @endif
        @if($deletedEvents->hasPages())
        <div class="mt-4 flex justify-between items-center" style="flex-wrap: wrap; gap: 1rem;">
            <span style="font-size: 0.85rem; color: var(--color-muted);">Showing {{ $deletedEvents->firstItem() }} to {{ $deletedEvents->lastItem() }} of {{ $deletedEvents->total() }}</span>
            {{ $deletedEvents->appends(request()->except('events_page') + [])->links('pagination::custom') }}
        </div>
        @endif
    </div>

    <!-- Contestants Tab -->
    <div id="contestants" class="tab-content" style="display: none;">
        @if($deletedContestants->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Event</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deletedContestants as $contestant)
                    <tr>
                        <td>{{ $contestant->name }}</td>
                        <td>{{ $contestant->event ? $contestant->event->name : 'N/A' }}</td>
                        <td>{{ $contestant->deleted_at->format('M d, Y H:i') }}</td>
                        <td>
                            <div class="actions">
                                <form method="POST" action="{{ route('trash.restore.contestant', $contestant->id) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-icon btn-icon-view" title="Restore Contestant">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('trash.force-delete.contestant', $contestant->id) }}" style="display:inline;" id="forceDelContestant{{ $contestant->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-icon btn-icon-delete" title="Permanently Delete Contestant" onclick="confirmForm(document.getElementById('forceDelContestant{{ $contestant->id }}'), 'This cannot be undone. The record will be permanently erased.', {title: 'Permanently Delete?'})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color: var(--color-muted);">No deleted contestants.</p>
        @endif
        @if($deletedContestants->hasPages())
        <div class="mt-4 flex justify-between items-center" style="flex-wrap: wrap; gap: 1rem;">
            <span style="font-size: 0.85rem; color: var(--color-muted);">Showing {{ $deletedContestants->firstItem() }} to {{ $deletedContestants->lastItem() }} of {{ $deletedContestants->total() }}</span>
            {{ $deletedContestants->appends(request()->except('contestants_page') + [])->links('pagination::custom') }}
        </div>
        @endif
    </div>

    <!-- Judges Tab -->
    <div id="judges" class="tab-content" style="display: none;">
        @if($deletedJudges->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deletedJudges as $judge)
                    <tr>
                        <td>{{ $judge->name }}</td>
                        <td>{{ $judge->email }}</td>
                        <td>{{ $judge->deleted_at->format('M d, Y H:i') }}</td>
                        <td>
                            <div class="actions">
                                <form method="POST" action="{{ route('trash.restore.judge', $judge->id) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-icon btn-icon-view" title="Restore Judge">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('trash.force-delete.judge', $judge->id) }}" style="display:inline;" id="forceDelJudge{{ $judge->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-icon btn-icon-delete" title="Permanently Delete Judge" onclick="confirmForm(document.getElementById('forceDelJudge{{ $judge->id }}'), 'This cannot be undone. The record will be permanently erased.', {title: 'Permanently Delete?'})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color: var(--color-muted);">No deleted judges.</p>
        @endif
        @if($deletedJudges->hasPages())
        <div class="mt-4 flex justify-between items-center" style="flex-wrap: wrap; gap: 1rem;">
            <span style="font-size: 0.85rem; color: var(--color-muted);">Showing {{ $deletedJudges->firstItem() }} to {{ $deletedJudges->lastItem() }} of {{ $deletedJudges->total() }}</span>
            {{ $deletedJudges->appends(request()->except('judges_page') + [])->links('pagination::custom') }}
        </div>
        @endif
    </div>

    <!-- Scores Tab -->
    <div id="scores" class="tab-content" style="display: none;">
        @if($deletedScores->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Judge</th>
                        <th>Contestant</th>
                        <th>Criteria</th>
                        <th>Score</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deletedScores as $score)
                    <tr>
                        <td>{{ $score->judge ? $score->judge->name : 'N/A' }}</td>
                        <td>{{ $score->contestant ? $score->contestant->name : 'N/A' }}</td>
                        <td>{{ $score->criteria ? $score->criteria->name : 'N/A' }}</td>
                        <td>{{ $score->score }}</td>
                        <td>{{ $score->deleted_at->format('M d, Y H:i') }}</td>
                        <td>
                            <div class="actions">
                                <form method="POST" action="{{ route('trash.restore.score', $score->id) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-icon btn-icon-view" title="Restore Score">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('trash.force-delete.score', $score->id) }}" style="display:inline;" id="forceDelScore{{ $score->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-icon btn-icon-delete" title="Permanently Delete Score" onclick="confirmForm(document.getElementById('forceDelScore{{ $score->id }}'), 'This cannot be undone. The record will be permanently erased.', {title: 'Permanently Delete?'})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color: var(--color-muted);">No deleted scores.</p>
        @endif
        @if($deletedScores->hasPages())
        <div class="mt-4 flex justify-between items-center" style="flex-wrap: wrap; gap: 1rem;">
            <span style="font-size: 0.85rem; color: var(--color-muted);">Showing {{ $deletedScores->firstItem() }} to {{ $deletedScores->lastItem() }} of {{ $deletedScores->total() }}</span>
            {{ $deletedScores->appends(request()->except('scores_page') + [])->links('pagination::custom') }}
        </div>
        @endif
    </div>
</div>

<script>
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tab-btn");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}
</script>
@endsection
