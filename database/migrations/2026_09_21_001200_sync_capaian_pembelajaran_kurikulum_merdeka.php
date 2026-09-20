<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\AkademikCapaianPembelajaranSeeder;

return new class extends Migration
{
    /**
     * Run the migrations to seed/sync official Kurikulum Merdeka Capaian Pembelajaran.
     */
    public function up(): void
    {
        (new AkademikCapaianPembelajaranSeeder())->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
