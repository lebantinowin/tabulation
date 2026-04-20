@extends('layouts.app')

@section('title', 'Contestants - Admin')

@section('content')
<div class="page-header">
    <h1>Contestants Management</h1>
    <a href="{{ route('contestants.create') }}" class="btn btn-primary" title="Add New Contestant">
        <i class="fas fa-user-plus"></i> Add Contestant
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table>
    <thead>
        <tr>
            <th>Photo</th>
            <th>Name</th>
            <th>Number</th>
            <th>Event</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($contestants as $contestant)
        <tr>
            <td>
                @php
                $imagePath = $contestant->image;
                $fullPath = '';
                $imageFound = false;
                
                // Build list of possible paths based on stored image path
                $possiblePaths = [];
                
                if ($imagePath) {
                    // If the path already contains 'storage', use as-is
                    if (str_contains($imagePath, 'storage/')) {
                        $possiblePaths[] = $imagePath;
                    }
                    // If the path has contestants prefix
                    elseif (str_contains($imagePath, 'contestants/')) {
                        $possiblePaths[] = 'storage/' . $imagePath;
                        $possiblePaths[] = $imagePath;
                    }
                    // Just the filename
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
                    <img src="{{ asset($fullPath) }}" alt="{{ $contestant->name }}" class="profile-image">
                @else
                    <div class="user-avatar">
                        {{ strtoupper(substr($contestant->name, 0, 1)) }}
                    </div>
                @endif
            </td>
            <td>{{ $contestant->name }}</td>
            <td>{{ $contestant->number }}</td>
            <td>{{ $contestant->event->name ?? 'N/A' }}</td>
            <td>
                <div class="actions" style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('contestants.show', $contestant->id) }}" class="btn-action btn-action-view" title="View Contestant Details">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('contestants.edit', $contestant->id) }}" class="btn-action btn-action-edit" title="Edit Contestant">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('contestants.destroy', $contestant->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-action-delete" onclick="return confirm('Are you sure you want to delete this contestant?')" title="Delete Contestant">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">No contestants found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<style>
.user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--color-btn);
    color: var(--color-white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.2rem;
    overflow: hidden;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-action-view {
    background: #697565;
    color: white;
}

.btn-action-view:hover {
    background: #3C3D37;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-action-edit {
    background: #D4A574;
    color: white;
}

.btn-action-edit:hover {
    background: #b8956a;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-action-delete {
    background: #8B4513;
    color: white;
}

.btn-action-delete:hover {
    background: #6B3410;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
</style>
@endsection
