@extends('layouts.app')

@section('title', 'Login')

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
    }
    
    .login-header p {
        color: #666;
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }
    
    .login-tabs {
        display: flex;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #ddd;
    }
    
    .login-tab {
        flex: 1;
        padding: 0.75rem;
        text-align: center;
        cursor: pointer;
        font-weight: 600;
        color: #666;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: all 0.3s ease;
    }
    
    .login-tab.active {
        color: #040D12;
        border-bottom-color: #040D12;
    }
    
    .login-tab:hover {
        color: #040D12;
    }
    
    .login-form {
        display: none;
    }
    
    .login-form.active {
        display: block;
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
    
    .code-display {
        background: #f8f9fa;
        border: 2px dashed #040D12;
        padding: 1rem;
        border-radius: 8px;
        text-align: center;
        margin-bottom: 1rem;
    }
    
    .code-display .code {
        font-size: 1.5rem;
        font-weight: bold;
        letter-spacing: 4px;
        color: #040D12;
    }
    
    .code-display .label {
        font-size: 0.8rem;
        color: #666;
        margin-bottom: 0.25rem;
    }
</style>

<div class="login-page">
    <div class="login-card">
        <div class="login-header">
            <h2>Welcome</h2>
            <p>Sign in to continue</p>
        </div>
        
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p style="margin: 0;">{{ $error }}</p>
                @endforeach
            </div>
        @endif
        
        <div class="login-tabs">
            <div class="login-tab active" onclick="switchTab('admin')">Admin</div>
            <div class="login-tab" onclick="switchTab('judge')">Judge</div>
        </div>
        
        <form method="POST" action="{{ route('login') }}" id="login-form">
            @csrf
            <input type="hidden" name="login_type" id="login_type" value="admin">
            
            <!-- Admin Login Form -->
            <div class="login-form active" id="admin-form">
                <div class="floating-group">
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder=" ">
                    <label for="email">Email Address</label>
                </div>
                
                <div class="floating-group">
                    <input type="password" id="password" name="password" required placeholder=" ">
                    <label for="password">Password</label>
                </div>
                
                <button type="submit" class="btn">Sign In</button>
            </div>
            
            <!-- Judge Login Form -->
            <div class="login-form" id="judge-form">
                <div class="floating-group">
                    <input type="text" id="login_code" name="login_code" placeholder=" " maxlength="6" style="text-transform: uppercase; letter-spacing: 4px; text-align: left; font-size: 1.2rem; padding-top: 1.5rem; padding-bottom: 0.5rem;">
                    <label for="login_code">Login Code</label>
                </div>
                
                <button type="submit" class="btn">Login with Code</button>
            </div>
        </form>
        
        <div class="login-footer">
            <a href="{{ route('landing') }}">← Back to Home</a>
        </div>
    </div>
</div>

<script>
function switchTab(type) {
    document.querySelectorAll('.login-tab').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.login-form').forEach(form => form.classList.remove('active'));
    
    if (type === 'admin') {
        document.querySelectorAll('.login-tab')[0].classList.add('active');
        document.getElementById('admin-form').classList.add('active');
        document.getElementById('login_type').value = 'admin';
    } else {
        document.querySelectorAll('.login-tab')[1].classList.add('active');
        document.getElementById('judge-form').classList.add('active');
        document.getElementById('login_type').value = 'judge';
    }
}

// Auto-uppercase the login code
document.getElementById('login_code')?.addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>
@endsection
