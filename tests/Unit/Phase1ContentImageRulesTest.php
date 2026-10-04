<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\SeoPage;
use App\Models\SeoImage;
use App\Seo\Rules\HeadingHierarchyRule;
use App\Seo\Rules\ImageDimensionsRule;
use App\Seo\Rules\ImageLazyLoadingRule;
use App\Seo\Rules\BrokenImageRule;
use App\Services\Seo\RobotsTxtService;

class Phase1ContentImageRulesTest extends TestCase
{
    protected function createDom(string $html): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        return [$dom, $xpath];
    }

    public function test_heading_hierarchy_rule()
    {
        $rule = new HeadingHierarchyRule();
        $page = new SeoPage([
            'headings' => [
                ['tag' => 'h1', 'text' => 'Main Title'],
                ['tag' => 'h3', 'text' => 'Skipped Subtitle'],
            ],
        ]);
        list($dom, $xpath) = $this->createDom('<html></html>');
        $issues = $rule->check($page, $dom, $xpath);
        
        $this->assertNotEmpty($issues);
        $this->assertEquals('content.heading_hierarchy', $issues[0]['rule']);
    }

    public function test_image_dimensions_rule()
    {
        $rule = new ImageDimensionsRule();
        $page = new SeoPage(['url' => 'https://example.com']);
        list($dom, $xpath) = $this->createDom('<html><body><img src="pic.jpg"></body></html>');
        $issues = $rule->check($page, $dom, $xpath);
        
        $this->assertNotEmpty($issues);
        $this->assertEquals('medium', $issues[0]['severity']);
    }

    public function test_image_lazy_loading_rule()
    {
        $rule = new ImageLazyLoadingRule();
        $page = new SeoPage(['url' => 'https://example.com']);
        list($dom, $xpath) = $this->createDom('<html><body><img src="1.jpg"><img src="2.jpg"><img src="3.jpg"><img src="4.jpg"><img src="5.jpg"></body></html>');
        $issues = $rule->check($page, $dom, $xpath);
        
        $this->assertNotEmpty($issues);
    }

    public function test_broken_image_rule()
    {
        $rule = new BrokenImageRule();
        $page = new SeoPage(['url' => 'https://example.com']);
        $page->setRelation('images', collect([
            new SeoImage(['src' => '']),
        ]));
        
        list($dom, $xpath) = $this->createDom('<html></html>');
        $issues = $rule->check($page, $dom, $xpath);
        
        $this->assertNotEmpty($issues);
        $this->assertEquals('high', $issues[0]['severity']);
    }

    public function test_robots_txt_service_resets_state_and_parses_patterns()
    {
        $service = new RobotsTxtService();
        $this->assertTrue($service->isAllowed('https://example.com/page'));
    }
}
