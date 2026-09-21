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
        'kode_nomor',
    ];

    protected $casts = [
        'hari_mengajar' => 'array',
        'tanggal_lahir' => 'date',
        'tmt_kerja'     => 'date',
    ];

    protected $appends = ['foto_url', 'label_kepegawaian', 'nama_lengkap_gelar', 'list_mapel', 'list_tugas_tambahan', 'peran_struktural'];

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

    /**
     * Label peran struktural sekolah resmi untuk legenda roster & jadwal.
     */
    public function getPeranStrukturalAttribute(): string
    {
        $text = strtolower(($this->jabatan ?? '') . ' ' . ($this->tugas_tambahan ?? '') . ' ' . ($this->jenis_ptk ?? ''));
        $role = strtolower($this->user ? ($this->user->role ?? '') : '');
        $roles = ($this->user && is_array($this->user->roles)) ? array_map('strtolower', $this->user->roles) : [];

        if (str_contains($text, 'kepala sekolah') || $role === 'kepala_sekolah' || in_array('kepala_sekolah', $roles)) {
            return 'Kepala Sekolah';
        }
        if (str_contains($text, 'waka kurikulum') || str_contains($text, 'bidang kurikulum') || $role === 'waka_kurikulum' || in_array('waka_kurikulum', $roles)) {
            return 'Waka Kurikulum';
        }
        if (str_contains($text, 'waka kesiswaan') || str_contains($text, 'bidang kesiswaan') || $role === 'waka_kesiswaan' || in_array('waka_kesiswaan', $roles)) {
            return 'Waka Kesiswaan';
        }
        if (str_contains($text, 'waka sarpras') || str_contains($text, 'sarana prasarana') || $role === 'waka_sarpras' || in_array('waka_sarpras', $roles)) {
            return 'Waka Sarpras';
        }
        if (str_contains($text, 'waka hubin') || str_contains($text, 'hubungan industri') || str_contains($text, 'humas') || $role === 'waka_hubin' || in_array('waka_hubin', $roles)) {
            return 'Waka Hubin';
        }
        if (!empty($this->tugas_tambahan)) {
            return $this->tugas_tambahan;
        }
        if (!empty($this->jabatan) && !str_contains(strtolower($this->jabatan), 'guru mata pelajaran')) {
            return $this->jabatan;
        }

        $mapel = !empty($this->list_mapel) ? implode(', ', array_slice($this->list_mapel, 0, 2)) : '';
        return $mapel ? "Guru {$mapel}" : 'Guru Mata Pelajaran';
    }

    /**
     * Hitung bobot hierarki struktural pendidik (Kepsek=1, Wakakur=2, Wakasis=3, Sarpras=4, Hubin=5, Kaprog=6, Bengkel/Lab=7, Pembina=8, BK=9, Wali=10, Guru=11).
     */
    public static function hitungSkorStruktural($g): array
    {
        $text = strtolower(($g->jabatan ?? '') . ' ' . ($g->tugas_tambahan ?? '') . ' ' . ($g->jenis_ptk ?? ''));
        $role = strtolower($g->user ? ($g->user->role ?? '') : '');
        $roles = ($g->user && is_array($g->user->roles)) ? array_map('strtolower', $g->user->roles) : [];

        // 1. Kepsek selalu No 1
        if (str_contains($text, 'kepala sekolah') || $role === 'kepala_sekolah' || in_array('kepala_sekolah', $roles)) {
            return [1, 0, $g->nama];
        }

        // 2. Wakakur selalu No 2
        if (str_contains($text, 'waka kurikulum') || str_contains($text, 'bidang kurikulum') || $role === 'waka_kurikulum' || in_array('waka_kurikulum', $roles)) {
            return [2, 0, $g->nama];
        }

        // 3. Wakasis selalu No 3
        if (str_contains($text, 'waka kesiswaan') || str_contains($text, 'bidang kesiswaan') || $role === 'waka_kesiswaan' || in_array('waka_kesiswaan', $roles)) {
            return [3, 0, $g->nama];
        }

        // 4. Waka Sarpras selalu No 4
        if (str_contains($text, 'waka sarpras') || str_contains($text, 'sarana prasarana') || str_contains($text, 'sarana dan prasarana') || $role === 'waka_sarpras' || in_array('waka_sarpras', $roles)) {
            return [4, 0, $g->nama];
        }

        // 5. Waka Hubin selalu No 5
        if (str_contains($text, 'waka hubin') || str_contains($text, 'hubungan industri') || str_contains($text, 'hubinmas') || str_contains($text, 'humas') || $role === 'waka_hubin' || in_array('waka_hubin', $roles)) {
            return [5, 0, $g->nama];
        }

        // 6. Kaprog (Ketua Program Keahlian / Jurusan)
        if (str_contains($text, 'kepala program') || str_contains($text, 'ketua program') || str_contains($text, 'kaprog') || str_contains($text, 'ketua jurusan') || $role === 'kaprog' || in_array('kaprog', $roles)) {
            $sub = 9;
            if (str_contains($text, 'rpl')) $sub = 1;
            elseif (str_contains($text, 'aphp')) $sub = 2;
            elseif (str_contains($text, 'tsm') || str_contains($text, 'otomotif')) $sub = 3;
            return [6, $sub, $g->nama];
        }

        // 7. Kepala Bengkel / Lab / Perpustakaan / Koordinator BKK
        if (str_contains($text, 'kepala bengkel') || str_contains($text, 'kepala lab') || str_contains($text, 'perpustakaan') || str_contains($text, 'koordinator bkk')) {
            $sub = 9;
            if (str_contains($text, 'bengkel')) $sub = 1;
            elseif (str_contains($text, 'lab komputer')) $sub = 2;
            elseif (str_contains($text, 'lab aphp')) $sub = 3;
            elseif (str_contains($text, 'perpustakaan')) $sub = 4;
            elseif (str_contains($text, 'bkk') || str_contains($text, 'pkl')) $sub = 5;
            return [7, $sub, $g->nama];
        }

        // 8. Pembina & Koordinator P5
        if (str_contains($text, 'pembina osis')) return [8, 1, $g->nama];
        if (str_contains($text, 'koordinator projek') || str_contains($text, 'p5')) return [8, 2, $g->nama];
        if (str_contains($text, 'pembina pramuka')) return [8, 3, $g->nama];
        if (str_contains($text, 'pembina pmr')) return [8, 4, $g->nama];
        if (str_contains($text, 'pembina rohis')) return [8, 5, $g->nama];
        if (str_contains($text, 'pembina english')) return [8, 6, $g->nama];
        if (str_contains($text, 'pembina')) return [8, 7, $g->nama];

        // 9. Guru BK
        if (str_contains($text, 'bimbingan konseling') || str_contains($text, 'guru bk') || $role === 'guru_bk' || in_array('guru_bk', $roles)) {
            return [9, 0, $g->nama];
        }

        // 10. Wali Kelas
        if (str_contains($text, 'wali kelas') || $role === 'wali_kelas' || in_array('wali_kelas', $roles)) {
            return [10, 0, $g->nama];
        }

        // 11. Guru Mata Pelajaran / GTK Lainnya
        return [11, 0, $g->nama];
    }

    /**
     * Sinkronisasi seluruh kode nomor guru (1, 2, 3...) sesuai hierarki jabatan struktural.
     * Siapa pun yang menjabat akan otomatis menduduki kode nomor sesuai aturan ini.
     * Sekaligus menyinkronkan kode_guru di tabel slot akademik_jadwal_pelajarans.
     */
    public static function sinkronisasiKodeHierarki(): array
    {
        $gurus = static::with('user')->where('status', 'aktif')->get();

        $sorted = $gurus->sort(function ($a, $b) {
            $sa = static::hitungSkorStruktural($a);
            $sb = static::hitungSkorStruktural($b);
            if ($sa[0] !== $sb[0]) return $sa[0] <=> $sb[0];
            if ($sa[1] !== $sb[1]) return $sa[1] <=> $sb[1];
            return strcasecmp($sa[2], $sb[2]);
        })->values();

        $updates = [];
        foreach ($sorted as $idx => $guru) {
            $nomorBaru = $idx + 1;
            if ($guru->kode_nomor !== $nomorBaru) {
                $guru->updateQuietly(['kode_nomor' => $nomorBaru]);
                $updates[] = [
                    'id' => $guru->id,
                    'nama' => $guru->nama,
                    'kode_nomor' => $nomorBaru,
                ];
            }
        }

        // Sinkronkan ke akademik_jadwal_pelajarans jika tabel ada
        if (\Illuminate\Support\Facades\Schema::hasTable('akademik_jadwal_pelajarans')) {
            try {
                $guruCodes = static::whereNotNull('kode_nomor')->pluck('kode_nomor', 'id');
                foreach ($guruCodes as $gId => $kNomor) {
                    \App\Models\AkademikJadwalPelajaran::where('guru_id', $gId)
                        ->update(['kode_guru' => (string)$kNomor]);
                }
            } catch (\Throwable $e) {
                \Log::warning('Gagal sinkron kode_guru di akademik_jadwal_pelajarans: ' . $e->getMessage());
            }
        }

        return $updates;
    }
}

