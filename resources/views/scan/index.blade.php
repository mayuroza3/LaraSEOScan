@extends('layouts.app')

@section('title', 'Start New Audit - LaraSEOScan')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="text-center mb-4">
                <i class="bi bi-search fs-1 text-primary"></i>
                <h2 class="fw-bold mt-2">Start New Technical Audit</h2>
                <p class="text-muted">Enter a website URL to begin a comprehensive technical SEO analysis.</p>
            </div>

            @if(session('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-lg">
                <div class="card-body p-4 p-md-5">
                    <form method="POST" action="{{ route('scan.submit') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="url" class="form-label fw-bold text-uppercase text-muted" style="font-size: 0.8rem;">Target Website</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-globe2"></i></span>
                                <input type="url" class="form-control border-start-0 ps-0" name="url" id="url" placeholder="https://example.com" required>
                            </div>
                            <div class="form-text mt-2">
                                <i class="bi bi-info-circle me-1"></i> We will crawl up to 200 pages.
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                <i class="bi bi-rocket-takeoff-fill me-2"></i> Launch Audit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-4 text-muted">
                <small>
                    <a href="{{ route('scan.history') }}" class="text-decoration-none text-muted">
                        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                    </a>
                </small>
            </div>
        </div>
    </div>
</div>
@endsection
