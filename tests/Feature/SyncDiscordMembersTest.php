<?php

namespace Tests\Feature;

use App\Filament\Resources\Members\Pages\ListMembers;
use App\Models\Member;
use App\Models\OrgRole;
use App\Models\User;
use App\Services\DiscordService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class SyncDiscordMembersTest extends TestCase
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

    public function test_sync_assigns_org_role_when_discord_role_id_matches(): void
    {
        $this->actingAs($this->adminUser());

        $orgRole = OrgRole::create([
            'name'           => 'pilot',
            'label'          => 'Pilot',
            'discord_role_id' => '999888777',
            'sort_order'     => 1,
        ]);

        $this->mockDiscordService([[
            'discord_id' => '111222333',
            'username'   => 'starpilot',
            'nickname'   => 'Star Pilot',
            'avatar_url' => null,
            'role_ids'   => ['999888777'],
        ]]);

        Livewire::test(ListMembers::class)
            ->callAction('syncDiscordMembers');

        $this->assertDatabaseHas('members', [
            'discord_id'  => '111222333',
            'org_role_id' => $orgRole->id,
        ]);
    }

    public function test_sync_does_not_overwrite_org_role_when_no_match(): void
    {
        $this->actingAs($this->adminUser());

        $existingRole = OrgRole::create([
            'name'       => 'member',
            'label'      => 'Member',
            'sort_order' => 0,
        ]);

        $member = Member::create([
            'discord_id'  => '111222333',
            'name'        => 'Star Pilot',
            'handle'      => 'starpilot',
            'org_role_id' => $existingRole->id,
        ]);

        $this->mockDiscordService([[
            'discord_id' => '111222333',
            'username'   => 'starpilot',
            'nickname'   => 'Star Pilot',
            'avatar_url' => null,
            'role_ids'   => ['no-match-role-id'],
        ]]);

        Livewire::test(ListMembers::class)
            ->callAction('syncDiscordMembers');

        // org_role_id should remain unchanged because no discord_role_id matched
        $this->assertDatabaseHas('members', [
            'discord_id'  => '111222333',
            'org_role_id' => $existingRole->id,
        ]);
    }

    public function test_sync_updates_org_role_when_discord_role_id_matches_existing_member(): void
    {
        $this->actingAs($this->adminUser());

        $oldRole = OrgRole::create([
            'name'       => 'recruit',
            'label'      => 'Recruit',
            'sort_order' => 10,
        ]);

        $newRole = OrgRole::create([
            'name'           => 'officer',
            'label'          => 'Officer',
            'discord_role_id' => '777666555',
            'sort_order'     => 5,
        ]);

        Member::create([
            'discord_id'  => '444555666',
            'name'        => 'Commander',
            'handle'      => 'commander',
            'org_role_id' => $oldRole->id,
        ]);

        $this->mockDiscordService([[
            'discord_id' => '444555666',
            'username'   => 'commander',
            'nickname'   => 'Commander',
            'avatar_url' => null,
            'role_ids'   => ['777666555'],
        ]]);

        Livewire::test(ListMembers::class)
            ->callAction('syncDiscordMembers');

        $this->assertDatabaseHas('members', [
            'discord_id'  => '444555666',
            'org_role_id' => $newRole->id,
        ]);
    }

    public function test_sync_picks_highest_priority_org_role_when_multiple_discord_roles_match(): void
    {
        $this->actingAs($this->adminUser());

        OrgRole::create([
            'name'           => 'officer',
            'label'          => 'Officer',
            'discord_role_id' => 'discord-officer-id',
            'sort_order'     => 5,
        ]);

        $seniorRole = OrgRole::create([
            'name'           => 'commander',
            'label'          => 'Commander',
            'discord_role_id' => 'discord-commander-id',
            'sort_order'     => 1, // lower sort_order = higher priority
        ]);

        $this->mockDiscordService([[
            'discord_id' => '111222333',
            'username'   => 'starpilot',
            'nickname'   => null,
            'avatar_url' => null,
            'role_ids'   => ['discord-officer-id', 'discord-commander-id'],
        ]]);

        Livewire::test(ListMembers::class)
            ->callAction('syncDiscordMembers');

        $this->assertDatabaseHas('members', [
            'discord_id'  => '111222333',
            'org_role_id' => $seniorRole->id,
        ]);
    }

    public function test_sync_creates_new_member_with_null_org_role_when_no_role_matches(): void
    {
        $this->actingAs($this->adminUser());

        $this->mockDiscordService([[
            'discord_id' => '123456789',
            'username'   => 'newpilot',
            'nickname'   => null,
            'avatar_url' => null,
            'role_ids'   => [],
        ]]);

        Livewire::test(ListMembers::class)
            ->callAction('syncDiscordMembers');

        $this->assertDatabaseHas('members', [
            'discord_id'  => '123456789',
            'name'        => 'newpilot',
            'org_role_id' => null,
        ]);
    }
}
