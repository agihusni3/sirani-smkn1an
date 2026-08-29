<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');
            DB::statement('CREATE TABLE "absensis_temp" ("id" integer primary key autoincrement not null, "pemilik_type" varchar check ("pemilik_type" in (\'siswa\', \'guru\')) not null, "pemilik_id" integer not null, "siswa_rombel_id" integer, "tanggal" date not null, "jam_masuk" time, "jam_pulang" time, "status" varchar check ("status" in (\'hadir\', \'terlambat\', \'alpha\', \'sakit\', \'izin\', \'bolos\')) not null default \'hadir\', "sumber_absen" varchar not null default \'rfid\', "created_at" datetime, "updated_at" datetime, foreign key("siswa_rombel_id") references "siswa_rombels"("id") on delete set null);');
            DB::statement('INSERT INTO "absensis_temp" SELECT * FROM "absensis";');
            DB::statement('DROP TABLE "absensis";');
            DB::statement('ALTER TABLE "absensis_temp" RENAME TO "absensis";');
            DB::statement('CREATE UNIQUE INDEX "absensis_pemilik_type_pemilik_id_tanggal_unique" ON "absensis" ("pemilik_type", "pemilik_id", "tanggal");');
            DB::statement('PRAGMA foreign_keys=ON;');
        }
    }

    public function down(): void
    {
    }
};
