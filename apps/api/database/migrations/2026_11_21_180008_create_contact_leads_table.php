<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_leads', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('full_name', 150);
            $table->string('email', 180);
            $table->string('phone', 30);
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->string('subject', 180)->nullable();
            $table->text('message');
            $table->string('source_path', 255)->nullable();
            $table->string('status', 20)->default('new')->index(); // new, contacted, qualified, converted, discarded
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE contact_leads ADD CONSTRAINT contact_leads_status_check CHECK (status IN ('new', 'contacted', 'qualified', 'converted', 'discarded'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_leads');
    }
};
