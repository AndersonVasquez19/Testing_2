<?php

declare(strict_types=1);

use App\Mail\ConstanciaReclamoMail;
use App\Models\Complaint;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
    Storage::fake('local');
    Mail::fake();
});

test('public complaint registration generates correlative code, 15 business day deadline, pdf receipt and email', function (): void {
    $response = $this->postJson('/api/v1/public/complaints', [
        'consumer_name' => 'Elena Castillo Vega',
        'consumer_document_type' => 'dni',
        'consumer_document_number' => '44556677',
        'consumer_phone' => '+51988776655',
        'consumer_email' => 'elena@ejemplo.pe',
        'consumer_address' => 'Jr. Amazonas 120, Cajamarca',
        'is_minor' => false,
        'item_type' => 'service',
        'item_description' => 'Curso de Cableado Estructurado',
        'claimed_amount' => '320.00',
        'complaint_type' => 'claim',
        'detail' => 'La fecha de inicio cambió sin notificación previa por correo.',
        'consumer_request' => 'Solicito confirmación de nueva fecha o devolución íntegra.',
    ]);

    $response->assertCreated();
    $code = $response->json('data.correlative_code');
    expect($code)->toMatch('/^REC-\d{4}-\d{4}$/');

    $complaint = Complaint::query()->where('correlative_code', $code)->firstOrFail();
    expect($complaint->receipt_pdf_path)->not->toBeNull();
    Storage::disk('local')->assertExists((string) $complaint->receipt_pdf_path);

    Mail::assertSent(ConstanciaReclamoMail::class, function (ConstanciaReclamoMail $mail): bool {
        return $mail->hasTo('elena@ejemplo.pe');
    });

    // Solo admin puede responder y cerrar oficialmente
    $secretary = User::factory()->withRole(User::ROLE_SECRETARY)->create();
    Sanctum::actingAs($secretary);
    $this->postJson("/api/v1/admin/complaints/{$complaint->id}/respond", [
        'company_response' => 'Hemos reprogramado y enviado el nuevo cronograma a su correo.',
        'status' => 'responded',
    ])->assertForbidden();

    $admin = User::factory()->withRole(User::ROLE_ADMIN)->create();
    Sanctum::actingAs($admin);
    $this->postJson("/api/v1/admin/complaints/{$complaint->id}/respond", [
        'company_response' => 'Hemos reprogramado y enviado el nuevo cronograma a su correo.',
        'status' => 'closed',
    ])->assertOk()
        ->assertJsonPath('data.status', 'closed');
});
