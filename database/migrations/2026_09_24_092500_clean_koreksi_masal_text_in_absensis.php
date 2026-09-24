<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("UPDATE absensis SET keterangan = REPLACE(keterangan, 'Koreksi Masal ', 'Koreksi ') WHERE keterangan LIKE '%Koreksi Masal%'");
        DB::statement("UPDATE notifikasi_ortus SET pesan = REPLACE(pesan, 'Koreksi Masal ', 'Koreksi ') WHERE pesan LIKE '%Koreksi Masal%'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
