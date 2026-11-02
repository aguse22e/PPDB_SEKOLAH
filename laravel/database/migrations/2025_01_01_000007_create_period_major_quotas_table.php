<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('period_major_quotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('admission_periods')->cascadeOnDelete();
            $table->foreignId('major_id')->constrained('majors')->cascadeOnDelete();
            $table->unsignedInteger('quota')->default(0);
            $table->timestamps();
            $table->unique(['period_id', 'major_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('period_major_quotas');
    }
};
