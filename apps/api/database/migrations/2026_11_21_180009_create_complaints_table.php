<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('correlative_code', 20)->unique();
            $table->string('consumer_name', 180);
            $table->string('consumer_document_type', 20); // dni, ce, passport, ruc
            $table->string('consumer_document_number', 20);
            $table->string('consumer_phone', 30);
            $table->string('consumer_email', 254);
            $table->string('consumer_address', 255);
            $table->boolean('is_minor')->default(false);
            $table->string('representative_name', 180)->nullable();
            $table->string('representative_address', 255)->nullable();
            $table->string('representative_phone', 30)->nullable();
            $table->string('representative_email', 254)->nullable();
            $table->string('item_type', 20); // product, service
            $table->string('item_description', 500)->nullable();
            $table->decimal('claimed_amount', 12, 2)->nullable();
            $table->string('complaint_type', 20); // claim, complaint
            $table->text('detail');
            $table->text('consumer_request');
            $table->string('status', 20)->default('registered')->index(); // registered, in_review, responded, closed
            $table->date('legal_deadline')->index();
            $table->text('company_response')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('responded_at')->nullable();
            $table->string('receipt_pdf_path', 500)->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE complaints ADD CONSTRAINT complaints_doc_type_check CHECK (consumer_document_type IN ('dni', 'ce', 'passport', 'ruc'))");
        DB::statement("ALTER TABLE complaints ADD CONSTRAINT complaints_item_type_check CHECK (item_type IN ('product', 'service'))");
        DB::statement("ALTER TABLE complaints ADD CONSTRAINT complaints_type_check CHECK (complaint_type IN ('claim', 'complaint'))");
        DB::statement("ALTER TABLE complaints ADD CONSTRAINT complaints_status_check CHECK (status IN ('registered', 'in_review', 'responded', 'closed'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
