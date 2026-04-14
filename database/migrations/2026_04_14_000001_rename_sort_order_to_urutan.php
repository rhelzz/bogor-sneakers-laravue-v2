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
        Schema::table('home_preorders', function (Blueprint $table) {
            $table->renameColumn('sort_order', 'urutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_preorders', function (Blueprint $table) {
            $table->renameColumn('urutan', 'sort_order');
        });
    }
};
