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
        Schema::create('home_preorders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_id')->constrained('catalogs')->cascadeOnDelete();
            $table->enum('status', ['buka', 'hampir_habis', 'habis'])->default('buka');
            $table->boolean('is_visible')->default(false);
            $table->unsignedSmallInteger('max_slots')->default(1);
            $table->unsignedSmallInteger('filled_slots')->default(0);
            $table->timestamp('countdown_ends_at')->nullable();
            $table->string('batch_label', 40)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique('catalog_id');
            $table->index('is_visible');
            $table->index('status');
            $table->index('countdown_ends_at');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_preorders');
    }
};
