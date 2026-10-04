<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;

class ImageOptimizationRule implements SeoRule
{
    public function key(): string { return 'image.optimization'; }
    public function title(): string { return 'Image optimization checks'; }
    public function category(): string { return 'images'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $images = [];
        
        // Extract images
        $nodes = $xpath->query('//img[@src]');
        foreach ($nodes as $node) {
            $src = $node->getAttribute('src');
            if (!$src) continue;
            
            // Resolve URL
            $fullUrl = $this->resolveUrl($src, $page->url);
            if (!\App\Services\Seo\SafeUrlService::isSafeUrl($fullUrl)) {
                continue;
            }
            $images[] = ['src' => $src, 'url' => $fullUrl];
        }

        if (empty($images)) return $issues;

        // Check size and format via HEAD requests
        $client = new Client(['timeout' => 3, 'http_errors' => false]);
        
        $totalSize = 0;
        $unoptimizedCount = 0;
        
        $requests = function ($images) use ($client) {
            foreach ($images as $img) {
                yield function() use ($client, $img) {
                    return $client->sendAsync(new Request('HEAD', $img['url']));
                };
            }
        };

        $pool = new Pool($client, $requests($images), [
            'concurrency' => 5,
            'fulfilled' => function ($response, $index) use (&$issues, $images, &$totalSize, &$unoptimizedCount) {
                $img = $images[$index];
                
                $contentType = $response->getHeaderLine('Content-Type');
                if ($contentType && !preg_match('/image\/(webp|avif|svg\+xml)/i', $contentType)) {
                    if (preg_match('/image\/(jpeg|png|gif)/i', $contentType)) {
                        $unoptimizedCount++;
                    }
                }

                // Check size
                $contentLength = $response->getHeaderLine('Content-Length');
                if ($contentLength) {
                    $sizeKb = (int) $contentLength / 1024;
                    $totalSize += (int) $contentLength;
                    
                    if ($sizeKb > 200) {
                        $issues[] = [
                            'rule' => 'image.large_size',
                            'severity' => 'warning',
                            'message' => "Image size (" . round($sizeKb) . "KB) exceeds 200KB limit.",
                            'selector' => 'img[src="' . $img['src'] . '"]',
                            'context' => ['src' => $img['src'], 'size_kb' => round($sizeKb)],
                        ];
                    }
                }
            },
            'rejected' => function ($reason, $index) {
                // Ignore failed image checks
            },
        ]);

        try {
            $pool->promise()->wait();
        } catch (\Throwable $e) {
            // Log or ignore network timeout during image checks
        }
        
        // Update Page model
        $page->image_total_size = $totalSize;
        $page->image_unoptimized_count = $unoptimizedCount;
        $page->saveQuietly();

        return $issues;
    }

    private function resolveUrl($url, $base)
    {
        if (parse_url($url, PHP_URL_SCHEME)) return $url;
        // Simple resolve, reusing logic or use library
        // Since we are inside a rule, we might duplicate slight logic or assume simple relative
        if (str_starts_with($url, '/')) {
             $baseParts = parse_url($base);
             return $baseParts['scheme'] . '://' . $baseParts['host'] . $url;
        }
        return rtrim($base, '/') . '/' . $url;
    }
}
