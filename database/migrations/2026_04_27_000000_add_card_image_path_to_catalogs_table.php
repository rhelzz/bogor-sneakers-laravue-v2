<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->string('card_image_path')->nullable()->after('collection');
        });

        $catalogIds = DB::table('catalogs')->pluck('id');

        foreach ($catalogIds as $catalogId) {
            $firstImagePath = DB::table('catalog_images')
                ->where('catalog_id', (int) $catalogId)
                ->orderBy('position')
                ->orderBy('id')
                ->value('image_path');

            if (is_string($firstImagePath) && $firstImagePath !== '') {
                DB::table('catalogs')
                    ->where('id', (int) $catalogId)
                    ->update(['card_image_path' => $firstImagePath]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->dropColumn('card_image_path');
        });
    }
};
