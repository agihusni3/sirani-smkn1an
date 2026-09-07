<?php

use Database\Seeders\PpdbSoalUjianSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Otomatis mempopulasikan 30 butir soal CBT dan 5 esai saat migrate dijalankan di VPS / server.
     */
    public function up(): void
    {
        try {
            $seeder = new PpdbSoalUjianSeeder();
            $seeder->run();
        } catch (\Throwable $e) {
            Artisan::call('db:seed', [
                '--class' => 'PpdbSoalUjianSeeder',
                '--force' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
