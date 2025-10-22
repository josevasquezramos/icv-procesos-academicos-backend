<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'user_id',
        'role',
        'enrollment_status',
        'assignment_date',
    ];

    protected $casts = [
        'assignment_date' => 'datetime',
    ];

    // Relaciones
    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

public function group()
{
    return $this->belongsTo(Group::class, 'group_id');
}

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Scopes
    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    public function scopeTeachers($query)
    {
        return $query->where('role', 'teacher');
    }

    public function scopeActive($query)
    {
        return $query->where('enrollment_status', 'active');
    }
}