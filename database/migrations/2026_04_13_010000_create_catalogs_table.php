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
        Schema::create('catalogs', function (Blueprint $table) {
            $table->id();
            $table->ulid('public_id')->unique();
            $table->string('slug');
            $table->string('name', 160);
            $table->string('code', 80)->unique();
            $table->string('brand', 120);
            $table->string('collection', 120);
            $table->text('description')->nullable();
            $table->unsignedInteger('price')->default(0);
            $table->enum('status', ['ready', 'po', 'habis'])->default('ready');
            $table->unsignedSmallInteger('preorder_min_days')->nullable();
            $table->unsignedSmallInteger('preorder_max_days')->nullable();
            $table->string('discount_type', 24)->nullable();
            $table->unsignedInteger('discount_value')->nullable();
            $table->timestamp('discount_starts_at')->nullable();
            $table->timestamp('discount_ends_at')->nullable();
            $table->unsignedSmallInteger('popularity_score')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('slug');
            $table->index('brand');
            $table->index('collection');
            $table->index('price');
            $table->index('status');
            $table->index('is_active');
            $table->index('sort_order');
            $table->index('created_at');
            $table->index('popularity_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogs');
    }
};
