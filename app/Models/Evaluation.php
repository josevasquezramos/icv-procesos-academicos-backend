<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    protected $fillable = [
        'group_id',
        'title',
        'evaluation_type',
        'start_date',
        'end_date',
        'duration_minutes',
        'total_score',
        'status',
        'teacher_creator_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'duration_minutes' => 'integer',
        'total_score' => 'decimal:2',
        'status' => 'string',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function teacherCreator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_creator_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }
}
