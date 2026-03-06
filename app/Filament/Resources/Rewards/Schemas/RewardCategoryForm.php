<?php

namespace App\Filament\Resources\Rewards\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RewardCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Category Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first.'),
            ]);
    }
}
