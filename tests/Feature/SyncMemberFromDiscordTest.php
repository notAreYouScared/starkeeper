<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\OrgRole;
use App\Models\User;
use App\Services\DiscordService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SyncMemberFromDiscordTest extends TestCase
{
    use RefreshDatabase;

    private function createMember(array $attrs = []): Member
    {
        return Member::create(array_merge([
            'name'       => 'Test Pilot',
            'handle'     => 'testpilot',
            'discord_id' => '111222333',
            'sort_order' => 0,
        ], $attrs));
    }

    private function mockDiscordService(?array $memberData): void
    {
        $mock = Mockery::mock(DiscordService::class);
        $mock->allows('getGuildMember')->andReturn($memberData);
        $this->app->instance(DiscordService::class, $mock);
    }

    private function discordMember(array $overrides = []): array
    {
        return array_merge([
            'discord_id'   => '111222333',
            'username'     => 'testpilot',
            'display_name' => null,
            'nickname'     => null,
            'avatar_url'   => 'https://cdn.discordapp.com/avatars/111222333/abc.png',
            'role_ids'     => [],
        ], $overrides);
    }

    public function test_non_admin_cannot_sync_member_from_discord(): void
    {
        $user   = User::factory()->create(['is_admin' => false]);
        $member = $this->createMember();

        $response = $this->actingAs($user)
            ->post(route('member.sync-discord', $member));

        $response->assertForbidden();
    }

    public function test_sync_button_shown_for_admin_when_member_has_discord_id(): void
    {
        $admin  = User::factory()->create(['is_admin' => true]);
        $member = $this->createMember(['discord_id' => '111222333']);

        $response = $this->actingAs($admin)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertSee('Update from Discord');
        $response->assertSee(route('member.sync-discord', $member), false);
    }

    public function test_sync_button_hidden_when_member_has_no_discord_id(): void
    {
        $admin  = User::factory()->create(['is_admin' => true]);
        $member = $this->createMember(['discord_id' => null]);

        $response = $this->actingAs($admin)->get(route('member.profile', $member));

        $response->assertStatus(200);
        $response->assertDontSee('Update from Discord');
    }

    public function test_sync_updates_name_from_server_nickname(): void
    {
        $admin  = User::factory()->create(['is_admin' => true]);
        $member = $this->createMember();
        $this->mockDiscordService($this->discordMember([
            'nickname'     => 'Galaxy Commander',
            'display_name' => 'Global Name',
            'username'     => 'testpilot',
        ]));

        $this->actingAs($admin)->post(route('member.sync-discord', $member));

        $this->assertDatabaseHas('members', ['id' => $member->id, 'name' => 'Galaxy Commander']);
    }

    public function test_sync_falls_back_to_display_name_when_no_nickname(): void
    {
        $admin  = User::factory()->create(['is_admin' => true]);
        $member = $this->createMember();
        $this->mockDiscordService($this->discordMember([
            'nickname'     => null,
            'display_name' => 'Global Name',
            'username'     => 'testpilot',
        ]));

        $this->actingAs($admin)->post(route('member.sync-discord', $member));

        $this->assertDatabaseHas('members', ['id' => $member->id, 'name' => 'Global Name']);
    }

    public function test_sync_falls_back_to_username_when_no_nickname_or_display_name(): void
    {
        $admin  = User::factory()->create(['is_admin' => true]);
        $member = $this->createMember(['name' => 'Old Name']);
        $this->mockDiscordService($this->discordMember([
            'nickname'     => null,
            'display_name' => null,
            'username'     => 'testpilot',
        ]));

        $this->actingAs($admin)->post(route('member.sync-discord', $member));

        $this->assertDatabaseHas('members', ['id' => $member->id, 'name' => 'testpilot']);
    }

    public function test_sync_updates_avatar_url(): void
    {
        $admin  = User::factory()->create(['is_admin' => true]);
        $member = $this->createMember(['avatar_url' => 'https://old.url/avatar.png']);
        $this->mockDiscordService($this->discordMember([
            'avatar_url' => 'https://cdn.discordapp.com/avatars/111222333/new.png',
        ]));

        $this->actingAs($admin)->post(route('member.sync-discord', $member));

        $this->assertDatabaseHas('members', [
            'id'         => $member->id,
            'avatar_url' => 'https://cdn.discordapp.com/avatars/111222333/new.png',
        ]);
    }

    public function test_sync_assigns_org_role_from_discord_roles(): void
    {
        $admin  = User::factory()->create(['is_admin' => true]);
        $member = $this->createMember();

        $orgRole = OrgRole::create([
            'name'             => 'officer',
            'label'            => 'Officer',
            'discord_role_ids' => ['role-abc'],
            'sort_order'       => 1,
        ]);

        $this->mockDiscordService($this->discordMember(['role_ids' => ['role-abc']]));

        $this->actingAs($admin)->post(route('member.sync-discord', $member));

        $this->assertDatabaseHas('members', ['id' => $member->id, 'org_role_id' => $orgRole->id]);
    }

    public function test_sync_does_not_overwrite_org_role_when_no_discord_role_matches(): void
    {
        $admin  = User::factory()->create(['is_admin' => true]);

        $existingRole = OrgRole::create([
            'name'       => 'member',
            'label'      => 'Member',
            'sort_order' => 0,
        ]);

        $member = $this->createMember(['org_role_id' => $existingRole->id]);
        $this->mockDiscordService($this->discordMember(['role_ids' => ['no-match']]));

        $this->actingAs($admin)->post(route('member.sync-discord', $member));

        $this->assertDatabaseHas('members', ['id' => $member->id, 'org_role_id' => $existingRole->id]);
    }

    public function test_sync_redirects_back_with_success_status(): void
    {
        $admin  = User::factory()->create(['is_admin' => true]);
        $member = $this->createMember();
        $this->mockDiscordService($this->discordMember());

        $response = $this->actingAs($admin)->post(route('member.sync-discord', $member));

        $response->assertRedirect(route('member.profile', $member));
        $response->assertSessionHas('status', 'Member synced from Discord successfully.');
    }

    public function test_sync_redirects_with_message_when_member_not_in_guild(): void
    {
        $admin  = User::factory()->create(['is_admin' => true]);
        $member = $this->createMember();
        $this->mockDiscordService(null);

        $response = $this->actingAs($admin)->post(route('member.sync-discord', $member));

        $response->assertRedirect(route('member.profile', $member));
        $response->assertSessionHas('status', 'Member not found in the Discord guild.');
    }

    public function test_sync_returns_422_when_member_has_no_discord_id(): void
    {
        $admin  = User::factory()->create(['is_admin' => true]);
        $member = $this->createMember(['discord_id' => null]);

        $response = $this->actingAs($admin)->post(route('member.sync-discord', $member));

        $response->assertStatus(422);
    }
}
