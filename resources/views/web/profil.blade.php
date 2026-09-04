@extends('web.layouts.app')

@section('title', 'Profil Kelembagaan — SMKN 1 Air Naningan')
@section('meta_description', 'Profil resmi SMKN 1 Air Naningan: Visi, Misi, Identitas Pokok NPSN, dan Profil Pimpinan Lembaga.')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/web-profil.css') }}?v={{ filemtime(public_path('css/web-profil.css')) }}">
@endpush

@section('content')
<div class="container profil-page-container">

    <!-- Header Profil & Identitas Ringkas -->
    <div class="profil-header-wrap">
        <div class="profil-category-badge">
            Profil Kelembagaan &amp; Visi Vokasi
        </div>
        <h1 class="profil-main-heading">Membangun Keahlian Presisi Menuju Kemandirian Industri</h1>
        <p class="profil-lead-desc">
            SMK Negeri 1 Air Naningan adalah institusi pendidikan kejuruan negeri di bawah naungan Pemerintah Provinsi Lampung yang berfokus pada integrasi rekayasa teknologi perangkat lunak, agro-industri pangan terapan, dan keteknikan otomotif.
        </p>
        
        <!-- Identitas Resmi Lembaga (Sleek Badges) -->
        <div class="profil-identity-badges">
            <span class="identity-badge negeri">SMK NEGERI (Pemprov Lampung)</span>
            <span class="identity-badge akreditasi">Terakreditasi B (BAN-SM)</span>
            <span class="identity-badge">NPSN: {{ $sekolah->npsn ?? '69888999' }}</span>
            <span class="identity-badge">Kecamatan Air Naningan, Tanggamus</span>
        </div>
    </div>

    <!-- Visual Banner Kampus (Cinematic Card) -->
    <div class="profil-banner-card">
        <img src="{{ asset('images/web/hero_kampus.jpg') }}" alt="Gedung Kampus SMKN 1 Air Naningan Tanggamus" class="profil-banner-img">
        <div class="profil-banner-overlay">
            <div class="profil-banner-tag">Kampus Vokasi Air Naningan</div>
            <h2 class="profil-banner-title">SMK Negeri 1 Air Naningan • Kabupaten Tanggamus, Lampung</h2>
            <div class="profil-metrics-strip">
                <span class="profil-metric-chip">3 Konsentrasi Keahlian Unggulan</span>
                <span class="profil-metric-chip">{{ $gurus->count() }} Pendidik &amp; Tenaga Ahli</span>
                <span class="profil-metric-chip">Sistem Terintegrasi SIRANI</span>
            </div>
        </div>
    </div>

    <!-- Pimpinan Lembaga & Sambutan Kepala Sekolah -->
    <div class="profil-leader-card">
        <div class="leader-meta-col">
            <div class="leader-photo-box">
                <img src="{{ asset('images/web/kepala_sekolah.jpg') }}" alt="Kepala SMK Negeri 1 Air Naningan" class="leader-photo-img">
            </div>
            <span class="leader-role-tag">Kepala Sekolah</span>
            <h3 class="leader-name">{{ $sekolah->nama_kepala_sekolah ?? 'Drs. H. Ahmad Sudrajat, M.Pd.' }}</h3>
            @if($sekolah->nip_kepala_sekolah)
                <span class="leader-nip">NIP: {{ $sekolah->nip_kepala_sekolah }}</span>
            @endif
        </div>
        <div class="leader-content-col">
            <span class="leader-eyebrow">Sambutan Pimpinan Lembaga</span>
            <h4 class="leader-statement-title">Komitmen Menghasilkan Lulusan Siap Kerja, Berkarakter, dan Berintegritas</h4>
            <p class="leader-statement-body">
                Selamat datang di laman resmi SMK Negeri 1 Air Naningan. Kami mendedikasikan seluruh ekosistem pembelajaran untuk mencetak generasi vokasi yang adaptif terhadap transformasi industri. Melalui sinergi kurikulum berbasis link &amp; match, penguatan karakter budaya kerja, serta integrasi teknologi digital, kami memastikan setiap peserta didik siap melangkah menjadi tenaga profesional maupun wirausahawan mandiri.
            </p>
        </div>
    </div>

    <!-- Visi & Misi Asimetris Elegan -->
    <div class="visi-misi-grid">
        
        <!-- Visi Sekolah -->
        <div class="visi-card">
            <div>
                <div class="visi-card-header">
                    <div class="visi-eyebrow">Arah Haluan Lembaga</div>
                    <h3 class="visi-title">Visi Sekolah</h3>
                </div>
                <div class="visi-quote-box">
                    <p class="visi-quote-text">
                        &ldquo;Menjadi Lembaga Pendidikan Kejuruan yang Unggul, Berakhlak Mulia, Berbudaya Industri, dan Berwawasan Lingkungan Menuju Indonesia Emas.&rdquo;
                    </p>
                </div>
            </div>
            <div class="visi-card-footer">
                Landasan strategis pembinaan karakter vokasi &amp; etos kerja profesional.
            </div>
        </div>

        <!-- Misi Sekolah -->
        <div class="misi-card">
            <div class="misi-card-header">
                <div class="misi-eyebrow">Strategi Nyata</div>
                <h3 class="misi-title">Misi Sekolah</h3>
            </div>
            <div class="misi-steps-list">
                <div class="misi-step-item">
                    <span class="misi-step-num">01</span>
                    <p class="misi-step-text">Menyelenggarakan proses pembelajaran berbasis kompetensi dan kemitraan link &amp; match dunia industri.</p>
                </div>
                <div class="misi-step-item">
                    <span class="misi-step-num">02</span>
                    <p class="misi-step-text">Menanamkan nilai religius, budi pekerti luhur, dan integritas kehadiran tinggi melalui ekosistem digital SIRANI.</p>
                </div>
                <div class="misi-step-item">
                    <span class="misi-step-num">03</span>
                    <p class="misi-step-text">Meningkatkan kapasitas sertifikasi kompetensi pendidik dan keahlian teknis siswa secara berkelanjutan.</p>
                </div>
                <div class="misi-step-item">
                    <span class="misi-step-num">04</span>
                    <p class="misi-step-text">Mengembangkan unit Teaching Factory (TEFA) sebagai sarana inkubasi wirausaha mandiri (technopreneur &amp; agripreneur).</p>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
