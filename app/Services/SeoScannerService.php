<?php

namespace App\Services;

use App\Models\SeoScan;
use App\Models\SeoPage;
use App\Models\SeoLink;
use App\Models\SeoImage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class SeoScannerService
{
    protected $visited = [];
    protected $maxDepth = 5;
    protected $maxPages = 25;
    protected $pageCount = 0;

    public function scan(SeoScan $scan)
    {
        $this->crawlAndScan($scan->url, $scan);
        $scan->status = 'COMPLETED';
        $scan->save();
    }

    protected function crawlAndScan(string $url, SeoScan $scan, int $depth = 0)
    {
        if ($depth > $this->maxDepth || isset($this->visited[$url]) || $this->pageCount >= $this->maxPages) {
            return;
        }

        $this->visited[$url] = true;
        $this->pageCount++;

        try {
            $response = Http::timeout(10)->get($url);
        } catch (\Exception $e) {
            return;
        }

        if (!$response->successful()) {
            return;
        }

        $html = $response->body();
        $crawler = new Crawler($html, $url);

        $headings = [];
        foreach (range(1, 6) as $level) {
            $crawler->filter("h{$level}")->each(function ($node) use (&$headings, $level) {
                $headings[] = [
                    'tag' => "h{$level}",
                    'text' => trim($node->text()),
                ];
            });
        }

        $page = SeoPage::create([
            'seo_scan_id' => $scan->id,
            'url' => $url,
            'title' => optional($crawler->filter('title'))->count() ? $crawler->filter('title')->text() : null,
            'description' => optional($crawler->filter('meta[name="description"]'))->count() ? $crawler->filter('meta[name="description"]')->attr('content') : null,
            'canonical' => $crawler->filter('link[rel=canonical]')->count() ? $crawler->filter('link[rel=canonical]')->attr('href') : null,
            'headings' => $headings,
        ]);

        // Extract links
        $crawler->filter('a')->each(function ($node) use ($page, $url) {
            $href = $node->attr('href');
            if (!$href) return;
            $absoluteUrl = $this->resolveUrl($href, $url);
            $status = null;

            try {
                $head = Http::timeout(5)->head($absoluteUrl);
                $status = $head->status();
            } catch (\Exception $e) {
                $status = null;
            }

            SeoLink::create([
                'seo_page_id' => $page->id,
                'href' => $absoluteUrl,
                'status_code' => $status,
            ]);
        });

        // Extract images
        $crawler->filter('img')->each(function ($node) use ($page) {
            $src = $node->attr('src');
            $alt = $node->attr('alt');
            if (!$src) return;

            SeoImage::create([
                'seo_page_id' => $page->id,
                'src' => $src,
                'alt' => $alt,
            ]);
        });

        // Crawl internal links recursively
        $crawler->filter('a')->each(function ($node) use ($scan, $url, $depth) {
            $href = $node->attr('href');
            if (!$href || Str::startsWith($href, ['mailto:', 'tel:', '#'])) return;

            $linkUrl = $this->resolveUrl($href, $url);
            if ($this->isInternal($linkUrl, $scan->url)) {
                $this->crawlAndScan($linkUrl, $scan, $depth + 1);
            }
        });
    }

    protected function resolveUrl($relative, $base)
    {
        return (string) \GuzzleHttp\Psr7\UriResolver::resolve(new \GuzzleHttp\Psr7\Uri($base), new \GuzzleHttp\Psr7\Uri($relative));
    }

    protected function isInternal($url, $base)
    {
        return parse_url($url, PHP_URL_HOST) === parse_url($base, PHP_URL_HOST);
    }
}
