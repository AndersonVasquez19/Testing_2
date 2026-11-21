<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Admin\AdminController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Coordinador\CoordinatorController;
use App\Http\Controllers\Api\V1\Estudiante\StudentController;
use App\Http\Controllers\Api\V1\Publico\CatalogController;
use App\Http\Controllers\Api\V1\Publico\CertificateVerificationController;
use App\Http\Controllers\Api\V1\Publico\ComplaintController;
use App\Http\Controllers\Api\V1\Publico\ContactLeadController;
use App\Http\Controllers\Api\V1\Publico\ReservationController;
use App\Http\Controllers\Api\V1\Secretaria\SecretaryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    // Autenticación
    Route::prefix('auth')->group(function (): void {
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:15,1');
        Route::post('register', [AuthController::class, 'register'])->middleware('throttle:10,1');

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('logout', [AuthController::class, 'logout']);
        });
    });

    // Público
    Route::prefix('public')->group(function (): void {
        Route::get('programs', [CatalogController::class, 'index']);
        Route::get('programs/{slug}', [CatalogController::class, 'show']);
        Route::post('contact-leads', [ContactLeadController::class, 'store'])->middleware('throttle:15,1');
        Route::post('complaints', [ComplaintController::class, 'store'])->middleware('throttle:10,1');
        Route::get('complaints/{correlativeCode}/receipt', [ComplaintController::class, 'receipt']);
        Route::get('certificates/verify/{uuid}', [CertificateVerificationController::class, 'show'])->middleware('throttle:30,1');
        Route::get('reservations/{token}', [ReservationController::class, 'show']);
        Route::post('reservations/{token}/voucher', [ReservationController::class, 'submitVoucher'])->middleware('throttle:10,1');
    });

    // Secretaría (+ Admin)
    Route::prefix('secretary')
        ->middleware(['auth:sanctum', 'role:admin,secretary'])
        ->group(function (): void {
            Route::get('dashboard', [SecretaryController::class, 'dashboard']);
            Route::get('leads', [SecretaryController::class, 'leads']);
            Route::patch('leads/{lead}', [SecretaryController::class, 'updateLead']);
            Route::get('students', [SecretaryController::class, 'students']);
            Route::post('students', [SecretaryController::class, 'storeStudent']);
            Route::get('cohorts', [SecretaryController::class, 'cohorts']);
            Route::get('enrollments', [SecretaryController::class, 'enrollments']);
            Route::post('enrollments', [SecretaryController::class, 'storeEnrollment']);
            Route::get('payments', [SecretaryController::class, 'payments']);
            Route::post('payments', [SecretaryController::class, 'storePayment']);
            Route::post('payments/{payment}/approve', [SecretaryController::class, 'approvePayment']);
            Route::post('payments/{payment}/reject', [SecretaryController::class, 'rejectPayment']);
            Route::get('whatsapp-queue', [SecretaryController::class, 'whatsappQueue']);
            Route::patch('whatsapp-queue/{enrollment}/mark-added', [SecretaryController::class, 'markAddedToWhatsapp']);
        });

    // Coordinador (+ Admin)
    Route::prefix('coordinator')
        ->middleware(['auth:sanctum', 'role:admin,coordinator'])
        ->group(function (): void {
            Route::get('cohorts', [CoordinatorController::class, 'cohorts']);
            Route::post('cohorts', [CoordinatorController::class, 'storeCohort']);
            Route::patch('cohorts/{cohort}', [CoordinatorController::class, 'updateCohort']);
            Route::get('cohorts/{cohort}/sessions', [CoordinatorController::class, 'sessions']);
            Route::post('cohorts/{cohort}/sessions', [CoordinatorController::class, 'storeSession']);
            Route::patch('sessions/{session}', [CoordinatorController::class, 'updateSession']);
            Route::post('sessions/{session}/attendance', [CoordinatorController::class, 'recordAttendance']);
            Route::get('cohorts/{cohort}/enrollments', [CoordinatorController::class, 'cohortEnrollments']);
            Route::patch('enrollments/{enrollment}/academic', [CoordinatorController::class, 'updateAcademicResult']);
            Route::post('enrollments/{enrollment}/certificate', [CoordinatorController::class, 'issueCertificate']);
            Route::get('advisory-slots', [CoordinatorController::class, 'advisorySlots']);
            Route::post('advisory-slots', [CoordinatorController::class, 'storeAdvisorySlot']);
        });

    // Administrador
    Route::prefix('admin')
        ->middleware(['auth:sanctum', 'role:admin'])
        ->group(function (): void {
            Route::get('metrics', [AdminController::class, 'metrics']);
            Route::get('users', [AdminController::class, 'users']);
            Route::post('users', [AdminController::class, 'storeUser']);
            Route::patch('users/{user}', [AdminController::class, 'updateUser']);
            Route::get('programs', [AdminController::class, 'programs']);
            Route::post('programs', [AdminController::class, 'storeProgram']);
            Route::patch('programs/{program}', [AdminController::class, 'updateProgram']);
            Route::get('complaints', [AdminController::class, 'complaints']);
            Route::post('complaints/{complaint}/respond', [AdminController::class, 'respondComplaint']);
            Route::get('certificates', [AdminController::class, 'certificates']);
            Route::post('certificates/{certificate}/revoke', [AdminController::class, 'revokeCertificate']);
            Route::get('activity-logs', [AdminController::class, 'activityLogs']);
        });

    // Estudiante (+ Admin)
    Route::prefix('student')
        ->middleware(['auth:sanctum', 'role:student,admin'])
        ->group(function (): void {
            Route::get('enrollments', [StudentController::class, 'enrollments']);
            Route::post('enrollments', [StudentController::class, 'enroll']);
            Route::post('enrollments/{enrollment}/payments', [StudentController::class, 'submitPayment']);
            Route::get('advisories', [StudentController::class, 'advisories']);
            Route::post('advisories/{slot}/book', [StudentController::class, 'bookAdvisory']);
            Route::get('certificates/{certificate}/download', [StudentController::class, 'downloadCertificate']);
        });
});
