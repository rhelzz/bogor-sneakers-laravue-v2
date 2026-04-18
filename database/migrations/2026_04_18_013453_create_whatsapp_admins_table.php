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
        Schema::create('whatsapp_admins', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('role', 160);
            $table->string('phone', 20)->unique();
            $table->string('initial', 2)->nullable();
            $table->string('color', 24)->default('matcha');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_admins');
    }
};
