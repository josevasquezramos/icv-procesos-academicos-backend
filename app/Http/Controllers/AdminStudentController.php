<?php
// app/Http/Controllers/AdminStudentController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // ← AGREGAR ESTA LÍNEA

class AdminStudentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::students()->with('student');

        // Búsqueda
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $students
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
        ]);

        DB::transaction(function() use ($validated) {
            // Crear usuario estudiante
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
                'role' => ['student'],
                'status' => $validated['status'],
            ]);

            // Crear registro en students
            Student::create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'document_number' => $validated['dni'],
                'status' => $validated['status'],
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Estudiante creado exitosamente'
        ], 201);
    }

    public function show($id)
    {
        $student = User::students()->with('student')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    public function update(Request $request, $id)
    {
        $student = User::students()->findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($student->id)
            ],
            'dni' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->ignore($student->id)
            ],
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'country' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,banned',
        ]);

        DB::transaction(function() use ($student, $validated) {
            // Actualizar usuario
            $student->update([
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

            // Actualizar estudiante
            if ($student->student) {
                $student->student->update([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'document_number' => $validated['dni'],
                    'status' => $validated['status'],
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Estudiante actualizado exitosamente'
        ]);
    }

    public function destroy($id)
    {
        $student = User::students()->findOrFail($id);
        
        DB::transaction(function() use ($student) {
            if ($student->student) {
                $student->student->delete();
            }
            $student->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Estudiante eliminado exitosamente'
        ]);
    }
}