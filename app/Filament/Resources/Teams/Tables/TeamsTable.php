<?php

namespace App\Filament\Resources\Teams\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Patch')
                    ->disk('public')
                    ->size(40)
                    ->defaultImageUrl(null),

                TextColumn::make('unit.name')
                    ->label('Unit')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Security' => 'danger',
                        'Industry' => 'warning',
                        'Racing' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Team Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(60)
                    ->placeholder('—'),

                TextColumn::make('team_members_count')
                    ->label('Members')
                    ->counts('teamMembers')
                    ->sortable(),

                TextInputColumn::make('sort_order')
                    ->label('Display Order')
                    ->width(5)
                    ->sortable(),

                TextColumn::make('discord_role_id')
                    ->label('Discord Role ID')
                    ->placeholder('—')
                    ->searchable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('unit')
                    ->relationship('unit', 'name')
                    ->label('Unit'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
