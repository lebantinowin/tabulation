@extends('layouts.app')

@section('title', 'My Profile - Judge')

@section('content')
<style>
    .input-reveal-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .input-reveal-wrapper input {
        padding-right: 2.8rem;
    }
    .reveal-eye {
        position: absolute;
        right: 14px;
        color: #888;
        cursor: pointer;
        font-size: 1rem;
        user-select: none;
        transition: color 0.2s;
    }
    .reveal-eye:hover { color: #333; }

    /* Hide native browser password reveal (Edge/Chrome) */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear,
    input[type="password"]::-webkit-credentials-auto-fill-button { display: none !important; }

    .code-dots {
        font-family: monospace;
        letter-spacing: 6px;
        font-size: 1.3rem;
        font-weight: bold;
        color: var(--color-btn);
        background: #f0f0f0;
        border: 1px solid var(--color-border);
        border-radius: 8px;
        padding: 0.45rem 1rem;
        flex: 1;
        cursor: default;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .section-divider {
        border: none;
        border-top: 1px solid var(--color-border);
        margin: 1.5rem 0;
    }
</style>

<div class="page-header">
    <h1>My Profile</h1>
    <a href="{{ route('judge.dashboard') }}" class="btn" title="Back to Dashboard">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error)
            <p style="margin:0;">{{ $error }}</p>
        @endforeach
    </div>
@endif

{{-- ── Profile Info Card ── --}}
<div class="card">
    <div class="profile-info" style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
        <div>
            @if(auth()->user()->image)
                <img src="{{ asset('storage/' . auth()->user()->image) }}" alt="Profile Photo"
                     style="width: 100px; height: 100px; object-fit: cover; border-radius: 12px; border: 3px solid var(--color-border);">
            @else
                <div style="width: 100px; height: 100px; background: var(--color-btn); color: white;
                            display: flex; align-items: center; justify-content: center;
                            font-size: 2.5rem; font-weight: bold; border-radius: 12px; border: 3px solid var(--color-border);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            @endif
        </div>
        <div>
            <h3 style="margin-bottom: 0.5rem; font-size: 1.5rem;">{{ auth()->user()->name }}</h3>
            <p style="margin-bottom: 0.5rem; color: var(--color-muted);">
                <strong>Role:</strong>
                <span class="badge badge-primary" style="background: var(--color-btn); color: white;">
                    {{ ucfirst(auth()->user()->role) }}
                </span>
            </p>

            {{-- Login Code with masked dots + eye reveal --}}
            <p style="margin-bottom: 0; color: var(--color-muted);"><strong>Login Code:</strong></p>
            <div class="input-reveal-wrapper" style="margin-top: 0.4rem; max-width: 240px;">
                <span class="code-dots" id="loginCodeDisplay">••••••</span>
                <i class="fas fa-eye reveal-eye" id="toggleLoginCode" title="Reveal code"
                   onclick="toggleLoginCode()" style="right: 10px;"></i>
            </div>

            {{-- Password masked dots + eye reveal --}}
            <p style="margin-bottom: 0; margin-top: 0.75rem; color: var(--color-muted);"><strong>Password:</strong></p>
            <div class="input-reveal-wrapper" style="margin-top: 0.4rem; max-width: 240px;">
                <span class="code-dots" id="passwordDisplay" style="letter-spacing: 4px; font-size: 1rem;">••••••••</span>
                <i class="fas fa-eye reveal-eye" id="togglePasswordDisplay" title="Reveal password"
                   onclick="togglePasswordDisplay()" style="right: 10px;"></i>
            </div>
            <small style="color: var(--color-muted); display: block; margin-top: 0.3rem;">
                Click the eye icon to reveal your credentials
            </small>
        </div>
    </div>
</div>

{{-- ── Update Profile + Password Card ── --}}
<div class="card" style="margin-top: 1.5rem;">
    <h3>Update Profile</h3>
    <form method="POST" action="{{ route('judge.profile.update') }}">
        @csrf

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
        </div>

        <hr class="section-divider">
        <h4 style="margin-bottom: 1rem; font-size: 1rem; color: var(--color-muted);">
            <i class="fas fa-lock" style="margin-right: 6px;"></i>Change Password
            <small style="font-weight: 400; font-size: 0.85rem; display: block; margin-top: 0.2rem;">
                Leave blank to keep your current password.
            </small>
        </h4>

        <div class="form-group">
            <label for="password">New Password</label>
            <div class="input-reveal-wrapper">
                <input type="password" id="password" name="password"
                       placeholder="Enter new password (min. 8 characters)" autocomplete="new-password">
                <i class="fas fa-eye reveal-eye" onclick="toggleField('password', this)" title="Show/hide"></i>
            </div>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm New Password</label>
            <div class="input-reveal-wrapper">
                <input type="password" id="password_confirmation" name="password_confirmation"
                       placeholder="Re-enter new password" autocomplete="new-password">
                <i class="fas fa-eye reveal-eye" onclick="toggleField('password_confirmation', this)" title="Show/hide"></i>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem; flex-wrap: wrap;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
            <a href="{{ route('judge.dashboard') }}" class="btn">Back to Dashboard</a>
        </div>
    </form>
</div>

<script>
@php $code = auth()->user()->login_code ?? 'N/A'; @endphp
const JUDGE_CODE = '{{ $code }}';
let codeRevealed = false;
let passRevealed = false;

function toggleLoginCode() {
    codeRevealed = !codeRevealed;
    const display = document.getElementById('loginCodeDisplay');
    const icon    = document.getElementById('toggleLoginCode');
    display.textContent = codeRevealed ? JUDGE_CODE : '••••••';
    display.style.letterSpacing = codeRevealed ? '8px' : '6px';
    icon.classList.toggle('fa-eye', !codeRevealed);
    icon.classList.toggle('fa-eye-slash', codeRevealed);
}

function togglePasswordDisplay() {
    passRevealed = !passRevealed;
    const display = document.getElementById('passwordDisplay');
    const icon    = document.getElementById('togglePasswordDisplay');
    // Show a reminder note since we can't show hashed passwords
    display.textContent = passRevealed
        ? '(hidden for security)'
        : '••••••••';
    display.style.fontSize  = passRevealed ? '0.8rem' : '1rem';
    display.style.letterSpacing = passRevealed ? '0' : '4px';
    display.style.color = passRevealed ? 'var(--color-muted)' : 'var(--color-btn)';
    icon.classList.toggle('fa-eye', !passRevealed);
    icon.classList.toggle('fa-eye-slash', passRevealed);
}

function toggleField(fieldId, iconEl) {
    const input = document.getElementById(fieldId);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    iconEl.classList.toggle('fa-eye', isText);
    iconEl.classList.toggle('fa-eye-slash', !isText);
}
</script>
@endsection
