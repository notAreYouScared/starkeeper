<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingSubtopic extends Model
{
    use HasFactory;

    protected $fillable = ['training_category_id', 'name', 'description', 'sort_order'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TrainingCategory::class, 'training_category_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(MemberTrainingRating::class);
    }
}
