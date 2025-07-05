@extends('layouts.app')

@section('title', 'LaraSEOScan - SEO Analyzer')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">🔍 LaraSEOScan</h2>
    <p>Enter a website URL below to perform an on-page SEO analysis.</p>

    <form action="{{ route('scan.submit') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="url" class="form-label">Website URL</label>
            <input type="url" class="form-control" name="url" id="url" placeholder="https://example.com" required>
        </div>
        <button type="submit" class="btn btn-primary">Start Scan</button>
    </form>
</div>
@endsection
