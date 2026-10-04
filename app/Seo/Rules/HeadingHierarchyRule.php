<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class HeadingHierarchyRule implements SeoRule
{
    public function key(): string { return 'content.heading_hierarchy'; }
    public function title(): string { return 'Heading Structure & Hierarchy Audit'; }
    public function category(): string { return 'content'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $headings = $page->headings ?? [];

        if (empty($headings)) {
            return $issues;
        }

        $lastLevel = 0;
        $skipped = [];

        foreach ($headings as $h) {
            $tag = strtolower($h['tag'] ?? '');
            if (!preg_match('/^h([1-6])$/', $tag, $m)) {
                continue;
            }

            $currentLevel = (int) $m[1];

            // Heading level skipped if it jumps by more than +1 (e.g., h1 to h3, or h2 to h4)
            if ($lastLevel > 0 && $currentLevel > $lastLevel + 1) {
                $skipped[] = "h{$lastLevel} to h{$currentLevel}";
            }

            $lastLevel = $currentLevel;
        }

        if (!empty($skipped)) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'medium',
                'message' => 'Skipped heading levels detected: ' . implode(', ', array_unique($skipped)) . '. Maintain proper heading hierarchy (e.g. H1 -> H2 -> H3) for accessibility and screen readers.',
                'selector' => 'h1, h2, h3, h4, h5, h6',
                'context' => ['skipped_jumps' => array_unique($skipped)],
            ];
        }

        return $issues;
    }
}
