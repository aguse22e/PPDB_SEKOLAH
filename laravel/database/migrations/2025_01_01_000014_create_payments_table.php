<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->enum('payment_type', ['formulir', 'tes', 'daftar_ulang', 'lainnya'])->default('formulir');
            $table->string('invoice_no', 30)->unique(); // INV-2027-0001
            $table->decimal('amount', 12, 2);
            $table->enum('method', ['transfer', 'qris', 'tunai', 'va', 'ewallet'])->nullable();
            $table->string('reference_no', 100)->nullable();
            $table->string('proof_path')->nullable();
            $table->enum('status', ['pending','waiting_verification','paid','failed','expired','refunded'])->default('pending')->index();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
