<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class BrokenImageRule implements SeoRule
{
    public function key(): string { return 'content.broken_images'; }
    public function title(): string { return 'Broken Images Audit'; }
    public function category(): string { return 'content'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $images = $page->images ?? [];

        $missingSrc = [];
        foreach ($images as $img) {
            $src = trim($img->src ?? '');
            if (empty($src)) {
                $missingSrc[] = $img;
            }
        }

        if (!empty($missingSrc)) {
            $count = count($missingSrc);
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'high',
                'message' => "{$count} image element(s) have empty or missing 'src' attributes.",
                'selector' => 'img:not([src])',
                'context' => ['count' => $count],
            ];
        }

        return $issues;
    }
}
