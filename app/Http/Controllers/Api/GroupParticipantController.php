<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GroupParticipant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GroupParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $participants = GroupParticipant::with(['group', 'user', 'attendances'])->get();
        return response()->json($participants);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:student,teacher',
            'enrollment_status' => 'required|string|max:50',
            'assignment_date' => 'required|date',
        ]);

        // Evitar duplicados: un usuario no puede estar dos veces en el mismo grupo con el mismo rol
        $exists = GroupParticipant::where('group_id', $validated['group_id'])
            ->where('user_id', $validated['user_id'])
            ->where('role', $validated['role'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'This user is already enrolled in this group with this role'
            ], 422);
        }

        $participant = GroupParticipant::create($validated);

        return response()->json($participant->load(['group', 'user']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $participant = GroupParticipant::with(['group', 'user', 'attendances'])->findOrFail($id);
        return response()->json($participant);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $participant = GroupParticipant::findOrFail($id);

        $validated = $request->validate([
            'group_id' => 'sometimes|required|exists:groups,id',
            'user_id' => 'sometimes|required|exists:users,id',
            'role' => 'sometimes|required|in:student,teacher',
            'enrollment_status' => 'sometimes|required|string|max:50',
            'assignment_date' => 'sometimes|required|date',
        ]);

        $participant->update($validated);

        return response()->json($participant->load(['group', 'user']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $participant = GroupParticipant::findOrFail($id);
        $participant->delete();

        return response()->json(['message' => 'Group participant deleted successfully'], 200);
    }
}