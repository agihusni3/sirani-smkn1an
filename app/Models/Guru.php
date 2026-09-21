<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AuditLog;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'gurus';

    protected static function boot(): void
    {
        parent::boot();
        static::created(function ($m) {
            AuditLog::catat('create', 'guru', "Guru/pegawai baru ditambahkan: {$m->nama}", null, $m->only(['nip','nama','jabatan','status']), $m);
        });
        static::updated(function ($m) {
            if ($m->wasChanged()) {
                AuditLog::catat('update', 'guru', "Data guru diubah: {$m->nama}", $m->getOriginal(), $m->getChanges(), $m);
            }
        });
        static::deleted(function ($m) {
            AuditLog::catat('delete', 'guru', "Guru dihapus: {$m->nama}", $m->only(['nip','nama','jabatan','status']), null, $m);
        });
        static::saved(function ($m) {
            if (str_contains(strtolower($m->jabatan ?? ''), 'kepala sekolah') || str_contains(strtolower($m->tugas_tambahan ?? ''), 'kepala sekolah')) {
                try {
                    $sekolah = PengaturanSekolah::first();
                    if ($sekolah) {
                        $sekolah->withoutEvents(function () use ($sekolah, $m) {
                            $sekolah->update([
                                'nama_kepala_sekolah' => $m->nama_lengkap_gelar ?: $m->nama,
                                'nip_kepala_sekolah'  => $m->nip,
                            ]);
                        });
                    }
                } catch (\Throwable $e) {
                    \Log::warning('Gagal sinkron PengaturanSekolah dari Guru: ' . $e->getMessage());
                }
            }
        });
    }

    protected $fillable = [
        'nip',
        'nama',
        'nama_lengkap',
        'gelar_depan',
        'gelar_belakang',
        'nik',
        'nuptk',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'alamat',
        'id_gtk',
        'jabatan',
        'jenis_kepegawaian',
        'jenis_ptk',
        'golongan_pangkat',
        'nomor_sk_pengangkatan',
        'tmt_kerja',
        'lembaga_pengangkat',
        'pendidikan_terakhir',
        'jurusan_kuliah',
        'kampus',
        'tahun_lulus',
        'status_sertifikasi',
        'nomor_serdik',
        'mapel_diampu',
        'jjm',
        'tugas_tambahan',
        'sk_tugas_tambahan',
        'hari_mengajar',
        'no_hp',
        'foto',
        'status',
        'golongan_ruang',
        'tmt_kgb_terakhir',
        'tmt_pangkat_terakhir',
        'jurusan_pendidikan',
    ];

    protected $casts = [
        'hari_mengajar' => 'array',
        'tanggal_lahir' => 'date',
        'tmt_kerja'     => 'date',
    ];

    protected $appends = ['foto_url', 'label_kepegawaian', 'nama_lengkap_gelar', 'list_mapel', 'list_tugas_tambahan'];

    /**
     * Dapatkan daftar mata pelajaran yang diampu dalam bentuk array.
     */
    public function getListMapelAttribute(): array
    {
        if (!empty($this->mapel_diampu)) {
            return array_values(array_filter(array_map('trim', explode(',', $this->mapel_diampu))));
        }

        // Sinkronisasi otomatis dari SK Pembagian Tugas Wakakur (AkademikDistribusiMengajar)
        try {
            $fromDist = $this->distribusiMengajars()->with('mataPelajaran')->get()->pluck('mataPelajaran.nama_mapel')->filter()->unique()->values()->all();
            if (!empty($fromDist)) {
                return $fromDist;
            }
        } catch (\Throwable $e) {}

        return [];
    }

    /**
     * Dapatkan daftar tugas tambahan dalam bentuk array (Sinkron dengan Permendikbud 15/2018 & SITUAN/SIRANI).
     */
    public function getListTugasTambahanAttribute(): array
    {
        return collect($this->tugas_tambahan_list)->pluck('nama')->all();
    }

    /**
     * Dapatkan total JJM (Jam Mengajar Mingguan).
     * Jika di profil manual belum diset, otomatis hitung dari SK Pembagian Tugas Wakakur.
     */
    public function getJjmAttribute(?int $value): int
    {
        if (!empty($value) && $value > 0) {
            return $value;
        }

        try {
            return (int) $this->distribusiMengajars()->sum('total_jam_per_minggu');
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Format nama guru/pegawai selalu dalam format Title Case standar resmi.
     */
    public function getNamaAttribute(?string $value): string
    {
        return \App\Support\NamaFormatter::format($value ?? ($this->attributes['nama'] ?? ''));
    }

    /**
     * Dapatkan nama lengkap resmi beserta gelar depan dan belakang.
     */
    public function getNamaLengkapGelarAttribute(): string
    {
        $baseName = !empty($this->nama_lengkap) ? $this->nama_lengkap : $this->nama;
        $depan = trim($this->gelar_depan ?? '');
        $belakang = trim($this->gelar_belakang ?? '');

        $formatted = \App\Support\NamaFormatter::format($baseName);
        if (!empty($depan) && !str_starts_with(strtolower($formatted), strtolower($depan))) {
            $formatted = $depan . ' ' . $formatted;
        }
        if (!empty($belakang) && !str_ends_with(strtolower($formatted), strtolower($belakang))) {
            $formatted = rtrim($formatted, ', ') . ', ' . $belakang;
        }

        return \App\Support\NamaFormatter::format($formatted);
    }

    /**
     * Relasi ke sertifikat pelatihan / pengembangan diri guru.
     */
    public function sertifikats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SertifikatGuru::class, 'guru_id')->orderBy('tahun', 'desc')->orderBy('id', 'desc');
    }

    /**
     * Cek apakah guru bertaraf honorer / GTT.
     */
    public function isHonor(): bool
    {
        return in_array($this->jenis_kepegawaian, ['honor', 'gtt', 'honorer']);
    }

    /**
     * Label teks kepegawaian untuk tampilan badge.
     */
    public function getLabelKepegawaianAttribute(): string
    {
        return match($this->jenis_kepegawaian) {
            'pns'     => 'PNS',
            'pppk'    => 'PPPK',
            'honor'   => 'Guru Honor (GTT)',
            'tendik'  => 'Tenaga Kependidikan',
            default   => 'PNS',
        };
    }

    /**
     * Dapatkan daftar hari mengajar (default Senin-Jumat jika tidak dispesifikasikan).
     */
    public function getHariMengajarList(): array
    {
        if (!empty($this->hari_mengajar) && is_array($this->hari_mengajar)) {
            return $this->hari_mengajar;
        }

        // Default untuk PNS / Tendik atau jika belum diset: Senin s/d Jumat
        return ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    }

    /**
     * Cek apakah guru wajib hadir pada hari tertentu (nama hari bahasa Indonesia atau string tanggal).
     */
    public function isWajibHadirHari(?string $hari = null): bool
    {
        if (empty($hari)) {
            $hari = \Carbon\Carbon::today()->locale('id')->isoFormat('dddd');
        }

        // Jika parameter berupa format tanggal (misal 2026-09-04), konversi ke nama hari bahasa Indonesia
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $hari)) {
            $hari = \Carbon\Carbon::parse($hari)->locale('id')->isoFormat('dddd');
        }

        $hariFormatted = ucfirst(strtolower(trim($hari)));

        // Hari libur akhir pekan
        if (in_array($hariFormatted, ['Sabtu', 'Minggu'])) {
            return false;
        }

        // Pegawai / Guru tetap (PNS, PPPK, Tendik) wajib hadir setiap hari kerja (Senin-Jumat)
        if (!$this->isHonor()) {
            return true;
        }

        // Guru honorer / GTT wajib hadir jika hari tersebut ada di jadwal mengajarnya
        $hariList = array_map(fn($h) => ucfirst(strtolower(trim($h))), $this->getHariMengajarList());
        return in_array($hariFormatted, $hariList);
    }

    /**
     * Cek apakah hari tertentu (atau hari ini) termasuk hari mengajar bagi guru ini.
     */
    public function isHariMengajar($tanggal = null): bool
    {
        return $this->isWajibHadirHari($tanggal ? \Carbon\Carbon::parse($tanggal)->toDateString() : null);
    }

    public function getFotoUrlAttribute(): string
    {
        if (!empty($this->foto)) {
            // Jika sudah berupa URL lengkap
            if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
                return $this->foto;
            }

            $cleanPath = ltrim(str_replace('storage/', '', $this->foto), '/');

            // Cek ketersediaan file fisik di storage maupun public
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath) ||
                file_exists(storage_path('app/public/' . $cleanPath)) ||
                file_exists(public_path('storage/' . $cleanPath))) {
                return asset('storage/' . $cleanPath);
            }

            if (file_exists(public_path($cleanPath))) {
                return asset($cleanPath);
            }

            // Fallback URL jika path tercatat di database
            return asset('storage/' . $cleanPath);
        }

        // Bersihkan gelar akademik depan dan belakang untuk inisial nama yang akurat
        $cleanName = preg_replace('/\b(Drs|Dra|Ir|Prof|Dr|H|Hj)\.\s*/i', '', $this->nama);
        $cleanName = preg_replace('/,.*$/', '', $cleanName); // Hapus gelar belakang seperti ', S.Pd', ', S.T.', ', M.Pd'
        $namaEncoded = urlencode($cleanName ?: $this->nama);
        return "https://ui-avatars.com/api/?name={$namaEncoded}&background=3B82F6&color=ffffff&bold=true&size=200";
    }

    public function getPangkatAttribute(): ?string
    {
        return $this->attributes['pangkat'] ?? $this->golongan_pangkat ?? null;
    }

    public function getTmtCpnsAttribute(): ?string
    {
        if (!empty($this->attributes['tmt_cpns'])) {
            return $this->attributes['tmt_cpns'];
        }
        if (!empty($this->tmt_kerja)) {
            return is_string($this->tmt_kerja) ? $this->tmt_kerja : $this->tmt_kerja->toDateString();
        }
        return null;
    }

    public function getTmtPangkatAttribute(): ?string
    {
        if (!empty($this->attributes['tmt_pangkat'])) {
            return $this->attributes['tmt_pangkat'];
        }
        if (!empty($this->tmt_pangkat_terakhir)) {
            return is_string($this->tmt_pangkat_terakhir) ? $this->tmt_pangkat_terakhir : $this->tmt_pangkat_terakhir;
        }
        return null;
    }

    public function getTmtKgbBerikutnyaAttribute(): ?string
    {
        if (!empty($this->tmt_kgb_terakhir)) {
            try {
                return \Carbon\Carbon::parse($this->tmt_kgb_terakhir)->addYears(2)->toDateString();
            } catch (\Throwable $e) {
                return null;
            }
        }
        return null;
    }


    public function absensis(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Absensi::class, 'pemilik_id')->where('pemilik_type', 'guru');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'guru_id');
    }

    public function rombels(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Rombel::class, 'wali_kelas_id');
    }

    public function kartuRfid(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(KartuRfid::class, 'pemilik_id')->where('pemilik_type', 'guru')->where('status', 'aktif');
    }

    public function kartuRfids(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(KartuRfid::class, 'pemilik_id')->where('pemilik_type', 'guru');
    }

    public function arsipDokumens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ArsipDokumenPtk::class, 'guru_id');
    }

    public function distribusiMengajars(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AkademikDistribusiMengajar::class, 'guru_id');
    }

    public function jadwalPelajarans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AkademikJadwalPelajaran::class, 'guru_id');
    }

    public function perangkatAjars(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AkademikPerangkatAjar::class, 'guru_id');
    }

    /**
     * Rincian tugas tambahan yang diemban guru beserta ekuivalensi jam (Permendikbud 15/2018)
     */
    public function getTugasTambahanListAttribute(): array
    {
        $list = [];
        $addedKeys = [];

        // 1. Parsing dari kolom tugas_tambahan atau jabatan
        $rawTugas = $this->tugas_tambahan ?? '';
        // Jika tugas_tambahan kosong tapi jabatan adalah Kepala Sekolah atau Waka, gunakan jabatan
        if (empty($rawTugas) && !empty($this->jabatan)) {
            $lowJab = strtolower($this->jabatan);
            if (str_contains($lowJab, 'kepala sekolah') || str_contains($lowJab, 'waka') || str_contains($lowJab, 'pembina')) {
                $rawTugas = $this->jabatan;
            }
        }

        // Ambil daftar master tugas tambahan dari database untuk ekuivalensi dinamis
        static $cachedMasterTugas = null;
        if ($cachedMasterTugas === null) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('akademik_master_tugas_tambahans')) {
                    $cachedMasterTugas = \App\Models\AkademikMasterTugasTambahan::where('is_active', true)->get();
                } else {
                    $cachedMasterTugas = collect();
                }
            } catch (\Throwable $e) {
                $cachedMasterTugas = collect();
            }
        }

        // Pisahkan jika ada beberapa tugas tambahan dipisah koma atau titik koma
        $tokens = array_filter(array_map('trim', preg_split('/[,;\n]+/', $rawTugas)));

        foreach ($tokens as $token) {
            $low = strtolower($token);
            if (empty($low)) continue;

            $nama = $token;
            $jp = null;
            $kategori = 'Tugas Tambahan';

            // 1. Dukung format jam manual dinamis jika diketik: "Nama Tugas (+X JP)" atau "Nama Tugas (X JP)"
            if (preg_match('/^(.*?)\s*\((\+?\d+)\s*jp\)$/i', $token, $mJp)) {
                $nama = trim($mJp[1]);
                $jp = (int) str_replace('+', '', $mJp[2]);
            }

            // 2. Cocokkan dengan Master Tugas Tambahan Dinamis di DB
            if ($cachedMasterTugas && $cachedMasterTugas->isNotEmpty()) {
                $matchedMaster = $cachedMasterTugas->first(function ($m) use ($nama) {
                    return strcasecmp(trim($m->nama_tugas), trim($nama)) === 0;
                });

                if (!$matchedMaster) {
                    $matchedMaster = $cachedMasterTugas->first(function ($m) use ($nama) {
                        return str_contains(strtolower($nama), strtolower($m->nama_tugas))
                            || str_contains(strtolower($m->nama_tugas), strtolower($nama));
                    });
                }

                if ($matchedMaster) {
                    $nama = $matchedMaster->nama_tugas;
                    if ($jp === null) {
                        $jp = (int) $matchedMaster->ekuivalensi_jam;
                    }
                    $kategori = $matchedMaster->kategori ?: 'Tugas Tambahan';
                }
            }

            // 3. Fallback cerdas jika belum terdaftar di master DB
            if ($jp === null) {
                $jp = 2;
                if (str_contains($low, 'kepala sekolah') && !str_contains($low, 'wakil') && !str_contains($low, 'waka')) {
                    $nama = 'Kepala Sekolah';
                    $jp = 24;
                    $kategori = 'Manajerial';
                } elseif (str_contains($low, 'waka') || str_contains($low, 'wakil')) {
                    $nama = ucwords($token);
                    $jp = 12;
                    $kategori = 'Pimpinan';
                } elseif (str_contains($low, 'kaprog') || str_contains($low, 'kepala program') || str_contains($low, 'ketua program') || str_contains($low, 'ketua jurusan')) {
                    $nama = ucwords($token);
                    $jp = 12;
                    $kategori = 'Ketua Program';
                } elseif (str_contains($low, 'kepala bengkel') || str_contains($low, 'kepala lab') || str_contains($low, 'kepala laboratorium')) {
                    $nama = ucwords($token);
                    $jp = 12;
                    $kategori = 'Kepala Bengkel/Lab';
                } elseif (str_contains($low, 'perpustakaan') || str_contains($low, 'perpus')) {
                    $nama = 'Kepala Perpustakaan Sekolah';
                    $jp = 12;
                    $kategori = 'Perpustakaan';
                } elseif (str_contains($low, 'unit produksi') || str_contains($low, 'blud')) {
                    $nama = ucwords($token);
                    $jp = 6;
                    $kategori = 'Unit Produksi';
                } elseif (str_contains($low, 'piket')) {
                    $nama = 'Guru Piket';
                    $jp = 1;
                    $kategori = 'Operasional';
                } else {
                    $nama = ucwords($nama);
                }
            }

            $uniqueKey = strtolower($nama);
            if (!isset($addedKeys[$uniqueKey])) {
                $addedKeys[$uniqueKey] = true;
                $list[] = [
                    'nama' => $nama,
                    'jp' => $jp,
                    'kategori' => $kategori,
                ];
            }
        }

        // 2. Dari Tabel Rombels (Wali Kelas Resmi)
        $rombelWali = \App\Models\Rombel::where('wali_kelas_id', $this->id)->get();
        foreach ($rombelWali as $rw) {
            $namaWali = 'Wali Kelas ' . $rw->nama_rombel;
            $uniqueKey = strtolower($namaWali);
            // Hindari duplikasi jika sudah terdeteksi di teks tugas_tambahan
            if (!isset($addedKeys[$uniqueKey])) {
                $addedKeys[$uniqueKey] = true;
                $list[] = [
                    'nama' => $namaWali,
                    'jp' => 2,
                    'kategori' => 'Wali Kelas',
                ];
            }
        }

        return $list;
    }

    /**
     * Total jam ekuivalen dari seluruh tugas tambahan
     */
    public function getTotalEkuivalenTugasTambahanAttribute(): int
    {
        return collect($this->tugas_tambahan_list)->sum('jp');
    }
}

