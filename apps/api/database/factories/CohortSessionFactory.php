<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Cohort;
use App\Models\CohortSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CohortSession>
 */
class CohortSessionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'cohort_id' => Cohort::factory(),
            'session_number' => 1,
            'title' => fake()->sentence(4),
            'session_date' => now()->addDays(10)->toDateString(),
            'start_time' => '18:00:00',
            'end_time' => '20:00:00',
            'session_type' => CohortSession::TYPE_VIRTUAL,
            'meeting_url' => 'https://meet.google.com/xyz-uvwx-rst',
            'classroom' => null,
            'location_name' => null,
            'location_map_url' => null,
            'recording_url' => null,
            'material_pdf_path' => null,
            'status' => 'scheduled',
        ];
    }
}
