<?php

namespace App\Filament\Resources\TrainingCategories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TrainingCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Category Name')
                    ->required()
                    ->maxLength(255),

                FileUpload::make('image')
                    ->label('Category Image')
                    ->image()
                    ->disk('public')
                    ->directory('training-category-images')
                    ->imagePreviewHeight('80')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml']),

                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first.'),
            ]);
    }
}
