<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grading extends Model
{
    protected $fillable = [
        'attempt_id',
        'teacher_grader_id',
        'grading_detail',
        'feedback',
        'grading_date',
    ];

    protected $casts = [
        'grading_detail' => 'array',
        'grading_date' => 'datetime',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(Attempt::class);
    }

    public function teacherGrader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_grader_id');
    }
}
