@extends('layouts.app')

@section('title', 'Dashboard - LaraSEOScan')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header with Action -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold mb-1">Projects Dashboard</h2>
            <p class="text-muted">Manage your SEO audits and reports</p>
        </div>
        <div>
            <a href="{{ route('scan.create') }}" class="btn btn-primary d-inline-flex align-items-center shadow-sm px-4 py-2">
                <i class="bi bi-plus-lg me-2"></i> New Audit
            </a>
        </div>
    </div>

    <!-- Stats Row (Placeholder/Simple counts) -->
    <div class="row g-4 mb-5">
        <div class="col-12 col-md-4">
             <div class="card border-0 shadow-sm h-100 bg-primary bg-opacity-10">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle p-3 me-3">
                        <i class="bi bi-hdd-stack fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Total Audits</h6>
                        <h3 class="fw-bold mb-0">{{ $scanStats['total'] ?? $scans->total() }}</h3>
                    </div>
                </div>
             </div>
        </div>
        <div class="col-12 col-md-4">
             <div class="card border-0 shadow-sm h-100 bg-success bg-opacity-10">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle p-3 me-3">
                        <i class="bi bi-check-lg fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Completed</h6>
                        <h3 class="fw-bold mb-0">{{ $scanStats['completed'] ?? '-' }}</h3> 
                    </div>
                </div>
             </div>
        </div>
        <div class="col-12 col-md-4">
             <div class="card border-0 shadow-sm h-100 bg-warning bg-opacity-10">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-warning text-white rounded-circle p-3 me-3">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Pending/Processing</h6>
                        <h3 class="fw-bold mb-0">{{ $scanStats['pending'] ?? '-' }}</h3>
                    </div>
                </div>
             </div>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom-0 py-3">
            <h5 class="fw-bold mb-0">Recent Scans</h5>
        </div>
        <div class="card-body p-0">
            @if ($scans->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-search fs-1 text-muted opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-muted">No audits found</h5>
                    <p class="text-muted mb-4">Start your first SEO analysis to get insights.</p>
                    <a href="{{ route('scan.create') }}" class="btn btn-outline-primary">
                        Start First Audit
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">
                            <tr>
                                <th class="ps-4 py-3">Project / URL</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Date</th>
                                <th class="text-end pe-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($scans as $scan)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3 border">
                                                <i class="bi bi-globe2 text-secondary"></i>
                                            </div>
                                            <div>
                                            <div>
                                                <a href="{{ route('scan.results', $scan->uuid) }}" class="fw-bold text-dark text-decoration-none stretched-link">
                                                    {{ parse_url($scan->url, PHP_URL_HOST) ?? $scan->url }}
                                                </a>
                                                <small class="d-block text-muted">{{ Str::limit($scan->url, 40) }}</small>
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
                                                Processing
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-muted">
                                        {{ $scan->created_at->diffForHumans() }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown" style="position: relative; z-index: 10;">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="z-index: 1055;">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('scan.results', $scan->uuid) }}">
                                                        <i class="bi bi-bar-chart me-2 opacity-50"></i> View Report
                                                    </a>    
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('scan.export.pdf', $scan->uuid) }}">
                                                        <i class="bi bi-file-pdf me-2 opacity-50"></i> Export PDF
                                                    </a>
                                                </li>
                                                 <li>
                                                    <a class="dropdown-item" href="{{ route('scan.export.csv', $scan->uuid) }}">
                                                        <i class="bi bi-file-spreadsheet me-2 opacity-50"></i> Export CSV
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('scan.delete', $scan->uuid) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this audit?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash me-2 opacity-50"></i> Delete
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
                <div class="px-4 py-3 border-top">
                    {{ $scans->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Auto-refresh every 20 seconds
    setTimeout(function() {
        window.location.reload();
    }, 120000);
</script>
@endsection
