<?php

namespace App\Filament\Resources\TeamRoles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;

class TeamRolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('unit.name')
                    ->label('Unit')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('label')
                    ->label('Display Label')
                    ->searchable(),

                TextColumn::make('team_members_count')
                    ->label('Members')
                    ->counts('teamMembers')
                    ->sortable(),

                TextInputColumn::make('sort_order')
                    ->label('Display Order')
                    ->width(5)
                    ->sortable(),

                ColorColumn::make('color')
                    ->label('Colour')
                    ->copyable(false),
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
