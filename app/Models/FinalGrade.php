<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group_id',
        'final_grade',
        'program_status',
        'calculation_date',
    ];

    protected $casts = [
        'final_grade' => 'decimal:2',
        'calculation_date' => 'datetime',
    ];

    // Relaciones
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // Scopes
    public function scopePassed($query)
    {
        return $query->where('program_status', 'Passed');
    }

    public function scopeFailed($query)
    {
        return $query->where('program_status', 'Failed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('program_status', 'In_progress');
    }
}