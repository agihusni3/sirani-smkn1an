<?php

namespace Database\Seeders;

use App\Models\AkademikAtpItem;
use App\Models\AkademikDistribusiMengajar;
use App\Models\AkademikKktpItem;
use App\Models\AkademikMataPelajaran;
use App\Models\AkademikModulAjar;
use App\Models\AkademikPerangkatAjar;
use App\Models\Guru;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AkademikPerangkatPembelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ta = TahunAjaran::where('is_active', true)->first() ?? TahunAjaran::latest('id')->first();
        $guru = Guru::where('status', 'aktif')->first() ?? Guru::first();
        $kepsekUser = User::where('role', 'kepala_sekolah')->first() ?? User::where('role', 'admin')->first();

        if (!$guru) {
            return;
        }

        // 1. Mapel Informatika (Fase E, Kelas X)
        $mapelInf = AkademikMataPelajaran::where('nama_mapel', 'like', '%Informatika%')->first();
        if ($mapelInf) {
            $dist = AkademikDistribusiMengajar::where('mata_pelajaran_id', $mapelInf->id)->first();

            $perangkat = AkademikPerangkatAjar::updateOrCreate(
                [
                    'guru_id' => $dist?->guru_id ?? $guru->id,
                    'mata_pelajaran_id' => $mapelInf->id,
                    'tingkat' => 'X',
                    'semester' => 1,
                ],
                [
                    'distribusi_id' => $dist?->id,
                    'tahun_ajaran_id' => $ta?->id,
                    'fase' => 'E',
                    'status' => 'disahkan',
                    'catatan_supervisi' => 'Perangkat ajar telah memenuhi 5 pilar standar Kurikulum Merdeka SMK (BSKAP 032/2024). Alokasi JP dan instrumen asesmen lengkap dan layak disahkan.',
                    'catatan_guru' => 'Perencanaan pembelajaran semester ganjil berfokus pada Penguatan Berpikir Komputasional dan Algoritma Pemrograman.',
                    'disahkan_oleh' => $kepsekUser?->id,
                    'tanggal_pengesahan' => now()->subDays(5),
                    'qr_token_pengesahan' => 'PERANGKAT-INF-E-X01-' . strtoupper(Str::random(6)),
                    'rpe_pekan_efektif' => 18,
                    'rpe_pekan_cadangan' => 2,
                ]
            );

            // Butir ATP Informatika
            $atps = [
                [
                    'urutan' => 1,
                    'kode_tp' => 'TP 1.1',
                    'elemen_cp' => 'Berpikir Komputasional (BK)',
                    'tujuan_pembelajaran' => 'Peserta didik mampu menerapkan strategi berpikir komputasional (dekomposisi, pengenalan pola, abstraksi, dan algoritma) untuk menyelesaikan persoalan pemodelan data sederhana.',
                    'materi_pokok' => 'Strategi Berpikir Komputasional & Optimasi Solusi',
                    'alokasi_jp' => 8,
                    'profil_pancasila' => 'Bernalar Kritis, Mandiri',
                    'semester' => 1,
                    'asesmen_rencana' => 'Asesmen Formatif (Latihan Studi Kasus Dekomposisi Data)',
                ],
                [
                    'urutan' => 2,
                    'kode_tp' => 'TP 1.2',
                    'elemen_cp' => 'Teknologi Informasi dan Komunikasi (TIK)',
                    'tujuan_pembelajaran' => 'Peserta didik mampu memanfaatkan perkakas kolaborasi digital daring (cloud storage, docs terpadu, lembar sebar) serta integrasi fitur aplikasi perkantoran secara kritis dan etis.',
                    'materi_pokok' => 'Pemanfaatan Kolaborasi Daring & Integrasi Aplikasi Perkantoran',
                    'alokasi_jp' => 12,
                    'profil_pancasila' => 'Gotong Royong, Kreatif',
                    'semester' => 1,
                    'asesmen_rencana' => 'Unjuk Kerja Praktik Laboratorium Komputer & Portofolio',
                ],
                [
                    'urutan' => 3,
                    'kode_tp' => 'TP 1.3',
                    'elemen_cp' => 'Sistem Komputer (SK)',
                    'tujuan_pembelajaran' => 'Peserta didik mampu menjelaskan komponen internal sistem komputer (CPU, RAM, Storage, Bus), interaksi antar perangkat keras, sistem operasi, serta mekanisme proteksi data.',
                    'materi_pokok' => 'Arsitektur Sistem Komputer & Sistem Operasi',
                    'alokasi_jp' => 8,
                    'profil_pancasila' => 'Bernalar Kritis',
                    'semester' => 1,
                    'asesmen_rencana' => 'Kuis Formatif CBT & Observasi Identifikasi Perangkat Keras',
                ],
                [
                    'urutan' => 4,
                    'kode_tp' => 'TP 1.4',
                    'elemen_cp' => 'Algoritma dan Pemrograman (AP)',
                    'tujuan_pembelajaran' => 'Peserta didik mampu merancang diagram alir (flowchart) serta menuliskan kode program terstruktur sederhana menggunakan bahasa pemrograman tekstual.',
                    'materi_pokok' => 'Logika Algoritma & Pemrograman Prosedural',
                    'alokasi_jp' => 12,
                    'profil_pancasila' => 'Mandiri, Bernalar Kritis, Kreatif',
                    'semester' => 1,
                    'asesmen_rencana' => 'Asesmen Sumatif Lingkup Materi (Projek Kode Program Sederhana)',
                ],
            ];

            foreach ($atps as $item) {
                $atpItem = AkademikAtpItem::updateOrCreate(
                    [
                        'perangkat_id' => $perangkat->id,
                        'kode_tp' => $item['kode_tp'],
                    ],
                    $item
                );

                // Buat KKTP per butir TP
                AkademikKktpItem::updateOrCreate(
                    [
                        'perangkat_id' => $perangkat->id,
                        'atp_item_id' => $atpItem->id,
                    ],
                    [
                        'pendekatan' => 'interval_nilai',
                        'keterangan_tuntas' => 'Nilai >= 75: Peserta didik menguasai ' . $item['materi_pokok'],
                        'keterangan_remedial' => 'Nilai < 75: Diberikan modul pendampingan dan penugasan ulang terarah',
                    ]
                );
            }

            // Buat Modul Ajar Contoh
            $firstAtp = AkademikAtpItem::where('perangkat_id', $perangkat->id)->first();
            AkademikModulAjar::updateOrCreate(
                [
                    'perangkat_id' => $perangkat->id,
                    'judul_modul' => 'Modul Ajar 1: Implementasi Berpikir Komputasional dalam Dunia Vokasi',
                ],
                [
                    'atp_item_id' => $firstAtp?->id,
                    'pertemuan_ke_mulai' => 1,
                    'pertemuan_ke_selesai' => 2,
                    'alokasi_jp' => 8,
                    'model_pembelajaran' => 'Problem-Based Learning (PBL)',
                    'metode_pembelajaran' => 'Diskusi Kelompok, Studi Kasus, Praktik Terbimbing',
                    'pemahaman_bermakna' => 'Berpikir komputasional merupakan fondasi pemecahan masalah sistematis yang relevan tidak hanya di bidang TI namun juga di seluruh lini industri kejuruan modern.',
                    'pertanyaan_pemantik' => 'Bagaimana perusahaan logistik dan e-commerce menentukan rute pengiriman paket tercepat ke ribuan alamat setiap hari?',
                    'kegiatan_pendahuluan' => 'Guru menyapa, berdoa, memeriksa presensi kelas, menyampaikan tujuan pembelajaran, dan memberikan apersepsi pemantik.',
                    'kegiatan_inti' => 'Peserta didik mengamati kasus nyata algoritma rute, membentuk tim 4 orang, membedah masalah melalui 4 pilar BK (Dekomposisi, Pola, Abstraksi, Algoritma), dan mempresentasikan solusinya.',
                    'kegiatan_penutup' => 'Guru dan peserta didik menyimpulkan materi, merefleksikan kegiatan, memberikan penguatan, dan menutup sesi.',
                    'refleksi_guru_siswa' => 'Sebagian siswa memerlukan analogi lebih visual pada pilar abstraksi; pertemuan berikutnya akan menggunakan studi kasus berbasis grafis.',
                ]
            );
        }
    }
}
