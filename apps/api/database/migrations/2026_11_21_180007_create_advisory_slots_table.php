<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advisory_slots', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('program_id')->constrained('programs')->restrictOnDelete();
            $table->foreignId('advisor_id')->constrained('users')->restrictOnDelete();
            $table->date('slot_date')->index();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('modality', 20); // virtual, presential
            $table->string('meeting_or_location', 500)->nullable();
            $table->boolean('is_booked')->default(false)->index();
            $table->foreignId('student_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->string('topic', 255)->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE advisory_slots ADD CONSTRAINT advisory_slots_modality_check CHECK (modality IN ('virtual', 'presential'))");
        DB::statement('ALTER TABLE advisory_slots ADD CONSTRAINT advisory_slots_time_check CHECK (end_time > start_time)');
    }

    public function down(): void
    {
        Schema::dropIfExists('advisory_slots');
    }
};
