<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamRole;
use App\Models\Unit;
use App\Models\User;
use App\Services\DiscordService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class TeamJoinRequestTest extends TestCase
{
    use RefreshDatabase;

    private function createUnit(): Unit
    {
        return Unit::create(['name' => 'Alpha Unit', 'sort_order' => 0]);
    }

    private function createTeam(Unit $unit, array $attributes = []): Team
    {
        return Team::create(array_merge([
            'unit_id'          => $unit->id,
            'name'             => 'Alpha Team',
            'sort_order'       => 0,
            'show_join_request' => false,
        ], $attributes));
    }

    private function createMember(array $attrs = []): Member
    {
        return Member::create(array_merge([
            'name'       => 'Test Pilot',
            'handle'     => 'testpilot',
            'sort_order' => 0,
        ], $attrs));
    }

    private function createTeamRole(Unit $unit, array $attrs = []): TeamRole
    {
        return TeamRole::create(array_merge([
            'unit_id'    => $unit->id,
            'name'       => 'pilot',
            'label'      => 'Pilot',
            'sort_order' => 0,
        ], $attrs));
    }

    // ── Visibility tests ─────────────────────────────────────────────────────

    public function test_request_to_join_button_not_shown_to_guests(): void
    {
        $unit = $this->createUnit();
        $this->createTeam($unit, ['show_join_request' => true]);

        $response = $this->get(route('hierarchy'));

        $response->assertStatus(200);
        $response->assertDontSee('Request to Join');
    }

    public function test_request_to_join_button_not_shown_when_toggle_disabled(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $unit = $this->createUnit();
        $this->createTeam($unit, ['show_join_request' => false]);

        $response = $this->actingAs($user)->get(route('hierarchy'));

        $response->assertStatus(200);
        $response->assertDontSee('Request to Join');
    }

    public function test_request_to_join_button_shown_to_authenticated_user_when_toggle_enabled(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $unit = $this->createUnit();
        $this->createTeam($unit, ['show_join_request' => true]);

        $response = $this->actingAs($user)->get(route('hierarchy'));

        $response->assertStatus(200);
        $response->assertSee('Request to Join');
    }

    // ── Route access tests ────────────────────────────────────────────────────

    public function test_unauthenticated_user_cannot_submit_join_request(): void
    {
        $unit = $this->createUnit();
        $team = $this->createTeam($unit, ['show_join_request' => true]);

        $response = $this->post(route('team.join-request', $team));

        $response->assertRedirect(route('filament.admin.auth.login'));
    }

    public function test_join_request_forbidden_when_toggle_disabled(): void
    {
        $user = User::factory()->create(['discord_id' => '111222333']);
        $unit = $this->createUnit();
        $team = $this->createTeam($unit, ['show_join_request' => false]);

        $response = $this->actingAs($user)->post(route('team.join-request', $team));

        $response->assertForbidden();
    }

    public function test_join_request_fails_when_user_has_no_discord_id(): void
    {
        $user = User::factory()->create(['discord_id' => null]);
        $unit = $this->createUnit();
        $team = $this->createTeam($unit, ['show_join_request' => true]);

        $response = $this->actingAs($user)->post(route('team.join-request', $team));

        $response->assertStatus(422);
    }

    public function test_join_request_redirects_with_error_when_team_has_no_owner_set(): void
    {
        $user = User::factory()->create(['discord_id' => '999888777']);
        $unit = $this->createUnit();
        $team = $this->createTeam($unit, ['show_join_request' => true]);
        // No owner_member_id set

        $response = $this->actingAs($user)->post(route('team.join-request', $team));

        $response->assertRedirect(route('hierarchy'));
        $response->assertSessionHas('join_request_error');
    }

    public function test_join_request_redirects_with_error_when_owner_has_no_discord_id(): void
    {
        $user        = User::factory()->create(['discord_id' => '999888777']);
        $unit        = $this->createUnit();
        $ownerMember = $this->createMember(['discord_id' => null, 'handle' => 'owner']);
        $team        = $this->createTeam($unit, ['show_join_request' => true, 'owner_member_id' => $ownerMember->id]);
        TeamMember::create(['team_id' => $team->id, 'member_id' => $ownerMember->id]);

        $response = $this->actingAs($user)->post(route('team.join-request', $team));

        $response->assertRedirect(route('hierarchy'));
        $response->assertSessionHas('join_request_error');
    }

    public function test_join_request_sends_dm_to_explicit_owner_and_redirects_with_success(): void
    {
        $user        = User::factory()->create(['discord_id' => '999888777']);
        $unit        = $this->createUnit();
        $ownerMember = $this->createMember(['discord_id' => '111222333', 'handle' => 'owner']);
        $team        = $this->createTeam($unit, ['show_join_request' => true, 'name' => 'Vanguard', 'owner_member_id' => $ownerMember->id]);
        TeamMember::create(['team_id' => $team->id, 'member_id' => $ownerMember->id]);

        $capturedMessage   = null;
        $capturedRecipient = null;

        $mock = Mockery::mock(DiscordService::class);
        $mock->shouldReceive('sendDirectMessage')
            ->once()
            ->withArgs(function (string $recipientId, string $message) use (&$capturedRecipient, &$capturedMessage) {
                $capturedRecipient = $recipientId;
                $capturedMessage   = $message;

                return true;
            });

        $this->app->instance(DiscordService::class, $mock);

        $response = $this->actingAs($user)->post(route('team.join-request', $team));

        $response->assertRedirect(route('hierarchy'));
        $response->assertSessionHas('join_request_success');

        $this->assertSame('111222333', $capturedRecipient);
        $this->assertStringContainsString('<@999888777>', $capturedMessage);
        $this->assertStringContainsString('Vanguard', $capturedMessage);
    }

    public function test_join_request_uses_explicit_owner_not_role_sort_order(): void
    {
        $user          = User::factory()->create(['discord_id' => '111000111']);
        $unit          = $this->createUnit();
        $leaderRole    = $this->createTeamRole($unit, ['name' => 'leader', 'label' => 'Leader', 'sort_order' => 0]);
        $memberRole    = TeamRole::create(['unit_id' => $unit->id, 'name' => 'grunt', 'label' => 'Grunt', 'sort_order' => 10]);
        // leaderMember has the highest-priority role but is NOT the designated owner
        $leaderMember  = $this->createMember(['discord_id' => 'leader-discord', 'handle' => 'leader']);
        // designatedOwner has a lower-priority role but IS explicitly set as the owner
        $designatedOwner = $this->createMember(['discord_id' => 'owner-discord', 'handle' => 'owner', 'name' => 'Owner']);

        $team = $this->createTeam($unit, ['show_join_request' => true, 'owner_member_id' => $designatedOwner->id]);

        TeamMember::create(['team_id' => $team->id, 'member_id' => $leaderMember->id, 'team_role_id' => $leaderRole->id, 'sort_order' => 0]);
        TeamMember::create(['team_id' => $team->id, 'member_id' => $designatedOwner->id, 'team_role_id' => $memberRole->id, 'sort_order' => 1]);

        $capturedRecipient = null;

        $mock = Mockery::mock(DiscordService::class);
        $mock->shouldReceive('sendDirectMessage')
            ->once()
            ->withArgs(function (string $recipientId) use (&$capturedRecipient) {
                $capturedRecipient = $recipientId;

                return true;
            });

        $this->app->instance(DiscordService::class, $mock);

        $this->actingAs($user)->post(route('team.join-request', $team));

        $this->assertSame('owner-discord', $capturedRecipient);
    }
}
