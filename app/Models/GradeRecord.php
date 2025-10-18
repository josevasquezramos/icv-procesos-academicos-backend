<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeRecord extends Model
{
    protected $fillable = [
        'user_id',
        'evaluation_id',
        'group_id',
        'configuration_id',
        'obtained_grade',
        'grade_weight',
        'grade_type',
        'status',
        'record_date',
    ];

    protected $casts = [
        'obtained_grade' => 'decimal:2',
        'grade_weight' => 'decimal:2',
        'grade_type' => 'string',
        'status' => 'string',
        'record_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function configuration(): BelongsTo
    {
        return $this->belongsTo(GradeConfiguration::class, 'configuration_id');
    }

    public function gradeChanges(): HasMany
    {
        return $this->hasMany(GradeChange::class, 'record_id');
    }
}
