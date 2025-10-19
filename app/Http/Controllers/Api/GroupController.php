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

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
