<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohorts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code', 40)->unique();
            $table->foreignId('program_id')->constrained('programs')->restrictOnDelete();
            $table->foreignId('coordinator_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('name', 180);
            $table->string('modality', 20)->index(); // virtual, presential, hybrid
            $table->string('whatsapp_group_link', 255)->nullable();
            $table->string('default_meeting_url', 500)->nullable();
            $table->string('location_name', 180)->nullable();
            $table->string('location_address', 255)->nullable();
            $table->string('location_map_url', 500)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('max_capacity');
            $table->integer('enrolled_count')->default(0);
            $table->string('status', 20)->default('open')->index(); // draft, open, in_progress, closed, cancelled
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE cohorts ADD CONSTRAINT cohorts_modality_check CHECK (modality IN ('virtual', 'presential', 'hybrid'))");
        DB::statement("ALTER TABLE cohorts ADD CONSTRAINT cohorts_status_check CHECK (status IN ('draft', 'open', 'in_progress', 'closed', 'cancelled'))");
        DB::statement('ALTER TABLE cohorts ADD CONSTRAINT cohorts_dates_check CHECK (end_date >= start_date)');
        DB::statement('ALTER TABLE cohorts ADD CONSTRAINT cohorts_capacity_check CHECK (max_capacity > 0 AND enrolled_count >= 0 AND enrolled_count <= max_capacity)');
    }

    public function down(): void
    {
        Schema::dropIfExists('cohorts');
    }
};
