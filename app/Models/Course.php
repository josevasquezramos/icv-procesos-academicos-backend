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

    public function programCourses(): HasMany
    {
        return $this->hasMany(ProgramCourse::class);
    }

    public function previousRequirements(): HasMany
    {
        return $this->hasMany(CoursePreviousRequirement::class, 'course_id');
    }

    public function requiredBy(): HasMany
    {
        return $this->hasMany(CoursePreviousRequirement::class, 'previous_course_id');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }
}
