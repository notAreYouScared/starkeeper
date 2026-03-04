<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DiscordService
{
    private const API_BASE = 'https://discord.com/api/v10';

    private const PROFILE_URL_PREFIX = 'discord://-/users/';

    private string $botToken;

    private string $guildId;

    public function __construct()
    {
        $this->botToken = config('services.discord.bot_token') ?? '';
        $this->guildId = config('services.discord.guild_id') ?? '';
    }

    /**
     * Return the Discord profile URL for a given user ID.
     */
    public static function profileUrl(string $userId): string
    {
        return self::PROFILE_URL_PREFIX . $userId;
    }

    /**
     * Fetch all members of the configured Discord guild.
     *
     * Each entry contains:
     *   - discord_id   (string)
     *   - username     (string)
     *   - nickname     (string|null)  server-specific nickname
     *   - avatar_url   (string|null)
     *   - role_ids     (array)        Discord role IDs the member holds
     *
     * @return array<int, array<string, mixed>>
     */
    public function getGuildMembers(): array
    {
        $members = [];
        $after = null;

        do {
            $query = ['limit' => 1000];
            if ($after !== null) {
                $query['after'] = $after;
            }

            $response = Http::withToken($this->botToken, 'Bot')
                ->get(self::API_BASE . "/guilds/{$this->guildId}/members", $query);

            $response->throw();

            $batch = $response->json();

            if (empty($batch)) {
                break;
            }

            foreach ($batch as $member) {
                $user = $member['user'] ?? [];
                $userId = $user['id'] ?? null;

                if ($userId === null) {
                    continue;
                }

                $members[] = [
                    'discord_id' => $userId,
                    'username'   => $user['username'] ?? '',
                    'nickname'   => $member['nick'] ?? null,
                    'avatar_url' => $this->resolveAvatarUrl($userId, $user, $member),
                    'role_ids'   => $member['roles'] ?? [],
                ];
            }

            $after = $batch[count($batch) - 1]['user']['id'] ?? null;
        } while (count($batch) === 1000);

        return $members;
    }

    /**
     * Fetch all roles defined in the configured Discord guild.
     *
     * Each entry contains:
     *   - id    (string)
     *   - name  (string)
     *   - color (int)
     *
     * @return array<string, array<string, mixed>>  keyed by role ID
     */
    public function getGuildRoles(): array
    {
        $response = Http::withToken($this->botToken, 'Bot')
            ->get(self::API_BASE . "/guilds/{$this->guildId}/roles");

        $response->throw();

        $roles = [];
        foreach ($response->json() as $role) {
            $roles[$role['id']] = [
                'id'    => $role['id'],
                'name'  => $role['name'],
                'color' => $role['color'],
            ];
        }

        return $roles;
    }

    /**
     * Resolve the best avatar URL for a guild member.
     *
     * Priority: guild-specific avatar > user avatar > null.
     */
    private function resolveAvatarUrl(string $userId, array $user, array $member): ?string
    {
        // Guild-specific avatar takes priority
        if (! empty($member['avatar'])) {
            $hash = $member['avatar'];
            $ext = str_starts_with($hash, 'a_') ? 'gif' : 'png';

            return "https://cdn.discordapp.com/guilds/{$this->guildId}/users/{$userId}/avatars/{$hash}.{$ext}";
        }

        // Fall back to global user avatar
        if (! empty($user['avatar'])) {
            $hash = $user['avatar'];
            $ext = str_starts_with($hash, 'a_') ? 'gif' : 'png';

            return "https://cdn.discordapp.com/avatars/{$userId}/{$hash}.{$ext}";
        }

        return null;
    }
}
