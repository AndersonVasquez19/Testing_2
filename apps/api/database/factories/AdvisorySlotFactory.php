<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AdvisorySlot;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AdvisorySlot>
 */
class AdvisorySlotFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'program_id' => Program::factory(),
            'advisor_id' => User::factory()->withRole(User::ROLE_COORDINATOR),
            'slot_date' => now()->addDays(5)->toDateString(),
            'start_time' => '15:00:00',
            'end_time' => '16:00:00',
            'modality' => 'virtual',
            'meeting_or_location' => 'https://meet.google.com/adv-horus-01',
            'is_booked' => false,
        ];
    }
}
