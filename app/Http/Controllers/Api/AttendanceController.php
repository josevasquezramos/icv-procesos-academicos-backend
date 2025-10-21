<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
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

        $attendance = Attendance::create($validated);

        return response()->json($attendance->load(['groupParticipant', 'class']), 201);
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
}