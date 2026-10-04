<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class HttpsAuditRule implements SeoRule
{
    public function key(): string { return 'security.https'; }
    public function title(): string { return 'HTTPS Protocol & Mixed Content Audit'; }
    public function category(): string { return 'security'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $isHttps = str_starts_with(strtolower($page->url), 'https://');

        if (!$isHttps) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'critical',
                'message' => 'Website is served over unencrypted HTTP protocol. HTTPS is strongly recommended for security and search ranking.',
                'selector' => null,
                'context' => ['url' => $page->url],
            ];
        } else {
            // Mixed content check
            $mixedResources = [];
            $nodes = $xpath->query('//img[@src] | //script[@src] | //link[@href and @rel="stylesheet"] | //iframe[@src]');
            
            if ($nodes) {
                foreach ($nodes as $node) {
                    $src = $node->getAttribute('src') ?: $node->getAttribute('href');
                    if (str_starts_with(strtolower(trim($src)), 'http://')) {
                        $mixedResources[] = $src;
                    }
                }
            }

            if (!empty($mixedResources)) {
                $count = count($mixedResources);
                $issues[] = [
                    'rule' => $this->key() . '.mixed_content',
                    'severity' => 'high',
                    'message' => "{$count} mixed content resource(s) loaded over insecure HTTP on an HTTPS page.",
                    'selector' => 'img, script, link, iframe',
                    'context' => ['mixed_resources' => array_slice($mixedResources, 0, 10)],
                ];
            }
        }

        return $issues;
    }
}
