<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmploymentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GraduateStatisticsController extends Controller
{
    /**
     * Obtener estadísticas generales de empleabilidad (solo para administradores)
     */
    public function index(Request $request)
    {
        // Verificar que el usuario es administrador
        if (!$this->isAdmin($request->user())) {
            return response()->json([
                'message' => 'No autorizado. Solo administradores pueden ver estadísticas.'
            ], 403);
        }

        $statistics = $this->getStatisticsData();

        return response()->json([
            'message' => 'Estadísticas obtenidas exitosamente',
            'data' => $statistics
        ]);
    }

    /**
     * Exportar reporte en formato JSON (base para PDF/Excel)
     */
    public function exportReport(Request $request)
    {
        if (!$this->isAdmin($request->user())) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $statistics = $this->getStatisticsData();

        return response()->json([
            'message' => 'Reporte generado exitosamente',
            'data' => $statistics,
            'generated_at' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Verificar si el usuario es administrador
     */
    private function isAdmin($user): bool
    {
        if (!$user) {
            return false;
        }

        $userRole = $user->role;
        
        // Convertir a string si es array o JSON
        if (is_array($userRole)) {
            $userRole = $userRole[0] ?? '';
        } elseif (is_string($userRole) && str_starts_with($userRole, '[')) {
            $decoded = json_decode($userRole, true);
            $userRole = is_array($decoded) ? ($decoded[0] ?? '') : $userRole;
        }
        
        return in_array(strtolower($userRole), ['admin', 'administrador']);
    }

    /**
     * Obtener los datos de estadísticas
     */
    private function getStatisticsData(): array
    {
        // Total de egresados (usuarios que tienen perfil laboral o completaron algún curso)
        $totalGraduates = EmploymentProfile::distinct('user_id')->count();

        // Empleados (estados: empleado, independiente, emprendedor)
        $employedCount = EmploymentProfile::whereIn('employment_status', [
            'empleado', 
            'independiente', 
            'emprendedor'
        ])->count();

        // Tasa de empleabilidad
        $employmentRate = $totalGraduates > 0 
            ? round(($employedCount / $totalGraduates) * 100, 1) 
            : 0;

        // Rango salarial promedio más común
        $averageSalaryRange = $this->getAverageSalaryRange();

        // Trabajo relacionado con estudios
        $relatedWorkCount = EmploymentProfile::where('is_related_to_studies', true)
            ->whereIn('employment_status', ['empleado', 'independiente', 'emprendedor'])
            ->count();
        
        $relatedWorkPercentage = $employedCount > 0 
            ? round(($relatedWorkCount / $employedCount) * 100, 1) 
            : 0;

        // Top industrias
        $topIndustries = $this->getTopIndustries();

        // Distribución por estado laboral
        $employmentByStatus = $this->getEmploymentByStatus($totalGraduates);

        return [
            'total_graduates' => $totalGraduates,
            'employed_count' => $employedCount,
            'employment_rate' => $employmentRate,
            'average_salary_range' => $averageSalaryRange,
            'related_work_percentage' => $relatedWorkPercentage,
            'top_industries' => $topIndustries,
            'employment_by_status' => $employmentByStatus,
        ];
    }

    /**
     * Obtener el rango salarial promedio
     */
    private function getAverageSalaryRange(): string
    {
        $mostCommonRange = EmploymentProfile::select('salary_range', DB::raw('count(*) as count'))
            ->whereNotNull('salary_range')
            ->groupBy('salary_range')
            ->orderBy('count', 'desc')
            ->first();

        if (!$mostCommonRange) {
            return 'No disponible';
        }

        $rangeMap = [
            'menos-1000' => 'Menos de S/ 1,000',
            '1000-2000' => 'S/ 1,000 - S/ 2,000',
            '2000-3000' => 'S/ 2,000 - S/ 3,000',
            '3000-5000' => 'S/ 3,000 - S/ 5,000',
            '5000-8000' => 'S/ 5,000 - S/ 8,000',
            'mas-8000' => 'Más de S/ 8,000',
        ];

        return $rangeMap[$mostCommonRange->salary_range] ?? 'No disponible';
    }

    /**
     * Obtener top 5 industrias
     */
    private function getTopIndustries(): array
    {
        $industries = EmploymentProfile::select('industry', DB::raw('count(*) as count'))
            ->whereNotNull('industry')
            ->groupBy('industry')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        if ($industries->isEmpty()) {
            return [];
        }

        $total = $industries->sum('count');

        $industryNames = [
            'tecnologia' => 'Tecnología',
            'educacion' => 'Educación',
            'salud' => 'Salud',
            'finanzas' => 'Finanzas',
            'retail' => 'Retail / Comercio',
            'manufactura' => 'Manufactura',
            'construccion' => 'Construcción',
            'servicios' => 'Servicios',
            'gobierno' => 'Gobierno',
            'otro' => 'Otros',
        ];

        return $industries->map(function ($industry) use ($total, $industryNames) {
            return [
                'name' => $industryNames[$industry->industry] ?? ucfirst($industry->industry),
                'count' => $industry->count,
                'percentage' => $total > 0 ? round(($industry->count / $total) * 100, 1) : 0,
            ];
        })->values()->toArray();
    }

    /**
     * Obtener distribución por estado laboral
     */
    private function getEmploymentByStatus(int $total): array
    {
        $statuses = EmploymentProfile::select('employment_status', DB::raw('count(*) as count'))
            ->groupBy('employment_status')
            ->get();

        if ($statuses->isEmpty()) {
            return [];
        }

        $statusNames = [
            'empleado' => 'Empleado',
            'independiente' => 'Independiente',
            'emprendedor' => 'Emprendedor',
            'buscando' => 'Buscando empleo',
            'estudiando' => 'Estudiando',
            'otro' => 'Otro',
        ];

        return $statuses->map(function ($status) use ($total, $statusNames) {
            return [
                'status' => $statusNames[$status->employment_status] ?? ucfirst($status->employment_status),
                'count' => $status->count,
                'percentage' => $total > 0 ? round(($status->count / $total) * 100, 1) : 0,
            ];
        })->values()->toArray();
    }
}