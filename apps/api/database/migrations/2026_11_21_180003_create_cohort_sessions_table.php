<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohort_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('cohort_id')->constrained('cohorts')->restrictOnDelete();
            $table->smallInteger('session_number');
            $table->string('title', 200);
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('session_type', 20); // virtual, presential
            $table->string('meeting_url', 500)->nullable();
            $table->string('classroom', 120)->nullable();
            $table->string('location_name', 180)->nullable();
            $table->string('location_map_url', 500)->nullable();
            $table->string('recording_url', 500)->nullable();
            $table->string('material_pdf_path', 500)->nullable();
            $table->string('status', 20)->default('scheduled')->index(); // scheduled, completed, cancelled
            $table->timestamps();

            $table->unique(['cohort_id', 'session_number']);
        });

        DB::statement("ALTER TABLE cohort_sessions ADD CONSTRAINT cohort_sessions_type_check CHECK (session_type IN ('virtual', 'presential'))");
        DB::statement("ALTER TABLE cohort_sessions ADD CONSTRAINT cohort_sessions_status_check CHECK (status IN ('scheduled', 'completed', 'cancelled'))");
        DB::statement('ALTER TABLE cohort_sessions ADD CONSTRAINT cohort_sessions_time_check CHECK (end_time > start_time)');
    }

    public function down(): void
    {
        Schema::dropIfExists('cohort_sessions');
    }
};
