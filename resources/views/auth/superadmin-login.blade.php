@extends('layouts.app')

@section('title', 'Superadmin Login')

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
        font-size: 0.8rem;
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

    /* Password input with eye icon */
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

    .login-card .btn {
        width: 100%;
        padding: 0.85rem;
        font-size: 1.05rem;
        font-weight: 600;
        margin-top: 1rem;
        border-radius: 8px;
    }
</style>

<div class="login-page">
    <div class="login-card">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <img src="{{ asset('ts_logo.svg') }}" alt="Logo" style="height: 60px; width: 60px; object-fit: contain; border-radius: 8px;">
        </div>
        <div class="login-header">
            <h2><i class="fas fa-crown"></i> Superadmin Login</h2>
            <p>Restricted to authorized superadmin accounts only</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p style="margin:0;">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('superadmin.login.post') }}" id="sa-form">
            @csrf

            <div class="floating-group">
                <input
                    type="email"
                    id="sa_email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder=" "
                >
                <label for="sa_email">Email Address</label>
            </div>

            <div class="floating-group">
                <div class="input-wrapper">
                    <input
                        type="password"
                        id="sa_password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder=" "
                    >
                    <label for="sa_password">Password</label>
                    <i class="fas fa-eye eye-icon" id="saPwdToggle"></i>
                </div>
            </div>

            <button type="submit" class="btn" id="sa-submit-btn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
    </div>
</div>

<script>
    // Password toggle
    const saPwdInput  = document.getElementById('sa_password');
    const saPwdToggle = document.getElementById('saPwdToggle');

    saPwdToggle.addEventListener('click', function () {
        if (saPwdInput.type === 'password') {
            saPwdInput.type = 'text';
            this.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            saPwdInput.type = 'password';
            this.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });

    // Submit animation
    document.getElementById('sa-form').addEventListener('submit', function (e) {
        if (!this.checkValidity()) return;

        e.preventDefault();
        const btn = document.getElementById('sa-submit-btn');
        btn.disabled = true;

        const steps = ['Verifying...', 'Authenticating...', 'Signing in...'];
        let idx = 0;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${steps[idx]}`;

        const iv = setInterval(() => {
            idx++;
            if (idx < steps.length) {
                btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${steps[idx]}`;
            } else {
                clearInterval(iv);
                this.submit();
            }
        }, 120);
    });
</script>
@endsection
