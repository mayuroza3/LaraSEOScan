<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class ViewportRule implements SeoRule
{
    public function key(): string { return 'meta.viewport'; }
    public function title(): string { return 'Viewport Meta Tag Audit'; }
    public function category(): string { return 'meta'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $viewports = $xpath->query('//meta[translate(@name, "VIEWPORT", "viewport")="viewport"]');

        if (!$viewports || $viewports->length === 0) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'high',
                'message' => 'Missing viewport meta tag. Viewport tag is critical for mobile responsiveness and Google mobile-first indexing.',
                'selector' => 'head',
                'context' => [],
            ];
        } else {
            $content = strtolower($viewports->item(0)->getAttribute('content'));
            if (!str_contains($content, 'width=device-width')) {
                $issues[] = [
                    'rule' => $this->key() . '.device_width',
                    'severity' => 'medium',
                    'message' => 'Viewport content missing "width=device-width". Ensure responsive layout scaling across mobile screens.',
                    'selector' => 'meta[name="viewport"]',
                    'context' => ['content' => $content],
                ];
            }
        }

        return $issues;
    }
}
