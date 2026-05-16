@extends('layouts.app')

@section('title', 'Create Admin Account')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-user-shield text-muted"></i> Create Admin Account</h1>
    <a href="{{ route('system-admins.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card" style="max-width: 600px;">
    <form action="{{ route('system-admins.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name"><i class="fas fa-user" style="margin-right: 6px;"></i> Full Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. Juan dela Cruz">
            @error('name') <div class="text-danger" style="font-size: 0.85rem; margin-top: 4px;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="email"><i class="fas fa-envelope" style="margin-right: 6px;"></i> Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="e.g. admin@tabulation.com">
            @error('email') <div class="text-danger" style="font-size: 0.85rem; margin-top: 4px;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password"><i class="fas fa-lock" style="margin-right: 6px;"></i> Password</label>
            <input type="password" id="password" name="password" required placeholder="Minimum 6 characters">
            @error('password') <div class="text-danger" style="font-size: 0.85rem; margin-top: 4px;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation"><i class="fas fa-lock" style="margin-right: 6px;"></i> Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Re-enter password">
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Create Admin
            </button>
            <a href="{{ route('system-admins.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
