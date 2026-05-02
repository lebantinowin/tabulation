<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use App\Models\Event;
use App\Models\Contestant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class JudgeController extends Controller
{
    public function dashboard()
    {
        $judge = Auth::user();
        $event = $judge->event_id ? Event::find($judge->event_id) : null;
        $contestants = $event ? Contestant::where('event_id', $event->id)->get() : collect();
        
        return view('judge.dashboard', compact('judge', 'event', 'contestants'));
    }

    public function index(Request $request)
    {
        $events = Event::orderBy('date', 'desc')->get();
        $selectedEventId = $request->get('event_id');

        $judgesQuery = User::where('role', 'judge');
        if ($selectedEventId) {
            $judgesQuery->where('event_id', $selectedEventId);
        }
        $judges = $judgesQuery->orderBy('judge_number')->orderBy('name')->get();

        return view('admin.judges.index', compact('judges', 'events', 'selectedEventId'));
    }

    public function create(Request $request)
    {
        $events = Event::orderBy('date', 'desc')->get();
        $defaultEventId = $request->get('event_id');

        // Build a map: event_id => [taken judge numbers]
        $takenNumbers = User::where('role', 'judge')
            ->whereNotNull('judge_number')
            ->whereNotNull('event_id')
            ->get(['event_id', 'judge_number'])
            ->groupBy('event_id')
            ->map(fn($group) => $group->pluck('judge_number')->toArray());

        return view('admin.judges.create', compact('events', 'defaultEventId', 'takenNumbers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'judge_number' => 'nullable|integer|min:1|max:99',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Uniqueness check: judge_number must be unique within the same event
        if ($request->judge_number && $request->event_id) {
            $exists = User::where('role', 'judge')
                ->where('event_id', $request->event_id)
                ->where('judge_number', $request->judge_number)
                ->exists();
            if ($exists) {
                return back()->withInput()
                    ->withErrors(['judge_number' => 'Judge ' . $request->judge_number . ' is already taken for this event. Please choose a different number.']);
            }
        }

        $judge = new User();
        $judge->name         = $request->name;
        $judge->role         = 'judge';
        $judge->is_active    = true;
        $judge->event_id     = $request->event_id;
        $judge->judge_number = $request->judge_number;
        $judge->login_code   = $this->generateUniqueLoginCode();
        $judge->password     = bcrypt(Str::random(16));

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('judges', 'public');
            $judge->image = $path;
        }

        $judge->save();

        AuditLog::log('judge_created', 'Created judge: ' . $judge->name);

        $redirect = $request->event_id
            ? route('judges.index', ['event_id' => $request->event_id])
            : route('judges.index');

        return redirect($redirect)->with('success', 'Judge created successfully.');
    }

    public function show(User $judge)
    {
        return view('admin.judges.show', compact('judge'));
    }

    public function edit(User $judge)
    {
        $events = Event::orderBy('date', 'desc')->get();

        // Build taken numbers map, excluding this judge's own number
        $takenNumbers = User::where('role', 'judge')
            ->where('id', '!=', $judge->id)
            ->whereNotNull('judge_number')
            ->whereNotNull('event_id')
            ->get(['event_id', 'judge_number'])
            ->groupBy('event_id')
            ->map(fn($group) => $group->pluck('judge_number')->toArray());

        return view('admin.judges.edit', compact('judge', 'events', 'takenNumbers'));
    }

    public function update(Request $request, User $judge)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'judge_number' => 'nullable|integer|min:1|max:99',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Uniqueness check: exclude this judge's own record
        if ($request->judge_number && $request->event_id) {
            $exists = User::where('role', 'judge')
                ->where('event_id', $request->event_id)
                ->where('judge_number', $request->judge_number)
                ->where('id', '!=', $judge->id)
                ->exists();
            if ($exists) {
                return back()->withInput()
                    ->withErrors(['judge_number' => 'Judge ' . $request->judge_number . ' is already taken for this event. Please choose a different number.']);
            }
        }

        $judge->name         = $request->name;
        $judge->event_id     = $request->event_id;
        $judge->judge_number = $request->judge_number;

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
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while (User::where('login_code', $code)->exists());

        return $code;
    }
}