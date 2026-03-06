<?php

namespace App\Filament\Resources\Merits\Pages;

use App\Filament\Resources\Merits\MeritRedemptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMeritRedemptions extends ListRecords
{
    protected static string $resource = MeritRedemptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
