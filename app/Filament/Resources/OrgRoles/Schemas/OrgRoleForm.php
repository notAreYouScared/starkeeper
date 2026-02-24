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

                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
