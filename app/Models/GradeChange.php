<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeChange extends Model
{
    protected $fillable = [
        'record_id',
        'previous_grade',
        'new_grade',
        'reason',
        'user_id',
        'change_date',
    ];

    protected $casts = [
        'previous_grade' => 'decimal:2',
        'new_grade' => 'decimal:2',
        'change_date' => 'datetime',
    ];

    public function record(): BelongsTo
    {
        return $this->belongsTo(GradeRecord::class, 'record_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
