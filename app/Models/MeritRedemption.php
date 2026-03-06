<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeritRedemption extends Model
{
    use HasFactory;

    protected $fillable = ['member_id', 'reward_id', 'merit_cost', 'redeemed_by_user_id'];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    public function redeemedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemed_by_user_id');
    }
}
