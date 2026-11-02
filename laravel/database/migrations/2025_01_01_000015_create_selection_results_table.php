<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('selection_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->string('stage', 50); // tes_tulis, wawancara, seleksi_berkas, prestasi
            $table->string('subject', 100)->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->decimal('max_score', 5, 2)->nullable()->default(100);
            $table->unsignedInteger('rank')->nullable();
            $table->enum('result_status', ['pending', 'passed', 'failed', 'waitlisted'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('assessed_by')->nullable()->constrained('users');
            $table->timestamp('assessed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('selection_results');
    }
};
