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
        h2, h4 {
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 20px 0;
        }
        th, td {
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
        <p><strong>Title:</strong> {{ $page->title }}</p>
        <p><strong>Description:</strong> {{ $page->description }}</p>

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
                        <td>{{ $link->status_code }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2">No links found</td></tr>
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
                    <tr><td colspan="2">No images found</td></tr>
                @endforelse
            </tbody>
        </table>
    @endforeach

</body>
</html>
