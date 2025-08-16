<?php
namespace App\Seo\Rules;

use App\Models\SeoPage;

class MissingTitleRule implements SeoRule
{
    public function key(): string { return 'meta.missing_title'; }
    public function title(): string { return 'Missing or empty title'; }
    public function category(): string { return 'meta'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];

        $nodes = $xpath->query('//head/title');
        $title = null;
        if ($nodes->length) {
            $title = trim($nodes->item(0)->textContent);
        }

        if (!$title) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'error',
                'message' => 'Title tag is missing or empty.',
                'selector' => 'head > title',
                'context' => ['found' => $title],
            ];
        }

        $page->title = $title;
        $page->save();

        return $issues;
    }
}
