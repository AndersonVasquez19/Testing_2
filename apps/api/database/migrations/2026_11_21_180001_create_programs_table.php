<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('code', 30)->unique();
            $table->string('name', 180);
            $table->string('slug', 200)->unique();
            $table->string('type', 20)->index(); // course, training, advisory
            $table->text('description');
            $table->jsonb('syllabus_json')->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('duration_hours');
            $table->decimal('min_attendance_percentage', 5, 2)->default(80.00);
            $table->decimal('min_passing_grade', 5, 2)->default(13.00);
            $table->string('certification_label', 255)->nullable();
            $table->string('image_path', 255)->nullable();
            $table->boolean('is_published')->default(true)->index();
            $table->string('status', 20)->default('active')->index(); // active, inactive, archived
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE programs ADD CONSTRAINT programs_type_check CHECK (type IN ('course', 'training', 'advisory'))");
        DB::statement("ALTER TABLE programs ADD CONSTRAINT programs_status_check CHECK (status IN ('active', 'inactive', 'archived'))");
        DB::statement('ALTER TABLE programs ADD CONSTRAINT programs_price_check CHECK (price >= 0)');
        DB::statement('ALTER TABLE programs ADD CONSTRAINT programs_duration_check CHECK (duration_hours > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
