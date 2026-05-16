@extends('layouts.app')

@section('title', 'Edit Admin Account')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-user-shield text-muted"></i> Edit Admin Account</h1>
    <a href="{{ route('system-admins.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card" style="max-width: 600px;">
    <form action="{{ route('system-admins.update', $admin->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name"><i class="fas fa-user" style="margin-right: 6px;"></i> Full Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $admin->name) }}" required>
            @error('name') <div class="text-danger" style="font-size: 0.85rem; margin-top: 4px;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="email"><i class="fas fa-envelope" style="margin-right: 6px;"></i> Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email', $admin->email) }}" required>
            @error('email') <div class="text-danger" style="font-size: 0.85rem; margin-top: 4px;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password"><i class="fas fa-lock" style="margin-right: 6px;"></i> New Password <span style="font-weight: normal; color: var(--color-muted); font-size: 0.85rem;">(leave blank to keep current)</span></label>
            <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
            @error('password') <div class="text-danger" style="font-size: 0.85rem; margin-top: 4px;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation"><i class="fas fa-lock" style="margin-right: 6px;"></i> Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter new password">
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
            <a href="{{ route('system-admins.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
