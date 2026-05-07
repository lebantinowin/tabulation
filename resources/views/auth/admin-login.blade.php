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
    
    .floating-group {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .floating-group input {
        width: 100%;
        padding: 1.25rem 1rem 0.5rem;
        font-size: 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        background: #fff;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    
    .floating-group input:focus {
        border-color: #040D12;
        box-shadow: 0 0 0 3px rgba(4, 13, 18, 0.1);
    }
    
    .floating-group label {
        position: absolute;
        top: 50%;
        left: 1rem;
        transform: translateY(-50%);
        color: #718096;
        font-size: 1rem;
        pointer-events: none;
        transition: all 0.2s ease;
        background: #fff;
        padding: 0 0.25rem;
        margin: 0;
    }
    
    .floating-group input:focus ~ label,
    .floating-group input:not(:placeholder-shown) ~ label {
        top: 0;
        transform: translateY(-50%) scale(0.85);
        color: #040D12;
        font-weight: 600;
    }
    
    .login-card .btn {
        width: 100%;
        padding: 0.85rem;
        font-size: 1.05rem;
        font-weight: 600;
        margin-top: 1rem;
        border-radius: 8px;
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
            
            <div class="floating-group">
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                    placeholder=" "
                >
                <label for="email">Email Address</label>
            </div>
            
            <div class="floating-group">
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    placeholder=" "
                >
                <label for="password">Password</label>
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

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        return;
    }
    
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.style.opacity = '0.8';

    const words = ['Authenticating...', 'Verifying...', 'Logging in...'];
    let i = 0;
    btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${words[i]}`;
    
    const interval = setInterval(() => {
        i++;
        if (i < words.length) {
            btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${words[i]}`;
        } else {
            clearInterval(interval);
            this.submit();
        }
    }, 500);
});
</script>
@endsection
