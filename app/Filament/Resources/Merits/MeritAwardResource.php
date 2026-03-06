<?php

namespace App\Filament\Resources\Merits;

use App\Filament\Resources\Merits\Pages\CreateMeritAward;
use App\Filament\Resources\Merits\Pages\ListMeritAwards;
use App\Filament\Resources\Merits\Schemas\MeritAwardForm;
use App\Filament\Resources\Merits\Tables\MeritAwardsTable;
use App\Models\MeritAward;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MeritAwardResource extends Resource
{
    protected static ?string $model = MeritAward::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static ?string $navigationLabel = 'Merit Awards';

    public static function getNavigationGroup(): ?string
    {
        return 'Merits';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return MeritAwardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MeritAwardsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListMeritAwards::route('/'),
            'create' => CreateMeritAward::route('/create'),
        ];
    }
}
