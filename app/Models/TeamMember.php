<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'member_id', 'team_role_id', 'title', 'sort_order'];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function teamRole(): BelongsTo
    {
        return $this->belongsTo(TeamRole::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
