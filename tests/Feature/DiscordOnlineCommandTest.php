<?php

namespace Tests\Feature;

use App\Services\DiscordService;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class DiscordOnlineCommandTest extends TestCase
{
    public function test_command_fails_when_bot_token_is_not_configured(): void
    {
        config(['services.discord.bot_token' => null]);

        $this->artisan('app:discord-online')
            ->expectsOutput('DISCORD_BOT_TOKEN is not configured.')
            ->assertFailed();
    }

    public function test_command_fails_when_gateway_url_cannot_be_fetched(): void
    {
        config(['services.discord.bot_token' => 'some-token']);

        $mock = Mockery::mock(DiscordService::class);
        $mock->allows('getGatewayUrl')->andThrow(new \RuntimeException('DNS resolution failed'));
        $this->app->instance(DiscordService::class, $mock);

        $this->artisan('app:discord-online')
            ->expectsOutputToContain('Could not fetch Discord Gateway URL')
            ->assertFailed();
    }
}
