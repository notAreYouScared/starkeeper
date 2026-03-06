<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamRole;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberProfileTeamsTest extends TestCase
{
    use RefreshDatabase;

    private function createMember(): Member
    {
        return Member::create([
            'name'       => 'Test Pilot',
            'handle'     => 'testpilot',
            'sort_order' => 0,
        ]);
    }

    private function createUnit(): Unit
    {
        return Unit::create(['name' => 'Alpha Unit', 'sort_order' => 0]);
    }

    private function createTeam(Unit $unit, array $attributes = []): Team
    {
        return Team::create(array_merge([
            'unit_id'    => $unit->id,
            'name'       => 'Alpha Team',
            'sort_order' => 0,
        ], $attributes));
    }

    private function createTeamRole(Unit $unit): TeamRole
    {
        return TeamRole::create([
            'unit_id'    => $unit->id,
            'name'       => 'pilot',
            'label'      => 'Pilot',
            'sort_order' => 0,
        ]);
    }

    public function test_member_profile_shows_teams_section_when_member_is_in_a_team(): void
    {
        $user   = User::factory()->create(['is_admin' => false]);
        $member = $this->createMember();
        $unit   = $this->createUnit();
        $team   = $this->createTeam($unit);

        TeamMember::create([
            'team_id'   => $team->id,
            'member_id' => $member->id,
        ]);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('Teams');
        $response->assertSee('Alpha Team');
    }

    public function test_member_profile_shows_team_role_label(): void
    {
        $user      = User::factory()->create(['is_admin' => false]);
        $member    = $this->createMember();
        $unit      = $this->createUnit();
        $team      = $this->createTeam($unit);
        $teamRole  = $this->createTeamRole($unit);

        TeamMember::create([
            'team_id'      => $team->id,
            'member_id'    => $member->id,
            'team_role_id' => $teamRole->id,
        ]);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('Pilot');
    }

    public function test_member_profile_shows_team_title(): void
    {
        $user   = User::factory()->create(['is_admin' => false]);
        $member = $this->createMember();
        $unit   = $this->createUnit();
        $team   = $this->createTeam($unit);

        TeamMember::create([
            'team_id'   => $team->id,
            'member_id' => $member->id,
            'title'     => 'Wing Commander',
        ]);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('Wing Commander');
    }

    public function test_member_profile_shows_all_teams_for_member_in_multiple_teams(): void
    {
        $user    = User::factory()->create(['is_admin' => false]);
        $member  = $this->createMember();
        $unit    = $this->createUnit();
        $teamA   = $this->createTeam($unit, ['name' => 'Alpha Team']);
        $teamB   = $this->createTeam($unit, ['name' => 'Beta Team']);

        TeamMember::create(['team_id' => $teamA->id, 'member_id' => $member->id]);
        TeamMember::create(['team_id' => $teamB->id, 'member_id' => $member->id]);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('Alpha Team');
        $response->assertSee('Beta Team');
    }

    public function test_member_profile_does_not_show_teams_section_when_member_is_in_no_teams(): void
    {
        $user   = User::factory()->create(['is_admin' => false]);
        $member = $this->createMember();

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertDontSee('Alpha Team');
        $response->assertDontSee('Teams');
    }

    public function test_member_profile_view_has_team_memberships_variable(): void
    {
        $user   = User::factory()->create(['is_admin' => false]);
        $member = $this->createMember();
        $unit   = $this->createUnit();
        $team   = $this->createTeam($unit);

        TeamMember::create(['team_id' => $team->id, 'member_id' => $member->id]);

        $response = $this->actingAs($user)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertViewHas('teamMemberships');
    }
}
