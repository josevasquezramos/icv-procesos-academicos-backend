<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // <-- AÑADIDO
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialiteController extends Controller
{
    /**
     * Redirige al usuario a la página de autenticación de Google.
     */
    public function redirectToGoogle()
    {
        try {
            $redirectUrl = Socialite::driver('google')
                ->stateless()
                ->redirect()
                ->getTargetUrl();
            
            return response()->json([
                'redirect_url' => $redirectUrl,
            ]);

        } catch (Exception $e) {
            Log::error('Error al redirigir a Google: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error al intentar redirigir a Google.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene la información del usuario de Google después de la autenticación.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'profile_photo' => $user->profile_photo ?? $googleUser->getAvatar(),
                    'last_access' => now(),
                ]);
            } else {
                $nameParts = explode(' ', $googleUser->getName());
                $firstName = $nameParts[0];
                $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';

                $user = User::create([
                    'full_name' => $googleUser->getName(),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $googleUser->getEmail(),
                    'profile_photo' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(24)), 
                    'status' => 'active',
                ]);
            }

            $token = $user->createToken('auth_token_google')->plainTextToken;

            return response()->json([
                'message' => 'Autenticación con Google exitosa',
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'email' => $user->email,
                    'profile_photo' => $user->profile_photo,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ], 200);

        } catch (Exception $e) {
            // Registramos el error completo en los logs de Laravel
            Log::error('Error en el callback de Google:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(), // Esto nos dará toda la información
            ]);

            // Devolvemos la misma respuesta genérica al usuario
            return response()->json([
                'message' => 'Error durante la autenticación con Google.',
                'error' => 'Se ha producido un error. Consulte los logs para más detalles.'
            ], 500);
        }
    }
}
