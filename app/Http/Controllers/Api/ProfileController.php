<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\GroupParticipant;
use App\Models\FinalGrade;
use App\Models\Credential;

class ProfileController extends Controller
{
    /**
     * Obtiene el perfil del usuario autenticado según su rol
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Datos básicos del usuario
            $profileData = [
                'user' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'dni' => $user->dni,
                    'phone_number' => $user->phone_number,
                    'address' => $user->address,
                    'birth_date' => $user->birth_date,
                    'gender' => $user->gender,
                    'country' => $user->country,
                    'country_location' => $user->country_location,
                    'role' => $user->role,
                    'status' => $user->status,
                ]
            ];

            // Determinar el rol principal del usuario
            $roles = is_array($user->role) ? $user->role : json_decode($user->role, true);
            
            // Prioridad: student > teacher > admin
            if (in_array('student', $roles)) {
                $profileData['profile_type'] = 'student';
                $profileData['student_data'] = $this->getStudentData($user);
            } elseif (in_array('teacher', $roles)) {
                $profileData['profile_type'] = 'teacher';
                $profileData['teacher_data'] = $this->getTeacherData($user);
            } elseif (in_array('admin', $roles)) {
                $profileData['profile_type'] = 'admin';
                // Solo datos básicos para admin
            } else {
                $profileData['profile_type'] = 'unknown';
            }

            return response()->json([
                'success' => true,
                'data' => $profileData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el perfil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene los datos específicos de un estudiante
     */
    private function getStudentData(User $user)
    {
        // Cursos activos (grupos donde el estudiante está inscrito y aún no termina)
        $activeCourses = GroupParticipant::where('user_id', $user->id)
            ->where('role', 'student')
            ->where('enrollment_status', 'active')
            ->with(['group.course'])
            ->whereHas('group', function($query) {
                $query->whereIn('status', ['open', 'in_progress', 'approved']);
            })
            ->get()
            ->map(function($participant) use ($user) {
                $group = $participant->group;
                $course = $group->course;
                
                // Calcular progreso basado en calificaciones
                $totalEvaluations = $group->evaluations()->count();
                $completedEvaluations = $group->evaluations()
                    ->whereHas('gradeRecords', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })->count();
                
                $progress = $totalEvaluations > 0 
                    ? round(($completedEvaluations / $totalEvaluations) * 100) 
                    : 0;

                return [
                    'group_id' => $group->id,
                    'course_id' => $course->id,
                    'course_name' => $course->title,
                    'course_description' => $course->description,
                    'group_code' => $group->code,
                    'group_name' => $group->name,
                    'start_date' => $group->start_date,
                    'end_date' => $group->end_date,
                    'status' => $group->status,
                    'enrolled_at' => $participant->assignment_date,
                    'progress' => $progress,
                    'level' => $course->level,
                    'sessions' => $course->sessions,
                ];
            });

        // Cursos completados (con calificación final aprobada)
        $completedCourses = FinalGrade::where('user_id', $user->id)
            ->where('program_status', 'Passed')
            ->with(['group.course'])
            ->get()
            ->map(function($finalGrade) use ($user) {
                $group = $finalGrade->group;
                $course = $group->course;
                
                // Verificar si tiene certificado
                $credential = Credential::where('user_id', $user->id)
                    ->where('group_id', $group->id)
                    ->first();

                return [
                    'group_id' => $group->id,
                    'course_id' => $course->id,
                    'course_name' => $course->title,
                    'course_description' => $course->description,
                    'group_code' => $group->code,
                    'group_name' => $group->name,
                    'completed_at' => $finalGrade->calculation_date,
                    'final_grade' => $finalGrade->final_grade,
                    'has_certificate' => $credential !== null,
                    'certificate_uuid' => $credential ? $credential->uuid : null,
                    'certificate_url' => $credential 
                        ? route('certificates.download', ['uuid' => $credential->uuid]) 
                        : null,
                    'issue_date' => $credential ? $credential->issue_date : null,
                ];
            });

        return [
            'active_courses' => $activeCourses,
            'completed_courses' => $completedCourses,
            'total_active' => $activeCourses->count(),
            'total_completed' => $completedCourses->count(),
        ];
    }

    /**
     * Obtiene los datos específicos de un docente
     */
    private function getTeacherData(User $user)
    {
        // Grupos a cargo del docente
        $groups = GroupParticipant::where('user_id', $user->id)
            ->where('role', 'teacher')
            ->where('enrollment_status', 'active')
            ->with(['group.course'])
            ->get()
            ->map(function($participant) {
                $group = $participant->group;
                $course = $group->course;
                
                // Contar estudiantes activos en el grupo
                $studentCount = GroupParticipant::where('group_id', $group->id)
                    ->where('role', 'student')
                    ->where('enrollment_status', 'active')
                    ->count();

                return [
                    'group_id' => $group->id,
                    'group_code' => $group->code,
                    'group_name' => $group->name,
                    'course_id' => $course->id,
                    'course_name' => $course->title,
                    'course_description' => $course->description,
                    'start_date' => $group->start_date,
                    'end_date' => $group->end_date,
                    'status' => $group->status,
                    'student_count' => $studentCount,
                    'assigned_at' => $participant->assignment_date,
                    'level' => $course->level,
                    'sessions' => $course->sessions,
                ];
            });

        // Perfil profesional si existe
        $teacherProfile = $user->teacherProfile;
        $professionalInfo = null;
        
        if ($teacherProfile) {
            $professionalInfo = [
                'professional_title' => $teacherProfile->professional_title,
                'specialty' => $teacherProfile->specialty,
                'experience_years' => $teacherProfile->experience_years,
                'biography' => $teacherProfile->biography,
            ];
        }

        return [
            'groups' => $groups,
            'total_groups' => $groups->count(),
            'professional_info' => $professionalInfo,
        ];
    }


    public function downloadCertificate($uuid)
{
    try {
        $credential = Credential::where('uuid', $uuid)->firstOrFail();
        
        
        // Ejemplo simple: retornar JSON con la info
        return response()->json([
            'success' => true,
            'data' => [
                'uuid' => $credential->uuid,
                'user' => $credential->user->full_name,
                'course' => $credential->group->course->title,
                'issue_date' => $credential->issue_date,
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Certificado no encontrado'
        ], 404);
    }
}
}