<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\CohortSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CambiarModalidadSesionAction
{
    public function __construct(private readonly AuditarAccionAction $auditar) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(CohortSession $session, array $data, User $actor, ?Request $request = null): CohortSession
    {
        return DB::transaction(function () use ($session, $data, $actor, $request): CohortSession {
            /** @var CohortSession $locked */
            $locked = CohortSession::query()->with('cohort')->lockForUpdate()->findOrFail($session->id);
            $cohort = $locked->cohort;

            $previousType = $locked->session_type;
            $newType = is_string($data['session_type'] ?? null) ? $data['session_type'] : $locked->session_type;

            $meetingUrl = array_key_exists('meeting_url', $data)
                ? $data['meeting_url']
                : ($newType === CohortSession::TYPE_VIRTUAL
                    ? ($locked->meeting_url ?? $cohort?->default_meeting_url)
                    : null);

            $classroom = array_key_exists('classroom', $data)
                ? $data['classroom']
                : $locked->classroom;

            $locationName = array_key_exists('location_name', $data)
                ? $data['location_name']
                : ($newType === CohortSession::TYPE_PRESENTIAL
                    ? ($locked->location_name ?? $cohort?->location_name)
                    : null);

            $locationMapUrl = array_key_exists('location_map_url', $data)
                ? $data['location_map_url']
                : ($newType === CohortSession::TYPE_PRESENTIAL
                    ? ($locked->location_map_url ?? $cohort?->location_map_url)
                    : null);

            if ($newType === CohortSession::TYPE_VIRTUAL && empty($meetingUrl)) {
                throw ValidationException::withMessages([
                    'meeting_url' => ['Una sesión virtual requiere un enlace de reunión (Zoom, Meet o Teams).'],
                ]);
            }

            if ($newType === CohortSession::TYPE_PRESENTIAL && empty($locationName) && empty($classroom)) {
                throw ValidationException::withMessages([
                    'classroom' => ['Una sesión presencial requiere sede o aula física.'],
                ]);
            }

            $locked->update([
                'session_type' => $newType,
                'meeting_url' => $meetingUrl,
                'classroom' => $newType === CohortSession::TYPE_PRESENTIAL ? $classroom : null,
                'location_name' => $newType === CohortSession::TYPE_PRESENTIAL ? $locationName : null,
                'location_map_url' => $newType === CohortSession::TYPE_PRESENTIAL ? $locationMapUrl : null,
                'title' => $data['title'] ?? $locked->title,
                'session_date' => $data['session_date'] ?? $locked->session_date,
                'start_time' => $data['start_time'] ?? $locked->start_time,
                'end_time' => $data['end_time'] ?? $locked->end_time,
                'recording_url' => array_key_exists('recording_url', $data) ? $data['recording_url'] : $locked->recording_url,
                'material_pdf_path' => array_key_exists('material_pdf_path', $data) ? $data['material_pdf_path'] : $locked->material_pdf_path,
                'status' => $data['status'] ?? $locked->status,
            ]);

            $this->auditar->execute(
                $actor,
                'cohort_session_modality_updated',
                $locked,
                [
                    'cohort_id' => $locked->cohort_id,
                    'previous_type' => $previousType,
                    'new_type' => $newType,
                ],
                $request,
            );

            return $locked->fresh() ?? $locked;
        });
    }
}
