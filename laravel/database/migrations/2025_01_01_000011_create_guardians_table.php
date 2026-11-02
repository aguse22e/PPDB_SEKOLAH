<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('applicants')->cascadeOnDelete();
            $table->enum('relationship', ['ayah', 'ibu', 'wali']);
            $table->string('name', 150);
            $table->string('nik', 20)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('education', 50)->nullable();
            $table->string('occupation', 100)->nullable();
            $table->string('income_range', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_emergency_contact')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
