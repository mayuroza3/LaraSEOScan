<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>SEO Scan Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        h2,
        h4 {
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 20px 0;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f0f0f0;
        }

        .spacer {
            margin-top: 30px;
            border-top: 1px dashed #999;
        }
    </style>
</head>

<body>

    <h2>SEO Scan Report</h2>
    <p><strong>URL:</strong> {{ $scan->url }}</p>
    <p><strong>Date:</strong> {{ $scan->created_at }}</p>

    @foreach ($scan->pages as $page)
        <div class="spacer"></div>

        <h4>Page URL: {{ $page->url }}</h4>
        <p><strong>Title:</strong> {{ $page->title ?? 'N/A' }}</p>
        <p><strong>Description:</strong> {{ $page->description ?? 'N/A' }}</p>
        <p><strong>Canonical:</strong> {{ $page->canonical ?? 'N/A' }}</p>
        <p><strong>Robots:</strong> {{ $page->robots ?? 'N/A' }}</p>

        @if (!empty($page->headings))
            <h5>Headings</h5>
            <table>
                <thead>
                    <tr>
                        <th>Tag</th>
                        <th>Text</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($page->headings as $heading)
                        <tr>
                            <td>{{ isset($heading['tag']) ? strtoupper($heading['tag']) : 'N/A' }}</td>
                            <td>{{ isset($heading['text']) ? $heading['text'] : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p><strong>Headings:</strong> No headings found</p>
        @endif

        <h5>Links</h5>
        <table>
            <thead>
                <tr>
                    <th>URL</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($page->links as $link)
                    <tr>
                        <td>{{ $link->href }}</td>
                        <td>{{ $link->status_code ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">No links found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <h5>Images</h5>
        <table>
            <thead>
                <tr>
                    <th>Image URL</th>
                    <th>Alt Text</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($page->images as $img)
                    <tr>
                        <td>{{ $img->src }}</td>
                        <td>{{ $img->alt ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">No images found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach

</body>

</html>
