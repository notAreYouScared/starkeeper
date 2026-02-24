<?php

namespace App\Filament\Resources\OrgRoles;

use App\Filament\Resources\OrgRoles\Pages\CreateOrgRole;
use App\Filament\Resources\OrgRoles\Pages\EditOrgRole;
use App\Filament\Resources\OrgRoles\Pages\ListOrgRoles;
use App\Filament\Resources\OrgRoles\Schemas\OrgRoleForm;
use App\Filament\Resources\OrgRoles\Tables\OrgRolesTable;
use App\Models\OrgRole;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrgRoleResource extends Resource
{
    protected static ?string $model = OrgRole::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Org Roles';

    public static function getNavigationGroup(): ?string
    {
        return 'Organisation';
    }

    public static function getNavigationSort(): ?int
    {
        return 0;
    }

    public static function form(Schema $schema): Schema
    {
        return OrgRoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrgRolesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrgRoles::route('/'),
            'create' => CreateOrgRole::route('/create'),
            'edit' => EditOrgRole::route('/{record}/edit'),
        ];
    }
}
