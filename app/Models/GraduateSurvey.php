<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GraduateSurvey extends Model
{
    protected $fillable = [
        'graduate_id',
        'date',
        'employability',
        'satisfaction',
        'curriculum_feedback',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function graduate(): BelongsTo
    {
        return $this->belongsTo(Graduate::class);
    }
}
