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
     * Filter data kasus berdasarkan role user yang login.
     */
    public function scopeForUser($query, $user)
    {
        if (!$user) return $query;

        // Jika Wali Kelas dan bukan Admin/Kepsek/Wakasis/BK
        if ($user->isWaliKelas() && !$user->isAdmin() && !$user->isKepalaSekolah() && !$user->isWakaKesiswaan() && !$user->isGuruBk()) {
            $rombelIds = $user->guru ? $user->guru->rombels()->pluck('id') : collect();
            return $query->whereHas('siswa.siswaRombels', function ($q) use ($rombelIds) {
                $q->whereIn('rombel_id', $rombelIds)->where('status_keanggotaan', 'aktif');
            });
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
                return '<div class="tahap-badge-wrap tahap-1"><div class="tahap-title">Tahap 1 · Wali Kelas</div><div class="tahap-sub">' . $setting->ambang_tahap_1_wali . '–' . ($setting->ambang_tahap_2_bk - 1) . ' Poin (Pembinaan Awal)</div></div>';
            case 'tahap_2_bk':
                return '<div class="tahap-badge-wrap tahap-2"><div class="tahap-title">Tahap 2 · Guru BK</div><div class="tahap-sub">' . $setting->ambang_tahap_2_bk . '–' . ($setting->ambang_tahap_3_wakasis - 1) . ' Poin (Musyawarah)</div></div>';
            case 'tahap_3_wakasis':
                return '<div class="tahap-badge-wrap tahap-3"><div class="tahap-title">Tahap 3 · Wakasis</div><div class="tahap-sub">' . $setting->ambang_tahap_3_wakasis . '–' . ($setting->ambang_tahap_4_kepsek - 1) . ' Poin (Sidang Disiplin)</div></div>';
            case 'tahap_4_kepsek':
                return '<div class="tahap-badge-wrap tahap-4"><div class="tahap-title">Tahap 4 · Kepala Sekolah</div><div class="tahap-sub">≥' . $setting->ambang_tahap_4_kepsek . ' Poin (Keputusan Akhir)</div></div>';
            case 'selesai_pembinaan':
            default:
                return '<div class="tahap-badge-wrap tahap-selesai"><div class="tahap-title">Selesai Pembinaan</div><div class="tahap-sub">Kondisi Siswa Tertib</div></div>';
        }
    }
}
