<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class FaviconRule implements SeoRule
{
    public function key(): string { return 'meta.favicon'; }
    public function title(): string { return 'Favicon Audit'; }
    public function category(): string { return 'meta'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $icons = $xpath->query('//link[contains(translate(@rel, "ICON", "icon"), "icon")]');

        if (!$icons || $icons->length === 0) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'medium',
                'message' => 'Missing favicon link tag (<link rel="icon">). Favicons improve brand recognition in search engine results and browser tabs.',
                'selector' => 'head',
                'context' => [],
            ];
        }

        return $issues;
    }
}
