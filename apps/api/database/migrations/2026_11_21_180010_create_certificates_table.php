<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('enrollment_id')->constrained('enrollments')->restrictOnDelete();
            $table->uuid('verification_uuid')->unique();
            $table->string('certificate_code', 30)->unique();
            $table->string('pdf_path', 500)->nullable();
            $table->text('qr_payload');
            $table->jsonb('snapshot_data');
            $table->foreignId('issued_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('issued_at');
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('revocation_reason', 255)->nullable();
            $table->timestamps();
        });

        DB::statement('CREATE UNIQUE INDEX certificates_active_enrollment_unique ON certificates (enrollment_id) WHERE revoked_at IS NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
