<?php

namespace App\Filament\Resources\OrgRoles\Schemas;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrgRoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('label')
                    ->label('Display Label')
                    ->required()
                    ->maxLength(255),

                TagsInput::make('discord_role_ids')
                    ->label('Discord Role IDs')
                    ->placeholder('Add a Discord snowflake ID')
                    ->helperText('Enter one or more Discord snowflake IDs. Members holding any of these Discord roles are automatically assigned this org role during sync.'),

                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
