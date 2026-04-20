@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="page-header">
    <h1>Admin Dashboard</h1>
</div>

@if(Session::has('login_success'))
    <div class="alert alert-success" style="animation: fadeIn 0.5s ease;">
        <i class="fas fa-check-circle"></i> Welcome back, {{ Auth::user()->name }}! You have successfully logged in.
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
    <a href="{{ route('events.index') }}" class="card-clickable" style="text-decoration: none;">
        <div class="card">
            <h3><i class="fas fa-calendar-alt"></i> Events</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--color-text);">{{ \App\Models\Event::count() }}</p>
        </div>
    </a>
    
    <a href="{{ route('contestants.index') }}" class="card-clickable" style="text-decoration: none;">
        <div class="card">
            <h3><i class="fas fa-users"></i> Contestants</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--color-text);">{{ \App\Models\Contestant::count() }}</p>
        </div>
    </a>
    
    <a href="{{ route('judges.index') }}" class="card-clickable" style="text-decoration: none;">
        <div class="card">
            <h3><i class="fas fa-user-tie"></i> Judges</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--color-text);">{{ \App\Models\User::where('role', 'judge')->count() }}</p>
        </div>
    </a>
    
    <a href="{{ route('auditLogs.index') }}" class="card-clickable" style="text-decoration: none;">
        <div class="card">
            <h3><i class="fas fa-clipboard-list"></i> Audit Logs</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--color-text);">
                <?php try { ?>
                    {{ \App\Models\AuditLog::count() }}
                <?php } catch (\Exception $e) { ?>
                    0
                <?php } ?>
            </p>
        </div>
    </a>
</div>

<div class="card mt-4">
    <h2>Quick Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="{{ route('events.create') }}" class="btn" title="Create New Event">
            <i class="fas fa-plus"></i> Create Event
        </a>
        <a href="{{ route('contestants.create') }}" class="btn" title="Add New Contestant">
            <i class="fas fa-user-plus"></i> Add Contestant
        </a>
        <a href="{{ route('judges.create') }}" class="btn" title="Add New Judge">
            <i class="fas fa-user-tie"></i> Add Judge
        </a>
        <a href="{{ route('results.index') }}" class="btn" title="View Tabulation Results">
            <i class="fas fa-chart-bar"></i> View Results
        </a>
        <a href="{{ route('auditLogs.index') }}" class="btn" title="View Audit Logs">
            <i class="fas fa-clipboard-list"></i> Audit Logs
        </a>
    </div>
</div>

<style>
.card-clickable .card {
    transition: all 0.2s ease;
    cursor: pointer;
}

.card-clickable .card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: var(--color-btn);
}

.card-clickable .card h3 {
    color: var(--color-text);
}

.card-clickable .card h3 i {
    margin-right: 0.5rem;
    color: var(--color-btn);
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endsection
