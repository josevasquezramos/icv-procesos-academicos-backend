<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmploymentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmploymentProfileController extends Controller
{
    /**
     * Obtener el perfil laboral del usuario autenticado
     */
    public function show(Request $request)
    {
        $profile = EmploymentProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json([
                'message' => 'No se encontró perfil laboral',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Perfil laboral obtenido exitosamente',
            'data' => $profile
        ]);
    }

    /**
     * Crear o actualizar el perfil laboral del usuario autenticado
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employment_status' => 'required|in:empleado,independiente,emprendedor,buscando,estudiando,otro',
            'company_name' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'salary_range' => 'nullable|in:menos-1000,1000-2000,2000-3000,3000-5000,5000-8000,mas-8000',
            'industry' => 'nullable|in:tecnologia,educacion,salud,finanzas,retail,manufactura,construccion,servicios,gobierno,otro',
            'is_related_to_studies' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $profile = EmploymentProfile::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validator->validated()
        );

        return response()->json([
            'message' => 'Perfil laboral actualizado exitosamente',
            'data' => $profile
        ], 200);
    }

    /**
     * Eliminar el perfil laboral del usuario autenticado
     */
    public function destroy(Request $request)
    {
        $profile = EmploymentProfile::where('user_id', $request->user()->id)->first();

        if (!$profile) {
            return response()->json([
                'message' => 'No se encontró perfil laboral'
            ], 404);
        }

        $profile->delete();

        return response()->json([
            'message' => 'Perfil laboral eliminado exitosamente'
        ], 200);
    }
}