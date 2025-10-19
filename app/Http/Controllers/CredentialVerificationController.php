<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use Illuminate\Http\Request;

class CredentialVerificationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $uuid)
    {
        // 1. Intentar encontrar la credencial por su UUID
        // Cargamos las relaciones que la vista necesitará
        $credential = Credential::where('uuid', $uuid)
            ->with('user', 'group.course') // Carga eficiente de datos
            ->first();

        // 2. Devolver la vista
        // Si $credential es null, la vista mostrará el mensaje de error
        return view('verification.result', [
            'credential' => $credential
        ]);
    }
}
