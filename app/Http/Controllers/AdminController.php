<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Student;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_students' => User::students()->count(),
            'total_teachers' => User::teachers()->count(),
            'total_admins' => User::admins()->count(),
            'pending_courses' => Course::pendingApproval()->count(),
            'active_students' => User::students()->active()->count(),
            'active_teachers' => User::teachers()->active()->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}