<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('Horus2026!'),
            'dni' => fake()->unique()->numerify('########'),
            'phone' => '+51'.fake()->numerify('9########'),
            'role' => User::ROLE_STUDENT,
            'is_active' => true,
            'must_change_password' => false,
            'remember_token' => Str::random(10),
        ];
    }

    public function withRole(string $role): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => $role,
        ])->afterCreating(function (User $user) use ($role): void {
            if (Role::query()->where('name', $role)->exists()) {
                $user->syncRoles([$role]);
            }
        });
    }
}
