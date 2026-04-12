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
        Schema::create('carousel_slides', function (Blueprint $table) {
            $table->id();
            $table->string('product'); // Product name
            $table->string('brand'); // Brand name
            $table->string('price'); // Price string (e.g., "Rp 1.850.000")
            $table->string('title'); // Slide title
            $table->string('image_path'); // Path to image file in storage
            $table->boolean('is_active')->default(true); // Whether slide is displayed on frontend
            $table->integer('order')->default(0); // Order for sorting slides
            $table->timestamps(); // created_at, updated_at

            // Index for faster queries
            $table->index('is_active');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carousel_slides');
    }
};
