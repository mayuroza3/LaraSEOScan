<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class InvalidHeadElementsRule implements SeoRule
{
    public function key(): string { return 'meta.invalid_head'; }
    public function title(): string { return 'Illegal Elements inside <head> Audit'; }
    public function category(): string { return 'meta'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        
        // Allowed tags inside head
        $allowedTags = ['title', 'meta', 'link', 'script', 'style', 'base', 'noscript', 'template'];

        // Query direct child elements of <head>
        $headChildren = $xpath->query('//head/*');
        $illegalMatches = [];

        if ($headChildren && $headChildren->length > 0) {
            foreach ($headChildren as $node) {
                $tagName = strtolower($node->nodeName);
                if (!in_array($tagName, $allowedTags, true)) {
                    $illegalMatches[] = $tagName;
                }
            }
        }

        // Fallback regex check on page context or DOM
        if (empty($illegalMatches)) {
            $rawHtml = $dom->saveHTML() ?: '';
            if (preg_match('/<head\b[^>]*>(.*?)<\/head>/is', $rawHtml, $matches)) {
                $headContent = $matches[1];
                if (preg_match_all('/<(img|div|p|table|iframe|form|h[1-6]|ul|ol|li)\b/i', $headContent, $found)) {
                    $illegalMatches = array_unique(array_map('strtolower', $found[1]));
                }
            }
        }

        if (!empty($illegalMatches)) {
            $illegalMatches = array_unique($illegalMatches);
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'high',
                'message' => 'Illegal DOM elements found inside <head>: ' . implode(', ', $illegalMatches) . '. Placing elements like <img> or <div> inside <head> breaks meta tag parsing.',
                'selector' => 'head',
                'context' => ['invalid_tags' => $illegalMatches],
            ];
        }

        return $issues;
    }
}
