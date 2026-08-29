<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>@yield('title', 'LaraSEOScan')</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo-icon.jpg') }}">
    @stack('meta')

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
        /* Ambient Background Radial Glows matching Welcome Hero */
        body {
            position: relative;
            overflow-x: hidden;
            background: #ffffff;
            color: #495057;
        }

        body::before {
            content: "";
            position: absolute;
            width: 600px;
            height: 600px;
            top: -200px;
            left: -200px;
            background: radial-gradient(circle, rgba(13, 110, 253, 0.05) 0%, rgba(0,0,0,0) 70%);
            z-index: -1;
            pointer-events: none;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background: #f8f9fa;
            border-right: 1px solid #e9ecef;
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
            color: #495057;
            text-decoration: none;
            border-radius: 12px;
            margin: 0.25rem 1rem;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .sidebar-link:hover, .sidebar-link.active {
            color: #0d6efd;
            background: rgba(13, 110, 253, 0.05);
            border: 1px solid rgba(13, 110, 253, 0.1);
        }

        .sidebar-link i {
            font-size: 1.2rem;
            margin-right: 0.75rem;
        }

        /* Header glassmorphism */
        .top-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .text-dark {
            color: #212529 !important;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .bg-white {
            background-color: #ffffff !important;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }

        .border-top, .border-bottom, .border {
            border-color: #e9ecef !important;
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
<body class="{{ Auth::check() ? 'authenticated' : 'guest' }}">

    @auth
    <!-- Left Premium Sidebar -->
    <div class="sidebar d-flex flex-column" id="sidebarMenu">
        <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
            <a class="d-flex align-items-center text-decoration-none" href="{{ route('scan.history') }}">
                <img src="{{ asset('images/logo-full.png') }}" alt="LaraSEOScan - Dashboard Logo" style="height: 40px; width: auto;" height="40">
            </a>
            <button class="btn btn-link text-dark d-lg-none p-0" onclick="toggleSidebar()"><i class="bi bi-x-lg"></i></button>
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

        <div class="p-4 border-top">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link w-100 border-0 bg-transparent text-start ps-0">
                    <i class="bi bi-box-arrow-right text-danger"></i> Log Out
                </button>
            </form>
        </div>
    </div>
    @endauth

    <!-- Main Content Area -->
    <div class="main-content" style="{{ Auth::check() ? '' : 'margin-left: 0 !important;' }}">
        <!-- Floating Glass Top navbar -->
        <div class="top-navbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                @auth
                <button class="btn btn-link text-dark d-lg-none me-3 p-0" onclick="toggleSidebar()"><i class="bi bi-list fs-3"></i></button>
                @else
                <a class="d-flex align-items-center text-decoration-none me-3" href="{{ route('scan.create') }}">
                    <img src="{{ asset('images/logo-full.png') }}" alt="LaraSEOScan - SEO Tool Suite Logo" style="height: 40px; width: auto;" height="40">
                </a>
                @endauth
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <!-- Free Tools Dropdown -->
                <div class="dropdown me-2 position-relative">
                    <button class="btn btn-link text-muted text-decoration-none dropdown-toggle fw-semibold p-0" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                        Free Tools <i class="bi bi-chevron-down ms-1" style="font-size: 0.75rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2" style="max-height: 380px; overflow-y: auto; min-width: 250px; background: #ffffff; border: 1px solid rgba(0, 0, 0, 0.08) !important; right: 0; left: auto;">
                        <li><a class="dropdown-item py-2 fw-bold text-dark border-bottom" href="{{ route('landing.hub') }}"><i class="bi bi-grid-fill me-2 text-primary"></i> Browse All Tools</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('landing.seo-checker') }}"><i class="bi bi-search me-2 text-primary"></i> Website SEO Checker</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('landing.meta-tag-checker') }}"><i class="bi bi-tags me-2 text-primary"></i> Meta Tag Checker</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('landing.meta-description-checker') }}"><i class="bi bi-file-earmark-text me-2 text-primary"></i> Meta Description Checker</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('landing.title-tag-checker') }}"><i class="bi bi-type-h1 me-2 text-primary"></i> Title Tag Checker</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('landing.h1-checker') }}"><i class="bi bi-hash me-2 text-primary"></i> H1 Tag Checker</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('landing.broken-link-checker') }}"><i class="bi bi-link-45deg me-2 text-primary"></i> Broken Link Checker</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('landing.robots-txt-checker') }}"><i class="bi bi-robot me-2 text-primary"></i> Robots.txt Checker</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('landing.sitemap-checker') }}"><i class="bi bi-diagram-3 me-2 text-primary"></i> Sitemap Checker</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('landing.schema-markup-checker') }}"><i class="bi bi-code-slash me-2 text-primary"></i> Schema Checker</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('landing.open-graph-checker') }}"><i class="bi bi-share me-2 text-primary"></i> Open Graph Checker</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('landing.image-seo-checker') }}"><i class="bi bi-image me-2 text-primary"></i> Image SEO Checker</a></li>
                    </ul>
                </div>

                @auth
                <div class="dropdown">
                    <button class="btn btn-link text-dark text-decoration-none dropdown-toggle d-flex align-items-center p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-weight: 600;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                        <span class="d-none d-md-inline text-dark">{{ Auth::user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Profile Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Log Out</button>
                            </form>
                        </li>
                    </ul>
                </div>
                @else
                <a href="{{ route('login') }}" class="text-decoration-none text-muted fw-semibold">Log In</a>
                <a href="{{ route('register') }}" class="btn btn-primary shadow-sm px-4">Register Free</a>
                @endauth
            </div>
        </div>

        <!-- Inner Content Grid -->
        <div class="flex-grow-1 container-fluid px-4 py-4">
            @yield('content')
        </div>

        <!-- Premium Footer -->
        <footer class="py-5 text-muted border-top" style="font-size: 0.85rem; background-color: #f8f9fa; border-top: 1px solid #e9ecef !important;">
            <div class="container">
                <div class="row gy-4 mb-4">
                    <div class="col-md-4">
                        <a class="d-flex align-items-center text-decoration-none mb-3" href="{{ route('scan.create') }}">
                            <img src="{{ asset('images/logo-full.png') }}" alt="LaraSEOScan - SEO Analytics Platform Logo" style="height: 40px; width: auto;" height="40">
                        </a>
                        <p class="text-muted mb-0">Open-source technical SEO auditing software. Scan domains, inspect links, and monitor core Web Vitals instantly.</p>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3 offset-lg-1">
                        <h6 class="text-uppercase text-dark fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.05em;">Free SEO Tools</h6>
                        <ul class="list-unstyled d-flex flex-column gap-2">
                            <li><a href="{{ route('landing.seo-checker') }}" class="text-decoration-none text-muted hover-primary">Website SEO Checker</a></li>
                            <li><a href="{{ route('landing.meta-tag-checker') }}" class="text-decoration-none text-muted hover-primary">Meta Tag Checker</a></li>
                            <li><a href="{{ route('landing.robots-txt-checker') }}" class="text-decoration-none text-muted hover-primary">Robots.txt Checker</a></li>
                            <li><a href="{{ route('landing.sitemap-checker') }}" class="text-decoration-none text-muted hover-primary">Sitemap Checker</a></li>
                            <li><a href="{{ route('landing.image-seo-checker') }}" class="text-decoration-none text-muted hover-primary">Image SEO Checker</a></li>
                        </ul>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <h6 class="text-uppercase text-dark fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.05em;">On-Page Audits</h6>
                        <ul class="list-unstyled d-flex flex-column gap-2">
                            <li><a href="{{ route('landing.title-tag-checker') }}" class="text-decoration-none text-muted hover-primary">Title Tag Checker</a></li>
                            <li><a href="{{ route('landing.meta-description-checker') }}" class="text-decoration-none text-muted hover-primary">Meta Description Checker</a></li>
                            <li><a href="{{ route('landing.h1-checker') }}" class="text-decoration-none text-muted hover-primary">H1 Tag Checker</a></li>
                            <li><a href="{{ route('landing.broken-link-checker') }}" class="text-decoration-none text-muted hover-primary">Broken Link Checker</a></li>
                            <li><a href="{{ route('landing.schema-markup-checker') }}" class="text-decoration-none text-muted hover-primary">Schema Markup Validator</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-top pt-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3" style="border-color: #e9ecef !important;">
                    <span class="small">&copy; {{ date('Y') }} LaraSEOScan. All rights reserved.</span>
                    <div class="small">
                        <a href="{{ route('legal.privacy') }}" class="text-decoration-none text-muted mx-2 hover-primary">Privacy Policy</a>
                        <a href="{{ route('legal.terms') }}" class="text-decoration-none text-muted mx-2 hover-primary">Terms of Service</a>
                        <a href="{{ route('legal.cookies') }}" class="text-decoration-none text-muted mx-2 hover-primary">Cookie Policy</a>
                    </div>
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
