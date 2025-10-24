<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmploymentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employment_status',
        'company_name',
        'position',
        'start_date',
        'salary_range',
        'industry',
        'is_related_to_studies',
    ];

    protected $casts = [
        'start_date' => 'date',
        'is_related_to_studies' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes útiles
    public function scopeEmployed($query)
    {
        return $query->whereIn('employment_status', ['empleado', 'independiente', 'emprendedor']);
    }

    public function scopeByIndustry($query, string $industry)
    {
        return $query->where('industry', $industry);
    }

    public function scopeRelatedToStudies($query)
    {
        return $query->where('is_related_to_studies', true);
    }
}