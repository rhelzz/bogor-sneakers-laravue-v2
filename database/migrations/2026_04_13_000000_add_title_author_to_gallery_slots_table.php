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
        Schema::table('gallery_slots', function (Blueprint $table) {
            $table->string('title')->nullable()->after('image_path');
            $table->string('author')->nullable()->after('title');
        });

        $defaultTitles = [
            1 => 'Air Max 97 x Jogja Streets',
            2 => 'Samba OG - Bogor Vibe',
            3 => 'Jordan 1 Bred - Cold Day',
            4 => 'NB 574 Navy x Rain',
            5 => 'Vans Old Skool',
            6 => 'Converse Chuck 70',
            7 => 'Asics Gel-Kayano',
            8 => 'Puma RS-X Effekt',
        ];

        $rows = DB::table('gallery_slots')->select(['id', 'slot', 'title', 'author'])->get();

        foreach ($rows as $row) {
            $slot = (int) $row->slot;
            $updates = [];

            if (! is_string($row->title) || trim($row->title) === '') {
                $updates['title'] = $defaultTitles[$slot] ?? sprintf('Galeri Karya Slot %d', $slot);
            }

            if (! is_string($row->author) || trim($row->author) === '') {
                $updates['author'] = '@bogorsneakers';
            }

            if ($updates !== []) {
                DB::table('gallery_slots')->where('id', $row->id)->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gallery_slots', function (Blueprint $table) {
            $table->dropColumn(['title', 'author']);
        });
    }
};
