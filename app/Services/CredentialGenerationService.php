<?php

namespace App\Services;

use App\Models\Credential;
use App\Models\FinalGrade;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CredentialGenerationService
{
    /**
     * Genera credenciales para una colección de registros FinalGrade (aprobados).
     */
    public function generateForPassedStudents(Collection $passedFinalGrades): void
    {
        foreach ($passedFinalGrades as $finalGrade) {
            
            // Verificamos si ya existe para evitar duplicados (idempotencia)
            Credential::firstOrCreate(
                [
                    'user_id' => $finalGrade->user_id,
                    'group_id' => $finalGrade->group_id,
                ],
                [
                    'uuid' => Str::uuid(),
                    'issue_date' => now()->toDateString(),
                ]
            );
        }
    }
}