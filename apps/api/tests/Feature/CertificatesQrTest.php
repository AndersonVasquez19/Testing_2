<?php

declare(strict_types=1);

use App\Models\Certificate;
use App\Models\Cohort;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
    Storage::fake('local');
});

test('coordinator issues verifiable QR certificate and admin can revoke it', function (): void {
    $coordinator = User::factory()->withRole(User::ROLE_COORDINATOR)->create();
    $admin = User::factory()->withRole(User::ROLE_ADMIN)->create();
    $student = User::factory()->withRole(User::ROLE_STUDENT)->create(['name' => 'Lucía Ramírez']);
    $program = Program::factory()->create(['name' => 'Excel Avanzado Corporativo']);
    $cohort = Cohort::factory()->create(['program_id' => $program->id, 'coordinator_id' => $coordinator->id]);

    $enrollment = Enrollment::factory()->create([
        'user_id' => $student->id,
        'cohort_id' => $cohort->id,
        'enrollment_status' => Enrollment::STATUS_ENROLLED,
        'academic_approved' => false,
    ]);

    Sanctum::actingAs($coordinator);

    // No puede emitir sin aprobación académica
    $this->postJson("/api/v1/coordinator/enrollments/{$enrollment->id}/certificate")
        ->assertUnprocessable();

    // Aprobar académicamente y emitir certificado
    $this->patchJson("/api/v1/coordinator/enrollments/{$enrollment->id}/academic", [
        'final_grade' => 18.5,
        'attendance_percentage' => 95,
        'academic_approved' => true,
    ])->assertOk();

    $issueRes = $this->postJson("/api/v1/coordinator/enrollments/{$enrollment->id}/certificate")
        ->assertCreated();

    $verificationUuid = $issueRes->json('data.verification_uuid');
    $certId = $issueRes->json('data.id');
    $cert = Certificate::query()->findOrFail($certId);
    Storage::disk('local')->assertExists((string) $cert->pdf_path);

    // Verificación pública QR
    $this->getJson("/api/v1/public/certificates/verify/{$verificationUuid}")
        ->assertOk()
        ->assertJsonPath('valid', true)
        ->assertJsonPath('status', 'valid')
        ->assertJsonPath('student_name', 'Lucía Ramírez')
        ->assertJsonPath('program_name', 'Excel Avanzado Corporativo');

    // Coordinador no puede revocar (solo Admin)
    $this->postJson("/api/v1/admin/certificates/{$certId}/revoke", [
        'reason' => 'Error de datos',
    ])->assertForbidden();

    // Admin revoca certificado
    Sanctum::actingAs($admin);
    $this->postJson("/api/v1/admin/certificates/{$certId}/revoke", [
        'reason' => 'Corrección de calificación solicitada por acta',
    ])->assertOk();

    // Verificación pública ahora muestra revocado
    $this->getJson("/api/v1/public/certificates/verify/{$verificationUuid}")
        ->assertOk()
        ->assertJsonPath('valid', false)
        ->assertJsonPath('status', 'revoked');
});
