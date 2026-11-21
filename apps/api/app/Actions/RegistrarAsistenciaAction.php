<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Attendance;
use App\Models\CohortSession;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrarAsistenciaAction
{
    public function __construct(private readonly AuditarAccionAction $auditar) {}

    /**
     * @param  list<array{enrollment_id: int, status: string, notes?: string|null}>  $records
     */
    public function execute(CohortSession $session, array $records, User $actor, ?Request $request = null): void
    {
        DB::transaction(function () use ($session, $records, $actor, $request): void {
            $now = now();
            $cohort = $session->cohort;
            $sessionCount = $cohort !== null
                ? $cohort->sessions()->where('status', '!=', 'cancelled')->count()
                : 1;
            $totalSessions = max(1, $sessionCount);

            foreach ($records as $item) {
                /** @var Enrollment $enrollment */
                $enrollment = Enrollment::query()
                    ->where('cohort_id', $session->cohort_id)
                    ->findOrFail($item['enrollment_id']);

                Attendance::query()->updateOrCreate(
                    [
                        'session_id' => $session->id,
                        'enrollment_id' => $enrollment->id,
                    ],
                    [
                        'status' => $item['status'],
                        'notes' => $item['notes'] ?? null,
                        'recorded_by' => $actor->id,
                        'recorded_at' => $now,
                    ]
                );

                $attendedCount = Attendance::query()
                    ->where('enrollment_id', $enrollment->id)
                    ->whereIn('status', [
                        Attendance::STATUS_PRESENT,
                        Attendance::STATUS_LATE,
                        Attendance::STATUS_JUSTIFIED,
                    ])
                    ->count();

                $percentage = round(($attendedCount / $totalSessions) * 100, 2);
                $enrollment->update(['attendance_percentage' => number_format($percentage, 2, '.', '')]);
            }

            $this->auditar->execute(
                $actor,
                'attendance_recorded',
                $session,
                [
                    'cohort_id' => $session->cohort_id,
                    'records_count' => count($records),
                ],
                $request,
            );
        });
    }
}
