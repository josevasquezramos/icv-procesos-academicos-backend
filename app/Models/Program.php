<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = [
        'name',
        'description',
        'duration_weeks',
        'max_capacity',
        'start_date',
        'end_date',
        'price',
        'currency',
        'image_url',
        'modality',
        'required_devices',
        'status',
    ];

    protected $casts = [
        'duration_weeks' => 'integer',
        'max_capacity' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
        'modality' => 'string',
        'status' => 'string',
    ];

    // Relaciones
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'program_courses')
            ->withPivot('mandatory')
            ->withTimestamps();
    }

    public function programCourses()
    {
        return $this->hasMany(ProgramCourse::class);
    }

    public function credentials()
    {
        return $this->hasMany(Credential::class);
    }
}
