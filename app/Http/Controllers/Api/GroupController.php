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
        //
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
