<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgramCourse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProgramCourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $programCourses = ProgramCourse::with(['program', 'course'])->get();
        return response()->json($programCourses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'course_id' => 'required|exists:courses,id',
            'mandatory' => 'required|boolean',
        ]);

        // Evitar duplicados: un curso no puede estar dos veces en el mismo programa
        $exists = ProgramCourse::where('program_id', $validated['program_id'])
            ->where('course_id', $validated['course_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'This course is already assigned to this program'
            ], 422);
        }

        $programCourse = ProgramCourse::create($validated);

        return response()->json($programCourse->load(['program', 'course']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $programCourse = ProgramCourse::with(['program', 'course'])->findOrFail($id);
        return response()->json($programCourse);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $programCourse = ProgramCourse::findOrFail($id);

        $validated = $request->validate([
            'program_id' => 'sometimes|required|exists:programs,id',
            'course_id' => 'sometimes|required|exists:courses,id',
            'mandatory' => 'sometimes|required|boolean',
        ]);

        $programCourse->update($validated);

        return response()->json($programCourse->load(['program', 'course']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $programCourse = ProgramCourse::findOrFail($id);
        $programCourse->delete();

        return response()->json(['message' => 'Program course deleted successfully'], 200);
    }
}