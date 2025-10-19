<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessGroupCompletion;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CompleteGroupController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Group $group)
    {
        Gate::authorize('complete', $group);

        // Validación de estado
        if ($group->status === 'completed') {
            return response()->json([
                'message' => 'Este grupo ya ha sido completado anteriormente.'
            ], 409); // 409 Conflict
        }

        // Despachar el Job a la cola
        ProcessGroupCompletion::dispatch($group);

        // Responder inmediatamente (HTTP 202)
        return response()->json([
            'message' => 'El proceso de completado del grupo ha comenzado.'
        ], 202);
    }
}
