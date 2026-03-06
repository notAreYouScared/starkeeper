<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RewardCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sort_order'];

    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class);
    }
}
