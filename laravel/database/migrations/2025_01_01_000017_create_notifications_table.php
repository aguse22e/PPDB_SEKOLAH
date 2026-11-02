<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            // CATATAN: Laravel punya fitur notifikasi bawaan dengan tabel bernama sama.
            // Jika proyekmu sudah memakai notifikasi bawaan Laravel, ganti nama tabel ini
            // menjadi 'app_notifications' di migration ini dan di Model-nya.
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('message');
            $table->enum('channel', ['app', 'email', 'whatsapp', 'sms'])->default('app');
            $table->boolean('is_read')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
