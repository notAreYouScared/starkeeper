<?php

namespace App\Filament\Resources\Members\RelationManagers;

use App\Models\MemberTrainingRating;
use App\Models\TrainingCategory;
use App\Models\TrainingSubtopic;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;

class TrainingRatingsRelationManager extends RelationManager
{
    protected static string $relationship = 'trainingRatings';

    protected static ?string $title = 'Training Ratings';

    /**
     * Called by the star-rating Alpine component when a star is clicked.
     * Only updates records that belong to this relation manager's owner member.
     */
    public function updateSubtopicRating(int $recordId, float $rating): void
    {
        MemberTrainingRating::where('id', $recordId)
            ->where('member_id', $this->getOwnerRecord()->getKey())
            ->update(['rating' => $rating]);
    }

    public function form(Schema $schema): Schema
    {
        // The form is only used by EditAction (kept for fallback fine-tuning).
        return $schema
            ->components([
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

                ViewColumn::make('rating')
                    ->label('Rating')
                    ->view('filament.tables.columns.star-rating')
                    ->sortable(),

                TextColumn::make('note')
                    ->label('Note')
                    ->placeholder('—')
                    ->limit(60)
                    ->tooltip(fn (MemberTrainingRating $record): ?string => $record->note),

                TextColumn::make('noteAuthor.name')
                    ->label('Note By')
                    ->placeholder('—'),
            ])
            ->defaultSort('subtopic.category.name')
            ->headerActions([
                Action::make('add_category')
                    ->label('Add Category')
                    ->icon('heroicon-o-plus')
                    ->schema([
                        Select::make('training_category_id')
                            ->label('Category')
                            ->options(TrainingCategory::orderBy('sort_order')->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (array $data): void {
                        $member = $this->getOwnerRecord();
                        $subtopics = TrainingSubtopic::where('training_category_id', $data['training_category_id'])
                            ->orderBy('sort_order')
                            ->get();

                        foreach ($subtopics as $subtopic) {
                            MemberTrainingRating::firstOrCreate(
                                [
                                    'member_id'            => $member->id,
                                    'training_subtopic_id' => $subtopic->id,
                                ],
                                ['rating' => 0]
                            );
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make('edit_note')
                    ->label('Edit Note')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Textarea::make('note')
                            ->label('Note (max 300 characters)')
                            ->maxLength(300)
                            ->rows(3)
                            ->placeholder('Add a private note visible only to this member...'),
                    ])
                    ->using(function (MemberTrainingRating $record, array $data): MemberTrainingRating {
                        $record->fill([
                            'note'           => $data['note'] ?: null,
                            'note_author_id' => auth()->id(),
                        ])->save();

                        return $record;
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
