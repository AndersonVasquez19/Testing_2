<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ContactLead;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ContactLead>
 */
class ContactLeadFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'full_name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => '+51'.fake()->numerify('9########'),
            'subject' => 'Información sobre cursos',
            'message' => fake()->paragraph(),
            'source_path' => '/contactos',
            'status' => ContactLead::STATUS_NEW,
        ];
    }
}
