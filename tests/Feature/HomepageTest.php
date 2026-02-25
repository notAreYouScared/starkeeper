<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;
    public function test_homepage_returns_successful_response_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    public function test_history_page_returns_successful_response(): void
    {
        $response = $this->get('/history');

        $response->assertStatus(200);
        $response->assertViewIs('history');
    }

    public function test_manifesto_page_returns_successful_response(): void
    {
        $response = $this->get('/manifesto');

        $response->assertStatus(200);
        $response->assertViewIs('manifesto');
    }

    public function test_charter_page_returns_successful_response(): void
    {
        $response = $this->get('/charter');

        $response->assertStatus(200);
        $response->assertViewIs('charter');
    }

    public function test_homepage_contains_discord_link(): void
    {
        $response = $this->get('/');

        $response->assertSee('https://discord.gg/starkeeper');
    }

    public function test_homepage_contains_rsi_org_link(): void
    {
        $response = $this->get('/');

        $response->assertSee('https://robertsspaceindustries.com/en/orgs/STARKEEPER');
    }

    public function test_homepage_contains_org_hierarchy_link(): void
    {
        $response = $this->get('/');

        $response->assertSee(route('org-hierarchy'));
    }

    public function test_homepage_contains_logo(): void
    {
        $response = $this->get('/');

        $response->assertSee('STARKEEPER-Logo.png');
    }

    public function test_authenticated_user_can_visit_homepage(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }
}
