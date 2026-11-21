<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MarcarAgregadoWhatsappAction
{
    public function __construct(private readonly AuditarAccionAction $auditar) {}

    public function execute(Enrollment $enrollment, User $actor, bool $added = true, ?Request $request = null): Enrollment
    {
        return DB::transaction(function () use ($enrollment, $actor, $added, $request): Enrollment {
            /** @var Enrollment $locked */
            $locked = Enrollment::query()->lockForUpdate()->findOrFail($enrollment->id);

            if (! in_array($locked->enrollment_status, [Enrollment::STATUS_ENROLLED, Enrollment::STATUS_COMPLETED], true)) {
                throw ValidationException::withMessages([
                    'enrollment_status' => ['El estudiante debe tener su pago validado antes de incorporarlo al grupo de WhatsApp.'],
                ]);
            }

            $locked->update([
                'added_to_whatsapp' => $added,
                'whatsapp_added_by' => $added ? $actor->id : null,
                'whatsapp_added_at' => $added ? now() : null,
            ]);

            $this->auditar->execute(
                $actor,
                $added ? 'enrollment_whatsapp_added' : 'enrollment_whatsapp_removed',
                $locked,
                [
                    'cohort_id' => $locked->cohort_id,
                    'student_id' => $locked->user_id,
                    'added_to_whatsapp' => $added,
                ],
                $request,
            );

            return $locked->fresh(['student', 'cohort.program', 'whatsappAddedBy']) ?? $locked;
        });
    }
}
