<?php

namespace App\Http\Controllers;

use App\Models\AssistanceRequest;
use App\Models\Event;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssistanceController extends Controller
{
    // Judge: Request assistance
    public function request(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'message' => 'required|string|max:1000',
        ]);

        $event = Event::find($request->event_id);
        
        AssistanceRequest::create([
            'judge_id' => Auth::id(),
            'event_id' => $request->event_id,
            'message' => $request->message,
        ]);

        // Audit log for assistance request
        AuditLog::log('assistance_request', 'Requested assistance for event "' . $event->name . '"');

        return back()->with('success', 'Assistance request sent to admin.');
    }

    // Judge: View my assistance requests
    public function myRequests()
    {
        $requests = AssistanceRequest::where('judge_id', Auth::id())
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->get();

        $events = Event::all();

        return view('judge.assistance.index', compact('requests', 'events'));
    }

    // Admin: View all assistance requests
    public function index()
    {
        $requests = AssistanceRequest::with(['judge', 'event'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.assistance.index', compact('requests'));
    }

    // Admin: Confirm assistance request
    public function confirm($id)
    {
        $assistanceRequest = AssistanceRequest::findOrFail($id);
        $event = $assistanceRequest->event;
        $judge = $assistanceRequest->judge;
        
        $assistanceRequest->update([
            'is_confirmed' => true,
            'confirmed_at' => now(),
        ]);

        // Audit log for confirming assistance
        AuditLog::log('assistance_confirmed', 'Confirmed assistance request from judge "' . $judge->name . '" for event "' . $event->name . '"');

        return back()->with('success', 'Assistance request confirmed.');
    }

    // Admin: Delete assistance request
    public function destroy($id)
    {
        $assistanceRequest = AssistanceRequest::findOrFail($id);
        $event = $assistanceRequest->event;
        $judge = $assistanceRequest->judge;
        
        $assistanceRequest->delete();

        // Audit log for deleting assistance request
        AuditLog::log('assistance_deleted', 'Deleted assistance request from judge "' . $judge->name . '" for event "' . $event->name . '"');

        return back()->with('success', 'Assistance request deleted.');
    }
}
