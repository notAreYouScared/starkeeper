<?php

namespace App\Filament\Resources\Teams\RelationManagers;

use App\Models\TeamRole;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;

class TeamMembersRelationManager extends RelationManager
{
    protected static string $relationship = 'teamMembers';

    protected static ?string $title = 'Team Members';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('member_id')
                    ->label('Member')
                    ->relationship('member', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('team_role_id')
                    ->label('Team Role')
                    ->options(function () {
                        $team = $this->getOwnerRecord();

                        return TeamRole::where('unit_id', $team->unit_id)
                            ->orderBy('sort_order')
                            ->get()
                            ->mapWithKeys(fn (TeamRole $role) => [$role->id => "{$role->label} ({$role->name})"]);
                    })
                    ->required(),

                TextInput::make('title')
                    ->label('Title')
                    ->placeholder('e.g. Wing Commander, Mining Specialist')
                    ->maxLength(255),

                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first within the team.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('member.name')
                    ->label('Name')
                    ->searchable(),

                TextColumn::make('teamRole.label')
                    ->label('Role')
                    ->badge()
                    ->placeholder('—'),

                TextInputColumn::make('title')
                    ->label('Title')
                    ->placeholder('—'),

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
