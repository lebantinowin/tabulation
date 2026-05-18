<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show Judge Login Form (main /login page)
    public function showJudgeLoginForm()
    {
        return view('auth.judge-login');
    }

    // Show Admin Login Form (/admin page)
    public function showAdminLoginForm()
    {
        return view('auth.admin-login');
    }

    // Show Superadmin Login Form (/superadmin page)
    public function showSuperadminLoginForm()
    {
        return view('auth.superadmin-login');
    }

    // Verify Admin Email (AJAX — step 1 of admin login)
    public function verifyAdminEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)
                    ->where('role', 'admin')
                    ->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No admin account found with this email.']);
        }

        if (!$user->is_active) {
            return response()->json(['success' => false, 'message' => 'This account has been deactivated. Please contact the superadmin.']);
        }

        return response()->json([
            'success'          => true,
            'name'             => $user->name,
            'password_changed' => (bool) $user->password_changed,
        ]);
    }

    // Verify Judge Code (AJAX step 1)
    public function verifyCode(Request $request)
    {
        $request->validate([
            'login_code' => 'required|string',
        ]);

        $user = User::where('login_code', strtoupper($request->login_code))
                    ->where('role', 'judge')
                    ->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Invalid login code.']);
        }

        if (!$user->is_active) {
            return response()->json(['success' => false, 'message' => 'Your account has been deactivated. Please contact the admin.']);
        }

        return response()->json([
            'success' => true,
            'name' => $user->name,
            'password_changed' => (bool) $user->password_changed
        ]);
    }

    // Handle Judge Login
    public function handleLogin(Request $request)
    {
        $request->validate([
            'login_code' => 'required|string',
            'password'   => 'required|string',
        ]);

        $user = User::where('login_code', strtoupper($request->login_code))
                    ->where('role', 'judge')
                    ->first();

        if (!$user) {
            return back()->withErrors([
                'login_code' => 'Invalid login code.',
            ])->onlyInput('login_code');
        }

        // Check if judge account is active
        if (!$user->is_active) {
            return back()->withErrors([
                'login_code' => 'Your account has been deactivated. Please contact the admin to activate your account.',
            ])->onlyInput('login_code');
        }

        // Handle password setting or checking
        if (!$user->password_changed) {
            // First time login - set the password
            $user->password = Hash::make($request->password);
            $user->password_changed = true;
            $user->save();
        } else {
            // Subsequent logins - check password
            if (!Hash::check($request->password, $user->password)) {
                return back()->withErrors([
                    'password' => 'Incorrect password.',
                ])->onlyInput('login_code');
            }
        }

        Auth::login($user);
        $request->session()->regenerate();
        
        // Audit log
        AuditLog::log('login', 'Judge logged in via code');
        
        // Flash welcome popup on dashboard
        $request->session()->flash('login_success', true);
        
        // If judge hasn't accepted agreement, flag it
        if (!$user->agreement_accepted) {
            $request->session()->flash('needs_agreement', true);
        }

        return redirect()->route('judge.dashboard');
    }

    // Handle Admin Login (admin role ONLY — superadmins must use /superadmin)
    public function handleAdminLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
                    ->where('role', 'admin')
                    ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No admin account found with this email.'])->onlyInput('email');
        }

        if (!$user->is_active) {
            return back()->withErrors(['email' => 'This account has been deactivated.'])->onlyInput('email');
        }

        // First-time login: set password inline (no separate setup page)
        if (!$user->password_changed) {
            $user->password         = Hash::make($request->password);
            $user->password_changed = true;
            $user->save();

            Auth::login($user);
            $request->session()->regenerate();

            AuditLog::log('admin_password_set', "Admin set password on first login: {$user->name} ({$user->email})");
            AuditLog::log('login', 'Admin logged in via /admin portal (first login)');

            $request->session()->flash('login_success', true);
            return redirect()->route('admin.dashboard');
        }

        // Normal login: verify password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.'])->onlyInput('email');
        }

        Auth::login($user);
        $request->session()->regenerate();

        AuditLog::log('login', 'Admin logged in via /admin portal');

        $request->session()->flash('login_success', true);
        return redirect()->route('admin.dashboard');
    }

    // Handle Superadmin Login (/superadmin portal)
    public function handleSuperadminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Only allow superadmin role
            if ($user->role !== 'superadmin') {
                Auth::logout();
                if ($user->role === 'admin') {
                    return back()->withErrors([
                        'email' => 'Admin accounts must login at /admin.',
                    ])->onlyInput('email');
                }
                return back()->withErrors([
                    'email' => 'Access denied. Superadmin credentials required.',
                ])->onlyInput('email');
            }

            AuditLog::log('login', 'Superadmin logged in via /superadmin portal');

            $request->session()->flash('login_success', true);

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // Logout
    public function logout(Request $request)
    {
        $user = Auth::user();
        $role = $user?->role;

        if ($user) {
            AuditLog::log('logout', ucfirst($role ?? 'User') . ' logged out');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect each role back to their own login portal
        if ($role === 'superadmin') {
            return redirect()->route('superadmin.login');
        } elseif ($role === 'admin') {
            return redirect()->route('admin.login');
        }

        return redirect()->route('login');
    }

    // Agreement Page (Judge only)
    public function agreement()
    {
        return view('auth.agreement');
    }

    // Accept Agreement
    public function acceptAgreement(Request $request)
    {
        $user = Auth::user();
        $user->agreement_accepted = true;
        $user->save();

        AuditLog::log('agreement_accepted', 'Judge accepted agreement');

        return redirect()->route('judge.dashboard');
    }

    // Judge Profile
    public function profile()
    {
        return view('judge.profile');
    }

    // Update Profile
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|confirmed|min:8',
        ]);

        $user->name = $request->name;
        
        if ($request->password) {
            $user->password = Hash::make($request->password);
            AuditLog::log('password_changed', 'Judge changed their password');
        }
        
        $user->save();

        AuditLog::log('profile_updated', 'Judge updated profile');

        return back()->with('success', 'Profile updated successfully!');
    }
}
