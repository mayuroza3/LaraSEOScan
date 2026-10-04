<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class TwitterCardRule implements SeoRule
{
    public function key(): string { return 'meta.twitter_card'; }
    public function title(): string { return 'Twitter Card Meta Tags Audit'; }
    public function category(): string { return 'og'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $requiredTags = ['twitter:card', 'twitter:title', 'twitter:description', 'twitter:image'];
        $missing = [];

        foreach ($requiredTags as $tag) {
            $nodes = $xpath->query("//meta[@name='{$tag}' or @property='{$tag}']");
            if (!$nodes || $nodes->length === 0 || empty(trim($nodes->item(0)->getAttribute('content')))) {
                $missing[] = $tag;
            }
        }

        if (!empty($missing)) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'medium',
                'message' => 'Missing Twitter Card meta tags: ' . implode(', ', $missing) . '. Twitter Card tags improve social media preview formatting.',
                'selector' => 'meta[name^="twitter:"]',
                'context' => ['missing_tags' => $missing],
            ];
        }

        return $issues;
    }
}
