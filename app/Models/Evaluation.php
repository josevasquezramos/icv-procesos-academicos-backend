<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'title',
        'description',
        'external_url',
        'evaluation_type',
        'due_date',
        'weight',
        'teacher_creator_id',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'weight' => 'decimal:2',
    ];

    // Relaciones
    public function group()
{
    return $this->belongsTo(Group::class, 'group_id');
}

public function gradeRecords()
{
    return $this->hasMany(GradeRecord::class, 'evaluation_id');
}

    public function teacherCreator()
    {
        return $this->belongsTo(User::class, 'teacher_creator_id');
    }

    

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('evaluation_type', $type);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('due_date', '>=', now());
    }

    public function scopePast($query)
    {
        return $query->where('due_date', '<', now());
    }
}