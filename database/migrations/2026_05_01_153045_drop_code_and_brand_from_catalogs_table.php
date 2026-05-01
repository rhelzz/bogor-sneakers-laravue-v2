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
        Schema::table('catalogs', function (Blueprint $table) {
            if (Schema::hasColumn('catalogs', 'brand')) {
                $table->dropIndex(['brand']);
                $table->dropColumn('brand');
            }
            if (Schema::hasColumn('catalogs', 'code')) {
                $table->dropColumn('code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->string('code', 80)->nullable()->unique();
            $table->string('brand', 120)->nullable();
            $table->index('brand');
        });
    }
};
