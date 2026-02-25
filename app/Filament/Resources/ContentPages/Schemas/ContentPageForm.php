<?php

namespace App\Filament\Resources\ContentPages\Schemas;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContentPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Page Title')
                    ->disabled(),

                TextInput::make('slug')
                    ->label('Slug')
                    ->disabled(),

                MarkdownEditor::make('content')
                    ->label('Content')
                    ->required()
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'attachFiles',
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'heading',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'table',
                        'undo',
                    ]),
            ]);
    }
}
