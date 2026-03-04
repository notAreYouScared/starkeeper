<?php

namespace App\Filament\Resources\OrgRoles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;

class OrgRolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('label')
                    ->label('Display Label')
                    ->searchable(),

                TextColumn::make('discord_role_ids')
                    ->label('Discord Role IDs')
                    ->placeholder('—')
                    ->formatStateUsing(fn (?array $state): string => ! empty($state) ? implode(', ', $state) : '')
                    ->searchable(query: function ($query, string $search) {
                        $query->whereJsonContains('discord_role_ids', $search);
                    }),

                TextColumn::make('members_count')
                    ->label('Members')
                    ->counts('members')
                    ->sortable(),

                TextInputColumn::make('sort_order')
                    ->label('Display Order')
                    ->width(5)
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([])
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
