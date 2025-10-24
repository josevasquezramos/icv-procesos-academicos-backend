<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GraduateSurvey extends Model
{
    use HasFactory, SoftDeletes;

    // Usar la tabla 'surveys' que ya existe
    protected $table = 'surveys';

    protected $fillable = [
        'title',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class, 'survey_id')->orderBy('order');
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class, 'survey_id');
    }

    public function userResponse($userId)
    {
        return $this->hasOne(SurveyResponse::class, 'survey_id')
            ->where('user_id', $userId);
    }
}