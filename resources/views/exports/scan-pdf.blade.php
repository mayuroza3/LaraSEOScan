<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>SEO & Security Audit Report - {{ $scan->domain }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #212529;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 12px;
        }

        .header h1 {
            color: #0d6efd;
            margin: 0;
            font-size: 24px;
        }

        .header p {
            color: #6c757d;
            font-size: 9px;
            margin: 4px 0 0 0;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .summary-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .score-badge {
            display: inline-block;
            background: #0d6efd;
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
            padding: 6px 14px;
            border-radius: 20px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .badge-completed { background: #d1e7dd; color: #0f5132; }
        .badge-failed { background: #f8d7da; color: #842029; }
        .badge-running { background: #fff3cd; color: #664d03; }

        .badge-critical { background: #dc3545; color: #ffffff; }
        .badge-high { background: #fd7e14; color: #ffffff; }
        .badge-medium { background: #ffc107; color: #000000; }
        .badge-info { background: #0dcaf0; color: #ffffff; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 20px 0;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f1f3f5;
            font-weight: bold;
            color: #495057;
            text-transform: uppercase;
            font-size: 9px;
        }

        .section-title {
            color: #0d6efd;
            border-bottom: 1px solid #0d6efd;
            padding-bottom: 4px;
            margin-top: 25px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>LaraSEOScan Audit Report</h1>
        <p>Technical SEO & Security Compliance Audit</p>
    </div>

    <div class="summary-box">
        <table style="border: none; margin: 0;">
            <tr style="border: none;">
                <td style="border: none; width: 60%;">
                    <p style="margin: 2px 0;"><strong>Target URL:</strong> {{ $scan->url }}</p>
                    <p style="margin: 2px 0;"><strong>Audit Date:</strong> {{ $scan->created_at->format('M d, Y H:i:s') }}</p>
                    <p style="margin: 2px 0;"><strong>Crawler Status:</strong> 
                        <span class="status-badge badge-{{ strtolower($scan->status) }}">{{ $scan->status }}</span>
                    </p>
                    @if($scan->error_message)
                        <p style="margin: 2px 0; color: #dc3545;"><strong>Failure Notice:</strong> {{ $scan->error_message }}</p>
                    @endif
                </td>
                <td style="border: none; text-align: right; vertical-align: middle;">
                    <span class="score-badge">{{ $scan->score }} / 100</span>
                    <p style="margin: 4px 0 0 0; font-size: 10px; color: #6c757d;">Overall SEO Health Score</p>
                </td>
            </tr>
        </table>
    </div>

    @php
        $allIssues = \App\Models\SeoIssue::whereHas('page', function($q) use ($scan) {
            $q->where('seo_scan_id', $scan->id);
        })->with('page')->orderByRaw("CASE severity WHEN 'critical' THEN 1 WHEN 'error' THEN 2 WHEN 'high' THEN 3 WHEN 'warning' THEN 4 WHEN 'medium' THEN 5 WHEN 'info' THEN 6 ELSE 7 END")->get();
    @endphp

    <h3 class="section-title">Detected SEO & Security Issues ({{ $allIssues->count() }})</h3>

    @if($allIssues->isEmpty())
        <p style="color: #198754;">🎉 No technical SEO or security issues detected!</p>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Severity</th>
                    <th style="width: 22%;">Rule Check</th>
                    <th>Issue Detail & Context</th>
                    <th style="width: 25%;">Affected URL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allIssues as $issue)
                    <tr>
                        <td>
                            <span class="status-badge badge-{{ strtolower($issue->severity) }}">
                                {{ strtoupper($issue->severity) }}
                            </span>
                        </td>
                        <td><strong>{{ $issue->rule_key }}</strong></td>
                        <td>
                            {{ $issue->message }}
                            @if($issue->selector)
                                <br><small style="color: #6c757d;">Selector: <code>{{ $issue->selector }}</code></small>
                            @endif
                        </td>
                        <td style="word-break: break-all;">
                            {{ $issue->page->url ?? $scan->url }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="page-break"></div>

    <h3 class="section-title">Crawled Pages Technical Breakdown</h3>

    @foreach ($scan->pages as $page)
        <div style="background: #f8f9fa; padding: 8px; border: 1px solid #dee2e6; margin-top: 15px; border-radius: 4px;">
            <h4 style="margin: 0; color: #212529;">{{ $page->url }}</h4>
        </div>

        <table>
            <tr>
                <th style="width: 20%;">Title</th>
                <td>{{ $page->title ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{ $page->description ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Canonical</th>
                <td>{{ $page->canonical ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Performance & Headers</th>
                <td>
                    <strong>HTTP Status:</strong> {{ $page->status_code ?? '200' }} | 
                    <strong>TTFB:</strong> {{ $page->ttfb_ms ? $page->ttfb_ms . ' ms' : 'N/A' }} | 
                    <strong>HTML Size:</strong> {{ $page->html_size_bytes ? round($page->html_size_bytes / 1024, 1) . ' KB' : 'N/A' }}
                </td>
            </tr>
        </table>

        @if(!empty($page->headings))
            <h5 style="margin: 8px 0 4px 0;">Headings Structure</h5>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Tag</th>
                        <th>Text</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($page->headings as $heading)
                        <tr>
                            <td><strong>{{ isset($heading['tag']) ? strtoupper($heading['tag']) : 'N/A' }}</strong></td>
                            <td>{{ isset($heading['text']) ? $heading['text'] : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

</body>

</html>
