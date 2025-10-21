<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Models\Course;
use App\Models\CoursePreviousRequirement;
use App\Models\ProgramCourse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    public function index()
    {
        try {
            $courses = Course::all();
            return response()->json($courses);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(StoreCourseRequest $request)
    {
        try {
            $validated = $request->validated();

            $course = Course::create($validated);

            if ($request->has('prerequisites') && !empty($request->prerequisites)) {
                $previousCourseIds = explode(',', $request->prerequisites);

                foreach ($previousCourseIds as $previousCourseId) {
                    $previousCourseId = trim($previousCourseId);

                    if (is_numeric($previousCourseId)) {
                        CoursePreviousRequirement::create([
                            'course_id' => $course->id,
                            'previous_course_id' => (int)$previousCourseId,
                        ]);
                    }
                }
            }

            return response()->json([
                'message' => 'Course created successfully!',
                'course' => $course,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating course.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getGroupsByCourse($id)
    {
        try {
            $course = Course::with(['groups', 'groups.participants.user'])
                ->find($id);

            if (!$course) {
                return response()->json([
                    'message' => 'Curso no encontrado.'
                ], 404);
            }

            $groupsWithTeachersAndStudents = [];

            foreach ($course->groups as $group) {
                $teacher = $group->participants()->teachers()->first();

                $students = $group->participants()->students()->get();

                $groupsWithTeachersAndStudents[] = [
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'group_code' => $group->code,
                    'status' => $group->status,
                    'start_date' => $group->start_date,
                    'end_date' => $group->end_date,
                    'teacher' => $teacher ? $teacher->user : null,
                    'students' => $students->map(function ($student) {
                        return $student->user;
                    }),
                ];
            }

            return response()->json([
                'course_id' => $course->id,
                'course_title' => $course->title,
                'groups' => $groupsWithTeachersAndStudents,
            ], 200);

        } catch (Exception $e) {
            Log::error('Error fetching groups for course: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error al obtener los grupos del curso.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store_course(Request $request)
    {
        try {
            $validated = $request->validate([
                'course_id' => 'nullable|string|max:255',
                'title' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'level' => 'nullable|string|max:100',
                'course_image' => 'nullable|string|max:255',
                'video_url' => 'nullable|string|max:255',
                'duration' => 'nullable|numeric|min:0',
                'sessions' => 'nullable|integer|min:0',
                'selling_price' => 'nullable|numeric|min:0',
                'discount_price' => 'nullable|numeric|min:0',
                'prerequisites' => 'nullable|string',
                'certificate_name' => 'nullable|boolean',
                'certificate_issuer' => 'nullable|string|max:255',
                'bestseller' => 'nullable|boolean',
                'featured' => 'nullable|boolean',
                'highest_rated' => 'nullable|boolean',
                'status' => 'nullable|boolean',
            ]);

            $course = Course::create($validated);

            return response()->json($course, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function show(string $id)
    {
        try {
            $course = Course::with(['programs', 'groups', 'previousRequirements', 'requiredFor'])
                ->findOrFail($id);
            
            return response()->json($course);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $course = Course::findOrFail($id);

            $validated = $request->validate([
                'course_id' => 'sometimes|string|max:255',
                'title' => 'sometimes|required|string|max:255',
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'level' => 'nullable|string|max:100',
                'course_image' => 'nullable|string|max:255',
                'video_url' => 'nullable|string|max:255',
                'duration' => 'nullable|numeric|min:0',
                'sessions' => 'nullable|integer|min:0',
                'selling_price' => 'nullable|numeric|min:0',
                'discount_price' => 'nullable|numeric|min:0',
                'prerequisites' => 'nullable|string',
                'certificate_name' => 'nullable|boolean',
                'certificate_issuer' => 'nullable|string|max:255',
                'bestseller' => 'nullable|boolean',
                'featured' => 'nullable|boolean',
                'highest_rated' => 'nullable|boolean',
                'status' => 'nullable|boolean',
            ]);

            $course->update($validated);

            return response()->json($course);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $course = Course::findOrFail($id);
            $course->delete();

            return response()->json(['message' => 'Course deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
