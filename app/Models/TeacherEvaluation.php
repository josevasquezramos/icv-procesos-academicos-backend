<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherEvaluation extends Model
{
    protected $fillable = [
        'evaluator_id',
        'group_id',
        'teacher_id',
        'answers',
        'score',
        'created_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
