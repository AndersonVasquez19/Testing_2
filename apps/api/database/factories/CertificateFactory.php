<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $verificationUuid = (string) Str::uuid();

        return [
            'uuid' => (string) Str::uuid(),
            'enrollment_id' => Enrollment::factory(),
            'verification_uuid' => $verificationUuid,
            'certificate_code' => 'CERT-2026-'.fake()->unique()->numerify('######'),
            'qr_payload' => 'http://localhost:3000/verificar-certificado/'.$verificationUuid,
            'snapshot_data' => [
                'student_name' => 'Estudiante Demo',
                'program_name' => 'Programa Demo',
            ],
            'issued_by' => User::factory()->withRole(User::ROLE_COORDINATOR),
            'issued_at' => now(),
        ];
    }
}
