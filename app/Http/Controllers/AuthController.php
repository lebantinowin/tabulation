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

    // Handle Judge Login
    public function handleLogin(Request $request)
    {
        $request->validate([
            'login_code' => 'required|string',
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

        Auth::login($user);
        $request->session()->regenerate();
        
        // Audit log
        AuditLog::log('login', 'Judge logged in via code');
        
        // Check if judge has accepted agreement
        if (!$user->agreement_accepted) {
            return redirect()->route('agreement');
        }
        
        return redirect()->route('judge.dashboard');
    }

    // Handle Admin Login
    public function handleAdminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Only allow admin role
            if ($user->role !== 'admin') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Access denied. Admin credentials required.',
                ])->onlyInput('email');
            }
            
            AuditLog::log('login', 'Admin logged in');
            
            // Set flash message for login success
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
        
        if ($user) {
            $action = $user->role === 'admin' ? 'Admin logged out' : 'Judge logged out';
            AuditLog::log('logout', $action);
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
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
        }
        
        $user->save();

        AuditLog::log('profile_updated', 'Judge updated profile');

        return back()->with('success', 'Profile updated successfully!');
    }
}
