<?php

namespace App\Services;

use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\SiswaRombel;
use App\Models\TahunAjaran;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class TransisiAkademikService
{
    /**
     * Memproses Kenaikan Kelas bagi siswa.
     */
    public function naikKelas(int $siswaId, int $rombelBaruId, int $tahunAjaranBaruId): SiswaRombel
    {
        return DB::transaction(function () use ($siswaId, $rombelBaruId, $tahunAjaranBaruId) {
            $siswa = Siswa::findOrFail($siswaId);
            if ($siswa->status !== 'aktif') {
                throw new Exception("Hanya siswa aktif yang dapat dinaikkan kelas.");
            }

            // 1. Tutup keanggotaan rombel lama
            SiswaRombel::where('siswa_id', $siswaId)
                ->where('status_keanggotaan', 'aktif')
                ->update(['status_keanggotaan' => 'naik']);

            // 2. Buka keanggotaan rombel baru
            return SiswaRombel::create([
                'siswa_id' => $siswaId,
                'rombel_id' => $rombelBaruId,
                'tahun_ajaran_id' => $tahunAjaranBaruId,
                'status_keanggotaan' => 'aktif',
            ]);
        });
    }

    /**
     * Memproses Siswa Tinggal Kelas (Tetap di tingkat/rombel lama pada Tahun Ajaran Baru).
     */
    public function tinggalKelas(int $siswaId, int $rombelTujuanId, int $tahunAjaranBaruId): SiswaRombel
    {
        return DB::transaction(function () use ($siswaId, $rombelTujuanId, $tahunAjaranBaruId) {
            $siswa = Siswa::findOrFail($siswaId);
            if ($siswa->status !== 'aktif') {
                throw new Exception("Hanya siswa aktif yang dapat diproses tinggal kelas.");
            }

            // 1. Tandai keanggotaan rombel lama sebagai tinggal
            SiswaRombel::where('siswa_id', $siswaId)
                ->where('status_keanggotaan', 'aktif')
                ->update(['status_keanggotaan' => 'tinggal']);

            // 2. Buka keanggotaan baru untuk tahun ajaran berikutnya
            return SiswaRombel::create([
                'siswa_id' => $siswaId,
                'rombel_id' => $rombelTujuanId,
                'tahun_ajaran_id' => $tahunAjaranBaruId,
                'status_keanggotaan' => 'aktif',
            ]);
        });
    }

    /**
     * Memproses Kelulusan Siswa (Status Terminal Lulus).
     */
    public function lulus(int $siswaId): Siswa
    {
        return DB::transaction(function () use ($siswaId) {
            $siswa = Siswa::findOrFail($siswaId);

            // 1. Ubah status siswa menjadi lulus
            $siswa->update(['status' => 'lulus']);

            // 2. Tutup keanggotaan rombel aktif
            SiswaRombel::where('siswa_id', $siswaId)
                ->where('status_keanggotaan', 'aktif')
                ->update(['status_keanggotaan' => 'lulus']);

            return $siswa;
        });
    }

    public function kelulusan(int $siswaId): Siswa
    {
        return $this->lulus($siswaId);
    }

    /**
     * Memproses siswa pindah (Status Terminal Pindah).
     */
    public function pindah(int $siswaId): Siswa
    {
        return DB::transaction(function () use ($siswaId) {
            $siswa = Siswa::findOrFail($siswaId);

            $siswa->update(['status' => 'pindah']);

            SiswaRombel::where('siswa_id', $siswaId)
                ->where('status_keanggotaan', 'aktif')
                ->update(['status_keanggotaan' => 'pindah']);

            return $siswa;
        });
    }

    /**
     * Memproses siswa keluar (Status Terminal Keluar).
     */
    public function keluar(int $siswaId): Siswa
    {
        return DB::transaction(function () use ($siswaId) {
            $siswa = Siswa::findOrFail($siswaId);

            $siswa->update(['status' => 'keluar']);

            SiswaRombel::where('siswa_id', $siswaId)
                ->where('status_keanggotaan', 'aktif')
                ->update(['status_keanggotaan' => 'keluar']);

            return $siswa;
        });
    }

    /**
     * Memproses Kenaikan Kelas Massal untuk seluruh siswa aktif di suatu rombel.
     */
    public function batchNaikKelas(int $rombelAsalId, int $rombelTujuanId, int $tahunAjaranBaruId, array $excludedSiswaIds = []): int
    {
        return DB::transaction(function () use ($rombelAsalId, $rombelTujuanId, $tahunAjaranBaruId, $excludedSiswaIds) {
            $siswaRombels = SiswaRombel::with('siswa')
                ->where('rombel_id', $rombelAsalId)
                ->where('status_keanggotaan', 'aktif')
                ->whereHas('siswa', function ($q) {
                    $q->where('status', 'aktif');
                })
                ->when(!empty($excludedSiswaIds), function ($q) use ($excludedSiswaIds) {
                    $q->whereNotIn('siswa_id', $excludedSiswaIds);
                })
                ->get();

            if ($siswaRombels->isEmpty()) {
                throw new Exception("Tidak ada siswa aktif yang dapat dinaikkan kelas pada rombel ini.");
            }

            $count = 0;
            foreach ($siswaRombels as $sr) {
                // Tutup keanggotaan rombel lama
                $sr->update(['status_keanggotaan' => 'naik']);

                // Buat keanggotaan baru
                SiswaRombel::create([
                    'siswa_id' => $sr->siswa_id,
                    'rombel_id' => $rombelTujuanId,
                    'tahun_ajaran_id' => $tahunAjaranBaruId,
                    'status_keanggotaan' => 'aktif',
                ]);
                $count++;
            }

            return $count;
        });
    }

    /**
     * Memproses Kelulusan Massal untuk seluruh siswa aktif di suatu rombel (Tingkat XII).
     */
    public function batchKelulusan(int $rombelAsalId, array $excludedSiswaIds = []): int
    {
        return DB::transaction(function () use ($rombelAsalId, $excludedSiswaIds) {
            $siswaRombels = SiswaRombel::with('siswa')
                ->where('rombel_id', $rombelAsalId)
                ->where('status_keanggotaan', 'aktif')
                ->whereHas('siswa', function ($q) {
                    $q->where('status', 'aktif');
                })
                ->when(!empty($excludedSiswaIds), function ($q) use ($excludedSiswaIds) {
                    $q->whereNotIn('siswa_id', $excludedSiswaIds);
                })
                ->get();

            if ($siswaRombels->isEmpty()) {
                throw new Exception("Tidak ada siswa aktif yang dapat diluluskan pada rombel ini.");
            }

            $count = 0;
            foreach ($siswaRombels as $sr) {
                $sr->siswa->update(['status' => 'lulus']);
                $sr->update(['status_keanggotaan' => 'lulus']);
                $count++;
            }

            return $count;
        });
    }

    /**
     * Memproses Tinggal Kelas Massal untuk seluruh siswa aktif di suatu rombel.
     */
    public function batchTinggalKelas(int $rombelAsalId, int $rombelTujuanId, int $tahunAjaranBaruId, array $excludedSiswaIds = []): int
    {
        return DB::transaction(function () use ($rombelAsalId, $rombelTujuanId, $tahunAjaranBaruId, $excludedSiswaIds) {
            $siswaRombels = SiswaRombel::with('siswa')
                ->where('rombel_id', $rombelAsalId)
                ->where('status_keanggotaan', 'aktif')
                ->whereHas('siswa', function ($q) {
                    $q->where('status', 'aktif');
                })
                ->when(!empty($excludedSiswaIds), function ($q) use ($excludedSiswaIds) {
                    $q->whereNotIn('siswa_id', $excludedSiswaIds);
                })
                ->get();

            if ($siswaRombels->isEmpty()) {
                throw new Exception("Tidak ada siswa aktif yang dapat diproses pada rombel ini.");
            }

            $count = 0;
            foreach ($siswaRombels as $sr) {
                $sr->update(['status_keanggotaan' => 'tinggal']);

                SiswaRombel::create([
                    'siswa_id' => $sr->siswa_id,
                    'rombel_id' => $rombelTujuanId,
                    'tahun_ajaran_id' => $tahunAjaranBaruId,
                    'status_keanggotaan' => 'aktif',
                ]);
                $count++;
            }

            return $count;
        });
    }

    /**
     * Memproses Siswa Memulai PKL (Praktek Kerja Lapangan).
     */
    public function mulaiPkl(int $siswaId): Siswa
    {
        return DB::transaction(function () use ($siswaId) {
            $siswa = Siswa::findOrFail($siswaId);
            if ($siswa->status !== 'aktif') {
                throw new Exception("Hanya siswa dengan status aktif yang dapat ditugaskan PKL.");
            }
            $siswa->update(['status' => 'pkl']);
            return $siswa;
        });
    }

    /**
     * Memproses Siswa Selesai PKL (Kembali Aktif di Sekolah).
     */
    public function selesaiPkl(int $siswaId): Siswa
    {
        return DB::transaction(function () use ($siswaId) {
            $siswa = Siswa::findOrFail($siswaId);
            if ($siswa->status !== 'pkl') {
                throw new Exception("Hanya siswa berstatus PKL yang dapat dikembalikan ke status aktif.");
            }
            $siswa->update(['status' => 'aktif']);
            return $siswa;
        });
    }

    /**
     * Memproses Penugasan PKL Massal untuk satu rombel kelas.
     */
    public function batchPkl(int $rombelId, array $excludedSiswaIds = []): int
    {
        return DB::transaction(function () use ($rombelId, $excludedSiswaIds) {
            $siswas = Siswa::whereHas('siswaRombels', function ($q) use ($rombelId) {
                $q->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif');
            })
            ->where('status', 'aktif')
            ->when(!empty($excludedSiswaIds), function ($q) use ($excludedSiswaIds) {
                $q->whereNotIn('id', $excludedSiswaIds);
            })
            ->get();

            if ($siswas->isEmpty()) {
                throw new Exception("Tidak ada siswa aktif yang dapat ditugaskan PKL pada rombel ini.");
            }

            foreach ($siswas as $s) {
                $s->update(['status' => 'pkl']);
            }

            return $siswas->count();
        });
    }

    /**
     * Memproses Penarikan PKL Massal (Kembali Aktif) untuk satu rombel kelas.
     */
    public function batchSelesaiPkl(int $rombelId, array $excludedSiswaIds = []): int
    {
        return DB::transaction(function () use ($rombelId, $excludedSiswaIds) {
            $siswas = Siswa::whereHas('siswaRombels', function ($q) use ($rombelId) {
                $q->where('rombel_id', $rombelId)->where('status_keanggotaan', 'aktif');
            })
            ->where('status', 'pkl')
            ->when(!empty($excludedSiswaIds), function ($q) use ($excludedSiswaIds) {
                $q->whereNotIn('id', $excludedSiswaIds);
            })
            ->get();

            if ($siswas->isEmpty()) {
                throw new Exception("Tidak ada siswa berstatus PKL pada rombel ini.");
            }

            foreach ($siswas as $s) {
                $s->update(['status' => 'aktif']);
            }

            return $siswas->count();
        });
    }
}
