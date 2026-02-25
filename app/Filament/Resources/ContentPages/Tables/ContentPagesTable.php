<?php

namespace App\Filament\Resources\ContentPages\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContentPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->badge(),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('slug');
    }
}
