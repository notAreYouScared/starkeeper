<?php

namespace App\Filament\Resources\Merits\Pages;

use App\Filament\Resources\Merits\MeritAwardResource;
use App\Models\MeritAward;
use Filament\Resources\Pages\CreateRecord;

class CreateMeritAward extends CreateRecord
{
    protected static string $resource = MeritAwardResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['awarded_by_user_id'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var MeritAward $record */
        $record = $this->getRecord();
        $record->member->increment('merits', $record->amount);
    }
}
