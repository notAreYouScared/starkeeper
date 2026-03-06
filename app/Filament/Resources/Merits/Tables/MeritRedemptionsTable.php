<?php

namespace App\Filament\Resources\Merits\Tables;

use App\Models\MeritRedemption;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MeritRedemptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('member.name')
                    ->label('Member')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('reward.name')
                    ->label('Reward')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('reward.category.name')
                    ->label('Category')
                    ->sortable(),

                TextColumn::make('merit_cost')
                    ->label('Merits Spent')
                    ->sortable(),

                TextColumn::make('redeemedBy.name')
                    ->label('Processed By')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->recordActions([
                DeleteAction::make()
                    ->after(function (MeritRedemption $record): void {
                        $record->member->increment('merits', $record->merit_cost);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
