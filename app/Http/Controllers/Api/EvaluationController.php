<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $evaluations = Evaluation::with(['group', 'teacherCreator', 'gradeRecords'])->get();
        return response()->json($evaluations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'external_url' => 'nullable|url|max:500',
            'evaluation_type' => 'required|string|max:100',
            'due_date' => 'required|date',
            'weight' => 'required|numeric|min:0|max:100',
            'teacher_creator_id' => 'required|exists:users,id',
        ]);

        $evaluation = Evaluation::create($validated);

        return response()->json($evaluation->load(['group', 'teacherCreator']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $evaluation = Evaluation::with(['group', 'teacherCreator', 'gradeRecords.groupParticipant.user'])
            ->findOrFail($id);
        return response()->json($evaluation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $evaluation = Evaluation::findOrFail($id);

        $validated = $request->validate([
            'group_id' => 'sometimes|required|exists:groups,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'external_url' => 'nullable|url|max:500',
            'evaluation_type' => 'sometimes|required|string|max:100',
            'due_date' => 'sometimes|required|date',
            'weight' => 'sometimes|required|numeric|min:0|max:100',
            'teacher_creator_id' => 'sometimes|required|exists:users,id',
        ]);

        $evaluation->update($validated);

        return response()->json($evaluation->load(['group', 'teacherCreator']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $evaluation = Evaluation::findOrFail($id);
        $evaluation->delete();

        return response()->json(['message' => 'Evaluation deleted successfully'], 200);
    }
}