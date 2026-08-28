@extends('layouts.app')

@section('title', 'Free SEO Audit Tools Directory | LaraSEOScan')

@section('content')
@push('meta')
    <meta name="description" content="Discover LaraSEOScan's directory of 100% free SEO audit tools. Check meta tags, page titles, H1 structures, canonicals, sitemaps, and robots.txt rules.">
    <meta property="og:title" content="Free SEO Audit Tools Directory | LaraSEOScan">
    <meta property="og:description" content="Discover LaraSEOScan's directory of 100% free SEO audit tools. Check meta tags, page titles, H1 structures, canonicals, sitemaps, and robots.txt rules.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
@endpush

<div class="container py-5">
    
    <!-- Welcome Header -->
    <div class="text-center mb-5">
        <h1 class="fw-bold mt-2 text-white">Free SEO Tools Hub</h1>
        <p class="text-muted fs-5 mx-auto" style="max-width: 600px;">Optimize your site's search presence with our specialized technical analyzers. No registration, no installation — just paste your link and check.</p>
    </div>

    <!-- Tools Grid -->
    <div class="row g-4">
        @foreach($toolsList as $key => $tool)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 h-100 p-4 bg-secondary bg-opacity-5">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 border border-primary border-opacity-10 rounded-3 icon-box-md me-3">
                            <i class="bi {{ $tool['icon'] }} fs-4 text-primary"></i>
                        </div>
                        <h4 class="text-white fw-bold mb-0" style="font-size: 1.15rem;">{{ $tool['header'] }}</h4>
                    </div>
                    <p class="text-muted small mb-4" style="flex-grow: 1;">{{ $tool['meta_description'] }}</p>
                    <div class="d-grid">
                        <a href="{{ route('landing.' . $key) }}" class="btn btn-outline-primary btn-sm rounded-pill py-2">
                            Launch Tool <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
