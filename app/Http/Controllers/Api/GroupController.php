<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGroupRequest;
use App\Models\Group;
use App\Models\GroupParticipant;
use Exception;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        try {
            $courses = Group::all();
            return response()->json($courses);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(StoreGroupRequest $request)
    {
        try {
            $validated = $request->validated();

            $group = Group::create($validated);

            if ($request->has('teacher_id')) {
                GroupParticipant::create([
                    'group_id' => $group->id,
                    'user_id' => $request->teacher_id,
                    'role' => 'teacher',
                    'enrollment_status' => 'active',
                    'assignment_date' => now(),
                ]);
            }

            return response()->json([
                'message' => 'Group created successfully!',
                'group' => $group,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el grupo.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function show(string $id)
    {
        try {
            // Buscar el grupo junto con los participantes (profesor y estudiantes), las clases y los cursos previos
            $group = Group::with(['participants.user', 'classes', 'course.coursePreviousRequirements.previousCourse'])
                ->find($id);

            if (!$group) {
                return response()->json([
                    'message' => 'Grupo no encontrado.',
                ], 404);
            }

            // Obtener el profesor (teacher) del grupo, que es único
            $teacher = $group->participants()->teachers()->first();

            // Obtener los estudiantes (students) del grupo
            $students = $group->participants()->students()->get();

            // Obtener los cursos previos
            $previousCourses = $group->course->coursePreviousRequirements->map(function ($requirement) {
                return [
                    'previous_course_id' => $requirement->previousCourse->id,
                    'previous_course_title' => $requirement->previousCourse->title,
                ];
            });

            // Estructura de datos con el grupo y sus relaciones
            $groupData = [
                'id' => $group->id,
                'name' => $group->name,
                'description' => $group->description,
                'status' => $group->status,
                'start_date' => $group->start_date,
                'end_date' => $group->end_date,
                'participants' => [
                    'teacher' => $teacher ? $teacher->user : null, // Profesor, si existe
                    'students' => $students->map(function ($student) {
                        return [
                            'id' => $student->user->id,
                            'name' => $student->user->name,
                            'role' => 'Estudiante', // Role es siempre "Estudiante" para los estudiantes
                            'expertise_area' => $student->user->expertise_area ?? null, // Solo si el campo existe
                        ];
                    }),
                ],
                'classes' => $group->classes->map(function ($class) {
                    return [
                        'id' => $class->id,
                        'class_name' => $class->class_name,
                        'class_date' => $class->class_date,
                        'start_time' => $class->start_time->format('H:i'), // Formateamos el tiempo
                        'end_time' => $class->end_time->format('H:i'), // Formateamos el tiempo
                        'description' => $class->description,
                        'class_status' => $class->class_status,
                    ];
                }),
                'previous_courses' => $previousCourses, // Cursos previos asociados al curso del grupo
            ];

            // Devolver la respuesta con los datos del grupo
            return response()->json([
                'group' => $groupData,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al obtener la información del grupo.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function joinGroup(Request $request, string $id)
{
    try {
        // Validar que el grupo existe
        $group = Group::find($id);

        if (!$group) {
            return response()->json([
                'message' => 'Grupo no encontrado.',
            ], 404);
        }

        // Obtener el usuario autenticado
        $user = $request->user();

        // Verificar si el usuario ya está en el grupo
        $alreadyEnrolled = GroupParticipant::where('group_id', $id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyEnrolled) {
            return response()->json([
                'message' => 'Ya estás inscrito en este grupo.',
            ], 400);
        }

        // Inscribir al estudiante en el grupo
        GroupParticipant::create([
            'group_id' => $id,
            'user_id' => $user->id,
            'role' => 'student',
            'enrollment_status' => 'active',
            'assignment_date' => now(),
        ]);

        return response()->json([
            'message' => '¡Te has unido al grupo exitosamente!',
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'message' => 'Error al unirse al grupo.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    public function update(Request $request, string $id)
    {
        try {
            $group = Group::findOrFail($id);

            $validated = $request->validate([
                'course_id' => 'sometimes|required|exists:courses,id',
                'code' => 'sometimes|required|string|max:50',
                'name' => 'sometimes|required|string|max:255',
                'start_date' => 'sometimes|required|date',
                'end_date' => 'sometimes|required|date|after_or_equal:start_date',
                'status' => 'sometimes|required|string|max:50',
            ]);

            $group->update($validated);

            return response()->json([
                'message' => 'Group updated successfully',
                'group' => $group,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el grupo.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

   public function destroy(string $id)
    {
        try {
            $group = Group::findOrFail($id);
            $group->delete();

            return response()->json([
                'message' => 'Group deleted successfully'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el grupo.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
