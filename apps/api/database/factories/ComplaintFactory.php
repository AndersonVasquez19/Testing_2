<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Complaint;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Complaint>
 */
class ComplaintFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'correlative_code' => 'REC-2026-'.fake()->unique()->numerify('####'),
            'consumer_name' => fake()->name(),
            'consumer_document_type' => 'dni',
            'consumer_document_number' => fake()->numerify('########'),
            'consumer_phone' => '+51'.fake()->numerify('9########'),
            'consumer_email' => fake()->safeEmail(),
            'consumer_address' => fake()->address(),
            'is_minor' => false,
            'item_type' => 'service',
            'item_description' => 'Curso de capacitación',
            'claimed_amount' => '250.00',
            'complaint_type' => 'claim',
            'detail' => fake()->paragraph(),
            'consumer_request' => 'Solicito atención oportuna.',
            'status' => Complaint::STATUS_REGISTERED,
            'legal_deadline' => now()->addWeekdays(15)->toDateString(),
        ];
    }
}
