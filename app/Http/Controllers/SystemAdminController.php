<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use App\Models\AdminReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemAdminController extends Controller
{
    // ─── Admin Management ────────────────────────────────────────────────────

    public function index()
    {
        $admins = User::where('role', 'admin')->orderBy('created_at', 'desc')->paginate(7);

        $reports = AdminReport::with('admin')
            ->orderBy('created_at', 'desc')
            ->paginate(6, ['*'], 'reports_page');

        return view('admin.system-admins.index', compact('admins', 'reports'));
    }

    public function create()
    {
        return view('admin.system-admins.create');
    }

    /** Superadmin sets name + email only. Admin will set their own password on first login. */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        User::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'password'         => bcrypt(\Illuminate\Support\Str::random(32)), // random unusable password
            'role'             => 'admin',
            'password_changed' => false, // flag: must set own password on first login
        ]);

        AuditLog::log('admin_account_created', 'Created admin account: ' . $request->name);

        return redirect()->route('system-admins.index')->with('success', 'Admin account created. They must set their password on first login.');
    }

    public function show(string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        $reports = AdminReport::where('admin_id', $admin->id)
            ->orderBy('created_at', 'desc')
            ->paginate(7);
            
        return view('admin.system-admins.show', compact('admin', 'reports'));
    }

    public function destroy(string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        $name  = $admin->name;
        $admin->delete();

        AuditLog::log('admin_account_deleted', 'Deleted admin account: ' . $name);

        return redirect()->route('system-admins.index')->with('success', 'Admin account deleted.');
    }

    public function toggleActive(string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        $admin->is_active = !$admin->is_active;
        $admin->save();

        $status = $admin->is_active ? 'activated' : 'deactivated';
        AuditLog::log('admin_account_status_changed', "Admin account {$status}: {$admin->name}");

        return redirect()->route('system-admins.index')->with('success', "Admin {$status} successfully.");
    }

    /** Superadmin marks a report as read */
    public function markReportRead(string $id)
    {
        $report = AdminReport::findOrFail($id);
        $report->is_read = true;
        $report->save();

        return redirect()->route('system-admins.index')->with('success', 'Report marked as read.');
    }

    // ─── Admin Profile Setup (first login) ───────────────────────────────────

    public function showSetup()
    {
        return view('admin.system-admins.setup');
    }

    public function completeSetup(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->where('role', 'admin')->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Admin account not found.'])->withInput();
        }

        if ($user->password_changed) {
            return back()->withErrors(['email' => 'Account already setup. Please login directly.'])->withInput();
        }

        $user->password         = bcrypt($request->password);
        $user->password_changed = true;
        $user->save();

        AuditLog::log('admin_password_set', "Admin account setup completed for: {$user->name} ({$user->email})");

        return redirect()->route('admin.login')->with('success', 'Password set successfully! You can now login.');
    }
}
