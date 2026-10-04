<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class ImageLazyLoadingRule implements SeoRule
{
    public function key(): string { return 'content.image_lazy_loading'; }
    public function title(): string { return 'Image Lazy Loading Audit'; }
    public function category(): string { return 'content'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $totalImages = $xpath->query('//img[@src]')->length;
        $lazyImages = $xpath->query('//img[@loading="lazy"]')->length;

        if ($totalImages > 4 && $lazyImages === 0) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'medium',
                'message' => "Page has {$totalImages} images but none use loading=\"lazy\". Adding loading=\"lazy\" improves page load performance.",
                'selector' => 'img',
                'context' => [
                    'total_images' => $totalImages,
                    'lazy_images' => $lazyImages,
                ],
            ];
        }

        return $issues;
    }
}
