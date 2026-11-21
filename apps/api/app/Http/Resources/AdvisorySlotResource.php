<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\AdvisorySlot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AdvisorySlot
 */
class AdvisorySlotResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'program_id' => $this->program_id,
            'advisor_id' => $this->advisor_id,
            'slot_date' => $this->slot_date->toDateString(),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'modality' => $this->modality,
            'meeting_or_location' => $this->when($this->is_booked || $request->user()?->hasPortalRole('admin', 'coordinator'), $this->meeting_or_location),
            'is_booked' => $this->is_booked,
            'student_id' => $this->student_id,
            'topic' => $this->topic,
            'program' => new ProgramResource($this->whenLoaded('program')),
            'advisor' => new UserResource($this->whenLoaded('advisor')),
            'student' => new UserResource($this->whenLoaded('student')),
        ];
    }
}
