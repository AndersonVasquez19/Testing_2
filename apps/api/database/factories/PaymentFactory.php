<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'enrollment_id' => Enrollment::factory(),
            'amount' => '250.00',
            'payment_method' => Payment::METHOD_YAPE,
            'operation_code' => fake()->numerify('OP-######'),
            'status' => Payment::STATUS_PENDING,
        ];
    }
}
