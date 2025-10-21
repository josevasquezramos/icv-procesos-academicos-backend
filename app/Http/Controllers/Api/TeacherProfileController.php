<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TeacherProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $profiles = TeacherProfile::with(['user', 'createdEvaluations'])->get();
        return response()->json($profiles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'professional_title' => 'required|string|max:255',
            'specialty' => 'nullable|string|max:255',
            'experience_years' => 'required|integer|min:0',
            'biography' => 'nullable|string',
        ]);

        // Evitar duplicados: un usuario solo puede tener un perfil de profesor
        $exists = TeacherProfile::where('user_id', $validated['user_id'])->exists();

        if ($exists) {
            return response()->json([
                'message' => 'This user already has a teacher profile'
            ], 422);
        }

        $profile = TeacherProfile::create($validated);

        return response()->json($profile->load('user'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $profile = TeacherProfile::with(['user', 'createdEvaluations'])->findOrFail($id);
        return response()->json($profile);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $profile = TeacherProfile::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'professional_title' => 'sometimes|required|string|max:255',
            'specialty' => 'nullable|string|max:255',
            'experience_years' => 'sometimes|required|integer|min:0',
            'biography' => 'nullable|string',
        ]);

        $profile->update($validated);

        return response()->json($profile->load('user'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $profile = TeacherProfile::findOrFail($id);
        $profile->delete();

        return response()->json(['message' => 'Teacher profile deleted successfully'], 200);
    }
}