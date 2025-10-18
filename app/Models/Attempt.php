<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Attempt extends Model
{
    protected $fillable = [
        'evaluation_id',
        'user_id',
        'start_date',
        'end_date',
        'answers',
        'obtained_score',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'answers' => 'array',
        'obtained_score' => 'decimal:2',
        'status' => 'string',
    ];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grading(): HasOne
    {
        return $this->hasOne(Grading::class);
    }
}
