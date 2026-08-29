@extends('layouts.app')

@section('title', 'Projects Dashboard - LaraSEOScan')

@section('content')
<div class="container-fluid px-0 py-2">
    <!-- Header with Action -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
        <div>
            <h2 class="fw-bold mb-1 text-dark">Dashboard</h2>
            <p class="text-muted mb-0">Monitor site status, health logs, and technical SEO issues.</p>
        </div>
        <div>
            <a href="{{ route('scan.create') }}" class="btn btn-primary d-inline-flex align-items-center shadow-lg px-4 py-2">
                <i class="bi bi-plus-lg me-2 fw-bold"></i> New Audit Project
            </a>
        </div>
    </div>

    <!-- Modern Metric Cards Row -->
    <div class="row g-4 mb-5">
        <div class="col-12 col-md-4">
             <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.05) 0%, rgba(13, 110, 253, 0.01) 100%) !important; border: 1px solid rgba(13, 110, 253, 0.1) !important;">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="rounded-3 p-3 me-3" style="background: rgba(13, 110, 253, 0.1); border: 1px solid rgba(13, 110, 253, 0.2);">
                        <i class="bi bi-folder-fill fs-3 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Total Projects</h6>
                        <h3 class="fw-bold mb-0 text-dark">{{ $scanStats['total'] ?? $scans->total() }}</h3>
                    </div>
                </div>
             </div>
        </div>
        <div class="col-12 col-md-4">
             <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, rgba(25, 135, 84, 0.05) 0%, rgba(25, 135, 84, 0.01) 100%) !important; border: 1px solid rgba(25, 135, 84, 0.1) !important;">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="rounded-3 p-3 me-3" style="background: rgba(25, 135, 84, 0.1); border: 1px solid rgba(25, 135, 84, 0.2);">
                        <i class="bi bi-shield-fill-check fs-3 text-success"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Completed Audits</h6>
                        <h3 class="fw-bold mb-0 text-dark">{{ $scanStats['completed'] ?? '-' }}</h3> 
                    </div>
                </div>
             </div>
        </div>
        <div class="col-12 col-md-4">
             <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.05) 0%, rgba(255, 193, 7, 0.01) 100%) !important; border: 1px solid rgba(255, 193, 7, 0.1) !important;">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="rounded-3 p-3 me-3" style="background: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.2);">
                        <i class="bi bi-arrow-repeat fs-3 text-warning"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">In Process</h6>
                        <h3 class="fw-bold mb-0 text-dark">{{ $scanStats['pending'] ?? '-' }}</h3>
                    </div>
                </div>
             </div>
        </div>
    </div>

    <!-- Projects Table Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-0 py-3 px-4">
            <h5 class="fw-bold mb-0 text-dark">Recent Audits</h5>
        </div>
        <div class="card-body p-0">
            @if ($scans->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3 text-muted">
                        <i class="bi bi-search fs-1 opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark">No audits found</h5>
                    <p class="text-muted mb-4">Start your first SEO analysis to get insights.</p>
                    <a href="{{ route('scan.create') }}" class="btn btn-outline-primary">
                        Start First Audit
                    </a>
                </div>
            @else
                <div class="table-responsive px-4 pb-3">
                    <table class="table align-middle mb-0">
                        @php
                            $typeNames = [
                                'meta-tag-checker' => 'Meta Tag Audit',
                                'meta-description-checker' => 'Meta Description Audit',
                                'title-tag-checker' => 'Title Tag Audit',
                                'h1-checker' => 'H1 Header Audit',
                                'broken-link-checker' => 'Broken Link Audit',
                                'robots-txt-checker' => 'Robots.txt Crawl',
                                'sitemap-checker' => 'Sitemap XML Audit',
                                'schema-markup-checker' => 'Schema Markup Audit',
                                'open-graph-checker' => 'Open Graph Audit',
                                'image-seo-checker' => 'Image Optimization Audit',
                                'seo-checker' => 'Full Website Audit',
                                'website-seo-checker' => 'Full Website Audit',
                            ];
                        @endphp
                        <thead class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                            <tr>
                                <th class="py-3">Domain / Address</th>
                                <th class="py-3">Scan Type</th>
                                <th class="py-3">Crawler Status</th>
                                <th class="py-3">Audit Date</th>
                                <th class="text-end py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($scans as $scan)
                                <tr data-scan-uuid="{{ $scan->uuid }}" data-scan-status="{{ $scan->status }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light border icon-box-md me-3 d-flex align-items-center justify-content-center overflow-hidden" style="border-color: var(--border-default) !important; width: 40px; height: 40px; border-radius: 8px;">
                                                @php
                                                    $domain = parse_url($scan->url, PHP_URL_HOST) ?? $scan->url;
                                                @endphp
                                                <img src="https://www.google.com/s2/favicons?domain={{ $domain }}&sz=32" 
                                                     alt="favicon" 
                                                     class="img-fluid" 
                                                     style="width: 20px; height: 20px;"
                                                     onerror="this.onerror=null; this.outerHTML='<i class=&quot;bi bi-globe2 text-muted fs-5&quot;></i>';">
                                            </div>
                                            <div>
                                                <a href="{{ route('scan.results', $scan->uuid) }}" class="fw-bold text-dark text-decoration-none text-primary-hover">
                                                    {{ $domain }}
                                                </a>
                                                <small class="d-block text-muted">{{ Str::limit($scan->url, 45) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-3 py-1 font-monospace" style="font-size: 0.7rem;">
                                            {{ $typeNames[$scan->type] ?? 'Full Website Audit' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($scan->status === 'COMPLETED')
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                                                <i class="bi bi-check-circle-fill me-1"></i> Completed
                                            </span>
                                        @elseif($scan->status === 'FAILED')
                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">
                                                <i class="bi bi-x-circle-fill me-1"></i> Failed
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">
                                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="width: 0.8rem; height: 0.8rem;"></span>
                                                Crawl Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-muted">
                                        {{ $scan->created_at->diffForHumans() }}
                                    </td>
                                    <td class="text-end" style="position: relative; z-index: 10;">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical fs-5"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="z-index: 1055;">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('scan.results', $scan->uuid) }}">
                                                        <i class="bi bi-bar-chart me-2"></i> View Report
                                                    </a>    
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('scan.export.pdf', $scan->uuid) }}">
                                                        <i class="bi bi-file-pdf me-2"></i> Export PDF
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('scan.export.csv', $scan->uuid) }}">
                                                        <i class="bi bi-file-spreadsheet me-2"></i> Export CSV
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('scan.delete', $scan->uuid) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this audit?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash me-2"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-top d-flex justify-content-center" style="border-color: var(--border-default) !important;">
                    {{ $scans->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Collect rows representing pending or queued scan operations
        const pendingScans = document.querySelectorAll('tr[data-scan-status="QUEUED"], tr[data-scan-status="PENDING"], tr[data-scan-status="PROCESSING"]');
        
        if (pendingScans.length > 0) {
            // Poll status checker endpoint every 4 seconds
            const interval = setInterval(function() {
                let statusChecks = [];
                
                pendingScans.forEach(row => {
                    const uuid = row.getAttribute('data-scan-uuid');
                    const fetchCheck = fetch(`/results/${uuid}/status-check`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'COMPLETED' || data.status === 'FAILED') {
                                return true;
                            }
                            return false;
                        })
                        .catch(() => false);
                    statusChecks.push(fetchCheck);
                });
                
                Promise.all(statusChecks).then(results => {
                    // Reload index table ONLY if one of the background scan jobs has finished
                    if (results.includes(true)) {
                        clearInterval(interval);
                        window.location.reload();
                    }
                });
            }, 4000);
        }
    });
</script>
@endsection
