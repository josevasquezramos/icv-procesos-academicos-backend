<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupParticipant extends Model
{
    protected $fillable = [
        'group_id',
        'user_id',
        'role',
        'teacher_function',
        'enrollment_status',
        'assignment_date',
        'schedule',
    ];

    protected $casts = [
        'assignment_date' => 'datetime',
        'schedule' => 'array',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
