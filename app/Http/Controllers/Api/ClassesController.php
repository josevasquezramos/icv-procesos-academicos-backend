<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClassesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $classes = Classes::with(['group', 'attendances'])->get();
        return response()->json($classes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'class_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_date' => 'required|date',
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
            'class_status' => 'nullable|string|max:50',
        ]);

        $class = Classes::create($validated);

        return response()->json($class->load(['group']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $class = Classes::with(['group', 'attendances.groupParticipant.user'])->findOrFail($id);
        return response()->json($class);
    }

    /**
     * Display a listing of classes by group ID.
     */
    public function getByGroup(string $groupId): JsonResponse
    {
        $classes = Classes::with(['group', 'attendances'])
            ->where('group_id', $groupId)
            ->get();

        if ($classes->isEmpty()) {
            return response()->json(['message' => 'No classes found for this group'], 404);
        }

        return response()->json($classes);
    }

    public function toggleVisibility(Request $request, string $classId, string $materialId): JsonResponse
    {
        $material = Classes::where('class_id', $classId)
            ->findOrFail($materialId);

        $validated = $request->validate([
            'visibility' => 'required|string|in:public,private'
        ]);

        $material->update(['visibility' => $validated['visibility']]);

        return response()->json($material);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $class = Classes::findOrFail($id);

        $validated = $request->validate([
            'group_id' => 'sometimes|required|exists:groups,id',
            'class_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'class_date' => 'sometimes|required|date',
            'start_time' => 'sometimes|required|date_format:Y-m-d H:i:s',
            'end_time' => 'sometimes|required|date_format:Y-m-d H:i:s|after:start_time',
            'class_status' => 'nullable|string|max:50',
        ]);

        $class->update($validated);

        return response()->json($class->load(['group']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $class = Classes::findOrFail($id);
        $class->delete();

        return response()->json(['message' => 'Class deleted successfully'], 200);
    }
}
