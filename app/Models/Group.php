<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'code',
        'name',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relaciones
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function classes()
    {
        return $this->hasMany(Classes::class);
    }

    public function participants()
    {
        return $this->hasMany(GroupParticipant::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'group_participants')
            ->wherePivot('role', 'student')
            ->withPivot('enrollment_status', 'assignment_date')
            ->withTimestamps();
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'group_participants')
            ->wherePivot('role', 'teacher')
            ->withPivot('enrollment_status', 'assignment_date')
            ->withTimestamps();
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function finalGrades()
    {
        return $this->hasMany(FinalGrade::class);
    }

    public function credentials()
    {
        return $this->hasMany(Credential::class);
    }
}