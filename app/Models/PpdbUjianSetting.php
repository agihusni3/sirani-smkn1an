<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpdbUjianSetting extends Model
{
    use HasFactory;

    protected $table = 'ppdb_ujian_settings';

    protected $fillable = [
        'judul_ujian',
        'tahun_ajaran',
        'jumlah_soal_pg',
        'jumlah_soal_esai',
        'kunci_jawaban_pg',  // dipertahankan untuk backward compat, tapi kunci utama sekarang di tiap soal
        'bobot_pg',
        'bobot_esai',
        'durasi_menit',
        'tanggal_pelaksanaan',
        'jam_mulai',
        'jam_selesai',
        'sesi_default',
        'daftar_sesi',
        'ruang_default',
        'gelombang_label',
        'is_active',
        'petunjuk_ujian',
        'materi_wawancara',
        'buka_pada',
        'tutup_pada',
        'created_by',
    ];

    protected $casts = [
        'tanggal_pelaksanaan' => 'date',
        'daftar_sesi'         => 'array',
        'kunci_jawaban_pg'    => 'array',
        'materi_wawancara'    => 'array',
        'bobot_pg'            => 'decimal:2',
        'bobot_esai'          => 'decimal:2',
        'is_active'           => 'boolean',
        'buka_pada'           => 'datetime',
        'tutup_pada'          => 'datetime',
    ];

    /**
     * Default template materi & rubrik wawancara PPDB
     */
    public static function getDefaultMateriWawancara(): array
    {
        return [
            'motivasi' => [
                'judul'      => 'Motivasi, Minat & Orientasi Masa Depan',
                'bobot'      => 25,
                'tujuan'     => 'Menilai kemauan murni belajar di SMK dan kejelasan target karir setelah lulus.',
                'pertanyaan' => [
                    'Mengapa Anda memilih SMKN 1 Air Naningan? Apakah ini murni pilihan dan kemauan Anda sendiri?',
                    'Apa rencana Anda setelah lulus nanti? Ingin langsung bekerja di industri, berwirausaha mandiri, atau kuliah?',
                    'Sejauh mana Anda mengetahui peluang kerja atau kompetensi dari jurusan yang dipilih?',
                ],
                'rubrik'     => [
                    '85 - 100' => 'Tujuan karir jelas, kemauan sendiri, pemahaman jurusan matang.',
                    '70 - 84'  => 'Minat baik, namun rencana masa depan masih bersifat umum.',
                    '< 70'     => 'Pasif, ikut-ikutan teman, atau terpaksa atas dorongan pihak lain.',
                ],
            ],
            'karakter' => [
                'judul'      => 'Karakter, Sikap, Integritas & Disiplin',
                'bobot'      => 25,
                'tujuan'     => 'Menilai sopan santun, kejujuran, komitmen jam masuk 07.00 WIB, dan kepatuhan tata tertib sekolah.',
                'pertanyaan' => [
                    'Apakah Anda siap mematuhi aturan disiplin: masuk pukul 07.00 WIB, seragam rapi, rambut pendek (putra), dan larangan merokok/vape baik di dalam maupun luar sekolah?',
                    'Bagaimana respon Anda jika sewaktu-waktu ditegur atau dibimbing guru atas suatu kekhilafan?',
                    'Bagaimana riwayat kedisiplinan dan absensi Anda selama di SMP/MTs?',
                ],
                'rubrik'     => [
                    '85 - 100' => 'Sopan santun luar biasa, jujur, komitmen disiplin sangat kuat.',
                    '70 - 84'  => 'Sikap wajar, terbuka untuk dibimbing dan mematuhi tata tertib.',
                    '< 70'     => 'Defensif, acuh tak acuh, atau ada catatan indisipliner berat.',
                ],
            ],
            'kejuruan_rpl' => [
                'judul'      => 'Kesiapan Kejuruan RPL (Rekayasa Perangkat Lunak)',
                'pertanyaan' => [
                    'Apakah Anda siap duduk berkonsentrasi berjam-jam memecahkan logika kode komputer?',
                    'Pernahkah memakai PC/laptop atau siap disiplin memanfaatkan lab komputer sekolah?',
                ],
                'uji_fisik'  => 'Cek Bebas Buta Warna (Ishihara) untuk membedakan desain UI/UX dan kode warna sintaks pemrograman.',
                'rubrik'     => [
                    '85 - 100' => 'Logika analitis baik, antusias coding, bebas buta warna.',
                    '70 - 84'  => 'Siap belajar walau belum memiliki pengalaman memakai komputer.',
                    '< 70'     => 'Hanya ingin bermain game, buta warna total.',
                ],
            ],
            'kejuruan_aphp' => [
                'judul'      => 'Kesiapan Kejuruan APHP (Agribisnis Pengolahan Hasil Pertanian)',
                'pertanyaan' => [
                    'Apakah Anda siap beraktivitas aktif di lab pengolahan/dapur produksi yang bersuhu hangat dan mencuci peralatan olahan pangan?',
                    'Tertarikkah mengolah komoditas lokal (kopi, pisang, rempah Lampung) menjadi produk kuliner bernilai jual?',
                ],
                'uji_fisik'  => 'Kebersihan kuku & tangan (personal hygiene pangan), riwayat alergi bahan baku, bebas buta warna sortasi mutu panen.',
                'rubrik'     => [
                    '85 - 100' => 'Higienis, antusias industri olahan pangan/kuliner, fisik prima.',
                    '70 - 84'  => 'Siap belajar dan mengikuti seluruh praktik dapur pengolahan.',
                    '< 70'     => 'Jijik/enggan kotor dengan bahan pangan mentah, menolak kerja dapur.',
                ],
            ],
            'kejuruan_tsm' => [
                'judul'      => 'Kesiapan Kejuruan TSM (Teknik & Bisnis Sepeda Motor)',
                'pertanyaan' => [
                    'Apakah Anda siap menghadapi oli, debu, dan kotoran saat membongkar mesin di bengkel?',
                    'Siapkah mematuhi SOP Keselamatan Kerja (K3) bengkel secara ketat (wearpack, sepatu safety)?',
                ],
                'uji_fisik'  => 'Cek Bebas Buta Warna MUTLAK (diagram warna kabel kelistrikan motor) dan ketahanan fisik gerak mekanik.',
                'rubrik'     => [
                    '85 - 100' => 'Bebas buta warna, minat mekanik tinggi, fisik prima dan siap kerja bengkel.',
                    '70 - 84'  => 'Bebas buta warna, fisik sehat, siap dibina dari nol.',
                    '< 70'     => 'Mengalami buta warna total (berisiko fatal korsleting), atau takut kotor terkena oli mesin.',
                ],
            ],
            'ortu' => [
                'judul'      => 'Dukungan & Komitmen Orang Tua / Wali',
                'bobot'      => 20,
                'tujuan'     => 'Menilai kesiapan orang tua mendampingi siswa, hadir rapat sekolah, serta pembiayaan magang PKL industri 6 bulan.',
                'pertanyaan' => [
                    'Apakah orang tua/wali sepenuhnya menyetujui jurusan ini dan bersedia hadir bila diundang pihak sekolah?',
                    'Apakah orang tua siap mendukung kebutuhan praktik dan pelaksanaan Magang / PKL industri 6 bulan di luar sekolah?',
                ],
                'rubrik'     => [
                    '85 - 100' => 'Orang tua mendukung penuh moral & material, menyetujui program PKL industri.',
                    '70 - 84'  => 'Orang tua mendukung wajar dan siap bekerja sama dengan sekolah.',
                    '< 70'     => 'Orang tua lepas tangan atau menentang jurusan pilihan anak.',
                ],
            ],
        ];
    }

    /**
     * Mengambil materi wawancara aktif (digabung dengan default bila ada kriteria kosong)
     */
    public function getMateriWawancaraAktifAttribute(): array
    {
        $default = self::getDefaultMateriWawancara();
        $custom = (array) ($this->materi_wawancara ?? []);
        if (empty($custom)) {
            return $default;
        }

        $result = $default;
        foreach ($custom as $secKey => $secVal) {
            if (!is_array($secVal)) {
                $result[$secKey] = $secVal;
                continue;
            }
            if (!isset($result[$secKey])) {
                $result[$secKey] = $secVal;
                continue;
            }
            foreach ($secVal as $k => $v) {
                if ($k === 'pertanyaan' && is_array($v)) {
                    // Timpa penuh daftar pertanyaan kustom
                    $result[$secKey]['pertanyaan'] = $v;
                } elseif ($k === 'rubrik' && is_array($v)) {
                    $result[$secKey]['rubrik'] = array_merge($result[$secKey]['rubrik'] ?? [], $v);
                } else {
                    $result[$secKey][$k] = $v;
                }
            }
        }
        return $result;
    }

    /**
     * Mengambil format rentang jam pelaksanaan ujian (Jam Mulai - Selesai)
     */
    public function getWaktuPelaksanaanAttribute(): string
    {
        $mulai = $this->jam_mulai ? str_replace(':', '.', substr($this->jam_mulai, 0, 5)) : '08.00';
        $selesai = $this->jam_selesai ? str_replace(':', '.', substr($this->jam_selesai, 0, 5)) : '10.00';
        return "{$mulai} - {$selesai} WIB";
    }

    /**
     * Mengambil daftar sesi ujian yang telah dikonfigurasi admin
     */
    public function getDaftarSesiListAttribute(): array
    {
        if (!empty($this->daftar_sesi) && is_array($this->daftar_sesi)) {
            return $this->daftar_sesi;
        }

        return [
            ['nama' => 'Sesi 1', 'waktu' => '08.00 - 10.00 WIB', 'label' => 'Sesi 1 (08.00 - 10.00 WIB)'],
            ['nama' => 'Sesi 2', 'waktu' => '10.30 - 12.30 WIB', 'label' => 'Sesi 2 (10.30 - 12.30 WIB)'],
            ['nama' => 'Sesi 3', 'waktu' => '13.30 - 15.30 WIB', 'label' => 'Sesi 3 (13.30 - 15.30 WIB)'],
        ];
    }

    /**
     * Mengambil array string label sesi untuk dropdown pilihan
     */
    public function getSesiOptionsAttribute(): array
    {
        $list = $this->daftar_sesi_list;
        $options = [];
        foreach ($list as $s) {
            $label = is_array($s) ? ($s['label'] ?? ($s['nama'] . ' (' . ($s['waktu'] ?? '') . ')')) : (string) $s;
            $options[] = trim($label);
        }
        return array_values(array_unique(array_filter($options)));
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pesertas()
    {
        return $this->hasMany(PpdbUjianPeserta::class, 'ppdb_ujian_setting_id');
    }

    public function absensiUjians()
    {
        return $this->hasMany(PpdbAbsensiUjian::class, 'ppdb_ujian_setting_id');
    }

    public function soals()
    {
        return $this->hasMany(PpdbSoalUjian::class, 'ppdb_ujian_setting_id')->orderBy('nomor_urut');
    }

    public function soalPg()
    {
        return $this->hasMany(PpdbSoalUjian::class, 'ppdb_ujian_setting_id')
                    ->where('tipe_soal', 'pg')->orderBy('nomor_urut');
    }

    public function soalEsai()
    {
        return $this->hasMany(PpdbSoalUjian::class, 'ppdb_ujian_setting_id')
                    ->where('tipe_soal', 'esai')->orderBy('nomor_urut');
    }

    /** Dapatkan setting ujian aktif saat ini */
    public static function getAktif(): ?self
    {
        $setting = self::where('is_active', true)->latest()->first();
        if (!$setting) {
            $setting = self::latest()->first();
        }
        return $setting;
    }

    /**
     * Hitung koreksi otomatis jawaban PG peserta.
     * Jawaban siswa: ['nomor_urut' => 'A', ...] (key = nomor_urut asli soal)
     */
    public function koreksiPg(array $jawabanSiswa): array
    {
        // Ambil soal PG beserta kunci dari bank soal
        $soalPg = $this->soalPg()->get()->keyBy('nomor_urut');

        $benar  = 0;
        $salah  = 0;
        $kosong = 0;
        $totalPg = max($soalPg->count(), (int) ($this->jumlah_soal_pg ?: 30));

        // Deteksi secara akurat apakah key jawaban siswa menggunakan ID tabel atau nomor_urut
        $keys = array_map('strval', array_keys($jawabanSiswa));
        $ids = $soalPg->pluck('id')->map(fn($id) => (string) $id)->all();
        $nomors = $soalPg->keys()->map(fn($n) => (string) $n)->all();

        $idMatches = count(array_intersect($keys, $ids));
        $nomorMatches = count(array_intersect($keys, $nomors));
        $isKeyedById = ($idMatches > $nomorMatches);

        foreach ($soalPg as $nomor => $soal) {
            $kunci = $soal->kunci_jawaban ? strtoupper(trim($soal->kunci_jawaban)) : null;
            $jawab = null;

            if ($isKeyedById) {
                $jawab = isset($jawabanSiswa[(string) $soal->id]) ? strtoupper(trim($jawabanSiswa[(string) $soal->id])) : null;
            } else {
                $jawab = isset($jawabanSiswa[(string) $nomor]) ? strtoupper(trim($jawabanSiswa[(string) $nomor])) : null;
            }

            if (empty($jawab)) {
                $kosong++;
            } elseif ($kunci && $jawab === $kunci) {
                $benar++;
            } else {
                $salah++;
            }
        }

        // Fallback ke kunci lama jika bank soal kosong
        if ($soalPg->isEmpty()) {
            return $this->koreksiPgLegacy($jawabanSiswa);
        }

        $bobotPg = (float) ($this->bobot_pg ?: 70.00);
        $skorPg  = $totalPg > 0 ? round(($benar / $totalPg) * $bobotPg, 2) : 0.00;

        return [
            'benar'   => $benar,
            'salah'   => $salah,
            'kosong'  => $kosong,
            'skor_pg' => $skorPg,
            'maks_pg' => $bobotPg,
        ];
    }

    /** Fallback koreksi PG dari kolom kunci_jawaban_pg lama */
    protected function koreksiPgLegacy(array $jawabanSiswa): array
    {
        $kunci   = $this->kunci_jawaban_pg ?: [];
        $totalPg = (int) ($this->jumlah_soal_pg ?: 30);
        $benar = $salah = $kosong = 0;

        for ($i = 1; $i <= $totalPg; $i++) {
            $k = isset($kunci[(string) $i]) ? strtoupper(trim($kunci[(string) $i])) : null;
            $j = isset($jawabanSiswa[(string) $i]) ? strtoupper(trim($jawabanSiswa[(string) $i])) : null;
            if (empty($j)) $kosong++;
            elseif ($k && $j === $k) $benar++;
            else $salah++;
        }

        $bobotPg = (float) ($this->bobot_pg ?: 70.00);
        return [
            'benar'   => $benar,
            'salah'   => $salah,
            'kosong'  => $kosong,
            'skor_pg' => $totalPg > 0 ? round(($benar / $totalPg) * $bobotPg, 2) : 0,
            'maks_pg' => $bobotPg,
        ];
    }
}
