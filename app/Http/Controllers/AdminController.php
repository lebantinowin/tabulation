<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Contestant;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\AssistanceRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $eventCount = Event::count();
        $contestantCount = Contestant::count();
        $judgeCount = User::where('role', 'judge')->count();
        $auditLogCount = 0;
        
        try {
            $auditLogCount = AuditLog::count();
        } catch (\Exception $e) {
            // Table might not exist yet if migrations aren't fully run
        }

        return view('admin.dashboard', compact(
            'eventCount',
            'contestantCount',
            'judgeCount',
            'auditLogCount'
        ));
    }

    public function pendingAssistance()
    {
        $count = AssistanceRequest::where('is_confirmed', false)->count();
        return response()->json(['count' => $count]);
    }
}
