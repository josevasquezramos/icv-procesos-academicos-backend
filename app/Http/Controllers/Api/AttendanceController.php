<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $attendances = Attendance::with(['groupParticipant', 'class'])->get();
        return response()->json($attendances);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'group_participant_id' => 'required|exists:group_participants,id',
            'class_id' => 'required|exists:classes,id',
            'attended' => 'required|boolean',
            'observations' => 'nullable|string',
        ]);

        // Create or Update en una sola línea
        $attendance = Attendance::updateOrCreate(
            [
                'group_participant_id' => $validated['group_participant_id'],
                'class_id' => $validated['class_id'],
            ],
            [
                'attended' => $validated['attended'],
                'observations' => $validated['observations'] ?? null,
            ]
        );

        return response()->json([
            'message' => $attendance->wasRecentlyCreated
                ? 'Attendance created successfully'
                : 'Attendance updated successfully',
            'attendance' => $attendance->load(['groupParticipant', 'class'])
        ], $attendance->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $attendance = Attendance::with(['groupParticipant', 'class'])->findOrFail($id);
        return response()->json($attendance);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $attendance = Attendance::findOrFail($id);

        $validated = $request->validate([
            'group_participant_id' => 'sometimes|required|exists:group_participants,id',
            'class_id' => 'sometimes|required|exists:classes,id',
            'attended' => 'sometimes|required|boolean',
            'observations' => 'nullable|string',
        ]);

        $attendance->update($validated);

        return response()->json($attendance->load(['groupParticipant', 'class']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return response()->json(['message' => 'Attendance deleted successfully'], 200);
    }

    public function getStudentAttendances($userId, $groupId)
    {
        $attendances = Attendance::with('class')
            ->whereHas('groupParticipant', function($q) use ($userId, $groupId) {
                $q->where('user_id', $userId)
                ->where('group_id', $groupId);
            })
            ->get();

        return response()->json($attendances);
    }

    public function getAttendancesByClass($classId)
    {
        $attendances = Attendance::with('groupParticipant.user')
            ->where('class_id', $classId)
            ->get();

        return response()->json($attendances);
    }

    public function getByClass(string $classId): JsonResponse
    {
        Classes::findOrFail($classId);

        $attendances = Attendance::with([
                'groupParticipant.user:id,first_name,last_name,full_name,email',
                'groupParticipant.group:id,name',
            ])
            ->where('class_id', $classId)
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'class_id' => (int) $classId,
            'count' => $attendances->count(),
            'attendances' => $attendances,
        ], 200);
    }

}
