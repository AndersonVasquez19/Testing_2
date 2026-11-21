<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\EmitirCertificadoAction;
use App\Models\AdvisorySlot;
use App\Models\Cohort;
use App\Models\CohortSession;
use App\Models\Complaint;
use App\Models\ContactLead;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Program;
use App\Models\User;
use App\Services\CalculadoraDiasHabilesService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@horus.pe'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Administrador Horus',
                'password' => Hash::make('Horus2026!'),
                'dni' => '70000001',
                'phone' => '+51972021374',
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ]
        );
        $admin->syncRoles([User::ROLE_ADMIN]);

        $secretary = User::query()->updateOrCreate(
            ['email' => 'secretaria@horus.pe'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Valeria Secretaría',
                'password' => Hash::make('Horus2026!'),
                'dni' => '70000002',
                'phone' => '+51972021375',
                'role' => User::ROLE_SECRETARY,
                'is_active' => true,
            ]
        );
        $secretary->syncRoles([User::ROLE_SECRETARY]);

        $coordinator = User::query()->updateOrCreate(
            ['email' => 'coordinador@horus.pe'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Carlos Coordinador',
                'password' => Hash::make('Horus2026!'),
                'dni' => '70000003',
                'phone' => '+51972021376',
                'role' => User::ROLE_COORDINATOR,
                'is_active' => true,
            ]
        );
        $coordinator->syncRoles([User::ROLE_COORDINATOR]);

        $studentDemo = User::query()->updateOrCreate(
            ['email' => 'estudiante@horus.pe'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'María Torres Díaz',
                'password' => Hash::make('Horus2026!'),
                'dni' => '71234567',
                'phone' => '+51987654321',
                'role' => User::ROLE_STUDENT,
                'is_active' => true,
            ]
        );
        $studentDemo->syncRoles([User::ROLE_STUDENT]);

        $studentTwo = User::query()->updateOrCreate(
            ['email' => 'luis.quispe@horus.pe'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Luis Quispe Rojas',
                'password' => Hash::make('Horus2026!'),
                'dni' => '72345678',
                'phone' => '+51981112233',
                'role' => User::ROLE_STUDENT,
                'is_active' => true,
            ]
        );
        $studentTwo->syncRoles([User::ROLE_STUDENT]);

        $programsData = [
            [
                'code' => 'CUR-CCTV-01',
                'name' => 'Instalación y Configuración de Cámaras de Seguridad (CCTV e IP)',
                'slug' => 'camaras-seguridad-cctv-ip',
                'type' => Program::TYPE_COURSE,
                'description' => 'Diseña, cablea, monta y configura sistemas de videovigilancia analógicos e IP, visualización remota y configuración NVR/DVR.',
                'price' => '280.00',
                'duration_hours' => 40,
                'image_path' => '/images/galeria/nuestro servicio tecnico/imagen 5.jpg',
            ],
            [
                'code' => 'CUR-REDES-02',
                'name' => 'Cableado Estructurado y Redes de Datos (UTP / Fibra Óptica)',
                'slug' => 'cableado-estructurado-redes',
                'type' => Program::TYPE_COURSE,
                'description' => 'Fundamentos normativos ANSI/TIA/EIA, conectorización, armado de racks, patch panels y certificación de puntos de red.',
                'price' => '320.00',
                'duration_hours' => 36,
                'image_path' => '/images/galeria/nuestro servicio tecnico/imagen 7.jpg',
            ],
            [
                'code' => 'CUR-EXCEL-03',
                'name' => 'Ofimática Profesional y Excel Avanzado para Gestión',
                'slug' => 'ofimatica-excel-avanzado',
                'type' => Program::TYPE_COURSE,
                'description' => 'Domina tablas dinámicas, funciones financieras, macros y automatización de reportes corporativos.',
                'price' => '220.00',
                'duration_hours' => 60,
                'image_path' => '/images/capacitaciones/imagen 1.jpg',
            ],
            [
                'code' => 'CAP-AUX-04',
                'name' => 'Primeros Auxilios Básicos y Atención Prehospitalaria',
                'slug' => 'primeros-auxilios-atencion-prehospitalaria',
                'type' => Program::TYPE_TRAINING,
                'description' => 'Técnicas de RCP, inmovilización, vendajes, manejo de atragantamientos y primeros auxilios con respaldo de Colegio Profesional.',
                'price' => '180.00',
                'duration_hours' => 24,
                'image_path' => '/images/galeria/practicas de primeros auxilios/imagen 1.jpg',
            ],
            [
                'code' => 'ASE-EMP-05',
                'name' => 'Asesoramiento Educativo, Empresarial y Legal 1 a 1',
                'slug' => 'asesoramiento-personalizado-horus',
                'type' => Program::TYPE_ADVISORY,
                'description' => 'Sesiones personalizadas 1 a 1 con especialistas para proyectos académicos, tesis, emprendimientos y gestión normativa.',
                'price' => '120.00',
                'duration_hours' => 2,
                'image_path' => '/images/asesoramiento/imagen 1.jpg',
            ],
        ];

        $programs = [];
        foreach ($programsData as $item) {
            $programs[$item['code']] = Program::query()->updateOrCreate(
                ['code' => $item['code']],
                array_merge($item, [
                    'uuid' => (string) Str::uuid(),
                    'syllabus_json' => [
                        ['module' => 'Módulo 1: Fundamentos', 'hours' => 10, 'topics' => ['Marco teórico', 'Normativa vigente']],
                        ['module' => 'Módulo 2: Taller Práctico', 'hours' => 14, 'topics' => ['Laboratorio guiado', 'Casos reales']],
                    ],
                    'min_attendance_percentage' => '80.00',
                    'min_passing_grade' => '13.00',
                    'certification_label' => 'Certificado físico y digital verificable por QR',
                    'is_published' => true,
                    'status' => 'active',
                ])
            );
        }

        $cohortHybrid = Cohort::query()->updateOrCreate(
            ['code' => 'ED-2026-CCTV-01'],
            [
                'uuid' => (string) Str::uuid(),
                'program_id' => $programs['CUR-CCTV-01']->id,
                'coordinator_id' => $coordinator->id,
                'name' => 'Edición Noviembre 2026 · Híbrida',
                'modality' => Cohort::MODALITY_HYBRID,
                'whatsapp_group_link' => 'https://chat.whatsapp.com/HorusCctvGrupo2026',
                'default_meeting_url' => 'https://meet.google.com/horus-cctv-vivo',
                'location_name' => 'Sede Central Horus Group',
                'location_address' => 'Jr. José Gálvez 322, Cajamarca',
                'location_map_url' => 'https://maps.google.com/?q=Jr.+Jose+Galvez+322+Cajamarca',
                'start_date' => now()->addDays(3)->toDateString(),
                'end_date' => now()->addDays(30)->toDateString(),
                'max_capacity' => 25,
                'enrolled_count' => 2,
                'status' => 'open',
            ]
        );

        $cohortVirtual = Cohort::query()->updateOrCreate(
            ['code' => 'ED-2026-EXCEL-02'],
            [
                'uuid' => (string) Str::uuid(),
                'program_id' => $programs['CUR-EXCEL-03']->id,
                'coordinator_id' => $coordinator->id,
                'name' => 'Edición Diciembre 2026 · 100% Virtual',
                'modality' => Cohort::MODALITY_VIRTUAL,
                'whatsapp_group_link' => 'https://chat.whatsapp.com/HorusExcelGrupo2026',
                'default_meeting_url' => 'https://zoom.us/j/9988776655',
                'start_date' => now()->subDays(10)->toDateString(),
                'end_date' => now()->addDays(20)->toDateString(),
                'max_capacity' => 40,
                'enrolled_count' => 1,
                'status' => 'in_progress',
            ]
        );

        CohortSession::query()->updateOrCreate(
            ['cohort_id' => $cohortHybrid->id, 'session_number' => 1],
            [
                'uuid' => (string) Str::uuid(),
                'title' => 'Sesión 1: Arquitectura de Videovigilancia IP',
                'session_date' => now()->addDays(3)->toDateString(),
                'start_time' => '19:00:00',
                'end_time' => '21:00:00',
                'session_type' => CohortSession::TYPE_VIRTUAL,
                'meeting_url' => 'https://meet.google.com/horus-cctv-vivo',
                'status' => 'scheduled',
            ]
        );

        CohortSession::query()->updateOrCreate(
            ['cohort_id' => $cohortHybrid->id, 'session_number' => 2],
            [
                'uuid' => (string) Str::uuid(),
                'title' => 'Sesión 2: Laboratorio de Ponchado y Montaje NVR',
                'session_date' => now()->addDays(6)->toDateString(),
                'start_time' => '15:00:00',
                'end_time' => '19:00:00',
                'session_type' => CohortSession::TYPE_PRESENTIAL,
                'classroom' => 'Laboratorio Técnico 201',
                'location_name' => 'Sede Central Horus Group',
                'location_map_url' => 'https://maps.google.com/?q=Jr.+Jose+Galvez+322+Cajamarca',
                'status' => 'scheduled',
            ]
        );

        CohortSession::query()->updateOrCreate(
            ['cohort_id' => $cohortVirtual->id, 'session_number' => 1],
            [
                'uuid' => (string) Str::uuid(),
                'title' => 'Sesión 1: Tablas Dinámicas y Dashboards',
                'session_date' => now()->subDays(5)->toDateString(),
                'start_time' => '19:30:00',
                'end_time' => '21:30:00',
                'session_type' => CohortSession::TYPE_VIRTUAL,
                'meeting_url' => 'https://zoom.us/j/9988776655',
                'recording_url' => 'https://drive.google.com/file/d/horus-grabacion-demo',
                'status' => 'completed',
            ]
        );

        // Matrícula 1: Pagada, pendiente de añadir al grupo de WhatsApp (para la Cola de Secretaría)
        $enrollmentEnrolled = Enrollment::query()->updateOrCreate(
            ['user_id' => $studentDemo->id, 'cohort_id' => $cohortHybrid->id],
            [
                'uuid' => (string) Str::uuid(),
                'enrollment_status' => Enrollment::STATUS_ENROLLED,
                'reservation_reference' => 'HRS-DEMO01',
                'reservation_token' => 'demo-token-enrolled-00000000000000000001',
                'reservation_expires_at' => now()->addDays(2),
                'added_to_whatsapp' => false,
                'attendance_percentage' => '100.00',
                'final_grade' => '18.00',
                'academic_approved' => true,
            ]
        );

        Payment::query()->updateOrCreate(
            ['enrollment_id' => $enrollmentEnrolled->id, 'operation_code' => 'YAPE-881234'],
            [
                'uuid' => (string) Str::uuid(),
                'amount' => '280.00',
                'payment_method' => Payment::METHOD_YAPE,
                'status' => Payment::STATUS_VALIDATED,
                'validated_by' => $secretary->id,
                'validated_at' => now()->subDay(),
            ]
        );

        // Matrícula 2: Pago pendiente de validación por Secretaría
        $enrollmentPending = Enrollment::query()->updateOrCreate(
            ['user_id' => $studentTwo->id, 'cohort_id' => $cohortHybrid->id],
            [
                'uuid' => (string) Str::uuid(),
                'enrollment_status' => Enrollment::STATUS_PENDING_PAYMENT,
                'reservation_reference' => 'HRS-PEND02',
                'reservation_token' => 'demo-token-pending-000000000000000000002',
                'reservation_expires_at' => now()->addDays(2),
                'added_to_whatsapp' => false,
            ]
        );

        Payment::query()->updateOrCreate(
            ['enrollment_id' => $enrollmentPending->id, 'operation_code' => 'PLIN-445566'],
            [
                'uuid' => (string) Str::uuid(),
                'amount' => '280.00',
                'payment_method' => Payment::METHOD_PLIN,
                'status' => Payment::STATUS_PENDING,
            ]
        );

        // Matrícula 3: Completada con certificado QR emitido
        $enrollmentCert = Enrollment::query()->updateOrCreate(
            ['user_id' => $studentDemo->id, 'cohort_id' => $cohortVirtual->id],
            [
                'uuid' => (string) Str::uuid(),
                'enrollment_status' => Enrollment::STATUS_ENROLLED,
                'reservation_reference' => 'HRS-CERT03',
                'reservation_token' => 'demo-token-cert-00000000000000000000003',
                'reservation_expires_at' => now()->addDays(2),
                'added_to_whatsapp' => true,
                'whatsapp_added_by' => $secretary->id,
                'whatsapp_added_at' => now()->subDays(9),
                'attendance_percentage' => '95.00',
                'final_grade' => '18.50',
                'academic_approved' => true,
            ]
        );

        if ($enrollmentCert->activeCertificate === null) {
            app(EmitirCertificadoAction::class)->execute($enrollmentCert, $coordinator);
        }

        AdvisorySlot::query()->updateOrCreate(
            ['program_id' => $programs['ASE-EMP-05']->id, 'slot_date' => now()->addDays(4)->toDateString(), 'start_time' => '16:00:00'],
            [
                'uuid' => (string) Str::uuid(),
                'advisor_id' => $coordinator->id,
                'end_time' => '17:00:00',
                'modality' => 'virtual',
                'meeting_or_location' => 'https://meet.google.com/horus-asesoria-1a1',
                'is_booked' => false,
            ]
        );

        ContactLead::query()->updateOrCreate(
            ['email' => 'interesado@ejemplo.pe'],
            [
                'uuid' => (string) Str::uuid(),
                'full_name' => 'Andrea Mendoza',
                'phone' => '+51976543210',
                'program_id' => $programs['CUR-CCTV-01']->id,
                'subject' => 'Consulta por horarios presenciales',
                'message' => 'Quisiera saber si las prácticas de CCTV son los sábados por la tarde.',
                'source_path' => '/educacion/cursos',
                'status' => ContactLead::STATUS_NEW,
            ]
        );

        $deadline = app(CalculadoraDiasHabilesService::class)->sumarDiasHabiles(
            CarbonImmutable::now('America/Lima'),
            15
        );

        Complaint::query()->updateOrCreate(
            ['correlative_code' => 'REC-2026-0001'],
            [
                'uuid' => (string) Str::uuid(),
                'consumer_name' => 'Jorge Paredes Sánchez',
                'consumer_document_type' => 'dni',
                'consumer_document_number' => '45678912',
                'consumer_phone' => '+51971234567',
                'consumer_email' => 'jorge.paredes@ejemplo.pe',
                'consumer_address' => 'Av. Hoyos Rubio 450, Cajamarca',
                'is_minor' => false,
                'item_type' => 'service',
                'item_description' => 'Curso de Excel Avanzado',
                'claimed_amount' => '220.00',
                'complaint_type' => 'claim',
                'detail' => 'Solicito verificación de horario actualizado del módulo 2.',
                'consumer_request' => 'Confirmar reprogramación por correo.',
                'status' => Complaint::STATUS_REGISTERED,
                'legal_deadline' => $deadline->toDateString(),
            ]
        );
    }
}
