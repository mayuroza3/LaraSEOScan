@extends('layouts.app')

@section('title', 'Projects Dashboard - LaraSEOScan')

@section('content')
<div class="container-fluid px-0 py-2">
    <!-- Header with Action -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
        <div>
            <h2 class="fw-bold mb-1 text-white">Dashboard</h2>
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
             <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(30, 41, 59, 0.4) 100%) !important;">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="rounded-3 p-3 me-3" style="background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.25);">
                        <i class="bi bi-folder-fill fs-3 text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Total Projects</h6>
                        <h3 class="fw-bold mb-0 text-white">{{ $scanStats['total'] ?? $scans->total() }}</h3>
                    </div>
                </div>
             </div>
        </div>
        <div class="col-12 col-md-4">
             <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(30, 41, 59, 0.4) 100%) !important;">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="rounded-3 p-3 me-3" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.25);">
                        <i class="bi bi-shield-fill-check fs-3 text-success"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Completed Audits</h6>
                        <h3 class="fw-bold mb-0 text-white">{{ $scanStats['completed'] ?? '-' }}</h3> 
                    </div>
                </div>
             </div>
        </div>
        <div class="col-12 col-md-4">
             <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(30, 41, 59, 0.4) 100%) !important;">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="rounded-3 p-3 me-3" style="background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.25);">
                        <i class="bi bi-arrow-repeat fs-3 text-warning"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">In Process</h6>
                        <h3 class="fw-bold mb-0 text-white">{{ $scanStats['pending'] ?? '-' }}</h3>
                    </div>
                </div>
             </div>
        </div>
    </div>

    <!-- Projects Table Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-0 py-3 px-4">
            <h5 class="fw-bold mb-0 text-white">Recent Audits</h5>
        </div>
        <div class="card-body p-0">
            @if ($scans->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3 text-muted">
                        <i class="bi bi-search fs-1 opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-white">No audits found</h5>
                    <p class="text-muted mb-4">Start your first SEO analysis to get insights.</p>
                    <a href="{{ route('scan.create') }}" class="btn btn-outline-primary">
                        Start First Audit
                    </a>
                </div>
            @else
                <div class="table-responsive px-4 pb-3">
                    <table class="table align-middle mb-0">
                        <thead class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                            <tr>
                                <th class="py-3">Domain / Address</th>
                                <th class="py-3">Crawler Status</th>
                                <th class="py-3">Audit Date</th>
                                <th class="text-end py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($scans as $scan)
                                <tr class="position-relative">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-secondary bg-opacity-15 border border-secondary border-opacity-10 icon-box-md me-3">
                                                <i class="bi bi-globe2 text-muted fs-5"></i>
                                            </div>
                                            <div>
                                                <a href="{{ route('scan.results', $scan->uuid) }}" class="fw-bold text-white text-decoration-none stretched-link">
                                                    {{ parse_url($scan->url, PHP_URL_HOST) ?? $scan->url }}
                                                </a>
                                                <small class="d-block text-muted">{{ Str::limit($scan->url, 45) }}</small>
                                            </div>
                                        </div>
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
                                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow border-0" style="z-index: 1055;">
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
                                                <li><hr class="dropdown-divider bg-secondary"></li>
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
                <div class="px-4 py-3 border-top border-secondary border-opacity-10 d-flex justify-content-center">
                    {{ $scans->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Auto-refresh every 20 seconds for processing scans
    setTimeout(function() {
        window.location.reload();
    }, 20000);
</script>
@endsection
