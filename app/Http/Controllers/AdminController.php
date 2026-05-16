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
    public function dashboard(Request $request)
    {
        $eventCount = Event::count();
        $contestantCount = Contestant::count();
        $judgeCount = User::where('role', 'judge')->count();
        
        $selectedYear = $request->query('year', date('Y'));
        if (!preg_match('/^\d{4}$/', $selectedYear)) {
            $selectedYear = date('Y');
        }

        $activityData = [];
        $availableYears = [date('Y')];
        $auditLogCount = 0;

        try {
            $auditLogCount = AuditLog::count();
            
            // Get available years for the dropdown
            $oldestLog = AuditLog::orderBy('created_at', 'asc')->first();
            if ($oldestLog) {
                $oldestYear = $oldestLog->created_at->year;
                $currentYear = date('Y');
                $availableYears = range($currentYear, min($oldestYear, $currentYear));
            }
            
            // Get activity for the selected year
            $activities = AuditLog::selectRaw('DATE(created_at) as date, count(*) as count')
                ->whereYear('created_at', $selectedYear)
                ->groupBy('date')
                ->get();
                
            foreach ($activities as $act) {
                $activityData[$act->date] = $act->count;
            }
        } catch (\Exception $e) {
            // Table might not exist yet if migrations aren't fully run
        }

        // Generate heatmap grid (Jan 1 to Dec 31 of selected year)
        $heatmap = [];
        $startDate = \Carbon\Carbon::create($selectedYear, 1, 1)->startOfWeek(\Carbon\Carbon::SUNDAY);
        $endDate = \Carbon\Carbon::create($selectedYear, 12, 31);
        
        $currentDate = $startDate->copy();
        while (true) {
            if ($currentDate > $endDate && $currentDate->dayOfWeek === \Carbon\Carbon::SUNDAY) {
                break;
            }
            
            $dateStr = $currentDate->format('Y-m-d');
            $inYear = $currentDate->year == $selectedYear;
            $count = $inYear ? ($activityData[$dateStr] ?? 0) : 0;
            
            $level = 0;
            if ($count > 0) $level = 1;
            if ($count > 10) $level = 2;
            if ($count > 30) $level = 3;
            if ($count > 60) $level = 4;
            
            $heatmap[] = [
                'date' => $dateStr,
                'count' => $count,
                'level' => $level,
                'day' => $currentDate->dayOfWeek,
                'month' => $currentDate->format('M'),
                'is_first_of_month' => $currentDate->day == 1 && $inYear,
                'in_year' => $inYear
            ];
            
            $currentDate->addDay();
        }

        return view('admin.dashboard', compact(
            'eventCount',
            'contestantCount',
            'judgeCount',
            'auditLogCount',
            'heatmap',
            'selectedYear',
            'availableYears'
        ));
    }

    public function pendingAssistance()
    {
        $count = AssistanceRequest::where('is_confirmed', false)->count();
        return response()->json(['count' => $count]);
    }
}
