<?php

namespace Tests\Feature;

use App\Filament\Resources\Members\Pages\ListMembers;
use App\Models\Member;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamRole;
use App\Models\Unit;
use App\Models\User;
use App\Services\DiscordService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class SyncDiscordTeamMembershipsTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function mockDiscordService(array $members): void
    {
        $mock = Mockery::mock(DiscordService::class);
        $mock->allows('getGuildMembers')->andReturn($members);
        $this->app->instance(DiscordService::class, $mock);
    }

    // -------------------------------------------------------------------------
    // Team membership sync (Team.discord_role_id)
    // -------------------------------------------------------------------------

    public function test_sync_adds_new_member_to_team_when_discord_role_id_matches(): void
    {
        $this->actingAs($this->adminUser());

        $unit = Unit::create(['name' => 'Avenger Wing', 'sort_order' => 0]);
        $team = Team::create([
            'unit_id'         => $unit->id,
            'name'            => 'Alpha Squadron',
            'discord_role_id' => 'alpha-discord-role',
        ]);

        $this->mockDiscordService([[
            'discord_id' => '111222333',
            'username'   => 'pilot',
            'nickname'   => null,
            'avatar_url' => null,
            'role_ids'   => ['alpha-discord-role'],
        ]]);

        Livewire::test(ListMembers::class)
            ->callAction('syncDiscordMembers');

        $member = Member::where('discord_id', '111222333')->first();
        $this->assertNotNull($member);
        $this->assertDatabaseHas('team_members', [
            'team_id'   => $team->id,
            'member_id' => $member->id,
        ]);
    }

    public function test_sync_adds_existing_member_to_team_when_discord_role_id_matches(): void
    {
        $this->actingAs($this->adminUser());

        $unit   = Unit::create(['name' => 'Avenger Wing', 'sort_order' => 0]);
        $team   = Team::create([
            'unit_id'         => $unit->id,
            'name'            => 'Alpha Squadron',
            'discord_role_id' => 'alpha-discord-role',
        ]);
        $member = Member::create([
            'discord_id' => '111222333',
            'name'       => 'Pilot',
            'handle'     => 'pilot',
        ]);

        $this->mockDiscordService([[
            'discord_id' => '111222333',
            'username'   => 'pilot',
            'nickname'   => null,
            'avatar_url' => null,
            'role_ids'   => ['alpha-discord-role'],
        ]]);

        Livewire::test(ListMembers::class)
            ->callAction('syncDiscordMembers');

        $this->assertDatabaseHas('team_members', [
            'team_id'   => $team->id,
            'member_id' => $member->id,
        ]);
    }

    public function test_sync_does_not_add_member_to_team_when_no_discord_role_matches(): void
    {
        $this->actingAs($this->adminUser());

        $unit = Unit::create(['name' => 'Avenger Wing', 'sort_order' => 0]);
        $team = Team::create([
            'unit_id'         => $unit->id,
            'name'            => 'Alpha Squadron',
            'discord_role_id' => 'alpha-discord-role',
        ]);

        $this->mockDiscordService([[
            'discord_id' => '111222333',
            'username'   => 'pilot',
            'nickname'   => null,
            'avatar_url' => null,
            'role_ids'   => ['some-other-role'],
        ]]);

        Livewire::test(ListMembers::class)
            ->callAction('syncDiscordMembers');

        $this->assertDatabaseMissing('team_members', ['team_id' => $team->id]);
    }

    public function test_sync_does_not_create_duplicate_team_member_records(): void
    {
        $this->actingAs($this->adminUser());

        $unit   = Unit::create(['name' => 'Avenger Wing', 'sort_order' => 0]);
        $team   = Team::create([
            'unit_id'         => $unit->id,
            'name'            => 'Alpha Squadron',
            'discord_role_id' => 'alpha-discord-role',
        ]);
        $member = Member::create([
            'discord_id' => '111222333',
            'name'       => 'Pilot',
            'handle'     => 'pilot',
        ]);
        TeamMember::create(['team_id' => $team->id, 'member_id' => $member->id]);

        $this->mockDiscordService([[
            'discord_id' => '111222333',
            'username'   => 'pilot',
            'nickname'   => null,
            'avatar_url' => null,
            'role_ids'   => ['alpha-discord-role'],
        ]]);

        Livewire::test(ListMembers::class)
            ->callAction('syncDiscordMembers');

        $this->assertSame(
            1,
            TeamMember::where('team_id', $team->id)->where('member_id', $member->id)->count()
        );
    }

    public function test_sync_adds_member_to_multiple_teams_when_multiple_discord_roles_match(): void
    {
        $this->actingAs($this->adminUser());

        $unit  = Unit::create(['name' => 'Avenger Wing', 'sort_order' => 0]);
        $teamA = Team::create([
            'unit_id'         => $unit->id,
            'name'            => 'Alpha Squadron',
            'discord_role_id' => 'alpha-role',
        ]);
        $teamB = Team::create([
            'unit_id'         => $unit->id,
            'name'            => 'Bravo Squadron',
            'discord_role_id' => 'bravo-role',
        ]);

        $this->mockDiscordService([[
            'discord_id' => '111222333',
            'username'   => 'pilot',
            'nickname'   => null,
            'avatar_url' => null,
            'role_ids'   => ['alpha-role', 'bravo-role'],
        ]]);

        Livewire::test(ListMembers::class)
            ->callAction('syncDiscordMembers');

        $member = Member::where('discord_id', '111222333')->first();
        $this->assertDatabaseHas('team_members', ['team_id' => $teamA->id, 'member_id' => $member->id]);
        $this->assertDatabaseHas('team_members', ['team_id' => $teamB->id, 'member_id' => $member->id]);
    }

    // -------------------------------------------------------------------------
    // Team role sync (TeamRole.discord_role_id)
    // -------------------------------------------------------------------------

    public function test_sync_updates_team_role_on_existing_team_member_when_discord_role_id_matches(): void
    {
        $this->actingAs($this->adminUser());

        $unit     = Unit::create(['name' => 'Avenger Wing', 'sort_order' => 0]);
        $team     = Team::create(['unit_id' => $unit->id, 'name' => 'Alpha Squadron']);
        $teamRole = TeamRole::create([
            'unit_id'         => $unit->id,
            'name'            => 'pilot',
            'label'           => 'Pilot',
            'sort_order'      => 1,
            'discord_role_id' => 'pilot-discord-role',
        ]);
        $member = Member::create([
            'discord_id' => '111222333',
            'name'       => 'Pilot',
            'handle'     => 'pilot',
        ]);
        TeamMember::create(['team_id' => $team->id, 'member_id' => $member->id]);

        $this->mockDiscordService([[
            'discord_id' => '111222333',
            'username'   => 'pilot',
            'nickname'   => null,
            'avatar_url' => null,
            'role_ids'   => ['pilot-discord-role'],
        ]]);

        Livewire::test(ListMembers::class)
            ->callAction('syncDiscordMembers');

        $this->assertDatabaseHas('team_members', [
            'team_id'      => $team->id,
            'member_id'    => $member->id,
            'team_role_id' => $teamRole->id,
        ]);
    }

    public function test_sync_does_not_update_team_role_when_no_discord_role_matches(): void
    {
        $this->actingAs($this->adminUser());

        $unit     = Unit::create(['name' => 'Avenger Wing', 'sort_order' => 0]);
        $team     = Team::create(['unit_id' => $unit->id, 'name' => 'Alpha Squadron']);
        $teamRole = TeamRole::create([
            'unit_id'         => $unit->id,
            'name'            => 'pilot',
            'label'           => 'Pilot',
            'sort_order'      => 1,
            'discord_role_id' => 'pilot-discord-role',
        ]);
        $member = Member::create([
            'discord_id' => '111222333',
            'name'       => 'Pilot',
            'handle'     => 'pilot',
        ]);
        TeamMember::create(['team_id' => $team->id, 'member_id' => $member->id, 'team_role_id' => $teamRole->id]);

        $this->mockDiscordService([[
            'discord_id' => '111222333',
            'username'   => 'pilot',
            'nickname'   => null,
            'avatar_url' => null,
            'role_ids'   => ['some-other-role'],
        ]]);

        Livewire::test(ListMembers::class)
            ->callAction('syncDiscordMembers');

        // team_role_id should remain unchanged
        $this->assertDatabaseHas('team_members', [
            'team_id'      => $team->id,
            'member_id'    => $member->id,
            'team_role_id' => $teamRole->id,
        ]);
    }

    public function test_sync_picks_lowest_sort_order_team_role_when_multiple_match_in_same_unit(): void
    {
        $this->actingAs($this->adminUser());

        $unit       = Unit::create(['name' => 'Avenger Wing', 'sort_order' => 0]);
        $team       = Team::create(['unit_id' => $unit->id, 'name' => 'Alpha Squadron']);
        $juniorRole = TeamRole::create([
            'unit_id'         => $unit->id,
            'name'            => 'pilot',
            'label'           => 'Pilot',
            'sort_order'      => 10,
            'discord_role_id' => 'pilot-discord-role',
        ]);
        $seniorRole = TeamRole::create([
            'unit_id'         => $unit->id,
            'name'            => 'commander',
            'label'           => 'Commander',
            'sort_order'      => 1,
            'discord_role_id' => 'commander-discord-role',
        ]);
        $member = Member::create([
            'discord_id' => '111222333',
            'name'       => 'Commander',
            'handle'     => 'commander',
        ]);
        TeamMember::create(['team_id' => $team->id, 'member_id' => $member->id]);

        $this->mockDiscordService([[
            'discord_id' => '111222333',
            'username'   => 'commander',
            'nickname'   => null,
            'avatar_url' => null,
            'role_ids'   => ['pilot-discord-role', 'commander-discord-role'],
        ]]);

        Livewire::test(ListMembers::class)
            ->callAction('syncDiscordMembers');

        $this->assertDatabaseHas('team_members', [
            'team_id'      => $team->id,
            'member_id'    => $member->id,
            'team_role_id' => $seniorRole->id,
        ]);
    }

    public function test_sync_only_updates_team_role_for_teams_in_matching_unit(): void
    {
        $this->actingAs($this->adminUser());

        $unitA    = Unit::create(['name' => 'Avenger Wing', 'sort_order' => 0]);
        $unitB    = Unit::create(['name' => 'Mining Division', 'sort_order' => 1]);
        $teamA    = Team::create(['unit_id' => $unitA->id, 'name' => 'Alpha Squadron']);
        $teamB    = Team::create(['unit_id' => $unitB->id, 'name' => 'Mining Team']);
        $roleForA = TeamRole::create([
            'unit_id'         => $unitA->id,
            'name'            => 'pilot',
            'label'           => 'Pilot',
            'sort_order'      => 1,
            'discord_role_id' => 'pilot-discord-role',
        ]);
        $member = Member::create([
            'discord_id' => '111222333',
            'name'       => 'Pilot',
            'handle'     => 'pilot',
        ]);
        TeamMember::create(['team_id' => $teamA->id, 'member_id' => $member->id]);
        TeamMember::create(['team_id' => $teamB->id, 'member_id' => $member->id]);

        $this->mockDiscordService([[
            'discord_id' => '111222333',
            'username'   => 'pilot',
            'nickname'   => null,
            'avatar_url' => null,
            'role_ids'   => ['pilot-discord-role'],
        ]]);

        Livewire::test(ListMembers::class)
            ->callAction('syncDiscordMembers');

        // Unit A team member gets the role
        $this->assertDatabaseHas('team_members', [
            'team_id'      => $teamA->id,
            'member_id'    => $member->id,
            'team_role_id' => $roleForA->id,
        ]);
        // Unit B team member is NOT updated
        $this->assertDatabaseHas('team_members', [
            'team_id'      => $teamB->id,
            'member_id'    => $member->id,
            'team_role_id' => null,
        ]);
    }

    // -------------------------------------------------------------------------
    // Combined: team membership + team role assigned in the same sync
    // -------------------------------------------------------------------------

    public function test_sync_adds_to_team_and_assigns_team_role_in_single_pass(): void
    {
        $this->actingAs($this->adminUser());

        $unit     = Unit::create(['name' => 'Avenger Wing', 'sort_order' => 0]);
        $team     = Team::create([
            'unit_id'         => $unit->id,
            'name'            => 'Alpha Squadron',
            'discord_role_id' => 'alpha-discord-role',
        ]);
        $teamRole = TeamRole::create([
            'unit_id'         => $unit->id,
            'name'            => 'pilot',
            'label'           => 'Pilot',
            'sort_order'      => 1,
            'discord_role_id' => 'pilot-discord-role',
        ]);

        $this->mockDiscordService([[
            'discord_id' => '111222333',
            'username'   => 'pilot',
            'nickname'   => null,
            'avatar_url' => null,
            'role_ids'   => ['alpha-discord-role', 'pilot-discord-role'],
        ]]);

        Livewire::test(ListMembers::class)
            ->callAction('syncDiscordMembers');

        $member = Member::where('discord_id', '111222333')->first();
        $this->assertNotNull($member);
        $this->assertDatabaseHas('team_members', [
            'team_id'      => $team->id,
            'member_id'    => $member->id,
            'team_role_id' => $teamRole->id,
        ]);
    }
}
