<?php

namespace App\Filament\Resources\Teams\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('unit_id')
                    ->label('Unit')
                    ->relationship('unit', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                TextInput::make('name')
                    ->label('Team Name')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->maxLength(1000),

                FileUpload::make('image')
                    ->label('Team Patch / Image')
                    ->image()
                    ->disk('public')
                    ->directory('team-images')
                    ->imagePreviewHeight('80')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml']),

                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first.'),

                TextInput::make('discord_role_id')
                    ->label('Discord Role ID')
                    ->placeholder('Discord snowflake ID')
                    ->nullable()
                    ->maxLength(255),

                Toggle::make('show_join_request')
                    ->label('Show "Request to Join" Button')
                    ->helperText('When enabled, a "Request to Join" button will be shown on this team\'s card in the Hierarchy page.')
                    ->default(false),
            ]);
    }
}
