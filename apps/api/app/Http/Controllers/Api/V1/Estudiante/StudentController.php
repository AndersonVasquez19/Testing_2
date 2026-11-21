<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Estudiante;

use App\Actions\RegistrarMatriculaAction;
use App\Actions\ReservarAsesoriaAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdvisorySlotResource;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\PaymentResource;
use App\Models\AdvisorySlot;
use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentController extends Controller
{
    public function enrollments(Request $request): AnonymousResourceCollection
    {
        /** @var User $student */
        $student = $request->user();

        $enrollments = Enrollment::query()
            ->where('user_id', $student->id)
            ->with(['cohort.program', 'cohort.sessions', 'payments', 'activeCertificate'])
            ->latest()
            ->get();

        return EnrollmentResource::collection($enrollments);
    }

    public function enroll(Request $request, RegistrarMatriculaAction $action): JsonResponse
    {
        $validated = $request->validate([
            'cohort_id' => ['required', 'integer', 'exists:cohorts,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        /** @var User $student */
        $student = $request->user();
        $enrollment = $action->execute($student, (int) $validated['cohort_id'], $student, $validated['notes'] ?? null, $request);

        return response()->json([
            'message' => 'Reserva de matrícula registrada. Realiza el pago para activar tu acceso.',
            'data' => new EnrollmentResource($enrollment->load(['cohort.program'])),
        ], 201);
    }

    public function submitPayment(Request $request, Enrollment $enrollment): JsonResponse
    {
        /** @var User $student */
        $student = $request->user();
        if ($enrollment->user_id !== $student->id && ! $student->hasPortalRole(User::ROLE_ADMIN)) {
            abort(403, 'No autorizado.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
            'payment_method' => ['required', 'string', 'in:yape,plin,transfer,gateway'],
            'operation_code' => ['required', 'string', 'max:80'],
            'voucher' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:3072'],
        ]);

        $voucherPath = null;
        $voucherHash = null;
        if ($request->hasFile('voucher')) {
            $file = $request->file('voucher');
            if ($file !== null) {
                $voucherPath = $file->store('vouchers', 'local');
                $realPath = $file->getRealPath();
                $voucherHash = $realPath !== false ? hash_file('sha256', $realPath) ?: null : null;
            }
        }

        $payment = Payment::query()->create([
            'enrollment_id' => $enrollment->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'operation_code' => $validated['operation_code'],
            'voucher_path' => $voucherPath,
            'voucher_hash' => $voucherHash,
            'status' => Payment::STATUS_PENDING,
        ]);

        return response()->json([
            'message' => 'Comprobante enviado correctamente.',
            'data' => new PaymentResource($payment),
        ], 201);
    }

    public function advisories(Request $request): AnonymousResourceCollection
    {
        /** @var User $student */
        $student = $request->user();

        $slots = AdvisorySlot::query()
            ->with(['program', 'advisor'])
            ->where(function ($q) use ($student): void {
                $q->where('is_booked', false)
                    ->orWhere('student_id', $student->id);
            })
            ->orderBy('slot_date')
            ->get();

        return AdvisorySlotResource::collection($slots);
    }

    public function bookAdvisory(Request $request, AdvisorySlot $slot, ReservarAsesoriaAction $action): JsonResponse
    {
        $validated = $request->validate([
            'topic' => ['required', 'string', 'min:5', 'max:255'],
        ]);

        /** @var User $student */
        $student = $request->user();
        $booked = $action->execute($slot, $student, $validated['topic'], $request);

        return response()->json([
            'message' => 'Asesoría reservada correctamente.',
            'data' => new AdvisorySlotResource($booked),
        ]);
    }

    public function downloadCertificate(Request $request, Certificate $certificate): StreamedResponse|JsonResponse
    {
        /** @var User $student */
        $student = $request->user();
        $enrollment = $certificate->enrollment;
        if (($enrollment === null || $enrollment->user_id !== $student->id) && ! $student->hasPortalRole(User::ROLE_ADMIN, User::ROLE_COORDINATOR)) {
            abort(403, 'No autorizado.');
        }

        if ($certificate->pdf_path === null || ! Storage::disk('local')->exists($certificate->pdf_path)) {
            return response()->json(['message' => 'PDF del certificado no disponible.'], 404);
        }

        return Storage::disk('local')->download(
            $certificate->pdf_path,
            "{$certificate->certificate_code}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }
}
