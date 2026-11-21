<?php

declare(strict_types=1);

use App\Models\Cohort;
use App\Models\CohortSession;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('coordinator switches session modality between virtual and presential and records attendance', function (): void {
    $coordinator = User::factory()->withRole(User::ROLE_COORDINATOR)->create();
    $student = User::factory()->withRole(User::ROLE_STUDENT)->create();
    $program = Program::factory()->create();

    Sanctum::actingAs($coordinator);

    $cohortRes = $this->postJson('/api/v1/coordinator/cohorts', [
        'program_id' => $program->id,
        'code' => 'ED-HYB-2026',
        'name' => 'Edición Híbrida Test',
        'modality' => Cohort::MODALITY_HYBRID,
        'whatsapp_group_link' => 'https://chat.whatsapp.com/TestGrupoHibrido',
        'default_meeting_url' => 'https://meet.google.com/horus-test-meet',
        'location_name' => 'Sede Central',
        'location_address' => 'Jr. José Gálvez 322',
        'start_date' => '2026-12-01',
        'end_date' => '2026-12-20',
        'max_capacity' => 20,
        'status' => 'open',
    ])->assertCreated();

    $cohortId = $cohortRes->json('data.id');

    $sessionRes = $this->postJson("/api/v1/coordinator/cohorts/{$cohortId}/sessions", [
        'session_number' => 1,
        'title' => 'Clase Inaugural',
        'session_date' => '2026-12-01',
        'start_time' => '18:00',
        'end_time' => '20:00',
        'session_type' => CohortSession::TYPE_VIRTUAL,
        'meeting_url' => 'https://meet.google.com/horus-test-meet',
    ])->assertCreated();

    $sessionId = $sessionRes->json('data.id');

    // Cambiar sesión específica a presencial con aula
    $this->patchJson("/api/v1/coordinator/sessions/{$sessionId}", [
        'session_type' => CohortSession::TYPE_PRESENTIAL,
        'classroom' => 'Aula Magna 301',
        'location_name' => 'Sede Central Cajamarca',
        'location_map_url' => 'https://maps.google.com/?q=Cajamarca',
    ])->assertOk()
        ->assertJsonPath('data.session_type', CohortSession::TYPE_PRESENTIAL)
        ->assertJsonPath('data.classroom', 'Aula Magna 301');

    // Cambiar nuevamente a virtual con grabación
    $this->patchJson("/api/v1/coordinator/sessions/{$sessionId}", [
        'session_type' => CohortSession::TYPE_VIRTUAL,
        'meeting_url' => 'https://zoom.us/j/123456789',
        'recording_url' => 'https://drive.google.com/file/d/test-rec',
    ])->assertOk()
        ->assertJsonPath('data.session_type', CohortSession::TYPE_VIRTUAL)
        ->assertJsonPath('data.recording_url', 'https://drive.google.com/file/d/test-rec');

    $enrollment = Enrollment::factory()->create([
        'user_id' => $student->id,
        'cohort_id' => $cohortId,
        'enrollment_status' => Enrollment::STATUS_ENROLLED,
    ]);

    $this->postJson("/api/v1/coordinator/sessions/{$sessionId}/attendance", [
        'records' => [
            ['enrollment_id' => $enrollment->id, 'status' => 'present'],
        ],
    ])->assertOk();

    expect((float) $enrollment->fresh()->attendance_percentage)->toBe(100.0);

    // Estudiante con pago validado ve enlace de WhatsApp y sesiones en su portal
    Sanctum::actingAs($student);
    $this->getJson('/api/v1/student/enrollments')
        ->assertOk()
        ->assertJsonPath('data.0.whatsapp_group_link', 'https://chat.whatsapp.com/TestGrupoHibrido');
});
