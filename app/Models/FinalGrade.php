<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalGrade extends Model
{
    protected $fillable = [
        'user_id',
        'group_id',
        'configuration_id',
        'final_grade',
        'partial_average',
        'program_status',
        'certification_obtained',
        'calculation_date',
    ];

    protected $casts = [
        'final_grade' => 'decimal:2',
        'partial_average' => 'decimal:2',
        'program_status' => 'string',
        'certification_obtained' => 'boolean',
        'calculation_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function configuration(): BelongsTo
    {
        return $this->belongsTo(GradeConfiguration::class, 'configuration_id');
    }
}
