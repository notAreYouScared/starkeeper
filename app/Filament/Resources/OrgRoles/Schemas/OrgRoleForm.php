<?php

namespace App\Filament\Resources\OrgRoles\Schemas;

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

                TextInput::make('discord_role_id')
                    ->label('Discord Role ID')
                    ->nullable()
                    ->maxLength(30)
                    ->helperText('The Discord snowflake ID for this role. When set, members with this Discord role are automatically assigned this org role during sync.'),

                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
