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
        'role' => '"student"',
        'status' => 'active',
        'synchronized' => true,
        'timezone' => 'America/Lima',
    ];

    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function groupParticipants(): HasMany
    {
        return $this->hasMany(GroupParticipant::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }

    public function teacherApplications(): HasMany
    {
        return $this->hasMany(TeacherApplication::class);
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

    public function createdEvaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'teacher_creator_id');
    }

    public function gradings(): HasMany
    {
        return $this->hasMany(Grading::class, 'teacher_grader_id');
    }

    public function teacherEvaluations(): HasMany
    {
        return $this->hasMany(TeacherEvaluation::class, 'teacher_id');
    }

    public function evaluationsAsEvaluator(): HasMany
    {
        return $this->hasMany(TeacherEvaluation::class, 'evaluator_id');
    }
}
