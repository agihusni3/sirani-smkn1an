<?php

namespace App\Services;

use App\Models\PpdbPendaftar;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use Illuminate\Support\Facades\DB;
use Exception;

class PpdbMutasiService
{
    /**
     * Mutasi Calon Siswa Diterima menjadi Siswa Aktif SIRANI
     */
    public function mutasiKeSiswa(PpdbPendaftar $pendaftar, int $rombelId): Siswa
    {
        return DB::transaction(function () use ($pendaftar, $rombelId) {
            $rombel = Rombel::findOrFail($rombelId);

            // 1. Cek atau Buat Siswa di tabel siswas
            $siswa = Siswa::firstOrNew(['nisn' => $pendaftar->nisn]);
            $siswa->nik = $pendaftar->nik;
            $siswa->nama = $pendaftar->nama_lengkap;
            $siswa->jenis_kelamin = $pendaftar->jenis_kelamin;
            $siswa->tempat_lahir = $pendaftar->tempat_lahir;
            $siswa->tanggal_lahir = $pendaftar->tanggal_lahir;
            $siswa->agama = $pendaftar->agama;
            $siswa->alamat = $pendaftar->alamat;
            $siswa->nama_ayah = $pendaftar->nama_ayah;
            $siswa->nama_ibu = $pendaftar->nama_ibu;
            $siswa->nama_ortu = $pendaftar->nama_ibu ?? $pendaftar->nama_ayah;
            $siswa->asal_sekolah = $pendaftar->asal_sekolah;
            $siswa->no_hp_ortu = $pendaftar->no_hp_ortu;
            $siswa->no_hp_siswa = $pendaftar->no_hp_siswa;
            $siswa->status = 'aktif';
            if ($pendaftar->berkas_foto && empty($siswa->foto)) {
                $siswa->foto = $pendaftar->berkas_foto;
            }
            $siswa->save();

            // 2. Hubungkan ke Rombel (Kelas X)
            $keanggotaan = SiswaRombel::firstOrNew([
                'siswa_id' => $siswa->id,
                'rombel_id' => $rombel->id,
                'tahun_ajaran_id' => $rombel->tahun_ajaran_id,
            ]);
            $keanggotaan->status_keanggotaan = 'aktif';
            $keanggotaan->save();

            // 3. Update Status Pendaftar PPDB
            $pendaftar->siswa_id = $siswa->id;
            $pendaftar->status = 'diterima';
            $pendaftar->jurusan_diterima_id = $rombel->jurusan_id;
            $pendaftar->dimutasi_pada = now();
            $pendaftar->catatan_panitia = ($pendaftar->catatan_panitia ? $pendaftar->catatan_panitia . ' | ' : '') . 'Telah dimutasi ke rombel ' . $rombel->nama_rombel;
            $pendaftar->save();

            return $siswa;
        });
    }
}
