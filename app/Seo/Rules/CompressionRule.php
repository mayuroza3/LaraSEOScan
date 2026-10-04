<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class CompressionRule implements SeoRule
{
    public function key(): string { return 'performance.compression'; }
    public function title(): string { return 'HTTP Compression (Gzip / Brotli) Audit'; }
    public function category(): string { return 'performance'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $headers = $page->headers ?? [];

        $encoding = '';
        foreach ($headers as $k => $v) {
            if (strtolower($k) === 'content-encoding') {
                $encoding = strtolower(is_array($v) ? implode(', ', $v) : (string) $v);
                break;
            }
        }

        if (empty($encoding) || (!str_contains($encoding, 'gzip') && !str_contains($encoding, 'br') && !str_contains($encoding, 'deflate'))) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'high',
                'message' => 'HTTP response is not compressed (missing Gzip or Brotli Content-Encoding header). Compression reduces network payload sizes by up to 70%.',
                'selector' => null,
                'context' => ['content_encoding' => $encoding ?: 'none'],
            ];
        }

        return $issues;
    }
}
