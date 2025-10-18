<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Graduate extends Model
{
    protected $fillable = [
        'user_id',
        'program_id',
        'graduation_date',
        'final_note',
        'state',
        'employability',
        'feedback',
    ];

    protected $casts = [
        'graduation_date' => 'date',
        'final_note' => 'float',
        'state' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function surveys(): HasMany
    {
        return $this->hasMany(GraduateSurvey::class, 'graduate_id');
    }
}
