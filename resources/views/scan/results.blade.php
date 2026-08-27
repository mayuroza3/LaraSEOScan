@extends('layouts.app')

@section('title', 'Audit Report - ' . parse_url($scan->url, PHP_URL_HOST))

@section('content')
    @php
        $totalPages = $scan->pages()->count(); 
        
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
        
        $brokenLinks = \App\Models\SeoIssue::whereHas('page', function($q) use ($scan) {
                $q->where('seo_scan_id', $scan->id);
            })
            ->where('rule_key', 'like', '%link%')
            ->whereIn('severity', ['error', 'critical'])
            ->count();

        // Calculate Score
        if ($totalPages > 0) {
             $issuesPerPage = $totalIssues / $totalPages;
             $score = max(0, 100 - ($issuesPerPage * 5));
             $score = round($score);
        } else {
             $score = 0;
        }
    @endphp

    <div class="container-fluid px-0 py-2">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
            <div>
                <h2 class="fw-bold mb-1 text-white">SEO Audit Report</h2>
                <p class="text-muted mb-0">
                    Target: <a href="{{ $scan->url }}" target="_blank" class="text-decoration-none text-primary fw-medium">{{ $scan->url }}</a>
                    <span class="mx-2 text-secondary">•</span>
                    {{ $scan->created_at->format('M d, Y h:i A') }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('scan.export.pdf', ['uuid' => $scan->uuid]) }}" class="btn btn-outline-danger d-inline-flex align-items-center">
                    <i class="bi bi-file-earmark-pdf me-2"></i> Export PDF
                </a>
                <a href="{{ route('scan.export.csv', ['uuid' => $scan->uuid]) }}" class="btn btn-outline-success d-inline-flex align-items-center">
                    <i class="bi bi-file-earmark-excel me-2"></i> Export CSV
                </a>
            </div>
        </div>

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
                        <h5 class="mb-0 fw-bold text-white">Site Status</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush bg-transparent">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent text-white border-secondary border-opacity-10">
                                <span><i class="bi bi-file-earmark-text me-2 text-primary"></i> Pages Crawled</span>
                                <span class="fw-bold">{{ $totalPages }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent text-white border-secondary border-opacity-10">
                                <span><i class="bi bi-bug me-2 text-danger"></i> Total Issues</span>
                                <span class="fw-bold">{{ $totalIssues }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent text-white border-secondary border-opacity-10">
                                <span><i class="bi bi-link-45deg me-2 text-warning"></i> Broken Links</span>
                                <span class="fw-bold text-warning">{{ $brokenLinks }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent text-white border-secondary border-opacity-10">
                                <span><i class="bi bi-robot me-2 text-secondary"></i> Robots.txt</span>
                                <span>{!! $sitewideChecks['robots_txt'] ? '✅' : '❌' !!}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent text-white border-0">
                                <span><i class="bi bi-diagram-3 me-2 text-secondary"></i> Sitemap.xml</span>
                                <span>{!! $sitewideChecks['sitemap_xml'] ? '✅' : '❌' !!}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

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
                <h4 class="mb-3 fw-bold text-white">Issues Overview</h4>
                
                @if($paginatedIssues->count() > 0)
                    <div class="card border-0 shadow px-3 py-3">
                        <x-seo-issues-table :issues="$paginatedIssues" />
                        <div class="mt-3 d-flex justify-content-center">
                            {{ $paginatedIssues->appends(['pages_page' => request('pages_page')])->links() }}
                        </div>
                    </div>
                @else
                     <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-4 py-3 shadow">No issues found across the scanned pages.</div>
                @endif
            </div>
        </div>

        <!-- Crawled Pages accordions -->
        <div class="row">
            <div class="col-12">
                <h4 class="mb-3 fw-bold text-white">Crawled Pages Analysis</h4>
                
                <div class="d-flex justify-content-end mb-2">
                     {{ $paginatedPages->appends(['issues_page' => request('issues_page')])->links() }}
                </div>

                <div class="mb-3" id="pagesList">
                    @foreach($paginatedPages as $index => $page)
                        <div class="card border-0 mb-3 bg-secondary bg-opacity-5 p-0">
                            <!-- Toggle Header -->
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 cursor-pointer border border-secondary border-opacity-10" 
                                 data-bs-toggle="collapse" 
                                 data-bs-target="#collapsePage-{{ $page->id }}" 
                                 style="cursor: pointer; background: rgba(30, 41, 59, 0.45);">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <i class="bi bi-chevron-down text-muted me-2"></i>
                                    <span class="fw-semibold text-break text-white" style="font-size: 0.95rem;">{{ $page->url }}</span>
                                </div>
                                @if($page->issues->count() > 0)
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">{{ $page->issues->count() }} Issues</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Healthy</span>
                                @endif
                            </div>

                            <!-- Collapsible Content -->
                            <div id="collapsePage-{{ $page->id }}" class="collapse">
                                <div class="p-4 border-top border-secondary border-opacity-10">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="card p-3 h-100 border-0 bg-secondary bg-opacity-10">
                                                <h6 class="text-uppercase text-muted fw-bold font-monospace mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">Metadata</h6>
                                                <p class="mb-1 text-white"><strong>Title:</strong> {{ $page->title ?? 'N/A' }}</p>
                                                <p class="mb-0 text-white"><strong>Description:</strong> {{ Str::limit($page->description ?? 'N/A', 100) }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card p-3 h-100 border-0 bg-secondary bg-opacity-10">
                                                <h6 class="text-uppercase text-muted fw-bold font-monospace mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">Stats Summary</h6>
                                                <div class="d-flex justify-content-between text-white">
                                                    <span>Links Found: <strong>{{ $page->links->count() }}</strong></span>
                                                    <span>Images: <strong>{{ $page->images->count() }}</strong></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($page->issues->count() > 0)
                                        <h6 class="mt-4 text-danger fw-bold mb-2">Page Specific Issues</h6>
                                        <div class="card border-0 px-3 py-2 bg-secondary bg-opacity-10">
                                            <x-seo-issues-table :issues="$page->issues" />
                                        </div>
                                    @endif
                                    
                                    <div class="mt-3">
                                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#detailsPage-{{ $page->id }}">
                                            View Raw Headings <i class="bi bi-chevron-down ms-1"></i>
                                        </button>
                                        <div class="collapse mt-2" id="detailsPage-{{ $page->id }}">
                                            <div class="card card-body border-0 bg-secondary bg-opacity-15 p-3">
                                                <h6 class="text-white fw-semibold mb-2">Headings Hierarchy</h6>
                                                <ul class="mb-0 text-white">
                                                    @forelse($page->headings ?? [] as $h)
                                                        <li><strong>{{ strtoupper($h['tag']) }}:</strong> {{ $h['text'] }}</li>
                                                    @empty
                                                        <li class="text-muted">No headings found</li>
                                                    @endforelse
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
