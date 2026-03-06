<?php

namespace App\Filament\Pages;

use App\Models\Member;
use App\Services\DiscordService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Log;

class DiscordGuildPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServerStack;

    protected string $view = 'filament.pages.discord-guild';

    /** @var array<int, array<string, mixed>> */
    public array $guildMembers = [];

    /** @var array<string, array<string, mixed>> */
    public array $guildRoles = [];

    /** @var array<string> */
    public array $importedIds = [];

    public static function getNavigationLabel(): string
    {
        return 'Discord Guild Members';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Administration';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public function getTitle(): string
    {
        return 'Discord Guild Members';
    }

    public function mount(): void
    {
        $this->importedIds = Member::whereNotNull('discord_id')
            ->pluck('discord_id')
            ->all();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('updateFromDiscord')
                ->label('Update from Discord')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->action(function (): void {
                    try {
                        $discord = app(DiscordService::class);
                        $this->guildRoles = $discord->getGuildRoles();
                        $this->guildMembers = $discord->getGuildMembers();

                        // Refresh the imported IDs in case the DB changed
                        $this->importedIds = Member::whereNotNull('discord_id')
                            ->pluck('discord_id')
                            ->all();

                        Notification::make()
                            ->title('Guild data updated')
                            ->body(count($this->guildMembers) . ' member(s) loaded from Discord.')
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Log::error('Discord guild fetch failed', ['error' => $e->getMessage()]);

                        Notification::make()
                            ->title('Could not reach Discord')
                            ->body('Check that DISCORD_BOT_TOKEN and DISCORD_GUILD_ID are set correctly.')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function importMember(string $discordId): void
    {
        $dm = collect($this->guildMembers)->firstWhere('discord_id', $discordId);

        if (! $dm) {
            return;
        }

        if (in_array($discordId, $this->importedIds, true)) {
            Notification::make()
                ->title('Already imported')
                ->body('This member is already in the roster.')
                ->warning()
                ->send();

            return;
        }

        $displayName = $dm['nickname'] ?? $dm['username'];

        Member::create([
            'discord_id'  => $dm['discord_id'],
            'name'        => $displayName,
            'handle'      => $dm['username'],
            'avatar_url'  => $dm['avatar_url'],
            'profile_url' => DiscordService::profileUrl($dm['discord_id']),
        ]);

        $this->importedIds[] = $discordId;

        Notification::make()
            ->title('Member imported')
            ->body("{$displayName} has been added to the roster.")
            ->success()
            ->send();
    }
}
