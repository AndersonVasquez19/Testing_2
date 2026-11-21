<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Coordinador;

use App\Actions\AuditarAccionAction;
use App\Actions\CambiarModalidadSesionAction;
use App\Actions\EmitirCertificadoAction;
use App\Actions\RegistrarAsistenciaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Coordinador\RecordAttendanceRequest;
use App\Http\Requests\Coordinador\StoreCohortRequest;
use App\Http\Requests\Coordinador\StoreSessionRequest;
use App\Http\Requests\Coordinador\UpdateAcademicResultRequest;
use App\Http\Requests\Coordinador\UpdateSessionRequest;
use App\Http\Resources\AdvisorySlotResource;
use App\Http\Resources\CertificateResource;
use App\Http\Resources\CohortResource;
use App\Http\Resources\CohortSessionResource;
use App\Http\Resources\EnrollmentResource;
use App\Models\AdvisorySlot;
use App\Models\Cohort;
use App\Models\CohortSession;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CoordinatorController extends Controller
{
    public function cohorts(Request $request): AnonymousResourceCollection
    {
        $query = Cohort::query()->with(['program', 'coordinator', 'sessions'])->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        return CohortResource::collection($query->paginate(20));
    }

    public function storeCohort(StoreCohortRequest $request, AuditarAccionAction $auditar): JsonResponse
    {
        /** @var User $actor */
        $actor = $request->user();
        $data = $request->validated();
        $data['coordinator_id'] = $data['coordinator_id'] ?? $actor->id;

        $cohort = Cohort::query()->create($data);
        $auditar->execute($actor, 'cohort_created', $cohort, ['code' => $cohort->code], $request);

        return response()->json([
            'message' => 'Convocatoria creada correctamente.',
            'data' => new CohortResource($cohort->load(['program', 'coordinator'])),
        ], 201);
    }

    public function updateCohort(Request $request, Cohort $cohort, AuditarAccionAction $auditar): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'min:3', 'max:180'],
            'modality' => ['sometimes', 'string', 'in:virtual,presential,hybrid'],
            'whatsapp_group_link' => ['nullable', 'url', 'max:255'],
            'default_meeting_url' => ['nullable', 'url', 'max:500'],
            'location_name' => ['nullable', 'string', 'max:180'],
            'location_address' => ['nullable', 'string', 'max:255'],
            'location_map_url' => ['nullable', 'url', 'max:500'],
            'status' => ['sometimes', 'string', 'in:draft,open,in_progress,closed,cancelled'],
        ]);

        $cohort->update($validated);
        $auditar->execute($request->user(), 'cohort_updated', $cohort, $validated, $request);

        return response()->json([
            'message' => 'Convocatoria actualizada.',
            'data' => new CohortResource($cohort->fresh(['program', 'coordinator', 'sessions']) ?? $cohort),
        ]);
    }

    public function sessions(Cohort $cohort): AnonymousResourceCollection
    {
        return CohortSessionResource::collection($cohort->sessions()->get());
    }

    public function storeSession(StoreSessionRequest $request, Cohort $cohort, AuditarAccionAction $auditar): JsonResponse
    {
        $session = $cohort->sessions()->create($request->validated());
        $auditar->execute($request->user(), 'cohort_session_created', $session, [
            'cohort_id' => $cohort->id,
            'session_type' => $session->session_type,
        ], $request);

        return response()->json([
            'message' => 'Sesión programada correctamente.',
            'data' => new CohortSessionResource($session),
        ], 201);
    }

    public function updateSession(
        UpdateSessionRequest $request,
        CohortSession $session,
        CambiarModalidadSesionAction $action,
    ): JsonResponse {
        /** @var User $actor */
        $actor = $request->user();
        $updated = $action->execute($session, $request->validated(), $actor, $request);

        return response()->json([
            'message' => 'Sesión actualizada correctamente.',
            'data' => new CohortSessionResource($updated),
        ]);
    }

    public function recordAttendance(
        RecordAttendanceRequest $request,
        CohortSession $session,
        RegistrarAsistenciaAction $action,
    ): JsonResponse {
        /** @var User $actor */
        $actor = $request->user();
        /** @var list<array{enrollment_id: int, status: string, notes?: string|null}> $records */
        $records = $request->validated('records');
        $action->execute($session, $records, $actor, $request);

        return response()->json([
            'message' => 'Asistencia registrada correctamente.',
        ]);
    }

    public function cohortEnrollments(Cohort $cohort): AnonymousResourceCollection
    {
        $enrollments = $cohort->enrollments()
            ->with(['student', 'activeCertificate'])
            ->whereIn('enrollment_status', [Enrollment::STATUS_ENROLLED, Enrollment::STATUS_COMPLETED])
            ->get();

        return EnrollmentResource::collection($enrollments);
    }

    public function updateAcademicResult(
        UpdateAcademicResultRequest $request,
        Enrollment $enrollment,
        AuditarAccionAction $auditar,
    ): JsonResponse {
        $enrollment->update($request->validated());
        $auditar->execute($request->user(), 'enrollment_academic_updated', $enrollment, $request->validated(), $request);

        return response()->json([
            'message' => 'Resultado académico actualizado.',
            'data' => new EnrollmentResource($enrollment->fresh(['student', 'activeCertificate']) ?? $enrollment),
        ]);
    }

    public function issueCertificate(
        Request $request,
        Enrollment $enrollment,
        EmitirCertificadoAction $action,
    ): JsonResponse {
        /** @var User $actor */
        $actor = $request->user();
        $certificate = $action->execute($enrollment, $actor, $request);

        return response()->json([
            'message' => 'Certificado emitido con código QR verificable.',
            'data' => new CertificateResource($certificate),
        ], 201);
    }

    public function advisorySlots(): AnonymousResourceCollection
    {
        return AdvisorySlotResource::collection(
            AdvisorySlot::query()->with(['program', 'advisor', 'student'])->orderBy('slot_date')->paginate(20)
        );
    }

    public function storeAdvisorySlot(Request $request, AuditarAccionAction $auditar): JsonResponse
    {
        $validated = $request->validate([
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'advisor_id' => ['nullable', 'integer', 'exists:users,id'],
            'slot_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i:s,H:i'],
            'end_time' => ['required', 'date_format:H:i:s,H:i', 'after:start_time'],
            'modality' => ['required', 'string', 'in:virtual,presential'],
            'meeting_or_location' => ['required', 'string', 'max:500'],
        ]);

        /** @var User $actor */
        $actor = $request->user();
        $validated['advisor_id'] = $validated['advisor_id'] ?? $actor->id;

        $slot = AdvisorySlot::query()->create($validated);
        $auditar->execute($actor, 'advisory_slot_created', $slot, ['slot_date' => $slot->slot_date->toDateString()], $request);

        return response()->json([
            'message' => 'Horario de asesoramiento publicado.',
            'data' => new AdvisorySlotResource($slot->load(['program', 'advisor'])),
        ], 201);
    }
}
