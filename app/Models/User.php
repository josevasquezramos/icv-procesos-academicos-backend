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

    /**
     * Respuestas de encuestas del usuario
     */
    public function surveyResponses()
    {
        return $this->hasMany(SurveyResponse::class, 'respondent_user_id');
    }

    /**
     * Encuestas creadas por el usuario (admin)
     */
    public function createdSurveys()
    {
        return $this->hasMany(Survey::class, 'created_by_user_id');
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin()
    {
        $roles = $this->role ?? [];
        return in_array('admin', $roles) || in_array('administrador', $roles);
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole($role)
    {
        $roles = $this->role ?? [];
        return in_array($role, $roles);
    }

    /**
     * Verificar si el usuario tiene alguno de los roles especificados
     */
    public function hasAnyRole(array $roles)
    {
        $userRoles = $this->role ?? [];
        return count(array_intersect($userRoles, $roles)) > 0;
    }
}
