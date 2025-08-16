<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SeoScan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoScanControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_only_shows_scans_belonging_to_logged_in_user()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $userScan = SeoScan::factory()->for($user)->create();
        $otherScan = SeoScan::factory()->for($otherUser)->create();

        $response = $this->actingAs($user)->get('/scan');

        $response->assertSee($userScan->url);
        $response->assertDontSee($otherScan->url);
    }
}
