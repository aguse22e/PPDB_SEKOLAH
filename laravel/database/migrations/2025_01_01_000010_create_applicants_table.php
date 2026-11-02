<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete(); // 1 akun = 1 siswa
            $table->string('full_name', 150);
            $table->string('nik', 20)->nullable()->unique();
            $table->string('nisn', 20)->nullable()->unique();
            $table->enum('gender', ['L', 'P']);
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('religion', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('rt_rw', 20)->nullable();
            $table->string('village', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('previous_school', 150)->nullable();
            $table->text('previous_school_address')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
