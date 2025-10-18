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

    public function programCourses(): HasMany
    {
        return $this->hasMany(ProgramCourse::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function diplomas(): HasMany
    {
        return $this->hasMany(Diploma::class);
    }

    public function graduates(): HasMany
    {
        return $this->hasMany(Graduate::class);
    }
}
