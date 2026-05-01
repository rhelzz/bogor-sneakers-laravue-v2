<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shoe_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shoe_model_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('full_preview_path');
            $table->json('svg_data');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shoe_variants');
    }
};
