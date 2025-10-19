<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoursePreviousRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'previous_course_id',
    ];

    // Relaciones
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function previousCourse()
    {
        return $this->belongsTo(Course::class, 'previous_course_id');
    }
}