<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassModel extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'group_id',
        'class_name',
        'class_date',
        'start_time',
        'end_time',
        'platform',
        'meeting_url',
        'external_meeting_id',
        'meeting_password',
        'allow_recording',
        'recording_url',
        'max_participants',
        'class_status',
    ];

    protected $casts = [
        'class_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'allow_recording' => 'boolean',
        'max_participants' => 'integer',
        'class_status' => 'string',
        'created_at' => 'datetime',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
