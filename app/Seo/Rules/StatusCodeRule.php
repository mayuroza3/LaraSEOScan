<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class StatusCodeRule implements SeoRule
{
    public function key(): string { return 'performance.status_code'; }
    public function title(): string { return 'HTTP Status Code Audit'; }
    public function category(): string { return 'performance'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $code = $page->status_code;

        if ($code === null) {
            return $issues;
        }

        if ($code >= 500) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'critical',
                'message' => "Server error encountered (HTTP status {$code}). Search engines cannot index pages returning server errors.",
                'selector' => null,
                'context' => ['status_code' => $code],
            ];
        } elseif ($code === 404) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'high',
                'message' => "Page not found (HTTP 404). Broken URLs hurt user experience and SEO rankings.",
                'selector' => null,
                'context' => ['status_code' => $code],
            ];
        } elseif ($code === 403) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'high',
                'message' => "Access forbidden (HTTP 403). Web crawlers are blocked from indexing this page.",
                'selector' => null,
                'context' => ['status_code' => $code],
            ];
        } elseif (in_array($code, [301, 302, 307, 308], true)) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'info',
                'message' => "Page returned redirect status code HTTP {$code}.",
                'selector' => null,
                'context' => ['status_code' => $code],
            ];
        }

        return $issues;
    }
}
