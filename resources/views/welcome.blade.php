<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>LaraSEOScan - Open Source SEO Audit Tool Built for Laravel</title>
    <meta name="description" content="LaraSEOScan is a powerful, self-hosted SEO crawler built on Laravel. Audit your website, detect broken links, analyze performance, and improve rankings with actionable insights.">
    <meta name="keywords" content="SEO audit tool, SEO crawler, technical SEO audit, broken link checker, website SEO analyzer, Laravel SEO tool, open source SEO audit, php seo scraper">
    <link rel="canonical" href="{{ url('/') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="LaraSEOScan - Open Source SEO Audit Tool Built for Laravel">
    <meta property="og:description" content="Audit your website, detect SEO issues, analyze performance, and improve rankings — all from your own secure dashboard.">
    <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="LaraSEOScan - Open Source SEO Audit Tool Built for Laravel">
    <meta property="twitter:description" content="Audit your website, detect SEO issues, analyze performance, and improve rankings — all from your own secure dashboard.">
    <meta property="twitter:image" content="{{ asset('images/og-image.jpg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/landing.css') }}" rel="stylesheet">

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "SoftwareApplication",
      "name": "LaraSEOScan",
      "operatingSystem": "Web",
      "applicationCategory": "SEOApplication",
      "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "USD"
      },
      "description": "LaraSEOScan is a Laravel-based SEO crawler that audits websites, detects SEO issues, analyzes pages, broken links, performance metrics, and provides actionable insights."
    }
    </script>
</head>
<body class="antialiased">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="{{ url('/') }}">
                <i class="bi bi-bar-chart-fill me-2 fs-4"></i> LaraSEOScan
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item">
                        <a class="nav-link px-3" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="#how-it-works">How It Works</a>
                    </li>
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item ms-lg-3">
                                <a href="{{ route('dashboard') }}" class="btn btn-primary-gradient shadow-sm">
                                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                                </a>
                            </li>
                        @else
                            <li class="nav-item ms-lg-3">
                                <a href="{{ route('login') }}" class="btn btn-outline-primary px-4 me-2 rounded-pill">Log in</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="btn btn-primary-gradient shadow-sm">Get Started</a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section d-flex align-items-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content text-center text-lg-start mb-5 mb-lg-0">
                    <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2 rounded-pill fw-semibold">
                        <i class="bi bi-rocket-takeoff-fill me-1"></i> New: Improved SEO Crawler Engine
                    </span>
                    <h1 class="display-4 mb-4 text-dark">
                        Open Source SEO Audit Tool <span class="text-primary">Built for Laravel</span>
                    </h1>
                    <p class="lead text-secondary mb-5">
                        Crawl your website, detect critical SEO issues, analyze performance, and improve your rankings — all from your own secure, self-hosted dashboard.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                        @auth
                            <a href="{{ route('scan.create') }}" class="btn btn-primary-gradient btn-lg shadow-lg">
                                <i class="bi bi-play-circle-fill me-2"></i> Start Free Audit
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-primary-gradient btn-lg shadow-lg">
                                <i class="bi bi-play-circle-fill me-2"></i> Start Free Audit
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-dark btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Login
                            </a>
                        @endauth
                    </div>
                    <div class="mt-4 text-muted small">
                        <i class="bi bi-check-circle-fill text-success me-1"></i> No credit card required &nbsp;&bull;&nbsp; 
                        <i class="bi bi-check-circle-fill text-success me-1"></i> Open Source &nbsp;&bull;&nbsp; 
                        <i class="bi bi-check-circle-fill text-success me-1"></i> Instant Analysis
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image-wrapper bg-white p-2">
                         <!-- Placeholder for Dashboard Screenshot -->
                         <!-- Ideally replace with actual screenshot -->
                        <div class="ratio ratio-16x9 bg-light d-flex align-items-center justify-content-center rounded overflow-hidden">
                             <div class="text-center p-5">
                                 <i class="bi bi-laptop fs-1 text-muted mb-3 d-block"></i>
                                 <h5 class="text-muted">Interactive SEO Dashboard Preview</h5>
                                 <p class="small text-muted mb-0">Visualizing 85/100 Health Score, Issue Charts, and Crawl Data</p>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust / Brands Section (Optional) -->
    <section class="py-5 bg-white border-bottom">
        <div class="container text-center">
            <p class="text-uppercase text-muted fw-bold small mb-4">Trusted by SEOs and Developers for Technical Audits</p>
            <div class="row justify-content-center align-items-center opacity-50 grayscale-0">
                 <!-- Placeholders for "Logos" -->
                 <div class="col-6 col-md-2 mb-4 mb-md-0"><h5 class="fw-bold text-muted mb-0"><i class="bi bi-building"></i> AgencyOne</h5></div>
                 <div class="col-6 col-md-2 mb-4 mb-md-0"><h5 class="fw-bold text-muted mb-0"><i class="bi bi-code-square"></i> DevStudio</h5></div>
                 <div class="col-6 col-md-2 mb-4 mb-md-0"><h5 class="fw-bold text-muted mb-0"><i class="bi bi-shop"></i> E-ComShop</h5></div>
                 <div class="col-6 col-md-2 mb-4 mb-md-0"><h5 class="fw-bold text-muted mb-0"><i class="bi bi-rocket"></i> StartupX</h5></div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light" id="features">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Everything you need to master Technical SEO</h2>
                <p class="text-muted lead mx-auto" style="max-width: 600px;">
                    Unlike other tools that just list problems, LaraSEOScan helps you understand and fix them with developer-friendly insights.
                </p>
            </div>
            
            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon bg-icon-primary rounded-circle">
                            <i class="bi bi-bug-fill"></i>
                        </div>
                        <h4>Deep Crawl Engine</h4>
                        <p class="text-muted">Our crawler navigates your site like Googlebot, discovering pages and identifying indexing issues before they hurt your rankings.</p>
                    </div>
                </div>
                <!-- Feature 2 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon bg-icon-warning rounded-circle">
                            <i class="bi bi-link-45deg"></i>
                        </div>
                        <h4>Broken Link Detection</h4>
                        <p class="text-muted">Instantly find 404 errors and broken internal/external links that frustrate users and degrade your site's authority.</p>
                    </div>
                </div>
                <!-- Feature 3 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon bg-icon-success rounded-circle">
                            <i class="bi bi-speedometer"></i>
                        </div>
                        <h4>Performance Analysis</h4>
                        <p class="text-muted">Measure load times and asset sizes. Identify unoptimized images and scripts slowing down your user experience.</p>
                    </div>
                </div>
                <!-- Feature 4 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon bg-icon-info rounded-circle">
                            <i class="bi bi-file-earmark-check"></i>
                        </div>
                        <h4>On-Page SEO Checks</h4>
                        <p class="text-muted">Validate titles, meta descriptions, canonical tags, headings (H1-H6) structure, and image alt attributes automatically.</p>
                    </div>
                </div>
                <!-- Feature 5 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon bg-light text-dark border rounded-circle">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </div>
                        <h4>Exportable Reports</h4>
                        <p class="text-muted">Generate professional PDF and CSV reports to share with clients or your development team for quick remediation.</p>
                    </div>
                </div>
                <!-- Feature 6 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon bg-primary bg-opacity-10 text-primary rounded-circle">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4>Self-Hosted Privacy</h4>
                        <p class="text-muted">Your data stays on your server. No sharing sensitive project URLs with third-party SaaS platforms.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-5 bg-white" id="how-it-works">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-5 mb-5 mb-lg-0">
                    <h2 class="fw-bold mb-4">SEO Auditing made simple</h2>
                    <p class="text-muted mb-4">We've stripped away the complexity. Get a comprehensive technical audit in 3 simple steps.</p>
                    
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <div class="step-number">1</div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="fw-bold">Enter your Website URL</h5>
                            <p class="text-muted small">Just paste your homepage link. We handle the sitemap discovery and crawling logic.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0">
                            <div class="step-number">2</div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="fw-bold">Review the Analysis</h5>
                            <p class="text-muted small">Watch as the real-time dashboard populates with health scores, issues, and page details.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <div class="step-number">3</div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="fw-bold">Export & Fix</h5>
                            <p class="text-muted small">Download the report, assign tasks to your dev team, and improve your search rankings.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 offset-lg-1">
                    <div class="bg-light p-4 rounded-3 shadow-sm border">
                        <!-- Code/Tech visual placeholder -->
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                             <div class="d-flex gap-1">
                                 <span class="bg-danger rounded-circle" style="width:10px;height:10px;"></span>
                                 <span class="bg-warning rounded-circle" style="width:10px;height:10px;"></span>
                                 <span class="bg-success rounded-circle" style="width:10px;height:10px;"></span>
                             </div>
                             <small class="text-muted font-monospace">audit_result.json</small>
                        </div>
                        <pre class="text-muted mb-0" style="font-size: 0.8rem;">
{
  "status": "completed",
  "score": 85,
  "issues_found": {
    "critical": 3,
    "warnings": 12,
    "notices": 5
  },
  "performance": {
    "load_time": "0.45s",
    "ttfb": "0.12s"
  },
  "recommendations": [
    "Fix broken links on /blog",
    "Add meta description to /contact",
    "Compress images on homepage"
  ]
}</pre>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose / USP -->
    <section class="py-5 bg-dark text-white text-center">
        <div class="container py-5">
            <h2 class="fw-bold mb-4">Why Developers Choose LaraSEOScan</h2>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <p class="lead text-white-50 mb-5">
                       Stop paying monthly subscriptions for basic crawling features. LaraSEOScan gives you enterprise-grade auditing power built on the PHP framework you start with.
                    </p>
                </div>
            </div>
            <div class="row g-4 text-start">
                <div class="col-md-4">
                    <div class="p-4 border border-secondary rounded h-100 bg-dark bg-gradient">
                        <i class="bi bi-code-slash fs-2 text-primary mb-3"></i>
                        <h5>Developer Friendly</h5>
                        <p class="text-white-50 small">Built with standard Laravel components. Easy to extend, modify, or integrate into your existing CI/CD pipelines.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 border border-secondary rounded h-100 bg-dark bg-gradient">
                        <i class="bi bi-hdd-network fs-2 text-primary mb-3"></i>
                        <h5>No Limits</h5>
                        <p class="text-white-50 small">Crawl as many pages as your server can handle. No artificial project limits or credit costs per scan.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 border border-secondary rounded h-100 bg-dark bg-gradient">
                        <i class="bi bi-lock fs-2 text-primary mb-3"></i>
                        <h5>Secure by Default</h5>
                        <p class="text-white-50 small">Authentication provided by Laravel Breeze/Jetstream. Your audit data never leaves your infrastructure.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary bg-gradient text-white text-center">
        <div class="container py-5">
            <h2 class="fw-bold display-5 mb-4">Ready to optimize your website?</h2>
            <p class="lead mb-5 opacity-75">Join developers and SEOs using LaraSEOScan to build better websites.</p>
            @auth
                <a href="{{ route('scan.create') }}" class="btn btn-light btn-lg px-5 py-3 rounded-pill fw-bold shadow">Go to Dashboard</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5 py-3 rounded-pill fw-bold shadow me-2">Get Started Free</a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill fw-bold">Login</a>
            @endauth
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4">
                    <a class="navbar-brand fw-bold text-primary d-flex align-items-center mb-3" href="#">
                        <i class="bi bi-bar-chart-fill me-2 fs-4"></i> LaraSEOScan
                    </a>
                    <p class="text-muted small">
                        LaraSEOScan is an open-source SEO crawling tool designed for modern web development teams.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-secondary fs-5"><i class="bi bi-github"></i></a>
                        <a href="#" class="text-secondary fs-5"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-secondary fs-5"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-6 col-lg-2 offset-lg-1">
                    <h6 class="fw-bold mb-3">Product</h6>
                    <a href="#features" class="footer-link">Features</a>
                    <a href="#how-it-works" class="footer-link">How it Works</a>
                    <a href="#" class="footer-link">Pricing</a>
                    <a href="#" class="footer-link">Changelog</a>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="fw-bold mb-3">Resources</h6>
                    <a href="#" class="footer-link">Documentation</a>
                    <a href="#" class="footer-link">API Reference</a>
                    <a href="#" class="footer-link">Community</a>
                    <a href="#" class="footer-link">Blog</a>
                </div>
                <div class="col-lg-3">
                    <h6 class="fw-bold mb-3">Legal</h6>
                    <a href="{{ route('legal.privacy') }}" class="footer-link">Privacy Policy</a>
                    <a href="{{ route('legal.terms') }}" class="footer-link">Terms of Service</a>
                    <a href="{{ route('legal.cookies') }}" class="footer-link">Cookie Policy</a>
                </div>
            </div>
            <div class="border-top mt-5 pt-4 text-center text-muted small">
                &copy; {{ date('Y') }} LaraSEOScan. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
