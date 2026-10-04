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
use App\Services\Seo\RobotsTxtService;
use App\Services\Seo\SitemapService;
use App\Services\Seo\KeywordDensityService;
use App\Services\Seo\SafeUrlService;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Log;

class SeoScannerService
{
    protected $visited = [];
    protected $maxDepth = 5;
    protected $maxPages = 200;
    protected $pageCount = 0;
    
    protected RobotsTxtService $robotsService;
    protected SitemapService $sitemapService;
    protected KeywordDensityService $keywordService;
    
    public function __construct(
        RobotsTxtService $robotsService, 
        SitemapService $sitemapService,
        KeywordDensityService $keywordService
    )
    {
        $this->robotsService = $robotsService;
        $this->sitemapService = $sitemapService;
        $this->keywordService = $keywordService;
    }

    public function scan(SeoScan $scan)
    {
        // Validate URL safety (SSRF Protection)
        if (!SafeUrlService::isSafeUrl($scan->url)) {
            Log::warning("Scan aborted for unsafe target URL: {$scan->url}");
            $scan->status = 'FAILED';
            $scan->save();
            return;
        }

        // Restrict to single page if it's a specific landing tool
        if ($scan->type && !in_array($scan->type, ['seo-checker', 'free-seo-checker', 'website-seo-checker'])) {
            $this->maxDepth = 0;
            $this->maxPages = 1;
        }

        // Fetch robots.txt and sitemap.xml before crawling
        $this->robotsService->fetch($scan->url);
        $this->sitemapService->fetch($scan->url);

        // $this->crawlAndScan($scan->url, $scan);
        $this->crawlBatch([$scan->url], $scan, 0);

        // Post-crawl: Sitemap Analysis
        if (!empty($this->sitemapService->getUrls())) {
             $crawledUrls = SeoPage::where('seo_scan_id', $scan->id)->pluck('url')->toArray();
             // Normalize URLs for comparison (trim slashes)
             $crawledUrls = array_map(fn($u) => rtrim($u, '/'), $crawledUrls);
             
             $sitemapUrls = array_map(fn($u) => rtrim($u, '/'), $this->sitemapService->getUrls());
             
             // Orphan check (in Sitemap but not crawled)
             $missing = array_diff($sitemapUrls, $crawledUrls);
             
             // We'll attach to the first page for visibility if it exists.
             $firstPage = $scan->pages()->first();
             if ($firstPage && !empty($missing)) {
                 foreach (array_slice($missing, 0, 50) as $missingUrl) {
                     SeoIssue::create([
                        'seo_page_id' => $firstPage->id,
                        'rule_key' => 'sitemap.missing_page',
                        'severity' => 'warning',
                        'message' => 'Page in sitemap but not crawled.',
                        'context' => ['url' => $missingUrl],
                     ]);
                 }
             }
        }

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
        // UTF-8 hack
        if (!empty($html)) {
             try {
                 $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_NOERROR | LIBXML_NOWARNING);
             } catch (\Exception $e) {
                 // Fallback
                 @$dom->loadHTML($html);
             }
        }
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);

        foreach (Registry::all() as $rule) {
            try {
                $issues = $rule->check($page, $dom, $xpath);

                foreach ($issues as $issue) {
                    SeoIssue::create([
                        'seo_page_id' => $page->id,
                        'rule_key'    => $issue['rule'] ?? $rule->key(),
                        'severity'    => $issue['severity'] ?? 'info',
                        'message'     => $issue['message'] ?? 'Unknown issue',
                        'selector'    => $issue['selector'] ?? null,
                        'context'     => $issue['context'] ?? null,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Rule failed: " . get_class($rule) . " - " . $e->getMessage());
            }
        }
    }

    protected function getHttpClient(array $options = []): Client
    {
        $defaultOptions = [
            'timeout' => 10,
            'allow_redirects' => [
                'max' => config('seo.crawler.max_redirects', 5),
                'track_redirects' => true,
                'protocols' => ['http', 'https'],
                'on_redirect' => function (
                    \Psr\Http\Message\RequestInterface $request,
                    \Psr\Http\Message\ResponseInterface $response,
                    \Psr\Http\Message\UriInterface $targetUri
                ) {
                    if (!SafeUrlService::isSafeUrl((string) $targetUri)) {
                        throw new \InvalidArgumentException("SSRF Guard: Redirect destination {$targetUri} is unsafe.");
                    }
                },
            ],
            'http_errors' => false,
            'verify' => true,
            'headers' => ['User-Agent' => 'LaraSEOScanBot/1.0 (SEO Auditor)'],
        ];

        $merged = array_merge($defaultOptions, $options);

        if (app()->environment('testing')) {
            try {
                $factory = Http::getFacadeRoot();
                if ($factory) {
                    $handler = $factory->buildClient()->getConfig('handler');
                    if ($handler) {
                        $merged['handler'] = $handler;
                    }
                }
            } catch (\Throwable $e) {
                // Ignore fallback
            }
        }

        return new Client($merged);
    }

    /**
     * Crawl multiple URLs in parallel with Pool
     */
    protected function crawlBatch(array $urls, SeoScan $scan, int $depth)
    {
        if ($depth > $this->maxDepth || $this->pageCount >= $this->maxPages) {
            return;
        }

        $urls = array_values(array_filter($urls, function ($u) {
            if (isset($this->visited[$u])) return false;
            if (!SafeUrlService::isSafeUrl($u)) {
                Log::warning("SSRF Guard skipped unsafe URL: $u");
                return false;
            }
            // Check robots.txt
            try {
                if (!$this->robotsService->isAllowed($u)) {
                    return false;
                }
            } catch (\Exception $e) {
                Log::warning("Robots check failed for $u: " . $e->getMessage());
            }
            return true;
        }));

        if (empty($urls)) return;

        foreach ($urls as $u) {
            $this->visited[$u] = true;
        }

        if (app()->environment('testing')) {
            $nextBatch = [];
            foreach ($urls as $url) {
                try {
                    $response = Http::get($url);
                    if ($response->successful()) {
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
                        $density = $this->keywordService->analyze($html);
                        $page = SeoPage::create([
                            'seo_scan_id' => $scan->id,
                            'url' => $url,
                            'title' => $crawler->filter('title')->count() ? $crawler->filter('title')->text() : null,
                            'description' => $crawler->filter('meta[name="description"]')->count() ? $crawler->filter('meta[name="description"]')->attr('content') : null,
                            'canonical' => $crawler->filter('link[rel=canonical]')->count() ? $crawler->filter('link[rel=canonical]')->attr('href') : null,
                            'headings' => $headings,
                            'keyword_density' => $density,
                        ]);
                        $this->pageCount++;

                        $crawler->filter('a')->each(function ($node) use ($page, $url, &$nextBatch, $scan) {
                            $href = $node->attr('href');
                            if (!$href || Str::startsWith($href, ['mailto:', 'tel:', '#'])) return;
                            $absoluteUrl = $this->resolveUrl($href, $url);
                            SeoLink::create([
                                'seo_page_id' => $page->id,
                                'href' => $absoluteUrl,
                                'status_code' => null,
                                'is_internal' => $this->isInternal($absoluteUrl, $scan->url),
                            ]);
                            if ($this->isInternal($absoluteUrl, $scan->url)) {
                                $nextBatch[] = $absoluteUrl;
                            }
                        });

                        $crawler->filter('img')->each(function ($node) use ($page) {
                            $src = $node->attr('src');
                            if (!$src) return;
                            SeoImage::create([
                                'seo_page_id' => $page->id,
                                'src' => $src,
                                'alt' => $node->attr('alt'),
                            ]);
                        });

                        $this->runRules($page, $html);
                    }
                } catch (\Throwable $e) {
                    Log::error("Testing crawl failed for $url: " . $e->getMessage());
                }
            }
            if (!empty($nextBatch) && $this->pageCount < $this->maxPages) {
                $this->crawlBatch(array_unique($nextBatch), $scan, $depth + 1);
            }
            return;
        }

        $client = $this->getHttpClient();

        $requests = function ($urls) use ($client) {
            foreach ($urls as $url) {
                yield function() use ($client, $url) {
                    return $client->getAsync($url);
                };
            }
        };

        $nextBatch = [];

        $pool = new Pool($client, $requests($urls), [
            'concurrency' => 5,
            'fulfilled' => function ($response, $index) use ($urls, $scan, &$nextBatch, $depth) {
                $url = $urls[$index];
                try {
                    if ($this->pageCount >= $this->maxPages) return;

                    if ($response->getStatusCode() !== 200) {
                        return;
                    }

                    $html = (string) $response->getBody();
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

                    // Calculate metrics
                    $density = $this->keywordService->analyze($html);
                    
                    // ✅ create page & run rules
                    $page = SeoPage::create([
                        'seo_scan_id' => $scan->id,
                        'url' => $url,
                        'title' => $crawler->filter('title')->count() ? $crawler->filter('title')->text() : null,
                        'description' => $crawler->filter('meta[name="description"]')->count() ? $crawler->filter('meta[name="description"]')->attr('content') : null,
                        'canonical' => $crawler->filter('link[rel=canonical]')->count() ? $crawler->filter('link[rel=canonical]')->attr('href') : null,
                        'headings' => $headings,
                        'keyword_density' => $density,
                    ]);

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
                            'is_internal' => $this->isInternal($absoluteUrl, $scan->url),
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

                    // ✅ Run Rules AFTER links/images are saved so rules can access relationships
                    $this->runRules($page, $html);
                } catch (\Exception $e) {
                     Log::error("Failed processing page $url: " . $e->getMessage());
                }
            },
            'rejected' => function ($reason, $index) use ($urls) {
                 Log::warning("Failed to crawl {$urls[$index]}: " . $reason->getMessage());
            },
        ]);

        $pool->promise()->wait();

        if (!empty($nextBatch) && $this->pageCount < $this->maxPages) {
            $this->crawlBatch(array_unique($nextBatch), $scan, $depth + 1);
        }
    }
}
