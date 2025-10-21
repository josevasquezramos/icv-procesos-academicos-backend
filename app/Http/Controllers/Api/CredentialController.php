<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CredentialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $credentials = Credential::with(['user', 'group'])->get();
        return response()->json($credentials);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'group_id' => 'required|exists:groups,id',
            'type' => 'required|in:certificate,diploma',
            'issue_date' => 'required|date',
        ]);

        // Generar UUID automáticamente si no se proporciona
        $validated['uuid'] = Str::uuid()->toString();

        $credential = Credential::create($validated);

        return response()->json($credential->load(['user', 'group']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $credential = Credential::with(['user', 'group'])->findOrFail($id);
        return response()->json($credential);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $credential = Credential::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'group_id' => 'sometimes|required|exists:groups,id',
            'type' => 'sometimes|required|in:certificate,diploma',
            'issue_date' => 'sometimes|required|date',
        ]);

        $credential->update($validated);

        return response()->json($credential->load(['user', 'group']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $credential = Credential::findOrFail($id);
        $credential->delete();

        return response()->json(['message' => 'Credential deleted successfully'], 200);
    }
}