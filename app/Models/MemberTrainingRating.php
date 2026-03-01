<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberTrainingRating extends Model
{
    use HasFactory;

    protected $fillable = ['member_id', 'training_subtopic_id', 'rating', 'note', 'note_author_id'];

    protected function casts(): array
    {
        return [
            'rating' => 'float',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function subtopic(): BelongsTo
    {
        return $this->belongsTo(TrainingSubtopic::class, 'training_subtopic_id');
    }

    public function noteAuthor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'note_author_id');
    }
}
