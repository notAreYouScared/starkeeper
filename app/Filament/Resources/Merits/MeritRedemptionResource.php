<?php

namespace App\Filament\Resources\Merits;

use App\Filament\Resources\Merits\Pages\CreateMeritRedemption;
use App\Filament\Resources\Merits\Pages\ListMeritRedemptions;
use App\Filament\Resources\Merits\Schemas\MeritRedemptionForm;
use App\Filament\Resources\Merits\Tables\MeritRedemptionsTable;
use App\Models\MeritRedemption;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MeritRedemptionResource extends Resource
{
    protected static ?string $model = MeritRedemption::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static ?string $navigationLabel = 'Redemptions';

    public static function getNavigationGroup(): ?string
    {
        return 'Merits';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function form(Schema $schema): Schema
    {
        return MeritRedemptionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MeritRedemptionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListMeritRedemptions::route('/'),
            'create' => CreateMeritRedemption::route('/create'),
        ];
    }
}
