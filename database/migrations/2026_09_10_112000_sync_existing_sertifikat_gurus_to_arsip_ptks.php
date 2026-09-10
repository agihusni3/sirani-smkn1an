<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\SertifikatGuru;
use App\Models\ArsipDokumenPtk;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sinkronkan seluruh sertifikat guru yang sudah ada ke lemari berkas digital E-Kabinet
        $sertifikats = SertifikatGuru::all();
        foreach ($sertifikats as $s) {
            if (!empty($s->file_sertifikat)) {
                ArsipDokumenPtk::firstOrCreate(
                    [
                        'guru_id'   => $s->guru_id,
                        'file_path' => $s->file_sertifikat,
                    ],
                    [
                        'kategori_berkas' => 'sertifikat_pelatihan',
                        'nama_dokumen'    => 'Sertifikat: ' . $s->nama_pelatihan . ($s->penyelenggara ? ' (' . $s->penyelenggara . ')' : ''),
                        'nomor_dokumen'   => null,
                        'tanggal_dokumen' => $s->tahun ? ($s->tahun . '-01-01') : $s->created_at,
                    ]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu menghapus berkas saat rollback
    }
};
