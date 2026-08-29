@extends('layouts.app')

@section('title', $data['title'])

@section('content')
@push('meta')
    <meta name="description" content="{{ $data['meta_description'] }}">
    <meta property="og:title" content="{{ $data['title'] }}">
    <meta property="og:description" content="{{ $data['meta_description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
@endpush

@push('scripts')
    <script>
    function copyCodeExample() {
        const codeElement = document.getElementById('codeSnippet');
        const range = document.createRange();
        range.selectNode(codeElement);
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        try {
            document.execCommand('copy');
            const copyBtn = document.getElementById('copyBtn');
            copyBtn.innerHTML = '<i class="bi bi-check2"></i> Copied!';
            setTimeout(() => {
                copyBtn.innerHTML = '<i class="bi bi-clipboard"></i> Copy HTML';
            }, 2000);
        } catch(err) {
            console.error('Failed to copy text', err);
        }
        window.getSelection().removeAllRanges();
    }
    </script>
@endpush

<div class="container py-4">
    
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted" style="font-size: 13px;">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('landing.hub') }}" class="text-decoration-none text-muted" style="font-size: 13px;">SEO Tools</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page" style="font-size: 13px;">{{ $data['header'] }}</li>
        </ol>
    </nav>

    <!-- Redesigned Premium Horizontal Tools Navigator (Sub-tabs bar) -->
    <div class="d-flex overflow-x-auto gap-2 pb-3 mb-5 border-bottom scrollbar-hidden" style="white-space: nowrap; -webkit-overflow-scrolling: touch; border-color: var(--border-default) !important;">
        <a href="{{ route('landing.seo-checker') }}" class="btn {{ $tool === 'seo-checker' ? 'btn-primary' : 'btn-light border text-dark' }} rounded-pill px-3 py-2 btn-sm">
            <i class="bi bi-search me-1"></i> Website SEO
        </a>
        <a href="{{ route('landing.meta-tag-checker') }}" class="btn {{ $tool === 'meta-tag-checker' ? 'btn-primary' : 'btn-light border text-dark' }} rounded-pill px-3 py-2 btn-sm">
            <i class="bi bi-tags me-1"></i> Meta Tags
        </a>
        <a href="{{ route('landing.meta-description-checker') }}" class="btn {{ $tool === 'meta-description-checker' ? 'btn-primary' : 'btn-light border text-dark' }} rounded-pill px-3 py-2 btn-sm">
            <i class="bi bi-file-earmark-text me-1"></i> Meta Description
        </a>
        <a href="{{ route('landing.title-tag-checker') }}" class="btn {{ $tool === 'title-tag-checker' ? 'btn-primary' : 'btn-light border text-dark' }} rounded-pill px-3 py-2 btn-sm">
            <i class="bi bi-type-h1 me-1"></i> Title Tag
        </a>
        <a href="{{ route('landing.h1-checker') }}" class="btn {{ $tool === 'h1-checker' ? 'btn-primary' : 'btn-light border text-dark' }} rounded-pill px-3 py-2 btn-sm">
            <i class="bi bi-hash me-1"></i> H1 Tag
        </a>
        <a href="{{ route('landing.broken-link-checker') }}" class="btn {{ $tool === 'broken-link-checker' ? 'btn-primary' : 'btn-light border text-dark' }} rounded-pill px-3 py-2 btn-sm">
            <i class="bi bi-link-45deg me-1"></i> Broken Links
        </a>
        <a href="{{ route('landing.robots-txt-checker') }}" class="btn {{ $tool === 'robots-txt-checker' ? 'btn-primary' : 'btn-light border text-dark' }} rounded-pill px-3 py-2 btn-sm">
            <i class="bi bi-robot me-1"></i> Robots.txt
        </a>
        <a href="{{ route('landing.sitemap-checker') }}" class="btn {{ $tool === 'sitemap-checker' ? 'btn-primary' : 'btn-light border text-dark' }} rounded-pill px-3 py-2 btn-sm">
            <i class="bi bi-diagram-3 me-1"></i> Sitemap.xml
        </a>
        <a href="{{ route('landing.schema-markup-checker') }}" class="btn {{ $tool === 'schema-markup-checker' ? 'btn-primary' : 'btn-light border text-dark' }} rounded-pill px-3 py-2 btn-sm">
            <i class="bi bi-code-slash me-1"></i> Schema
        </a>
        <a href="{{ route('landing.open-graph-checker') }}" class="btn {{ $tool === 'open-graph-checker' ? 'btn-primary' : 'btn-light border text-dark' }} rounded-pill px-3 py-2 btn-sm">
            <i class="bi bi-share me-1"></i> Open Graph
        </a>
        <a href="{{ route('landing.image-seo-checker') }}" class="btn {{ $tool === 'image-seo-checker' ? 'btn-primary' : 'btn-light border text-dark' }} rounded-pill px-3 py-2 btn-sm">
            <i class="bi bi-image me-1"></i> Image SEO
        </a>
    </div>

    <!-- Main Tool Content -->
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            
            <!-- Premium Hero Redesign -->
            <div class="text-center mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill font-monospace text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.05em;">Technical SEO Tool</span>
                <h1 class="fw-bold mb-3 text-dark">{{ $data['header'] }}</h1>
                <p class="text-muted fs-5 mx-auto" style="max-width: 650px;">{{ $data['description'] }}</p>
            </div>

            @if(session('message'))
                <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success alert-dismissible fade show rounded-4 py-3 mb-4 shadow" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i> {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Redesigned Hero Scanner Input Box -->
            <div class="card border-0 p-4 p-md-5 mb-5" style="background: var(--bg-surface-1) !important;">
                <form method="POST" action="{{ route('scan.submit') }}">
                    @csrf
                    <input type="hidden" name="type" value="{{ $tool }}">
                    
                    <div class="mb-4">
                        <div class="input-group border rounded-3 overflow-hidden shadow-inner p-1 bg-white" style="height: 60px; border-color: var(--border-default) !important;">
                            <span class="input-group-text bg-transparent border-0 text-muted px-3"><i class="bi bi-globe2 fs-4 text-muted"></i></span>
                            <input type="url" class="form-control bg-transparent border-0 text-dark ps-2 fs-5" name="url" id="url" placeholder="https://example.com" required style="outline: none; box-shadow: none;">
                            <button type="submit" class="btn btn-primary px-4 d-flex align-items-center justify-content-center h-100 rounded-2" style="font-size: 1rem;">
                                Scan Website <i class="bi bi-arrow-right-short fs-4 ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-center gap-4 text-muted small mt-2">
                        <span><i class="bi bi-check-circle text-success me-1"></i> ✓ Free initial scan</span>
                        <span><i class="bi bi-check-circle text-success me-1"></i> ✓ No installation required</span>
                        <span><i class="bi bi-check-circle text-success me-1"></i> ✓ Real-time metrics</span>
                    </div>
                </form>
                
                <!-- Analyzes direct items under the bar -->
                <div class="text-center mt-4 pt-3 border-top text-muted small" style="border-color: var(--border-default) !important;">
                    Analyzes: <strong class="text-dark">{{ implode(' • ', array_keys($data['features'])) }}</strong>
                </div>
            </div>

            <!-- Dynamic Visual Hero Artifact per page -->
            <div class="card border-0 p-4 mb-5" style="background: var(--bg-surface-1) !important;">
                <h5 class="text-dark fw-bold mb-3" style="font-size: 1rem;"><i class="bi bi-cpu text-primary me-2"></i> Live Analyzer Simulator</h5>
                
                @if(in_array($tool, ['meta-tag-checker', 'title-tag-checker', 'meta-description-checker']))
                    <!-- Metadata Visual Mockup -->
                    <div class="p-4 rounded-4 bg-white border border-secondary border-opacity-15 shadow-sm">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom border-light pb-2">
                            <span class="text-muted font-monospace small">Document Head Metadata</span>
                            <span class="badge bg-success bg-opacity-10 text-success">Active Check</span>
                        </div>
                        <div class="p-3 rounded bg-light mb-2 border border-light">
                            <span class="text-muted font-monospace small d-block mb-1">&lt;title&gt;</span>
                            <strong class="text-dark fs-5">{{ $data['title'] }}</strong>
                        </div>
                        <div class="p-3 rounded bg-light mb-2 border border-light">
                            <span class="text-muted font-monospace small d-block mb-1">&lt;meta name="description"&gt;</span>
                            <span class="text-secondary small">{{ $data['meta_description'] }}</span>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-light text-muted">Title: {{ strlen($data['title']) }} chars</span>
                            <span class="badge bg-light text-muted">Desc: {{ strlen($data['meta_description']) }} chars</span>
                        </div>
                    </div>

                @elseif($tool === 'robots-txt-checker')
                    <!-- Robots.txt File Simulator -->
                    <div class="p-4 rounded-4 bg-white border border-secondary border-opacity-15 shadow-sm font-monospace">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom border-light pb-2">
                            <span class="text-muted small">/robots.txt Directives</span>
                            <span class="badge bg-success bg-opacity-10 text-success">Accessible</span>
                        </div>
                        <div class="p-3 rounded bg-light border border-light text-primary">
                            <div>User-agent: *</div>
                            <div>Disallow: /admin/</div>
                            <div>Disallow: /config/</div>
                            <div class="text-dark mt-2">Allow: /</div>
                            <div class="text-muted mt-3">Sitemap: https://yourdomain.com/sitemap.xml</div>
                        </div>
                    </div>

                @elseif($tool === 'h1-checker')
                    <!-- H1 Nested Tree Simulator -->
                    <div class="p-4 rounded-4 bg-white border border-secondary border-opacity-15 shadow-sm">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom border-light pb-2">
                            <span class="text-muted font-monospace small">Heading Hierarchy Structure</span>
                            <span class="badge bg-info bg-opacity-10 text-info">Visualizer</span>
                        </div>
                        <div class="p-3 rounded bg-light border border-light">
                            <div class="mb-2">
                                <span class="badge bg-primary me-2">H1</span> <strong class="text-dark">Main Page Title (Only 1 Recommended)</strong>
                            </div>
                            <div class="ms-4 mb-2 border-left border-light ps-3">
                                <span class="badge bg-secondary text-light me-2">H2</span> <span class="text-dark">Primary Section Header</span>
                            </div>
                            <div class="ms-5 border-left border-light ps-3">
                                <span class="badge bg-dark text-muted me-2">H3</span> <span class="text-muted">Subsection Detail Points</span>
                            </div>
                        </div>
                    </div>

                @elseif($tool === 'sitemap-checker')
                    <!-- Sitemap visualizer -->
                    <div class="p-4 rounded-4 bg-white border border-secondary border-opacity-15 shadow-sm font-monospace">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom border-light pb-2">
                            <span class="text-muted small">XML Sitemap Map Directory</span>
                            <span class="badge bg-success bg-opacity-10 text-success">Valid Format</span>
                        </div>
                        <div class="p-3 rounded bg-light border border-light text-muted small">
                            <div>&lt;urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"&gt;</div>
                            <div class="ms-3 text-primary">&lt;url&gt;</div>
                            <div class="ms-4 text-dark">&lt;loc&gt;https://yourdomain.com/&lt;/loc&gt;</div>
                            <div class="ms-4 text-dark">&lt;lastmod&gt;{{ date('Y-m-d') }}&lt;/lastmod&gt;</div>
                            <div class="ms-3 text-primary">&lt;/url&gt;</div>
                            <div>&lt;/urlset&gt;</div>
                        </div>
                    </div>

                @elseif($tool === 'broken-link-checker')
                    <!-- Link diagnostic simulator -->
                    <div class="p-4 rounded-4 bg-white border border-secondary border-opacity-15 shadow-sm">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom border-light pb-2">
                            <span class="text-muted font-monospace small">Link Status Auditor</span>
                            <span class="badge bg-success bg-opacity-10 text-success">Scan Mode</span>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex justify-content-between p-2 rounded bg-light border border-light align-items-center">
                                <span class="text-muted small">https://yourdomain.com/about</span>
                                <span class="badge bg-success bg-opacity-20 text-success">200 OK</span>
                            </div>
                            <div class="d-flex justify-content-between p-2 rounded bg-light border border-light align-items-center">
                                <span class="text-muted small">https://yourdomain.com/blog/seo-tips</span>
                                <span class="badge bg-warning bg-opacity-20 text-warning">301 Redirect</span>
                            </div>
                            <div class="d-flex justify-content-between p-2 rounded bg-light border border-light align-items-center">
                                <span class="text-muted small">https://yourdomain.com/dead-end-page</span>
                                <span class="badge bg-danger bg-opacity-20 text-danger">404 Broken</span>
                            </div>
                        </div>
                    </div>

                @else
                    <!-- Generic technical audit dashboard simulator -->
                    <div class="p-4 rounded-4 bg-white border border-secondary border-opacity-15 shadow-sm">
                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom border-light pb-2">
                            <span class="text-muted font-monospace small">Technical SEO Score Checker</span>
                            <span class="badge bg-success bg-opacity-10 text-success">Ready</span>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-6 text-center">
                                <div class="d-inline-block p-4 rounded-circle bg-light border border-light mb-2">
                                    <h2 class="fw-bold mb-0 text-primary">-- / 100</h2>
                                </div>
                                <div class="text-muted small">Technical Health Rating</div>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 text-dark small">
                                    <li><i class="bi bi-circle-fill text-success me-2 fs-6" style="font-size: 8px !important;"></i> Passed Checks</li>
                                    <li><i class="bi bi-circle-fill text-warning me-2 fs-6" style="font-size: 8px !important;"></i> Pending Warnings</li>
                                    <li><i class="bi bi-circle-fill text-danger me-2 fs-6" style="font-size: 8px !important;"></i> Critical Redirects</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Rich Educational Description (H2, H3 hierarchy) -->
            <div class="card border-0 p-4 p-md-5 mb-4" style="background: var(--bg-surface-1) !important;">
                <h2 class="fw-bold text-dark mb-3" style="font-size: 1.75rem;">{{ $data['h2_title'] }}</h2>
                <p class="text-muted mb-4 fs-6 leading-relaxed">{{ $data['h2_desc'] }}</p>
                
                <div class="row g-4 mt-2">
                    @foreach($data['features'] as $title => $desc)
                        <div class="col-md-4">
                            <div class="p-3 rounded-4 bg-secondary bg-opacity-5 border h-100" style="border-color: var(--border-default) !important;">
                                <h5 class="text-dark fw-bold mb-2" style="font-size: 1rem;"><i class="bi bi-check2-circle text-primary me-2"></i> {{ $title }}</h5>
                                <p class="text-muted small mb-0">{{ $desc }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- What we Check details -->
            <div class="card border-0 p-4 p-md-5 mb-4" style="background: var(--bg-surface-1) !important;">
                <div class="rounded-4 overflow-hidden mb-4" style="max-height: 240px; border: 1px solid var(--border-default);">
                    <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=800&q=80" alt="Technical SEO Audit Illustration" class="w-100" style="height: 200px; object-fit: cover;">
                </div>
                <h2 class="fw-bold text-dark mb-4" style="font-size: 1.75rem;"><i class="bi bi-patch-check text-primary me-2"></i> What Does This Audit Validate?</h2>
                @foreach($data['checks'] as $check)
                    <div class="mb-4">
                        <h3 class="fw-bold text-dark fs-5"><i class="bi bi-check2-circle text-primary me-2"></i> {{ $check['title'] }}</h3>
                        <p class="text-muted small">{{ $check['desc'] }}</p>
                    </div>
                @endforeach
            </div>

            <!-- HTML Code Example Section with Copy Option -->
            <div class="card border-0 p-4 p-md-5 mb-4" style="background: var(--bg-surface-1) !important;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="fw-bold text-dark mb-0"><i class="bi bi-code-slash text-primary me-2"></i> {{ $data['code_title'] ?? 'Technical Code Example' }}</h3>
                    <button type="button" id="copyBtn" onclick="copyCodeExample()" class="btn btn-outline-primary btn-sm rounded-pill py-1 px-3" style="font-size: 0.8rem;">
                        <i class="bi bi-clipboard"></i> Copy HTML
                    </button>
                </div>
                <p class="text-muted mb-4 small">Here is a standard, optimized markup template for this feature:</p>
                <div class="bg-dark bg-opacity-90 p-4 rounded-4 border position-relative mb-2" style="border-color: var(--border-default) !important;">
                    <pre class="mb-0 text-info" style="font-size: 0.85rem; overflow-x: auto;"><code id="codeSnippet">{!! e($data['code_example']) !!}</code></pre>
                </div>
            </div>

            <!-- Common Problems & Fixes Table -->
            <div class="card border-0 p-4 p-md-5 mb-4" style="background: var(--bg-surface-1) !important;">
                <h3 class="fw-bold text-dark mb-4"><i class="bi bi-exclamation-triangle text-primary me-2"></i> Common Issues & Fixes</h3>
                <div class="table-responsive">
                    <table class="table table-borderless text-dark mb-0" style="font-size: 0.9rem;">
                        <thead>
                            <tr class="border-bottom" style="border-color: var(--border-default) !important;">
                                <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem;">Problem</th>
                                <th class="py-3 text-uppercase text-muted" style="font-size: 0.75rem;">Recommended Fix</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['fixes'] as $fix)
                                <tr>
                                    <td class="py-3 font-semibold text-danger">{{ $fix['problem'] }}</td>
                                    <td class="py-3 text-muted">{{ $fix['fix'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Clean FAQs Accordion (Unboxed layout) -->
            <div class="py-4 mb-4">
                <h3 class="fw-bold text-dark mb-4"><i class="bi bi-question-circle text-primary me-2"></i> Frequently Asked Questions</h3>
                <div class="accordion border-0 bg-transparent" id="faqAccordion">
                    @foreach($data['faqs'] as $index => $faq)
                        <div class="accordion-item border-0 bg-transparent mb-3 border-bottom pb-3" style="border-color: var(--border-default) !important;">
                            <h4 class="accordion-header" id="headingFaq-{{ $index }}">
                                <button class="accordion-button collapsed bg-transparent text-dark border-0 py-2 px-0 shadow-none hover-primary" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapseFaq-{{ $index }}" 
                                        aria-expanded="false" 
                                        aria-controls="collapseFaq-{{ $index }}"
                                        style="font-size: 1.05rem; font-weight: 600;">
                                    {{ $faq['q'] }}
                                </button>
                            </h4>
                            <div id="collapseFaq-{{ $index }}" class="collapse" aria-labelledby="headingFaq-{{ $index }}" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted px-0 py-2 bg-transparent rounded-0 mt-1" style="font-size: 0.95rem; line-height: 1.6;">
                                    {{ $faq['a'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- How It Works Section -->
            <div class="card border-0 p-4 p-md-5 mb-4" style="background: var(--bg-surface-1) !important;">
                <h3 class="fw-bold text-dark mb-4"><i class="bi bi-gear-wide-connected text-primary me-2"></i> How the {{ $data['header'] }} Works</h3>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="d-flex align-items-start">
                            <div class="badge bg-primary bg-opacity-10 text-primary rounded-circle px-3 py-2 me-3 fs-5">1</div>
                            <div>
                                <h5 class="text-dark fw-bold mb-2">Enter URL</h5>
                                <p class="text-muted small">Input any target website address in the tool input field above to start the check.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-start">
                            <div class="badge bg-primary bg-opacity-10 text-primary rounded-circle px-3 py-2 me-3 fs-5">2</div>
                            <div>
                                <h5 class="text-dark fw-bold mb-2">Real-Time Audit</h5>
                                <p class="text-muted small">LaraSEOScan will retrieve the page data and scan it against standard web directives.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-start">
                            <div class="badge bg-primary bg-opacity-10 text-primary rounded-circle px-3 py-2 me-3 fs-5">3</div>
                            <div>
                                <h5 class="text-dark fw-bold mb-2">Get Actionable Tips</h5>
                                <p class="text-muted small">Download a detailed audit card showing errors, warnings, and optimization code suggestions.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Master Technical SEO Features Grid -->
            <div class="card border-0 p-4 p-md-5 mb-4" style="background: var(--bg-surface-1) !important;">
                <h3 class="fw-bold text-dark mb-2"><i class="bi bi-grid-fill text-primary me-2"></i> Everything You Need to Master Technical SEO</h3>
                <p class="text-muted mb-4 fs-6">LaraSEOScan gives you deep crawl capabilities to fix site health errors and capture organic rankings.</p>
                <div class="row g-3">
                    <div class="col-md-6 col-lg-4">
                        <div class="p-3 rounded-4 bg-secondary bg-opacity-5 border h-100" style="border-color: var(--border-default) !important;">
                            <i class="bi bi-bug-fill text-primary fs-3 d-block mb-2"></i>
                            <h6 class="text-dark fw-bold">Deep Crawl Engine</h6>
                            <p class="text-muted small mb-0">Googlebot-like link crawl to check indexing directives and hierarchy paths.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="p-3 rounded-4 bg-secondary bg-opacity-5 border h-100" style="border-color: var(--border-default) !important;">
                            <i class="bi bi-link-45deg text-success fs-3 d-block mb-2"></i>
                            <h6 class="text-dark fw-bold">Broken Link Detection</h6>
                            <p class="text-muted small mb-0">Identify 404 response codes and redirect errors instantly before bots penalize you.</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="p-3 rounded-4 bg-secondary bg-opacity-5 border h-100" style="border-color: var(--border-default) !important;">
                            <i class="bi bi-speedometer text-warning fs-3 d-block mb-2"></i>
                            <h6 class="text-dark fw-bold">Performance Auditing</h6>
                            <p class="text-muted small mb-0">Capture page load speed issues, asset size anomalies, and Web Vitals checklist logs.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Developer USPs Section -->
            <div class="card border-0 p-4 p-md-5 mb-4 text-center" style="background: var(--bg-surface-1) !important;">
                <h3 class="fw-bold text-dark mb-2"><i class="bi bi-cpu text-primary me-2"></i> Why Developers Choose LaraSEOScan</h3>
                <p class="text-muted mb-5 fs-6">Open-source technical auditing software built directly on Laravel.</p>
                <div class="row g-4 text-start">
                    <div class="col-md-4">
                        <div class="p-3 rounded-4 bg-secondary bg-opacity-5 border h-100" style="border-color: var(--border-default) !important;">
                            <i class="bi bi-code-slash text-primary fs-3 d-block mb-2"></i>
                            <h5 class="text-dark fw-bold" style="font-size: 1rem;">Developer First</h5>
                            <p class="text-muted small mb-0">Easily integrated into staging environments, custom reporting, or CI/CD pipelines.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-4 bg-secondary bg-opacity-5 border h-100" style="border-color: var(--border-default) !important;">
                            <i class="bi bi-hdd-network text-success fs-3 d-block mb-2"></i>
                            <h5 class="text-dark fw-bold" style="font-size: 1rem;">Crawl Without Limits</h5>
                            <p class="text-muted small mb-0">Run as many scans as your infrastructure allows. Zero credit restrictions or recurring fees.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-4 bg-secondary bg-opacity-5 border h-100" style="border-color: var(--border-default) !important;">
                            <i class="bi bi-lock text-warning fs-3 d-block mb-2"></i>
                            <h5 class="text-dark fw-bold" style="font-size: 1rem;">Privacy Focused</h5>
                            <p class="text-muted small mb-0">Keep all target audit databases and scanned domain history safe on your own servers.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contextual Internal Links -->
            @php
                $allToolsList = [
                    'seo-checker' => ['name' => 'Website SEO Checker', 'route' => 'landing.seo-checker', 'prompt' => 'Need a broader audit? Run a full'],
                    'meta-tag-checker' => ['name' => 'Meta Tag Checker', 'route' => 'landing.meta-tag-checker', 'prompt' => 'Want to check all tags together? Use our'],
                    'meta-description-checker' => ['name' => 'Meta Description Checker', 'route' => 'landing.meta-description-checker', 'prompt' => 'Need to validate descriptions? Try our'],
                    'title-tag-checker' => ['name' => 'Title Tag Checker', 'route' => 'landing.title-tag-checker', 'prompt' => 'Check page titles specifically with our'],
                    'h1-checker' => ['name' => 'H1 Tag Checker', 'route' => 'landing.h1-checker', 'prompt' => 'Verify heading structure with our'],
                    'broken-link-checker' => ['name' => 'Broken Link Checker', 'route' => 'landing.broken-link-checker', 'prompt' => 'Scan for dead loops with our'],
                    'robots-txt-checker' => ['name' => 'Robots.txt Checker', 'route' => 'landing.robots-txt-checker', 'prompt' => 'Audit crawler instructions using'],
                    'sitemap-checker' => ['name' => 'XML Sitemap Checker', 'route' => 'landing.sitemap-checker', 'prompt' => 'Validate sitemap files with our'],
                    'schema-markup-checker' => ['name' => 'Schema Validator', 'route' => 'landing.schema-markup-checker', 'prompt' => 'Check rich snippet script using'],
                    'open-graph-checker' => ['name' => 'Open Graph Checker', 'route' => 'landing.open-graph-checker', 'prompt' => 'Optimize social sharing previews with'],
                    'image-seo-checker' => ['name' => 'Image SEO Checker', 'route' => 'landing.image-seo-checker', 'prompt' => 'Check image alt tags with our'],
                ];
                
                $activeSlug = $tool ?? '';
                $filteredToolsList = array_filter($allToolsList, function($k) use ($activeSlug) {
                    return $k !== $activeSlug;
                }, ARRAY_FILTER_USE_KEY);
                
                $keys = array_keys($filteredToolsList);
                shuffle($keys);
                $pickedItems = array_slice($keys, 0, 3);
            @endphp
            <div class="p-4 mb-4 rounded-4 border" style="background: rgba(13, 110, 253, 0.02) !important; border-color: var(--border-default) !important;">
                <p class="mb-0 text-muted small text-center">
                    @foreach($pickedItems as $index => $itemKey)
                        @php $item = $allToolsList[$itemKey]; @endphp
                        {{ $item['prompt'] }} <a href="{{ route($item['route']) }}" class="text-primary text-decoration-none fw-semibold">{{ $item['name'] }}</a>.
                        @if($index < 2) &nbsp;&bull;&nbsp; @endif
                    @endforeach
                </p>
            </div>

        </div>
    </div>
</div>

<style>
    .scrollbar-hidden::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hidden {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
</style>
@endsection
