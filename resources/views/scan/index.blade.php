@extends('layouts.app')

@section('title', 'LaraSEOScan - SEO Analyzer')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">🔍 LaraSEOScan</h2>
    <p>Enter a website URL below to perform an on-page SEO analysis.</p>

    @if ($errors->has('limit'))
        <div class="alert alert-danger mt-3">
            {{ $errors->first('limit') }}
        </div>
    @endif
    <form action="{{ route('scan.submit') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="url" class="form-label">Website URL</label>
            <input type="url" class="form-control" name="url" id="url" placeholder="https://example.com" required>
        </div>
        <button type="submit" class="btn btn-primary">Start Scan</button>
        <p class="text-muted">You have {{ max(0, 5 - Auth::user()->seoScans()->withTrashed()->whereDate('created_at', now())->count()) }} scans left today.</p>
    </form>
</div>
@endsection
