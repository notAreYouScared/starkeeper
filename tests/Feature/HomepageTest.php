<?php

namespace Tests\Feature;

use App\Models\Member;
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

    public function test_authenticated_user_with_linked_member_sees_my_profile_link(): void
    {
        $user = User::factory()->create(['is_admin' => false, 'discord_id' => 'discord-abc']);
        $member = Member::create(['name' => 'Test Member', 'handle' => 'testmember', 'discord_id' => 'discord-abc', 'sort_order' => 0]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('My Profile');
        $response->assertSee(route('member.profile', $member), false);
    }

    public function test_authenticated_user_without_linked_member_does_not_see_my_profile_link(): void
    {
        $user = User::factory()->create(['is_admin' => false, 'discord_id' => 'discord-no-member']);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('My Profile');
    }

    public function test_homepage_contains_disclaimer(): void
    {
        $response = $this->get('/');

        $response->assertSee('This is an unofficial fansite');
        $response->assertSee('Cloud Imperium Rights LLC');
    }

    public function test_history_page_contains_disclaimer(): void
    {
        $response = $this->get('/history');

        $response->assertSee('This is an unofficial fansite');
    }

    public function test_manifesto_page_contains_disclaimer(): void
    {
        $response = $this->get('/manifesto');

        $response->assertSee('This is an unofficial fansite');
    }

    public function test_charter_page_contains_disclaimer(): void
    {
        $response = $this->get('/charter');

        $response->assertSee('This is an unofficial fansite');
    }

    public function test_guest_does_not_see_my_profile_link(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('My Profile');
    }
}
