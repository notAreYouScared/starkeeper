<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_admin')
                    ->label('Admin Access')
                    ->helperText('Grant this user access to the admin panel.'),
            ]);
    }
}
