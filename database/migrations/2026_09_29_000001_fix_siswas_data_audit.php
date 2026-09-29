<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Perbaikan data siswa hasil audit 29 September 2026:
     * 1. NISN 16-digit (NIK) -> dipindah ke kolom nik, nisn di-NULL-kan
     * 2. NISN >10 digit lainnya yang tidak valid -> di-NULL-kan
     * 3. NISN dummy/placeholder -> di-NULL-kan
     * 4. Nama ALL CAPS -> dikonversi ke Title Case
     */
    public function up(): void
    {
        // ============================================================
        // FIX 1: NISN 16-digit (kemungkinan diisi NIK) -> pindah ke kolom nik
        // ============================================================
        DB::statement("
            UPDATE siswas
            SET nik = nisn, nisn = NULL
            WHERE LENGTH(nisn) = 16
              AND (nik IS NULL OR nik = '')
        ");

        // ============================================================
        // FIX 2: NISN >10 digit lainnya (bukan NIK 16-digit) -> NULL
        //   - ID 380: Rosita Meilani        (nisn: 6473115430853  — 13 digit)
        //   - ID 427: Muhammad Mufid        (nisn: 201120105318   — 12 digit)
        //   - ID 474: Andkdmaba             (nisn: 27292992833    — 11 digit)
        //   - ID 510: Risqi Aditia Pratama  (nisn: 180626208090003— 15 digit)
        // ============================================================
        DB::statement("
            UPDATE siswas
            SET nisn = NULL
            WHERE LENGTH(nisn) > 10
              AND LENGTH(nisn) != 16
        ");

        // ============================================================
        // FIX 3: NISN placeholder / dummy -> NULL
        //   - ID 525: Falentino bintang permana  (NISN000059)
        //   - ID 445: Sukma Dinata               (12345678)
        // ============================================================
        DB::statement("UPDATE siswas SET nisn = NULL WHERE nisn = 'NISN000059'");
        DB::statement("UPDATE siswas SET nisn = NULL WHERE nisn = '12345678'");

        // ============================================================
        // FIX 4: Nama ALL CAPS -> Title Case
        //   - ID 527: RISKY PERNANDO
        //   - ID 528: AHMAD SIHENDRA
        //   - ID 542: ELLISA DWI RAHMAWATI
        // ============================================================
        DB::statement("UPDATE siswas SET nama = 'Risky Pernando'      WHERE id = 527 AND nama = 'RISKY PERNANDO'");
        DB::statement("UPDATE siswas SET nama = 'Ahmad Sihendra'       WHERE id = 528 AND nama = 'AHMAD SIHENDRA'");
        DB::statement("UPDATE siswas SET nama = 'Ellisa Dwi Rahmawati' WHERE id = 542 AND nama = 'ELLISA DWI RAHMAWATI'");

        // ============================================================
        // CATATAN: Nama dengan tanda kutip (') TIDAK diubah
        //   - Al'Fadzrian, Safi'I, Ma'Ruf, A'An = VALID (nama Arab)
        //
        // CATATAN: Duplikat Novita Yuli Yanti (ID 417 & 418) TIDAK dihapus
        //   - Perlu verifikasi manual fisik siswa
        //
        // CATATAN: Andkdmaba (ID 474) TIDAK dihapus
        //   - Perlu verifikasi manual siapa siswa ini
        // ============================================================
    }

    /**
     * Rollback: kembalikan data ke kondisi sebelum fix
     * (NISN yang di-NULL tidak bisa di-rollback otomatis karena tidak disimpan)
     */
    public function down(): void
    {
        // Rollback nama ALL CAPS (bisa dikembalikan)
        DB::statement("UPDATE siswas SET nama = 'RISKY PERNANDO'       WHERE id = 527 AND nama = 'Risky Pernando'");
        DB::statement("UPDATE siswas SET nama = 'AHMAD SIHENDRA'        WHERE id = 528 AND nama = 'Ahmad Sihendra'");
        DB::statement("UPDATE siswas SET nama = 'ELLISA DWI RAHMAWATI'  WHERE id = 542 AND nama = 'Ellisa Dwi Rahmawati'");

        // Rollback NISN 16-digit dari kolom nik (jika masih ada)
        DB::statement("
            UPDATE siswas
            SET nisn = nik, nik = NULL
            WHERE LENGTH(nik) = 16
              AND nisn IS NULL
              AND id IN (426, 444, 462)
        ");
    }
};
