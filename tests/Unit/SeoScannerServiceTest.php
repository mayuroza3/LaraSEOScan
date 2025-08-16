<?php

namespace Tests\Unit;

use App\Models\SeoScan;
use App\Services\SeoScannerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SeoScannerServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_parses_title_and_description()
    {
        // Fake HTML response
        Http::fake([
            '*' => Http::response(
                '<html><head><title>SEO Title</title><meta name="description" content="SEO description"></head></html>'
            ),
        ]);

        $scan = SeoScan::factory()->create(['url' => 'https://example.com']);

        $service = new SeoScannerService;
        $service->scan($scan);

        $this->assertEquals('SEO Title', $scan->pages()->first()->title);
        $this->assertEquals('SEO description', $scan->pages()->first()->description);
    }
}
