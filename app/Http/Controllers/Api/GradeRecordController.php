<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\GradeRecord;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GradeRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $gradeRecords = GradeRecord::with(['evaluation', 'student'])->get();
        return response()->json($gradeRecords);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'evaluation_id' => 'required|exists:evaluations,id',
            'user_id' => 'required|exists:users,id',
            'obtained_grade' => 'required|numeric|min:0|max:20',
            'feedback' => 'nullable|string',
            'record_date' => 'required|date',
        ]);

        // Evitar duplicados: un estudiante no puede tener dos registros de nota para la misma evaluación
        $exists = GradeRecord::where('evaluation_id', $validated['evaluation_id'])
            ->where('user_id', $validated['user_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'This student already has a grade record for this evaluation'
            ], 422);
        }

        $gradeRecord = GradeRecord::create($validated);

        return response()->json($gradeRecord->load(['evaluation', 'student']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $gradeRecord = GradeRecord::with(['evaluation', 'student'])->findOrFail($id);
        return response()->json($gradeRecord);
    }

    /**
     * Obtener evaluaciones y calificaciones por grupo.
     */
    public function getByGroup(string $groupId): JsonResponse
    {
        try {
            // Obtener las evaluaciones del grupo
            $evaluations = Evaluation::with([
                'gradeRecords.student' // Trae las calificaciones con los estudiantes
            ])->where('group_id', $groupId)->get();

            if ($evaluations->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron evaluaciones para este grupo.',
                    'evaluations' => [],
                ], 200);
            }

            // Formatear la respuesta para incluir estudiantes y sus calificaciones
            $evaluationsData = $evaluations->map(function ($evaluation) {
                return [
                    'id' => $evaluation->id,
                    'title' => $evaluation->title,
                    'description' => $evaluation->description,
                    'evaluation_type' => $evaluation->evaluation_type,
                    'due_date' => $evaluation->due_date,
                    'weight' => $evaluation->weight,
                    'teacher_creator_id' => $evaluation->teacher_creator_id,
                    'grades' => $evaluation->gradeRecords->map(function ($gradeRecord) {
                        return [
                            'student_id' => $gradeRecord->student->id,
                            'student_name' => $gradeRecord->student->full_name,
                            'obtained_grade' => $gradeRecord->obtained_grade,
                            'feedback' => $gradeRecord->feedback,
                            'record_date' => $gradeRecord->record_date,
                        ];
                    }),
                ];
            });

            return response()->json([
                'message' => 'Evaluaciones y calificaciones obtenidas correctamente.',
                'evaluations' => $evaluationsData,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener evaluaciones y calificaciones del grupo.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $gradeRecord = GradeRecord::findOrFail($id);

        $validated = $request->validate([
            'evaluation_id' => 'sometimes|required|exists:evaluations,id',
            'user_id' => 'sometimes|required|exists:users,id',
            'obtained_grade' => 'sometimes|required|numeric|min:0|max:20',
            'feedback' => 'nullable|string',
            'record_date' => 'sometimes|required|date',
        ]);

        $gradeRecord->update($validated);

        return response()->json($gradeRecord->load(['evaluation', 'student']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $gradeRecord = GradeRecord::findOrFail($id);
        $gradeRecord->delete();

        return response()->json(['message' => 'Grade record deleted successfully'], 200);
    }
}