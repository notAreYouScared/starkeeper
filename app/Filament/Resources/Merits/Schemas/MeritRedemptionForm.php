<?php

namespace App\Filament\Resources\Merits\Schemas;

use App\Models\Member;
use App\Models\Reward;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MeritRedemptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('member_id')
                    ->label('Member')
                    ->relationship('member', 'name', fn ($query) => $query->orderBy('name'))
                    ->searchable()
                    ->required(),

                Select::make('reward_id')
                    ->label('Reward')
                    ->options(
                        Reward::with('category')
                            ->get()
                            ->mapWithKeys(fn (Reward $reward) => [
                                $reward->id => "{$reward->name} ({$reward->merit_cost} merits)",
                            ])
                    )
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set): void {
                        if ($state) {
                            $reward = Reward::find($state);
                            $set('merit_cost', $reward?->merit_cost);
                        }
                    }),

                TextInput::make('merit_cost')
                    ->label('Merit Cost')
                    ->numeric()
                    ->required()
                    ->helperText('Auto-filled from the selected reward. Adjust if needed.'),
            ]);
    }
}
