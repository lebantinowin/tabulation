@extends('layouts.app')

@section('title', 'Judge Login')

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
    
    .code-input {
        text-transform: uppercase;
        letter-spacing: 8px;
        text-align: center;
        font-size: 1.5rem !important;
        font-weight: bold;
    }
</style>

<div class="login-page">
    <div class="login-card">
        <div class="login-header">
            <h2><i class="fas fa-user-tie"></i> Judge Login</h2>
            <p>Enter your login code to continue</p>
        </div>
        
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p style="margin: 0;">{{ $error }}</p>
                @endforeach
            </div>
        @endif
        
<form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <label for="login_code">Login Code</label>
                <input 
                    type="text" 
                    id="login_code" 
                    name="login_code" 
                    class="code-input"
                    placeholder="XXXXXX" 
                    maxlength="6" 
                    required
                    autofocus
                >
                <small style="color: #666; display: block; margin-top: 0.5rem; text-align: center;">
                    Enter the 6-character code provided by admin
                </small>
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
// Auto-uppercase the login code
document.getElementById('login_code')?.addEventListener('input', function() {
    this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
});
</script>
@endsection
