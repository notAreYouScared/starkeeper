<?php

namespace App\Filament\Resources\Merits\Schemas;

use App\Models\Member;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MeritAwardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('member_id')
                    ->label('Member')
                    ->relationship('member', 'name', fn ($query) => $query->orderBy('name'))
                    ->searchable()
                    ->required(),

                TextInput::make('amount')
                    ->label('Merit Points')
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                Textarea::make('reason')
                    ->label('Reason')
                    ->required()
                    ->maxLength(1000)
                    ->rows(3)
                    ->placeholder('e.g. Outstanding performance at Fleet Battle event'),
            ]);
    }
}
