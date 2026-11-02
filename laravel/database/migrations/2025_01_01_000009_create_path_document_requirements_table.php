<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('path_document_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('path_id')->constrained('admission_paths')->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained('document_types')->cascadeOnDelete();
            $table->boolean('is_required')->default(true);
            $table->timestamps();
            $table->unique(['path_id', 'document_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('path_document_requirements');
    }
};
