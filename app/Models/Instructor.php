<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'user_id',
        'bio',
        'expertise_area',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courseOfferings()
    {
        return $this->hasMany(CourseOffering::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_instructors');
    }
}