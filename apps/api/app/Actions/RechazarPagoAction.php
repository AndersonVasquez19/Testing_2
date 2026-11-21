<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RechazarPagoAction
{
    public function __construct(private readonly AuditarAccionAction $auditar) {}

    public function execute(Payment $payment, User $validator, string $reason, ?Request $request = null): Payment
    {
        return DB::transaction(function () use ($payment, $validator, $reason, $request): Payment {
            /** @var Payment $lockedPayment */
            $lockedPayment = Payment::query()->lockForUpdate()->findOrFail($payment->id);

            if ($lockedPayment->status !== Payment::STATUS_PENDING) {
                throw ValidationException::withMessages([
                    'status' => ['Solo se pueden rechazar pagos en estado pendiente.'],
                ]);
            }

            $lockedPayment->update([
                'status' => Payment::STATUS_REJECTED,
                'rejection_reason' => $reason,
                'validated_by' => $validator->id,
                'validated_at' => now(),
            ]);

            $this->auditar->execute(
                $validator,
                'payment_rejected',
                $lockedPayment,
                [
                    'enrollment_id' => $lockedPayment->enrollment_id,
                    'reason' => $reason,
                ],
                $request,
            );

            return $lockedPayment->fresh(['enrollment.student', 'validator']) ?? $lockedPayment;
        });
    }
}
