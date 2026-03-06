<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeritAward extends Model
{
    use HasFactory;

    protected $fillable = ['member_id', 'awarded_by_user_id', 'amount', 'reason'];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function awardedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'awarded_by_user_id');
    }
}
