<?php

namespace Tests\Feature;

use App\Services\DiscordService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DiscordServiceTest extends TestCase
{
    private function makeGuildMemberPayload(array $overrides = []): array
    {
        return array_merge([
            'user' => [
                'id'       => '111222333',
                'username' => 'starpilot',
                'avatar'   => 'abc123',
            ],
            'nick'   => 'Star Pilot',
            'avatar' => null,
            'roles'  => ['role-1', 'role-2'],
        ], $overrides);
    }

    public function test_get_guild_members_returns_mapped_data(): void
    {
        Http::fake([
            'discord.com/api/v10/guilds/*/members*' => Http::response([
                $this->makeGuildMemberPayload(),
            ], 200),
        ]);

        $service = new DiscordService();
        $members = $service->getGuildMembers();

        $this->assertCount(1, $members);
        $this->assertEquals('111222333', $members[0]['discord_id']);
        $this->assertEquals('starpilot', $members[0]['username']);
        $this->assertEquals('Star Pilot', $members[0]['nickname']);
        $this->assertStringContainsString('abc123', $members[0]['avatar_url']);
        $this->assertEquals(['role-1', 'role-2'], $members[0]['role_ids']);
    }

    public function test_get_guild_members_uses_guild_avatar_when_present(): void
    {
        $member = $this->makeGuildMemberPayload(['avatar' => 'guild_avatar_hash']);

        Http::fake([
            'discord.com/api/v10/guilds/*/members*' => Http::response([$member], 200),
        ]);

        config(['services.discord.guild_id' => 'test-guild']);

        $service = new DiscordService();
        $members = $service->getGuildMembers();

        $this->assertStringContainsString('guilds/test-guild/users', $members[0]['avatar_url']);
        $this->assertStringContainsString('guild_avatar_hash', $members[0]['avatar_url']);
    }

    public function test_get_guild_members_falls_back_to_user_avatar(): void
    {
        $member = $this->makeGuildMemberPayload(['avatar' => null]);

        Http::fake([
            'discord.com/api/v10/guilds/*/members*' => Http::response([$member], 200),
        ]);

        $service = new DiscordService();
        $members = $service->getGuildMembers();

        $this->assertStringContainsString('avatars/111222333/abc123', $members[0]['avatar_url']);
    }

    public function test_get_guild_members_returns_null_avatar_when_no_avatar(): void
    {
        $member = $this->makeGuildMemberPayload([
            'avatar' => null,
            'user'   => ['id' => '111222333', 'username' => 'starpilot', 'avatar' => null],
        ]);

        Http::fake([
            'discord.com/api/v10/guilds/*/members*' => Http::response([$member], 200),
        ]);

        $service = new DiscordService();
        $members = $service->getGuildMembers();

        $this->assertNull($members[0]['avatar_url']);
    }

    public function test_get_guild_members_uses_username_when_no_nickname(): void
    {
        $member = $this->makeGuildMemberPayload(['nick' => null]);

        Http::fake([
            'discord.com/api/v10/guilds/*/members*' => Http::response([$member], 200),
        ]);

        $service = new DiscordService();
        $members = $service->getGuildMembers();

        $this->assertNull($members[0]['nickname']);
        $this->assertEquals('starpilot', $members[0]['username']);
    }

    public function test_get_guild_members_skips_bot_users(): void
    {
        Http::fake([
            'discord.com/api/v10/guilds/*/members*' => Http::response([
                $this->makeGuildMemberPayload([
                    'user' => ['id' => '111222333', 'username' => 'starpilot', 'avatar' => null, 'bot' => true],
                ]),
                $this->makeGuildMemberPayload([
                    'user' => ['id' => '444555666', 'username' => 'humanpilot', 'avatar' => null],
                ]),
            ], 200),
        ]);

        $service = new DiscordService();
        $members = $service->getGuildMembers();

        $this->assertCount(1, $members);
        $this->assertEquals('444555666', $members[0]['discord_id']);
    }

    public function test_get_guild_members_skips_entries_without_user_id(): void
    {
        Http::fake([
            'discord.com/api/v10/guilds/*/members*' => Http::response([
                ['user' => [], 'nick' => null, 'avatar' => null, 'roles' => []],
            ], 200),
        ]);

        $service = new DiscordService();
        $members = $service->getGuildMembers();

        $this->assertEmpty($members);
    }

    public function test_get_guild_members_sends_bot_authorization_header(): void
    {
        Http::fake([
            'discord.com/api/v10/guilds/*/members*' => Http::response([], 200),
        ]);

        config(['services.discord.bot_token' => 'my-bot-token']);

        $service = new DiscordService();
        $service->getGuildMembers();

        Http::assertSent(function (Request $request) {
            return $request->hasHeader('Authorization', 'Bot my-bot-token');
        });
    }

    public function test_get_guild_roles_returns_keyed_array(): void
    {
        Http::fake([
            'discord.com/api/v10/guilds/*/roles' => Http::response([
                ['id' => 'role-1', 'name' => 'Admin', 'color' => 15158332],
                ['id' => 'role-2', 'name' => 'Member', 'color' => 0],
            ], 200),
        ]);

        $service = new DiscordService();
        $roles = $service->getGuildRoles();

        $this->assertArrayHasKey('role-1', $roles);
        $this->assertEquals('Admin', $roles['role-1']['name']);
        $this->assertEquals(15158332, $roles['role-1']['color']);
        $this->assertArrayHasKey('role-2', $roles);
    }

    public function test_get_gateway_url_returns_url_from_api(): void
    {
        Http::fake([
            'discord.com/api/v10/gateway/bot' => Http::response(['url' => 'wss://gateway.discord.gg'], 200),
        ]);

        $service = new DiscordService();
        $url = $service->getGatewayUrl();

        $this->assertEquals('wss://gateway.discord.gg', $url);
    }

    public function test_get_guild_members_skips_users_without_minimum_member_role(): void
    {
        config(['services.discord.minimum_member_role_id' => 'member-role-id']);

        Http::fake([
            'discord.com/api/v10/guilds/*/members*' => Http::response([
                // has the required role — should be included
                $this->makeGuildMemberPayload([
                    'user'  => ['id' => '111111111', 'username' => 'fullmember', 'avatar' => null],
                    'roles' => ['member-role-id', 'extra-role'],
                ]),
                // missing the required role — should be skipped
                $this->makeGuildMemberPayload([
                    'user'  => ['id' => '222222222', 'username' => 'visitor', 'avatar' => null],
                    'roles' => ['some-other-role'],
                ]),
            ], 200),
        ]);

        $service = new DiscordService();
        $members = $service->getGuildMembers();

        $this->assertCount(1, $members);
        $this->assertEquals('111111111', $members[0]['discord_id']);
    }

    public function test_get_guild_members_includes_all_users_when_minimum_role_not_configured(): void
    {
        // Default empty string — filter should be inactive
        config(['services.discord.minimum_member_role_id' => '']);

        Http::fake([
            'discord.com/api/v10/guilds/*/members*' => Http::response([
                $this->makeGuildMemberPayload([
                    'user'  => ['id' => '111111111', 'username' => 'pilot1', 'avatar' => null],
                    'roles' => [],
                ]),
                $this->makeGuildMemberPayload([
                    'user'  => ['id' => '222222222', 'username' => 'pilot2', 'avatar' => null],
                    'roles' => ['some-role'],
                ]),
            ], 200),
        ]);

        $service = new DiscordService();
        $members = $service->getGuildMembers();

        $this->assertCount(2, $members);
    }

    {
        $member = $this->makeGuildMemberPayload([
            'avatar' => null,
            'user'   => ['id' => '111222333', 'username' => 'starpilot', 'avatar' => 'a_animatedhash'],
        ]);

        Http::fake([
            'discord.com/api/v10/guilds/*/members*' => Http::response([$member], 200),
        ]);

        $service = new DiscordService();
        $members = $service->getGuildMembers();

        $this->assertStringEndsWith('.gif', $members[0]['avatar_url']);
    }
}
