<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use App\Services\CertificatePdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CredentialPdfController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __construct(
        protected CertificatePdfService $certificateService
    ) {
    }

    public function __invoke(Request $request, Credential $credential)
    {
        // 1. Autorización
        // Gate::authorize('view', $credential);

        // 2. Delegar Generación
        $pdf = $this->certificateService->generate($credential);

        // 3. Responder
        return $pdf->stream('certificado-' . $credential->uuid . '.pdf');
    }
}
