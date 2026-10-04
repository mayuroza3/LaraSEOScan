<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class KeywordInIntroRule implements SeoRule
{
    public function key(): string { return 'content.keyword_in_intro'; }
    public function title(): string { return 'Target Keyword in First Paragraph Audit'; }
    public function category(): string { return 'content'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $firstP = $xpath->query('//p[string-length(normalize-space()) > 30]');

        if (!$firstP || $firstP->length === 0) {
            return $issues;
        }

        $introText = strtolower(trim($firstP->item(0)->textContent));
        $keywordDensity = $page->keyword_density ?? [];

        $topKeyword = '';
        foreach ($keywordDensity as $item) {
            $kw = strtolower($item['word'] ?? '');
            if (!empty($kw) && strlen($kw) > 3) {
                $topKeyword = $kw;
                break;
            }
        }

        if (!empty($topKeyword) && !str_contains($introText, $topKeyword)) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'medium',
                'message' => "Primary topic keyword '{$topKeyword}' does not appear in the opening paragraph. Placing key topics early signals immediate content value to readers and search engines.",
                'selector' => 'p:first-of-type',
                'context' => [
                    'primary_keyword' => $topKeyword,
                    'intro_snippet' => \Illuminate\Support\Str::limit($firstP->item(0)->textContent, 100),
                ],
            ];
        }

        return $issues;
    }
}
