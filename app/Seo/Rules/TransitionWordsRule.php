<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class TransitionWordsRule implements SeoRule
{
    public function key(): string { return 'content.transition_words'; }
    public function title(): string { return 'Transition Words Readability Audit'; }
    public function category(): string { return 'content'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $bodyNodes = $xpath->query('//body');

        if (!$bodyNodes || $bodyNodes->length === 0) {
            return $issues;
        }

        $text = strtolower(preg_replace('/\s+/', ' ', trim($bodyNodes->item(0)->textContent)));
        $sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        if (count($sentences) < 5) {
            return $issues;
        }

        $transitions = [
            'however', 'therefore', 'because', 'furthermore', 'moreover', 'consequently',
            'in addition', 'for example', 'for instance', 'as a result', 'on the other hand',
            'nevertheless', 'such as', 'although', 'despite', 'in order to', 'specifically',
            'meaning', 'thus', 'specifically', 'to summarize', 'in conclusion'
        ];

        $matchedSentences = 0;
        foreach ($sentences as $sentence) {
            foreach ($transitions as $word) {
                if (str_contains($sentence, $word)) {
                    $matchedSentences++;
                    break;
                }
            }
        }

        $percentage = round(($matchedSentences / count($sentences)) * 100, 1);

        if ($percentage < 15) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'info',
                'message' => "Low transition word density: Only {$percentage}% of sentences use transition words (recommended: at least 20%). Adding connectors like 'however', 'because', or 'for example' improves text flow.",
                'selector' => 'body',
                'context' => ['transition_percentage' => $percentage],
            ];
        }

        return $issues;
    }
}
