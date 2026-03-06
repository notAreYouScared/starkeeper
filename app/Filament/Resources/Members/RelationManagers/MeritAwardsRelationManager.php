<?php

namespace App\Filament\Resources\Members\RelationManagers;

use App\Models\MeritAward;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MeritAwardsRelationManager extends RelationManager
{
    protected static string $relationship = 'meritAwards';

    protected static ?string $title = 'Merit Awards';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount')
                    ->label('Merits Awarded')
                    ->sortable(),

                TextColumn::make('reason')
                    ->label('Reason')
                    ->limit(60)
                    ->tooltip(fn (MeritAward $record): string => $record->reason),

                TextColumn::make('awardedBy.name')
                    ->label('Awarded By')
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['awarded_by_user_id'] = auth()->id();

                        return $data;
                    })
                    ->after(function (MeritAward $record): void {
                        $record->member->increment('merits', $record->amount);
                    }),
            ])
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
