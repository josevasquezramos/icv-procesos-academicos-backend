<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeacherRecruitment extends Model
{
    protected $fillable = [
        'request_date',
        'title',
        'description',
        'required_profile',
        'status',
    ];

    protected $casts = [
        'request_date' => 'date',
        'status' => 'string',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(TeacherApplication::class, 'recruitment_id');
    }
}
