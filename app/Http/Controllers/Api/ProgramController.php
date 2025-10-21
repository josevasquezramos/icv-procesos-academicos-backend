<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $programs = Program::with(['courses', 'programCourses'])->get();
        return response()->json($programs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_weeks' => 'required|integer|min:1',
            'max_capacity' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'image_url' => 'nullable|url|max:500',
            'modality' => 'required|string|max:50',
            'required_devices' => 'nullable|string',
            'status' => 'required|string|max:50',
        ]);

        $program = Program::create($validated);

        return response()->json($program, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $program = Program::with(['courses', 'programCourses.course'])->findOrFail($id);
        return response()->json($program);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $program = Program::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'duration_weeks' => 'sometimes|required|integer|min:1',
            'max_capacity' => 'nullable|integer|min:1',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'price' => 'sometimes|required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'image_url' => 'nullable|url|max:500',
            'modality' => 'sometimes|required|string|max:50',
            'required_devices' => 'nullable|string',
            'status' => 'sometimes|required|string|max:50',
        ]);

        $program->update($validated);

        return response()->json($program);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $program = Program::findOrFail($id);
        $program->delete();

        return response()->json(['message' => 'Program deleted successfully'], 200);
    }
}