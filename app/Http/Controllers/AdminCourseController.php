<?php
// app/Http/Controllers/AdminCourseController.php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class AdminCourseController extends Controller
{
    public function pendingCourses(Request $request)
    {
        $query = Course::pendingApproval()->with(['categories', 'instructors']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }

    public function approveCourse($id)
    {
        $course = Course::pendingApproval()->findOrFail($id);

        $course->update([
            'status' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Curso aprobado exitosamente'
        ]);
    }

    public function rejectCourse(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $course = Course::pendingApproval()->findOrFail($id);

        // Aquí podrías guardar el motivo del rechazo en otra tabla
        // Por ahora simplemente eliminamos el curso
        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Curso rechazado exitosamente'
        ]);
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id'
        ]);

        Course::whereIn('id', $request->course_ids)
              ->pendingApproval()
              ->update(['status' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Cursos aprobados exitosamente'
        ]);
    }
}