<?php

namespace App\Services;

use App\Models\SeoScan;
use App\Models\SeoPage;
use App\Models\SeoLink;
use App\Models\SeoImage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use App\Seo\Rules\Registry;
use App\Models\SeoIssue;
use GuzzleHttp\Promise\Utils;

class SeoScannerService
{
    protected $visited = [];
    protected $maxDepth = 5;
    protected $maxPages = 25;
    protected $pageCount = 0;

    public function scan(SeoScan $scan)
    {
        // $this->crawlAndScan($scan->url, $scan);
        $this->crawlBatch([$scan->url], $scan, 0);
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
        $this->runRules($page, $html);

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

    protected function checkLinks(array $links): array
    {
        $client = new Client([
            'timeout' => 5,
            'allow_redirects' => true,
            'headers' => ['User-Agent' => 'LaraSEOScanBot/1.0'],
        ]);

        $results = [];

        $requests = function ($links) use ($client) {
            foreach ($links as $link) {
                yield function () use ($client, $link) {
                    return $client->headAsync($link);
                };
            }
        };

        $pool = new Pool($client, $requests($links), [
            'concurrency' => 10,
            'fulfilled' => function ($response, $index) use (&$results, $links) {
                $results[$links[$index]] = $response->getStatusCode();
            },
            'rejected' => function ($reason, $index) use (&$results, $links) {
                $results[$links[$index]] = null;
            },
        ]);

        $pool->promise()->wait();

        return $results;
    }
    protected function fetchRobotsTxt(string $url): ?string
    {
        try {
            return Http::timeout(5)->get($url . '/robots.txt')->body();
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function fetchSitemap(string $url): ?string
    {
        try {
            return Http::timeout(5)->get($url . '/sitemap.xml')->body();
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function runRules(SeoPage $page, string $html): void
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);


        foreach (Registry::all() as $rule) {
            // Pass html as the 3rd argument
            $issues = $rule->check($page, $dom, $xpath);

            foreach ($issues as $issue) {
                SeoIssue::create([
                    'seo_page_id' => $page->id,
                    'rule_key'    => $rule->key(),
                    'severity'    => $issue['severity'] ?? 'info',
                    'message'     => $issue['message'] ?? 'Unknown issue',
                    'selector'    => $issue['selector'] ?? null,
                    'context'     => $issue['context'] ?? null,
                ]);
            }
        }
    }

    /**
     * Crawl multiple URLs in parallel with Pool
     */
    protected function crawlBatch(array $urls, SeoScan $scan, int $depth)
    {
        if ($depth > $this->maxDepth || $this->pageCount >= $this->maxPages) {
            return;
        }

        $client = new Client([
            'timeout' => 10,
            'allow_redirects' => true,
            'http_errors' => false,
            'verify' => false,
            'headers' => ['User-Agent' => 'LaraSEOScanBot/1.0'],
        ]);

        $urls = array_filter($urls, fn($u) => !isset($this->visited[$u]));
        if (empty($urls)) return;

        foreach ($urls as $u) {
            $this->visited[$u] = true;
        }

        $requests = function ($urls) {
            foreach ($urls as $url) {
                yield new \GuzzleHttp\Psr7\Request('GET', $url);
            }
        };

        $nextBatch = [];

        $pool = new Pool($client, $requests($urls), [
            'concurrency' => 5,
            'fulfilled' => function ($response, $index) use ($urls, $scan, &$nextBatch, $depth) {
                $url = $urls[$index];
                if ($this->pageCount >= $this->maxPages) return;

                if ($response->getStatusCode() !== 200) {
                    return;
                }

                $html = (string) $response->getBody();
                $crawler = new Crawler($html, $url);

                // ✅ create page & run rules
                $page = SeoPage::create([
                    'seo_scan_id' => $scan->id,
                    'url' => $url,
                    'title' => $crawler->filter('title')->count() ? $crawler->filter('title')->text() : null,
                    'description' => $crawler->filter('meta[name="description"]')->count() ? $crawler->filter('meta[name="description"]')->attr('content') : null,
                    'canonical' => $crawler->filter('link[rel=canonical]')->count() ? $crawler->filter('link[rel=canonical]')->attr('href') : null,
                    'headings' => [], // keep simple, you already had heading extraction
                ]);

                $this->runRules($page, $html);
                $this->pageCount++;

                // ✅ links
                $crawler->filter('a')->each(function ($node) use ($page, $url, &$nextBatch, $scan) {
                    $href = $node->attr('href');
                    if (!$href || Str::startsWith($href, ['mailto:', 'tel:', '#'])) return;
                    $absoluteUrl = $this->resolveUrl($href, $url);

                    SeoLink::create([
                        'seo_page_id' => $page->id,
                        'href' => $absoluteUrl,
                        'status_code' => null, // bulk check with checkLinks() if needed
                    ]);

                    if ($this->isInternal($absoluteUrl, $scan->url)) {
                        $nextBatch[] = $absoluteUrl;
                    }
                });

                // ✅ images
                $crawler->filter('img')->each(function ($node) use ($page) {
                    $src = $node->attr('src');
                    if (!$src) return;
                    SeoImage::create([
                        'seo_page_id' => $page->id,
                        'src' => $src,
                        'alt' => $node->attr('alt'),
                    ]);
                });
            },
            'rejected' => function ($reason, $index) use ($urls) {
                // optional logging
            },
        ]);

        $pool->promise()->wait();

        if (!empty($nextBatch) && $this->pageCount < $this->maxPages) {
            $this->crawlBatch(array_unique($nextBatch), $scan, $depth + 1);
        }
    }
}
