<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DiscordService
{
    private const API_BASE = 'https://discord.com/api/v10';

    private const PROFILE_URL_BASE = 'discord://-/users/';

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
        return self::PROFILE_URL_BASE . $userId;
    }

    /**
     * Fetch all members of the configured Discord guild.
     *
     * Each entry contains:
     *   - discord_id   (string)
     *   - username     (string)
     *   - display_name (string|null)  global display name
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

                // Skip bots — they should not appear in the member roster
                if ($user['bot'] ?? false) {
                    continue;
                }

                // Skip users who do not hold the minimum required member role
                $minimumRoleId = config('services.discord.minimum_member_role_id', '');
                if ($minimumRoleId !== '' && ! in_array($minimumRoleId, $member['roles'] ?? [], true)) {
                    continue;
                }

                $members[] = [
                    'discord_id'   => $userId,
                    'username'     => $user['username'] ?? '',
                    'display_name' => $user['global_name'] ?? null,
                    'nickname'     => $member['nick'] ?? null,
                    'avatar_url'   => $this->resolveAvatarUrl($userId, $user, $member),
                    'role_ids'     => $member['roles'] ?? [],
                ];
            }

            $after = $batch[count($batch) - 1]['user']['id'] ?? null;
        } while (count($batch) === 1000);

        return $members;
    }

    /**
     * Fetch a single Discord user by their ID.
     *
     * Returns an array with:
     *   - id           (string)
     *   - username     (string)
     *   - global_name  (string|null)  user's chosen display name across Discord
     *
     * @return array<string, mixed>
     */
    public function getUser(string $userId): array
    {
        $response = Http::withToken($this->botToken, 'Bot')
            ->get(self::API_BASE . "/users/{$userId}");

        $response->throw();

        $user = $response->json();

        return [
            'id'          => $user['id'] ?? $userId,
            'username'    => $user['username'] ?? '',
            'global_name' => $user['global_name'] ?? null,
        ];
    }

    /**
     * Fetch a single member of the configured Discord guild by their Discord user ID.
     *
     * Returns the same structure as individual entries from getGuildMembers(),
     * or null if the member is not found (404) or is a bot.
     *
     * When the member has no server nickname, an additional API call is made to
     * fetch the user's global display name (global_name) before falling back to
     * their username.
     *
     * @return array<string, mixed>|null
     */
    public function getGuildMember(string $discordId): ?array
    {
        $response = Http::withToken($this->botToken, 'Bot')
            ->get(self::API_BASE . "/guilds/{$this->guildId}/members/{$discordId}");

        if ($response->status() === 404) {
            return null;
        }

        $response->throw();

        $member = $response->json();
        $user = $member['user'] ?? [];

        if ($user['bot'] ?? false) {
            return null;
        }

        $nickname = $member['nick'] ?? null;

        // When there is no server nickname, fetch the user's global display name
        // via a dedicated API call so we can prefer it over the plain username.
        $globalName = null;
        if ($nickname === null) {
            $userData   = $this->getUser($discordId);
            $globalName = $userData['global_name'];
        }

        return [
            'discord_id'   => $user['id'],
            'username'     => $user['username'] ?? '',
            'display_name' => $globalName,
            'nickname'     => $nickname,
            'avatar_url'   => $this->resolveAvatarUrl($user['id'], $user, $member),
            'role_ids'     => $member['roles'] ?? [],
        ];
    }

    /**
     * Resolve the display name for a guild member using the priority:
     * server nickname → global display name → username.
     *
     * @param  array<string, mixed>  $discordMember  An entry from getGuildMembers() or getGuildMember()
     */
    public static function resolveDisplayName(array $discordMember): string
    {
        return $discordMember['nickname'] ?? $discordMember['display_name'] ?? $discordMember['username'];
    }

    /**
     * Send a Direct Message to a Discord user via the bot.
     *
     * Creates (or retrieves) a DM channel with the recipient and posts
     * the given message content to it.
     */
    public function sendDirectMessage(string $recipientDiscordId, string $message): void
    {
        // Step 1: open / retrieve a DM channel with the recipient
        $channelResponse = Http::withToken($this->botToken, 'Bot')
            ->post(self::API_BASE . '/users/@me/channels', [
                'recipient_id' => $recipientDiscordId,
            ]);

        $channelResponse->throw();

        $channelId = $channelResponse->json('id');

        // Step 2: post the message to that channel
        $messageResponse = Http::withToken($this->botToken, 'Bot')
            ->post(self::API_BASE . "/channels/{$channelId}/messages", [
                'content' => $message,
            ]);

        $messageResponse->throw();
    }

    /**
     * Fetch the Discord Gateway WebSocket URL for bots.
     */
    public function getGatewayUrl(): string
    {
        $response = Http::withToken($this->botToken, 'Bot')
            ->get(self::API_BASE . '/gateway/bot');

        $response->throw();

        return $response->json('url');
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
