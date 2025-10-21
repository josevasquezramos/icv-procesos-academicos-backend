<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinalGrade;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FinalGradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $finalGrades = FinalGrade::with(['student', 'group'])->get();
        return response()->json($finalGrades);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'group_id' => 'required|exists:groups,id',
            'final_grade' => 'required|numeric|min:0|max:20',
            'program_status' => 'required|in:Passed,Failed,In_progress',
            'calculation_date' => 'required|date',
        ]);

        // Evitar duplicados: un usuario no puede tener dos notas finales en el mismo grupo
        $exists = FinalGrade::where('user_id', $validated['user_id'])
            ->where('group_id', $validated['group_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'This student already has a final grade for this group'
            ], 422);
        }

        $finalGrade = FinalGrade::create($validated);

        return response()->json($finalGrade->load(['student', 'group']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $finalGrade = FinalGrade::with(['student', 'group'])->findOrFail($id);
        return response()->json($finalGrade);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $finalGrade = FinalGrade::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'group_id' => 'sometimes|required|exists:groups,id',
            'final_grade' => 'sometimes|required|numeric|min:0|max:20',
            'program_status' => 'sometimes|required|in:Passed,Failed,In_progress',
            'calculation_date' => 'sometimes|required|date',
        ]);

        $finalGrade->update($validated);

        return response()->json($finalGrade->load(['student', 'group']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $finalGrade = FinalGrade::findOrFail($id);
        $finalGrade->delete();

        return response()->json(['message' => 'Final grade deleted successfully'], 200);
    }
}