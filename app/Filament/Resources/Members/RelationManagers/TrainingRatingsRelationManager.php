<?php

namespace App\Filament\Resources\Members\RelationManagers;

use App\Models\TrainingSubtopic;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrainingRatingsRelationManager extends RelationManager
{
    protected static string $relationship = 'trainingRatings';

    protected static ?string $title = 'Training Ratings';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('training_subtopic_id')
                    ->label('Subtopic')
                    ->options(
                        TrainingSubtopic::with('category')
                            ->orderBy('sort_order')
                            ->get()
                            ->groupBy(fn ($s) => $s->category->name)
                            ->map(fn ($group) => $group->pluck('name', 'id'))
                            ->toArray()
                    )
                    ->searchable()
                    ->required(),

                Select::make('rating')
                    ->label('Rating (0–5 stars)')
                    ->options([
                        '0.0' => '0 – No rating',
                        '0.5' => '0.5 ★',
                        '1.0' => '1 ★',
                        '1.5' => '1.5 ★',
                        '2.0' => '2 ★',
                        '2.5' => '2.5 ★',
                        '3.0' => '3 ★',
                        '3.5' => '3.5 ★',
                        '4.0' => '4 ★',
                        '4.5' => '4.5 ★',
                        '5.0' => '5 ★',
                    ])
                    ->default('0.0')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subtopic.category.name')
                    ->label('Category')
                    ->sortable(),

                TextColumn::make('subtopic.name')
                    ->label('Subtopic')
                    ->searchable(),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn (float $state): string => number_format($state, 1) . ' / 5.0')
                    ->sortable(),
            ])
            ->defaultSort('subtopic.category.name')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
