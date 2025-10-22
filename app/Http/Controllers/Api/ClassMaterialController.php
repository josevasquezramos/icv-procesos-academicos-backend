<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\ClassMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $materials = ClassMaterial::with('class')->get();
        
        return response()->json([
            'success' => true,
            'data' => $materials
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'material_url' => 'required|string',
            'type' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $material = ClassMaterial::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Material creado exitosamente',
            'data' => $material
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $material = ClassMaterial::with('class')->find($id);

        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Material no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $material
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $material = ClassMaterial::find($id);

        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Material no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'class_id' => 'sometimes|required|exists:classes,id',
            'material_url' => 'sometimes|required|string',
            'type' => 'sometimes|required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $material->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Material actualizado exitosamente',
            'data' => $material
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $material = ClassMaterial::find($id);

        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Material no encontrado'
            ], 404);
        }

        $material->delete();

        return response()->json([
            'success' => true,
            'message' => 'Material eliminado exitosamente'
        ], 200);
    }

    /**
     * Get all materials for a specific class.
     */
    public function getByClass(string $classId)
    {
        $materials = ClassMaterial::where('class_id', $classId)->get();

        return response()->json([
            'success' => true,
            'data' => $materials
        ], 200);
    }
}