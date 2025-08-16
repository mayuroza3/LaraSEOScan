@extends('layouts.app')

@section('title', 'Scan Results')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Scan Results</h4>
            <div>
                <a href="{{ route('scan.export.pdf', ['id' => $scan->id]) }}" class="btn btn-outline-primary  btn-sm">
                    🖨️ PDF
                </a>
                <a href="{{ route('scan.export.csv', ['id' => $scan->id]) }}" class="btn btn-outline-success btn-sm">
                    📄 CSV
                </a>
            </div>
        </div>

        <div class="alert alert-info d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">🌐 Sitewide SEO File Check</h5>
                <p class="mb-0">
                    <strong>robots.txt:</strong>
                    @if ($scan->has_robots_txt)
                        ✅ robots.txt found
                    @else
                        ❌ robots.txt missing
                    @endif
                    <br>
                    <strong>sitemap.xml:</strong>
                    @if ($scan->has_sitemap_xml)
                        ✅ sitemap.xml found
                    @else
                        ❌ sitemap.xml missing
                    @endif
                </p>
            </div>
        </div>

        @foreach ($scan->pages as $index => $page)
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <strong>📄 Page: {{ $page->url }}</strong>

                </div>

                <div id="page-{{ $index }}">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Title:</strong> {{ $page->title ?? 'N/A' }}</p>
                                <p><strong>Description:</strong> {{ $page->description ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Canonical:</strong> {{ $page->canonical ?? 'N/A' }}</p>
                                <p><strong>Robots:</strong> {{ $page->robots ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <!-- Tabs for Headings / Links / Images -->
                        <ul class="nav nav-tabs" id="tabs-{{ $index }}" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="headings-tab-{{ $index }}" data-bs-toggle="tab"
                                    data-bs-target="#headings-{{ $index }}" type="button" role="tab">📑
                                    Headings</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="links-tab-{{ $index }}" data-bs-toggle="tab"
                                    data-bs-target="#links-{{ $index }}" type="button" role="tab">🔗
                                    Links</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="images-tab-{{ $index }}" data-bs-toggle="tab"
                                    data-bs-target="#images-{{ $index }}" type="button" role="tab">🖼️
                                    Images</button>
                            </li>
                        </ul>
                        <div class="tab-content border border-top-0 p-3" id="tabs-content-{{ $index }}">
                            <!-- Headings Tab -->
                            <div class="tab-pane fade show active" id="headings-{{ $index }}" role="tabpanel">
                                @if (!empty($page->headings))
                                    <ul class="list-group list-group-flush">
                                        @foreach ($page->headings as $heading)
                                            <li class="list-group-item">
                                                <strong>{{ strtoupper($heading['tag']) }}:</strong> {{ $heading['text'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">No headings found.</p>
                                @endif
                            </div>

                            <!-- Links Tab -->
                            <div class="tab-pane fade" id="links-{{ $index }}" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle">
                                        <thead>
                                            <tr>
                                                <th class="px-2 py-1">Link</th>
                                                <th class="px-2 py-1">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($page->links as $link)
                                                <tr>
                                                    <td class="px-2 py-1">
                                                        <a href="{{ e($link->href) }}"
                                                        target="_blank"
                                                        rel="nofollow noopener noreferrer">
                                                        {{ Str::limit($link->href, 60) }}
                                                        </a>
                                                    </td>
                                                    <td class="px-2 py-1">
                                                        @if($link->status_code)
                                                            <span class="badge {{ $link->status_code >=400 ? 'bg-red-500' : 'bg-green-500' }}">
                                                                {{ $link->status_code }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-gray-500">N/A</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Images Tab -->
                            <div class="tab-pane fade" id="images-{{ $index }}" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Image URL</th>
                                                <th>Alt Text</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($page->images as $image)
                                                <tr>
                                                    <td><a href="{{ $image->src }}"
                                                            target="_blank">{{ $image->src }}</a></td>
                                                    <td>{{ $image->alt ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> <!-- tab-content -->
                    </div>
                </div> <!-- collapse -->
            </div>
        @endforeach
    </div>
@endsection
