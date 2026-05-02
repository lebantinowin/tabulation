@extends('layouts.app')

@section('title', 'My Profile - Judge')

@section('content')
<div class="page-header">
    <h1>My Profile</h1>
    <a href="{{ route('judge.dashboard') }}" class="btn" title="Back to Dashboard">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="profile-info" style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
        <div>
            @if(auth()->user()->image)
                <img src="{{ asset('storage/' . auth()->user()->image) }}" alt="Profile Photo" style="width: 100px; height: 100px; object-fit: cover; border-radius: 12px; border: 3px solid var(--color-border);">
            @else
                <div style="width: 100px; height: 100px; background: var(--color-btn); color: white; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: bold; border-radius: 12px; border: 3px solid var(--color-border);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            @endif
        </div>
        <div>
            <h3 style="margin-bottom: 0.5rem; font-size: 1.5rem;">{{ auth()->user()->name }}</h3>
            <p style="margin-bottom: 0.5rem; color: var(--color-muted);"><strong>Role:</strong> <span class="badge badge-primary" style="background: var(--color-btn); color: white;">{{ ucfirst(auth()->user()->role) }}</span></p>
            <p style="margin-bottom: 0; color: var(--color-muted);">
                <strong>Login Code:</strong> 
                <span style="font-family: monospace; letter-spacing: 4px; background: #eee; padding: 4px 10px; border-radius: 6px; font-weight: bold; color: var(--color-btn); font-size: 1.1rem; margin-left: 0.5rem;">
                    {{ auth()->user()->login_code ?? 'N/A' }}
                </span>
            </p>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 1.5rem;">
    <h3>Update Profile</h3>
    <form method="POST" action="{{ route('judge.profile.update') }}">
        @csrf
        
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ auth()->user()->name }}" required>
        </div>
        

        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit" class="btn btn-primary">Update Profile</button>
            <a href="{{ route('judge.dashboard') }}" class="btn">Back to Dashboard</a>
        </div>
    </form>
</div>
@endsection
