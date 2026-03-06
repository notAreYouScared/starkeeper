<?php

namespace App\Filament\Resources\Rewards\Pages;

use App\Filament\Resources\Rewards\RewardCategoryResource;
use Filament\Resources\Pages\ListRecords;

class ListRewardCategories extends ListRecords
{
    protected static string $resource = RewardCategoryResource::class;
}
