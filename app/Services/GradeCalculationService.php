<?php

namespace App\Services;

use App\Models\Group;
use App\Models\AcademicSetting;
use App\Models\FinalGrade;
use App\Models\GradeRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class GradeCalculationService
{
    /**
     * Calcula las notas finales para todos los estudiantes de un grupo.
     * Guarda el FinalGrade y devuelve una colección de los que aprobaron.
     */
    public function calculateFinalGradesForGroup(Group $group): Collection
    {
        // 1. Obtener la configuración académica (nota mínima)
        $settings = AcademicSetting::first();
        if (!$settings) {
            // Si no hay settings, usamos un fallback razonable
            Log::warning('No se encontró AcademicSetting. Usando fallback de 11.00 para aprobar.');
            $min_passing_grade = 11.00;
        } else {
            $min_passing_grade = $settings->min_passing_grade;
        }

        // 2. Obtener todos los estudiantes activos del grupo
        $students = $group->students()->where('enrollment_status', 'active')->get();

        $passedFinalGrades = collect();

        // 3. Iterar por cada estudiante para calcular su nota
        foreach ($students as $student) {
            
            // 4. Obtener todos los GradeRecord del estudiante PARA ESTE GRUPO
            $gradeRecords = GradeRecord::where('user_id', $student->id)
                ->whereHas('evaluation', function ($query) use ($group) {
                    $query->where('group_id', $group->id);
                })
                ->with('evaluation:id,weight') // Traemos el peso de la evaluación
                ->get();

            // 5. Calcular promedio ponderado
            $totalWeight = $gradeRecords->sum('evaluation.weight');
            $totalPoints = $gradeRecords->sum(function ($record) {
                // Multiplicamos la nota por el peso de su evaluación
                return $record->obtained_grade * $record->evaluation->weight;
            });

            // Evitar división por cero si no hay evaluaciones o pesos
            $finalGrade = ($totalWeight > 0) ? ($totalPoints / $totalWeight) : 0;
            
            // Redondear a 2 decimales
            $finalGrade = round($finalGrade, 2); 

            // 6. Determinar estado
            $status = ($finalGrade >= $min_passing_grade) ? 'Passed' : 'Failed';

            // 7. Guardar el registro en final_grades
            $finalGradeRecord = FinalGrade::updateOrCreate(
                [
                    'user_id' => $student->id,
                    'group_id' => $group->id,
                ],
                [
                    'final_grade' => $finalGrade,
                    'program_status' => $status,
                    'calculation_date' => now(),
                ]
            );

            // 8. Si aprobó, añadirlo a la colección de retorno
            if ($status === 'Passed') {
                $passedFinalGrades->push($finalGradeRecord);
            }
        }

        return $passedFinalGrades;
    }
}