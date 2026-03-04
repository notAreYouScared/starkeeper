<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Resources\Members\MemberResource;
use App\Models\Member;
use App\Models\OrgRole;
use App\Services\DiscordService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Log;

class ListMembers extends ListRecords
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncDiscordMembers')
                ->label('Sync Discord')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Sync members from Discord')
                ->modalDescription('This will update names and avatars for existing members matched by Discord ID, and create new member records for any Discord server members not yet in the roster. Continue?')
                ->action(function (): void {
                    try {
                        $discord = app(DiscordService::class);
                        $discordMembers = $discord->getGuildMembers();

                        $discordIds = array_column($discordMembers, 'discord_id');
                        $existing = Member::whereIn('discord_id', $discordIds)
                            ->get()
                            ->keyBy('discord_id');

                        // Build a flat map of discord_role_id => OrgRole for priority-aware lookup.
                        // Each OrgRole may declare multiple Discord role IDs (e.g. "Director of Operations"
                        // and "Director of Flight" both map to the "Director" org role).
                        // When a member holds multiple Discord roles that each map to an org role,
                        // the org role with the lowest sort_order (highest priority) is assigned.
                        $roleMap = [];
                        OrgRole::whereNotNull('discord_role_ids')
                            ->orderBy('sort_order')
                            ->get(['id', 'discord_role_ids', 'sort_order'])
                            ->each(function ($orgRole) use (&$roleMap) {
                                foreach ($orgRole->discord_role_ids ?? [] as $discordRoleId) {
                                    if ($discordRoleId === '') {
                                        continue;
                                    }
                                    // Only set if this discord role ID isn't already mapped to a
                                    // higher-priority (lower sort_order) org role
                                    if (! isset($roleMap[$discordRoleId])) {
                                        $roleMap[$discordRoleId] = $orgRole;
                                    }
                                }
                            });

                        $created = 0;
                        $updated = 0;

                        foreach ($discordMembers as $dm) {
                            $displayName = $dm['nickname'] ?? $dm['username'];

                            // Find the highest-priority org role that matches any of the member's Discord roles
                            $orgRoleId = null;
                            $bestSortOrder = PHP_INT_MAX;
                            foreach ($dm['role_ids'] as $discordRoleId) {
                                if (isset($roleMap[$discordRoleId])) {
                                    $candidate = $roleMap[$discordRoleId];
                                    if ($candidate->sort_order < $bestSortOrder) {
                                        $orgRoleId = $candidate->id;
                                        $bestSortOrder = $candidate->sort_order;
                                    }
                                }
                            }

                            if ($existing->has($dm['discord_id'])) {
                                $updateData = [
                                    'name'       => $displayName,
                                    'avatar_url' => $dm['avatar_url'],
                                ];
                                if ($orgRoleId !== null) {
                                    $updateData['org_role_id'] = $orgRoleId;
                                }
                                $existing->get($dm['discord_id'])->update($updateData);
                                $updated++;
                            } else {
                                Member::create([
                                    'discord_id'  => $dm['discord_id'],
                                    'name'        => $displayName,
                                    'handle'      => $dm['username'],
                                    'avatar_url'  => $dm['avatar_url'],
                                    'profile_url' => DiscordService::profileUrl($dm['discord_id']),
                                    'org_role_id' => $orgRoleId,
                                ]);
                                $created++;
                            }
                        }

                        Notification::make()
                            ->title('Discord sync complete')
                            ->body("Created {$created} new member(s), updated {$updated} existing member(s).")
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Log::error('Discord member sync failed', ['error' => $e->getMessage()]);

                        Notification::make()
                            ->title('Discord sync failed')
                            ->body('Could not connect to Discord. Check that DISCORD_BOT_TOKEN and DISCORD_GUILD_ID are set correctly.')
                            ->danger()
                            ->send();
                    }
                }),

            CreateAction::make(),
        ];
    }
}
