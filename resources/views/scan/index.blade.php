@extends('layouts.app')

@section('title', 'LaraSEOScan - SEO Analyzer')

@section('content')
<div class="container py-5">
    <div class="row">

        @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">SEO Scan</h5>
                </div>
                <div class="card-body">
                    {{-- Your existing form / table / content --}}
                    <form method="POST" action="{{ route('scan.submit') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="url" class="form-label">Website URL</label>
                            <input type="text" class="form-control" name="url" id="url" placeholder="https://example.com">
                        </div>
                        <button type="submit" class="btn btn-success">Start Scan</button>
                    </form>

                    {{-- Add results table if applicable --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
