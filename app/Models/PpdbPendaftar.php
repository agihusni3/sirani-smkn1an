<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpdbPendaftar extends Model
{
    use HasFactory;

    protected $table = 'ppdb_pendaftars';

    protected $fillable = [
        'no_pendaftaran',
        'nisn',
        'nik',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'asal_sekolah',
        'tahun_lulus',
        'hobi',
        'organisasi_minat',
        'alamat',
        'nama_ayah',
        'pekerjaan_ayah',
        'pendidikan_ayah',
        'no_hp_ayah',
        'nama_ibu',
        'pekerjaan_ibu',
        'pendidikan_ibu',
        'no_hp_ibu',
        'nama_wali',
        'pekerjaan_ortu',
        'no_hp_ortu',
        'no_hp_siswa',
        'jurusan_id_1',
        'jurusan_id_2',
        'jalur_pendaftaran',
        'nilai_rata_rata',
        'berkas_foto',
        'berkas_kk',
        'berkas_ijazah_skl',
        'berkas_ktp_ortu',
        'berkas_akta',
        'berkas_kip',
        'berkas_sktm',
        'status',
        'jadwal_tes_tanggal',
        'jadwal_tes_sesi',
        'jadwal_tes_ruang',
        'nilai_tes_tertulis',
        'catatan_tes_tertulis',
        'nilai_wawancara_motivasi',
        'nilai_wawancara_karakter',
        'nilai_wawancara_kejuruan',
        'nilai_wawancara_ortu',
        'nilai_wawancara_total',
        'catatan_wawancara',
        'pewawancara_id',
        'diwawancara_pada',
        'nilai_akhir',
        'peringkat_jurusan',
        'rekomendasi_jurusan_id',
        'jurusan_diterima_id',
        'catatan_panitia',
        'diverifikasi_oleh',
        'diverifikasi_pada',
        'siswa_id',
        'dimutasi_pada',
    ];

    protected $casts = [
        'tanggal_lahir'            => 'date',
        'jadwal_tes_tanggal'       => 'date',
        'diwawancara_pada'         => 'datetime',
        'nilai_rata_rata'          => 'decimal:2',
        'nilai_tes_tertulis'       => 'decimal:2',
        'nilai_wawancara_motivasi' => 'decimal:2',
        'nilai_wawancara_karakter' => 'decimal:2',
        'nilai_wawancara_kejuruan' => 'decimal:2',
        'nilai_wawancara_ortu'     => 'decimal:2',
        'nilai_wawancara_total'    => 'decimal:2',
        'nilai_akhir'              => 'decimal:2',
        'diverifikasi_pada'        => 'datetime',
        'dimutasi_pada'            => 'datetime',
    ];

    public function jurusan1()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id_1');
    }

    public function jurusan2()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id_2');
    }

    public function jurusanPilihan1()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id_1');
    }

    public function jurusanPilihan2()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id_2');
    }

    public function jurusanDiterima()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_diterima_id');
    }

    public function pewawancara()
    {
        return $this->belongsTo(User::class, 'pewawancara_id');
    }

    public function rekomendasiJurusan()
    {
        return $this->belongsTo(Jurusan::class, 'rekomendasi_jurusan_id');
    }

    public function ujianPeserta()
    {
        return $this->hasOne(PpdbUjianPeserta::class, 'ppdb_pendaftar_id');
    }

    public function absensiUjian()
    {
        return $this->hasOne(PpdbAbsensiUjian::class, 'ppdb_pendaftar_id')->latestOfMany('waktu_hadir');
    }

    public function absensiUjians()
    {
        return $this->hasMany(PpdbAbsensiUjian::class, 'ppdb_pendaftar_id');
    }

    public function isSudahAbsenUjian($tanggal = null): bool
    {
        $query = $this->absensiUjians();
        if ($tanggal) {
            $query->whereDate('jadwal_tanggal', $tanggal);
        }
        return $query->exists();
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Hitung nilai wawancara dari 4 kriteria:
     * 1. Motivasi & Minat: 25%
     * 2. Karakter & Sikap: 25%
     * 3. Uji Khusus Kejuruan / Bebas Buta Warna: 30%
     * 4. Komitmen Orang Tua: 20%
     */
    public function hitungNilaiWawancara(): float
    {
        $m = (float) ($this->nilai_wawancara_motivasi ?: 0);
        $k = (float) ($this->nilai_wawancara_karakter ?: 0);
        $j = (float) ($this->nilai_wawancara_kejuruan ?: 0);
        $o = (float) ($this->nilai_wawancara_ortu ?: 0);

        $total = round((0.25 * $m) + (0.25 * $k) + (0.30 * $j) + (0.20 * $o), 2);
        $this->nilai_wawancara_total = $total;
        return $total;
    }

    /**
     * Hitung skor akhir seleksi terbobot:
     * - Nilai Rapor / Administrasi: 30%
     * - Nilai Tes Tertulis (PG + Esai): 35%
     * - Nilai Tes Wawancara: 35%
     */
    public function hitungNilaiAkhir(): float
    {
        $rapor = (float) ($this->nilai_rata_rata ?: 0);
        $tertulis = (float) ($this->nilai_tes_tertulis ?: 0);
        $wawancara = (float) ($this->nilai_wawancara_total ?: $this->hitungNilaiWawancara());

        // Jika nilai rapor dalam skala 10 atau puluhan (misal 7.8 atau 78)
        if ($rapor > 0 && $rapor <= 10) {
            $rapor = $rapor * 10; // normalisasi ke skala 0-100
        }

        $akhir = round((0.30 * $rapor) + (0.35 * $tertulis) + (0.35 * $wawancara), 2);
        $this->nilai_akhir = $akhir;
        return $akhir;
    }

    /**
     * Alias generator nomor pendaftaran
     */
    public static function generateNomor(): string
    {
        return self::generateNomorPendaftaran();
    }

    /**
     * Generate nomor pendaftaran otomatis: PPDB-YYYY-XXXXX
     */
    public static function generateNomorPendaftaran(): string
    {
        $year = date('Y');
        $prefix = "PPDB-{$year}-";
        $last = self::where('no_pendaftaran', 'like', "{$prefix}%")
                    ->orderBy('id', 'desc')
                    ->first();

        if (!$last) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($last->no_pendaftaran, strlen($prefix));
        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }

    // Accessors & Mutators untuk kompatibilitas
    public function getNomorPendaftaranAttribute() { return $this->attributes['no_pendaftaran'] ?? ''; }
    public function setNomorPendaftaranAttribute($value) { $this->attributes['no_pendaftaran'] = $value; }

    public function getAlamatLengkapAttribute() { return $this->attributes['alamat'] ?? ''; }
    public function setAlamatLengkapAttribute($value) { $this->attributes['alamat'] = $value; }

    public function getPasFotoAttribute() { return $this->attributes['berkas_foto'] ?? null; }
    public function setPasFotoAttribute($value) { $this->attributes['berkas_foto'] = $value; }

    public function getScanKkAttribute() { return $this->attributes['berkas_kk'] ?? null; }
    public function setScanKkAttribute($value) { $this->attributes['berkas_kk'] = $value; }

    public function getScanIjazahSklAttribute() { return $this->attributes['berkas_ijazah_skl'] ?? null; }
    public function setScanIjazahSklAttribute($value) { $this->attributes['berkas_ijazah_skl'] = $value; }

    public function getScanKtpOrtuAttribute() { return $this->attributes['berkas_ktp_ortu'] ?? null; }
    public function setScanKtpOrtuAttribute($value) { $this->attributes['berkas_ktp_ortu'] = $value; }

    public function getScanAktaAttribute() { return $this->attributes['berkas_akta'] ?? null; }
    public function setScanAktaAttribute($value) { $this->attributes['berkas_akta'] = $value; }

    public function getScanKipAttribute() { return $this->attributes['berkas_kip'] ?? null; }
    public function setScanKipAttribute($value) { $this->attributes['berkas_kip'] = $value; }

    public function getScanSktmAttribute() { return $this->attributes['berkas_sktm'] ?? null; }
    public function setScanSktmAttribute($value) { $this->attributes['berkas_sktm'] = $value; }

    public function getJurusanPilihan1IdAttribute() { return $this->attributes['jurusan_id_1'] ?? null; }
    public function setJurusanPilihan1IdAttribute($value) { $this->attributes['jurusan_id_1'] = $value; }

    public function getJurusanPilihan2IdAttribute() { return $this->attributes['jurusan_id_2'] ?? null; }
    public function setJurusanPilihan2IdAttribute($value) { $this->attributes['jurusan_id_2'] = $value; }

    public function getStatusPendaftaranAttribute() 
    { 
        $st = $this->attributes['status'] ?? 'menunggu_verifikasi';
        if ($st === 'menunggu_verifikasi') return 'menunggu';
        if ($st === 'terverifikasi') return 'berkas_valid';
        return $st;
    }

    public function setStatusPendaftaranAttribute($value) 
    { 
        if ($value === 'menunggu') $value = 'menunggu_verifikasi';
        if ($value === 'berkas_valid') $value = 'terverifikasi';
        $this->attributes['status'] = $value; 
    }

    public function getCatatanAttribute() { return $this->attributes['catatan_panitia'] ?? null; }
    public function setCatatanAttribute($value) { $this->attributes['catatan_panitia'] = $value; }

    public function getTahunAjaranAttribute() { return date('Y') . '/' . (date('Y') + 1); }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'terverifikasi'       => 'Berkas Terverifikasi',
            'diterima'            => 'Lulus / Diterima',
            'cadangan'            => 'Cadangan',
            'ditolak'             => 'Tidak Lolos',
            default               => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'badge-warning',
            'terverifikasi'       => 'badge-info',
            'diterima'            => 'badge-success',
            'cadangan'            => 'badge-secondary',
            'ditolak'             => 'badge-danger',
            default               => 'badge-dark',
        };
    }
}
