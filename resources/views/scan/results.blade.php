@extends('layouts.app')

@section('title', 'SEO Scan Results - ' . parse_url($scan->url, PHP_URL_HOST))

@section('content')
    @php
        
        $totalPages = $scan->pages()->count(); // Count query
        
        // Aggregate totals using database queries instead of loading all models
        $totalIssues = \App\Models\SeoIssue::whereHas('page', function($q) use ($scan) {
                $q->where('seo_scan_id', $scan->id);
            })->count();
        $critical = \App\Models\SeoIssue::whereHas('page', function($q) use ($scan) {
                $q->where('seo_scan_id', $scan->id);
            })->where('severity', 'critical')->count();
        $error = \App\Models\SeoIssue::whereHas('page', function($q) use ($scan) {
                $q->where('seo_scan_id', $scan->id);
            })->where('severity', 'error')->count();
        $warning = \App\Models\SeoIssue::whereHas('page', function($q) use ($scan) {
                $q->where('seo_scan_id', $scan->id);
            })->where('severity', 'warning')->count();
        $info = \App\Models\SeoIssue::whereHas('page', function($q) use ($scan) {
                $q->where('seo_scan_id', $scan->id);
            })->where('severity', 'info')->count();
        
        // Broken links (rough estimate via issues)
        $brokenLinks = \App\Models\SeoIssue::whereHas('page', function($q) use ($scan) {
                $q->where('seo_scan_id', $scan->id);
            })
            ->where('rule_key', 'like', '%link%')
            ->whereIn('severity', ['error', 'critical'])
            ->count();

        // Calculate Score
        $deduction = ($critical * 10) + ($error * 5) + ($warning * 2);
        
        if ($totalPages > 0) {
             $issuesPerPage = $totalIssues / $totalPages;
             $score = max(0, 100 - ($issuesPerPage * 5));
             $score = round($score);
        } else {
            $score = 0;
        }
    @endphp

    <div class="container-fluid px-4 py-4">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">SEO Audit Report</h2>
                <p class="text-muted mb-0">
                    Target: <a href="{{ $scan->url }}" target="_blank" class="text-decoration-none">{{ $scan->url }}</a>
                    <span class="mx-2">•</span>
                    {{ $scan->created_at->format('M d, Y h:i A') }}
                </p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('scan.export.pdf', ['uuid' => $scan->uuid]) }}" class="btn btn-outline-danger me-2">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
                <a href="{{ route('scan.export.csv', ['uuid' => $scan->uuid]) }}" class="btn btn-outline-success">
                    <i class="bi bi-file-earmark-excel"></i> Export CSV
                </a>
            </div>
        </div>

        <!-- Section 1 & 3: Score & Charts -->
        <div class="row g-4 mb-4">
            <!-- SEO Score -->
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
            
            <!-- Quick Summary Panel -->
            <div class="col-12 col-md-4 col-lg-4">
                 <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <h5 class="mb-0 fw-bold">Site Health</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span><i class="bi bi-file-earmark-text me-2 text-primary"></i> Pages Crawled</span>
                                <span class="fw-bold">{{ $totalPages }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span><i class="bi bi-bug me-2 text-danger"></i> Total Issues</span>
                                <span class="fw-bold">{{ $totalIssues }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span><i class="bi bi-link-45deg me-2 text-warning"></i> Broken Links</span>
                                <span class="fw-bold">{{ $brokenLinks }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span><i class="bi bi-robot me-2 text-secondary"></i> Robots.txt</span>
                                <span>{!! $sitewideChecks['robots_txt'] ? '✅' : '❌' !!}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span><i class="bi bi-diagram-3 me-2 text-secondary"></i> Sitemap.xml</span>
                                <span>{!! $sitewideChecks['sitemap_xml'] ? '✅' : '❌' !!}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Summary Cards -->
        <div class="row g-4 mb-4">
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

        <!-- Section 4: Issues Overview (Paginated) -->
        <div class="row mb-5">
            <div class="col-12">
                <h4 class="mb-3 fw-bold">Issues Overview</h4>
                
                @if($paginatedIssues->count() > 0)
                    <x-seo-issues-table :issues="$paginatedIssues" />
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $paginatedIssues->appends(['pages_page' => request('pages_page')])->links() }}
                    </div>
                @else
                     <div class="alert alert-success">No issues found across the scanned pages.</div>
                @endif
            </div>
        </div>

        <!-- Page Details Accordion (Paginated) -->
        <div class="row">
            <div class="col-12">
                <h4 class="mb-3 fw-bold">Crawled Pages Analysis</h4>
                
                <div class="d-flex justify-content-end mb-2">
                     {{ $paginatedPages->appends(['issues_page' => request('issues_page')])->links() }}
                </div>

                <div class="accordion shadow-sm mb-3" id="accordionPages">
                    @foreach($paginatedPages as $index => $page)
                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header" id="heading{{ $page->id }}">
                                <button class="accordion-button collapsed bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $page->id }}" aria-expanded="false" aria-controls="collapse{{ $page->id }}">
                                    <div class="d-flex align-items-center w-100 flex-wrap">
                                        <span class="me-3 fw-medium text-break">{{ $page->url }}</span>
                                        @if($page->issues->count() > 0)
                                            <span class="badge bg-danger rounded-pill ms-auto me-3">{{ $page->issues->count() }} Issues</span>
                                        @else
                                            <span class="badge bg-success rounded-pill ms-auto me-3">Healthy</span>
                                        @endif
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse{{ $page->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $page->id }}" data-bs-parent="#accordionPages">
                                <div class="accordion-body bg-light">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card p-3 h-100 border-0 shadow-sm">
                                                <h6 class="text-uppercase text-muted fw-bold font-monospace" style="font-size: 0.75rem;">Metadata</h6>
                                                <p class="mb-1"><strong>Title:</strong> {{ $page->title ?? 'N/A' }}</p>
                                                <p class="mb-0"><strong>Desc:</strong> {{ Str::limit($page->description ?? 'N/A', 100) }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card p-3 h-100 border-0 shadow-sm">
                                                <h6 class="text-uppercase text-muted fw-bold font-monospace" style="font-size: 0.75rem;">Stats</h6>
                                                <div class="d-flex justify-content-between">
                                                    <span>Links Found: {{ $page->links->count() }}</span>
                                                    <span>Images: {{ $page->images->count() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($page->issues->count() > 0)
                                        <h6 class="mt-3 text-danger fw-bold">Page Issues</h6>
                                        <x-seo-issues-table :issues="$page->issues" />
                                    @endif
                                    
                                    <div class="mt-3">
                                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#details-{{ $page->id }}">
                                            View Raw Details <i class="bi bi-chevron-down"></i>
                                        </button>
                                        <div class="collapse mt-2" id="details-{{ $page->id }}">
                                            <div class="card card-body border-0 shadow-sm">
                                                <h6>Headings</h6>
                                                <ul>
                                                    @foreach($page->headings ?? [] as $h)
                                                        <li><strong>{{ $h['tag'] }}:</strong> {{ $h['text'] }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
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
@endsection
