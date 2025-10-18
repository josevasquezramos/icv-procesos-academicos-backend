<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherApplication extends Model
{
    protected $fillable = [
        'recruitment_id',
        'user_id',
        'cv',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function recruitment(): BelongsTo
    {
        return $this->belongsTo(TeacherRecruitment::class, 'recruitment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
