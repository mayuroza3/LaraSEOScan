<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>@yield('title', 'LaraSEOScan')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- ECharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.6.0/dist/echarts.min.js"></script>

    <!-- Custom Dashboard JS -->
    <script src="{{ asset('js/seo-dashboard.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Ambient Background Radial Glows */
        body {
            position: relative;
            overflow-x: hidden;
            background: #0f172a; /* Premium Dark Mode Background */
            color: #e2e8f0;
        }

        body::before {
            content: "";
            position: absolute;
            width: 600px;
            height: 600px;
            top: -200px;
            left: -200px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, rgba(0,0,0,0) 70%);
            z-index: -1;
            pointer-events: none;
        }

        body::after {
            content: "";
            position: absolute;
            width: 600px;
            height: 600px;
            bottom: -200px;
            right: -200px;
            background: radial-gradient(circle, rgba(124, 58, 237, 0.12) 0%, rgba(0,0,0,0) 70%);
            z-index: -1;
            pointer-events: none;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            transition: all 0.3s ease;
        }

        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Nav Link Active Glow */
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.8rem 1.25rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 12px;
            margin: 0.25rem 1rem;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .sidebar-link:hover, .sidebar-link.active {
            color: #fff;
            background: rgba(99, 102, 241, 0.15);
            box-shadow: inset 0 0 12px rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        .sidebar-link i {
            font-size: 1.2rem;
            margin-right: 0.75rem;
        }

        /* Header glassmorphism */
        .top-navbar {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        /* Custom Cards Override for dark theme */
        .card {
            background: rgba(30, 41, 59, 0.4) !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
            backdrop-filter: blur(12px) !important;
            border-radius: 20px !important;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2) !important;
            color: #f1f5f9 !important;
        }

        .card:hover {
            border-color: rgba(99, 102, 241, 0.2) !important;
        }

        .table th {
            color: #94a3b8 !important;
        }

        .table tbody tr {
            background: rgba(30, 41, 59, 0.3) !important;
            border-color: rgba(255, 255, 255, 0.05) !important;
        }

        .table tbody tr:hover {
            background: rgba(99, 102, 241, 0.1) !important;
            border-color: rgba(99, 102, 241, 0.3) !important;
        }

        .text-dark {
            color: #f8fafc !important;
        }

        .text-muted {
            color: #94a3b8 !important;
        }

        .bg-white {
            background-color: rgba(15, 23, 42, 0.8) !important;
        }

        .bg-light {
            background-color: rgba(30, 41, 59, 0.5) !important;
        }

        .border-top, .border-bottom, .border {
            border-color: rgba(255, 255, 255, 0.05) !important;
        }

        /* Mobile Sidebar adjustments */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Left Premium Sidebar -->
    <div class="sidebar d-flex flex-column" id="sidebarMenu">
        <div class="p-4 border-bottom border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
            <a class="d-flex align-items-center text-decoration-none text-white fw-bold fs-4" href="{{ route('scan.history') }}">
                <i class="bi bi-shield-shaded text-primary me-2 fs-3"></i> LaraSEOScan
            </a>
            <button class="btn btn-link text-white d-lg-none p-0" onclick="toggleSidebar()"><i class="bi bi-x-lg"></i></button>
        </div>
        
        <div class="py-4 flex-grow-1">
            <a href="{{ route('scan.history') }}" class="sidebar-link {{ request()->routeIs('scan.history') || request()->routeIs('scan.results') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a href="{{ route('scan.create') }}" class="sidebar-link {{ request()->routeIs('scan.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle-fill"></i> New Scan
            </a>
            <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <i class="bi bi-person-fill-gear"></i> Settings
            </a>
        </div>

        <div class="p-4 border-top border-secondary border-opacity-25">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link w-100 border-0 bg-transparent text-start ps-0">
                    <i class="bi bi-box-arrow-right text-danger"></i> Log Out
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Floating Glass Top navbar -->
        <div class="top-navbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <button class="btn btn-link text-white d-lg-none me-3 p-0" onclick="toggleSidebar()"><i class="bi bi-list fs-3"></i></button>
                <h5 class="mb-0 fw-semibold text-white">SEO Audit Control</h5>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-link text-white text-decoration-none dropdown-toggle d-flex align-items-center p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-weight: 600;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                    <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow border-0 mt-2">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Profile Settings</a></li>
                    <li><hr class="dropdown-divider bg-secondary"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Log Out</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Inner Content Grid -->
        <div class="flex-grow-1 container-fluid px-4 py-4">
            @yield('content')
        </div>

        <!-- Premium Footer -->
        <footer class="text-center py-4 text-muted border-top bg-transparent">
            <div class="container">
                <small>&copy; {{ date('Y') }} LaraSEOScan. All rights reserved.</small>
                <div class="mt-2 small">
                    <a href="{{ route('legal.privacy') }}" class="text-decoration-none text-muted mx-2">Privacy Policy</a>
                    <a href="{{ route('legal.terms') }}" class="text-decoration-none text-muted mx-2">Terms of Service</a>
                    <a href="{{ route('legal.cookies') }}" class="text-decoration-none text-muted mx-2">Cookie Policy</a>
                </div>
            </div>
        </footer>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebarMenu').classList.toggle('show');
        }
    </script>
</body>
</html>
