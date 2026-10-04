<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class CssSizeRule implements SeoRule
{
    public function key(): string { return 'performance.css_size'; }
    public function title(): string { return 'External CSS Audit'; }
    public function category(): string { return 'performance'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $stylesheets = $xpath->query('//link[@rel="stylesheet" and @href]');
        $count = $stylesheets ? $stylesheets->length : 0;

        if ($count > 6) {
            $issues[] = [
                'rule' => $this->key() . '.count',
                'severity' => 'medium',
                'message' => "High number of external CSS stylesheets ({$count} files). Combining CSS files improves render-blocking behavior.",
                'selector' => 'link[rel="stylesheet"]',
                'context' => ['stylesheet_count' => $count],
            ];
        }

        return $issues;
    }
}
