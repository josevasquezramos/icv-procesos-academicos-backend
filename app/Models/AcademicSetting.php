<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicSetting extends Model
{
    protected $table = 'academic_settings';

    protected $fillable = [
        'base_grade',
        'min_passing_grade',
    ];

    protected $casts = [
        'base_grade' => 'decimal:2',
        'min_passing_grade' => 'decimal:2',
    ];
}
