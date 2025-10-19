<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'name',
        'description',
        'level',
        'course_image',
        'video_url',
        'duration',
        'sessions',
        'selling_price',
        'discount_price',
        'prerequisites',
        'certificate_name',
        'certificate_issuer',
        'bestseller',
        'featured',
        'highest_rated',
        'status',
    ];

    protected $casts = [
        'duration' => 'decimal:2',
        'sessions' => 'integer',
        'selling_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'certificate_name' => 'boolean',
        'bestseller' => 'boolean',
        'featured' => 'boolean',
        'highest_rated' => 'boolean',
        'status' => 'boolean',
        'level' => 'string',
    ];
    
// Relaciones
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_courses')
            ->withPivot('mandatory')
            ->withTimestamps();
    }

    public function programCourses()
    {
        return $this->hasMany(ProgramCourse::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function previousRequirements()
    {
        return $this->belongsToMany(Course::class, 'course_previous_requirements', 'course_id', 'previous_course_id')
            ->withTimestamps();
    }

    public function requiredFor()
    {
        return $this->belongsToMany(Course::class, 'course_previous_requirements', 'previous_course_id', 'course_id')
            ->withTimestamps();
    }

    public function coursePreviousRequirements()
    {
        return $this->hasMany(CoursePreviousRequirement::class);
    }
}
