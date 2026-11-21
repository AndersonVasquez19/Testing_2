<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\AuditarAccionAction;
use App\Actions\ResponderReclamoAction;
use App\Actions\RevocarCertificadoAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RespondComplaintRequest;
use App\Http\Requests\Admin\StoreProgramRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\ActivityLogResource;
use App\Http\Resources\CertificateResource;
use App\Http\Resources\ComplaintResource;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\UserResource;
use App\Models\ActivityLog;
use App\Models\Certificate;
use App\Models\Complaint;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function metrics(): JsonResponse
    {
        $validatedRevenue = (string) Payment::query()
            ->where('status', Payment::STATUS_VALIDATED)
            ->sum('amount');

        return response()->json([
            'total_revenue_pen' => number_format((float) $validatedRevenue, 2, '.', ''),
            'pending_payments_count' => Payment::query()->where('status', Payment::STATUS_PENDING)->count(),
            'active_enrollments_count' => Enrollment::query()->whereIn('enrollment_status', [Enrollment::STATUS_ENROLLED, Enrollment::STATUS_COMPLETED])->count(),
            'whatsapp_pending_count' => Enrollment::query()
                ->whereIn('enrollment_status', [Enrollment::STATUS_ENROLLED, Enrollment::STATUS_COMPLETED])
                ->where('added_to_whatsapp', false)
                ->count(),
            'open_complaints_count' => Complaint::query()->whereIn('status', [Complaint::STATUS_REGISTERED, Complaint::STATUS_IN_REVIEW])->count(),
            'issued_certificates_count' => Certificate::query()->whereNull('revoked_at')->count(),
            'programs_count' => Program::query()->where('status', 'active')->count(),
            'users_count' => User::query()->where('is_active', true)->count(),
        ]);
    }

    public function users(Request $request): AnonymousResourceCollection
    {
        $query = User::query()->latest();
        if ($request->filled('role')) {
            $query->where('role', $request->string('role')->toString());
        }

        return UserResource::collection($query->paginate(20));
    }

    public function storeUser(StoreUserRequest $request, AuditarAccionAction $auditar): JsonResponse
    {
        $data = $request->validated();
        $data['email'] = strtolower($request->string('email')->toString());

        $user = User::query()->create($data);
        $user->syncRoles([$user->role]);

        $auditar->execute($request->user(), 'user_created', $user, ['role' => $user->role], $request);

        return response()->json([
            'message' => 'Usuario creado correctamente.',
            'data' => new UserResource($user),
        ], 201);
    }

    public function updateUser(UpdateUserRequest $request, User $user, AuditarAccionAction $auditar): JsonResponse
    {
        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        }
        if (isset($data['email']) && is_string($data['email'])) {
            $data['email'] = strtolower($data['email']);
        }

        /** @var User $actor */
        $actor = $request->user();
        if ($user->id === $actor->id && (($data['role'] ?? $user->role) !== User::ROLE_ADMIN || ($data['is_active'] ?? true) === false)) {
            return response()->json(['message' => 'No puedes degradar ni desactivar tu propia cuenta administradora.'], 422);
        }

        $user->update($data);
        if (isset($data['role']) && is_string($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        $auditar->execute($actor, 'user_updated', $user, ['role' => $user->role], $request);

        return response()->json([
            'message' => 'Usuario actualizado.',
            'data' => new UserResource($user->fresh() ?? $user),
        ]);
    }

    public function programs(): AnonymousResourceCollection
    {
        return ProgramResource::collection(Program::query()->latest()->paginate(20));
    }

    public function storeProgram(StoreProgramRequest $request, AuditarAccionAction $auditar): JsonResponse
    {
        $data = $request->validated();
        $slug = is_string($data['slug'] ?? null) ? $data['slug'] : null;
        $data['slug'] = $slug ?? Str::slug($request->string('name')->toString()).'-'.strtolower(Str::random(4));

        $program = Program::query()->create($data);
        $auditar->execute($request->user(), 'program_created', $program, ['code' => $program->code], $request);

        return response()->json([
            'message' => 'Programa creado correctamente.',
            'data' => new ProgramResource($program),
        ], 201);
    }

    public function updateProgram(Request $request, Program $program, AuditarAccionAction $auditar): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'min:3', 'max:180'],
            'description' => ['sometimes', 'string', 'min:10', 'max:5000'],
            'price' => ['sometimes', 'numeric', 'min:0', 'max:99999999.99'],
            'duration_hours' => ['sometimes', 'integer', 'min:1'],
            'is_published' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'string', 'in:active,inactive,archived'],
        ]);

        $program->update($validated);
        $auditar->execute($request->user(), 'program_updated', $program, $validated, $request);

        return response()->json([
            'message' => 'Programa actualizado.',
            'data' => new ProgramResource($program->fresh() ?? $program),
        ]);
    }

    public function complaints(Request $request): AnonymousResourceCollection
    {
        $query = Complaint::query()->with('responder')->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        return ComplaintResource::collection($query->paginate(20));
    }

    public function respondComplaint(
        RespondComplaintRequest $request,
        Complaint $complaint,
        ResponderReclamoAction $action,
    ): JsonResponse {
        /** @var User $actor */
        $actor = $request->user();
        $updated = $action->execute(
            $complaint,
            $request->string('company_response')->toString(),
            $request->string('status', Complaint::STATUS_RESPONDED)->toString(),
            $actor,
            $request,
        );

        return response()->json([
            'message' => 'Respuesta registrada y enviada al consumidor.',
            'data' => new ComplaintResource($updated),
        ]);
    }

    public function certificates(): AnonymousResourceCollection
    {
        return CertificateResource::collection(
            Certificate::query()->with(['enrollment.student', 'enrollment.cohort.program', 'issuer'])->latest()->paginate(20)
        );
    }

    public function revokeCertificate(
        Request $request,
        Certificate $certificate,
        RevocarCertificadoAction $action,
    ): JsonResponse {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ]);

        /** @var User $actor */
        $actor = $request->user();
        $updated = $action->execute($certificate, $actor, $validated['reason'], $request);

        return response()->json([
            'message' => 'Certificado revocado.',
            'data' => new CertificateResource($updated),
        ]);
    }

    public function activityLogs(Request $request): AnonymousResourceCollection
    {
        $query = ActivityLog::query()->with('user')->latest('id');
        if ($request->filled('action')) {
            $query->where('action', $request->string('action')->toString());
        }

        return ActivityLogResource::collection($query->paginate(30));
    }
}
