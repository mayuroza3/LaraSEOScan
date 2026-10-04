<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class LongSentencesRule implements SeoRule
{
    public function key(): string { return 'content.long_sentences'; }
    public function title(): string { return 'Sentence Length Readability Audit'; }
    public function category(): string { return 'content'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $bodyNodes = $xpath->query('//body');

        if (!$bodyNodes || $bodyNodes->length === 0) {
            return $issues;
        }

        $text = preg_replace('/\s+/', ' ', trim($bodyNodes->item(0)->textContent));
        $sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        if (empty($sentences)) {
            return $issues;
        }

        $total = count($sentences);
        $longCount = 0;

        foreach ($sentences as $sentence) {
            $words = str_word_count(trim($sentence));
            if ($words > 25) {
                $longCount++;
            }
        }

        $percentage = round(($longCount / $total) * 100, 1);

        if ($percentage > 20 && $total >= 5) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'medium',
                'message' => "Readability warning: {$percentage}% of sentences contain more than 25 words ({$longCount} of {$total} sentences). Shortening sentences improves user comprehension.",
                'selector' => 'body',
                'context' => [
                    'long_sentence_percentage' => $percentage,
                    'long_sentence_count' => $longCount,
                    'total_sentences' => $total,
                ],
            ];
        }

        return $issues;
    }
}
