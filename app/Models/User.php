<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'discord_id',
        'avatar',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
        ];
    }

    public function member(): HasOne
    {
        return $this->hasOne(Member::class, 'discord_id', 'discord_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        try {
            $panelId = $panel->getId();
        } catch (\LogicException) {
            return $this->is_admin;
        }

        if ($panelId === 'admin') {
            return $this->is_admin;
        }

        // member panel — only users with a linked Member record and a valid org role
        $this->loadMissing('member');

        return $this->member?->org_role_id !== null;
    }
}
