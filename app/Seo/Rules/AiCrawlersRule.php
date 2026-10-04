<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class AiCrawlersRule implements SeoRule
{
    public function key(): string { return 'ai.crawlers_blocked'; }
    public function title(): string { return 'AI Crawler Policy Audit'; }
    public function category(): string { return 'content'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];

        // Informational status rule for AI search crawlers (GPTBot, ClaudeBot, PerplexityBot)
        $issues[] = [
            'rule' => $this->key(),
            'severity' => 'info',
            'message' => 'AI crawler visibility audit: Verify robots.txt policies for AI search engines like GPTBot, ClaudeBot, and PerplexityBot.',
            'selector' => null,
            'context' => ['audited_bots' => ['GPTBot', 'ClaudeBot', 'PerplexityBot', 'ByteSpider']],
        ];

        return $issues;
    }
}
