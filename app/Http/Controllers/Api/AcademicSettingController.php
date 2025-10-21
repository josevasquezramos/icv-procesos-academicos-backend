<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AcademicSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $settings = AcademicSetting::all();
        return response()->json($settings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'base_grade' => 'required|numeric|min:0|max:20',
            'min_passing_grade' => 'required|numeric|min:0|max:20',
        ]);

        $setting = AcademicSetting::create($validated);

        return response()->json($setting, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $setting = AcademicSetting::findOrFail($id);
        return response()->json($setting);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $setting = AcademicSetting::findOrFail($id);

        $validated = $request->validate([
            'base_grade' => 'sometimes|required|numeric|min:0|max:20',
            'min_passing_grade' => 'sometimes|required|numeric|min:0|max:20',
        ]);

        $setting->update($validated);

        return response()->json($setting);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $setting = AcademicSetting::findOrFail($id);
        $setting->delete();

        return response()->json(['message' => 'Academic setting deleted successfully'], 200);
    }
}