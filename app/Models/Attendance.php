<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_participant_id',
        'class_id',
        'attended',
        'observations',
    ];

    protected $casts = [
        'attended' => 'boolean',
    ];

    // Relaciones
    public function groupParticipant()
    {
        return $this->belongsTo(GroupParticipant::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    // Scopes
    public function scopeAttended($query)
    {
        return $query->where('attended', true);
    }

    public function scopeAbsent($query)
    {
        return $query->where('attended', false);
    }
}