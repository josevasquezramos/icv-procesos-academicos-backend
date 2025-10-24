<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'first_name',
        'last_name',
        'full_name',
        'dni',
        'document',
        'email',
        'email_verified_at',
        'phone_number',
        'address',
        'birth_date',
        'role',
        'password',
        'gender',
        'country',
        'country_location',
        'timezone',
        'profile_photo',
        'status',
        'synchronized',
        'last_access_ip',
        'last_access',
        'last_connection',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date' => 'date',
            'role' => 'array',
            'password' => 'hashed',
            'synchronized' => 'boolean',
            'last_access' => 'datetime',
            'last_connection' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected $attributes = [
        'role' => '["student"]',
        'status' => 'active',
        'synchronized' => true,
        'timezone' => 'America/Lima',
    ];

    // Relaciones
    public function teacherProfile()
{
    return $this->hasOne(TeacherProfile::class, 'user_id');
}


 public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function instructor()
    {
        return $this->hasOne(Instructor::class, 'user_id');
    }


    public function groupParticipants()
{
    return $this->hasMany(GroupParticipant::class, 'user_id');
}

    public function enrolledGroups()
    {
        return $this->belongsToMany(Group::class, 'group_participants')
            ->withPivot('role', 'enrollment_status', 'assignment_date')
            ->withTimestamps();
    }

    public function gradeRecords()
    {
        return $this->hasMany(GradeRecord::class);
    }

    public function finalGrades()
{
    return $this->hasMany(FinalGrade::class, 'user_id');
}

    public function credentials()
{
    return $this->hasMany(Credential::class, 'user_id');
}

    public function createdEvaluations()
    {
        return $this->hasMany(Evaluation::class, 'teacher_creator_id');
    }

    // Helpers
    public function isTeacher()
    {
        return $this->teacherProfile()->exists();
    }

    // Métodos de utilidad
    public function isStudent()
    {
        return in_array('student', $this->role ?? []);
    }

    public function isAdmin()
    {
        return in_array('admin', $this->role ?? []);
    }

    public function getFullNameAttribute()
    {
        return $this->full_name ?? "{$this->first_name} {$this->last_name}";
    }


      // Scopes para diferentes roles
    public function scopeStudents($query)
    {
        return $query->whereJsonContains('role', 'student');
    }

    public function scopeTeachers($query)
    {
        return $query->whereJsonContains('role', 'teacher');
    }

    public function scopeAdmins($query)
    {
        return $query->whereJsonContains('role', 'admin');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

