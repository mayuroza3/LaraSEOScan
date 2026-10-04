<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\SeoPage;
use App\Seo\Rules\NoindexNofollowRule;
use App\Seo\Rules\TwitterCardRule;
use App\Seo\Rules\OpenGraphCompleteRule;
use App\Seo\Rules\HtmlLangRule;
use App\Seo\Rules\ViewportRule;
use App\Seo\Rules\CharsetRule;
use App\Seo\Rules\FaviconRule;
use App\Seo\Rules\HreflangRule;
use App\Seo\Rules\InvalidHeadElementsRule;
use App\Seo\Rules\CanonicalValidationRule;

class Phase1MetaHeadRulesTest extends TestCase
{
    protected function createDom(string $html): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        return [$dom, $xpath];
    }

    public function test_noindex_nofollow_rule()
    {
        $rule = new NoindexNofollowRule();
        $page = new SeoPage(['url' => 'https://example.com']);
        list($dom, $xpath) = $this->createDom('<html><head><meta name="robots" content="noindex, nofollow"></head></html>');
        
        $issues = $rule->check($page, $dom, $xpath);
        $this->assertCount(2, $issues);
        $this->assertEquals('critical', $issues[0]['severity']);
    }

    public function test_twitter_card_rule()
    {
        $rule = new TwitterCardRule();
        $page = new SeoPage(['url' => 'https://example.com']);
        list($dom, $xpath) = $this->createDom('<html><head></head></html>');
        
        $issues = $rule->check($page, $dom, $xpath);
        $this->assertNotEmpty($issues);
        $this->assertEquals('medium', $issues[0]['severity']);
    }

    public function test_og_complete_rule()
    {
        $rule = new OpenGraphCompleteRule();
        $page = new SeoPage(['url' => 'https://example.com']);
        list($dom, $xpath) = $this->createDom('<html><head><meta property="og:title" content="Title"></head></html>');
        
        $issues = $rule->check($page, $dom, $xpath);
        $this->assertNotEmpty($issues);
        $this->assertContains('og:image', $issues[0]['context']['missing_tags']);
    }

    public function test_html_lang_rule()
    {
        $rule = new HtmlLangRule();
        $page = new SeoPage(['url' => 'https://example.com']);
        list($dom, $xpath) = $this->createDom('<html><head></head></html>');
        
        $issues = $rule->check($page, $dom, $xpath);
        $this->assertNotEmpty($issues);
        $this->assertEquals('high', $issues[0]['severity']);

        list($domValid, $xpathValid) = $this->createDom('<html lang="en"><head></head></html>');
        $this->assertEmpty($rule->check($page, $domValid, $xpathValid));
    }

    public function test_viewport_rule()
    {
        $rule = new ViewportRule();
        $page = new SeoPage(['url' => 'https://example.com']);
        list($dom, $xpath) = $this->createDom('<html><head></head></html>');
        
        $issues = $rule->check($page, $dom, $xpath);
        $this->assertNotEmpty($issues);
        $this->assertEquals('high', $issues[0]['severity']);
    }

    public function test_charset_rule()
    {
        $rule = new CharsetRule();
        $page = new SeoPage(['url' => 'https://example.com']);
        list($dom, $xpath) = $this->createDom('<html><head><meta charset="UTF-8"></head></html>');
        
        $this->assertEmpty($rule->check($page, $dom, $xpath));
    }

    public function test_favicon_rule()
    {
        $rule = new FaviconRule();
        $page = new SeoPage(['url' => 'https://example.com']);
        list($dom, $xpath) = $this->createDom('<html><head></head></html>');
        
        $issues = $rule->check($page, $dom, $xpath);
        $this->assertNotEmpty($issues);
    }

    public function test_invalid_head_elements_rule()
    {
        $rule = new InvalidHeadElementsRule();
        $page = new SeoPage(['url' => 'https://example.com']);
        list($dom, $xpath) = $this->createDom('<html><head><title>Test</title><invalid-div></invalid-div></head></html>');
        
        $issues = $rule->check($page, $dom, $xpath);
        $this->assertNotEmpty($issues);
        $this->assertEquals('high', $issues[0]['severity']);
    }

    public function test_canonical_validation_rule()
    {
        $rule = new CanonicalValidationRule();
        $page = new SeoPage(['url' => 'https://example.com']);
        list($dom, $xpath) = $this->createDom('<html><head><link rel="canonical" href="https://example.com"></head></html>');
        
        $this->assertEmpty($rule->check($page, $dom, $xpath));
    }
}
