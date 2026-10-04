<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class OpenGraphCompleteRule implements SeoRule
{
    public function key(): string { return 'meta.og_complete'; }
    public function title(): string { return 'Open Graph Metadata Audit'; }
    public function category(): string { return 'og'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $requiredOg = [
            'og:title' => 'Open Graph Title',
            'og:description' => 'Open Graph Description',
            'og:image' => 'Open Graph Image',
            'og:url' => 'Open Graph URL',
            'og:type' => 'Open Graph Type',
        ];

        $missing = [];
        foreach ($requiredOg as $property => $label) {
            $nodes = $xpath->query("//meta[@property='{$property}']");
            if (!$nodes || $nodes->length === 0 || empty(trim($nodes->item(0)->getAttribute('content')))) {
                $missing[] = $property;
            }
        }

        if (!empty($missing)) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'medium',
                'message' => 'Missing Open Graph tags: ' . implode(', ', $missing) . '. Complete Open Graph tags enhance social previews on Facebook, LinkedIn, and messaging apps.',
                'selector' => 'meta[property^="og:"]',
                'context' => ['missing_tags' => $missing],
            ];
        }

        return $issues;
    }
}
