<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class LlmsTxtRule implements SeoRule
{
    public function key(): string { return 'ai.llms_txt'; }
    public function title(): string { return '/llms.txt Standard Audit'; }
    public function category(): string { return 'content'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];

        // Check if page links to /llms.txt in head or footer
        $links = $xpath->query('//a[contains(@href, "/llms.txt")] | //link[contains(@href, "/llms.txt")]');

        if (!$links || $links->length === 0) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'info',
                'message' => 'No /llms.txt standard link found. Creating an /llms.txt file helps LLMs and AI search engines extract structured site information.',
                'selector' => 'link, a',
                'context' => [],
            ];
        }

        return $issues;
    }
}
