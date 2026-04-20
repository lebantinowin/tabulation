<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CriteriaController;
use App\Http\Controllers\ContestantController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\TabulationController;
use App\Http\Controllers\JudgeController;
use App\Http\Controllers\AssistanceController;
use App\Http\Controllers\AuditLogController;

// Landing Page
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Public Contestant View
Route::get('/contestants', [ContestantController::class, 'publicIndex'])->name('contestants.public');

// Authentication - Judge Login
Route::get('/login', [AuthController::class, 'showJudgeLoginForm'])->name('login');
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
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Event Management
    Route::resource('events', EventController::class);

    // Criteria & Sub-criteria
    Route::resource('criteria', CriteriaController::class);

    // Contestants / Participants
    Route::resource('contestants', ContestantController::class);

    // Judges Management
    Route::resource('judges', JudgeController::class);
    
    // Toggle Judge Active Status
    Route::post('/judges/{judge}/toggle-active', [JudgeController::class, 'toggleActive'])->name('judges.toggleActive');

    // Assign Judges to Event
    Route::get('/events/{event}/assign-judges', [EventController::class, 'assignJudges'])->name('events.assignJudges');
    Route::post('/events/{event}/assign-judges', [EventController::class, 'storeAssignedJudges'])->name('events.storeAssignedJudges');

    // Assistance Requests
    Route::get('/assistance', [AssistanceController::class, 'index'])->name('assistance.index');
    Route::post('/assistance/{id}/confirm', [AssistanceController::class, 'confirm'])->name('assistance.confirm');
    Route::delete('/assistance/{id}', [AssistanceController::class, 'destroy'])->name('assistance.destroy');

    // Tabulation Control
    Route::get('/tabulation/results', [TabulationController::class, 'results'])->name('tabulation.results');
    Route::post('/tabulation/override', [TabulationController::class, 'override'])->name('tabulation.override');
    Route::post('/tabulation/lock', [TabulationController::class, 'lock'])->name('tabulation.lock');
    Route::post('/tabulation/unlock', [TabulationController::class, 'unlock'])->name('tabulation.unlock');
    Route::post('/tabulation/message', [TabulationController::class, 'message'])->name('tabulation.message');

    // Print Results
    Route::get('/tabulation/print', [TabulationController::class, 'print'])->name('tabulation.print');
    Route::get('/tabulation/print-category/{criteriaId}', [TabulationController::class, 'printCategory'])->name('tabulation.print-category');

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('auditLogs.index');
});

// Public Results Routes (accessible to all)
Route::get('/results', [TabulationController::class, 'publicIndex'])->name('results.index');
Route::get('/results/{event}', [TabulationController::class, 'publicResults'])->name('results.show');

// Judge Dashboard
Route::middleware(['auth', 'role:judge'])->group(function () {
    Route::get('/judge/dashboard', function () {
        return view('judge.dashboard');
    })->name('judge.dashboard');

    // Scoring
    Route::resource('scores', ScoreController::class);

    // Score Lock/Unlock
    Route::post('/scores/{score}/lock', [ScoreController::class, 'lock'])->name('scores.lock');
    Route::post('/scores/{score}/unlock', [ScoreController::class, 'unlock'])->name('scores.unlock');

    // Profile Management
    Route::get('/judge/profile', [AuthController::class, 'profile'])->name('judge.profile');
    Route::post('/judge/profile/update', [AuthController::class, 'updateProfile'])->name('judge.profile.update');

    // Assistance Requests
    Route::get('/judge/assistance', [AssistanceController::class, 'myRequests'])->name('judge.assistance.index');
    Route::post('/judge/assistance/request', [AssistanceController::class, 'request'])->name('judge.assistance.request');
});
