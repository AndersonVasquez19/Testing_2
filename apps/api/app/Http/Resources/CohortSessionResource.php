<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\CohortSession;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CohortSession
 */
class CohortSessionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'cohort_id' => $this->cohort_id,
            'session_number' => $this->session_number,
            'title' => $this->title,
            'session_date' => $this->session_date->toDateString(),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'session_type' => $this->session_type,
            'meeting_url' => $this->meeting_url,
            'classroom' => $this->classroom,
            'location_name' => $this->location_name,
            'location_map_url' => $this->location_map_url,
            'recording_url' => $this->recording_url,
            'material_pdf_path' => $this->material_pdf_path,
            'status' => $this->status,
        ];
    }
}
