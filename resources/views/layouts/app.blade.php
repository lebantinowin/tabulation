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
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--color-main);
            color: var(--color-text);
            line-height: 1.6;
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
        
        .sidebar.collapsed .nav-links a {
            padding: 0.85rem;
            justify-content: center;
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
        
        /* Toggle Button - Outside sidebar on the right edge */
        .sidebar-toggle-btn {
            position: fixed;
            left: 260px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--color-btn);
            border: 2px solid var(--color-white);
            color: var(--color-white);
            width: 20px;
            height: 40px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            z-index: 9999;
            padding: 0;
            font-size: 0.7rem;
        }
        
        .sidebar-toggle-btn:hover {
            background: var(--color-btn-hover);
            transform: translateY(-50%) scale(1.05);
            left: 258px;
        }
        
        .sidebar.collapsed + .sidebar-toggle-btn,
        .sidebar.collapsed ~ .sidebar-toggle-btn {
            left: 70px;
        }
        
        .sidebar.collapsed ~ .sidebar-toggle-btn:hover {
            left: 68px;
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
            background: #28a745;
            color: var(--color-white);
        }
        
        .badge-warning {
            background: #D4A574;
            color: var(--color-text);
        }
        
        .badge-danger {
            background: #dc3545;
            color: var(--color-white);
        }
        
        .badge-secondary {
            background: #6c757d;
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
            background: #D4A574;
        }
        
        .btn-icon-edit:hover {
            background: #b8956a;
        }
        
        .btn-icon-delete {
            background: #8B4513;
        }
        
        .btn-icon-delete:hover {
            background: #6B3410;
        }
        
        .btn-icon-view {
            background: #697565;
        }
        
        .btn-icon-view:hover {
            background: #3C3D37;
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
                <a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
                <a href="{{ route('events.index') }}"><i class="fas fa-calendar-alt"></i> <span>Events</span></a>
                <a href="{{ route('contestants.index') }}"><i class="fas fa-users"></i> <span>Contestants</span></a>
                <a href="{{ route('judges.index') }}"><i class="fas fa-user-tie"></i> <span>Judges</span></a>
                <a href="{{ route('results.index') }}"><i class="fas fa-trophy"></i> <span>Results</span></a>
                <a href="{{ route('auditLogs.index') }}"><i class="fas fa-clipboard-list"></i> <span>Audit Logs</span></a>
            @elseif(auth()->user()->isJudge())
                <a href="{{ route('judge.dashboard') }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a>
                <a href="{{ route('scores.index') }}"><i class="fas fa-star"></i> <span>Scores</span></a>
                <a href="{{ route('judge.profile') }}"><i class="fas fa-user"></i> <span>Profile</span></a>
            @endif
        </div>
        
        <div class="user-info">
            <strong>{{ auth()->user()->name }}</strong>
            <span>{{ auth()->user()->role }}</span>
        </div>
        
        <div class="logout-form">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></button>
            </form>
        </div>
    </nav>
    
    <!-- Toggle Button - Outside sidebar on the right edge -->
    <button class="sidebar-toggle-btn" onclick="toggleSidebar()" title="Toggle Sidebar">
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
            
            // Move toggle button with sidebar
            if (sidebar.classList.contains('collapsed')) {
                toggleBtn.style.left = '70px';
            } else {
                toggleBtn.style.left = '260px';
            }
            
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
                toggleBtn.style.left = '70px';
                const toggleIcon = document.getElementById('toggle-icon');
                toggleIcon.classList.remove('fa-chevron-left');
                toggleIcon.classList.add('fa-chevron-right');
            }
        });
    </script>
</body>
</html>
