<?php

namespace App\Filament\Resources\Rewards;

use App\Filament\Resources\Rewards\Pages\CreateRewardCategory;
use App\Filament\Resources\Rewards\Pages\EditRewardCategory;
use App\Filament\Resources\Rewards\Pages\ListRewardCategories;
use App\Filament\Resources\Rewards\RelationManagers\RewardsRelationManager;
use App\Filament\Resources\Rewards\Schemas\RewardCategoryForm;
use App\Filament\Resources\Rewards\Tables\RewardCategoriesTable;
use App\Models\RewardCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RewardCategoryResource extends Resource
{
    protected static ?string $model = RewardCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;

    protected static ?string $navigationLabel = 'Reward Categories';

    public static function getNavigationGroup(): ?string
    {
        return 'Merits';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return RewardCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RewardCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RewardsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListRewardCategories::route('/'),
            'create' => CreateRewardCategory::route('/create'),
            'edit'   => EditRewardCategory::route('/{record}/edit'),
        ];
    }
}
