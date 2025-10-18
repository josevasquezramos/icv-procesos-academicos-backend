<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'group_participant_id',
        'class_id',
        'attended',
        'entry_time',
        'exit_time',
        'connected_minutes',
        'connection_ip',
        'device',
        'approximate_location',
        'connection_quality',
        'observations',
        'cloud_synchronized',
        'record_date',
    ];

    protected $casts = [
        'attended' => 'string',
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'connected_minutes' => 'integer',
        'cloud_synchronized' => 'boolean',
        'record_date' => 'datetime',
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(GroupParticipant::class, 'group_participant_id');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}
