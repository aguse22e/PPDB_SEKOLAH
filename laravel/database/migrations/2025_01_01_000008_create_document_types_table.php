<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique(); // kk, akta, ijazah, rapor, foto, kip
            $table->string('name', 100);
            $table->string('allowed_extensions', 100)->default('pdf,jpg,jpeg,png');
            $table->unsignedInteger('max_size_mb')->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
