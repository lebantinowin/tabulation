@extends('layouts.app')

@section('title', 'Create Admin Account')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-user-shield text-muted"></i> Create Admin Account</h1>
    <a href="{{ route('system-admins.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card" style="max-width: 540px;">
    <div style="background: rgba(59,130,246,0.06); border: 1px solid rgba(59,130,246,0.2); border-radius: 8px; padding: 0.9rem 1.1rem; margin-bottom: 1.5rem; font-size: 0.88rem; color: #1d4ed8;">
        <i class="fas fa-info-circle" style="margin-right: 6px;"></i>
        You only set the name and email. The admin will set their own password when they log in for the first time.
    </div>

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

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Create Admin Account
            </button>
            <a href="{{ route('system-admins.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
