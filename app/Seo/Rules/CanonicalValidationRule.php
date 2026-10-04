<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class CanonicalValidationRule implements SeoRule
{
    public function key(): string { return 'meta.canonical_advanced'; }
    public function title(): string { return 'Canonical Tag Validation Audit'; }
    public function category(): string { return 'meta'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $canonicals = $xpath->query('//link[@rel="canonical"]');

        if (!$canonicals || $canonicals->length === 0) {
            $issues[] = [
                'rule' => $this->key() . '.missing',
                'severity' => 'high',
                'message' => 'Missing canonical URL link tag (<link rel="canonical">). Canonical tags prevent duplicate content issues across URL variations.',
                'selector' => 'head',
                'context' => [],
            ];
        } elseif ($canonicals->length > 1) {
            $issues[] = [
                'rule' => $this->key() . '.multiple',
                'severity' => 'high',
                'message' => "Multiple canonical tags ({$canonicals->length}) found on page. Search engines may ignore conflicting canonical directives.",
                'selector' => 'link[rel="canonical"]',
                'context' => ['count' => $canonicals->length],
            ];
        } else {
            $href = trim($canonicals->item(0)->getAttribute('href'));
            if (empty($href)) {
                $issues[] = [
                    'rule' => $this->key() . '.empty',
                    'severity' => 'high',
                    'message' => 'Canonical link tag contains an empty href attribute.',
                    'selector' => 'link[rel="canonical"]',
                    'context' => [],
                ];
            } elseif (!filter_var($href, FILTER_VALIDATE_URL)) {
                $issues[] = [
                    'rule' => $this->key() . '.relative',
                    'severity' => 'medium',
                    'message' => "Canonical URL '{$href}' should be an absolute URL including protocol and domain name.",
                    'selector' => 'link[rel="canonical"]',
                    'context' => ['canonical' => $href],
                ];
            }
        }

        return $issues;
    }
}
