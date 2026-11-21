<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\AdvisorySlot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservarAsesoriaAction
{
    public function __construct(private readonly AuditarAccionAction $auditar) {}

    public function execute(
        AdvisorySlot $slot,
        User $student,
        ?string $topic = null,
        ?Request $request = null,
    ): AdvisorySlot {
        return DB::transaction(function () use ($slot, $student, $topic, $request): AdvisorySlot {
            /** @var AdvisorySlot $locked */
            $locked = AdvisorySlot::query()->lockForUpdate()->findOrFail($slot->id);

            if ($locked->is_booked) {
                throw ValidationException::withMessages([
                    'slot_id' => ['Este horario de asesoramiento ya fue reservado.'],
                ]);
            }

            $locked->update([
                'is_booked' => true,
                'student_id' => $student->id,
                'topic' => $topic,
            ]);

            $this->auditar->execute(
                $student,
                'advisory_slot_booked',
                $locked,
                [
                    'program_id' => $locked->program_id,
                    'advisor_id' => $locked->advisor_id,
                    'student_id' => $student->id,
                ],
                $request,
            );

            return $locked->fresh(['program', 'advisor', 'student']) ?? $locked;
        });
    }
}
