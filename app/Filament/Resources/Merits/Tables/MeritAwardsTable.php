<?php

namespace App\Filament\Resources\Merits\Tables;

use App\Models\MeritAward;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MeritAwardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('member.name')
                    ->label('Member')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Merits Awarded')
                    ->sortable(),

                TextColumn::make('reason')
                    ->label('Reason')
                    ->limit(60)
                    ->tooltip(fn (MeritAward $record): string => $record->reason),

                TextColumn::make('awardedBy.name')
                    ->label('Awarded By')
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
                    ->after(function (MeritAward $record): void {
                        $record->member->decrement('merits', $record->amount);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
