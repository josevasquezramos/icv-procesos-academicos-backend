<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeConfiguration extends Model
{
    protected $fillable = [
        'group_id',
        'grading_system',
        'max_grade',
        'passing_grade',
        'evaluation_weight',
    ];

    protected $casts = [
        'max_grade' => 'decimal:2',
        'passing_grade' => 'decimal:2',
        'evaluation_weight' => 'decimal:2',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function gradeRecords(): HasMany
    {
        return $this->hasMany(GradeRecord::class, 'configuration_id');
    }

    public function finalGrades(): HasMany
    {
        return $this->hasMany(FinalGrade::class, 'configuration_id');
    }
}
