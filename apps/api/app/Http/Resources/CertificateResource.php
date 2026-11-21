<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Certificate
 */
class CertificateResource extends JsonResource
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
            'verification_uuid' => $this->verification_uuid,
            'certificate_code' => $this->certificate_code,
            'qr_payload' => $this->qr_payload,
            'snapshot_data' => $this->snapshot_data,
            'issued_at' => $this->issued_at->toIso8601String(),
            'revoked_at' => $this->revoked_at?->toIso8601String(),
            'revocation_reason' => $this->revocation_reason,
            'is_valid' => ! $this->isRevoked(),
            'issuer' => new UserResource($this->whenLoaded('issuer')),
        ];
    }
}
