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
    <div class="profile-info">
        <h3>{{ auth()->user()->name }}</h3>
        <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
        <p><strong>Role:</strong> {{ ucfirst(auth()->user()->role) }}</p>
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
        
        <div class="form-group">
            <label for="password">New Password (leave blank to keep current)</label>
            <input type="password" id="password" name="password">
        </div>
        
        <div class="form-group">
            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation">
        </div>
        
        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit" class="btn btn-primary">Update Profile</button>
            <a href="{{ route('judge.dashboard') }}" class="btn">Back to Dashboard</a>
        </div>
    </form>
</div>
@endsection
