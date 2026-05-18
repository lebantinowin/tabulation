@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="text-center" style="padding: 6rem 0; margin-top: 18px;">

    <h1>Tabulation System</h1>
    <p style="margin: 1.5rem 0; color: #666;">A comprehensive solution for judging and scoring events</p>

    <div style="display: flex; gap: 2rem; justify-content: center; margin-top: 3rem; flex-wrap: wrap;">
        <div class="card" style="padding: 2rem; width: 240px;">
            <h3><i class="fas fa-user-tie"></i> Judges</h3>
            <p style="color: #666; margin: 1rem 0;">Score contestants and provide feedback</p>
            <a href="{{ route('login') }}" class="btn">Judge Login</a>
        </div>
        <div class="card" style="padding: 2rem; width: 240px;">
            <h3><i class="fas fa-shield-alt"></i> Admins</h3>
            <p style="color: #666; margin: 1rem 0;">Manage events, judges, and contestants</p>
            <a href="{{ route('admin.login') }}" class="btn">Admin Login</a>
        </div>
        <div class="card" style="padding: 2rem; width: 240px;">
            <h3><i class="fas fa-crown"></i> Superadmin</h3>
            <p style="color: #666; margin: 1rem 0;">Full system access and administration</p>
            <a href="{{ route('superadmin.login') }}" class="btn" style="background: linear-gradient(135deg,#7a3cf0,#3a78f5); border: none;">Superadmin Login</a>
        </div>
    </div>

    <footer><p style="margin: 3rem 0 1rem; color: #666;">Powered By: ECCENTRI, Inc.</p></footer>
</div>
@endsection
