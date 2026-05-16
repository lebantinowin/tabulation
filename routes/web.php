<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CriteriaController;
use App\Http\Controllers\ContestantController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\TabulationController;
use App\Http\Controllers\JudgeController;
use App\Http\Controllers\AuditLogController;

// Landing Page
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Public Contestant View
Route::get('/contestants', [ContestantController::class, 'publicIndex'])->name('contestants.public');

// Authentication - Judge Login
Route::get('/login', [AuthController::class, 'showJudgeLoginForm'])->name('login');
Route::post('/login/verify-code', [AuthController::class, 'verifyCode'])->name('login.verifyCode');
Route::post('/login', [AuthController::class, 'handleLogin']);

// Admin Login (separate page)
Route::get('/admin', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/admin', [AuthController::class, 'handleAdminLogin']);

// Logout (shared)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Agreement (Judge only)
Route::middleware(['auth', 'role:judge'])->group(function () {
    Route::get('/agreement', [AuthController::class, 'agreement'])->name('agreement');
    Route::post('/agreement/accept', [AuthController::class, 'acceptAgreement'])->name('agreement.accept');
});

// Admin Dashboard
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Event Management
    Route::post('/events/{event}/archive', [App\Http\Controllers\EventController::class, 'archive'])->name('events.archive');
    Route::post('/events/{event}/unarchive', [App\Http\Controllers\EventController::class, 'unarchive'])->name('events.unarchive');
    Route::post('/events/{event}/reset-scores', [App\Http\Controllers\EventController::class, 'resetScores'])->name('events.resetScores');
    Route::post('/events/{event}/set-performing', [App\Http\Controllers\EventController::class, 'setPerforming'])->name('events.setPerforming');
    Route::resource('events', EventController::class);

    // Criteria routes
    Route::resource('criteria', CriteriaController::class)->parameters(['criteria' => 'criteria']);

    // Trash / Recycle Bin routes
    Route::get('/trash', [App\Http\Controllers\TrashController::class, 'index'])->name('trash.index');
    Route::post('/trash/event/{id}/restore', [App\Http\Controllers\TrashController::class, 'restoreEvent'])->name('trash.restore.event');
    Route::delete('/trash/event/{id}/force', [App\Http\Controllers\TrashController::class, 'forceDeleteEvent'])->name('trash.force-delete.event');
    
    Route::post('/trash/contestant/{id}/restore', [App\Http\Controllers\TrashController::class, 'restoreContestant'])->name('trash.restore.contestant');
    Route::delete('/trash/contestant/{id}/force', [App\Http\Controllers\TrashController::class, 'forceDeleteContestant'])->name('trash.force-delete.contestant');
    
    Route::post('/trash/judge/{id}/restore', [App\Http\Controllers\TrashController::class, 'restoreJudge'])->name('trash.restore.judge');
    Route::delete('/trash/judge/{id}/force', [App\Http\Controllers\TrashController::class, 'forceDeleteJudge'])->name('trash.force-delete.judge');
    
    Route::post('/trash/score/{id}/restore', [App\Http\Controllers\TrashController::class, 'restoreScore'])->name('trash.restore.score');
    Route::delete('/trash/score/{id}/force', [App\Http\Controllers\TrashController::class, 'forceDeleteScore'])->name('trash.force-delete.score');

    // System configurations / Participants
    Route::resource('contestants', ContestantController::class);

    // Judges Management
    Route::resource('judges', JudgeController::class);
    
    Route::get('/judges/{judge}/export-pdf', [JudgeController::class, 'exportPdf'])->name('judges.exportPdf');
    
    // Toggle Judge Active Status
    Route::post('/judges/{judge}/toggle-active', [JudgeController::class, 'toggleActive'])->name('judges.toggleActive');

    // Assign Judges to Event
    Route::get('/events/{event}/assign-judges', [EventController::class, 'assignJudges'])->name('events.assignJudges');
    Route::post('/events/{event}/assign-judges', [EventController::class, 'storeAssignedJudges'])->name('events.storeAssignedJudges');

    // Tabulation Control (override, lock, unlock, message)
    Route::post('/tabulation/override', [TabulationController::class, 'override'])->name('tabulation.override');
    Route::post('/tabulation/lock', [TabulationController::class, 'lock'])->name('tabulation.lock');
    Route::post('/tabulation/unlock', [TabulationController::class, 'unlock'])->name('tabulation.unlock');
    Route::post('/tabulation/message', [TabulationController::class, 'message'])->name('tabulation.message');

    // PDF Export / Print
    Route::get('/tabulation/print', [TabulationController::class, 'print'])->name('tabulation.print');
    Route::get('/tabulation/print-category/{criteriaId}', [TabulationController::class, 'printCategory'])->name('tabulation.print-category');
    Route::get('/tabulation/print-judge/{eventId}/{judgeId}', [TabulationController::class, 'printJudge'])->name('tabulation.print-judge');

    // Documents
    Route::get('/documents', [App\Http\Controllers\DocumentsController::class, 'index'])->name('documents.index');

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('auditLogs.index');
});

// Superadmin Management (superadmin only)
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::resource('system-admins', App\Http\Controllers\SystemAdminController::class)
        ->except(['edit', 'update'])
        ->parameters(['system-admins' => 'id']);
    Route::post('/system-admins/{id}/toggle-active', [App\Http\Controllers\SystemAdminController::class, 'toggleActive'])->name('system-admins.toggleActive');
    Route::post('/system-admins/reports/{id}/read', [App\Http\Controllers\SystemAdminController::class, 'markReportRead'])->name('system-admins.reports.read');
});

// Admin Reports (admin role only)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/reports', [App\Http\Controllers\AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/create', [App\Http\Controllers\AdminReportController::class, 'create'])->name('admin.reports.create');
    Route::post('/admin/reports', [App\Http\Controllers\AdminReportController::class, 'store'])->name('admin.reports.store');
});

// Admin first-login password setup (Unauthenticated)
Route::get('/admin/setup', [App\Http\Controllers\SystemAdminController::class, 'showSetup'])->name('admin.setup');
Route::post('/admin/setup', [App\Http\Controllers\SystemAdminController::class, 'completeSetup'])->name('admin.setup.complete');

// Public Results Routes (accessible to all)
Route::get('/results', [TabulationController::class, 'publicIndex'])->name('results.index');
Route::get('/results/{event}', [TabulationController::class, 'publicResults'])->name('results.show');

// Judge Dashboard
Route::middleware(['auth', 'role:judge'])->group(function () {
    Route::get('/judge/dashboard', [JudgeController::class, 'dashboard'])->name('judge.dashboard');
    Route::get('/judge/current-performing', [JudgeController::class, 'currentPerforming'])->name('judge.currentPerforming');

    // Scoring
    Route::resource('scores', ScoreController::class);

    // Score Lock/Unlock
    Route::post('/scores/{score}/lock', [ScoreController::class, 'lock'])->name('scores.lock');
    Route::post('/scores/{score}/unlock', [ScoreController::class, 'unlock'])->name('scores.unlock');

    // Profile Management
    Route::get('/judge/profile', [AuthController::class, 'profile'])->name('judge.profile');
    Route::post('/judge/profile/update', [AuthController::class, 'updateProfile'])->name('judge.profile.update');
});
