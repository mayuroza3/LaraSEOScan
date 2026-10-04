<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class CharsetRule implements SeoRule
{
    public function key(): string { return 'meta.charset'; }
    public function title(): string { return 'Character Encoding (Charset) Audit'; }
    public function category(): string { return 'meta'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $charsets = $xpath->query('//meta[@charset] | //meta[translate(@http-equiv, "CONTENT-TYPE", "content-type")="content-type"]');

        $found = false;
        if ($charsets && $charsets->length > 0) {
            foreach ($charsets as $node) {
                $charsetVal = $node->getAttribute('charset') ?: $node->getAttribute('content');
                if (!empty($charsetVal) && (str_contains(strtolower($charsetVal), 'utf-8') || str_contains(strtolower($charsetVal), 'charset='))) {
                    $found = true;
                    break;
                }
            }
        }

        // Also check Content-Type header
        if (!$found) {
            $headers = $page->headers ?? [];
            foreach ($headers as $k => $v) {
                if (strtolower($k) === 'content-type') {
                    $val = is_array($v) ? implode(', ', $v) : (string) $v;
                    if (str_contains(strtolower($val), 'utf-8')) {
                        $found = true;
                        break;
                    }
                }
            }
        }

        if (!$found) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'high',
                'message' => 'Missing character encoding declaration (<meta charset="UTF-8">). Explicit UTF-8 declaration prevents character rendering glitches.',
                'selector' => 'head',
                'context' => [],
            ];
        }

        return $issues;
    }
}
