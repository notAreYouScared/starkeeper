<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sort_order'];

    public function subtopics(): HasMany
    {
        return $this->hasMany(TrainingSubtopic::class)->orderBy('sort_order');
    }
}
