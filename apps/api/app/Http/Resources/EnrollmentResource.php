<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Enrollment
 */
class EnrollmentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $paidAccess = in_array($this->enrollment_status, [Enrollment::STATUS_ENROLLED, Enrollment::STATUS_COMPLETED], true);

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'user_id' => $this->user_id,
            'cohort_id' => $this->cohort_id,
            'enrollment_status' => $this->enrollment_status,
            'reservation_reference' => $this->reservation_reference,
            'reservation_token' => $this->reservation_token,
            'reservation_expires_at' => $this->reservation_expires_at?->toIso8601String(),
            'added_to_whatsapp' => $this->added_to_whatsapp,
            'whatsapp_added_by' => $this->whatsapp_added_by,
            'whatsapp_added_at' => $this->whatsapp_added_at?->toIso8601String(),
            'whatsapp_group_link' => $this->when(
                $paidAccess && $this->relationLoaded('cohort'),
                fn () => $this->cohort?->whatsapp_group_link
            ),
            'final_grade' => $this->final_grade !== null ? (string) $this->final_grade : null,
            'attendance_percentage' => $this->attendance_percentage !== null ? (string) $this->attendance_percentage : null,
            'academic_approved' => $this->academic_approved,
            'notes' => $this->notes,
            'student' => new UserResource($this->whenLoaded('student')),
            'cohort' => new CohortResource($this->whenLoaded('cohort')),
            'whatsapp_added_by_user' => new UserResource($this->whenLoaded('whatsappAddedBy')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'active_certificate' => new CertificateResource($this->whenLoaded('activeCertificate')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
