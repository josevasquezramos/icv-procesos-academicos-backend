<?php

namespace App\Jobs;

use App\Models\Group;
use App\Services\CredentialGenerationService;
use App\Services\GradeCalculationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessGroupCompletion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Group $group
    ) {}

    /**
     * Execute the job.
     */
    public function handle(
        GradeCalculationService $gradeService,
        CredentialGenerationService $credentialService
    ): void {
        
        Log::info("Iniciando proceso de completado para el Grupo ID: {$this->group->id}");

        try {
            DB::transaction(function () use ($gradeService, $credentialService) {
                
                // PASO 1: Calcular notas finales
                // Este método devolverá solo los que aprobaron.
                $passedFinalGrades = $gradeService->calculateFinalGradesForGroup($this->group);

                // PASO 2: Generar credenciales solo para los aprobados
                $credentialService->generateForPassedStudents($passedFinalGrades);

                // PASO 3: Marcar el grupo como completado
                $this->group->update(['status' => 'completed']);
                
                // Actualizar estado de participantes 'active' a 'finished'
                $this->group->participants()
                            ->where('enrollment_status', 'active')
                            ->update(['enrollment_status' => 'finished']);

            });

            Log::info("Proceso de completado finalizado con éxito para el Grupo ID: {$this->group->id}");
            
            // (Opcional) Enviar notificación al profesor
            // ...

        } catch (\Exception $e) {
            // Manejo de error
            Log::error("Error completando Grupo ID {$this->group->id}: " . $e->getMessage());
            
            // (Opcional) Reintentar el job si es apropiado
            // o notificar al profesor del fallo
        }
    }
}
