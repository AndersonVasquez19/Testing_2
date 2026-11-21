<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Payment
 */
class PaymentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'enrollment_id' => $this->enrollment_id,
            'amount' => (string) $this->amount,
            'payment_method' => $this->payment_method,
            'operation_code' => $this->operation_code,
            'voucher_path' => $this->voucher_path,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'validated_by' => $this->validated_by,
            'validated_at' => $this->validated_at?->toIso8601String(),
            'notes' => $this->notes,
            'enrollment' => new EnrollmentResource($this->whenLoaded('enrollment')),
            'validator' => new UserResource($this->whenLoaded('validator')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
