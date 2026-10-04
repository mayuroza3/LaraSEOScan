<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\SeoPage;
use App\Seo\Rules\SsrfProtectionRule;
use App\Seo\Rules\HttpsAuditRule;
use App\Seo\Rules\RedirectChainRule;
use App\Seo\Rules\SecurityHeadersRule;
use App\Seo\Rules\TtfbRule;
use App\Seo\Rules\StatusCodeRule;
use App\Seo\Rules\HtmlSizeRule;
use App\Seo\Rules\JavascriptSizeRule;
use App\Seo\Rules\CssSizeRule;
use App\Seo\Rules\CompressionRule;

class Phase1SecurityPerformanceRulesTest extends TestCase
{
    protected function createDom(string $html): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        return [$dom, $xpath];
    }

    public function test_ssrf_protection_rule()
    {
        $rule = new SsrfProtectionRule();
        
        $safePage = new SeoPage(['url' => 'https://example.com']);
        list($dom, $xpath) = $this->createDom('<html></html>');
        $this->assertEmpty($rule->check($safePage, $dom, $xpath));

        $unsafePage = new SeoPage(['url' => 'http://169.254.169.254/latest']);
        $issues = $rule->check($unsafePage, $dom, $xpath);
        $this->assertNotEmpty($issues);
        $this->assertEquals('critical', $issues[0]['severity']);
    }

    public function test_https_audit_rule()
    {
        $rule = new HttpsAuditRule();
        
        $httpPage = new SeoPage(['url' => 'http://example.com']);
        list($dom, $xpath) = $this->createDom('<html></html>');
        $issues = $rule->check($httpPage, $dom, $xpath);
        $this->assertNotEmpty($issues);
        $this->assertEquals('critical', $issues[0]['severity']);

        $httpsPage = new SeoPage(['url' => 'https://example.com']);
        list($dom, $xpath) = $this->createDom('<html><body><img src="http://insecure.com/logo.png"></body></html>');
        $mixedIssues = $rule->check($httpsPage, $dom, $xpath);
        $this->assertNotEmpty($mixedIssues);
        $this->assertEquals('high', $mixedIssues[0]['severity']);
    }

    public function test_redirect_chain_rule()
    {
        $rule = new RedirectChainRule();
        
        $page = new SeoPage(['url' => 'https://example.com', 'redirect_count' => 4]);
        list($dom, $xpath) = $this->createDom('<html></html>');
        $issues = $rule->check($page, $dom, $xpath);
        $this->assertNotEmpty($issues);
        $this->assertEquals('high', $issues[0]['severity']);
    }

    public function test_security_headers_rule()
    {
        $rule = new SecurityHeadersRule();
        
        $page = new SeoPage(['url' => 'https://example.com', 'headers' => []]);
        list($dom, $xpath) = $this->createDom('<html></html>');
        $issues = $rule->check($page, $dom, $xpath);
        $this->assertCount(5, $issues);
    }

    public function test_ttfb_rule()
    {
        $rule = new TtfbRule();
        
        $fastPage = new SeoPage(['url' => 'https://example.com', 'ttfb_ms' => 150]);
        list($dom, $xpath) = $this->createDom('<html></html>');
        $this->assertEmpty($rule->check($fastPage, $dom, $xpath));

        $slowPage = new SeoPage(['url' => 'https://example.com', 'ttfb_ms' => 850]);
        $issues = $rule->check($slowPage, $dom, $xpath);
        $this->assertNotEmpty($issues);
        $this->assertEquals('medium', $issues[0]['severity']);
    }

    public function test_status_code_rule()
    {
        $rule = new StatusCodeRule();
        
        $page500 = new SeoPage(['url' => 'https://example.com', 'status_code' => 500]);
        list($dom, $xpath) = $this->createDom('<html></html>');
        $issues = $rule->check($page500, $dom, $xpath);
        $this->assertEquals('critical', $issues[0]['severity']);

        $page404 = new SeoPage(['url' => 'https://example.com', 'status_code' => 404]);
        $issues404 = $rule->check($page404, $dom, $xpath);
        $this->assertEquals('high', $issues404[0]['severity']);
    }

    public function test_compression_rule()
    {
        $rule = new CompressionRule();
        
        $compressedPage = new SeoPage(['url' => 'https://example.com', 'headers' => ['content-encoding' => 'gzip']]);
        list($dom, $xpath) = $this->createDom('<html></html>');
        $this->assertEmpty($rule->check($compressedPage, $dom, $xpath));

        $uncompressedPage = new SeoPage(['url' => 'https://example.com', 'headers' => []]);
        $issues = $rule->check($uncompressedPage, $dom, $xpath);
        $this->assertNotEmpty($issues);
        $this->assertEquals('high', $issues[0]['severity']);
    }
}
