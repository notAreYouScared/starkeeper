<?php

namespace App\Console\Commands;

use App\Services\DiscordService;
use Illuminate\Console\Command;
use WebSocket\Client;
use WebSocket\TimeoutException;

class DiscordOnlineCommand extends Command
{
    protected $signature = 'app:discord-online';

    protected $description = 'Connect to the Discord Gateway and keep the bot\'s status as online';

    /** Discord Gateway op-codes */
    private const OP_DISPATCH   = 0;
    private const OP_HEARTBEAT  = 1;
    private const OP_IDENTIFY   = 2;
    private const OP_RECONNECT  = 7;
    private const OP_HELLO      = 10;
    private const OP_HEARTBEAT_ACK = 11;

    public function handle(DiscordService $discord): int
    {
        $token = config('services.discord.bot_token');

        if (blank($token)) {
            $this->error('DISCORD_BOT_TOKEN is not configured.');

            return self::FAILURE;
        }

        $this->info('Fetching Gateway URL…');

        try {
            $gatewayUrl = $discord->getGatewayUrl();
        } catch (\Throwable $e) {
            $this->error('Could not fetch Discord Gateway URL: ' . $e->getMessage());

            return self::FAILURE;
        }

        $url = $gatewayUrl . '?v=10&encoding=json';

        $this->info("Connecting to Gateway: {$url}");

        // Allow graceful shutdown via SIGTERM / SIGINT (Ctrl+C / Supervisor stop)
        $running = true;
        if (function_exists('pcntl_signal')) {
            $stop = static function () use (&$running): void { $running = false; };
            pcntl_signal(SIGTERM, $stop);
            pcntl_signal(SIGINT, $stop);
        }

        while ($running) {
            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }

            try {
                $this->runGatewaySession($url, $token, $running);
            } catch (\Throwable $e) {
                $this->warn('Gateway session ended: ' . $e->getMessage());
            }

            if ($running) {
                $this->info('Reconnecting in 5 seconds…');
                sleep(5);
            }
        }

        $this->info('Shutting down gracefully.');

        return self::SUCCESS;
    }

    /**
     * Run a single Gateway session until it ends, fails, or shutdown is requested.
     * The $running flag is passed by reference so the outer loop can stop cleanly.
     */
    private function runGatewaySession(string $url, string $token, bool &$running): void
    {
        $client = new Client($url, [
            'timeout'   => 60,
            'persistent' => true,
        ]);

        $heartbeatInterval = null;
        $lastHeartbeatAck  = true;
        $lastHeartbeatAt   = 0;

        while ($running) {
            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }

            // Send heartbeat if due
            if ($heartbeatInterval !== null) {
                $now = (int) (microtime(true) * 1000);
                if (($now - $lastHeartbeatAt) >= $heartbeatInterval) {
                    if (! $lastHeartbeatAck) {
                        $this->warn('Heartbeat not acknowledged — reconnecting…');
                        $client->close();

                        return;
                    }

                    $client->text(json_encode(['op' => self::OP_HEARTBEAT, 'd' => null]));
                    $lastHeartbeatAck = false;
                    $lastHeartbeatAt  = $now;
                }
            }

            // Read with a short timeout so heartbeat loop stays accurate
            try {
                $client->setTimeout(1);
                $raw = $client->receive();
            } catch (TimeoutException) {
                continue;
            }

            if ($raw === null) {
                break;
            }

            $payload = json_decode($raw, true);

            if (! is_array($payload)) {
                continue;
            }

            $op   = $payload['op'] ?? null;
            $data = $payload['d'] ?? null;

            switch ($op) {
                case self::OP_HELLO:
                    $heartbeatInterval = $data['heartbeat_interval'] ?? 41250;
                    $lastHeartbeatAt   = (int) (microtime(true) * 1000);

                    $this->sendIdentify($client, $token);
                    $this->info('Identified — bot is now online.');
                    break;

                case self::OP_HEARTBEAT:
                    // Server requests an immediate heartbeat
                    $client->text(json_encode(['op' => self::OP_HEARTBEAT, 'd' => null]));
                    $lastHeartbeatAt = (int) (microtime(true) * 1000);
                    break;

                case self::OP_HEARTBEAT_ACK:
                    $lastHeartbeatAck = true;
                    break;

                case self::OP_RECONNECT:
                    $this->info('Server requested reconnect.');
                    $client->close();

                    return;
            }
        }
    }

    private function sendIdentify(Client $client, string $token): void
    {
        $client->text(json_encode([
            'op' => self::OP_IDENTIFY,
            'd'  => [
                'token'      => $token,
                'intents'    => 0,
                'properties' => [
                    'os'      => 'linux',
                    'browser' => 'starkeeper',
                    'device'  => 'starkeeper',
                ],
                'presence' => [
                    'status'     => 'online',
                    'afk'        => false,
                    'activities' => [],
                    'since'      => null,
                ],
            ],
        ]));
    }
}
