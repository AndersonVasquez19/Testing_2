<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\CohortSession;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'session_id' => CohortSession::factory(),
            'enrollment_id' => Enrollment::factory(),
            'status' => Attendance::STATUS_PRESENT,
            'recorded_by' => User::factory()->withRole(User::ROLE_COORDINATOR),
            'recorded_at' => now(),
        ];
    }
}
