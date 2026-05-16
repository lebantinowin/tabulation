@extends('layouts.app')

@section('title', 'Set Your Password')

@section('content')
<style>
    .setup-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #F5F5F0;
    }
    .setup-card {
        background: #fff;
        border-radius: 16px;
        padding: 2.5rem;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        border: 1px solid #ddd;
    }
    .setup-header { text-align: center; margin-bottom: 1.75rem; }
    .setup-header .icon-wrap {
        width: 64px; height: 64px; border-radius: 50%;
        background: var(--color-btn); color: white;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.75rem; margin: 0 auto 1rem;
    }
    .setup-header h2 { font-size: 1.5rem; margin: 0; }
    .setup-header p { color: var(--color-muted); font-size: 0.9rem; margin-top: 0.4rem; }
    .floating-group { position: relative; margin-bottom: 1.25rem; }
    .floating-group input {
        width: 100%; padding: 1.25rem 1rem 0.5rem;
        font-size: 1rem; border: 2px solid #e2e8f0;
        border-radius: 8px; background: #fff; outline: none;
        transition: border-color 0.2s;
    }
    .floating-group input:focus { border-color: var(--color-btn); }
    .floating-group label {
        position: absolute; top: 50%; left: 1rem;
        transform: translateY(-50%); color: #718096;
        font-size: 1rem; pointer-events: none;
        transition: all 0.2s; background: #fff; padding: 0 0.25rem; margin: 0;
    }
    .floating-group input:focus ~ label,
    .floating-group input:not(:placeholder-shown) ~ label {
        top: 0; transform: translateY(-50%) scale(0.85);
        color: var(--color-btn); font-weight: 600;
    }
    .setup-card .btn { width: 100%; padding: 0.85rem; font-size: 1rem; font-weight: 600; margin-top: 0.5rem; border-radius: 8px; }
</style>

<div class="setup-page">
    <div class="setup-card">
        <div class="setup-header">
            <div class="icon-wrap"><i class="fas fa-key"></i></div>
            <h2>Setup Admin Account</h2>
            <p>Please verify your email and set your password to continue.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error) <p style="margin:0;">{{ $error }}</p> @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.setup.complete') }}">
            @csrf
            <div class="floating-group">
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder=" " required>
                <label for="email">Your Email Address</label>
            </div>
            <div class="floating-group">
                <input type="password" id="password" name="password" placeholder=" " required>
                <label for="password">New Password</label>
            </div>
            <div class="floating-group">
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder=" " required>
                <label for="password_confirmation">Confirm Password</label>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-lock"></i> Set Password & Continue
            </button>
        </form>
    </div>
</div>
@endsection
