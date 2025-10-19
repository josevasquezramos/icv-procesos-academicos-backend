<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'user_id',
        'obtained_grade',
        'feedback',
        'record_date',
    ];

    protected $casts = [
        'obtained_grade' => 'decimal:2',
        'record_date' => 'datetime',
    ];

    // Relaciones
    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeByStudent($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePassed($query, $passingGrade = 60)
    {
        return $query->where('obtained_grade', '>=', $passingGrade);
    }

    public function scopeFailed($query, $passingGrade = 60)
    {
        return $query->where('obtained_grade', '<', $passingGrade);
    }
}