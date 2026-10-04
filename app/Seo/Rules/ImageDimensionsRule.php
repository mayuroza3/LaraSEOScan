<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class ImageDimensionsRule implements SeoRule
{
    public function key(): string { return 'content.image_dimensions'; }
    public function title(): string { return 'Image Explicit Dimensions (CLS) Audit'; }
    public function category(): string { return 'content'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $images = $xpath->query('//img[not(@width) or not(@height)]');
        $count = $images ? $images->length : 0;

        if ($count > 0) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'medium',
                'message' => "{$count} image(s) missing explicit 'width' or 'height' attributes. Explicit dimensions prevent Cumulative Layout Shift (CLS).",
                'selector' => 'img:not([width]), img:not([height])',
                'context' => ['images_missing_dimensions_count' => $count],
            ];
        }

        return $issues;
    }
}
