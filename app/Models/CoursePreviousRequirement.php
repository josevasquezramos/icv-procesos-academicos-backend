<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoursePreviousRequirement extends Model
{

    protected $fillable = [
        'course_id',
        'previous_course_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function previousCourse(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'previous_course_id');
    }
}
