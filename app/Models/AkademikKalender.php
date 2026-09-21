<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AkademikKalender extends Model
{
    use HasFactory;

    protected $table = 'akademik_kalenders';

    protected $fillable = [
        'tahun_ajaran_id',
        'semester',
        'total_pekan',
        'pekan_efektif',
        'pekan_cadangan',
        'catatan',
        'is_locked',
        'created_by',
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(AkademikKalenderItem::class, 'akademik_kalender_id')->orderBy('minggu_ke_semester');
    }

    /**
     * Hitung ulang pekan efektif dan pekan cadangan berdasarkan items
     */
    public function hitungUlangPekan(): void
    {
        $totalPekan = $this->items()->count();
        $efektif = $this->items()->where('jenis', 'efektif')->count();
        $cadangan = $totalPekan - $efektif;

        $this->update([
            'total_pekan' => $totalPekan,
            'pekan_efektif' => $efektif,
            'pekan_cadangan' => $cadangan,
        ]);
    }

    /**
     * Generate template standar SMK untuk semester ganjil atau genap
     */
    public static function generateDefaultSemester(int $tahunAjaranId, int $semester, ?int $userId = null): self
    {
        $validUserId = ($userId && User::where('id', $userId)->exists()) ? $userId : null;

        return DB::transaction(function () use ($tahunAjaranId, $semester, $validUserId) {
            $kalender = self::firstOrCreate(
                ['tahun_ajaran_id' => $tahunAjaranId, 'semester' => $semester],
                [
                    'total_pekan' => 25,
                    'pekan_efektif' => 18,
                    'pekan_cadangan' => 7,
                    'catatan' => 'Kalender Pendidikan Resmi SMKN 1 Air Naningan Semester ' . ($semester == 1 ? 'Ganjil' : 'Genap'),
                    'is_locked' => false,
                    'created_by' => $validUserId,
                ]
            );

            // Bersihkan item lama jika ada lalu buat baru
            $kalender->items()->delete();

            if ($semester == 1) {
                // Semester 1: Juli - Desember (25 Pekan)
                $structure = [
                    'Juli' => [
                        ['jenis' => 'non_efektif', 'kategori' => 'mpls', 'keterangan' => 'MPLS & Masa Orientasi Jurusan', 'warna' => '#f59e0b'],
                        ['jenis' => 'non_efektif', 'kategori' => 'mpls', 'keterangan' => 'MPLS & Pengenalan Budaya Kerja / K3', 'warna' => '#f59e0b'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 1', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 2', 'warna' => '#2563eb'],
                    ],
                    'Agustus' => [
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 3', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 4', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 5 (HUT RI)', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 6', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 7', 'warna' => '#2563eb'],
                    ],
                    'September' => [
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 8', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 9', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 10', 'warna' => '#2563eb'],
                        ['jenis' => 'non_efektif', 'kategori' => 'sts', 'keterangan' => 'Sumatif Tengah Semester (STS)', 'warna' => '#8b5cf6'],
                    ],
                    'Oktober' => [
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 11', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 12', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 13', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 14', 'warna' => '#2563eb'],
                    ],
                    'November' => [
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 15', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 16', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 17', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 18', 'warna' => '#2563eb'],
                    ],
                    'Desember' => [
                        ['jenis' => 'non_efektif', 'kategori' => 'sas', 'keterangan' => 'Sumatif Akhir Semester (SAS / ASAS)', 'warna' => '#ec4899'],
                        ['jenis' => 'non_efektif', 'kategori' => 'sas', 'keterangan' => 'Lanjutan SAS & Asesmen Praktik Kejuruan', 'warna' => '#ec4899'],
                        ['jenis' => 'non_efektif', 'kategori' => 'rapor', 'keterangan' => 'Pengolahan Nilai & Pembagian Rapor Smt 1', 'warna' => '#10b981'],
                        ['jenis' => 'non_efektif', 'kategori' => 'libur', 'keterangan' => 'Libur Akhir Semester Ganjil', 'warna' => '#ef4444'],
                    ],
                ];
            } else {
                // Semester 2: Januari - Juni (25 Pekan)
                $structure = [
                    'Januari' => [
                        ['jenis' => 'non_efektif', 'kategori' => 'libur', 'keterangan' => 'Lanjutan Libur Semester / Refleksi Awal Tahun', 'warna' => '#ef4444'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 1', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 2', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 3', 'warna' => '#2563eb'],
                    ],
                    'Februari' => [
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 4', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 5', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 6', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 7', 'warna' => '#2563eb'],
                    ],
                    'Maret' => [
                        ['jenis' => 'non_efektif', 'kategori' => 'ukk', 'keterangan' => 'Uji Kompetensi Keahlian (UKK) / Asesmen LSP-P1 SMK', 'warna' => '#f97316'],
                        ['jenis' => 'non_efektif', 'kategori' => 'sts', 'keterangan' => 'Sumatif Tengah Semester (STS)', 'warna' => '#8b5cf6'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 8', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 9', 'warna' => '#2563eb'],
                    ],
                    'April' => [
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 10', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 11', 'warna' => '#2563eb'],
                        ['jenis' => 'non_efektif', 'kategori' => 'libur', 'keterangan' => 'Libur Sekitar Hari Raya Idul Fitri', 'warna' => '#ef4444'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 12', 'warna' => '#2563eb'],
                    ],
                    'Mei' => [
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 13', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 14', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 15', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 16', 'warna' => '#2563eb'],
                        ['jenis' => 'efektif', 'kategori' => 'kbm', 'keterangan' => 'KBM Efektif Pekan 17', 'warna' => '#2563eb'],
                    ],
                    'Juni' => [
                        ['jenis' => 'non_efektif', 'kategori' => 'sat', 'keterangan' => 'Sumatif Akhir Tahun (SAT / Kenaikan Kelas)', 'warna' => '#ec4899'],
                        ['jenis' => 'non_efektif', 'kategori' => 'sat', 'keterangan' => 'Lanjutan SAT & Ujian Praktik', 'warna' => '#ec4899'],
                        ['jenis' => 'non_efektif', 'kategori' => 'rapor', 'keterangan' => 'Pengolahan Rapor Kenaikan & Kelulusan', 'warna' => '#10b981'],
                        ['jenis' => 'non_efektif', 'kategori' => 'libur', 'keterangan' => 'Libur Akhir Tahun Ajaran', 'warna' => '#ef4444'],
                    ],
                ];
            }

            $globalWeek = 1;
            foreach ($structure as $bulan => $weeks) {
                foreach ($weeks as $idx => $wData) {
                    AkademikKalenderItem::create([
                        'akademik_kalender_id' => $kalender->id,
                        'bulan' => $bulan,
                        'minggu_ke' => $idx + 1,
                        'minggu_ke_semester' => $globalWeek++,
                        'jenis' => $wData['jenis'],
                        'kategori' => $wData['kategori'],
                        'keterangan' => $wData['keterangan'],
                        'warna' => $wData['warna'],
                    ]);
                }
            }

            $kalender->hitungUlangPekan();

            return $kalender;
        });
    }
}
