<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Cohort;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Cohort>
 */
class CohortFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'code' => 'ED-2026-'.fake()->unique()->numerify('###'),
            'program_id' => Program::factory(),
            'coordinator_id' => User::factory()->withRole(User::ROLE_COORDINATOR),
            'name' => 'Edición '.fake()->monthName().' 2026',
            'modality' => Cohort::MODALITY_HYBRID,
            'whatsapp_group_link' => 'https://chat.whatsapp.com/'.Str::random(22),
            'default_meeting_url' => 'https://meet.google.com/abc-defg-hij',
            'location_name' => 'Sede Central Cajamarca',
            'location_address' => 'Jr. José Gálvez 322, Cajamarca',
            'location_map_url' => 'https://maps.google.com/?q=Jr.+Jose+Galvez+322+Cajamarca',
            'start_date' => now()->addDays(7)->toDateString(),
            'end_date' => now()->addDays(45)->toDateString(),
            'max_capacity' => 30,
            'enrolled_count' => 0,
            'status' => 'open',
        ];
    }
}
