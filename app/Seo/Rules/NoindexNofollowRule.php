<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class NoindexNofollowRule implements SeoRule
{
    public function key(): string { return 'meta.noindex_nofollow'; }
    public function title(): string { return 'Noindex / Nofollow Directives Audit'; }
    public function category(): string { return 'meta'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $robotsMeta = '';

        $metaNodes = $xpath->query('//meta[translate(@name, "ROBOTS", "robots")="robots" or translate(@name, "GOOGLEBOT", "googlebot")="googlebot"]');
        if ($metaNodes && $metaNodes->length > 0) {
            foreach ($metaNodes as $node) {
                $robotsMeta .= ' ' . strtolower($node->getAttribute('content'));
            }
        }

        // Also check headers for X-Robots-Tag
        $headers = $page->headers ?? [];
        foreach ($headers as $k => $v) {
            if (strtolower($k) === 'x-robots-tag') {
                $robotsMeta .= ' ' . strtolower(is_array($v) ? implode(', ', $v) : (string) $v);
            }
        }

        if (str_contains($robotsMeta, 'noindex')) {
            $issues[] = [
                'rule' => $this->key() . '.noindex',
                'severity' => 'critical',
                'message' => 'Page contains a "noindex" directive in meta tags or headers, preventing search engines from indexing this page.',
                'selector' => 'meta[name="robots"]',
                'context' => ['directives' => trim($robotsMeta)],
            ];
        }

        if (str_contains($robotsMeta, 'nofollow')) {
            $issues[] = [
                'rule' => $this->key() . '.nofollow',
                'severity' => 'high',
                'message' => 'Page contains a "nofollow" directive, preventing search engine bots from following internal and external links.',
                'selector' => 'meta[name="robots"]',
                'context' => ['directives' => trim($robotsMeta)],
            ];
        }

        return $issues;
    }
}
