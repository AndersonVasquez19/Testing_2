<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Publico;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function show(string $token): JsonResponse
    {
        $enrollment = Enrollment::query()
            ->where('reservation_token', $token)
            ->with(['student', 'cohort.program', 'payments'])
            ->first();

        if ($enrollment === null) {
            return response()->json(['message' => 'Enlace de pago no válido.'], 404);
        }

        $cohort = $enrollment->cohort;
        $program = $cohort?->program;
        $student = $enrollment->student;

        $total = $program !== null ? (float) $program->price : 0.0;
        $rawValidated = $enrollment->payments
            ->where('status', Payment::STATUS_VALIDATED)
            ->sum('amount');
        $validatedSum = is_numeric($rawValidated) ? (float) $rawValidated : 0.0;
        $rawPending = $enrollment->payments
            ->where('status', Payment::STATUS_PENDING)
            ->sum('amount');
        $pendingSum = is_numeric($rawPending) ? (float) $rawPending : 0.0;
        $balance = max(0.0, $total - $validatedSum);

        $state = 'activa';
        $active = true;
        if ($enrollment->enrollment_status === Enrollment::STATUS_CANCELLED) {
            $state = $enrollment->reservation_expires_at !== null && $enrollment->reservation_expires_at->isPast()
                ? 'vencida'
                : 'cerrada';
            $active = false;
        } elseif ($enrollment->enrollment_status === Enrollment::STATUS_ENROLLED || $enrollment->enrollment_status === Enrollment::STATUS_COMPLETED) {
            $state = 'cerrada';
            $active = false;
        } elseif ($pendingSum > 0) {
            $state = 'con_pago';
            $active = true;
        } elseif ($enrollment->reservation_expires_at !== null && $enrollment->reservation_expires_at->isPast()) {
            $state = 'vencida';
            $active = false;
        }

        $studentName = $student !== null ? trim($student->name) : 'Estudiante';
        $firstName = explode(' ', $studentName)[0];

        return response()->json([
            'data' => [
                'estado' => $state,
                'vigente' => $active,
                'referencia' => $enrollment->reservation_reference,
                'vence_en' => $enrollment->reservation_expires_at?->toIso8601String(),
                'estudiante' => $firstName,
                'curso' => $program?->name,
                'edicion' => $cohort?->code,
                'monto' => [
                    'moneda' => 'PEN',
                    'total' => number_format($total, 2, '.', ''),
                    'pagado' => number_format($validatedSum, 2, '.', ''),
                    'por_confirmar' => number_format($pendingSum, 2, '.', ''),
                    'saldo' => number_format($balance, 2, '.', ''),
                ],
                'metodos' => [
                    'yape' => config('horus.yape'),
                    'plin' => config('horus.plin'),
                ],
            ],
        ]);
    }

    public function submitVoucher(Request $request, string $token): JsonResponse
    {
        $enrollment = Enrollment::query()
            ->where('reservation_token', $token)
            ->with('cohort.program')
            ->firstOrFail();

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'in:yape,plin,transfer,gateway,cash'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
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
            'message' => 'Comprobante recibido. Secretaría verificará tu pago en breve.',
            'data' => new PaymentResource($payment),
        ], 201);
    }
}
