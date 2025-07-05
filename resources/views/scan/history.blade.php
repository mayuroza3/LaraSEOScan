@extends('layouts.app')

@section('title', 'Your Scan History')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">📜 Your Scan History</h2>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($scans->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>URL</th>
                    <th>Status</th>
                    <th>Scanned At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($scans as $index => $scan)
                    <tr>
                        <td>{{ $index + $scans->firstItem() }}</td>
                        <td>{{ $scan->url }}</td>
                        <td>
                            <span class="badge bg-{{ $scan->status === 'COMPLETED' ? 'success' : ($scan->status === 'PENDING' ? 'warning' : 'danger') }}">
                                {{ ucfirst($scan->status) }}
                            </span>
                        </td>
                        <td>{{ $scan->created_at->format('d M Y, H:i') }}</td>
                        <td>
                            <a href="{{ route('scan.results', $scan->id) }}" class="btn btn-sm btn-primary">View</a>
                            <form action="{{ route('scan.delete', $scan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this scan?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">🗑️</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $scans->links() }}
    @else
        <p>You haven't scanned any URLs yet.</p>
    @endif
</div>
@endsection
