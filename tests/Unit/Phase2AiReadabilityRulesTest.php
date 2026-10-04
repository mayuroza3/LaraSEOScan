<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\SeoPage;
use App\Seo\Rules\AiCrawlersRule;
use App\Seo\Rules\LlmsTxtRule;
use App\Seo\Rules\ContentLengthRule;
use App\Seo\Rules\LongSentencesRule;
use App\Seo\Rules\TransitionWordsRule;
use App\Seo\Rules\KeywordInTitleRule;
use App\Seo\Rules\KeywordInIntroRule;
use App\Seo\Rules\ModernImageFormatRule;

class Phase2AiReadabilityRulesTest extends TestCase
{
    protected function createDom(string $html): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        return [$dom, $xpath];
    }

    public function test_ai_crawlers_rule()
    {
        $rule = new AiCrawlersRule();
        $page = new SeoPage(['url' => 'https://example.com']);
        list($dom, $xpath) = $this->createDom('<html></html>');
        
        $issues = $rule->check($page, $dom, $xpath);
        $this->assertNotEmpty($issues);
        $this->assertEquals('info', $issues[0]['severity']);
    }

    public function test_llms_txt_rule()
    {
        $rule = new LlmsTxtRule();
        $page = new SeoPage(['url' => 'https://example.com']);
        list($dom, $xpath) = $this->createDom('<html><head></head></html>');
        
        $issues = $rule->check($page, $dom, $xpath);
        $this->assertNotEmpty($issues);
    }

    public function test_content_length_rule()
    {
        $rule = new ContentLengthRule();
        $page = new SeoPage(['url' => 'https://example.com', 'word_count' => 120]);
        list($dom, $xpath) = $this->createDom('<html><body>Short body text</body></html>');
        
        $issues = $rule->check($page, $dom, $xpath);
        $this->assertNotEmpty($issues);
        $this->assertEquals('medium', $issues[0]['severity']);
    }

    public function test_modern_image_format_rule()
    {
        $rule = new ModernImageFormatRule();
        $page = new SeoPage(['url' => 'https://example.com']);
        $page->setRelation('images', collect([
            new \App\Models\SeoImage(['src' => '1.png']),
            new \App\Models\SeoImage(['src' => '2.jpg']),
            new \App\Models\SeoImage(['src' => '3.jpeg']),
            new \App\Models\SeoImage(['src' => '4.png']),
        ]));

        list($dom, $xpath) = $this->createDom('<html></html>');
        $issues = $rule->check($page, $dom, $xpath);
        $this->assertNotEmpty($issues);
    }
}
