<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AprobarPagoAction
{
    public function __construct(private readonly AuditarAccionAction $auditar) {}

    public function execute(Payment $payment, User $validator, ?string $notes = null, ?Request $request = null): Payment
    {
        return DB::transaction(function () use ($payment, $validator, $notes, $request): Payment {
            /** @var Payment $lockedPayment */
            $lockedPayment = Payment::query()->lockForUpdate()->findOrFail($payment->id);

            if ($lockedPayment->status !== Payment::STATUS_PENDING) {
                throw ValidationException::withMessages([
                    'status' => ['Solo se pueden aprobar pagos en estado pendiente.'],
                ]);
            }

            /** @var Enrollment $enrollment */
            $enrollment = Enrollment::query()->lockForUpdate()->findOrFail($lockedPayment->enrollment_id);

            $lockedPayment->update([
                'status' => Payment::STATUS_VALIDATED,
                'validated_by' => $validator->id,
                'validated_at' => now(),
                'notes' => $notes ?? $lockedPayment->notes,
            ]);

            if ($enrollment->enrollment_status === Enrollment::STATUS_PENDING_PAYMENT) {
                $enrollment->update([
                    'enrollment_status' => Enrollment::STATUS_ENROLLED,
                ]);
            }

            $this->auditar->execute(
                $validator,
                'payment_validated',
                $lockedPayment,
                [
                    'enrollment_id' => $enrollment->id,
                    'amount' => $lockedPayment->amount,
                    'payment_method' => $lockedPayment->payment_method,
                    'operation_code' => $lockedPayment->operation_code,
                ],
                $request,
            );

            return $lockedPayment->fresh(['enrollment.student', 'enrollment.cohort.program', 'validator']) ?? $lockedPayment;
        });
    }
}
