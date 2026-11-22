<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            // Pemilik absensi: siswa (App\Models\Applicant) atau guru (App\Models\User).
            $table->string('attendee_type', 100);
            $table->unsignedBigInteger('attendee_id');
            $table->date('date')->index();
            $table->timestamp('check_in_at')->nullable(); // absen masuk
            $table->timestamp('check_out_at')->nullable(); // absen pulang
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpa'])->default('hadir')->index();
            $table->enum('method', ['barcode', 'manual'])->default('barcode');
            $table->foreignId('scanned_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Satu orang hanya punya satu baris absensi per hari.
            $table->unique(['attendee_type', 'attendee_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
