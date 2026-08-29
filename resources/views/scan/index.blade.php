@extends('layouts.app')

@section('title', 'Launch Audit - LaraSEOScan')

@section('content')
<div class="row justify-content-center align-items-center py-5">
    <div class="col-md-9 col-lg-7 col-xl-6">
        
        <!-- Welcome/Brand Header -->
        <div class="text-center mb-5">
            <div class="bg-primary bg-opacity-10 border border-primary border-opacity-25 shadow-lg icon-box-lg mb-3 mx-auto">
                <i class="bi bi-rocket-takeoff-fill fs-1 text-primary"></i>
            </div>
            <h2 class="fw-bold mt-2 text-dark">Start Site Audit</h2>
            <p class="text-muted">Enter any public domain or page URL below to launch a parallel technical SEO crawler.</p>
        </div>

        @if(session('message'))
            <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success alert-dismissible fade show rounded-4 py-3 mb-4 shadow" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i> {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger alert-dismissible fade show rounded-4 py-3 mb-4 shadow" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Neon Glass Card Audit Form -->
        <div class="card border-0 shadow-lg" style="background: var(--bg-surface-1) !important;">
            <div class="card-body p-4 p-md-5">
                <form method="POST" action="{{ route('scan.submit') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="url" class="form-label fw-bold text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.05em;">Target Domain / URL</label>
                        <div class="input-group border rounded-3 overflow-hidden shadow-inner p-1 bg-white" style="border-color: var(--border-default) !important;">
                            <span class="input-group-text bg-transparent border-0 text-muted px-3"><i class="bi bi-globe2"></i></span>
                            <input type="url" class="form-control bg-transparent border-0 text-dark ps-2" name="url" id="url" placeholder="https://example.com" required style="outline: none; box-shadow: none;">
                        </div>
                        <div class="form-text mt-3 text-muted d-flex align-items-center">
                            <i class="bi bi-shield-check text-primary me-2 fs-5"></i> 
                            <span>Self-hosted private scanning. Crawling limit set to 200 pages.</span>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg shadow-lg d-flex align-items-center justify-content-center py-3">
                            <i class="bi bi-play-circle-fill me-2 fs-5"></i> Launch Audit Engine
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('scan.history') }}" class="text-decoration-none text-muted small hover-primary">
                <i class="bi bi-arrow-left me-1"></i> Return to Dashboard
            </a>
        </div>

    </div>
</div>
@endsection
