<?php

namespace Database\Seeders;

use App\Models\PpdbSoalUjian;
use App\Models\PpdbUjianSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PpdbSoalUjianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengisi 30 butir soal CBT Pilihan Ganda dan 5 butir soal Esai
     * untuk Tes Seleksi Masuk SMKN 1 Air Naningan.
     */
    public function run(): void
    {
        // Dapatkan setting ujian aktif atau ID 1
        $setting = PpdbUjianSetting::where('is_active', true)->first();
        if (!$setting) {
            $setting = PpdbUjianSetting::first();
        }

        if (!$setting) {
            $setting = PpdbUjianSetting::create([
                'judul_ujian'      => 'Tes Potensi Akademik & Minat Vokasi PPDB 2026',
                'tahun_ajaran'     => '2026/2027',
                'jumlah_soal_pg'   => 30,
                'jumlah_soal_esai' => 5,
                'bobot_pg'         => 70.00,
                'bobot_esai'       => 30.00,
                'durasi_menit'     => 60,
                'is_active'        => true,
                'petunjuk_ujian'   => 'Bacalah setiap soal dengan saksama. Pilihlah satu jawaban yang paling tepat untuk soal pilihan ganda, dan berikan jawaban yang ringkas serta jelas pada soal esai.',
            ]);
        }

        $settingId = $setting->id;

        // Daftar 30 Soal Pilihan Ganda (PG)
        $soalPgData = [
            [
                'nomor_urut'    => 1,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Sebuah toko peralatan teknik memberikan diskon sebesar 15% untuk pembelian satu set obeng seharga Rp120.000,00. Berapakah jumlah uang yang harus dibayar oleh pembeli setelah mendapatkan diskon?',
                'opsi_a'        => 'Rp102.000,00',
                'opsi_b'        => 'Rp105.000,00',
                'opsi_c'        => 'Rp108.000,00',
                'opsi_d'        => 'Rp110.000,00',
                'opsi_e'        => 'Rp112.000,00',
                'kunci_jawaban' => 'A',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 2,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Perhatikan pola barisan bilangan berikut: 3, 7, 15, 31, 63, ... Angka berikutnya pada pola barisan tersebut adalah...',
                'opsi_a'        => '95',
                'opsi_b'        => '125',
                'opsi_c'        => '127',
                'opsi_d'        => '129',
                'opsi_e'        => '135',
                'kunci_jawaban' => 'C',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 3,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Sebuah mesin pemotong kayu membutuhkan waktu 45 menit untuk memproses 15 batang kayu gelondongan. Jika mesin beroperasi dengan kecepatan konstan, berapa menit waktu yang diperlukan untuk memproses 40 batang kayu?',
                'opsi_a'        => '90 menit',
                'opsi_b'        => '100 menit',
                'opsi_c'        => '110 menit',
                'opsi_d'        => '120 menit',
                'opsi_e'        => '135 menit',
                'kunci_jawaban' => 'D',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 4,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Sebuah pelat baja tipis berbentuk persegi panjang memiliki panjang 24 cm dan lebar 15 cm. Berapakah luas permukaan pelat baja tersebut?',
                'opsi_a'        => '320 cm²',
                'opsi_b'        => '340 cm²',
                'opsi_c'        => '360 cm²',
                'opsi_d'        => '380 cm²',
                'opsi_e'        => '400 cm²',
                'kunci_jawaban' => 'C',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 5,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Sepeda motor bergerak dari Kecamatan Air Naningan menuju Kota Talang Padang sejauh 90 km dengan kecepatan rata-rata 45 km/jam. Jika pengendara berangkat pukul 07.30 WIB, maka pengendara akan tiba pada pukul...',
                'opsi_a'        => '08.45 WIB',
                'opsi_b'        => '09.00 WIB',
                'opsi_c'        => '09.15 WIB',
                'opsi_d'        => '09.45 WIB',
                'opsi_e'        => '09.30 WIB',
                'kunci_jawaban' => 'E',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 6,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Lima orang calon siswa (Agi, Budi, Cici, Dedi, dan Eka) mengikuti tes keterampilan kerja. Nilai Agi lebih tinggi dari Budi, namun lebih rendah dari Cici. Nilai Dedi lebih tinggi dari Cici, sedangkan nilai Eka lebih rendah dari Budi. Siapakah yang memperoleh nilai paling tinggi?',
                'opsi_a'        => 'Agi',
                'opsi_b'        => 'Budi',
                'opsi_c'        => 'Cici',
                'opsi_d'        => 'Dedi',
                'opsi_e'        => 'Eka',
                'kunci_jawaban' => 'D',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 7,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Perhatikan kutipan teks berikut: "Penerapan Keselamatan dan Kesehatan Kerja (K3) di lingkungan bengkel kejuruan bukan sekadar kepatuhan terhadap aturan tertulis, melainkan budaya kerja yang melindungi tenaga kerja dari risiko kecelakaan, menjaga produktivitas mesin, serta menjamin kenyamanan proses produksi." Ide pokok dari kutipan tersebut adalah...',
                'opsi_a'        => 'Tingginya angka kecelakaan kerja di bengkel',
                'opsi_b'        => 'Sanksi bagi pelanggar aturan keselamatan sekolah',
                'opsi_c'        => 'Pentingnya budaya penerapan K3 di bengkel kejuruan',
                'opsi_d'        => 'Cara praktis memperbaiki mesin produksi yang aus',
                'opsi_e'        => 'Daftar harga peralatan pelindung diri untuk siswa',
                'kunci_jawaban' => 'C',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 8,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Istilah "Presisi" dalam konteks pengerjaan benda kerja atau pengukuran teknik memiliki makna yang paling tepat dengan...',
                'opsi_a'        => 'Kecepatan pengerjaan tugas',
                'opsi_b'        => 'Ketepatan dan ketelitian ukuran hasil kerja',
                'opsi_c'        => 'Tingkat kekerasan bahan baku logam',
                'opsi_d'        => 'Keindahan bentuk tampilan fisik',
                'opsi_e'        => 'Keringanan bobot material komposit',
                'kunci_jawaban' => 'B',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 9,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Sebelum menyalakan mesin gerinda potong, operator diwajibkan memeriksa kondisi kabel daya, memasang pelindung pisau (safety guard), dan mengenakan kacamata pelindung (safety goggles). Tujuan utama dari langkah SOP tersebut adalah...',
                'opsi_a'        => 'Menghemat konsumsi daya listrik bengkel',
                'opsi_b'        => 'Mempercepat putaran mata pisau gerinda',
                'opsi_c'        => 'Mencegah percikan serpihan logam mengenai mata dan meminimalkan bahaya sengatan arus listrik',
                'opsi_d'        => 'Menjaga agar pisau gerinda tidak cepat tumpul',
                'opsi_e'        => 'Memudahkan pengukuran dimensi benda kerja yang dipotong',
                'kunci_jawaban' => 'C',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 10,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Semua siswa jurusan teknik wajib mengenakan sepatu pengaman (safety shoes) saat memasuki dan beraktivitas di ruang bengkel. Rian adalah siswa jurusan teknik yang sedang melaksanakan jam praktikum bengkel. Kesimpulan yang paling tepat adalah...',
                'opsi_a'        => 'Rian boleh memakai sandal jika cuaca panas',
                'opsi_b'        => 'Rian hanya perlu bersepatu jika ada guru pengawas',
                'opsi_c'        => 'Rian boleh mengenakan sepatu kain olahraga biasa',
                'opsi_d'        => 'Rian tidak wajib memakai sepatu pengaman jika berada di sudut bengkel',
                'opsi_e'        => 'Rian wajib mengenakan sepatu pengaman selama praktikum di bengkel',
                'kunci_jawaban' => 'E',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 11,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Manakah di antara kalimat berikut yang merupakan contoh catatan laporan hasil pemeriksaan teknis yang objektif, jelas, dan baku?',
                'opsi_a'        => 'Menurut perasaan saya, oli motor ini kelihatannya sudah agak jelek.',
                'opsi_b'        => 'Tegangan baterai tercatat 12,6 Volt dan level ketinggian oli mesin berada di antara garis Lower dan Upper.',
                'opsi_c'        => 'Sepeda motor tampak sangat prima karena cat bodinya mengkilap dan bersih.',
                'opsi_d'        => 'Suara knalpot sepertinya agak aneh saat digas kencang di jalan menanjak.',
                'opsi_e'        => 'Baut roda kelihatannya sudah cukup erat menurut perkiraan mekanik.',
                'kunci_jawaban' => 'B',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 12,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Ketika menemukan kesalahan pencatatan inventaris alat bengkel yang dilakukan oleh rekan satu tim kerja, tindakan yang paling profesional dan beretika adalah...',
                'opsi_a'        => 'Membiarkan kesalahan tersebut agar rekan ditegur pembimbing',
                'opsi_b'        => 'Menyebarkan kekeliruan tersebut ke media sosial',
                'opsi_c'        => 'Mengonfirmasi dan mendiskusikan temuan selisih data secara santun dengan rekan kerja untuk dikoreksi bersama',
                'opsi_d'        => 'Menghapus catatan buku inventaris tanpa koordinasi',
                'opsi_e'        => 'Melimpahkan seluruh tanggung jawab tugas kepada ketua kelas',
                'kunci_jawaban' => 'C',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 13,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Tiga pilar utama yang saling melengkapi dan tidak dapat dipisahkan dalam operasional sebuah sistem komputer adalah...',
                'opsi_a'        => 'Hardware (Perangkat Keras), Software (Perangkat Lunak), dan Brainware (Pengguna)',
                'opsi_b'        => 'Monitor, Keyboard, dan Mouse optik',
                'opsi_c'        => 'Motherboard, Harddisk, dan Power Supply Unit',
                'opsi_d'        => 'Sistem Operasi Windows, Linux, dan Android',
                'opsi_e'        => 'Router nirkabel, Kabel UTP, dan Modem optik',
                'kunci_jawaban' => 'A',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 14,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Protokol komunikasi internet yang dilengkapi dengan enkripsi keamanan data kriptografi (SSL/TLS) sehingga aman untuk pengiriman data login, formulir, atau transaksi digital adalah...',
                'opsi_a'        => 'HTTP',
                'opsi_b'        => 'FTP',
                'opsi_c'        => 'HTTPS',
                'opsi_d'        => 'Telnet',
                'opsi_e'        => 'DHCP',
                'kunci_jawaban' => 'C',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 15,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Dalam pembuatan diagram alir logika pemrograman (flowchart), simbol berbentuk belah ketupat (diamond) berfungsi untuk...',
                'opsi_a'        => 'Menandai titik awal (Start) atau akhir (End) dari program',
                'opsi_b'        => 'Melakukan operasi aritmatika dan penugasan nilai variabel',
                'opsi_c'        => 'Mengambil keputusan berdasarkan pengujian kondisi Benar (True) atau Salah (False)',
                'opsi_d'        => 'Menampilkan hasil cetakan dokumen ke perangkat printer',
                'opsi_e'        => 'Menghubungkan alur program yang berpindah halaman kertas',
                'kunci_jawaban' => 'C',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 16,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Jenis memori komputer yang bersifat sementara (volatile), di mana seluruh data yang sedang diproses akan hilang seketika saat aliran daya listrik terputus, adalah...',
                'opsi_a'        => 'Hard Disk Drive (HDD)',
                'opsi_b'        => 'Solid State Drive (SSD)',
                'opsi_c'        => 'Random Access Memory (RAM)',
                'opsi_d'        => 'Read Only Memory (ROM BIOS)',
                'opsi_e'        => 'Flash Disk USB',
                'kunci_jawaban' => 'C',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 17,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Bentuk kejahatan siber di mana pelaku menyamar sebagai instansi terpercaya melalui email atau pesan tautan palsu untuk memancing korban memberikan kata sandi atau informasi pribadi disebut...',
                'opsi_a'        => 'Defragmenting',
                'opsi_b'        => 'Phishing',
                'opsi_c'        => 'Formatting',
                'opsi_d'        => 'Overclocking',
                'opsi_e'        => 'Benchmarking',
                'kunci_jawaban' => 'B',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 18,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Kombinasi tombol pintasan keyboard standar yang digunakan untuk membatalkan satu atau beberapa tindakan terakhir yang baru saja dilakukan (Undo) pada aplikasi komputer adalah...',
                'opsi_a'        => 'Ctrl + C',
                'opsi_b'        => 'Ctrl + V',
                'opsi_c'        => 'Ctrl + S',
                'opsi_d'        => 'Ctrl + A',
                'opsi_e'        => 'Ctrl + Z',
                'kunci_jawaban' => 'E',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 19,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Urutan empat langkah kerja siklus pembakaran pada mesin bensin 4 tak (four-stroke engine) yang berlangsung secara berkesinambungan adalah...',
                'opsi_a'        => 'Langkah Hisap - Langkah Kompresi - Langkah Usaha (Tenaga) - Langkah Buang',
                'opsi_b'        => 'Langkah Kompresi - Langkah Hisap - Langkah Usaha - Langkah Buang',
                'opsi_c'        => 'Langkah Hisap - Langkah Buang - Langkah Usaha - Langkah Kompresi',
                'opsi_d'        => 'Langkah Buang - Langkah Usaha - Langkah Kompresi - Langkah Hisap',
                'opsi_e'        => 'Langkah Usaha - Langkah Hisap - Langkah Kompresi - Langkah Buang',
                'kunci_jawaban' => 'A',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 20,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Alat ukur presisi bengkel mekanik yang dapat digunakan untuk mengukur diameter luar poros, diameter dalam silinder, serta kedalaman lubang dengan akurat adalah...',
                'opsi_a'        => 'Meteran pita gulung',
                'opsi_b'        => 'Jangka Sorong (Vernier Caliper)',
                'opsi_c'        => 'Mistar baja biasa',
                'opsi_d'        => 'Busur derajat (Protractor)',
                'opsi_e'        => 'Waterpass bangunan',
                'kunci_jawaban' => 'B',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 21,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Selain berfungsi melumasi bidang kontak antar logam yang bergesekan, fungsi penting lain dari pelumas (oli mesin) pada kendaraan bermotor adalah...',
                'opsi_a'        => 'Menaikkan oktan bahan bakar di ruang tangki',
                'opsi_b'        => 'Membantu mendinginkan suhu mesin, meredam getaran, dan membersihkan partikel kerak sisa pembakaran',
                'opsi_c'        => 'Mengubah arus bolak-balik (AC) menjadi arus searah (DC)',
                'opsi_d'        => 'Menambah tekanan angin pada ban kendaraan',
                'opsi_e'        => 'Menghubungkan putaran poros engkol ke roda belakang secara elektrik',
                'kunci_jawaban' => 'B',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 22,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Komponen kelistrikan otomotif yang bertindak sebagai penyimpan energi listrik dalam bentuk reaksi kimia dan menyuplai arus searah (DC) untuk mengaktifkan motor starter adalah...',
                'opsi_a'        => 'Busi (Spark Plug)',
                'opsi_b'        => 'Karburator / Injektor',
                'opsi_c'        => 'Kiprok (Regulator Rectifier)',
                'opsi_d'        => 'Aki / Baterai (Accumulator)',
                'opsi_e'        => 'Spul Pengisian (Stator Coil)',
                'kunci_jawaban' => 'D',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 23,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Perkakas tangan khusus yang dilengkapi jarum penunjuk atau skala klik untuk mengencangkan baut/mur kepala silinder sesuai nilai kekencangan torsi yang disyaratkan manual servis adalah...',
                'opsi_a'        => 'Kunci pas biasa',
                'opsi_b'        => 'Kunci inggris (Adjustable Wrench)',
                'opsi_c'        => 'Kunci Momen / Torsi (Torque Wrench)',
                'opsi_d'        => 'Kunci pipa besar',
                'opsi_e'        => 'Tang jepit kombinasi',
                'kunci_jawaban' => 'C',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 24,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Rambu keselamatan kerja bengkel berbentuk segitiga warna kuning dengan simbol tengkorak manusia dan tulang bersilang memberikan peringatan bahaya...',
                'opsi_a'        => 'Bahaya tegangan arus listrik tinggi',
                'opsi_b'        => 'Bahan beracun dan zat kimia berbahaya (Toxic Hazard)',
                'opsi_c'        => 'Lantai licin karena tumpahan pelumas',
                'opsi_d'        => 'Area paparan kebisingan suara knalpot',
                'opsi_e'        => 'Radiasi medan magnetik tinggi',
                'kunci_jawaban' => 'B',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 25,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Metode pengawetan bahan pangan cair (seperti susu segar atau sari buah) dengan pemanasan terkontrol pada suhu di bawah titik didih untuk mengeliminasi mikroba patogen tanpa merusak rasa dan nutrisi disebut...',
                'opsi_a'        => 'Evaporasi vakum',
                'opsi_b'        => 'Fermentasi alkoholik',
                'opsi_c'        => 'Pasteurisasi (Pasteurization)',
                'opsi_d'        => 'Kristalisasi sukrosa',
                'opsi_e'        => 'Distilasi fraksinasi',
                'kunci_jawaban' => 'C',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 26,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Kabupaten Tanggamus di Provinsi Lampung memiliki potensi perkebunan yang sangat kaya. Jenis komoditas kopi yang paling banyak dibudidayakan oleh masyarakat petani di lereng perbukitan Air Naningan adalah...',
                'opsi_a'        => 'Kopi Luwak Liar',
                'opsi_b'        => 'Kopi Arabika Typica',
                'opsi_c'        => 'Kopi Liberika Lahan Gambut',
                'opsi_d'        => 'Kopi Excelsa',
                'opsi_e'        => 'Kopi Robusta',
                'kunci_jawaban' => 'E',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 27,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Pedoman cara produksi pangan yang baik dan higienis yang mencakup kebersihan bahan baku, sanitasi ruang produksi, kebersihan karyawan, serta pencegahan kontaminasi silang dikenal sebagai...',
                'opsi_a'        => 'ISO 14001',
                'opsi_b'        => 'Good Manufacturing Practices (GMP)',
                'opsi_c'        => 'Just In Time (JIT)',
                'opsi_d'        => 'Kanban Visual Management',
                'opsi_e'        => 'Statistical Process Control (SPC)',
                'kunci_jawaban' => 'B',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 28,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Penambahan konsentrasi gula pasir yang tinggi (sekitar 55% - 65%) pada pembuatan selai buah lokal dapat membuat produk bertahan lama tanpa bahan pengawet kimiawi sintetis karena...',
                'opsi_a'        => 'Gula mengikat air bebas melalui tekanan osmosis sehingga bakteri pembusuk tidak dapat tumbuh dan berkembang biak',
                'opsi_b'        => 'Gula secara otomatis menaikkan suhu bahan makanan menjadi steril',
                'opsi_c'        => 'Gula menghasilkan gas beracun yang mematikan kapang',
                'opsi_d'        => 'Gula merusak serat buah menjadi cair pekat',
                'opsi_e'        => 'Gula menghilangkan seluruh vitamin C dalam buah',
                'kunci_jawaban' => 'A',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 29,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Mikroorganisme jenis kapang baik yang berperan penting dalam proses bioteknologi tradisional pengolahan fermentasi biji kedelai menjadi produk tempe adalah...',
                'opsi_a'        => 'Saccharomyces cerevisiae',
                'opsi_b'        => 'Lactobacillus bulgaricus',
                'opsi_c'        => 'Rhizopus oryzae',
                'opsi_d'        => 'Acetobacter xylinum',
                'opsi_e'        => 'Escherichia coli',
                'kunci_jawaban' => 'C',
                'bobot_nilai'   => 1.00,
            ],
            [
                'nomor_urut'    => 30,
                'tipe_soal'     => 'pg',
                'pertanyaan'    => 'Teknik pengemasan modern di mana seluruh udara oksigen di dalam wadah plastik kedap udara dihisap keluar sebelum dilakukan penyegelan rapat untuk mencegah oksidasi dan ketengikan disebut...',
                'opsi_a'        => 'Pengemasan Vakum (Vacuum Packaging)',
                'opsi_b'        => 'Pengemasan Aerobik Terbuka',
                'opsi_c'        => 'Pengemasan Kertas Pembungkus',
                'opsi_d'        => 'Pengemasan Karung Goni',
                'opsi_e'        => 'Pengemasan Kaleng Terbuka',
                'kunci_jawaban' => 'A',
                'bobot_nilai'   => 1.00,
            ],
        ];

        // Daftar 5 Soal Esai (Nomor urut 31 - 35)
        $soalEsaiData = [
            [
                'nomor_urut'    => 31,
                'tipe_soal'     => 'esai',
                'pertanyaan'    => 'Jelaskan alasan dan motivasi terbesar Anda memilih kompetensi keahlian di SMKN 1 Air Naningan, serta apa target yang ingin Anda raih selama 3 tahun menempuh pendidikan kejuruan!',
                'bobot_nilai'   => 6.00,
            ],
            [
                'nomor_urut'    => 32,
                'tipe_soal'     => 'esai',
                'pertanyaan'    => 'Apabila Anda diberikan tugas praktik baru oleh guru instruktur yang belum pernah Anda pelajari sebelumnya, langkah-langkah apa yang akan Anda lakukan secara runtut agar tugas tersebut dapat diselesaikan dengan baik?',
                'bobot_nilai'   => 6.00,
            ],
            [
                'nomor_urut'    => 33,
                'tipe_soal'     => 'esai',
                'pertanyaan'    => 'Mengapa penerapan disiplin dan aturan Keselamatan dan Kesehatan Kerja (K3) sangat penting dalam lingkungan bengkel dan laboratorium praktik sekolah kejuruan? Jelaskan dampak yang dapat terjadi jika aturan K3 diabaikan!',
                'bobot_nilai'   => 6.00,
            ],
            [
                'nomor_urut'    => 34,
                'tipe_soal'     => 'esai',
                'pertanyaan'    => 'Sebutkan satu potensi atau permasalahan nyata di lingkungan sekitar Anda (misalnya di bidang pertanian, otomotif/perbengkelan, atau teknologi informasi), dan berikan usulan ide solusi sederhana yang dapat diterapkan menggunakan ilmu kejuruan!',
                'bobot_nilai'   => 6.00,
            ],
            [
                'nomor_urut'    => 35,
                'tipe_soal'     => 'esai',
                'pertanyaan'    => 'Ceritakan pengalaman nyata Anda saat harus bekerja sama dalam sebuah tim (kelompok belajar, organisasi OSIS, atau kepanitiaan) untuk mencapai tujuan bersama, dan bagaimana cara Anda menyelesaikan perbedaan pendapat jika terjadi!',
                'bobot_nilai'   => 6.00,
            ],
        ];

        DB::transaction(function () use ($setting, $settingId, $soalPgData, $soalEsaiData) {
            // Hapus butir soal lama untuk setting ini agar bersih dan terurut
            PpdbSoalUjian::where('ppdb_ujian_setting_id', $settingId)->delete();

            // Masukkan 30 Soal PG
            $kunciJawabanArray = [];
            foreach ($soalPgData as $soal) {
                PpdbSoalUjian::create([
                    'ppdb_ujian_setting_id' => $settingId,
                    'nomor_urut'            => $soal['nomor_urut'],
                    'tipe_soal'             => 'pg',
                    'pertanyaan'            => $soal['pertanyaan'],
                    'opsi_a'                => $soal['opsi_a'],
                    'opsi_b'                => $soal['opsi_b'],
                    'opsi_c'                => $soal['opsi_c'],
                    'opsi_d'                => $soal['opsi_d'],
                    'opsi_e'                => $soal['opsi_e'],
                    'kunci_jawaban'         => $soal['kunci_jawaban'],
                    'bobot_nilai'           => $soal['bobot_nilai'],
                ]);

                $kunciJawabanArray[(string) $soal['nomor_urut']] = $soal['kunci_jawaban'];
            }

            // Masukkan 5 Soal Esai
            foreach ($soalEsaiData as $soal) {
                PpdbSoalUjian::create([
                    'ppdb_ujian_setting_id' => $settingId,
                    'nomor_urut'            => $soal['nomor_urut'],
                    'tipe_soal'             => 'esai',
                    'pertanyaan'            => $soal['pertanyaan'],
                    'opsi_a'                => null,
                    'opsi_b'                => null,
                    'opsi_c'                => null,
                    'opsi_d'                => null,
                    'opsi_e'                => null,
                    'kunci_jawaban'         => null,
                    'bobot_nilai'           => $soal['bobot_nilai'],
                ]);
            }

            // Perbarui setting ujian: sinkronkan kunci jawaban PG, jumlah soal PG = 30, jumlah soal esai = 5
            $setting->update([
                'jumlah_soal_pg'   => 30,
                'jumlah_soal_esai' => 5,
                'kunci_jawaban_pg' => $kunciJawabanArray,
                'bobot_pg'         => 70.00,
                'bobot_esai'       => 30.00,
            ]);
        });

        $this->command->info("Berhasil membuat 30 butir soal PG dan 5 butir soal Esai untuk CBT PPDB SMKN 1 Air Naningan.");
    }
}
