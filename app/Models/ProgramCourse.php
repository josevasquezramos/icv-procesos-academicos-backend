<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'course_id',
        'mandatory',
    ];

    protected $casts = [
        'mandatory' => 'boolean',
    ];

    // Relaciones
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}