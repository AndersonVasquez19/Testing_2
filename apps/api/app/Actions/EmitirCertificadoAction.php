<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\GeneradorPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EmitirCertificadoAction
{
    public function __construct(
        private readonly GeneradorPdfService $pdfService,
        private readonly AuditarAccionAction $auditar,
    ) {}

    public function execute(Enrollment $enrollment, User $actor, ?Request $request = null): Certificate
    {
        return DB::transaction(function () use ($enrollment, $actor, $request): Certificate {
            /** @var Enrollment $locked */
            $locked = Enrollment::query()
                ->with(['student', 'cohort.program', 'activeCertificate'])
                ->lockForUpdate()
                ->findOrFail($enrollment->id);

            if (! in_array($locked->enrollment_status, [Enrollment::STATUS_ENROLLED, Enrollment::STATUS_COMPLETED], true)) {
                throw ValidationException::withMessages([
                    'enrollment_status' => ['La matrícula debe estar pagada/activa para emitir certificado.'],
                ]);
            }

            if (! $locked->academic_approved) {
                throw ValidationException::withMessages([
                    'academic_approved' => ['El estudiante debe contar con aprobación académica del coordinador para emitir el certificado.'],
                ]);
            }

            if ($locked->activeCertificate !== null) {
                throw ValidationException::withMessages([
                    'enrollment_id' => ['Esta matrícula ya cuenta con un certificado vigente emitido.'],
                ]);
            }

            $student = $locked->student;
            $cohort = $locked->cohort;
            $program = $cohort?->program;

            if ($student === null || $cohort === null || $program === null) {
                throw ValidationException::withMessages([
                    'enrollment_id' => ['La matrícula no tiene datos completos de estudiante o programa.'],
                ]);
            }

            $year = now()->format('Y');
            $prefix = "CERT-{$year}-";

            /** @var Certificate|null $latest */
            $latest = Certificate::query()
                ->where('certificate_code', 'like', "{$prefix}%")
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $nextNumber = 1;
            if ($latest !== null) {
                $parts = explode('-', $latest->certificate_code);
                $nextNumber = ((int) end($parts)) + 1;
            }

            $certificateCode = sprintf('%s%06d', $prefix, $nextNumber);
            $verificationUuid = (string) Str::uuid();
            $rawWebUrl = config('horus.web_url', 'http://localhost:3000');
            $webUrl = rtrim(is_string($rawWebUrl) ? $rawWebUrl : 'http://localhost:3000', '/');
            $qrPayload = "{$webUrl}/verificar-certificado/{$verificationUuid}";

            $snapshot = [
                'student_name' => $student->name,
                'student_dni' => $student->dni,
                'program_name' => $program->name,
                'program_type' => $program->type,
                'cohort_code' => $cohort->code,
                'cohort_name' => $cohort->name,
                'duration_hours' => $program->duration_hours,
                'start_date' => $cohort->start_date->format('d/m/Y'),
                'end_date' => $cohort->end_date->format('d/m/Y'),
                'final_grade' => $locked->final_grade,
                'attendance_percentage' => $locked->attendance_percentage,
            ];

            $certificate = Certificate::query()->create([
                'enrollment_id' => $locked->id,
                'verification_uuid' => $verificationUuid,
                'certificate_code' => $certificateCode,
                'qr_payload' => $qrPayload,
                'snapshot_data' => $snapshot,
                'issued_by' => $actor->id,
                'issued_at' => now(),
            ]);

            $pdfPath = $this->pdfService->generarCertificado($certificate);
            $certificate->update(['pdf_path' => $pdfPath]);

            $locked->update(['enrollment_status' => Enrollment::STATUS_COMPLETED]);

            $this->auditar->execute(
                $actor,
                'certificate_issued',
                $certificate,
                [
                    'enrollment_id' => $locked->id,
                    'certificate_code' => $certificateCode,
                    'verification_uuid' => $verificationUuid,
                ],
                $request,
            );

            return $certificate->fresh(['enrollment.student', 'enrollment.cohort.program', 'issuer']) ?? $certificate;
        });
    }
}
