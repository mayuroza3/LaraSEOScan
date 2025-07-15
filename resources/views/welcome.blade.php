<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Free SEO Checker – Audit your website’s meta tags, links, headings, alt attributes, robots.txt, sitemap and more. Export PDF or CSV SEO reports instantly.">
    <meta name="keywords"
        content="Free SEO Checker, Website SEO Audit, Meta Tag Analyzer, SEO Report Generator, SEO Tool Laravel">
    <meta name="author" content="Mayur Oza">
    <title>Free SEO Checker & Website Audit Tool | LaraSEOScan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bg-light-alt {
            background-color: #f9f9f9;
        }

        .section-pad {
            padding: 60px 0;
        }
    </style>
</head>

<body>

    <!-- Hero Section -->
    <section class="text-white text-center py-5"
        style="background-image: url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1600&q=80'); background-size: cover; background-position: center;">
        <div class="container" style="background: rgba(0, 0, 0, 0.5); padding: 60px 20px;">
            <h1 class="display-4 fw-bold">Free SEO Checker & Website Audit Tool</h1>
            <p class="lead">Instantly scan any website for SEO health – Meta Tags, Headings, Canonical, Links, Alt
                Text, Sitemap & more.</p>
            <a href="{{ route('login') }}" class="btn btn-light btn-lg mt-3">🧪 Start Free SEO Scan</a>
        </div>
    </section>

    <!-- Features -->
    <main class="section-pad bg-white" id="features">
        <div class="container">
            <h2 class="text-center mb-5">🚀 SEO Features We Scan</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">📑 Meta Tags</h5>
                            <p class="card-text">Check for Title, Description, Canonical and Robots meta tags to ensure
                                optimal search appearance.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">🔍 Headings Structure</h5>
                            <p class="card-text">Analyze H1–H6 tags to ensure logical and SEO-friendly content
                                structure.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">🖼️ Images & Alt Text</h5>
                            <p class="card-text">Scan all images and detect missing or empty alt attributes affecting
                                accessibility and SEO.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">🔗 Link Checker</h5>
                            <p class="card-text">Identify broken internal links and external links with error codes
                                (404, 403, etc).</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">📁 Robots.txt & Sitemap</h5>
                            <p class="card-text">Automatically check if your domain has a valid robots.txt and
                                sitemap.xml file.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">📤 PDF / CSV Export</h5>
                            <p class="card-text">Download SEO reports in clean PDF or CSV format, perfect for sharing
                                with teams or clients.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- How It Works -->
    <section class="bg-light-alt section-pad" id="how-it-works">
        <div class="container">
            <h2 class="text-center mb-5">🔧 How LaraSEOScan Works</h2>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="https://images.unsplash.com/photo-1709281847802-9aef10b6d4bf?auto=format&fit=crop&w=800&q=80"
                        class="img-fluid rounded shadow" alt="How it works">
                </div>
                <div class="col-md-6">
                    <ol class="fs-5">
                        <li><strong>Enter Your URL:</strong> Simply login and enter the website or page URL you want to
                            scan.</li>
                        <li><strong>Run SEO Scan:</strong> Our crawler will inspect every major on-page SEO element.
                        </li>
                        <li><strong>Get Report:</strong> Review errors, warnings, and passed checks visually.</li>
                        <li><strong>Export:</strong> Download a formatted SEO report in PDF or CSV.</li>
                    </ol>
                    <p class="text-muted mt-4 text-center">Fast. Accurate. Developer Friendly.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section with Image Background -->
    <section class="text-white text-center section-pad"
        style="background-image: url('https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1600&q=80'); background-size: cover; background-position: center;">
        <div class="container" style="background: rgba(0, 0, 0, 0.5); padding: 60px 20px;">
            <h3>Ready to Audit Your Website?</h3>
            <p class="lead">Start scanning your site and boost your SEO rankings today.</p>
            <a href="{{ route('register') }}" class="btn btn-light btn-lg mt-2">🚀 Create Free Account</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} LaraSEOScan by Mayur Oza. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
