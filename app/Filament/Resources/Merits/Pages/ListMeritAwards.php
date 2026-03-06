<?php

namespace App\Filament\Resources\Merits\Pages;

use App\Filament\Resources\Merits\MeritAwardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMeritAwards extends ListRecords
{
    protected static string $resource = MeritAwardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
