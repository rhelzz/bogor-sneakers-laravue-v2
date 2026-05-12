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
        Schema::create('carts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('user_agent')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('cart_id')->constrained('carts')->onDelete('cascade');
            $table->foreignId('catalog_id')->constrained('catalogs')->onDelete('cascade');
            $table->foreignId('catalog_size_id')->constrained('catalog_sizes')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->integer('price_at_time'); // Snapshot price in IDR
            $table->timestamps();

            // Ensure one product-size combination per cart
            $table->unique(['cart_id', 'catalog_size_id']);
            $table->index('cart_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
