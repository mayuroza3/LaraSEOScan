@extends('layouts.app')

@section('title', 'Scan Results')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">🧾 Scan Results for: {{ $scan->url }}</h2>

    <p><strong>Status:</strong> {{ $scan->status }}</p>

    @foreach ($scan->pages as $page)
        <hr>
        <h4>📄 Page: {{ $page->url }}</h4>
        <p><strong>Title:</strong> {{ $page->title ?? 'N/A' }}</p>
        <p><strong>Description:</strong> {{ $page->description ?? 'N/A' }}</p>
        <p><strong>Canonical:</strong> {{ $page->canonical ?? 'N/A' }}</p>
        <p><strong>Robots:</strong> {{ $page->robots ?? 'N/A' }}</p>

        <h5>📑 Headings:</h5>
        <ul>
            @foreach ($page->headings ?? [] as $heading)
                <li><strong>{{ strtoupper($heading['tag']) }}:</strong> {{ $heading['text'] }}</li>
            @endforeach
        </ul>

        <h5>🔗 Links:</h5>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>URL</th>
                    <th>Status Code</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($page->links as $link)
                    <tr>
                        <td><a href="{{ $link->href }}" target="_blank">{{ $link->href }}</a></td>
                        <td>{{ $link->status_code ?? 'N/A' }}</td>
                        <td>{{ $link->is_internal ? 'Internal' : 'External' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h5>🖼️ Images:</h5>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Image URL</th>
                    <th>Alt Text</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($page->images as $image)
                    <tr>
                        <td><a href="{{ $image->src }}" target="_blank">{{ $image->src }}</a></td>
                        <td>{{ $image->alt ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</div>
@endsection
