<?php

namespace App\Exports;

use App\Models\SeoScan;
use App\Models\SeoIssue;
use Maatwebsite\Excel\Concerns\FromArray;

class SeoScanExport implements FromArray
{
    protected $scan;

    public function __construct($id)
    {
        $this->scan = SeoScan::with(['pages.links', 'pages.images', 'pages.issues'])->findOrFail($id);
    }

    public function array(): array
    {
        $data = [];

        // Executive Summary Header
        $data[] = ['AUDIT EXECUTIVE SUMMARY'];
        $data[] = ['Target URL', $this->scan->url];
        $data[] = ['Audit Date', $this->scan->created_at->format('Y-m-d H:i:s')];
        $data[] = ['Status', $this->scan->status];
        $data[] = ['SEO Health Score', $this->scan->score . ' / 100'];
        if ($this->scan->error_message) {
            $data[] = ['Error Message', $this->scan->error_message];
        }
        $data[] = [''];

        // Audit Issues Summary Table
        $issues = SeoIssue::whereHas('page', function ($q) {
            $q->where('seo_scan_id', $this->scan->id);
        })->with('page')->orderByRaw("CASE severity WHEN 'critical' THEN 1 WHEN 'error' THEN 2 WHEN 'high' THEN 3 WHEN 'warning' THEN 4 WHEN 'medium' THEN 5 WHEN 'info' THEN 6 ELSE 7 END")->get();

        $data[] = ['DETECTED SEO & SECURITY ISSUES (' . $issues->count() . ' total)'];
        $data[] = ['Page URL', 'Rule Key', 'Severity', 'Issue Description', 'Target Selector'];

        foreach ($issues as $issue) {
            $data[] = [
                $issue->page->url ?? $this->scan->url,
                $issue->rule_key,
                strtoupper($issue->severity),
                $issue->message,
                $issue->selector ?? 'N/A',
            ];
        }

        $data[] = [''];
        $data[] = ['CRAWLED PAGES BREAKDOWN'];

        foreach ($this->scan->pages as $page) {
            $data[] = ['Page URL', $page->url];
            $data[] = ['Title', $page->title ?? 'N/A'];
            $data[] = ['Description', $page->description ?? 'N/A'];
            $data[] = ['Canonical', $page->canonical ?? 'N/A'];
            $data[] = ['HTTP Status Code', $page->status_code ?? 'N/A'];
            $data[] = ['TTFB (ms)', $page->ttfb_ms ?? 'N/A'];
            $data[] = ['HTML Size (KB)', $page->html_size_bytes ? round($page->html_size_bytes / 1024, 1) : 'N/A'];

            $data[] = ['Page Issues'];
            $data[] = ['Rule Key', 'Severity', 'Message'];
            foreach ($page->issues as $issue) {
                $data[] = [$issue->rule_key, strtoupper($issue->severity), $issue->message];
            }

            $data[] = ['Links (' . $page->links->count() . ')'];
            $data[] = ['URL', 'Status Code', 'Is Internal'];
            foreach ($page->links as $link) {
                $data[] = [$link->href, $link->status_code ?? 'N/A', $link->is_internal ? 'Yes' : 'No'];
            }

            $data[] = ['Images (' . $page->images->count() . ')'];
            $data[] = ['Image URL', 'Alt Text'];
            foreach ($page->images as $img) {
                $data[] = [$img->src, $img->alt ?? 'N/A'];
            }

            $data[] = ['']; // Spacer row between pages
        }

        return $data;
    }
}
