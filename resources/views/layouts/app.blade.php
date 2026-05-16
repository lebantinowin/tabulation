<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tabulation System')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --color-main: #F5F5F0;
            --color-text: #000000;
            --color-btn: #040D12;
            --color-btn-hover: #1a2634;
            --color-white: #ffffff;
            --color-muted: #666666;
            --color-border: #cccccc;
            --sidebar-width: 260px;
            --sidebar-collapsed: 70px;
            
            /* Semantic Colors */
            --color-primary: #040D12;
            --color-secondary: #4A5568;
            --color-success: #2F855A;
            --color-warning: #C05621;
            --color-danger: #C53030;
            --color-info: #2B6CB0;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--color-main);
            color: var(--color-text);
            line-height: 1.6;
            font-size: 13px;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.03);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--color-btn);
            color: var(--color-white);
            display: flex;
            flex-direction: column;
            padding: 1.5rem 0;
            box-shadow: 4px 0 15px rgba(0,0,0,0.2);
            transition: width 0.3s ease;
            z-index: 1000;
            overflow-x: hidden;
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }
        
        .sidebar.collapsed .nav-links a span,
        .sidebar.collapsed .user-info span,
        .sidebar.collapsed .user-info strong,
        .sidebar.collapsed .logout-form button span {
            display: none;
        }
        
        .sidebar.collapsed .brand {
            padding: 1rem 0.5rem 1.5rem;
            text-align: center;
        }
        
        .sidebar.collapsed .user-info {
            padding: 1rem 0.5rem;
            text-align: center;
        }
        
        .sidebar.collapsed .logout-form {
            padding: 0.75rem 0.5rem;
        }
        
        .sidebar .brand {
            font-size: 1.3rem;
            font-weight: 600;
            padding: 0.5rem 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar .brand a {
            color: var(--color-white);
            text-decoration: none;
            letter-spacing: 0.5px;
        }
        
        /* Brand text toggle - shows full text when expanded, short when collapsed */
        .sidebar .brand-text-full { display: inline !important; }
        .sidebar .brand-text-short { display: none !important; }
        .sidebar.collapsed .brand-text-full { display: none !important; }
        .sidebar.collapsed .brand-text-short { display: inline !important; }
        
        /* Toggle Button - Attached to sidebar edge */
        .sidebar-toggle-btn {
            position: fixed;
            left: 260px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--color-sidebar, #040D12);
            border: 1px solid #040D12;
            border-left: none;
            color: var(--color-white);
            width: 15px;
            height: 60px;
            border-radius: 0 10px 10px 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 1001;
            padding: 0;
            font-size: 0.9rem;
            box-shadow: 2px 0 5px rgba(0,0,0,0.2);
        }

        .sidebar.collapsed ~ .sidebar-toggle-btn {
            left: 70px;
        }
        
        .sidebar .nav-links {
            flex: 1;
            padding-top: 0.5rem;
            overflow-y: auto;
        }
        
        .sidebar a {
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            padding: 0.85rem 1.5rem;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            gap: 12px;
            white-space: nowrap;
            min-height: 50px;
        }
        
        .sidebar a i {
            width: 22px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255, 255, 255, 0.1);
            color: var(--color-white);
            border-left-color: var(--color-white);
        }
        
        .sidebar .user-info {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.6);
        }
        
        .sidebar .user-info strong {
            color: var(--color-white);
            display: block;
            margin-bottom: 0.25rem;
        }
        
        .sidebar .logout-form {
            padding: 0.75rem 1.5rem 1.5rem;
        }
        
        .sidebar .logout-form button {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.8);
            padding: 0.6rem 1rem;
            cursor: pointer;
            font-size: 0.85rem;
            width: 100%;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }
        
        .sidebar .logout-form button:hover {
            background: rgba(255, 255, 255, 0.2);
            color: var(--color-white);
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        
        .main-content.expanded {
            margin-left: var(--sidebar-collapsed);
        }
        
        /* Full width content (no sidebar) */
        .full-content {
            padding: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1, h2, h3, h4, h5 {
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--color-text);
        }
        
        h1 { font-size: 2rem; }
        h2 { font-size: 1.5rem; }
        h3 { font-size: 1.25rem; }
        
        .card {
            background: var(--color-white);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--color-border);
        }
        
        .card-clickable { text-decoration: none; display: block; }
        .card-clickable .card { transition: all 0.2s ease; cursor: pointer; }
        .card-clickable .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: var(--color-btn);
        }
        .card-clickable .card h3 { color: var(--color-text); }
        .card-clickable .card h3 i { margin-right: 0.5rem; color: var(--color-btn); }
        
        .btn {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            border: none;
            background: var(--color-btn);
            color: var(--color-white);
            text-decoration: none;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-family: 'Poppins', sans-serif;
        }
        
        .btn:hover {
            background: var(--color-btn-hover);
            transform: translateY(-2px);
            color: var(--color-white);
        }
        
        .btn-primary {
            background: var(--color-btn);
            color: var(--color-white);
        }
        
        .btn-primary:hover {
            background: var(--color-btn-hover);
        }
        
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            border-radius: 6px;
        }
        
        .btn-danger {
            background: #8B4513;
            color: var(--color-white);
        }
        
        .btn-danger:hover {
            background: #6B3410;
        }
        
        .btn-secondary {
            background: var(--color-muted);
            color: var(--color-white);
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: var(--color-white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--color-border);
        }
        
        th {
            background: var(--color-btn);
            color: var(--color-white);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:hover td {
            background: rgba(0, 0, 0, 0.03);
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--color-text);
            font-size: 0.9rem;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="date"],
        input[type="file"],
        textarea,
        select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--color-border);
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: var(--color-white);
            color: var(--color-text);
        }
        
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: var(--color-btn);
            background: var(--color-white);
            box-shadow: 0 0 0 3px rgba(4, 13, 18, 0.1);
        }
        
        .alert {
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            background: var(--color-white);
            border-left: 4px solid var(--color-btn);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }
        
        .alert-success {
            border-left-color: #28a745;
            background: rgba(40, 167, 69, 0.1);
        }
        
        .alert-danger {
            border-left-color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
        }
        
        .badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 20px;
            background: var(--color-border);
            color: var(--color-text);
        }
        
        .badge-success {
            background: var(--color-success);
            color: var(--color-white);
        }
        
        .badge-warning {
            background: var(--color-warning);
            color: var(--color-white);
        }
        
        .badge-danger {
            background: var(--color-danger);
            color: var(--color-white);
        }
        
        .badge-secondary {
            background: var(--color-secondary);
            color: var(--color-white);
        }
        
        /* Pagination Styles */
        .pagination {
            display: flex;
            gap: 0.25rem;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .pagination li {
            list-style: none;
        }
        
        .pagination li a,
        .pagination li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
            border-radius: 6px;
            text-decoration: none;
            background: var(--color-white);
            color: var(--color-btn);
            border: 1px solid var(--color-border);
            transition: all 0.2s ease;
            min-width: 36px;
            height: 36px;
        }
        
        .pagination li a:hover {
            background: var(--color-btn);
            color: var(--color-white);
            border-color: var(--color-btn);
        }
        
        .pagination li.active span {
            background: var(--color-btn);
            color: var(--color-white);
            border-color: var(--color-btn);
        }
        
        .pagination li.disabled span {
            color: var(--color-muted);
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .pagination-info {
            font-size: 0.85rem;
            color: var(--color-muted);
            display: flex;
            align-items: center;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mt-3 { margin-top: 1rem; }
        .mb-3 { margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--color-border);
        }
        
        .page-header h1 {
            margin-bottom: 0;
        }
        
        footer {
            text-align: center;
            padding: 2rem;
            color: var(--color-muted);
            font-size: 0.85rem;
        }
        
        footer a {
            color: var(--color-btn);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        footer a:hover {
            color: #D4A574;
        }
        
        /* Image styles */
        .img-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid var(--color-border);
        }
        
        .img-large {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            border: 3px solid var(--color-border);
        }
        
        /* User Avatar */
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: var(--color-btn);
            color: var(--color-white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            border: 2px solid #000;
        }
        
        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #000;
        }
        
        /* Consistent Profile Image Style - Square with black border */
        .profile-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #000;
        }
        
        .profile-image-sm {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #000;
        }
        
        .profile-image-lg {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            border: 3px solid #000;
        }
        
        .profile-image-xl {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            border: 4px solid #000;
        }
        
        /* Icon Button Styles */
        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            color: white;
        }
        
        .btn-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            color: white;
        }
        
        .btn-icon-edit {
            background: var(--color-warning);
        }
        
        .btn-icon-edit:hover {
            background: #9c4221;
        }
        
        .btn-icon-delete {
            background: var(--color-danger);
        }
        
        .btn-icon-delete:hover {
            background: #9b2c2c;
        }
        
        .btn-icon-view {
            background: var(--color-success);
        }
        
        .btn-icon-view:hover {
            background: #22543d;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: var(--color-white);
            border-radius: 12px;
            padding: 2rem;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            position: relative;
        }
        
        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--color-muted);
            background: none;
            border: none;
            transition: color 0.2s;
        }
        
        .modal-close:hover {
            color: var(--color-danger);
        }

        /* Utility Classes */
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .gap-1 { gap: 0.25rem; }
        .gap-2 { gap: 0.5rem; }
        .gap-3 { gap: 1rem; }
        .gap-4 { gap: 1.5rem; }
        .mt-1 { margin-top: 0.25rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-3 { margin-top: 1rem; }
        .mt-4 { margin-top: 1.5rem; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        .p-3 { padding: 1rem; }
        .p-4 { padding: 1.5rem; }
        .grid { display: grid; }
        .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-cols-4 { grid-template-columns: repeat(4, 1fr); }
        .text-center { text-align: center; }
        .w-full { width: 100%; }

        /* Tabs Styles */
        .nav-tabs {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0 0 1.5rem 0;
            border-bottom: 1px solid var(--color-border);
            gap: 1rem;
        }
        
        .tab-btn {
            background: none;
            border: none;
            padding: 0.5rem 1rem;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            color: var(--color-muted);
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }
        
        .tab-btn.active {
            color: var(--color-btn);
            border-bottom-color: var(--color-btn);
        }
        
        .tab-btn:hover {
            color: var(--color-btn);
        }

        
        /* Banner styles */
        .event-banner {
            margin-bottom: 1.5rem;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .event-banner img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
        
        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                width: var(--sidebar-collapsed);
            }
            
            .sidebar .brand span,
            .sidebar .nav-links a span,
            .sidebar .user-info span,
            .sidebar .user-info strong,
            .sidebar .logout-form button span {
                display: none;
            }
            
            .main-content {
                margin-left: var(--sidebar-collapsed);
            }
            
            .sidebar-toggle-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    @auth
    <!-- Sidebar Navigation -->
    <nav class="sidebar" id="sidebar">
        <div class="brand">
            <span class="brand-text-full">Tabulation System</span>
            <span class="brand-text-short">TS</span>
        </div>
        
        <div class="nav-links">
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard"><i class="fas fa-tachometer-alt" style="width: 20px; text-align: center;"></i> <span>Dashboard</span></a>
                <a href="{{ route('events.index') }}" class="{{ request()->routeIs('events.*') ? 'active' : '' }}" title="Events"><i class="fas fa-calendar-alt" style="width: 20px; text-align: center;"></i> <span>Events</span></a>
                <a href="{{ route('contestants.index') }}" class="{{ request()->routeIs('contestants.*') ? 'active' : '' }}" title="Contestants"><i class="fas fa-users" style="width: 20px; text-align: center;"></i> <span>Contestants</span></a>
                <a href="{{ route('judges.index') }}" class="{{ request()->routeIs('judges.*') ? 'active' : '' }}" title="Judges"><i class="fas fa-user-tie" style="width: 20px; text-align: center;"></i> <span>Judges</span></a>
                <a href="{{ route('results.index') }}" class="{{ request()->routeIs('results.*') ? 'active' : '' }}" title="Results"><i class="fas fa-trophy" style="width: 20px; text-align: center;"></i> <span>Results</span></a>
                <a href="{{ route('documents.index') }}" class="{{ request()->routeIs('documents.*') ? 'active' : '' }}" title="Documents"><i class="fas fa-folder-open" style="width: 20px; text-align: center;"></i> <span>Documents</span></a>
                <a href="{{ route('auditLogs.index') }}" class="{{ request()->routeIs('auditLogs.*') ? 'active' : '' }}" title="Audit Logs"><i class="fas fa-clipboard-list" style="width: 20px; text-align: center;"></i> <span>Audit Logs</span></a>
                <a href="{{ route('trash.index') }}" class="{{ request()->routeIs('trash.*') ? 'active' : '' }}" title="Recycle Bin"><i class="fas fa-trash-restore" style="width: 20px; text-align: center;"></i> <span>Recycle Bin</span></a>
            @elseif(auth()->user()->isJudge())
                <a href="{{ route('judge.dashboard') }}" class="{{ request()->routeIs('judge.dashboard') ? 'active' : '' }}" title="Dashboard"><i class="fas fa-tachometer-alt" style="width: 20px; text-align: center;"></i> <span>Dashboard</span></a>
                <a href="{{ route('scores.index') }}" class="{{ request()->routeIs('scores.*') ? 'active' : '' }}" title="Scores"><i class="fas fa-star" style="width: 20px; text-align: center;"></i> <span>Scores</span></a>
                @if(auth()->user()->event_id)
                    <a href="{{ route('results.show', auth()->user()->event_id) }}" class="{{ request()->routeIs('results.*') ? 'active' : '' }}" title="Results"><i class="fas fa-trophy" style="width: 20px; text-align: center;"></i> <span>Results</span></a>
                @endif
                <a href="{{ route('judge.profile') }}" class="{{ request()->routeIs('judge.profile') ? 'active' : '' }}" title="Profile"><i class="fas fa-user" style="width: 20px; text-align: center;"></i> <span>Profile</span></a>
            @endif
        </div>
        
        <div class="user-info">
            <strong>{{ auth()->user()->name }}</strong>
            <span>{{ auth()->user()->role }}</span>
        </div>
        
        <div class="logout-form">
            <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                @csrf
                <button type="button" title="Logout" onclick="startLogoutCountdown()">
                    <i class="fas fa-sign-out-alt" style="width: 20px; text-align: center;"></i> <span>Logout</span>
                </button>
            </form>
        </div>
    </nav>

    <!-- Toggle Button - Outside sidebar on the right edge -->
    <button class="sidebar-toggle-btn" id="sidebarToggleBtn" onclick="toggleSidebar()" title="Toggle Sidebar">
        <i class="fas fa-chevron-left" id="toggle-icon"></i>
    </button>
    @endauth
    
    <div class="@auth main-content @else full-content @endauth" id="main-content">
        <div class="container">
            @yield('content')
        </div>
        
        @auth
        <footer>
            <p style="margin: 1rem 0; color: #666;">Powered By: <a href="https://www.facebook.com/profile.php?id=61585146655957" target="_blank">ECCENTRI, Inc.</a></p>
        </footer>
        @endauth
    </div>
    
    <script>
        // Auto-hide success alerts after 5 seconds
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll(".alert-success");
                alerts.forEach(function(alert) {
                    alert.style.transition = "opacity 0.5s ease";
                    alert.style.opacity = "0";
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                });
            }, 5000);
        });
        
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleBtn = document.querySelector('.sidebar-toggle-btn');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // Toggle icon direction
            const toggleIcon = document.getElementById('toggle-icon');
            if (sidebar.classList.contains('collapsed')) {
                toggleIcon.classList.remove('fa-chevron-left');
                toggleIcon.classList.add('fa-chevron-right');
            } else {
                toggleIcon.classList.remove('fa-chevron-right');
                toggleIcon.classList.add('fa-chevron-left');
            }
            
            localStorage.setItem('sidebar-collapsed', sidebar.classList.contains('collapsed'));
        }
        
        // Check for stored sidebar state - default is expanded
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleBtn = document.querySelector('.sidebar-toggle-btn');
            
            // Default is expanded - only collapse if explicitly saved
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                const toggleIcon = document.getElementById('toggle-icon');
                if(toggleIcon) {
                    toggleIcon.classList.remove('fa-chevron-left');
                    toggleIcon.classList.add('fa-chevron-right');
                }
            }
        });

    </script>

    <!-- ─── Global Custom Confirm Modal ─── -->
    <div id="confirmModal" class="modal" style="z-index: 99999;">
        <div class="modal-content" style="max-width: 420px; text-align: center; padding: 2.5rem 2rem;">
            <div id="confirmIcon" style="font-size: 3rem; margin-bottom: 1rem;">
                <i class="fas fa-exclamation-triangle" style="color: var(--color-warning);"></i>
            </div>
            <h3 id="confirmTitle" style="margin-bottom: 0.5rem; font-size: 1.2rem;"></h3>
            <p id="confirmMessage" style="color: var(--color-muted); margin-bottom: 2rem; font-size: 0.95rem; line-height: 1.6;"></p>
            <div class="flex gap-2 justify-center">
                <button id="confirmCancelBtn" class="btn" style="background: var(--color-muted); min-width: 100px;" onclick="closeConfirmModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button id="confirmOkBtn" class="btn" style="background: var(--color-danger); min-width: 100px;" onclick="confirmProceed()">
                    <i class="fas fa-check"></i> Confirm
                </button>
            </div>
        </div>
    </div>

    <script>
        let _confirmCallback = null;

        function confirmAction(message, callback, options = {}) {
            const title = options.title || 'Are you sure?';
            const dangerLevel = options.danger || 'high'; // 'high' = red, 'medium' = warning

            document.getElementById('confirmTitle').innerText = title;
            document.getElementById('confirmMessage').innerText = message;

            const okBtn = document.getElementById('confirmOkBtn');
            if (dangerLevel === 'high') {
                okBtn.style.background = 'var(--color-danger)';
                document.getElementById('confirmIcon').innerHTML = '<i class="fas fa-exclamation-triangle" style="color: var(--color-warning);"></i>';
            } else {
                okBtn.style.background = 'var(--color-warning)';
                document.getElementById('confirmIcon').innerHTML = '<i class="fas fa-question-circle" style="color: var(--color-info);"></i>';
            }

            _confirmCallback = callback;
            document.getElementById('confirmModal').classList.add('active');
            document.getElementById('confirmOkBtn').focus();
        }

        function confirmProceed() {
            const callback = _confirmCallback;
            closeConfirmModal();
            if (typeof callback === 'function') {
                callback();
            }
        }

        let _logoutTimer = null;

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.remove('active');
            _confirmCallback = null;
            
            // Clear logout timer if it exists
            if (_logoutTimer) {
                clearInterval(_logoutTimer);
                _logoutTimer = null;
            }
            
            // Restore default button states in case they were modified
            const okBtn = document.getElementById('confirmOkBtn');
            const cancelBtn = document.getElementById('confirmCancelBtn');
            if (okBtn) okBtn.style.display = 'inline-flex';
            if (cancelBtn) {
                cancelBtn.style.display = 'inline-flex';
                cancelBtn.style.alignItems = 'center';
                cancelBtn.style.justifyContent = 'center';
                cancelBtn.innerHTML = '<i class="fas fa-times" style="margin-right: 6px;"></i> Cancel';
            }
        }

        // Keyboard support: Enter = confirm, Escape = cancel
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('confirmModal');
            if (!modal.classList.contains('active')) return;
            if (e.key === 'Escape') closeConfirmModal();
            if (e.key === 'Enter') confirmProceed();
        });

        // Helper: attach to a form's delete/dangerous button
        function confirmForm(formEl, message, options = {}) {
            confirmAction(message, function() { formEl.submit(); }, options);
        }

        function startLogoutCountdown() {
            const titleEl = document.getElementById('confirmTitle');
            const messageEl = document.getElementById('confirmMessage');
            const okBtn = document.getElementById('confirmOkBtn');
            const cancelBtn = document.getElementById('confirmCancelBtn');
            const iconEl = document.getElementById('confirmIcon');

            titleEl.innerText = 'Logging Out';
            iconEl.innerHTML = '<i class="fas fa-sign-out-alt" style="color: var(--color-warning);"></i>';
            
            // Hide Confirm button, only show Cancel
            okBtn.style.display = 'none';
            cancelBtn.style.display = 'inline-flex';
            cancelBtn.style.alignItems = 'center';
            cancelBtn.style.justifyContent = 'center';
            cancelBtn.innerHTML = '<i class="fas fa-times" style="margin-right: 6px;"></i> Cancel Logout';
            
            let seconds = 5;
            messageEl.innerHTML = `You will be automatically logged out in <br><strong style="font-size: 3rem; color: var(--color-danger); display: block; margin: 1rem 0;">${seconds}</strong>`;

            document.getElementById('confirmModal').classList.add('active');

            // Clear any existing timer just in case
            if (_logoutTimer) clearInterval(_logoutTimer);

            _logoutTimer = setInterval(() => {
                seconds--;
                if (seconds > 0) {
                    messageEl.innerHTML = `You will be automatically logged out in <br><strong style="font-size: 3rem; color: var(--color-danger); display: block; margin: 1rem 0;">${seconds}</strong>`;
                } else {
                    clearInterval(_logoutTimer);
                    _logoutTimer = null;
                    
                    cancelBtn.style.display = 'none';
                    messageEl.innerHTML = '<div style="margin: 2rem 0;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem;"></i><p style="margin-top: 1rem;">Logging out securely...</p></div>';
                    
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('logout') }}";
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = "{{ csrf_token() }}";
                    
                    form.appendChild(csrfToken);
                    document.body.appendChild(form);
                    form.submit();
                }
            }, 1000);
        }
    </script>
</body>
</html>
