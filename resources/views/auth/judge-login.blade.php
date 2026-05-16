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
        padding-right: 2.5rem; /* Space for eye icon */
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .eye-icon {
        position: absolute;
        right: 15px;
        color: #888;
        cursor: pointer;
        font-size: 1.2rem;
        user-select: none;
    }
    
    .eye-icon:hover {
        color: #333;
    }

    .step-container {
        display: none;
        animation: fadeIn 0.3s ease;
    }

    .step-container.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .readonly-code {
        font-family: monospace;
        font-size: 1.2rem;
        letter-spacing: 4px;
        background: #f0f0f0;
        padding: 0.5rem;
        border-radius: 8px;
        text-align: center;
        color: #333;
        margin-bottom: 1rem;
        border: 1px solid #ddd;
    }
</style>

<div class="login-page">
    <div class="login-card">
        
        <div id="error-alert" class="alert alert-danger" style="display: none;"></div>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p style="margin: 0;">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- STEP 1: Enter Code -->
        <div id="step1" class="step-container active">
            <div class="login-header">
                <h2><i class="fas fa-user-tie"></i> Judge Login</h2>
                <p>Enter your login code to continue</p>
            </div>
            <form id="step1Form">
                <div class="form-group">
                    <label for="login_code">Login Code</label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            id="login_code" 
                            name="login_code" 
                            class="code-input"
                            placeholder="XXXXXX" 
                            maxlength="6" 
                            required
                            autofocus
                        >
                        <i class="fas fa-eye eye-icon" id="toggleCode" title="Hold to reveal"></i>
                    </div>
                    <small style="color: #666; display: block; margin-top: 0.5rem; text-align: center;">
                        Enter the 6-character code provided by admin
                    </small>
                </div>
                
                <button type="submit" class="btn" id="btnStep1">
                    Next <i class="fas fa-arrow-right"></i>
                </button>
            </form>
            
            <div class="login-footer">
                <a href="{{ route('landing') }}">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </div>
        </div>

        <!-- STEP 2: Enter/Create Password -->
        <div id="step2" class="step-container">
            <div class="login-header">
                <h2><i class="fas fa-lock"></i> Secure Login</h2>
                <p id="step2-greeting"></p>
            </div>
            
            <form id="step2Form" method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="login_code" id="step2_login_code">
                
                <div class="readonly-code" id="display_code">XXXXXX</div>

                <div class="form-group">
                    <label for="password" id="password-label">Password</label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            placeholder="Enter your password"
                        >
                        <i class="fas fa-eye eye-icon" id="togglePassword"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn" id="btnStep2">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
                <button type="button" class="btn btn-secondary" onclick="goBack()" style="background: #e0e0e0; color: #333; margin-top: 0.5rem;">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
            </form>
        </div>
        
    </div>
</div>

<script>
    const loginCodeInput = document.getElementById('login_code');
    const toggleCodeIcon = document.getElementById('toggleCode');
    const passwordInput = document.getElementById('password');
    const togglePasswordIcon = document.getElementById('togglePassword');
    const errorAlert = document.getElementById('error-alert');
    let revealTimeout;

    // Force uppercase and alnum for code
    loginCodeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });

    // Code Eye Icon Logic (Hold to reveal, click for split second)
    const revealCode = () => {
        loginCodeInput.type = 'text';
        toggleCodeIcon.classList.remove('fa-eye');
        toggleCodeIcon.classList.add('fa-eye-slash');
        clearTimeout(revealTimeout);
    };

    const hideCode = () => {
        loginCodeInput.type = 'password';
        toggleCodeIcon.classList.remove('fa-eye-slash');
        toggleCodeIcon.classList.add('fa-eye');
    };

    toggleCodeIcon.addEventListener('mousedown', revealCode);
    toggleCodeIcon.addEventListener('mouseup', hideCode);
    toggleCodeIcon.addEventListener('mouseleave', hideCode);

    // Touch support for mobile
    toggleCodeIcon.addEventListener('touchstart', revealCode);
    toggleCodeIcon.addEventListener('touchend', hideCode);

    toggleCodeIcon.addEventListener('click', function(e) {
        e.preventDefault();
        revealCode();
        revealTimeout = setTimeout(hideCode, 1000); // 1 second reveal
    });

    // Password Eye Icon Logic (Standard toggle)
    togglePasswordIcon.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            this.classList.remove('fa-eye');
            this.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            this.classList.remove('fa-eye-slash');
            this.classList.add('fa-eye');
        }
    });

    function showError(msg) {
        errorAlert.innerText = msg;
        errorAlert.style.display = 'block';
    }

    function hideError() {
        errorAlert.style.display = 'none';
    }

    function goBack() {
        hideError();
        document.getElementById('step2').classList.remove('active');
        document.getElementById('step1').classList.add('active');
        loginCodeInput.focus();
    }

    // Step 1 Submit
    document.getElementById('step1Form').addEventListener('submit', function(e) {
        e.preventDefault();
        hideError();
        
        const code = loginCodeInput.value;
        if (code.length < 6) return;

        const btn = document.getElementById('btnStep1');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
        btn.disabled = true;

        fetch('{{ route("login.verifyCode") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ login_code: code })
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;

            if (data.success) {
                // Switch to step 2
                document.getElementById('step1').classList.remove('active');
                document.getElementById('step2').classList.add('active');
                
                document.getElementById('step2_login_code').value = code;
                document.getElementById('display_code').innerText = code;
                
                const greeting = document.getElementById('step2-greeting');
                const pLabel = document.getElementById('password-label');
                
                if (data.password_changed) {
                    greeting.innerText = `Enter your password, ${data.name}`;
                    pLabel.innerText = "Password";
                    passwordInput.placeholder = "Enter your password";
                } else {
                    greeting.innerText = `Create your password, ${data.name}`;
                    pLabel.innerText = "New Password";
                    passwordInput.placeholder = "Create a secure password";
                }
                
                passwordInput.value = '';
                passwordInput.focus();
            } else {
                showError(data.message || 'Invalid login code.');
            }
        })
        .catch(err => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            showError('An error occurred. Please try again.');
        });
    });

    // Step 2 Submit Animation
    document.getElementById('step2Form').addEventListener('submit', function(e) {
        const btn = document.getElementById('btnStep2');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
        btn.disabled = true;
    });

</script>
@endsection
