<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');
            DB::statement('CREATE TABLE "siswas_temp" ("id" integer primary key autoincrement not null, "nis" varchar not null, "nisn" varchar, "nama" varchar not null, "status" varchar check ("status" in (\'aktif\', \'pkl\', \'lulus\', \'pindah\', \'keluar\')) not null default \'aktif\', "foto" varchar, "created_at" datetime, "updated_at" datetime);');
            DB::statement('INSERT INTO "siswas_temp" SELECT * FROM "siswas";');
            DB::statement('DROP TABLE "siswas";');
            DB::statement('ALTER TABLE "siswas_temp" RENAME TO "siswas";');
            DB::statement('CREATE UNIQUE INDEX "siswas_nis_unique" ON "siswas" ("nis");');
            DB::statement('CREATE UNIQUE INDEX "siswas_nisn_unique" ON "siswas" ("nisn");');
            DB::statement('PRAGMA foreign_keys=ON;');
        }
    }

    public function down(): void
    {
    }
};
