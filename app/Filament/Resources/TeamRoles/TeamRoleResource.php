<?php

namespace App\Filament\Resources\TeamRoles;

use App\Filament\Resources\TeamRoles\Pages\CreateTeamRole;
use App\Filament\Resources\TeamRoles\Pages\EditTeamRole;
use App\Filament\Resources\TeamRoles\Pages\ListTeamRoles;
use App\Filament\Resources\TeamRoles\Schemas\TeamRoleForm;
use App\Filament\Resources\TeamRoles\Tables\TeamRolesTable;
use App\Models\TeamRole;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TeamRoleResource extends Resource
{
    protected static ?string $model = TeamRole::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Team Roles';

    public static function getNavigationGroup(): ?string
    {
        return 'Team';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return TeamRoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeamRolesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTeamRoles::route('/'),
            'create' => CreateTeamRole::route('/create'),
            'edit'   => EditTeamRole::route('/{record}/edit'),
        ];
    }
}
