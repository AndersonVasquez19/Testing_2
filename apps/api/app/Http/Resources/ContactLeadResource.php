<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\ContactLead;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ContactLead
 */
class ContactLeadResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'program_id' => $this->program_id,
            'subject' => $this->subject,
            'message' => $this->message,
            'source_path' => $this->source_path,
            'status' => $this->status,
            'assigned_to' => $this->assigned_to,
            'notes' => $this->notes,
            'program' => new ProgramResource($this->whenLoaded('program')),
            'assignee' => new UserResource($this->whenLoaded('assignee')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
