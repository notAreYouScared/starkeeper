<?php

namespace Tests\Feature;

use App\Filament\Pages\DiscordGuildPage;
use App\Models\Member;
use App\Models\User;
use App\Services\DiscordService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class DiscordGuildPageTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function mockDiscordService(array $members = [], array $roles = []): void
    {
        $mock = Mockery::mock(DiscordService::class);
        $mock->allows('getGuildMembers')->andReturn($members);
        $mock->allows('getGuildRoles')->andReturn($roles);
        $this->app->instance(DiscordService::class, $mock);
    }

    public function test_page_renders_empty_state_before_update(): void
    {
        $response = $this->actingAs($this->adminUser())
            ->get('/admin/discord-guild-page');

        $response->assertOk();
        $response->assertSee('Update from Discord');
    }

    public function test_import_member_creates_local_member(): void
    {
        $this->actingAs($this->adminUser());

        $discordId = '555666777';
        $username = 'galaxy_pilot';
        $nickname = 'Galaxy Pilot';
        $avatarUrl = 'https://cdn.discordapp.com/avatars/555666777/hash.png';

        \Livewire\Livewire::test(DiscordGuildPage::class)
            ->set('guildMembers', [[
                'discord_id' => $discordId,
                'username'   => $username,
                'nickname'   => $nickname,
                'avatar_url' => $avatarUrl,
                'role_ids'   => [],
            ]])
            ->call('importMember', $discordId);

        $this->assertDatabaseHas('members', [
            'discord_id' => $discordId,
            'name'       => $nickname,
            'handle'     => $username,
            'avatar_url' => $avatarUrl,
        ]);
    }

    public function test_import_member_uses_username_when_no_nickname(): void
    {
        $this->actingAs($this->adminUser());

        $discordId = '555666778';

        \Livewire\Livewire::test(DiscordGuildPage::class)
            ->set('guildMembers', [[
                'discord_id' => $discordId,
                'username'   => 'raw_user',
                'nickname'   => null,
                'avatar_url' => null,
                'role_ids'   => [],
            ]])
            ->call('importMember', $discordId);

        $this->assertDatabaseHas('members', [
            'discord_id' => $discordId,
            'name'       => 'raw_user',
        ]);
    }

    public function test_import_member_marks_discord_id_as_imported(): void
    {
        $this->actingAs($this->adminUser());

        $discordId = '555666779';

        $component = \Livewire\Livewire::test(DiscordGuildPage::class)
            ->set('guildMembers', [[
                'discord_id' => $discordId,
                'username'   => 'pilot_x',
                'nickname'   => null,
                'avatar_url' => null,
                'role_ids'   => [],
            ]])
            ->call('importMember', $discordId);

        $this->assertContains($discordId, $component->get('importedIds'));
    }

    public function test_import_already_imported_member_is_a_noop(): void
    {
        $this->actingAs($this->adminUser());

        $discordId = '555666780';

        Member::create([
            'discord_id' => $discordId,
            'name'       => 'Existing',
            'handle'     => 'existing_handle',
        ]);

        \Livewire\Livewire::test(DiscordGuildPage::class)
            ->set('guildMembers', [[
                'discord_id' => $discordId,
                'username'   => 'existing_handle',
                'nickname'   => 'Existing',
                'avatar_url' => null,
                'role_ids'   => [],
            ]])
            ->call('importMember', $discordId);

        $this->assertDatabaseCount('members', 1);
    }

    public function test_update_action_populates_guild_members(): void
    {
        $this->actingAs($this->adminUser());

        $this->mockDiscordService(
            members: [[
                'discord_id' => '123',
                'username'   => 'testuser',
                'nickname'   => 'Test User',
                'avatar_url' => null,
                'role_ids'   => [],
            ]],
            roles: ['role-1' => ['id' => 'role-1', 'name' => 'Pilot', 'color' => 0]]
        );

        $component = \Livewire\Livewire::test(DiscordGuildPage::class)
            ->callAction('updateFromDiscord');

        $this->assertCount(1, $component->get('guildMembers'));
        $this->assertArrayHasKey('role-1', $component->get('guildRoles'));
    }

    public function test_update_action_shows_danger_notification_on_failure(): void
    {
        $this->actingAs($this->adminUser());

        $mock = Mockery::mock(DiscordService::class);
        $mock->allows('getGuildRoles')->andThrow(new \RuntimeException('Connection refused'));
        $this->app->instance(DiscordService::class, $mock);

        \Livewire\Livewire::test(DiscordGuildPage::class)
            ->callAction('updateFromDiscord')
            ->assertNotified();
    }

    public function test_mount_preloads_existing_discord_ids(): void
    {
        $this->actingAs($this->adminUser());

        Member::create([
            'discord_id' => '999111222',
            'name'       => 'Pre-existing',
            'handle'     => 'preexisting',
        ]);

        $component = \Livewire\Livewire::test(DiscordGuildPage::class);

        $this->assertContains('999111222', $component->get('importedIds'));
    }
}
