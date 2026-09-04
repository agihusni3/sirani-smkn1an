@extends('web.layouts.app')

@section('title', 'Profil Kelembagaan — SMKN 1 Air Naningan')
@section('meta_description', 'Profil resmi SMKN 1 Air Naningan: Visi, Misi, Identitas Pokok NPSN, Sarana Prasarana Praktik Kejuruan, dan Direktori Tenaga Pendidik.')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/web-profil.css') }}?v={{ filemtime(public_path('css/web-profil.css')) }}">
@endpush

@section('content')
<div class="container profil-page-container">

    <!-- Header Profil -->
    <div class="profil-header-wrap">
        <div class="profil-category-badge">
            Profil Kelembagaan &amp; Visi Vokasi
        </div>
        <h1 class="profil-main-heading">Membangun Keahlian Presisi Menuju Kemandirian Industri</h1>
        <p class="profil-lead-desc">
            SMK Negeri 1 Air Naningan adalah institusi pendidikan kejuruan negeri di bawah naungan Pemerintah Provinsi Lampung yang berfokus pada integrasi rekayasa teknologi perangkat lunak, agro-industri pangan terapan, dan keteknikan otomotif.
        </p>
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

    <!-- Identitas Pokok & Data Resmi Sekolah -->
    <div class="identitas-section-wrap">
        <div class="identitas-topbar">
            <h3 class="identitas-heading">Identitas &amp; Data Pokok Sekolah</h3>
            <span class="identitas-tag">DATA POKOK PENDIDIKAN (DAPODIK)</span>
        </div>

        <div class="identitas-grid">
            <div class="identitas-card">
                <div class="identitas-label">NPSN Sekolah</div>
                <div class="identitas-val">{{ $sekolah->npsn ?? '69888999' }}</div>
            </div>
            <div class="identitas-card">
                <div class="identitas-label">Status Kelembagaan</div>
                <div class="identitas-val emerald">NEGERI (Pemprov Lampung)</div>
            </div>
            <div class="identitas-card">
                <div class="identitas-label">Status Akreditasi</div>
                <div class="identitas-val amber">Terakreditasi B (BAN-SM)</div>
            </div>
            <div class="identitas-card">
                <div class="identitas-label">Pimpinan Lembaga</div>
                <div class="identitas-val" style="font-size: 0.98rem;">{{ $sekolah->nama_kepala_sekolah ?? 'Drs. H. Ahmad Sudrajat, M.Pd.' }}</div>
            </div>
        </div>
    </div>

    <!-- Sarana & Prasarana Workshop Kejuruan -->
    <div class="workshop-section-wrap">
        <div class="workshop-header">
            <div class="workshop-eyebrow">Sarana Praktik Mandiri</div>
            <h3 class="workshop-title">Workshop &amp; Laboratorium Standar Industri</h3>
            <p class="workshop-desc">
                Setiap konsentrasi keahlian didukung fasilitas praktik mandiri untuk menunjang kurikulum berbasis rekayasa langsung.
            </p>
        </div>

        <div class="workshop-grid">
            <!-- RPL -->
            <div class="workshop-card">
                <div class="workshop-img-box">
                    <img src="{{ asset('images/web/jurusan_rpl.jpg') }}" alt="Laboratorium Rekayasa Perangkat Lunak" class="workshop-img">
                    <span class="workshop-badge rpl">01 / SOFTWARE LAB</span>
                </div>
                <div class="workshop-body">
                    <h4 class="workshop-name">Lab Rekayasa Perangkat Lunak</h4>
                    <p class="workshop-text">PC workstation modern, gigabit network, cloud deployment workstation, dan IoT testbed untuk inovasi rekayasa digital.</p>
                </div>
            </div>

            <!-- APHP -->
            <div class="workshop-card">
                <div class="workshop-img-box">
                    <img src="{{ asset('images/web/jurusan_aphp.jpg') }}" alt="Unit Produksi Pengolahan Pangan" class="workshop-img">
                    <span class="workshop-badge aphp">02 / AGRO-TECH LAB</span>
                </div>
                <div class="workshop-body">
                    <h4 class="workshop-name">Workshop Agro-Teknologi Pangan</h4>
                    <p class="workshop-text">Peralatan olahan komoditas kopi Tanggamus, digital coffee roaster, grinder industri, packaging sealing, dan instrumen uji mutu higienis.</p>
                </div>
            </div>

            <!-- TSM -->
            <div class="workshop-card">
                <div class="workshop-img-box">
                    <img src="{{ asset('images/web/jurusan_tsm.jpg') }}" alt="Bengkel Teknik Sepeda Motor" class="workshop-img">
                    <span class="workshop-badge tsm">03 / AUTOMOTIVE WORKSHOP</span>
                </div>
                <div class="workshop-body">
                    <h4 class="workshop-name">Bengkel Otomotif Sepeda Motor</h4>
                    <p class="workshop-text">Standar bengkel resmi APM, hydraulic bike lift, diagnostic scanner EFI, tune-up station, dan SST toolkit presisi mekanik.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Direktori Dewan Guru & Tenaga Kependidikan -->
    <div class="guru-section-wrap">
        <div class="guru-section-topbar">
            <div class="guru-title-block">
                <h3 class="guru-heading">Dewan Pendidik &amp; Tenaga Kependidikan</h3>
                <p class="guru-subtext">Direktori {{ $gurus->count() }} Guru dan Tenaga Kependidikan bersertifikasi di SMKN 1 Air Naningan</p>
            </div>
            
            <!-- Live Search Bar -->
            <div class="guru-search-box">
                <input type="text" 
                       id="guruSearchInput" 
                       class="guru-search-input" 
                       placeholder="Cari nama guru atau mapel..." 
                       oninput="filterGuru(this.value)">
            </div>
        </div>

        <div class="guru-grid" id="guruGrid">
            @php
                $gradients = [
                    'linear-gradient(135deg, #1e40af, #3b82f6)',
                    'linear-gradient(135deg, #065f46, #10b981)',
                    'linear-gradient(135deg, #6d28d9, #8b5cf6)',
                    'linear-gradient(135deg, #b45309, #f59e0b)',
                    'linear-gradient(135deg, #0f766e, #14b8a6)',
                    'linear-gradient(135deg, #be185d, #ec4899)',
                ];
            @endphp

            @foreach($gurus as $guru)
                @php
                    // Ambil inisial 2 huruf dari nama guru
                    $cleanName = preg_replace('/[^a-zA-Z\s]/', '', $guru->nama);
                    $words = array_values(array_filter(explode(' ', trim($cleanName))));
                    $initials = '';
                    if (count($words) >= 2) {
                        $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                    } elseif (count($words) === 1) {
                        $initials = strtoupper(substr($words[0], 0, 2));
                    } else {
                        $initials = 'TK';
                    }
                    $bgGrad = $gradients[$loop->index % count($gradients)];
                @endphp

                <div class="guru-card" data-name="{{ strtolower($guru->nama) }}" data-mapel="{{ strtolower($guru->mata_pelajaran ?? '') }}">
                    <div class="guru-avatar-initials" style="background: {{ $bgGrad }};">
                        {{ $initials }}
                    </div>
                    <div class="guru-name">{{ $guru->nama }}</div>
                    <div class="guru-mapel">{{ $guru->mata_pelajaran ?? 'Pendidik Kejuruan' }}</div>
                    @if($guru->nip)
                        <div class="guru-nip">NIP: {{ $guru->nip }}</div>
                    @endif
                </div>
            @endforeach

            <!-- Empty Search State -->
            <div id="guruEmptyState" class="guru-empty-state">
                Tidak ada tenaga pendidik yang cocok dengan kata kunci pencarian.
            </div>
        </div>
    </div>

</div>

<script>
function filterGuru(query) {
    const q = query.toLowerCase().trim();
    const cards = document.querySelectorAll('.guru-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const name = card.getAttribute('data-name') || '';
        const mapel = card.getAttribute('data-mapel') || '';
        if (name.includes(q) || mapel.includes(q)) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    const emptyState = document.getElementById('guruEmptyState');
    if (emptyState) {
        emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
    }
}
</script>
@endsection
