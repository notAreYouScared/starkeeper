<?php

namespace App\Filament\Resources\Merits\Pages;

use App\Filament\Resources\Merits\MeritRedemptionResource;
use App\Models\MeritRedemption;
use Filament\Resources\Pages\CreateRecord;

class CreateMeritRedemption extends CreateRecord
{
    protected static string $resource = MeritRedemptionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['redeemed_by_user_id'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var MeritRedemption $record */
        $record = $this->getRecord();
        $record->member->decrement('merits', $record->merit_cost);
    }
}
