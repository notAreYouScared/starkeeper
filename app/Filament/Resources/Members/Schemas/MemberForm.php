<?php

namespace App\Filament\Resources\Members\Schemas;

use App\Models\OrgRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                TextInput::make('name')
                    ->label('Display Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('handle')
                    ->label('RSI Handle')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('avatar_url')
                    ->label('Profile Picture URL')
                    ->url()
                    ->placeholder('https://...')
                    ->maxLength(2048),

                TextInput::make('profile_url')
                    ->label('Profile URL')
                    ->disabled()
                    ->readOnly()
                    ->maxLength(2048),

                TextInput::make('title')
                    ->label('Title')
                    ->placeholder('e.g. Grand Admiral, Fleet Commander')
                    ->maxLength(255),

                Select::make('org_role_id')
                    ->label('Organisation Role')
                    ->relationship('orgRole', 'label', fn ($query) => $query->orderBy('sort_order'))
                    ->default(fn () => OrgRole::where('name', 'member')->value('id'))
                    ->required(),

                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first in the org hierarchy.'),

                TextInput::make('merits')
                    ->label('Merit Balance')
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Total merits available. Manage via the Merit Awards and Redemptions tabs below.'),
            ]);
    }
}
