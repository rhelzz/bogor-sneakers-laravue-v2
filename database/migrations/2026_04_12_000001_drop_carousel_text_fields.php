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
        Schema::table('carousel_slides', function (Blueprint $table) {
            $table->dropColumn(['product', 'brand', 'price', 'title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carousel_slides', function (Blueprint $table) {
            $table->string('product')->after('id');
            $table->string('brand')->after('product');
            $table->string('price')->after('brand');
            $table->string('title')->after('price');
        });
    }
};
