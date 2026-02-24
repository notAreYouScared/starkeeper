<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class DiscordCallbackTest extends TestCase
{
    use RefreshDatabase;

    private function mockDiscordUser(array $attributes = []): SocialiteUser
    {
        $discordUser = Mockery::mock(SocialiteUser::class);
        $discordUser->allows('getId')->andReturn($attributes['id'] ?? '123456789');
        $discordUser->allows('getName')->andReturn($attributes['name'] ?? 'Test User');
        $discordUser->allows('getNickname')->andReturn($attributes['nickname'] ?? 'testuser');
        $discordUser->allows('getEmail')->andReturn($attributes['email'] ?? 'test@example.com');
        $discordUser->allows('getAvatar')->andReturn($attributes['avatar'] ?? 'https://cdn.discordapp.com/avatars/123456789/avatar.png');

        return $discordUser;
    }

    private function mockSocialite(SocialiteUser $discordUser): void
    {
        $provider = Mockery::mock(\Laravel\Socialite\Two\AbstractProvider::class);
        $provider->allows('user')->andReturn($discordUser);

        $socialite = Mockery::mock(SocialiteFactory::class);
        $socialite->allows('driver')->with('discord')->andReturn($provider);

        $this->app->instance(SocialiteFactory::class, $socialite);
    }

    public function test_discord_callback_creates_member(): void
    {
        $discordUser = $this->mockDiscordUser();
        $this->mockSocialite($discordUser);

        $this->get(route('auth.discord.callback'));

        $this->assertDatabaseHas('members', [
            'discord_id'  => '123456789',
            'name'        => 'Test User',
            'handle'      => 'testuser',
            'avatar_url'  => 'https://cdn.discordapp.com/avatars/123456789/avatar.png',
            'profile_url' => 'discord://-/users/123456789',
        ]);
    }

    public function test_discord_callback_updates_existing_member(): void
    {
        Member::create([
            'discord_id'  => '123456789',
            'name'        => 'Old Name',
            'handle'      => 'testuser',
            'avatar_url'  => 'https://cdn.discordapp.com/avatars/123456789/old.png',
            'profile_url' => 'discord://-/users/123456789',
        ]);

        $discordUser = $this->mockDiscordUser([
            'name'   => 'New Name',
            'avatar' => 'https://cdn.discordapp.com/avatars/123456789/new.png',
        ]);
        $this->mockSocialite($discordUser);

        $this->get(route('auth.discord.callback'));

        $this->assertDatabaseCount('members', 1);
        $this->assertDatabaseHas('members', [
            'discord_id'  => '123456789',
            'name'        => 'New Name',
            'avatar_url'  => 'https://cdn.discordapp.com/avatars/123456789/new.png',
            'profile_url' => 'discord://-/users/123456789',
        ]);
    }

    public function test_discord_callback_creates_user(): void
    {
        $discordUser = $this->mockDiscordUser();
        $this->mockSocialite($discordUser);

        $this->get(route('auth.discord.callback'));

        $this->assertDatabaseHas('users', [
            'discord_id' => '123456789',
            'name'       => 'Test User',
        ]);
    }
}
