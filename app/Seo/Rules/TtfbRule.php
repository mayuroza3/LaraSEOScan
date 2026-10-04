<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class TtfbRule implements SeoRule
{
    public function key(): string { return 'performance.ttfb'; }
    public function title(): string { return 'Time To First Byte (TTFB) Audit'; }
    public function category(): string { return 'performance'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $ttfb = $page->ttfb_ms;

        if ($ttfb !== null && $ttfb > 600) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'medium',
                'message' => "Slow Time To First Byte (TTFB) recorded: {$ttfb} ms. Target threshold is under 600 ms for optimal server responsiveness.",
                'selector' => null,
                'context' => ['ttfb_ms' => $ttfb],
            ];
        }

        return $issues;
    }
}
