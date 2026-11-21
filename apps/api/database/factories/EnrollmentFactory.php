<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Cohort;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'user_id' => User::factory()->withRole(User::ROLE_STUDENT),
            'cohort_id' => Cohort::factory(),
            'enrollment_status' => Enrollment::STATUS_PENDING_PAYMENT,
            'reservation_reference' => 'HRS-'.strtoupper(Str::random(6)),
            'reservation_token' => Str::random(40),
            'reservation_expires_at' => now()->addDays(2),
            'added_to_whatsapp' => false,
            'academic_approved' => false,
        ];
    }
}
