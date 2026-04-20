<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AuditLogController extends Controller
{
    // Display a listing of the audit logs.
    public function index()
    {
        try {
            // Check if the table exists
            if (!Schema::hasTable('audit_logs')) {
                $auditLogs = collect([]);
                return view('admin.audit-logs.index', compact('auditLogs'))->with('error', 'Audit logs table does not exist. Please run migrations.');
            }
            
$auditLogs = AuditLog::with('user')->latest()->paginate(7);
            return view('admin.audit-logs.index', compact('auditLogs'));
        } catch (\Exception $e) {
            $auditLogs = collect([]);
            return view('admin.audit-logs.index', compact('auditLogs'))->with('error', 'Unable to load audit logs: ' . $e->getMessage());
        }
    }
}
