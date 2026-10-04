<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class JavascriptSizeRule implements SeoRule
{
    public function key(): string { return 'performance.javascript_size'; }
    public function title(): string { return 'External JavaScript Audit'; }
    public function category(): string { return 'performance'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $scripts = $xpath->query('//script[@src]');
        $count = $scripts ? $scripts->length : 0;

        if ($count > 15) {
            $issues[] = [
                'rule' => $this->key() . '.count',
                'severity' => 'medium',
                'message' => "High number of external JavaScript requests ({$count} scripts). Consider bundling to reduce HTTP overhead.",
                'selector' => 'script[src]',
                'context' => ['script_count' => $count],
            ];
        }

        return $issues;
    }
}
