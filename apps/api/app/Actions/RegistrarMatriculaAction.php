<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Cohort;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegistrarMatriculaAction
{
    public function __construct(private readonly AuditarAccionAction $auditar) {}

    public function execute(
        User $student,
        int $cohortId,
        ?User $actor = null,
        ?string $notes = null,
        ?Request $request = null,
    ): Enrollment {
        return DB::transaction(function () use ($student, $cohortId, $actor, $notes, $request): Enrollment {
            /** @var Cohort $cohort */
            $cohort = Cohort::query()->lockForUpdate()->findOrFail($cohortId);

            if (! in_array($cohort->status, ['open', 'in_progress'], true)) {
                throw ValidationException::withMessages([
                    'cohort_id' => ['La convocatoria no está abierta para matrículas.'],
                ]);
            }

            if ($cohort->enrolled_count >= $cohort->max_capacity) {
                throw ValidationException::withMessages([
                    'cohort_id' => ['No hay cupos disponibles en esta convocatoria.'],
                ]);
            }

            $existing = Enrollment::query()
                ->where('user_id', $student->id)
                ->where('cohort_id', $cohort->id)
                ->where('enrollment_status', '!=', Enrollment::STATUS_CANCELLED)
                ->exists();

            if ($existing) {
                throw ValidationException::withMessages([
                    'user_id' => ['El estudiante ya tiene una matrícula activa en esta convocatoria.'],
                ]);
            }

            $rawDays = config('horus.reservation_days', 2);
            $days = is_numeric($rawDays) ? max(2, (int) $rawDays) : 2;

            $enrollment = Enrollment::query()->create([
                'user_id' => $student->id,
                'cohort_id' => $cohort->id,
                'enrollment_status' => Enrollment::STATUS_PENDING_PAYMENT,
                'reservation_reference' => 'HRS-'.strtoupper(Str::random(6)),
                'reservation_token' => Str::random(40),
                'reservation_expires_at' => now()->addDays($days),
                'added_to_whatsapp' => false,
                'notes' => $notes,
            ]);

            $cohort->increment('enrolled_count');

            $this->auditar->execute(
                $actor ?? $student,
                'enrollment_created',
                $enrollment,
                [
                    'cohort_id' => $cohort->id,
                    'student_id' => $student->id,
                    'reference' => $enrollment->reservation_reference,
                ],
                $request,
            );

            return $enrollment;
        });
    }
}
