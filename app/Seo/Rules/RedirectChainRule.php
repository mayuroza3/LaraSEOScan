<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class RedirectChainRule implements SeoRule
{
    public function key(): string { return 'security.redirect_chain'; }
    public function title(): string { return 'Redirect Chain & Hop Count Audit'; }
    public function category(): string { return 'security'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $chain = $page->redirect_chain ?? [];
        $count = $page->redirect_count ?: count($chain);

        if ($count > 2) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'high',
                'message' => "Page triggered {$count} redirect hops. Excessive redirect chains slow down crawl budget and user load times.",
                'selector' => null,
                'context' => [
                    'redirect_count' => $count,
                    'chain' => $chain,
                ],
            ];
        }

        return $issues;
    }
}
