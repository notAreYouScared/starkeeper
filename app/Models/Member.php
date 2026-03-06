<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = ['discord_id', 'name', 'handle', 'rsi_handle', 'avatar_url', 'profile_url', 'title', 'org_role_id', 'sort_order', 'merits'];

    public function orgRole(): BelongsTo
    {
        return $this->belongsTo(OrgRole::class);
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_members')
            ->withPivot(['team_role_id', 'title', 'sort_order'])
            ->withTimestamps();
    }

    public function trainingRatings(): HasMany
    {
        return $this->hasMany(MemberTrainingRating::class);
    }

    public function meritAwards(): HasMany
    {
        return $this->hasMany(MeritAward::class);
    }

    public function meritRedemptions(): HasMany
    {
        return $this->hasMany(MeritRedemption::class);
    }
}
