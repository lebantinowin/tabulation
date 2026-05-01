@extends('layouts.app')

@section('title', 'View Event - Admin')

@section('content')
<div class="page-header">
    <h1>Event Details</h1>
    <a href="{{ route('events.index') }}" class="btn" title="Back to Events List">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

@if($event->banner)
<div class="event-banner" style="margin-bottom: 1.5rem; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);">
    @php
    $bannerPath = $event->banner;
    $fullBannerPath = '';
    $bannerFound = false;
    
    $possibleBannerPaths = [];
    
    if ($bannerPath) {
        if (str_contains($bannerPath, 'storage/')) {
            $possibleBannerPaths[] = $bannerPath;
        }
        elseif (str_contains($bannerPath, 'banners/')) {
            $possibleBannerPaths[] = 'storage/' . $bannerPath;
            $possibleBannerPaths[] = $bannerPath;
        }
        else {
            $possibleBannerPaths[] = 'storage/banners/' . $bannerPath;
            $possibleBannerPaths[] = 'storage/' . $bannerPath;
            $possibleBannerPaths[] = 'banners/' . $bannerPath;
            $possibleBannerPaths[] = $bannerPath;
        }
    }
    
    foreach ($possibleBannerPaths as $path) {
        if (file_exists(public_path($path))) {
            $fullBannerPath = $path;
            $bannerFound = true;
            break;
        }
    }
    @endphp
    @if($bannerFound && $fullBannerPath)
    <img src="{{ asset($fullBannerPath) }}" alt="{{ $event->name }}" style="width: 100%; height: 350px; object-fit: cover;">
    @endif
</div>
@endif

<div class="card">
    <h2>{{ $event->name }}</h2>
    
    <div class="form-group">
        <label>Date:</label>
        <p>{{ $event->date }}</p>
    </div>
    
    <div class="form-group">
        <label>Description:</label>
        <p>{{ $event->description }}</p>
    </div>
    
    <div class="form-group">
        <label>Status:</label>
        @php
            $statusBadge = '';
            $statusLabel = '';
            if($event->status == 'upcoming') {
                $statusBadge = 'badge-info';
                $statusLabel = 'Upcoming';
            } elseif($event->status == 'ongoing') {
                $statusBadge = 'badge-success';
                $statusLabel = 'Ongoing';
            } else {
                $statusBadge = 'badge-secondary';
                $statusLabel = 'Completed';
            }
        @endphp
        <span class="badge {{ $statusBadge }}">
            {{ $statusLabel }}
        </span>
    </div>
    
    <div class="actions" style="margin-top: 1.5rem; display: flex; gap: 0.5rem;">
        <a href="{{ route('events.edit', $event->id) }}" class="btn-icon btn-icon-edit" title="Edit Event">
            <i class="fas fa-edit"></i>
        </a>
        <form action="{{ route('events.destroy', $event->id) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="button" class="btn-icon btn-icon-delete" onclick="confirmForm(this.closest('form'), 'This event and all its associated data will be deleted.', {title: 'Delete Event?'})" title="Delete Event">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
</div>

<!-- Judges Section -->
<div class="card">
    <div class="page-header" style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: none;">
        <h2><i class="fas fa-gavel"></i> Judges</h2>
        <a href="{{ route('events.assignJudges', $event->id) }}" class="btn-icon" style="background: #D4A574;" title="Assign Judges">
            <i class="fas fa-user-plus"></i>
        </a>
    </div>
    
    @php
    $assignedJudges = \App\Models\User::where('event_id', $event->id)->where('role', 'judge')->get();
    @endphp
    
    @if($assignedJudges && $assignedJudges->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assignedJudges as $judge)
            <tr>
                <td>
                    @if($judge->image)
                        @php
                        $jp = $judge->image;
                        $fp = ''; $jf = false;
                        $pps = [];
                        if ($jp) {
                            if (str_contains($jp, 'storage/')) { $pps[] = $jp; }
                            elseif (str_contains($jp, 'judges/')) { $pps[] = 'storage/'.$jp; $pps[] = $jp; }
                            else { $pps[] = 'storage/judges/'.$jp; $pps[] = 'storage/'.$jp; $pps[] = 'judges/'.$jp; $pps[] = $jp; }
                        }
                        foreach ($pps as $p) { if (file_exists(public_path($p))) { $fp = $p; $jf = true; break; } }
                        @endphp
                        @if($jf && $fp)
                        <img src="{{ asset($fp) }}" alt="{{ $judge->name }}" class="profile-image-sm">
                        @else
                        <div class="user-avatar">{{ strtoupper(substr($judge->name,0,1)) }}</div>
                        @endif
                    @else
                    <div class="user-avatar">{{ strtoupper(substr($judge->name,0,1)) }}</div>
                    @endif
                </td>
                <td>{{ $judge->name }}</td>
                <td>{{ $judge->email ?? 'N/A' }}</td>
                <td>
                    @if($judge->agreement_accepted)
                    <span class="badge badge-success">Active</span>
                    @else
                    <span class="badge badge-warning">Pending</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="color: var(--color-muted);">No judges assigned. <a href="{{ route('events.assignJudges', $event->id) }}">Assign now</a></p>
    @endif
</div>

<!-- Criteria Section -->
<div class="card">
    <div class="page-header" style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: none;">
        <h2><i class="fas fa-list"></i> Criteria</h2>
        <a href="{{ route('criteria.create') }}?event_id={{ $event->id }}" class="btn-icon" style="background: #D4A574;" title="Add Criteria">
            <i class="fas fa-plus"></i>
        </a>
    </div>
    
    @if($event->criteria && $event->criteria->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Weight</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($event->criteria as $criteria)
            <tr>
                <td>{{ $criteria->name }}</td>
                <td>{{ $criteria->weight }}%</td>
                <td>{{ $criteria->description ?? 'N/A' }}</td>
                <td>
                    <div class="actions">
                        <a href="{{ route('criteria.edit', $criteria->id) }}" class="btn-icon btn-icon-edit" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                            <form action="{{ route('criteria.destroy', $criteria->id) }}" method="POST" style="display: inline;" id="delCriteriaShow{{ $criteria->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn-icon btn-icon-delete" onclick="confirmForm(document.getElementById('delCriteriaShow{{ $criteria->id }}'), 'This criteria will be removed from the event.', {title: 'Delete Criteria?'})" title="Delete">
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
    <p style="color: var(--color-muted);">No criteria defined for this event.</p>
    @endif
</div>

<!-- Results Section -->
<div class="card">
    <div class="page-header" style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: none;">
        <h2><i class="fas fa-chart-bar"></i> Results</h2>
    </div>
    
    @php
    $contestants = $event->contestants;
    $criteriaList = $event->criteria;
    @endphp
    
    @if($contestants && $contestants->count() > 0 && $criteriaList && $criteriaList->count() > 0)
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Contestant</th>
                @foreach($criteriaList as $criteria)
                <th>{{ $criteria->name }} ({{ $criteria->weight }}%)</th>
                @endforeach
                <th>Total</th>
                <th>Rank</th>
            </tr>
        </thead>
        <tbody>
            @php
            $rankings = [];
            foreach($contestants as $contestant) {
                $total = 0;
                foreach($criteriaList as $criteria) {
                    $score = $contestant->scores()->where('criteria_id', $criteria->id)->avg('score') ?? 0;
                    $total += $score * ($criteria->weight / 100);
                }
                $rankings[$contestant->id] = $total;
            }
            arsort($rankings);
            $rank = 1;
            @endphp
            
            @foreach($rankings as $contestantId => $totalScore)
            @php
            $contestant = $contestants->find($contestantId);
            @endphp
            <tr>
                <td>{{ $contestant->number }}</td>
                <td>
                    @php
                    $imagePath = $contestant->image;
                    $fullPath = '';
                    $imageFound = false;
                    
                    $possiblePaths = [];
                    
                    if ($imagePath) {
                        if (str_contains($imagePath, 'storage/')) {
                            $possiblePaths[] = $imagePath;
                        }
                        elseif (str_contains($imagePath, 'contestants/')) {
                            $possiblePaths[] = 'storage/' . $imagePath;
                            $possiblePaths[] = $imagePath;
                        }
                        else {
                            $possiblePaths[] = 'storage/contestants/' . $imagePath;
                            $possiblePaths[] = 'storage/' . $imagePath;
                            $possiblePaths[] = 'contestants/' . $imagePath;
                            $possiblePaths[] = $imagePath;
                        }
                    }
                    
                    foreach ($possiblePaths as $path) {
                        if (file_exists(public_path($path))) {
                            $fullPath = $path;
                            $imageFound = true;
                            break;
                        }
                    }
                    @endphp
                    @if($imageFound && $fullPath)
                    <img src="{{ asset($fullPath) }}" alt="{{ $contestant->name }}" class="profile-image-sm" style="margin-right: 10px;">
                    @endif
                    {{ $contestant->name }}
                </td>
                @foreach($criteriaList as $criteria)
                @php
                $score = $contestant->scores()->where('criteria_id', $criteria->id)->avg('score') ?? 0;
                @endphp
                <td>{{ number_format($score, 1) }}</td>
                @endforeach
                <td><strong>{{ number_format($totalScore, 2) }}</strong></td>
                <td>
                    @if($rank == 1)
                    <span class="badge badge-success">🥇 1st</span>
                    @elseif($rank == 2)
                    <span class="badge badge-warning">🥈 2nd</span>
                    @elseif($rank == 3)
                    <span class="badge" style="background: #CD7F32; color: white;">🥉 3rd</span>
                    @else
                    <span class="badge">{{ $rank }}th</span>
                    @endif
                </td>
            </tr>
            @php $rank++; @endphp
            @endforeach
        </tbody>
    </table>
    @else
    <p style="color: var(--color-muted);">No results available yet. Add criteria and scores to see results.</p>
    @endif
</div>

<style>
.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    color: white;
}

.btn-icon-edit {
    background: #D4A574;
}

.btn-icon-edit:hover {
    background: #b8956a;
}

.btn-icon-delete {
    background: #8B4513;
}

.btn-icon-delete:hover {
    background: #6B3410;
}
</style>
@endsection
