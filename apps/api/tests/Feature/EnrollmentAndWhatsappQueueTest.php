<?php

declare(strict_types=1);

use App\Models\ActivityLog;
use App\Models\Cohort;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Program;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('secretary validates payment, enrolls student, and marks added to whatsapp queue with audit log', function (): void {
    $secretary = User::factory()->withRole(User::ROLE_SECRETARY)->create();
    $student = User::factory()->withRole(User::ROLE_STUDENT)->create(['phone' => '987654321']);
    $program = Program::factory()->create(['price' => '280.00']);
    $cohort = Cohort::factory()->create([
        'program_id' => $program->id,
        'max_capacity' => 10,
        'enrolled_count' => 0,
        'whatsapp_group_link' => 'https://chat.whatsapp.com/TestGrupoHorus123',
    ]);

    Sanctum::actingAs($secretary);

    // 1. Secretaría registra matrícula con reserva de cupo
    $enrollResponse = $this->postJson('/api/v1/secretary/enrollments', [
        'user_id' => $student->id,
        'cohort_id' => $cohort->id,
    ]);
    $enrollResponse->assertCreated();
    $enrollmentId = $enrollResponse->json('data.id');

    expect($cohort->fresh()->enrolled_count)->toBe(1);
    expect(Enrollment::query()->find($enrollmentId)?->enrollment_status)->toBe(Enrollment::STATUS_PENDING_PAYMENT);

    // 2. Se registra comprobante de pago por Yape
    $paymentResponse = $this->postJson('/api/v1/secretary/payments', [
        'enrollment_id' => $enrollmentId,
        'amount' => '280.00',
        'payment_method' => Payment::METHOD_YAPE,
        'operation_code' => 'YAPE-998877',
    ]);
    $paymentResponse->assertCreated();
    $paymentId = $paymentResponse->json('data.id');

    // 3. Secretaría aprueba el pago -> pasa a enrolled y entra a la Cola de WhatsApp
    $approveResponse = $this->postJson("/api/v1/secretary/payments/{$paymentId}/approve");
    $approveResponse->assertOk();

    $enrollment = Enrollment::query()->findOrFail($enrollmentId);
    expect($enrollment->enrollment_status)->toBe(Enrollment::STATUS_ENROLLED);
    expect($enrollment->added_to_whatsapp)->toBeFalse();

    // 4. Aparece en la Cola de WhatsApp pendiente
    $queueResponse = $this->getJson('/api/v1/secretary/whatsapp-queue?added_to_whatsapp=0');
    $queueResponse->assertOk()
        ->assertJsonFragment(['id' => $enrollmentId])
        ->assertJsonFragment(['whatsapp_group_link' => 'https://chat.whatsapp.com/TestGrupoHorus123']);

    // 5. Secretaría marca el check de auditoría added_to_whatsapp = true
    $markResponse = $this->patchJson("/api/v1/secretary/whatsapp-queue/{$enrollmentId}/mark-added", [
        'added' => true,
    ]);
    $markResponse->assertOk()
        ->assertJsonPath('data.added_to_whatsapp', true);

    $enrollment->refresh();
    expect($enrollment->added_to_whatsapp)->toBeTrue();
    expect($enrollment->whatsapp_added_by)->toBe($secretary->id);
    expect($enrollment->whatsapp_added_at)->not->toBeNull();

    expect(ActivityLog::query()->where('action', 'payment_validated')->exists())->toBeTrue();
    expect(ActivityLog::query()->where('action', 'enrollment_whatsapp_added')->exists())->toBeTrue();
});

test('non-secretary roles cannot access secretary whatsapp queue or approve payments', function (): void {
    $student = User::factory()->withRole(User::ROLE_STUDENT)->create();
    $coordinator = User::factory()->withRole(User::ROLE_COORDINATOR)->create();
    $payment = Payment::factory()->create();

    Sanctum::actingAs($student);
    $this->getJson('/api/v1/secretary/whatsapp-queue')->assertForbidden();
    $this->postJson("/api/v1/secretary/payments/{$payment->id}/approve")->assertForbidden();

    Sanctum::actingAs($coordinator);
    $this->getJson('/api/v1/secretary/whatsapp-queue')->assertForbidden();
    $this->postJson("/api/v1/secretary/payments/{$payment->id}/approve")->assertForbidden();
});
