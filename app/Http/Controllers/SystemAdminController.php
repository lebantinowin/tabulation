<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SystemAdminController extends Controller
{
    public function index()
    {
        $admins = User::where('role', 'admin')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.system-admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.system-admins.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $admin = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'admin',
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Created admin account: ' . $admin->name,
        ]);

        return redirect()->route('system-admins.index')->with('success', 'Admin account created successfully.');
    }

    public function show(string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        return view('admin.system-admins.show', compact('admin'));
    }

    public function edit(string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        return view('admin.system-admins.edit', compact('admin'));
    }

    public function update(Request $request, string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $admin->name  = $request->name;
        $admin->email = $request->email;
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }
        $admin->save();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Updated admin account: ' . $admin->name,
        ]);

        return redirect()->route('system-admins.index')->with('success', 'Admin account updated successfully.');
    }

    public function destroy(string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        $name  = $admin->name;
        $admin->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action'  => 'Deleted admin account: ' . $name,
        ]);

        return redirect()->route('system-admins.index')->with('success', 'Admin account deleted.');
    }
}
