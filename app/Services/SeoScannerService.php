<?php

namespace App\Services;

use App\Models\SeoScan;
use App\Models\SeoPage;
use App\Models\SeoLink;
use App\Models\SeoImage;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class SeoScannerService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 10]);
    }

    public function scan(SeoScan $scan)
    {
        try {
            $response = $this->client->get($scan->url);
            $html = (string) $response->getBody();

            $crawler = new Crawler($html);

            // Extract SEO data
            $page = SeoPage::create([
                'seo_scan_id' => $scan->id,
                'url' => $scan->url,
                'title' => $crawler->filter('title')->count() ? $crawler->filter('title')->text() : null,
                'description' => $this->getMeta($crawler, 'description'),
                'canonical' => $this->getLinkRel($crawler, 'canonical'),
                'robots' => $this->getMeta($crawler, 'robots'),
                'headings' => $this->getHeadings($crawler),
            ]);

            // Save all links
            $this->saveLinks($crawler, $page);

            // Save all images
            $this->saveImages($crawler, $page);

            $scan->status = 'COMPLETED';
            $scan->save();
        } catch (\Exception $e) {
            $scan->status = 'FAILED';
            $scan->save();
            // Log error for debugging
            \Log::error("SEO Scan Failed: " . $e->getMessage());
        }
    }

    protected function getMeta(Crawler $crawler, string $name): ?string
    {
        $node = $crawler->filter("meta[name='$name']");
        return $node->count() ? $node->attr('content') : null;
    }

    protected function getLinkRel(Crawler $crawler, string $rel): ?string
    {
        $node = $crawler->filter("link[rel='$rel']");
        return $node->count() ? $node->attr('href') : null;
    }

    protected function getHeadings(Crawler $crawler): array
    {
        $headings = [];
        foreach (['h1','h2','h3'] as $tag) {
            $crawler->filter($tag)->each(function ($node) use (&$headings, $tag) {
                $headings[] = ['tag' => $tag, 'text' => $node->text()];
            });
        }
        return $headings;
    }

    protected function saveLinks(Crawler $crawler, SeoPage $page): void
    {
        $links = $crawler->filter('a[href]')->each(function ($node) use ($page) {
            $href = $node->attr('href');

            $fullUrl = $this->normalizeUrl($href, $page->url);
            $isInternal = str_starts_with($fullUrl, parse_url($page->url, PHP_URL_HOST));

            $status = null;
            try {
                $res = $this->client->head($fullUrl, ['http_errors' => false]);
                $status = $res->getStatusCode();
            } catch (\Exception $e) {
                $status = null;
            }

            SeoLink::create([
                'seo_page_id' => $page->id,
                'href' => $fullUrl,
                'status_code' => $status,
                'is_internal' => $isInternal
            ]);
        });
    }

    protected function saveImages(Crawler $crawler, SeoPage $page): void
    {
        $crawler->filter('img')->each(function ($node) use ($page) {
            $src = $node->attr('src');
            $alt = $node->attr('alt') ?? null;

            SeoImage::create([
                'seo_page_id' => $page->id,
                'src' => $src,
                'alt' => $alt,
            ]);
        });
    }

    protected function normalizeUrl(string $href, string $baseUrl): string
    {
        // If already absolute
        if (preg_match('/^https?:\/\//', $href)) {
            return $href;
        }

        // Handle relative paths
        return rtrim($baseUrl, '/') . '/' . ltrim($href, '/');
    }
}
