<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class HreflangRule implements SeoRule
{
    public function key(): string { return 'meta.hreflang'; }
    public function title(): string { return 'Hreflang Internationalization Audit'; }
    public function category(): string { return 'meta'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $hreflangs = $xpath->query('//link[@rel="alternate" and @hreflang]');

        if (!$hreflangs || $hreflangs->length === 0) {
            return $issues;
        }

        $codes = [];
        foreach ($hreflangs as $node) {
            $code = strtolower(trim($node->getAttribute('hreflang')));
            $href = trim($node->getAttribute('href'));

            if (!empty($code)) {
                $codes[] = $code;
            }

            if (empty($href)) {
                $issues[] = [
                    'rule' => $this->key() . '.empty_href',
                    'severity' => 'high',
                    'message' => "Hreflang tag for language '{$code}' has an empty href attribute.",
                    'selector' => 'link[hreflang]',
                    'context' => ['hreflang' => $code],
                ];
            }
        }

        return $issues;
    }
}
