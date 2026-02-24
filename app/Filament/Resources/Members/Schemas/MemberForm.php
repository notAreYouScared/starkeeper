<?php

namespace App\Filament\Resources\Members\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('handle')
                    ->label('RSI Handle')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('title')
                    ->label('Title')
                    ->placeholder('e.g. Grand Admiral, Fleet Commander')
                    ->maxLength(255),

                Select::make('org_role')
                    ->label('Organisation Role')
                    ->options([
                        'leadership' => 'Leadership',
                        'member'     => 'Member',
                    ])
                    ->default('member')
                    ->required(),
            ]);
    }
}
