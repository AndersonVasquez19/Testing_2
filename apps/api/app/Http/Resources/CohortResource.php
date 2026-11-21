<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Cohort;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Cohort
 */
class CohortResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $actor = $request->user();
        $isStaff = $actor instanceof User && $actor->hasPortalRole(User::ROLE_ADMIN, User::ROLE_SECRETARY, User::ROLE_COORDINATOR);

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'code' => $this->code,
            'program_id' => $this->program_id,
            'coordinator_id' => $this->coordinator_id,
            'name' => $this->name,
            'modality' => $this->modality,
            'whatsapp_group_link' => $this->when($isStaff, $this->whatsapp_group_link),
            'default_meeting_url' => $this->when($isStaff, $this->default_meeting_url),
            'location_name' => $this->location_name,
            'location_address' => $this->location_address,
            'location_map_url' => $this->location_map_url,
            'start_date' => $this->start_date->toDateString(),
            'end_date' => $this->end_date->toDateString(),
            'max_capacity' => $this->max_capacity,
            'enrolled_count' => $this->enrolled_count,
            'available_slots' => $this->availableSlots(),
            'status' => $this->status,
            'program' => new ProgramResource($this->whenLoaded('program')),
            'coordinator' => new UserResource($this->whenLoaded('coordinator')),
            'sessions' => CohortSessionResource::collection($this->whenLoaded('sessions')),
        ];
    }
}
