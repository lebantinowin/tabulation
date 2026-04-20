<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JudgeController extends Controller
{
    public function index()
    {
        $judges = User::where('role', 'judge')->get();
        return view('admin.judges.index', compact('judges'));
    }

    public function create()
    {
        return view('admin.judges.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $judge = new User();
        $judge->name       = $request->name;
        $judge->role       = 'judge';
        $judge->is_active  = true;
        $judge->event_id   = $request->event_id;
        $judge->login_code = $this->generateUniqueLoginCode();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('judges', 'public');
            $judge->image = $path;
        }

        $judge->save();

        AuditLog::log('judge_created', 'Created judge: ' . $judge->name);

        return redirect()->route('judges.index')->with('success', 'Judge created successfully.');
    }

    public function show(User $judge)
    {
        return view('admin.judges.show', compact('judge'));
    }

    public function edit(User $judge)
    {
        return view('admin.judges.edit', compact('judge'));
    }

    public function update(Request $request, User $judge)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $judge->name     = $request->name;
        $judge->event_id = $request->event_id;

        if ($request->hasFile('image')) {
            if ($judge->image) {
                Storage::disk('public')->delete($judge->image);
            }
            $path = $request->file('image')->store('judges', 'public');
            $judge->image = $path;
        }

        if ($request->has('regenerate_code')) {
            $judge->login_code = $this->generateUniqueLoginCode();
        }

        $judge->save();

        AuditLog::log('judge_updated', 'Updated judge: ' . $judge->name);

        return redirect()->route('judges.index')->with('success', 'Judge updated successfully.');
    }

    public function destroy(User $judge)
    {
        if ($judge->image) {
            Storage::disk('public')->delete($judge->image);
        }

        $judgeName = $judge->name;
        $judge->delete();

        AuditLog::log('judge_deleted', 'Deleted judge: ' . $judgeName);

        return redirect()->route('judges.index')->with('success', 'Judge deleted successfully.');
    }

    public function toggleActive(Request $request, User $judge)
    {
        $judge->is_active = !$judge->is_active;
        $judge->save();

        $status = $judge->is_active ? 'activated' : 'deactivated';
        AuditLog::log('judge_toggle_active', "Judge {$judge->name} {$status}");

        $message = $judge->is_active
            ? 'Judge activated successfully. They can now log in.'
            : 'Judge deactivated successfully. They can no longer log in.';

        return redirect()->back()->with('success', $message);
    }

    private function generateUniqueLoginCode()
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $length = 6;

        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[rand(0, strlen($characters) - 1)];
            }
        } while (User::where('login_code', $code)->exists());

        return $code;
    }
}