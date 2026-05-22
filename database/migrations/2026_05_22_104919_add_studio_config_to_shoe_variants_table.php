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
        Schema::table('shoe_variants', function (Blueprint $table) {
            $table->json('studio_config')->nullable()->after('shoe_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shoe_variants', function (Blueprint $table) {
            $table->dropColumn('studio_config');
        });
    }
};
