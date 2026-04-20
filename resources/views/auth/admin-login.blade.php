@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
<style>
    .login-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #F5F5F0;
    }
    
    .login-card {
        background: #fff;
        border-radius: 16px;
        padding: 2.5rem;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        border: 1px solid #ddd;
    }
    
    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .login-header h2 {
        color: #040D12;
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }
    
    .login-header p {
        color: #666;
        font-size: 0.9rem;
    }
    
    .login-card .btn {
        width: 100%;
        padding: 0.85rem;
        font-size: 1rem;
        margin-top: 0.5rem;
    }
    
    .login-footer {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #eee;
    }
    
    .login-footer a {
        color: #666;
        text-decoration: none;
        font-weight: 500;
    }
    
    .login-footer a:hover {
        color: #040D12;
        text-decoration: underline;
    }
</style>

<div class="login-page">
    <div class="login-card">
        <div class="login-header">
            <h2><i class="fas fa-shield-alt"></i> Admin Login</h2>
            <p>Enter your credentials to access the admin panel</p>
        </div>
        
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p style="margin: 0;">{{ $error }}</p>
                @endforeach
            </div>
        @endif
        
        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                    placeholder="admin@example.com"
                >
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    placeholder="Enter your password"
                >
            </div>
            
            <button type="submit" class="btn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="login-footer">
            <a href="{{ route('landing') }}">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
    </div>
</div>
@endsection
