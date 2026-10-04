<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class ContentLengthRule implements SeoRule
{
    public function key(): string { return 'content.content_length'; }
    public function title(): string { return 'Body Content Length Audit'; }
    public function category(): string { return 'content'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $wordCount = $page->word_count;

        if ($wordCount === null) {
            // Extract body text word count dynamically
            $bodyNodes = $xpath->query('//body');
            if ($bodyNodes && $bodyNodes->length > 0) {
                $text = preg_replace('/\s+/', ' ', trim($bodyNodes->item(0)->textContent));
                $wordCount = str_word_count($text);
            } else {
                $wordCount = 0;
            }
        }

        if ($wordCount < 300) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'medium',
                'message' => "Thin content detected: {$wordCount} words found. Pages with under 300 words may struggle to rank for competitive search terms.",
                'selector' => 'body',
                'context' => ['word_count' => $wordCount],
            ];
        }

        return $issues;
    }
}
