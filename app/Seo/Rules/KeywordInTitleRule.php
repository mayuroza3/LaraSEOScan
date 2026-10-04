<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class KeywordInTitleRule implements SeoRule
{
    public function key(): string { return 'content.keyword_in_title'; }
    public function title(): string { return 'Target Keyword in Title Tag Audit'; }
    public function category(): string { return 'content'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $title = strtolower($page->title ?? '');
        $keywordDensity = $page->keyword_density ?? [];

        if (empty($title) || empty($keywordDensity)) {
            return $issues;
        }

        // Identify highest frequency non-stopword keyword
        $topKeyword = '';
        foreach ($keywordDensity as $item) {
            $kw = strtolower($item['word'] ?? '');
            if (!empty($kw) && strlen($kw) > 3) {
                $topKeyword = $kw;
                break;
            }
        }

        if (!empty($topKeyword) && !str_contains($title, $topKeyword)) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'medium',
                'message' => "Primary content keyword '{$topKeyword}' does not appear in the page <title> tag. Including target keywords in title tags boosts search relevance.",
                'selector' => 'title',
                'context' => [
                    'primary_keyword' => $topKeyword,
                    'title' => $page->title,
                ],
            ];
        }

        return $issues;
    }
}
