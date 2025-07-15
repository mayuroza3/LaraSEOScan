@extends('layouts.app')

@section('title', 'Scan History')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">📊 Scan History</h4>
            <a href="{{ route('scan.form') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> New Scan
            </a>
        </div>

        @if ($scans->isEmpty())
            <div class="alert alert-info">
                <i class="bi bi-info-circle-fill me-1"></i>
                No scan history found. Start a new scan to see results here.
            </div>
        @else
            <div class="table-responsive shadow-sm border rounded">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>URL</th>
                            <th>Scanned At</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($scans as $index => $scan)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('scan.results', $scan->id) }}" class="text-decoration-none">
                                        <i class="bi bi-link-45deg me-1"></i>{{ Str::limit($scan->url, 50) }}
                                    </a>
                                </td>
                                <td><i class="bi bi-calendar-event me-1"></i>{{ $scan->created_at->format('d M Y, h:i A') }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $scan->status === 'completed' ? 'success' : 'secondary' }}">
                                        <i
                                            class="bi {{ $scan->status === 'completed' ? 'bi-check-circle' : 'bi-hourglass-split' }} me-1"></i>
                                        {{ ucfirst($scan->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('scan.results', $scan->id) }}"
                                        class="btn btn-sm btn-outline-primary me-1" title="View Details">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('scan.export.pdf', $scan->id) }}"
                                        class="btn btn-sm btn-outline-success me-1" title="Export PDF">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </a>
                                    <form action="{{ route('scan.delete', $scan->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this scan?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Delete Scan">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $scans->links() }}
            </div>
        @endif
    </div>
@endsection
