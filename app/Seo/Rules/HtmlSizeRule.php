<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class HtmlSizeRule implements SeoRule
{
    public function key(): string { return 'performance.html_size'; }
    public function title(): string { return 'HTML Document Size Audit'; }
    public function category(): string { return 'performance'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $sizeBytes = $page->html_size_bytes ?? strlen($dom->saveHTML() ?: '');

        if ($sizeBytes > 102400) { // 100 KB
            $sizeKb = round($sizeBytes / 1024, 1);
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'medium',
                'message' => "Uncompressed HTML document size is {$sizeKb} KB, exceeding recommended 100 KB limit.",
                'selector' => 'html',
                'context' => [
                    'size_bytes' => $sizeBytes,
                    'size_kb' => $sizeKb,
                ],
            ];
        }

        return $issues;
    }
}
