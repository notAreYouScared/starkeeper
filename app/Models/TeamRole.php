<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeamRole extends Model
{
    use HasFactory;

    protected $fillable = ['unit_id', 'name', 'label', 'sort_order', 'color', 'discord_role_id'];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }
}
