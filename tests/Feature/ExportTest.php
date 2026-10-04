<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\SeoScan;
use App\Models\SeoPage;
use App\Models\SeoIssue;

class ExportTest extends TestCase
{
    public function test_export_pdf_returns_success()
    {
        $user = User::factory()->create();
        $scan = SeoScan::factory()->for($user)->create([
            'status' => 'COMPLETED',
            'score' => 88,
        ]);
        $page = SeoPage::factory()->for($scan)->create(['url' => 'https://example.com']);
        SeoIssue::create([
            'seo_page_id' => $page->id,
            'rule_key' => 'security.https',
            'severity' => 'critical',
            'message' => 'Unencrypted HTTP protocol used',
        ]);

        $response = $this->actingAs($user)->get("/scan/{$scan->uuid}/export/pdf");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_export_csv_returns_success()
    {
        $user = User::factory()->create();
        $scan = SeoScan::factory()->for($user)->create([
            'status' => 'COMPLETED',
            'score' => 95,
        ]);
        $page = SeoPage::factory()->for($scan)->create(['url' => 'https://example.com']);

        $response = $this->actingAs($user)->get("/scan/{$scan->uuid}/export/csv");

        $response->assertStatus(200);
    }
}
