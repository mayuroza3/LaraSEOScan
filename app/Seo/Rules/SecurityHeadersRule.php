<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class SecurityHeadersRule implements SeoRule
{
    public function key(): string { return 'security.security_headers'; }
    public function title(): string { return 'Security Headers Audit'; }
    public function category(): string { return 'security'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $rawHeaders = $page->headers ?? [];
        
        // Normalize header keys to lowercase
        $headers = [];
        foreach ($rawHeaders as $k => $v) {
            $value = is_array($v) ? implode(', ', $v) : (string) $v;
            $headers[strtolower($k)] = $value;
        }

        $expectedHeaders = [
            'strict-transport-security' => [
                'name' => 'Strict-Transport-Security (HSTS)',
                'severity' => 'high',
                'description' => 'Enforces secure HTTPS connections and prevents SSL stripping.',
            ],
            'x-content-type-options' => [
                'name' => 'X-Content-Type-Options',
                'severity' => 'medium',
                'description' => 'Prevents MIME-sniffing attacks by enforcing specified content types.',
            ],
            'x-frame-options' => [
                'name' => 'X-Frame-Options',
                'severity' => 'medium',
                'description' => 'Protects against clickjacking attacks by controlling framing behavior.',
            ],
            'referrer-policy' => [
                'name' => 'Referrer-Policy',
                'severity' => 'info',
                'description' => 'Controls how much referrer information is included with requests.',
            ],
            'content-security-policy' => [
                'name' => 'Content-Security-Policy (CSP)',
                'severity' => 'medium',
                'description' => 'Mitigates XSS and data injection attacks by restricting resource loading.',
            ],
        ];

        $missing = [];
        foreach ($expectedHeaders as $key => $meta) {
            if (!isset($headers[$key])) {
                $missing[] = $meta['name'];
                $issues[] = [
                    'rule' => $this->key() . '.' . str_replace('-', '_', $key),
                    'severity' => $meta['severity'],
                    'message' => "Missing security header: {$meta['name']}. {$meta['description']}",
                    'selector' => null,
                    'context' => ['header' => $meta['name']],
                ];
            }
        }

        return $issues;
    }
}
