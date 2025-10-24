<?php
// app/Http/Controllers/AdminTeacherController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class AdminTeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = User::teachers()->with('instructor');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $teachers = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $teachers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'dni' => 'required|string|unique:users,dni|max:20',
            'password' => 'required|string|min:8',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'country' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,banned',
            'bio' => 'nullable|string',
            'expertise_area' => 'nullable|string|max:255',
        ]);

        DB::transaction(function() use ($validated) {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'full_name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'dni' => $validated['dni'],
                'password' => Hash::make($validated['password']),
                'phone_number' => $validated['phone_number'] ?? null,
                'address' => $validated['address'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'country' => $validated['country'] ?? null,
                'role' => ['teacher'],
                'status' => $validated['status'],
            ]);

            Instructor::create([
                'user_id' => $user->id,
                'bio' => $validated['bio'] ?? null,
                'expertise_area' => $validated['expertise_area'] ?? null,
                'status' => $validated['status'],
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Docente creado exitosamente'
        ], 201);
    }

    public function show($id)
    {
        $teacher = User::teachers()->with('instructor')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $teacher
        ]);
    }

    public function update(Request $request, $id)
    {
        $teacher = User::teachers()->findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($teacher->id)
            ],
            'dni' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->ignore($teacher->id)
            ],
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'country' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,banned',
            'bio' => 'nullable|string',
            'expertise_area' => 'nullable|string|max:255',
        ]);

        DB::transaction(function() use ($teacher, $validated) {
            $teacher->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'full_name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'dni' => $validated['dni'],
                'phone_number' => $validated['phone_number'] ?? null,
                'address' => $validated['address'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'country' => $validated['country'] ?? null,
                'status' => $validated['status'],
            ]);

            if ($teacher->instructor) {
                $teacher->instructor->update([
                    'bio' => $validated['bio'] ?? null,
                    'expertise_area' => $validated['expertise_area'] ?? null,
                    'status' => $validated['status'],
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Docente actualizado exitosamente'
        ]);
    }

    public function destroy($id)
    {
        $teacher = User::teachers()->findOrFail($id);
        
        DB::transaction(function() use ($teacher) {
            if ($teacher->instructor) {
                $teacher->instructor->delete();
            }
            $teacher->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Docente eliminado exitosamente'
        ]);
    }
}