<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Secretaria;

use App\Actions\AprobarPagoAction;
use App\Actions\MarcarAgregadoWhatsappAction;
use App\Actions\RechazarPagoAction;
use App\Actions\RegistrarMatriculaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Secretaria\StoreEnrollmentRequest;
use App\Http\Requests\Secretaria\StorePaymentRequest;
use App\Http\Resources\ContactLeadResource;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\UserResource;
use App\Models\Cohort;
use App\Models\ContactLead;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SecretaryController extends Controller
{
    public function dashboard(): JsonResponse
    {
        return response()->json([
            'pending_payments' => Payment::query()->where('status', Payment::STATUS_PENDING)->count(),
            'whatsapp_pending' => Enrollment::query()
                ->whereIn('enrollment_status', [Enrollment::STATUS_ENROLLED, Enrollment::STATUS_COMPLETED])
                ->where('added_to_whatsapp', false)
                ->count(),
            'new_leads' => ContactLead::query()->where('status', ContactLead::STATUS_NEW)->count(),
            'active_enrollments' => Enrollment::query()->where('enrollment_status', Enrollment::STATUS_ENROLLED)->count(),
        ]);
    }

    public function leads(Request $request): AnonymousResourceCollection
    {
        $query = ContactLead::query()->with(['program', 'assignee'])->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        return ContactLeadResource::collection($query->paginate(20));
    }

    public function updateLead(Request $request, ContactLead $lead): ContactLeadResource
    {
        $validated = $request->validate([
            'status' => ['sometimes', 'string', 'in:new,contacted,qualified,converted,discarded'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $lead->update($validated);

        return new ContactLeadResource($lead->fresh(['program', 'assignee']) ?? $lead);
    }

    public function students(Request $request): AnonymousResourceCollection
    {
        $query = User::query()->where('role', User::ROLE_STUDENT)->latest();
        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('dni', 'like', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        return UserResource::collection($query->paginate($request->integer('per_page', 20)));
    }

    public function storeStudent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:180'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'dni' => ['required', 'string', 'regex:/^\d{8}$/', 'unique:users,dni'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'dni' => $validated['dni'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['dni']),
            'role' => User::ROLE_STUDENT,
            'is_active' => true,
            'must_change_password' => true,
        ]);

        if (Role::query()->where('name', User::ROLE_STUDENT)->exists()) {
            $user->syncRoles([User::ROLE_STUDENT]);
        }

        return response()->json([
            'message' => 'Estudiante registrado correctamente.',
            'data' => new UserResource($user),
        ], 201);
    }

    public function cohorts(): JsonResponse
    {
        $cohorts = Cohort::query()
            ->with('program')
            ->whereIn('status', ['open', 'in_progress'])
            ->orderBy('start_date')
            ->get();

        return response()->json(['data' => $cohorts]);
    }

    public function enrollments(Request $request): AnonymousResourceCollection
    {
        $query = Enrollment::query()
            ->with(['student', 'cohort.program', 'payments'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('enrollment_status', $request->string('status')->toString());
        }

        return EnrollmentResource::collection($query->paginate(20));
    }

    public function storeEnrollment(StoreEnrollmentRequest $request, RegistrarMatriculaAction $action): JsonResponse
    {
        /** @var User $actor */
        $actor = $request->user();
        /** @var User $student */
        $student = User::query()->findOrFail($request->integer('user_id'));

        $enrollment = $action->execute(
            $student,
            $request->integer('cohort_id'),
            $actor,
            $request->filled('notes') ? $request->string('notes')->toString() : null,
            $request,
        );

        return response()->json([
            'message' => 'Matrícula registrada con reserva de cupo.',
            'data' => new EnrollmentResource($enrollment->load(['student', 'cohort.program'])),
        ], 201);
    }

    public function payments(Request $request): AnonymousResourceCollection
    {
        $query = Payment::query()
            ->with(['enrollment.student', 'enrollment.cohort.program', 'validator'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        return PaymentResource::collection($query->paginate(20));
    }

    public function storePayment(StorePaymentRequest $request): JsonResponse
    {
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
            'enrollment_id' => $request->integer('enrollment_id'),
            'amount' => $request->input('amount'),
            'payment_method' => $request->string('payment_method')->toString(),
            'operation_code' => $request->input('operation_code'),
            'voucher_path' => $voucherPath,
            'voucher_hash' => $voucherHash,
            'status' => Payment::STATUS_PENDING,
            'notes' => $request->input('notes'),
        ]);

        return response()->json([
            'message' => 'Comprobante registrado y pendiente de validación.',
            'data' => new PaymentResource($payment->load(['enrollment.student', 'enrollment.cohort.program'])),
        ], 201);
    }

    public function approvePayment(Request $request, Payment $payment, AprobarPagoAction $action): JsonResponse
    {
        /** @var User $actor */
        $actor = $request->user();
        $notes = $request->filled('notes') ? $request->string('notes')->toString() : null;
        $updated = $action->execute($payment, $actor, $notes, $request);

        return response()->json([
            'message' => 'Pago validado y alumno incorporado a la cola de WhatsApp.',
            'data' => new PaymentResource($updated),
        ]);
    }

    public function rejectPayment(Request $request, Payment $payment, RechazarPagoAction $action): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ]);

        /** @var User $actor */
        $actor = $request->user();
        $updated = $action->execute($payment, $actor, $validated['reason'], $request);

        return response()->json([
            'message' => 'Pago rechazado.',
            'data' => new PaymentResource($updated),
        ]);
    }

    public function whatsappQueue(Request $request): AnonymousResourceCollection
    {
        $query = Enrollment::query()
            ->whereIn('enrollment_status', [Enrollment::STATUS_ENROLLED, Enrollment::STATUS_COMPLETED])
            ->with(['student', 'cohort.program', 'whatsappAddedBy'])
            ->orderBy('added_to_whatsapp')
            ->latest();

        if ($request->filled('cohort_id')) {
            $query->where('cohort_id', $request->integer('cohort_id'));
        }

        if ($request->filled('added_to_whatsapp')) {
            $query->where('added_to_whatsapp', $request->boolean('added_to_whatsapp'));
        }

        return EnrollmentResource::collection($query->paginate(25));
    }

    public function markAddedToWhatsapp(
        Request $request,
        Enrollment $enrollment,
        MarcarAgregadoWhatsappAction $action,
    ): JsonResponse {
        /** @var User $actor */
        $actor = $request->user();
        $added = $request->boolean('added', true);
        $updated = $action->execute($enrollment, $actor, $added, $request);

        return response()->json([
            'message' => $added ? 'Estudiante marcado como incorporado al grupo de WhatsApp.' : 'Estado de WhatsApp revertido.',
            'data' => new EnrollmentResource($updated),
        ]);
    }
}
