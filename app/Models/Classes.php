<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'class_name',
        'meeting_url',
        'description',
        'class_date',
        'start_time',
        'end_time',
        'class_status',
    ];

    protected $casts = [
        'class_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // Relaciones
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }

    public function classMaterials()
    {
        return $this->hasMany(ClassMaterial::class, 'class_id');
    }
}