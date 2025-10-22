<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /**
     * Maneja la solicitud de registro de un nuevo usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // 1. Validar los datos de entrada
        try {
            $request->validate([
                'dni' => 'required|string|digits:8|unique:users,dni',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        }

        // 2. ---- INICIO: LÓGICA DE API DNI ----
        $token = env('APIPERU_TOKEN');
        if (!$token) {
            // Error si no has configurado el token en tu .env
            return response()->json(['message' => 'El servicio de validación de DNI no está configurado.'], 500);
        }

        $response = Http::withToken($token)->get('https://apiperu.dev/api/dni/' . $request->dni);

        // 2a. Manejar error si la API de ApiPeru falla
        if (!$response->successful()) {
            return response()->json([
                'message' => 'Error al consultar el servicio de DNI. Intente más tarde.',
                'errors' => ['dni' => ['No se pudo validar el DNI en este momento.']]
            ], 503); // 503 Servicio No Disponible
        }

        $data = $response->json();

        // 2b. Manejar si el DNI no es encontrado por la API
        if (!isset($data['success']) || $data['success'] === false) {
             return response()->json([
                'message' => 'DNI no encontrado.',
                'errors' => ['dni' => ['El número de DNI no existe o no pudo ser verificado.']]
            ], 422); // 422 Error de validación
        }

        // 3. Preparar datos del usuario desde la API
        $apiData = $data['data'];
        $nombres = $apiData['nombres'];
        $apellidoPaterno = $apiData['apellido_paterno'];
        $apellidoMaterno = $apiData['apellido_materno'];
        
        $fullName = $nombres . ' ' . $apellidoPaterno . ' ' . $apellidoMaterno;
        $lastName = $apellidoPaterno . ' ' . $apellidoMaterno;

        // 4. Crear el nuevo usuario
        $user = User::create([
            'first_name' => $nombres,
            'last_name' => $lastName,
            'full_name' => $fullName,
            'dni' => $request->dni,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 5. Generar el token para el nuevo usuario
        $token = $user->createToken('auth_token')->plainTextToken;

        // 6. Devolver la respuesta
        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'full_name' => $user->full_name,
                'email' => $user->email,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Maneja la solicitud de login.
     * (Este método no necesita cambios)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // 1. Validar los datos de entrada
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:8|max:20',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        }

        // 2. Intentar autenticar al usuario
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales inválidas.',
            ], 401);
        }

        // 3. Generar el token (Sanctum)
        // 'auth_token' es el nombre que le damos al token
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Devolver la respuesta al frontend
        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'email' => $user->email,
                'role' => is_array($user->role) ? $user->role[0] : $user->role, // Obtener el primer elemento si es array
            ],
            // El frontend lo espera como "token"
            'token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * Maneja la solicitud de logout.
     * (Este método no necesita cambios)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Elimina el token actual que se usó para autenticar la solicitud.
        // Esto requiere el middleware 'auth:sanctum'.
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente.',
        ], 200);
    }
}