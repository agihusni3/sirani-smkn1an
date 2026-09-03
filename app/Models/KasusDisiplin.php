<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasusDisiplin extends Model
{
    use HasFactory;

    protected $table = 'kasus_disiplins';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'total_alpha',
        'total_bolos',
        'total_terlambat',
        'total_poin_pelanggaran',
        'total_poin_pemulihan',
        'status_tahap',
        'catatan_wali_kelas',
        'tanggal_tindak_wali',
        'catatan_bk',
        'tanggal_panggilan_bk',
        'hasil_musyawarah_bk',
        'catatan_wakasis',
        'sanksi_wakasis',
        'tanggal_sidang_wakasis',
        'keputusan_kepsek',
        'tanggal_keputusan_kepsek',
        'diverifikasi_oleh',
        'is_active',
    ];

    /**
     * Tabel bobot poin pelanggaran (referensi Tata Tertib SMKN 1 Air Naningan):
     *   Terlambat           =  3 poin / kejadian
     *   Alpha (tidak hadir) = 10 poin / hari
     *   Bolos               = 15 poin / kejadian
     *
     * Ambang batas eskalasi tahap pembinaan:
     *    1 – 25 poin  → Tahap 1 (Wali Kelas – peringatan lisan/tertulis)
     *   26 – 50 poin  → Tahap 2 (Guru BK – panggilan orang tua)
     *   51 – 75 poin  → Tahap 3 (Waka Kesiswaan – surat + sidang)
     *   ≥  76 poin   → Tahap 4 (Kepala Sekolah – skorsing / DO)
     */
    const BOBOT_TERLAMBAT = 3;
    const BOBOT_ALPHA     = 10;
    const BOBOT_BOLOS     = 15;

    const AMBANG_TAHAP_1 = 1;
    const AMBANG_TAHAP_2 = 26;
    const AMBANG_TAHAP_3 = 51;
    const AMBANG_TAHAP_4 = 76;

    protected $casts = [
        'tanggal_tindak_wali'      => 'date:Y-m-d',
        'tanggal_panggilan_bk'     => 'date:Y-m-d',
        'tanggal_sidang_wakasis'   => 'date:Y-m-d',
        'tanggal_keputusan_kepsek' => 'date:Y-m-d',
        'is_active'                => 'boolean',
        'total_poin_pelanggaran'   => 'integer',
        'total_poin_pemulihan'     => 'integer',
    ];

    public function rewards(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(KasusDisiplinReward::class, 'kasus_disiplin_id')->orderBy('tanggal', 'desc')->orderBy('id', 'desc');
    }

    public function pelanggarans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(KasusDisiplinPelanggaran::class, 'kasus_disiplin_id')->orderBy('tanggal', 'desc')->orderBy('id', 'desc');
    }

    public function siswa(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function tahunAjaran(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function logs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(KasusDisiplinLog::class, 'kasus_disiplin_id')->orderBy('tanggal_kegiatan', 'desc')->orderBy('id', 'desc');
    }

    public function dokumens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(KasusDisiplinDokumen::class, 'kasus_disiplin_id')->latest();
    }

    /**
     * Hitung Poin Bersih Akumulasi (Poin Pelanggaran - Poin Pemulihan)
     */
    public function getPoinBersihAttribute(): int
    {
        return max(0, (int)$this->total_poin_pelanggaran - (int)$this->total_poin_pemulihan);
    }

    /**
     * Sinkronisasi akumulasi pelanggaran dari tabel absensi siswa dengan skema poin dinamis.
     */
    public static function syncFromPresensi(int $siswaId): self
    {
        // Guard: pastikan siswa_id valid dan masih ada di tabel siswas
        // Mencegah FOREIGN KEY constraint error jika data absensi adalah orphan (siswa telah dihapus)
        $siswa = \App\Models\Siswa::find($siswaId);
        if (!$siswa) {
            // Siswa tidak ditemukan, kembalikan instance kosong tanpa menyimpan
            return new self(['siswa_id' => $siswaId]);
        }

        $setting = PengaturanDisiplin::getPengaturan();
        $taAktif = TahunAjaran::where('is_active', true)->first();
        $taId = $taAktif ? $taAktif->id : null;

        $stats = Absensi::where('pemilik_type', 'siswa')
            ->where('pemilik_id', $siswaId)
            ->selectRaw("
                SUM(CASE WHEN status = 'alpha' THEN 1 ELSE 0 END) as total_alpha,
                SUM(CASE WHEN status = 'bolos' THEN 1 ELSE 0 END) as total_bolos,
                SUM(CASE WHEN status = 'terlambat' THEN 1 ELSE 0 END) as total_terlambat
            ")->first();

        $alpha = (int) ($stats->total_alpha ?? 0);
        $bolos = (int) ($stats->total_bolos ?? 0);
        $terlambat = (int) ($stats->total_terlambat ?? 0);

        $kasus = self::firstOrNew([
            'siswa_id'  => $siswaId,
            'is_active' => true,
        ]);

        $kasus->tahun_ajaran_id = $taId;
        $kasus->total_alpha     = $alpha;
        $kasus->total_bolos     = $bolos;
        $kasus->total_terlambat = $terlambat;

        // Hitung total poin reward yang sudah pernah diraih siswa dari tabel riwayat reward
        $totalRewardRecorded = 0;
        $totalPelanggaranRecorded = 0;
        if ($kasus->exists) {
            $totalRewardRecorded = (int) KasusDisiplinReward::where('kasus_disiplin_id', $kasus->id)->sum('poin_dikurangi');
            $totalPelanggaranRecorded = (int) KasusDisiplinPelanggaran::where('kasus_disiplin_id', $kasus->id)->sum('poin_ditambah');
            $kasus->total_poin_pemulihan = $totalRewardRecorded;
        }

        // Kalkulasi poin dinamis dari PengaturanDisiplin
        $terlambatDihitung = max(0, $terlambat - (int)$setting->toleransi_terlambat_piket);
        $poinTerlambat = $terlambatDihitung * (int)$setting->bobot_terlambat;
        $poinAlpha = $alpha * (int)$setting->bobot_alpha;
        $poinBolos = $bolos * (int)$setting->bobot_bolos;

        $poinDasar = $poinAlpha + $poinBolos + $poinTerlambat + $totalPelanggaranRecorded;
        $kasus->total_poin_pelanggaran = $poinDasar;

        // Eskalasi tahap berbasis poin bersih
        $poinBersih = max(0, $poinDasar - (int)$kasus->total_poin_pemulihan);
        
        // Jika sedang dalam status selesai_pembinaan dan tidak ada poin baru, pertahankan
        if ($poinBersih >= (int)$setting->ambang_tahap_4_kepsek) {
            $kasus->status_tahap = 'tahap_4_kepsek';
        } elseif ($poinBersih >= (int)$setting->ambang_tahap_3_wakasis) {
            $kasus->status_tahap = 'tahap_3_wakasis';
        } elseif ($poinBersih >= (int)$setting->ambang_tahap_2_bk) {
            $kasus->status_tahap = 'tahap_2_bk';
        } elseif ($poinBersih >= (int)$setting->ambang_tahap_1_wali) {
            $kasus->status_tahap = 'tahap_1_wali_kelas';
        } else {
            $kasus->status_tahap = 'selesai_pembinaan';
        }

        $kasus->save();
        return $kasus;
    }

    /**
     * Filter data kasus berdasarkan kerahasiaan & wewenang role user yang login.
     */
    public function scopeForUser($query, $user)
    {
        if (!$user) return $query;
        // Admin dan Kepala Sekolah memiliki akses supervisi penuh ke seluruh data kasus sekolah
        if ($user->isAdmin() || $user->isKepalaSekolah()) return $query;

        // 1. Wali Kelas: Eksklusif memantau semua status kasus HANYA untuk siswa di rombel perwaliannya sendiri
        if ($user->isWaliKelas() && !$user->isWakaKesiswaan() && !$user->isGuruBk()) {
            $rombelIds = $user->guru ? $user->guru->rombels()->pluck('id') : collect();
            return $query->whereHas('siswa.siswaRombels', function ($q) use ($rombelIds) {
                $q->whereIn('rombel_id', $rombelIds)->where('status_keanggotaan', 'aktif');
            });
        }

        // 2. Guru BK: Kasus yang telah masuk/dieskalasi ke ranah BK (Tahap 2, 3, 4, Selesai)
        if ($user->isGuruBk() && !$user->isWakaKesiswaan() && !$user->isWaliKelas()) {
            return $query->whereIn('status_tahap', ['tahap_2_bk', 'tahap_3_wakasis', 'tahap_4_kepsek', 'selesai_pembinaan']);
        }

        // 3. Waka Kesiswaan: Kasus yang masuk ke ranah Kesiswaan (Tahap 2, 3, 4, Selesai)
        if ($user->isWakaKesiswaan() && !$user->isGuruBk() && !$user->isWaliKelas()) {
            return $query->whereIn('status_tahap', ['tahap_2_bk', 'tahap_3_wakasis', 'tahap_4_kepsek', 'selesai_pembinaan']);
        }

        return $query;
    }

    /**
     * Tampilan visual badge tahap pembinaan terstruktur & dinamis.
     */
    public function getBadgeTahapAttribute(): string
    {
        $setting = PengaturanDisiplin::getPengaturan();

        switch ($this->status_tahap) {
            case 'tahap_1_wali_kelas':
                return '<span class="badge" style="background:#000000; color:#FFFFFF; font-weight:800; font-size:11px; padding:3px 9px; border-radius:6px; display:inline-flex; align-items:center; gap:5px;"><i class="bi bi-shield-exclamation"></i> Tahap 1 · Wali Kelas</span>';
            case 'tahap_2_bk':
                return '<span class="badge" style="background:#000000; color:#FFFFFF; font-weight:800; font-size:11px; padding:3px 9px; border-radius:6px; display:inline-flex; align-items:center; gap:5px;"><i class="bi bi-person-exclamation"></i> Tahap 2 · Guru BK</span>';
            case 'tahap_3_wakasis':
                return '<span class="badge" style="background:#000000; color:#FFFFFF; font-weight:800; font-size:11px; padding:3px 9px; border-radius:6px; display:inline-flex; align-items:center; gap:5px;"><i class="bi bi-gavel"></i> Tahap 3 · Wakasis</span>';
            case 'tahap_4_kepsek':
                return '<span class="badge" style="background:#000000; color:#FFFFFF; font-weight:800; font-size:11px; padding:3px 9px; border-radius:6px; display:inline-flex; align-items:center; gap:5px;"><i class="bi bi-award"></i> Tahap 4 · Kepala Sekolah</span>';
            case 'selesai_pembinaan':
            default:
                return '<span class="badge" style="background:var(--bg-3); color:var(--text); border:1px solid var(--border-2); font-weight:800; font-size:11px; padding:3px 9px; border-radius:6px; display:inline-flex; align-items:center; gap:5px;"><i class="bi bi-check-circle-fill"></i> Selesai Pembinaan</span>';
        }
    }
}
