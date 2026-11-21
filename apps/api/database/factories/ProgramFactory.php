<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->sentence(3);

        return [
            'uuid' => (string) Str::uuid(),
            'code' => strtoupper(fake()->unique()->bothify('PRG-###')),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('###'),
            'type' => Program::TYPE_COURSE,
            'description' => fake()->paragraph(),
            'syllabus_json' => [
                ['module' => 'Módulo 1', 'topics' => ['Fundamentos', 'Práctica guiada']],
                ['module' => 'Módulo 2', 'topics' => ['Aplicación real', 'Evaluación final']],
            ],
            'price' => '250.00',
            'duration_hours' => 40,
            'min_attendance_percentage' => '80.00',
            'min_passing_grade' => '13.00',
            'certification_label' => 'Certificado digital verificable por QR',
            'is_published' => true,
            'status' => 'active',
        ];
    }
}
