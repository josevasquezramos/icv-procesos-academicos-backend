<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'user_id',
        'company_id',
        'document_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}