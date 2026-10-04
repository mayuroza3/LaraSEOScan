<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;
use App\Services\Seo\SafeUrlService;

class SsrfProtectionRule implements SeoRule
{
    public function key(): string { return 'security.ssrf_protection'; }
    public function title(): string { return 'SSRF & Private IP Audit'; }
    public function category(): string { return 'security'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];

        if (!SafeUrlService::isSafeUrl($page->url)) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'critical',
                'message' => "URL '{$page->url}' resolves to a private, loopback, or cloud metadata IP address.",
                'selector' => null,
                'context' => ['url' => $page->url],
            ];
        }

        if (!empty($page->redirect_chain)) {
            foreach ($page->redirect_chain as $hop) {
                if (!SafeUrlService::isSafeUrl($hop)) {
                    $issues[] = [
                        'rule' => $this->key(),
                        'severity' => 'critical',
                        'message' => "Redirect hop '{$hop}' points to an unsafe or private network destination.",
                        'selector' => null,
                        'context' => ['redirect_hop' => $hop],
                    ];
                }
            }
        }

        return $issues;
    }
}
