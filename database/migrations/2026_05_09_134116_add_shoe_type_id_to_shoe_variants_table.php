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
            $table->foreignId('shoe_type_id')->nullable()->after('shoe_model_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shoe_variants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shoe_type_id');
        });
    }
};
