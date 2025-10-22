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
        'uuid',
        'group_id',
        'type',
        'issue_date',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    // Relaciones
   public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

public function group()
{
    return $this->belongsTo(Group::class, 'group_id');
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
}