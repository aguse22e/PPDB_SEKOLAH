<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('applicants')->cascadeOnDelete();
            $table->foreignId('period_id')->constrained('admission_periods');
            $table->foreignId('path_id')->constrained('admission_paths');
            $table->foreignId('major_id')->nullable()->constrained('majors');
            $table->foreignId('major_choice_2_id')->nullable()->constrained('majors');
            $table->string('registration_no', 30)->unique(); // PPDB-2027-0001
            $table->enum('status', ['draft','submitted','document_review','verified','rejected_document','selection','accepted','waitlisted','rejected','registered','withdrawn'])->default('draft')->index();
            $table->text('notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->index('period_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
