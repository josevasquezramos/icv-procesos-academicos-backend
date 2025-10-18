<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'evaluation_id',
        'statement',
        'question_type',
        'answer_options',
        'correct_answer',
        'score',
    ];

    protected $casts = [
        'answer_options' => 'array',
        'correct_answer' => 'array',
        'score' => 'decimal:2',
    ];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }
}
