<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $users = User::with(['teacherProfile', 'groupParticipants', 'credentials'])->get();
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'dni' => 'nullable|string|max:20|unique:users,dni',
            'document' => 'nullable|string|max:50',
            'email' => 'required|email|max:255|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'role' => 'nullable|array',
            'password' => 'required|string|min:8',
            'gender' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'country_location' => 'nullable|string|max:100',
            'timezone' => 'nullable|string|max:50',
            'profile_photo' => 'nullable|string|max:500',
            'status' => 'nullable|string|max:50',
        ]);

        // Hash password
        $validated['password'] = Hash::make($validated['password']);

        // Auto-generar full_name si no se proporciona
        if (empty($validated['full_name'])) {
            $validated['full_name'] = trim($validated['first_name'] . ' ' . $validated['last_name']);
        }

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $user = User::with([
            'teacherProfile',
            'groupParticipants.group',
            'credentials',
            'finalGrades',
            'gradeRecords'
        ])->findOrFail($id);

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'dni' => 'nullable|string|max:20|unique:users,dni,' . $id,
            'document' => 'nullable|string|max:50',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'role' => 'nullable|array',
            'password' => 'nullable|string|min:8',
            'gender' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'country_location' => 'nullable|string|max:100',
            'timezone' => 'nullable|string|max:50',
            'profile_photo' => 'nullable|string|max:500',
            'status' => 'nullable|string|max:50',
        ]);

        // Hash password si se está actualizando
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Auto-generar full_name si se actualizan los nombres
        if (isset($validated['first_name']) || isset($validated['last_name'])) {
            $firstName = $validated['first_name'] ?? $user->first_name;
            $lastName = $validated['last_name'] ?? $user->last_name;
            $validated['full_name'] = trim($firstName . ' ' . $lastName);
        }

        $user->update($validated);

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
