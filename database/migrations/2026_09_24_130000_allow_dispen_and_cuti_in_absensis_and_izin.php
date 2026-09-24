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
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `absensis` MODIFY COLUMN `status` VARCHAR(32) NOT NULL DEFAULT 'hadir'");
            DB::statement("ALTER TABLE `izin_siswas` MODIFY COLUMN `jenis` VARCHAR(32) NOT NULL DEFAULT 'izin'");
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');

            // 1. Perbarui tabel absensis agar status menerima dispen, dispensasi, cuti, dinas_luar tanpa restriksi check constraint
            DB::statement('CREATE TABLE "absensis_temp" (
                "id" integer primary key autoincrement not null,
                "pemilik_type" varchar check ("pemilik_type" in (\'siswa\', \'guru\')) not null,
                "pemilik_id" integer not null,
                "siswa_rombel_id" integer,
                "tanggal" date not null,
                "jam_masuk" time,
                "jam_pulang" time,
                "status" varchar not null default \'hadir\',
                "sumber_absen" varchar not null default \'rfid\',
                "created_at" datetime,
                "updated_at" datetime,
                "keterangan" text,
                foreign key("siswa_rombel_id") references "siswa_rombels"("id") on delete set null
            );');

            DB::statement('INSERT INTO "absensis_temp" ("id", "pemilik_type", "pemilik_id", "siswa_rombel_id", "tanggal", "jam_masuk", "jam_pulang", "status", "sumber_absen", "created_at", "updated_at", "keterangan")
                SELECT "id", "pemilik_type", "pemilik_id", "siswa_rombel_id", "tanggal", "jam_masuk", "jam_pulang", "status", "sumber_absen", "created_at", "updated_at", "keterangan" FROM "absensis";');

            DB::statement('DROP TABLE "absensis";');
            DB::statement('ALTER TABLE "absensis_temp" RENAME TO "absensis";');

            DB::statement('CREATE UNIQUE INDEX "absensis_pemilik_type_pemilik_id_tanggal_unique" ON "absensis" ("pemilik_type", "pemilik_id", "tanggal");');
            DB::statement('CREATE INDEX "idx_absensis_tanggal" ON "absensis"("tanggal");');
            DB::statement('CREATE INDEX "idx_absensis_type_tgl_status" ON "absensis"("pemilik_type", "tanggal", "status");');
            DB::statement('CREATE INDEX "idx_absensis_type_status_id" ON "absensis"("pemilik_type", "status", "pemilik_id");');
            DB::statement('CREATE INDEX "idx_absensis_type_id_tgl" ON "absensis"("pemilik_type", "pemilik_id", "tanggal");');

            // 2. Perbarui tabel izin_siswas agar jenis menerima dispen dan dispensasi tanpa restriksi check constraint
            DB::statement('CREATE TABLE "izin_siswas_temp" (
                "id" integer primary key autoincrement not null,
                "siswa_id" integer not null,
                "tanggal" date not null,
                "jenis" varchar not null default \'izin\',
                "status" varchar check ("status" in (\'pending\', \'disetujui\', \'ditolak\')) not null default \'disetujui\',
                "keterangan" text,
                "created_at" datetime,
                "updated_at" datetime,
                "disetujui_oleh" varchar,
                "file_pendukung" varchar,
                foreign key("siswa_id") references "siswas"("id") on delete cascade
            );');

            DB::statement('INSERT INTO "izin_siswas_temp" ("id", "siswa_id", "tanggal", "jenis", "status", "keterangan", "created_at", "updated_at", "disetujui_oleh", "file_pendukung")
                SELECT "id", "siswa_id", "tanggal", "jenis", "status", "keterangan", "created_at", "updated_at", "disetujui_oleh", "file_pendukung" FROM "izin_siswas";');

            DB::statement('DROP TABLE "izin_siswas";');
            DB::statement('ALTER TABLE "izin_siswas_temp" RENAME TO "izin_siswas";');

            DB::statement('PRAGMA foreign_keys=ON;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op rollback to preserve data compatibility
    }
};
