<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = ['reward_category_id', 'name', 'description', 'merit_cost', 'sort_order'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(RewardCategory::class, 'reward_category_id');
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(MeritRedemption::class);
    }
}
