<?php

namespace Database\Seeders;

use App\Models\AkademikMataPelajaran;
use Illuminate\Database\Seeder;

class AkademikCapaianPembelajaranSeeder extends Seeder
{
    /**
     * Sinkronisasi Capaian Pembelajaran (CP) Resmi Kurikulum Merdeka SMK
     * Berdasarkan Keputusan Kepala BSKAP Kemendikbudristek No. 032/H/KR/2024 & 033/H/KR/2022
     */
    public function run(): void
    {
        $dataCp = [
            'Informatika' => [
                'fase' => 'E',
                'tingkat' => 'X',
                'singkatan' => 'INF',
                'cp' => "Peserta didik mampu menerapkan berpikir komputasional (BK) untuk memecahkan persoalan komputasi kompleks; memanfaatkan perkakas TIK secara kritis dan kolaboratif; memahami komponen dan arsitektur sistem komputer serta mekanisme jaringan internet dan keamanan data; mengolah dan menganalisis data terstruktur; merancang algoritma dan mengimplementasikan program terstruktur; memahami dampak sosial teknologi digital; serta menyelesaikan proyek praktik lintas bidang (PLB).",
            ],
            'Matematika' => [
                'fase' => 'E,F',
                'tingkat' => 'X,XI,XII',
                'singkatan' => 'MTK',
                'cp' => "Peserta didik mampu menggeneralisasi sifat operasi bilangan berpangkat (eksponen) dan logaritma; menyelesaikan sistem persamaan dan pertidaksamaan linier serta kuadrat; memodelkan fenomena kontekstual dengan fungsi aljabar; menerapkan perbandingan trigonometri serta aturan geometri; menganalisis data statistik deskriptif dan peluang kejadian majemuk; serta menerapkan konsep kalkulus terapan untuk pemecahan masalah kejuruan dan kehidupan nyata.",
            ],
            'Bahasa Indonesia' => [
                'fase' => 'E,F',
                'tingkat' => 'X,XI,XII',
                'singkatan' => 'B.INDO',
                'cp' => "Peserta didik mampu mengevaluasi dan mengkreasi informasi berupa gagasan, pikiran, pandangan, atau pesan yang akurat dari berbagai tipe teks lisan dan audiovisual; membaca dan memirsa kritis teks sastra dan non-sastra; berbicara dan mempresentasikan ide secara logis, kritis, santun, dan percaya diri; serta menulis berbagai jenis teks fungsional, teks eksposisi, proposal, dan laporan hasil kerja kejuruan dengan kaidah bahasa yang baku.",
            ],
            'Bahasa Inggris' => [
                'fase' => 'E,F',
                'tingkat' => 'X,XI,XII',
                'singkatan' => 'B.ING',
                'cp' => "Peserta didik mampu menggunakan bahasa Inggris lisan dan tulisan untuk berkomunikasi secara efektif dalam konteks sosial dan dunia kerja (setara level B1-B2 CEFR); memahami teks autentik faktual, deskriptif, dan prosedur kejuruan; menyampaikan opini, argumen, dan presentasi bisnis; serta menyusun korespondensi profesional, surel formal, curriculum vitae, dan dokumen teknis standar industri.",
            ],
            'Pendidikan Agama dan Budi Pekerti' => [
                'fase' => 'E,F',
                'tingkat' => 'X,XI,XII',
                'singkatan' => 'PAI',
                'cp' => "Peserta didik mampu menganalisis ayat Al-Qur'an dan Hadis tentang toleransi, etos kerja keras, dan iptek; memperkokoh keimanan dan ketakwaan kepada Tuhan Yang Maha Esa; menerapkan akhlak mulia dalam hubungan sosial, kepemimpinan, dan etika profesi kejuruan; memahami hukum fikih muamalah dan ibadah syariah; serta meneladani sejarah perkembangan peradaban Islam sebagai pembentuk peradaban dunia yang rahmatan lil 'alamin.",
            ],
            'Pendidikan Pancasila dan Kewarganegaraan' => [
                'fase' => 'E,F',
                'tingkat' => 'X,XI,XII',
                'singkatan' => 'PPKN',
                'cp' => "Peserta didik mampu menganalisis kedudukan dan fungsi Pancasila sebagai ideologi terbuka dan falsafah hidup bangsa; mengkaji pasal-pasal UUD NRI 1945 terkait hak asasi manusia serta penegakan hukum; mengapresiasi dan merawat kebhinekaan budaya dalam bingkai persatuan nasional; serta berpartisipasi aktif menjaga keutuhan kedaulatan Negara Kesatuan Republik Indonesia (NKRI) di era globalisasi.",
            ],
            'Pendidikan Jasmani, Olahraga dan Kesehatan' => [
                'fase' => 'E,F',
                'tingkat' => 'X,XI,XII',
                'singkatan' => 'PJOK',
                'cp' => "Peserta didik mampu mempraktikkan keterampilan gerak spesifik dalam berbagai cabang olahraga permainan, bela diri, senam, dan aktivitas air; merancang program latihan peningkatan kebugaran jasmani terkait kesehatan dan keterampilan kerja ergonomis; serta menerapkan pola hidup sehat, keselamatan diri di tempat kerja, sportivitas, dan kepemimpinan tim.",
            ],
            'Sejarah Indonesia' => [
                'fase' => 'E,F',
                'tingkat' => 'X,XI',
                'singkatan' => 'SEJ',
                'cp' => "Peserta didik mampu menganalisis proses masuk dan berkembangnya kebudayaan di Nusantara (masa Hindu-Buddha, Islam, dan Kolonialisme); dinamika pergerakan kebangsaan menuju proklamasi kemerdekaan; mempertahankan kemerdekaan dan integrasi bangsa; hingga masa reformasi kontemporer; serta mengambil hikmah keteladanan tokoh pejuang untuk memperkokoh jati diri bangsa.",
            ],
            'Proyek Ilmu Pengetahuan Alam dan Sosial' => [
                'fase' => 'E',
                'tingkat' => 'X',
                'singkatan' => 'IPAS',
                'cp' => "Peserta didik mampu menjelaskan fenomena ilmiah terkait makhluk hidup dan lingkungannya, zat dan perubahannya, energi dan perubahannya, bumi dan antariksa, konektivitas ruang dan waktu, interaksi sosial serta dinamika ekonomi; mendesain penyelidikan ilmiah terapan; serta menerjemahkan data pengamatan secara saintifik untuk pemecahan masalah lingkungan dan kejuruan.",
            ],
            'Seni Budaya' => [
                'fase' => 'E',
                'tingkat' => 'X',
                'singkatan' => 'SENI',
                'cp' => "Peserta didik mampu mengeksplorasi elemen dan teknik seni (rupa, musik, tari, atau teater); merancang dan menciptakan karya seni yang mengintegrasikan kearifan budaya nusantara; merefleksikan nilai estetis, pesan moral, dan etika karya seni; serta menampilkan karya seni kreatif yang berdampak positif bagi lingkungan sekolah dan masyarakat.",
            ],
            'Kreatifitas, Inovasi dan Kewirausahaan' => [
                'fase' => 'F',
                'tingkat' => 'XI,XII',
                'singkatan' => 'PKK',
                'cp' => "Peserta didik mampu membaca dan memvalidasi peluang usaha berbasis kompetensi keahlian; merancang model bisnis (Business Model Canvas); mengembangkan prototipe produk/jasa kejuruan; merencanakan proses produksi massal dan pengendalian mutu; menyusun strategi pemasaran digital dan kemitraan; serta menyusun laporan keuangan (HPP, BEP, laba-rugi) dan legalitas usaha mandiri.",
            ],
            'Praktik Kerja Lapangan' => [
                'fase' => 'F',
                'tingkat' => 'XII',
                'singkatan' => 'PKL',
                'cp' => "Peserta didik mampu menginternalisasi soft skills kerja (integritas, komunikasi, kerjasama tim, budaya 5S/Kaizen, keselamatan K3LH); mengaplikasikan keterampilan teknis hard skills kejuruan secara nyata di Dunia Usaha / Dunia Industri (DU/DI); memecahkan persoalan operasional di lapangan; serta menyusun laporan portofolio komprehensif dan mempresentasikannya.",
            ],
            'Dasar-Dasar Program Keahlian PPLG' => [
                'fase' => 'E',
                'tingkat' => 'X',
                'singkatan' => 'DKK-RPL',
                'cp' => "Peserta didik mampu memahami proses bisnis rekayasa perangkat lunak dan gim; perkembangan teknologi digital (cloud computing, IoT, big data, AI); profesi dan peluang kerja software engineering; menerapkan keselamatan kerja (K3LH); memahami logika dan algoritma pemrograman terstruktur; serta merancang pemodelan perangkat lunak berorientasi objek (UML / diagram sistem).",
            ],
            'Konsentrasi Keahlian PPLG (Web & Mobile)' => [
                'fase' => 'F',
                'tingkat' => 'XI,XII',
                'singkatan' => 'KK-RPL',
                'cp' => "Peserta didik mampu merancang dan membangun antarmuka web responsif (HTML5, CSS3, JavaScript modern); mengembangkan aplikasi web dinamis berbasis framework backend (PHP/Laravel); merancang dan mengoptimalkan basis data relasional (MySQL/PostgreSQL); membangun aplikasi mobile multiplatform (Flutter/Android); mengintegrasikan RESTful API dan arsitektur microservices; serta menerapkan standard clean code dan version control (Git).",
            ],
            'Mata Pelajaran Pilihan PPLG' => [
                'fase' => 'F',
                'tingkat' => 'XI',
                'singkatan' => 'MP-RPL',
                'cp' => "Peserta didik mampu memperdalam keahlian pada arsitektur API modern, pengembangan aplikasi fullstack JavaScript, containerization menggunakan Docker, automated testing, dan penerapan CI/CD pipeline untuk deployment perangkat lunak berskala industri.",
            ],
            'Dasar-Dasar Program Keahlian APHP' => [
                'fase' => 'E',
                'tingkat' => 'X',
                'singkatan' => 'DKK-APHP',
                'cp' => "Peserta didik mampu memahami proses bisnis industri pengolahan hasil pertanian dan pangan; perkembangan bioteknologi pangan dan rantai pasok agroindustri; menerapkan K3LH, sanitasi industri, dan Good Manufacturing Practices (GMP); mengidentifikasi karakteristik komoditas hasil pertanian (nabati dan hewani); serta mempraktikkan teknik dasar pengolahan dan pengujian mutu komoditas pertanian.",
            ],
            'Konsentrasi Keahlian APHP' => [
                'fase' => 'F',
                'tingkat' => 'XI,XII',
                'singkatan' => 'KK-APHP',
                'cp' => "Peserta didik mampu memproduksi olahan komoditas nabati (buah, sayur, umbi, serealia); memproduksi olahan hasil hewani (daging, ikan, susu); mengolah hasil perkebunan dan herbal lokal (kopi, kakao, lada); menerapkan sistem jaminan mutu dan keamanan pangan (HACCP/ISO 22000); merancang kemasan ramah lingkungan, label BPOM/Halal, serta mengelola produksi pangan komersial bernilai jual tinggi.",
            ],
            'Mata Pelajaran Pilihan APHP' => [
                'fase' => 'F',
                'tingkat' => 'XI',
                'singkatan' => 'MP-APHP',
                'cp' => "Peserta didik mampu mengembangkan inovasi diversifikasi produk pangan fungsional berbasis kearifan lokal Tanggamus (seperti olahan kopi robusta specialty, fermentasi kakao, dan minyak atsiri), teknik pengawetan modern, serta pengemasan vakum/retort.",
            ],
            'Dasar-Dasar Program Keahlian TSM' => [
                'fase' => 'E',
                'tingkat' => 'X',
                'singkatan' => 'DKK-TSM',
                'cp' => "Peserta didik mampu memahami proses bisnis bengkel otomotif dan dealer sepeda motor; perkembangan teknologi otomotif terkini (sistem injeksi, hybrid, sepeda motor listrik/EV); menerapkan K3LH dan budaya kerja industri 5S; membaca dan membuat gambar teknik otomotif; menggunakan alat ukur presisi mekanik, elektrik, dan pneumatik (PDTM); serta melakukan pemeliharaan dasar komponen kendaraan.",
            ],
            'Konsentrasi Keahlian TSM (Mesin & Chasis)' => [
                'fase' => 'F',
                'tingkat' => 'XI,XII',
                'singkatan' => 'KK-TSM',
                'cp' => "Peserta didik mampu mendiagnosis kerusakan dan melakukan perawatan berkala mesin sepeda motor 2-tak dan 4-tak; merawat dan memperbaiki sistem sasis, kemudi, suspensi, dan sistem rem cakram/ABS; memperbaiki transmisi manual dan CVT matic; mendiagnosis sistem kelistrikan body, pengisian, pengapian, dan sensor sistem bahan bakar injeksi (EFI) dengan engine scanner; serta mengelola manajemen bengkel mandiri.",
            ],
            'Mata Pelajaran Pilihan TSM' => [
                'fase' => 'F',
                'tingkat' => 'XI',
                'singkatan' => 'MP-TSM',
                'cp' => "Peserta didik mampu menganalisis dan melakukan penyetelan performa mesin sepeda motor (engine tuning & remapping ECU), pengujian torsi dan daya pada dynometer, perawatan sistem kelistrikan sepeda motor listrik (motor BLDC dan controller), serta teknik modifikasi aman.",
            ],
            'Koding dan Kecerdasan Artifisial' => [
                'fase' => 'E,F',
                'tingkat' => 'X,XI',
                'singkatan' => 'AI',
                'cp' => "Peserta didik mampu memahami logika komputasional dan dasar koding; mengenal konsep Machine Learning, Computer Vision, dan Natural Language Processing (NLP); memanfaatkan model AI generatif dan prompt engineering untuk otomatisasi pekerjaan; serta menerapkan etika pemanfaatan kecerdasan buatan, privasi data, dan hak cipta digital.",
            ],
            'Mulok Bahasa Lampung' => [
                'fase' => 'E,F',
                'tingkat' => 'X,XI',
                'singkatan' => 'B.LPG',
                'cp' => "Peserta didik mampu memahami nilai-nilai luhur kearifan lokal falsafah Piil Pesenggiri (Nemui Nyimah, Nengah Nyappur, Sakai Sambayan, Juluk Adek); berkomunikasi lisan dan tulisan dalam ragam bahasa Lampung dialek O/A; membaca dan menulis aksara Lampung (Had Lampung) secara tepat; serta melestarikan adat istiadat, sastra lisan, dan seni pertunjukan tradisi Lampung.",
            ],
            'Mulok Pertanian' => [
                'fase' => 'E,F',
                'tingkat' => 'X,XI',
                'singkatan' => 'PERTANIAN',
                'cp' => "Peserta didik mampu menganalisis karakteristik agroklimat dan potensi tanah Kabupaten Tanggamus; mempraktikkan teknik pembibitan, penanaman, dan pemeliharaan komoditas perkebunan unggulan (kopi robusta, lada, kakao, cengkeh); menerapkan Pengendalian Hama Terpadu (PHT) berbasis hayati; serta mengelola pertanian ramah lingkungan berkelanjutan.",
            ],
            'Pendidikan Anti Korupsi' => [
                'fase' => 'E,F',
                'tingkat' => 'X,XI,XII',
                'singkatan' => 'PAK',
                'cp' => "Peserta didik mampu memahami hakikat, bentuk, faktor penyebab, dan dampak destruktif korupsi bagi kemajuan bangsa; menginternalisasi 9 nilai integritas (Jujur, Peduli, Mandiri, Disiplin, Tanggung Jawab, Kerja Keras, Sederhana, Berani, Adil) dalam kehidupan sehari-hari; serta membiasakan tata kelola transparansi dan akuntabilitas.",
            ],
            'Bimbingan Konseling' => [
                'fase' => 'E,F',
                'tingkat' => 'X,XI,XII',
                'singkatan' => 'BK',
                'cp' => "Peserta didik mampu mengenali potensi bakat dan minat pribadi; membangun penyesuaian sosial yang harmonis dan inklusif; mengembangkan motivasi dan strategi belajar efektif; serta merumuskan perencanaan karir masa depan secara matang (Bekerja di industri, Melanjutkan ke perguruan tinggi, atau Berwirausaha - BMW).",
            ],
            'Internet of Things (IoT) & Robotika' => [
                'fase' => 'F',
                'tingkat' => 'XI,XII',
                'singkatan' => 'IOT',
                'cp' => "Peserta didik mampu merancang sistem instrumentasi cerdas; memprogram mikrokontroler (Arduino/ESP32) dan mengintegrasikan sensor/aktuator; menghubungkan perangkat ke cloud via protokol komunikasi IoT (MQTT/HTTP); serta membangun proyek otomasi kendali cerdas untuk pertanian pintar (smart farming) dan monitoring industri.",
            ],
            'Teaching Factory (TEFA)' => [
                'fase' => 'F',
                'tingkat' => 'XI,XII',
                'singkatan' => 'TEFA',
                'cp' => "Peserta didik mampu melaksanakan pembelajaran berbasis produksi riil sesuai pesanan konsumen dan standar dunia industri; menerapkan manajemen waktu kerja presisi, jaminan mutu produk (Quality Assurance), komunikasi pelayanan pelanggan, dan perhitungan efisiensi biaya produksi.",
            ],
        ];

        foreach ($dataCp as $namaMapel => $item) {
            $mapels = AkademikMataPelajaran::where('nama_mapel', 'like', "%{$namaMapel}%")->get();
            foreach ($mapels as $m) {
                $m->deskripsi_cp = $item['cp'];
                if (!empty($item['singkatan']) && empty($m->singkatan_mapel)) {
                    $m->singkatan_mapel = $item['singkatan'];
                }
                if (!empty($item['fase'])) {
                    $m->fase = $item['fase'];
                }
                if (!empty($item['tingkat']) && empty($m->tingkat)) {
                    $m->tingkat = $item['tingkat'];
                }
                $m->save();
            }
        }
    }
}
