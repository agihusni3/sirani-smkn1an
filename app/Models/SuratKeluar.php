<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_agenda',
        'tahun_agenda',
        'klasifikasi_id',
        'kode_klasifikasi',
        'nomor_surat_lengkap',
        'kode_verifikasi_qr',
        'tujuan_surat',
        'perihal',
        'tanggal_surat',
        'penandatangan',
        'jabatan_penandatangan',
        'nip_penandatangan',
        'jenis_surat',
        'sifat_surat',
        'lampiran',
        'isi_surat',
        'tembusan',
        'sumber_modul',
        'kategori_surat',
        'link_cetak',
        'is_nomor_manual',
        'file_arsip',
        'created_by',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->kode_verifikasi_qr)) {
                $model->kode_verifikasi_qr = 'SK-' . strtoupper(substr(hash('sha256', uniqid('sk_', true) . microtime()), 0, 24));
            }
        });
    }

    public function ensureKodeVerifikasi(): string
    {
        if (empty($this->kode_verifikasi_qr)) {
            $this->kode_verifikasi_qr = 'SK-' . strtoupper(substr(hash('sha256', uniqid('sk_' . $this->id . '_', true) . microtime()), 0, 24));
            $this->saveQuietly();
        }
        return $this->kode_verifikasi_qr;
    }

    protected $casts = [
        'tanggal_surat'   => 'date',
        'is_nomor_manual' => 'boolean',
    ];

    public function klasifikasi()
    {
        return $this->belongsTo(KlasifikasiSurat::class, 'klasifikasi_id');
    }

    public function pelayanans()
    {
        return $this->hasMany(PelayananSurat::class, 'surat_keluar_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function notifikasiOrtu()
    {
        return $this->hasOne(NotifikasiOrtu::class, 'surat_keluar_id');
    }

    public function kasusDisiplin()
    {
        return $this->hasOne(KasusDisiplin::class, 'surat_keluar_id');
    }

    /**
     * Konversi bulan angka ke Romawi untuk nomor surat dinas.
     */
    public static function romawiBulan(int $bulan): string
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $map[$bulan] ?? 'I';
    }

    /**
     * Generate nomor surat lengkap otomatis sesuai standar Kemendikdasmen:
     * Format: [Nomor Urut]/[Kode Klasifikasi]/SMKN1AN/[Bulan Romawi]/[Tahun]
     */
    public static function generateNomorSurat(string $kodeKlasifikasi, ?\DateTimeInterface $tanggal = null): array
    {
        $tgl = $tanggal ? \Carbon\Carbon::parse($tanggal) : now();
        $year = (int) $tgl->format('Y');
        $month = (int) $tgl->format('n');
        $romawi = static::romawiBulan($month);

        $max = static::where('tahun_agenda', $year)->max('nomor_agenda');
        $nextNomor = ($max ? (int) $max : 0) + 1;

        $padNomor = str_pad((string) $nextNomor, 3, '0', STR_PAD_LEFT);
        $nomorLengkap = "{$padNomor}/{$kodeKlasifikasi}/SMKN1AN/{$romawi}/{$year}";

        return [
            'nomor_agenda'        => $nextNomor,
            'tahun_agenda'        => $year,
            'nomor_surat_lengkap' => $nomorLengkap,
        ];
    }

    /**
     * Sinkronisasi Surat Panggilan Orang Tua (SIRANI Kesiswaan) ke Buku Agenda Surat Keluar SITUAN.
     */
    public static function syncSuratPanggilanOrtu(Siswa $siswa, ?NotifikasiOrtu $notifikasi = null, ?string $rombelNama = null, ?\DateTimeInterface $tanggal = null): self
    {
        $tgl = $tanggal ? \Carbon\Carbon::parse($tanggal) : now();

        if ($notifikasi && $notifikasi->surat_keluar_id) {
            $existing = static::find($notifikasi->surat_keluar_id);
            if ($existing) {
                return $existing;
            }
        }

        $existing = static::where('kode_klasifikasi', '421.3')
            ->where('tahun_agenda', (int)$tgl->format('Y'))
            ->where('kategori_surat', 'Panggilan Orang Tua')
            ->where('tujuan_surat', 'like', "%{$siswa->nama}%")
            ->whereDate('tanggal_surat', $tgl->toDateString())
            ->first();

        if ($existing) {
            if ($notifikasi && !$notifikasi->surat_keluar_id) {
                $notifikasi->update(['surat_keluar_id' => $existing->id]);
            }
            return $existing;
        }

        $gen = static::generateNomorSurat('421.3', $tgl);
        $klasifikasi = KlasifikasiSurat::where('kode', '421.3')->first();
        $linkCetak = $notifikasi 
            ? route('surat.cetak', $notifikasi->id) 
            : (route('surat.cetak') . '?siswa_id=' . $siswa->id . '&kategori=panggilan_ortu');

        $surat = static::create([
            'nomor_agenda'        => $gen['nomor_agenda'],
            'tahun_agenda'        => $gen['tahun_agenda'],
            'klasifikasi_id'      => $klasifikasi?->id,
            'kode_klasifikasi'    => '421.3',
            'nomor_surat_lengkap' => $gen['nomor_surat_lengkap'],
            'tujuan_surat'        => "Orang Tua / Wali Siswa: {$siswa->nama} (NISN: " . ($siswa->nisn ?: '-') . ")",
            'perihal'             => "Surat Panggilan Orang Tua / Wali Murid: {$siswa->nama}" . ($rombelNama ? " ({$rombelNama})" : ''),
            'tanggal_surat'       => $tgl->toDateString(),
            'penandatangan'       => 'Kepala Sekolah',
            'jenis_surat'         => 'suket_siswa',
            'sumber_modul'        => 'sirani_kesiswaan',
            'kategori_surat'      => 'Panggilan Orang Tua',
            'link_cetak'          => $linkCetak,
            'created_by'          => auth()->id(),
        ]);

        if ($notifikasi) {
            $notifikasi->update(['surat_keluar_id' => $surat->id]);
        }

        return $surat;
    }

    /**
     * Sinkronisasi Berita Acara Pembinaan BK (SIRANI BK) ke Buku Agenda Surat Keluar SITUAN.
     */
    public static function syncBeritaAcaraBk(Siswa $siswa, ?NotifikasiOrtu $notifikasi = null, ?string $rombelNama = null, ?\DateTimeInterface $tanggal = null): self
    {
        $tgl = $tanggal ? \Carbon\Carbon::parse($tanggal) : now();

        if ($notifikasi && $notifikasi->surat_keluar_id) {
            $existing = static::find($notifikasi->surat_keluar_id);
            if ($existing && $existing->kategori_surat === 'Berita Acara BK') {
                return $existing;
            }
        }

        $existing = static::where('kode_klasifikasi', '421.3')
            ->where('tahun_agenda', (int)$tgl->format('Y'))
            ->where('kategori_surat', 'Berita Acara BK')
            ->where('tujuan_surat', 'like', "%{$siswa->nama}%")
            ->whereDate('tanggal_surat', $tgl->toDateString())
            ->first();

        if ($existing) {
            return $existing;
        }

        $year = (int) $tgl->format('Y');
        $month = (int) $tgl->format('n');
        $romawi = static::romawiBulan($month);

        $max = static::where('tahun_agenda', $year)->max('nomor_agenda');
        $nextNomor = ($max ? (int) $max : 0) + 1;
        $padNomor = str_pad((string) $nextNomor, 3, '0', STR_PAD_LEFT);

        // Format khusus Berita Acara BK yang mengandung 'BA-BK' dan standar penomoran dinas
        $nomorLengkap = "{$padNomor}/BA-BK/421.3/SMKN1AN/{$romawi}/{$year}";
        $klasifikasi = KlasifikasiSurat::where('kode', '421.3')->first();
        $linkCetak = $notifikasi 
            ? (route('surat.cetak', $notifikasi->id) . '?kategori=berita_acara') 
            : (route('surat.cetak') . '?siswa_id=' . $siswa->id . '&kategori=berita_acara');

        return static::create([
            'nomor_agenda'        => $nextNomor,
            'tahun_agenda'        => $year,
            'klasifikasi_id'      => $klasifikasi?->id,
            'kode_klasifikasi'    => '421.3',
            'nomor_surat_lengkap' => $nomorLengkap,
            'tujuan_surat'        => "Orang Tua / Wali Siswa: {$siswa->nama}",
            'perihal'             => "Berita Acara Tindak Lanjut & Musyawarah BK: {$siswa->nama}" . ($rombelNama ? " ({$rombelNama})" : ''),
            'tanggal_surat'       => $tgl->toDateString(),
            'penandatangan'       => 'Guru BK & Kepala Sekolah',
            'jenis_surat'         => 'umum',
            'sumber_modul'        => 'sirani_bk',
            'kategori_surat'      => 'Berita Acara BK',
            'link_cetak'          => $linkCetak,
            'created_by'          => auth()->id(),
        ]);
    }

    /**
     * Sinkronisasi Surat Keterangan Bebas Masalah (SIRANI Disiplin) ke Buku Agenda Surat Keluar SITUAN.
     */
    public static function syncSuratBebasMasalah(Siswa $siswa, ?\DateTimeInterface $tanggal = null): self
    {
        $tgl = $tanggal ? \Carbon\Carbon::parse($tanggal) : now();

        $existing = static::where('kode_klasifikasi', '421.5')
            ->where('tahun_agenda', (int)$tgl->format('Y'))
            ->where('kategori_surat', 'Suket Bebas Masalah')
            ->where('tujuan_surat', 'like', "%{$siswa->nama}%")
            ->first();

        if ($existing) {
            return $existing;
        }

        $gen = static::generateNomorSurat('421.5', $tgl);
        $klasifikasi = KlasifikasiSurat::where('kode', '421.5')->first();
        $linkCetak = route('siswa.surat-bebas-masalah', $siswa->id);

        return static::create([
            'nomor_agenda'        => $gen['nomor_agenda'],
            'tahun_agenda'        => $gen['tahun_agenda'],
            'klasifikasi_id'      => $klasifikasi?->id,
            'kode_klasifikasi'    => '421.5',
            'nomor_surat_lengkap' => $gen['nomor_surat_lengkap'],
            'tujuan_surat'        => "Siswa: {$siswa->nama} (NISN: " . ($siswa->nisn ?: '-') . ")",
            'perihal'             => "Surat Keterangan Bebas Kasus Disiplin & Resume Presensi Siswa: {$siswa->nama}",
            'tanggal_surat'       => $tgl->toDateString(),
            'penandatangan'       => 'Kepala Sekolah',
            'jenis_surat'         => 'suket_siswa',
            'sumber_modul'        => 'sirani_disiplin',
            'kategori_surat'      => 'Suket Bebas Masalah',
            'link_cetak'          => $linkCetak,
            'created_by'          => auth()->id(),
        ]);
    }

    /**
     * Sinkronisasi Surat Keputusan (SK) Sanksi Disiplin ke Buku Register SK dan Surat Keluar SITUAN.
     */
    public static function syncSkKasusDisiplin(KasusDisiplin $kasus, ?\DateTimeInterface $tanggal = null): array
    {
        $tgl = $tanggal ? \Carbon\Carbon::parse($tanggal) : now();
        $siswa = $kasus->siswa;
        $year = (int)$tgl->format('Y');
        $month = (int)$tgl->format('n');
        $romawi = static::romawiBulan($month);

        $bukuSk = null;
        if ($kasus->buku_sk_id) {
            $bukuSk = BukuSkKepsek::find($kasus->buku_sk_id);
        }

        if (!$bukuSk) {
            $nextUrut = BukuSkKepsek::nextNomorUrut($year);
            $padUrut = str_pad((string)$nextUrut, 3, '0', STR_PAD_LEFT);
            $nomorSkLengkap = "{$padUrut}/421.5/SMKN1AN/SK-DISIPLIN/{$romawi}/{$year}";

            $bukuSk = BukuSkKepsek::create([
                'nomor_urut_sk'      => $nextUrut,
                'tahun_sk'           => $year,
                'nomor_sk_lengkap'   => $nomorSkLengkap,
                'tentang_sk'         => "Penetapan Sanksi & Pembinaan Kesiswaan Terpadu a.n. " . ($siswa?->nama ?? 'Siswa'),
                'tanggal_ditetapkan' => $tgl->toDateString(),
                'kategori_sk'        => 'Kesiswaan & Disiplin',
                'is_active'          => true,
            ]);

            $kasus->update(['buku_sk_id' => $bukuSk->id]);
        }

        $suratKeluar = null;
        if ($kasus->surat_keluar_id) {
            $suratKeluar = static::find($kasus->surat_keluar_id);
        }

        if (!$suratKeluar) {
            $maxAgenda = static::where('tahun_agenda', $year)->max('nomor_agenda');
            $nextAgenda = ($maxAgenda ? (int)$maxAgenda : 0) + 1;
            $padAgenda = str_pad((string)$nextAgenda, 3, '0', STR_PAD_LEFT);
            $nomorAgendaLengkap = "{$padAgenda}/421.5/SMKN1AN/SK-DISIPLIN/{$romawi}/{$year}";
            $klasifikasi = KlasifikasiSurat::where('kode', '421.5')->first();
            $linkCetak = route('admin.disiplin.sk.cetak', $kasus->id);

            $suratKeluar = static::create([
                'nomor_agenda'        => $nextAgenda,
                'tahun_agenda'        => $year,
                'klasifikasi_id'      => $klasifikasi?->id,
                'kode_klasifikasi'    => '421.5',
                'nomor_surat_lengkap' => $nomorAgendaLengkap,
                'tujuan_surat'        => "Siswa & Orang Tua: " . ($siswa?->nama ?? 'Siswa'),
                'perihal'             => "SK Penetapan Sanksi Kedisiplinan Siswa: " . ($siswa?->nama ?? 'Siswa'),
                'tanggal_surat'       => $tgl->toDateString(),
                'penandatangan'       => 'Kepala Sekolah',
                'jenis_surat'         => 'sk_kepsek',
                'sumber_modul'        => 'sirani_disiplin',
                'kategori_surat'      => 'SK Penetapan Sanksi',
                'link_cetak'          => $linkCetak,
                'created_by'          => auth()->id(),
            ]);

            $kasus->update(['surat_keluar_id' => $suratKeluar->id]);
        }

        return [
            'buku_sk'      => $bukuSk,
            'surat_keluar' => $suratKeluar,
            'nomor_sk'     => $suratKeluar->nomor_surat_lengkap,
        ];
    }

    /**
     * Sinkronisasi Surat Pengantar Kenaikan Gaji Berkala (SITUAN Kepegawaian) ke Buku Agenda Surat Keluar.
     */
    public static function syncPengantarKgb(Guru $guru, ?\DateTimeInterface $tanggal = null): self
    {
        $tgl = $tanggal ? \Carbon\Carbon::parse($tanggal) : now();
        $year = (int)$tgl->format('Y');

        $existing = static::where('kode_klasifikasi', '821.2')
            ->where('tahun_agenda', $year)
            ->where('kategori_surat', 'Pengantar KGB')
            ->where('tujuan_surat', 'like', "%{$guru->nama}%")
            ->first();

        if ($existing) {
            return $existing;
        }

        $gen = static::generateNomorSurat('821.2', $tgl);
        $klasifikasi = KlasifikasiSurat::where('kode', '821.2')->first();
        $linkCetak = route('situan.radar-kgb.cetak-pengantar', $guru->id);

        return static::create([
            'nomor_agenda'        => $gen['nomor_agenda'],
            'tahun_agenda'        => $gen['tahun_agenda'],
            'klasifikasi_id'      => $klasifikasi?->id,
            'kode_klasifikasi'    => '821.2',
            'nomor_surat_lengkap' => $gen['nomor_surat_lengkap'],
            'tujuan_surat'        => "Kepala Dinas Pendidikan dan Kebudayaan Provinsi Lampung (a.n. {$guru->nama})",
            'perihal'             => "Pengantar Kenaikan Gaji Berkala (KGB) a.n. {$guru->nama} (NIP: " . ($guru->nip ?: '-') . ")",
            'tanggal_surat'       => $tgl->toDateString(),
            'penandatangan'       => 'Kepala Sekolah',
            'jenis_surat'         => 'surat_tugas',
            'sumber_modul'        => 'situan_kepegawaian',
            'kategori_surat'      => 'Pengantar KGB',
            'link_cetak'          => $linkCetak,
            'created_by'          => auth()->id(),
        ]);
    }
}
