<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Resources\Members\MemberResource;
use App\Models\Member;
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
                ->label('Sync Discord Members')
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

                        $created = 0;
                        $updated = 0;

                        foreach ($discordMembers as $dm) {
                            $displayName = $dm['nickname'] ?? $dm['username'];

                            if ($existing->has($dm['discord_id'])) {
                                $existing->get($dm['discord_id'])->update([
                                    'name'       => $displayName,
                                    'avatar_url' => $dm['avatar_url'],
                                ]);
                                $updated++;
                            } else {
                                Member::create([
                                    'discord_id'  => $dm['discord_id'],
                                    'name'        => $displayName,
                                    'handle'      => $dm['username'],
                                    'avatar_url'  => $dm['avatar_url'],
                                    'profile_url' => DiscordService::profileUrl($dm['discord_id']),
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
