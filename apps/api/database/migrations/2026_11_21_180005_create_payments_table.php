<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('enrollment_id')->constrained('enrollments')->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 20); // yape, plin, transfer, gateway, cash
            $table->string('operation_code', 80)->nullable();
            $table->string('voucher_path', 500)->nullable();
            $table->string('voucher_hash', 64)->nullable()->index();
            $table->string('status', 20)->default('pending')->index(); // pending, validated, rejected, reversed
            $table->string('rejection_reason', 255)->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_method_check CHECK (payment_method IN ('yape', 'plin', 'transfer', 'gateway', 'cash'))");
        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_status_check CHECK (status IN ('pending', 'validated', 'rejected', 'reversed'))");
        DB::statement('ALTER TABLE payments ADD CONSTRAINT payments_amount_check CHECK (amount > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
