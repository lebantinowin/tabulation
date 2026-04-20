@extends('layouts.app')

@section('title', 'Judges - Admin')

@section('content')
<div class="page-header">
    <h1>Judges Management</h1>
    <a href="{{ route('judges.create') }}" class="btn btn-primary" title="Add New Judge">
        <i class="fas fa-plus"></i> Add Judge
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
            <th>Email</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($judges as $judge)
        <tr>
            <td>
                @if($judge->image)
                    @php
                    $imagePath = $judge->image;
                    $fullPath = '';
                    $imageFound = false;
                    
                    // Build list of possible paths based on stored image path
                    $possiblePaths = [];
                    
                    if ($imagePath) {
                        // If the path already contains 'storage', use as-is
                        if (str_contains($imagePath, 'storage/')) {
                            $possiblePaths[] = $imagePath;
                        }
                        // If the path has judges prefix
                        elseif (str_contains($imagePath, 'judges/')) {
                            $possiblePaths[] = 'storage/' . $imagePath;
                            $possiblePaths[] = $imagePath;
                        }
                        // Just the filename
                        else {
                            $possiblePaths[] = 'storage/judges/' . $imagePath;
                            $possiblePaths[] = 'storage/' . $imagePath;
                            $possiblePaths[] = 'judges/' . $imagePath;
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
                    <img src="{{ asset($fullPath) }}" alt="{{ $judge->name }}" class="profile-image">
                    @else
                    <div class="user-avatar">
                        {{ strtoupper(substr($judge->name, 0, 1)) }}
                    </div>
                    @endif
                @else
                    <div class="user-avatar">
                        {{ strtoupper(substr($judge->name, 0, 1)) }}
                    </div>
                @endif
            </td>
            <td>{{ $judge->name }}</td>
            <td>{{ $judge->email }}</td>
            <td>
                <span class="badge {{ $judge->is_active ? 'badge-success' : 'badge-secondary' }}">
                    {{ $judge->is_active ? 'Active' : 'Inactive' }}
                </span>
            </td>
            <td>
                <div class="actions" style="display: flex; gap: 0.5rem;">
                    <form action="{{ route('judges.toggleActive', $judge->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-action {{ $judge->is_active ? 'btn-action-deactivate' : 'btn-action-activate' }}" 
                            onclick="return confirm('Are you sure you want to {{ $judge->is_active ? 'deactivate' : 'activate' }} this judge?')" 
                            title="{{ $judge->is_active ? 'Deactivate' : 'Activate' }} Judge">
                            <i class="fas fa-{{ $judge->is_active ? 'ban' : 'check' }}"></i>
                        </button>
                    </form>
                    <a href="{{ route('judges.show', $judge->id) }}" class="btn-action btn-action-view" title="View Judge Details">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('judges.edit', $judge->id) }}" class="btn-action btn-action-edit" title="Edit Judge">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('judges.destroy', $judge->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-action-delete" onclick="return confirm('Are you sure you want to delete this judge?')" title="Delete Judge">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">No judges found.</td>
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

.btn-action-activate {
    background: #28a745;
    color: white;
}

.btn-action-activate:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-action-deactivate {
    background: #dc3545;
    color: white;
}

.btn-action-deactivate:hover {
    background: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
</style>
@endsection
