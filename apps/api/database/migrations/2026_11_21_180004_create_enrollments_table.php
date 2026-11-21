<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('cohort_id')->constrained('cohorts')->restrictOnDelete();
            $table->string('enrollment_status', 24)->default('pending_payment')->index(); // pending_payment, enrolled, completed, cancelled
            $table->string('reservation_reference', 30)->nullable()->unique();
            $table->string('reservation_token', 64)->nullable()->unique();
            $table->timestamp('reservation_expires_at')->nullable();
            $table->boolean('added_to_whatsapp')->default(false)->index();
            $table->foreignId('whatsapp_added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('whatsapp_added_at')->nullable();
            $table->decimal('final_grade', 5, 2)->nullable();
            $table->decimal('attendance_percentage', 5, 2)->nullable();
            $table->boolean('academic_approved')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE enrollments ADD CONSTRAINT enrollments_status_check CHECK (enrollment_status IN ('pending_payment', 'enrolled', 'completed', 'cancelled'))");
        DB::statement("CREATE UNIQUE INDEX enrollments_active_user_cohort_unique ON enrollments (user_id, cohort_id) WHERE deleted_at IS NULL AND enrollment_status <> 'cancelled'");
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
