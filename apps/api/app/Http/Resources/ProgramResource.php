<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Program
 */
class ProgramResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'code' => $this->code,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'description' => $this->description,
            'syllabus_json' => $this->syllabus_json,
            'price' => (string) $this->price,
            'duration_hours' => $this->duration_hours,
            'min_attendance_percentage' => (string) $this->min_attendance_percentage,
            'min_passing_grade' => (string) $this->min_passing_grade,
            'certification_label' => $this->certification_label,
            'image_path' => $this->image_path,
            'is_published' => $this->is_published,
            'status' => $this->status,
            'cohorts' => CohortResource::collection($this->whenLoaded('cohorts')),
        ];
    }
}
