<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;

class ModernImageFormatRule implements SeoRule
{
    public function key(): string { return 'content.modern_image_formats'; }
    public function title(): string { return 'Modern Next-Gen Image Formats Audit'; }
    public function category(): string { return 'content'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $images = $page->images ?? [];

        if (empty($images)) {
            return $issues;
        }

        $legacyImages = [];
        foreach ($images as $img) {
            $src = strtolower(trim($img->src ?? ''));
            if (!empty($src) && (str_ends_with($src, '.png') || str_ends_with($src, '.jpg') || str_ends_with($src, '.jpeg'))) {
                $legacyImages[] = $src;
            }
        }

        if (count($legacyImages) > 3) {
            $issues[] = [
                'rule' => $this->key(),
                'severity' => 'medium',
                'message' => count($legacyImages) . " image(s) use legacy PNG or JPEG formats. Serving images in modern formats like WebP or AVIF reduces file sizes by 30-50%.",
                'selector' => 'img[src$=".png"], img[src$=".jpg"]',
                'context' => [
                    'legacy_images_count' => count($legacyImages),
                    'samples' => array_slice($legacyImages, 0, 5),
                ],
            ];
        }

        return $issues;
    }
}
