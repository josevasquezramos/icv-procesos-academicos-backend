<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'professional_title',
        'specialty',
        'experience_years',
        'biography',
    ];

    protected $casts = [
        'experience_years' => 'integer',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdEvaluations()
    {
        return $this->hasMany(Evaluation::class, 'teacher_creator_id');
    }
}