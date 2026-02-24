<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('auth_type')
                    ->label('Auth Type')
                    ->badge()
                    ->state(fn (mixed $record): string => $record->discord_id ? 'Discord SSO' : 'Password')
                    ->color(fn (string $state): string => $state === 'Discord SSO' ? 'info' : 'gray'),

                IconColumn::make('is_admin')
                    ->label('Admin Access')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_admin')
                    ->label('Admin Access'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
