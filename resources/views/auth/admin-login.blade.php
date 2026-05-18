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

    /* Eye icon wrapper */
    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-wrapper input {
        width: 100%;
        padding: 1.25rem 2.75rem 0.5rem 1rem;
        font-size: 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        background: #fff;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .input-wrapper input:focus {
        border-color: #040D12;
        box-shadow: 0 0 0 3px rgba(4, 13, 18, 0.1);
    }

    .input-wrapper label {
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
        z-index: 1;
    }

    .input-wrapper input:focus ~ label,
    .input-wrapper input:not(:placeholder-shown) ~ label {
        top: 0;
        transform: translateY(-50%) scale(0.85);
        color: #040D12;
        font-weight: 600;
    }

    .eye-icon {
        position: absolute;
        right: 1rem;
        color: #888;
        cursor: pointer;
        font-size: 1.1rem;
        user-select: none;
        z-index: 2;
    }

    .eye-icon:hover { color: #333; }

    /* Hide native password reveal */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear { display: none !important; }

    /* Steps */
    .step-container { display: none; animation: fadeIn 0.3s ease; }
    .step-container.active { display: block; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Readonly email display */
    .readonly-email {
        font-size: 0.95rem;
        background: #f0f4f8;
        padding: 0.6rem 1rem;
        border-radius: 8px;
        text-align: center;
        color: #333;
        margin-bottom: 1.25rem;
        border: 1px solid #e2e8f0;
        word-break: break-all;
    }

    /* Error alert */
    #error-alert { display: none; }
</style>

<div class="login-page">
    <div class="login-card">

        <div id="error-alert" class="alert alert-danger"></div>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p style="margin:0;">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- STEP 1: Enter Email --}}
        <div id="step1" class="step-container active">
            <div class="login-header">
                <h2><i class="fas fa-shield-alt"></i> Admin Login</h2>
                <p>Enter your admin email to continue</p>
            </div>

            <form id="step1Form">
                <div class="floating-group">
                    <input
                        type="email"
                        id="admin_email"
                        name="email"
                        required
                        autofocus
                        placeholder=" "
                        autocomplete="username"
                    >
                    <label for="admin_email">Email Address</label>
                </div>

                <button type="submit" class="btn" id="btnStep1">
                    Next <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>

        {{-- STEP 2: Enter / Create Password --}}
        <div id="step2" class="step-container">
            <div class="login-header">
                <h2><i class="fas fa-lock"></i> <span id="step2-title">Secure Login</span></h2>
                <p id="step2-greeting"></p>
            </div>

            <div class="readonly-email" id="display_email"></div>

            <form id="step2Form" method="POST" action="{{ route('admin.login.post') }}">
                @csrf
                <input type="hidden" name="email" id="step2_email">

                <div class="floating-group">
                    <div class="input-wrapper">
                        <input
                            type="password"
                            id="admin_password"
                            name="password"
                            required
                            placeholder=" "
                            autocomplete="current-password"
                        >
                        <label for="admin_password" id="password-label">Password</label>
                        <i class="fas fa-eye eye-icon" id="togglePwd"></i>
                    </div>
                </div>

                <button type="submit" class="btn" id="btnStep2">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
                <button type="button" class="btn btn-secondary" onclick="goBack()"
                    style="background:#e0e0e0; color:#333; margin-top:0.5rem;">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
            </form>
        </div>

    </div>
</div>

<script>
    const errorAlert = document.getElementById('error-alert');
    const pwdInput   = document.getElementById('admin_password');
    const togglePwd  = document.getElementById('togglePwd');

    // Password reveal toggle
    togglePwd.addEventListener('click', function () {
        if (pwdInput.type === 'password') {
            pwdInput.type = 'text';
            this.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            pwdInput.type = 'password';
            this.classList.replace('fa-eye-slash', 'fa-eye');
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
        document.getElementById('admin_email').focus();
    }

    // ── Step 1: Verify Email ──
    document.getElementById('step1Form').addEventListener('submit', function (e) {
        e.preventDefault();
        hideError();

        const email = document.getElementById('admin_email').value.trim();
        if (!email) return;

        const btn = document.getElementById('btnStep1');
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
        btn.disabled = true;

        fetch('{{ route("admin.verifyEmail") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email })
        })
        .then(r => r.json())
        .then(data => {
            btn.innerHTML = orig;
            btn.disabled = false;

            if (data.success) {
                // Move to step 2
                document.getElementById('step1').classList.remove('active');
                document.getElementById('step2').classList.add('active');

                document.getElementById('step2_email').value = email;
                document.getElementById('display_email').textContent = email;

                const greeting  = document.getElementById('step2-greeting');
                const pwdLabel  = document.getElementById('password-label');
                const step2Title = document.getElementById('step2-title');

                if (data.password_changed) {
                    step2Title.textContent = 'Welcome back!';
                    greeting.textContent   = `Enter your password, ${data.name}`;
                    pwdLabel.textContent   = 'Password';
                    pwdInput.placeholder   = ' ';
                    pwdInput.autocomplete  = 'current-password';
                } else {
                    step2Title.textContent = 'Create Password';
                    greeting.textContent   = `Set your password, ${data.name}`;
                    pwdLabel.textContent   = 'New Password';
                    pwdInput.placeholder   = ' ';
                    pwdInput.autocomplete  = 'new-password';
                }

                pwdInput.value = '';
                pwdInput.focus();
            } else {
                showError(data.message || 'Email not found.');
            }
        })
        .catch(() => {
            btn.innerHTML = orig;
            btn.disabled = false;
            showError('An error occurred. Please try again.');
        });
    });

    // ── Step 2: Submit animation ──
    document.getElementById('step2Form').addEventListener('submit', function () {
        const btn = document.getElementById('btnStep2');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
        btn.disabled = true;
    });
</script>
@endsection
