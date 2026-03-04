<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrgRole extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'label', 'discord_role_ids', 'sort_order'];

    protected $casts = [
        'discord_role_ids' => 'array',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
