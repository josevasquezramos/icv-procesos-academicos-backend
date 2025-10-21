<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoursePreviousRequirement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CoursePreviousRequirementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $requirements = CoursePreviousRequirement::with(['course', 'previousCourse'])->get();
        return response()->json($requirements);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'previous_course_id' => 'required|exists:courses,id|different:course_id',
        ]);

        // Evitar duplicados
        $exists = CoursePreviousRequirement::where('course_id', $validated['course_id'])
            ->where('previous_course_id', $validated['previous_course_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'This prerequisite relationship already exists'
            ], 422);
        }

        $requirement = CoursePreviousRequirement::create($validated);

        return response()->json($requirement->load(['course', 'previousCourse']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $requirement = CoursePreviousRequirement::with(['course', 'previousCourse'])->findOrFail($id);
        return response()->json($requirement);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $requirement = CoursePreviousRequirement::findOrFail($id);

        $validated = $request->validate([
            'course_id' => 'sometimes|required|exists:courses,id',
            'previous_course_id' => 'sometimes|required|exists:courses,id|different:course_id',
        ]);

        $requirement->update($validated);

        return response()->json($requirement->load(['course', 'previousCourse']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $requirement = CoursePreviousRequirement::findOrFail($id);
        $requirement->delete();

        return response()->json(['message' => 'Course prerequisite deleted successfully'], 200);
    }
}