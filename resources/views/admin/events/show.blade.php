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

<!-- Tabs Navigation -->
<div class="event-tabs" style="margin-bottom: 1.5rem; display: flex; border-bottom: 2px solid var(--color-border); gap: 1rem;">
    <button class="tab-btn active" onclick="switchTab('criteria', this)">Criteria</button>
    <button class="tab-btn" onclick="switchTab('judges', this)">Judges</button>
    <button class="tab-btn" onclick="switchTab('contestants', this)">Contestants</button>
</div>

<!-- Judges Section -->
<div id="tab-judges" class="tab-pane" style="display: none;">
<div class="card">
    <div class="page-header" style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: none;">
        <h2><i class="fas fa-gavel"></i> Judges</h2>
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('judges.create') }}?event_id={{ $event->id }}" class="btn-icon" style="background: #D4A574;" title="Add Judge">
            <i class="fas fa-plus"></i>
        </a>
        @endif
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
                <th style="text-align: center;">Judge #</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
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
                <td>
                    {{ $judge->name }}
                    @if($judge->judge_number)
                        <br><small style="color: var(--color-muted);">Judge {{ $judge->judge_number }}</small>
                    @endif
                </td>
                <td style="text-align: center;">
                    @if($judge->judge_number)
                        <span class="badge badge-info" style="background: var(--color-btn); color: #fff;">J{{ $judge->judge_number }}</span>
                    @else
                        <span style="color: #ccc;">—</span>
                    @endif
                </td>
                <td>{{ $judge->email ?? 'N/A' }}</td>
                <td>
                    @if($judge->agreement_accepted)
                    <span class="badge badge-success">Active</span>
                    @else
                    <span class="badge badge-warning">Pending</span>
                    @endif
                </td>
                <td>
                    <div class="actions">
                        @if(auth()->user()->isSuperAdmin())
                        <form action="{{ route('judges.toggleActive', $judge->id) }}" method="POST" style="display: inline;" id="toggleJudgeForm{{ $judge->id }}">
                            @csrf
                            <button type="button"
                                class="btn-icon {{ $judge->is_active ? 'btn-icon-delete' : 'btn-icon-view' }}"
                                onclick="confirmForm(document.getElementById('toggleJudgeForm{{ $judge->id }}'), 'Are you sure you want to {{ $judge->is_active ? 'deactivate' : 'activate' }} this judge?', {title: '{{ $judge->is_active ? 'Deactivate' : 'Activate' }} Judge?', danger: '{{ $judge->is_active ? 'high' : 'medium' }}'})"
                                title="{{ $judge->is_active ? 'Deactivate' : 'Activate' }} Judge">
                                <i class="fas fa-{{ $judge->is_active ? 'ban' : 'check' }}"></i>
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('judges.show', $judge->id) }}" class="btn-icon btn-icon-view" title="View Judge Details" style="background: #040D12;">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('judges.exportPdf', $judge->id) }}" class="btn-icon" target="_blank" title="Print Credentials" style="background-color: var(--color-success); color: white;">
                            <i class="fas fa-print"></i>
                        </a>
                        <a href="{{ route('judges.edit', $judge->id) }}" class="btn-icon btn-icon-edit" title="Edit Judge">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('judges.destroy', $judge->id) }}" method="POST" style="display: inline;" id="deleteJudgeForm{{ $judge->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn-icon btn-icon-delete" onclick="confirmForm(document.getElementById('deleteJudgeForm{{ $judge->id }}'), 'This judge will be removed from the event.', {title: 'Delete Judge?'})" title="Delete Judge">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="color: var(--color-muted);">No judges added yet. <a href="{{ route('judges.create', ['event_id' => $event->id]) }}">Add a judge</a></p>
    @endif
</div>
</div>

<!-- Criteria Section -->
<div id="tab-criteria" class="tab-pane active" style="display: block;">
<div class="card">
    <div class="page-header" style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: none;">
        <h2><i class="fas fa-list"></i> Criteria</h2>
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('criteria.create') }}?event_id={{ $event->id }}" class="btn-icon" style="background: #D4A574;" title="Add Criteria">
            <i class="fas fa-plus"></i>
        </a>
        @endif
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
                        @if(auth()->user()->isSuperAdmin())
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
                        @endif
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
</div>

<!-- Contestants Section -->
<div id="tab-contestants" class="tab-pane" style="display: none;">
<div class="card">
    <div class="page-header" style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: none;">
        <h2><i class="fas fa-users"></i> Contestants</h2>
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('contestants.create') }}?event_id={{ $event->id }}" class="btn-icon" style="background: #D4A574;" title="Add Contestant">
            <i class="fas fa-user-plus"></i>
        </a>
        @endif
    </div>
    
    @php
    $contestants = $event->contestants;
    @endphp
    
    @if($contestants && $contestants->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Photo</th>
                <th>Name</th>
                <th>Number</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contestants as $contestant)
            <tr>
                <td>
                    @php
                    $imagePath = $contestant->image;
                    $fullPath = '';
                    $imageFound = false;
                    
                    $possiblePaths = [];
                    if ($imagePath) {
                        if (str_contains($imagePath, 'storage/')) {
                            $possiblePaths[] = $imagePath;
                        } elseif (str_contains($imagePath, 'contestants/')) {
                            $possiblePaths[] = 'storage/' . $imagePath;
                            $possiblePaths[] = $imagePath;
                        } else {
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
                        <img src="{{ asset($fullPath) }}" alt="{{ $contestant->name }}" class="profile-image-sm">
                    @else
                        <div class="user-avatar" style="width: 40px; height: 40px; font-size: 1rem;">
                            {{ strtoupper(substr($contestant->name, 0, 1)) }}
                        </div>
                    @endif
                </td>
                <td>{{ $contestant->name }}</td>
                <td>{{ $contestant->number }}</td>
                <td>
                    <div class="actions">
                        <a href="{{ route('contestants.show', $contestant->id) }}" class="btn-icon btn-icon-view" style="background: #040D12;" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('contestants.edit', $contestant->id) }}" class="btn-icon btn-icon-edit" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('contestants.destroy', $contestant->id) }}" method="POST" style="display: inline;" id="deleteContestantForm{{ $contestant->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn-icon btn-icon-delete" onclick="confirmForm(document.getElementById('deleteContestantForm{{ $contestant->id }}'), 'This contestant will be removed from the event.', {title: 'Delete Contestant?'})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="color: var(--color-muted);">No contestants added to this event yet.</p>
    @endif
</div>
</div>

<script>
function switchTab(tabId, btn) {
    // Hide all tab panes
    document.querySelectorAll('.tab-pane').forEach(el => el.style.display = 'none');
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    
    // Show target tab pane
    document.getElementById('tab-' + tabId).style.display = 'block';
    
    // Add active class to clicked button
    btn.classList.add('active');
}
</script>

<style>
.tab-btn {
    background: none;
    border: none;
    padding: 0.75rem 1.5rem;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--color-muted);
    cursor: pointer;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    transition: all 0.3s ease;
}

.tab-btn:hover {
    color: var(--color-text);
}

.tab-btn.active {
    color: var(--color-primary);
    border-bottom-color: var(--color-primary);
}
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
