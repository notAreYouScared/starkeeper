<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrgRole extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'label', 'discord_role_id', 'sort_order'];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
