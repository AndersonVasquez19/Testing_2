<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('public registration always assigns student role and login returns bearer token', function (): void {
    $registerRes = $this->postJson('/api/v1/auth/register', [
        'name' => 'Nuevo Estudiante',
        'email' => 'nuevo@horus.pe',
        'dni' => '76543210',
        'phone' => '+51912345678',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'role' => 'admin', // Intento de escalamiento ignorado
    ])->assertCreated();

    $registerRes->assertJsonPath('user.role', User::ROLE_STUDENT);

    $this->postJson('/api/v1/auth/login', [
        'email' => 'nuevo@horus.pe',
        'password' => 'Password123!',
    ])->assertOk()
        ->assertJsonStructure(['token', 'user' => ['id', 'uuid', 'name', 'email', 'role']]);
});

test('admin can view metrics and jsonb activity logs', function (): void {
    $admin = User::factory()->withRole(User::ROLE_ADMIN)->create();
    Sanctum::actingAs($admin);

    $this->getJson('/api/v1/admin/metrics')
        ->assertOk()
        ->assertJsonStructure([
            'total_revenue_pen',
            'pending_payments_count',
            'active_enrollments_count',
            'whatsapp_pending_count',
            'open_complaints_count',
            'issued_certificates_count',
        ]);

    $this->getJson('/api/v1/admin/activity-logs')->assertOk();
});
