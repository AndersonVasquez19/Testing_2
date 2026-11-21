<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Publico;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\JsonResponse;

class CertificateVerificationController extends Controller
{
    public function show(string $uuid): JsonResponse
    {
        $certificate = Certificate::query()
            ->where('verification_uuid', $uuid)
            ->orWhere('certificate_code', $uuid)
            ->first();

        if ($certificate === null) {
            return response()->json([
                'valid' => false,
                'status' => 'not_found',
                'message' => 'No se encontró ningún certificado con el código indicado.',
            ], 404);
        }

        $snapshot = $certificate->snapshot_data;
        $revoked = $certificate->isRevoked();

        return response()->json([
            'valid' => ! $revoked,
            'status' => $revoked ? 'revoked' : 'valid',
            'certificate_code' => $certificate->certificate_code,
            'verification_uuid' => $certificate->verification_uuid,
            'issued_at' => $certificate->issued_at->toDateString(),
            'revoked_at' => $certificate->revoked_at?->toDateString(),
            'revocation_reason' => $certificate->revocation_reason,
            'student_name' => $snapshot['student_name'] ?? 'Estudiante',
            'program_name' => $snapshot['program_name'] ?? '',
            'cohort_code' => $snapshot['cohort_code'] ?? '',
            'duration_hours' => $snapshot['duration_hours'] ?? null,
        ]);
    }
}
