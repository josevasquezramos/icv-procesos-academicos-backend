<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Credential extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'program_id',
        'type',
        'issue_date',
        'verification_code',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // Scopes
    public function scopeCertificates($query)
    {
        return $query->where('type', 'certificate');
    }

    public function scopeDiplomas($query)
    {
        return $query->where('type', 'diploma');
    }

    // Boot method para generar código de verificación
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($credential) {
            if (empty($credential->verification_code)) {
                $credential->verification_code = strtoupper(Str::random(10));
            }
        });
    }
}