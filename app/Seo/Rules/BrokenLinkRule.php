<?php

namespace App\Seo\Rules;

use App\Models\SeoPage;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;

class BrokenLinkRule implements SeoRule
{
    public function key(): string { return 'links.broken'; }
    public function title(): string { return 'Broken link detection'; }
    public function category(): string { return 'links'; }

    public function check(SeoPage $page, \DOMDocument $dom, \DOMXPath $xpath): array
    {
        $issues = [];
        $client = new Client([
            'timeout' => 10,
            'allow_redirects' => true,
            'headers' => ['User-Agent' => 'LaraSEOScanBot/1.0'],
        ]);

        $baseUrl = $this->getBaseUrl($page, $dom);

        $nodes = $xpath->query('//a[@href]');
        $urls = [];

        foreach ($nodes as $node) {
            if (!($node instanceof \DOMElement)) continue;

            $href = trim($node->getAttribute('href'));

            if (empty($href) ||
                str_starts_with($href, '#') ||
                str_starts_with($href, 'mailto:') ||
                str_starts_with($href, 'javascript:')) {
                continue;
            }

            $urls[] = [
                'original' => $href,
                'resolved' => $this->resolveUrl($href, $baseUrl),
            ];
        }

        // No links
        if (empty($urls)) {
            return $issues;
        }

        // Run pool (5 concurrency)
        $requests = function ($urls) {
            foreach ($urls as $url) {
                yield new Request('HEAD', $url['resolved']);
            }
        };

        $pool = new Pool($client, $requests($urls), [
            'concurrency' => 10,
            'fulfilled' => function ($response, $index) use (&$issues, $urls) {
                $status = $response->getStatusCode();
                if ($status >= 400) {
                    $issues[] = [
                        'rule'     => $this->key(),
                        'severity' => 'error',
                        'message'  => "Broken link detected ({$status}): {$urls[$index]['resolved']}",
                        'selector' => 'a[href]',
                        'context'  => [
                            'href'    => $urls[$index]['original'],
                            'resolved'=> $urls[$index]['resolved'],
                            'status'  => $status
                        ],
                    ];
                }
            },
            'rejected' => function ($reason, $index) use (&$issues, $urls) {
                $issues[] = [
                    'rule'     => $this->key(),
                    'severity' => 'error',
                    'message'  => "Broken link error: {$urls[$index]['resolved']} ({$reason->getMessage()})",
                    'selector' => 'a[href]',
                    'context'  => [
                        'href'    => $urls[$index]['original'],
                        'resolved'=> $urls[$index]['resolved'],
                    ],
                ];
            },
        ]);

        $pool->promise()->wait();

        return $issues;
    }

    private function getBaseUrl(SeoPage $page, \DOMDocument $dom): string
    {
        $xpath = new \DOMXPath($dom);
        $baseNode = $xpath->query('//base[@href]')->item(0);
        if ($baseNode instanceof \DOMElement) {
            return $baseNode->getAttribute('href');
        }

        $parts = parse_url($page->url);
        if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
            return $page->url;
        }

        $base = $parts['scheme'] . '://' . $parts['host'];
        if (!empty($parts['port'])) {
            $base .= ':' . $parts['port'];
        }
        return rtrim($base, '/') . '/';
    }

    private function resolveUrl(string $href, string $baseUrl): string
    {
        if (preg_match('#^https?://#i', $href)) {
            return $href;
        }

        if (str_starts_with($href, '//')) {
            $scheme = parse_url($baseUrl, PHP_URL_SCHEME) ?: 'http';
            return $scheme . ':' . $href;
        }

        return rtrim($baseUrl, '/') . '/' . ltrim($href, '/');
    }
}
