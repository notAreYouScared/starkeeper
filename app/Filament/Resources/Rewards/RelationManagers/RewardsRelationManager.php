<?php

namespace App\Filament\Resources\Rewards\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;

class RewardsRelationManager extends RelationManager
{
    protected static string $relationship = 'rewards';

    protected static ?string $title = 'Rewards';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Reward Name')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Description')
                    ->placeholder('Brief description of this reward.')
                    ->rows(3)
                    ->maxLength(1000),

                FileUpload::make('image')
                    ->label('Image')
                    ->image()
                    ->disk('public')
                    ->directory('reward-images')
                    ->imagePreviewHeight('80')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])
                    ->nullable(),

                TextInput::make('merit_cost')
                    ->label('Merit Cost')
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->helperText('Number of merits required to redeem this reward.'),

                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->disk('public')
                    ->size(40)
                    ->defaultImageUrl(null)
                    ->extraImgAttributes(['class' => 'rounded']),

                TextColumn::make('name')
                    ->label('Reward Name')
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->placeholder('—')
                    ->limit(60),

                TextColumn::make('merit_cost')
                    ->label('Merit Cost')
                    ->sortable(),

                TextInputColumn::make('sort_order')
                    ->label('Display Order')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
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
