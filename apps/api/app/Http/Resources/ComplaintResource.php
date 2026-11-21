<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Complaint
 */
class ComplaintResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'correlative_code' => $this->correlative_code,
            'consumer_name' => $this->consumer_name,
            'consumer_document_type' => $this->consumer_document_type,
            'consumer_document_number' => $this->consumer_document_number,
            'consumer_phone' => $this->consumer_phone,
            'consumer_email' => $this->consumer_email,
            'consumer_address' => $this->consumer_address,
            'is_minor' => $this->is_minor,
            'representative_name' => $this->representative_name,
            'item_type' => $this->item_type,
            'item_description' => $this->item_description,
            'claimed_amount' => $this->claimed_amount !== null ? (string) $this->claimed_amount : null,
            'complaint_type' => $this->complaint_type,
            'detail' => $this->detail,
            'consumer_request' => $this->consumer_request,
            'status' => $this->status,
            'legal_deadline' => $this->legal_deadline->toDateString(),
            'company_response' => $this->company_response,
            'responded_by' => $this->responded_by,
            'responded_at' => $this->responded_at?->toIso8601String(),
            'receipt_pdf_path' => $this->receipt_pdf_path,
            'responder' => new UserResource($this->whenLoaded('responder')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
