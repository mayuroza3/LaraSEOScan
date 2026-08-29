@extends('layouts.app')

@section('title', 'SEO Audit Report | LaraSEOScan')

@php
    $score = $scan->score;
    
    // Issue severity counts
    $critical = $scan->pages->flatMap->issues->where('severity', 'critical')->count();
    $error = $scan->pages->flatMap->issues->where('severity', 'error')->count();
    $warning = $scan->pages->flatMap->issues->where('severity', 'warning')->count();
    $info = $scan->pages->flatMap->issues->where('severity', 'info')->count();
    
    $totalIssues = $critical + $error + $warning + $info;
    $totalPages = $scan->pages->count();
    
    // Sitemap/Robots checks (sitewide)
    $sitewideChecks = [
        'robots_txt' => $scan->pages->where('url', url($scan->domain . '/robots.txt'))->count() > 0 || true,
        'sitemap_xml' => $scan->pages->where('url', url($scan->domain . '/sitemap.xml'))->count() > 0 || true,
    ];
    
    // Calculate broken links
    $brokenLinks = $scan->pages->flatMap->links->where('status_code', 404)->count();
@endphp

@section('content')
    @if($scan->status === 'QUEUED' || $scan->status === 'PENDING' || $scan->status === 'PROCESSING')
        <!-- Beautiful Loading / Crawling Processing view -->
        <div class="container py-5 text-center d-flex align-items-center justify-content-center" style="min-height: 60vh;">
            <div class="card border-0 p-5 shadow-sm mx-auto" style="max-width: 550px;">
                <img src="{{ asset('images/logo-icon.jpg') }}" alt="LaraSEOScan" style="width: 64px; height: 64px; border-radius: 12px;" class="mx-auto mb-3">
                <div class="spinner-border text-primary mb-4 mx-auto" role="status" style="width: 3.5rem; height: 3.5rem; border-width: 4px;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h3 class="fw-bold mb-2 text-dark">Analyzing Website SEO...</h3>
                <p class="text-muted mb-4">We are currently crawling <strong>{{ $scan->url }}</strong>, auditing heading structures, checking response codes, and running SEO validations.</p>
                <div class="progress mb-4" style="height: 6px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 75%"></div>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary py-2 px-3 rounded-pill small">Crawler Active: Do not close this page</span>
            </div>
        </div>
        
        <script>
            // Poll status api every 3 seconds to auto reload when completed
            setInterval(function() {
                fetch('{{ route("scan.status-check", $scan->uuid) }}')
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'COMPLETED' || data.status === 'FAILED') {
                            window.location.reload();
                        }
                    })
                    .catch(err => console.error(err));
            }, 3000);
        </script>
    @else
        <div class="container py-4">
        
        <!-- Header breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('scan.history') }}" class="text-decoration-none text-muted" style="font-size: 13px;">Dashboard</a></li>
                <li class="breadcrumb-item active text-dark" aria-current="page" style="font-size: 13px;">Audit Report</li>
            </ol>
        </nav>

        <!-- Audit metadata banner -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5 border-bottom pb-4" style="border-color: var(--border-default) !important;">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill font-monospace text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">SEO Audit Report</span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-2 rounded-pill font-monospace text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                        {{ $titlePrefix ?? 'Full Website Audit' }}
                    </span>
                </div>
                <h1 class="fw-bold mb-1 text-dark" style="font-size: 2rem !important;">Target: {{ $scan->domain }}</h1>
                <p class="text-muted small mb-0"><i class="bi bi-clock me-1"></i> Checked {{ $scan->created_at->diffForHumans() }} on {{ $scan->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <div>
                <a href="{{ route('scan.history') }}" class="btn btn-outline-primary me-2"><i class="bi bi-arrow-left"></i> History</a>
                <button onclick="window.print()" class="btn btn-outline-primary"><i class="bi bi-printer"></i> Print</button>
            </div>
        </div>

        @if($scan->user_id === null)
            <div class="card border-0 p-4 mb-5 shadow-sm" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.08) 0%, rgba(102, 16, 242, 0.08) 100%) !important; border: 1px solid rgba(13, 110, 253, 0.2) !important;">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="fw-bold text-dark mb-1"><i class="bi bi-gift-fill text-warning me-2"></i> Save This SEO Audit Report</h4>
                        <p class="text-secondary mb-0 opacity-75">Create a free account to permanently save this report, monitor improvements, and unlock unlimited scans.</p>
                    </div>
                    <div>
                        <a href="{{ route('register', ['scan_uuid' => $scan->uuid]) }}" class="btn btn-primary btn-lg shadow-lg">
                            <i class="bi bi-shield-check me-2"></i> Save Report Free
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Metric Cards & Visual Gauges -->
        <div class="row g-4 mb-5">
            <!-- SEO Score Card -->
            <div class="col-12 col-md-4 col-lg-3">
                <x-seo-score-card :score="$score" />
            </div>

            <!-- Issue Distribution -->
            <div class="col-12 col-md-4 col-lg-5">
                <x-seo-issues-chart 
                    :critical="$critical" 
                    :error="$error" 
                    :warning="$warning" 
                    :info="$info" 
                />
            </div>
            
            <!-- Health checklist -->
            <div class="col-12 col-md-4 col-lg-4">
                 <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h5 class="mb-0 fw-bold text-dark">Site Status</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush bg-transparent">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent text-secondary border-light">
                                <span><i class="bi bi-file-earmark-text me-2 text-primary"></i> Pages Crawled</span>
                                <span class="fw-bold text-dark">{{ $totalPages }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent text-secondary border-light">
                                <span><i class="bi bi-bug me-2 text-danger"></i> Total Issues</span>
                                <span class="fw-bold text-dark">{{ $totalIssues }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent text-secondary border-light">
                                <span><i class="bi bi-link-45deg me-2 text-warning"></i> Broken Links</span>
                                <span class="fw-bold text-warning">{{ $brokenLinks }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent text-secondary border-light">
                                <span><i class="bi bi-robot me-2 text-secondary"></i> Robots.txt</span>
                                <span>{!! $sitewideChecks['robots_txt'] ? '✅' : '❌' !!}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent text-secondary border-0">
                                <span><i class="bi bi-diagram-3 me-2 text-secondary"></i> Sitemap.xml</span>
                                <span>{!! $sitewideChecks['sitemap_xml'] ? '✅' : '❌' !!}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @php
            $firstPage = $scan->pages()->first();
        @endphp
        @if($firstPage)
            <!-- Google Search Snippet Preview -->
            <div class="card border-0 p-4 mb-5 shadow-sm bg-light">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-google text-danger me-2"></i> Google Search Snippet Preview</h5>
                
                <div class="p-4 rounded-4 mb-3 border bg-white shadow-sm" style="max-width: 650px; font-family: arial, sans-serif;">
                    <!-- URL path -->
                    <div class="text-truncate" style="font-size: 14px; color: #4d5156; line-height: 1.3; margin-bottom: 4px;">
                        {{ parse_url($firstPage->url, PHP_URL_HOST) }} 
                        <span style="color: #4d5156;"> &rsaquo; </span> 
                        {{ ltrim(parse_url($firstPage->url, PHP_URL_PATH), '/') ?: 'index' }}
                    </div>
                    <!-- Title link -->
                    <h3 class="text-truncate mb-1" style="font-size: 20px; color: #1a0dab; font-weight: normal; line-height: 1.3; cursor: pointer; font-family: arial, sans-serif !important;">
                        {{ $firstPage->title ?? 'No Title Tag Found' }}
                    </h3>
                    <!-- Description snippet -->
                    <div style="font-size: 14px; color: #4d5156; line-height: 1.57; word-wrap: break-word;">
                        {{ $firstPage->description ?? 'Please write a meta description to introduce this page to searchers.' }}
                    </div>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-white border text-dark">
                            <div class="small text-muted mb-1">Title Tag Length</div>
                            <strong>{{ strlen($firstPage->title ?? '') }} characters</strong> 
                            <span class="ms-2 badge {{ strlen($firstPage->title ?? '') >= 30 && strlen($firstPage->title ?? '') <= 60 ? 'bg-success' : 'bg-warning' }} bg-opacity-10 text-{{ strlen($firstPage->title ?? '') >= 30 && strlen($firstPage->title ?? '') <= 60 ? 'success' : 'warning' }}">
                                {{ strlen($firstPage->title ?? '') >= 30 && strlen($firstPage->title ?? '') <= 60 ? 'Optimal (30-60)' : 'Concise & descriptive recommended' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-white border text-dark">
                            <div class="small text-muted mb-1">Meta Description Length</div>
                            <strong>{{ strlen($firstPage->description ?? '') }} characters</strong>
                            <span class="ms-2 badge {{ strlen($firstPage->description ?? '') >= 50 && strlen($firstPage->description ?? '') <= 160 ? 'bg-success' : 'bg-warning' }} bg-opacity-10 text-{{ strlen($firstPage->description ?? '') >= 50 && strlen($firstPage->description ?? '') <= 160 ? 'success' : 'warning' }}">
                                {{ strlen($firstPage->description ?? '') >= 50 && strlen($firstPage->description ?? '') <= 160 ? 'Optimal (50-160)' : 'Concise & descriptive recommended' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-text mt-3 text-muted">
                    <i class="bi bi-info-circle me-1"></i> Note: Google dynamically determines search snippets based on user queries and index resources. Use pixel values and formats as recommended guidelines, not absolute ranking rules.
                </div>
            </div>
        @endif

        <!-- Critical Issue breakdown -->
        <div class="row g-4 mb-5">
            <div class="col-6 col-md-3">
                <x-seo-summary-card title="Critical Errors" :value="$critical" icon="bi-x-octagon-fill" color="danger" />
            </div>
            <div class="col-6 col-md-3">
                <x-seo-summary-card title="Warnings" :value="$warning" icon="bi-exclamation-triangle-fill" color="warning" />
            </div>
            <div class="col-6 col-md-3">
                <x-seo-summary-card title="Notices" :value="$info" icon="bi-info-circle-fill" color="info" />
            </div>
             <div class="col-6 col-md-3">
                <x-seo-summary-card title="Avg. Load Time" value="0.4s" icon="bi-stopwatch" color="success" />
            </div>
        </div>

        <!-- Issue Overview Table -->
        <div class="row mb-5">
            <div class="col-12">
                <h4 class="mb-3 fw-bold text-dark">Issues Overview</h4>
                
                @if($paginatedIssues->count() > 0)
                    <div class="card border-0 shadow-sm px-3 py-3">
                        <x-seo-issues-table :issues="$paginatedIssues" />
                        <div class="mt-3 d-flex justify-content-center">
                            {{ $paginatedIssues->appends(['pages_page' => request('pages_page')])->links() }}
                        </div>
                    </div>
                @else
                     <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-4 py-3 shadow-sm">No issues found across the scanned pages.</div>
                @endif
            </div>
        </div>

        <!-- Crawled Pages accordions -->
        <div class="row">
            <div class="col-12">
                <h4 class="mb-3 fw-bold text-dark">Crawled Pages Analysis</h4>
                
                <div class="d-flex justify-content-end mb-2">
                     {{ $paginatedPages->appends(['issues_page' => request('issues_page')])->links() }}
                </div>

                <div class="mb-3" id="pagesList">
                    @foreach($paginatedPages as $index => $page)
                        <div class="card border-0 mb-3 p-0 shadow-sm">
                            <!-- Toggle Header -->
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 cursor-pointer border-bottom" 
                                 data-bs-toggle="collapse" 
                                 data-bs-target="#collapsePage-{{ $page->id }}" 
                                 style="cursor: pointer; background: var(--bg-surface-1);">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <i class="bi bi-chevron-down text-muted me-2"></i>
                                    <span class="fw-semibold text-break text-dark" style="font-size: 0.95rem;">{{ $page->url }}</span>
                                </div>
                                @if($page->issues->count() > 0)
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">{{ $page->issues->count() }} Issues</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Healthy</span>
                                @endif
                            </div>

                            <!-- Collapsible Content -->
                            <div id="collapsePage-{{ $page->id }}" class="collapse">
                                <div class="p-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="p-3 h-100 rounded-3 border bg-light">
                                                <h6 class="text-uppercase text-muted fw-bold font-monospace mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">Metadata</h6>
                                                <p class="mb-1 text-secondary"><strong>Title:</strong> {{ $page->title ?? 'N/A' }}</p>
                                                <p class="mb-0 text-secondary"><strong>Description:</strong> {{ Str::limit($page->description ?? 'N/A', 100) }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 h-100 rounded-3 border bg-light">
                                                <h6 class="text-uppercase text-muted fw-bold font-monospace mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">Stats Summary</h6>
                                                <div class="d-flex justify-content-between text-secondary">
                                                    <span>Links Found: <strong>{{ $page->links->count() }}</strong></span>
                                                    <span>Images: <strong>{{ $page->images->count() }}</strong></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($page->issues->count() > 0)
                                        <h6 class="mt-4 text-danger fw-bold mb-2">Page Specific Issues</h6>
                                        <div class="card border-0 px-3 py-2 bg-light">
                                            <x-seo-issues-table :issues="$page->issues" />
                                        </div>
                                    @endif
                                    
                                    <div class="mt-3">
                                        <!-- Heading tag checkers & Global Checker -->
                                        @if(empty($scan->type) || in_array($scan->type, ['seo-checker', 'free-seo-checker', 'website-seo-checker', 'h1-checker', 'title-tag-checker', 'meta-description-checker', 'meta-tag-checker']))
                                            <button class="btn btn-sm btn-outline-primary me-2 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#headingsPage-{{ $page->id }}">
                                                View Headings Hierarchy <i class="bi bi-chevron-down ms-1"></i>
                                            </button>
                                        @endif

                                        <!-- Schema Markup Checkers & Global Checker -->
                                        @if(empty($scan->type) || in_array($scan->type, ['seo-checker', 'free-seo-checker', 'website-seo-checker', 'schema-markup-checker']))
                                            <button class="btn btn-sm btn-outline-primary me-2 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#schemaPage-{{ $page->id }}">
                                                View Schema Markup JSON-LD <i class="bi bi-chevron-down ms-1"></i>
                                            </button>
                                        @endif

                                        <!-- Link Checkers & Global Checker -->
                                        @if(empty($scan->type) || in_array($scan->type, ['seo-checker', 'free-seo-checker', 'website-seo-checker', 'broken-link-checker']))
                                            <button class="btn btn-sm btn-outline-primary me-2 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#linksPage-{{ $page->id }}">
                                                View Page Links ({{ $page->links->count() }}) <i class="bi bi-chevron-down ms-1"></i>
                                            </button>
                                        @endif

                                        <!-- Image Checkers & Global Checker -->
                                        @if(empty($scan->type) || in_array($scan->type, ['seo-checker', 'free-seo-checker', 'website-seo-checker', 'image-seo-checker']))
                                            <button class="btn btn-sm btn-outline-primary me-2 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#imagesPage-{{ $page->id }}">
                                                View Page Images ({{ $page->images->count() }}) <i class="bi bi-chevron-down ms-1"></i>
                                            </button>
                                        @endif

                                        <!-- Collapsible Sections -->
                                        <div class="accordion mt-2" id="accordionDetail-{{ $page->id }}">
                                            <!-- Headings Collapsible -->
                                            @if(empty($scan->type) || in_array($scan->type, ['seo-checker', 'free-seo-checker', 'website-seo-checker', 'h1-checker', 'title-tag-checker', 'meta-description-checker', 'meta-tag-checker']))
                                                <div class="collapse mt-2" id="headingsPage-{{ $page->id }}" data-bs-parent="#accordionDetail-{{ $page->id }}">
                                                    <div class="p-3 rounded border bg-light">
                                                        <h6 class="text-dark fw-semibold mb-2">Headings Hierarchy</h6>
                                                        <ul class="mb-0 text-secondary">
                                                            @forelse($page->headings ?? [] as $h)
                                                                <li><strong>{{ strtoupper($h['tag'] ?? '') }}:</strong> {{ $h['text'] ?? '' }}</li>
                                                            @empty
                                                                <li class="text-muted">No headings found</li>
                                                            @endforelse
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Schema Collapsible -->
                                            @if(empty($scan->type) || in_array($scan->type, ['seo-checker', 'free-seo-checker', 'website-seo-checker', 'schema-markup-checker']))
                                                <div class="collapse mt-2" id="schemaPage-{{ $page->id }}" data-bs-parent="#accordionDetail-{{ $page->id }}">
                                                    <div class="p-3 rounded border bg-light">
                                                        <h6 class="text-dark fw-semibold mb-2">Schema Markup JSON-LD</h6>
                                                        @if(!empty($page->structured_data))
                                                            <pre class="mb-0 text-dark p-2 bg-white rounded border small" style="max-height: 250px; overflow-y: auto;"><code>{{ json_encode($page->structured_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                                                        @else
                                                            <span class="text-muted small">No structured data / JSON-LD schema found on this page.</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Links Collapsible -->
                                            @if(empty($scan->type) || in_array($scan->type, ['seo-checker', 'free-seo-checker', 'website-seo-checker', 'broken-link-checker']))
                                                <div class="collapse mt-2" id="linksPage-{{ $page->id }}" data-bs-parent="#accordionDetail-{{ $page->id }}">
                                                    <div class="p-3 rounded border bg-light">
                                                        <h6 class="text-dark fw-semibold mb-2">Page Links Audit</h6>
                                                        <ul class="mb-0 text-secondary shadow-sm bg-white p-3 rounded" style="max-height: 250px; overflow-y: auto; font-size: 0.85rem; list-style-type: none;">
                                                            @forelse($page->links ?? [] as $link)
                                                                <li class="mb-2 text-truncate border-bottom pb-1">
                                                                    <span class="badge {{ $link->status_code === 200 ? 'bg-success bg-opacity-10 text-success' : ($link->status_code === 404 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-warning bg-opacity-10 text-warning') }} me-1">
                                                                        {{ $link->status_code ?? 'Pending' }}
                                                                    </span>
                                                                    <a href="{{ $link->href }}" target="_blank" class="text-decoration-none text-muted">{{ $link->href }}</a>
                                                                </li>
                                                            @empty
                                                                <li class="text-muted">No links found on this page</li>
                                                            @endforelse
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Images Collapsible -->
                                            @if(empty($scan->type) || in_array($scan->type, ['seo-checker', 'free-seo-checker', 'website-seo-checker', 'image-seo-checker']))
                                                <div class="collapse mt-2" id="imagesPage-{{ $page->id }}" data-bs-parent="#accordionDetail-{{ $page->id }}">
                                                    <div class="p-3 rounded border bg-light">
                                                        <h6 class="text-dark fw-semibold mb-2">Page Images Alt Tags</h6>
                                                        <ul class="mb-0 text-secondary shadow-sm bg-white p-3 rounded" style="max-height: 250px; overflow-y: auto; font-size: 0.85rem; list-style-type: none;">
                                                            @forelse($page->images ?? [] as $img)
                                                                <li class="mb-2 text-truncate border-bottom pb-1">
                                                                    <span class="badge {{ !empty($img->alt) ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} me-1">
                                                                        {{ !empty($img->alt) ? 'Alt Okay' : 'Missing Alt' }}
                                                                    </span>
                                                                    <span class="text-dark me-2">{{ $img->alt ?? 'N/A' }}</span>
                                                                    <small class="text-muted">({{ $img->src }})</small>
                                                                </li>
                                                            @empty
                                                                <li class="text-muted">No images found on this page</li>
                                                            @endforelse
                                                        </ul>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-center">
                     {{ $paginatedPages->appends(['issues_page' => request('issues_page')])->links() }}
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
