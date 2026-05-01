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
                    <form action="{{ route('judges.toggleActive', $judge->id) }}" method="POST" style="display: inline;" id="toggleForm{{ $judge->id }}">
                        @csrf
                        <button type="button"
                            class="btn-icon {{ $judge->is_active ? 'btn-icon-delete' : 'btn-icon-view' }}"
                            onclick="confirmForm(document.getElementById('toggleForm{{ $judge->id }}'), 'Are you sure you want to {{ $judge->is_active ? 'deactivate' : 'activate' }} this judge?', {title: '{{ $judge->is_active ? 'Deactivate' : 'Activate' }} Judge?', danger: '{{ $judge->is_active ? 'high' : 'medium' }}'})"
                            title="{{ $judge->is_active ? 'Deactivate' : 'Activate' }} Judge">
                            <i class="fas fa-{{ $judge->is_active ? 'ban' : 'check' }}"></i>
                        </button>
                    </form>
                    <a href="{{ route('judges.show', $judge->id) }}" class="btn-icon btn-icon-view" title="View Judge Details">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('judges.edit', $judge->id) }}" class="btn-icon btn-icon-edit" title="Edit Judge">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('judges.destroy', $judge->id) }}" method="POST" style="display: inline;" id="deleteJudgeForm{{ $judge->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-icon btn-icon-delete" onclick="confirmForm(document.getElementById('deleteJudgeForm{{ $judge->id }}'), 'This judge will be deleted. This action cannot be undone.', {title: 'Delete Judge?'})" title="Delete Judge">
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


@endsection
