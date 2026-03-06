<?php

namespace App\Filament\Resources\Members\RelationManagers;

use App\Models\MeritRedemption;
use App\Models\Reward;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MeritRedemptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'meritRedemptions';

    protected static ?string $title = 'Merit Redemptions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('reward_id')
                    ->label('Reward')
                    ->options(
                        Reward::with('category')
                            ->get()
                            ->mapWithKeys(fn (Reward $reward) => [
                                $reward->id => "{$reward->name} ({$reward->merit_cost} merits)",
                            ])
                    )
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set): void {
                        if ($state) {
                            $reward = Reward::find($state);
                            $set('merit_cost', $reward?->merit_cost);
                        }
                    }),

                TextInput::make('merit_cost')
                    ->label('Merit Cost')
                    ->numeric()
                    ->required()
                    ->helperText('Auto-filled from the selected reward. Adjust if needed.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reward.name')
                    ->label('Reward')
                    ->sortable(),

                TextColumn::make('reward.category.name')
                    ->label('Category')
                    ->sortable(),

                TextColumn::make('merit_cost')
                    ->label('Merits Spent')
                    ->sortable(),

                TextColumn::make('redeemedBy.name')
                    ->label('Processed By')
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
                        $data['redeemed_by_user_id'] = auth()->id();

                        return $data;
                    })
                    ->after(function (MeritRedemption $record): void {
                        $record->member->decrement('merits', $record->merit_cost);
                    }),
            ])
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
