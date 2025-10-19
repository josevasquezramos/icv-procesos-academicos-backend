<?php

namespace App\Services;

use App\Models\Credential;
use App\Models\AcademicSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\URL;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificatePdfService
{
    /**
     * Genera un PDF para una credencial dada.
     */
    public function generate(Credential $credential)
    {
        // 1. Cargar relaciones necesarias
        $credential->load([
            'user',
            'group.course',
            'group.evaluations.gradeRecords' => function ($query) use ($credential) {
                $query->where('user_id', $credential->user_id);
            },
            'group.finalGrades' => function ($query) use ($credential) {
                $query->where('user_id', $credential->user_id);
            },
            'group.participants' => function ($query) use ($credential) {
                $query->where('user_id', $credential->user_id);
            },
        ]);

        // 2. Obtener configuración académica
        $academicSettings = AcademicSetting::first();
        $baseGrade = $academicSettings->base_grade ?? 20.00;
        $minPassingGrade = $academicSettings->min_passing_grade ?? 11.00;

        // 3. Recolectar Datos Básicos
        $studentName = $credential->user->full_name;
        $courseName = $credential->group->course->name;
        $dni = $credential->user->dni;

        // Formatear fechas
        $completionDate = $credential->group->end_date ? \Carbon\Carbon::parse($credential->group->end_date)->format('d/m/Y') : '';
        $issueDate = $credential->issue_date ? \Carbon\Carbon::parse($credential->issue_date)->format('d/m/Y') : '';

        // 4. Generar URL de Verificación y QR
        $verificationUrl = route('credentials.verify', ['uuid' => $credential->uuid]);
        $qrCodeImage = 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')->size(150)->generate($verificationUrl)
        );

        // 5. Logo de la institución
        $logoPath = public_path('incadev_isologotipo.svg');
        $logoBase64 = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($logoPath));

        // 6. Calcular datos académicos
        $academicData = $this->getAcademicData($credential, $baseGrade);

        // 7. Preparar los datos para la vista
        $data = [
            // Datos básicos
            'studentName' => $studentName,
            'courseName' => $courseName,
            'dni' => $dni,
            'completionDate' => $completionDate,
            'issueDate' => $issueDate,
            'verificationUrl' => $verificationUrl,
            'qrCodeImage' => $qrCodeImage,
            'logoImage' => $logoBase64,
            
            // Configuración académica
            'baseGrade' => $baseGrade,
            'minPassingGrade' => $minPassingGrade,
            
            // Datos académicos detallados
            'academicData' => $academicData,
        ];

        // 8. Cargar la Vista y Renderizar PDF
        $pdf = Pdf::loadView('pdfs.certificate', $data);
        
        // Configurar primera página horizontal, segunda vertical
        $pdf->setPaper('A4', 'landscape');

        return $pdf;
    }

    /**
     * Obtiene y calcula los datos académicos del estudiante
     */
    private function getAcademicData(Credential $credential, $baseGrade)
    {
        $group = $credential->group;
        $userId = $credential->user_id;
        
        // Nota final
        $finalGrade = $group->finalGrades->where('user_id', $userId)->first();
        
        // Evaluaciones y notas
        $evaluations = [];
        $totalWeight = 0;
        $weightedSum = 0;

        foreach ($group->evaluations as $evaluation) {
            $gradeRecord = $evaluation->gradeRecords->where('user_id', $userId)->first();
            $obtainedGrade = $gradeRecord ? $gradeRecord->obtained_grade : 0;
            
            $evaluations[] = [
                'title' => $evaluation->title,
                'type' => $evaluation->evaluation_type,
                'weight' => $evaluation->weight,
                'obtained_grade' => $obtainedGrade,
                'max_grade' => $baseGrade,
                'feedback' => $gradeRecord->feedback ?? '',
            ];

            $totalWeight += $evaluation->weight;
            $weightedSum += $obtainedGrade * ($evaluation->weight / 100);
        }

        return [
            'final_grade' => $finalGrade ? $finalGrade->final_grade : ($totalWeight > 0 ? $weightedSum : 0),
            'program_status' => $finalGrade ? $finalGrade->program_status : 'Completado',
            'evaluations' => $evaluations,
            'calculated_grade' => $totalWeight > 0 ? $weightedSum : null,
        ];
    }
}