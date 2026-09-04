@extends('web.layouts.app')

@section('title', 'SMKN 1 Air Naningan — Precision Vocational Academy')
@section('meta_description', 'Official Portal SMKN 1 Air Naningan. Lembaga Pendidikan Vokasi Unggulan Rekayasa Perangkat Lunak, Agro-Teknologi Pangan, dan Teknik Sepeda Motor di Tanggamus.')

@push('styles')
<style>
    /* ── Precision Workshop Styling ── */
    .hero-stage {
        background: #ffffff;
        border: 1px solid var(--border-main);
        border-radius: var(--radius-xl);
        overflow: hidden;
        margin-top: 28px;
        margin-bottom: 40px;
        box-shadow: var(--shadow-card);
    }

    .hero-stage-grid {
        display: grid;
        grid-template-columns: 1.25fr 1fr;
        min-height: 520px;
    }

    .hero-stage-left {
        padding: clamp(36px, 5vw, 64px);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .hero-badge-industrial {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        background: var(--bg-surface-alt);
        border: 1px solid var(--border-main);
        border-radius: 30px;
        font-family: var(--font-tech);
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--text-dark);
        letter-spacing: 0.04em;
        margin-bottom: 24px;
        width: fit-content;
    }

    .hero-main-title {
        font-size: clamp(2.2rem, 4vw, 3.4rem);
        font-weight: 800;
        color: var(--text-dark);
        line-height: 1.1;
        letter-spacing: -0.035em;
        margin-bottom: 20px;
    }

    .hero-lead-text {
        font-size: 1.05rem;
        color: var(--text-body);
        line-height: 1.65;
        max-width: 580px;
        margin-bottom: 32px;
    }

    .hero-stage-right {
        position: relative;
        background: #0f172a;
        overflow: hidden;
    }

    .hero-stage-right img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.92;
        transition: transform 0.6s ease;
    }

    .hero-stage-right:hover img {
        transform: scale(1.03);
    }

    .hero-stage-overlay-tag {
        position: absolute;
        bottom: 24px;
        left: 24px;
        right: 24px;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        padding: 14px 18px;
        color: #ffffff;
    }

    /* Workshop Telemetry Bar */
    .telemetry-strip {
        background: #ffffff;
        border: 1px solid var(--border-main);
        border-radius: var(--radius-lg);
        padding: 20px 28px;
        margin-bottom: 60px;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        box-shadow: var(--shadow-subtle);
    }

    .telemetry-item {
        border-right: 1px solid var(--border-main);
        padding-right: 20px;
    }

    .telemetry-item:last-child {
        border-right: none;
        padding-right: 0;
    }

    .telemetry-value {
        font-family: var(--font-tech);
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--text-dark);
        line-height: 1.1;
        margin-bottom: 4px;
    }

    .telemetry-desc {
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.05em;
    }

    /* ── Industrial Spec Sheets Layout (01, 02, 03) ── */
    .spec-sheet-grid {
        display: flex;
        flex-direction: column;
        gap: 30px;
        margin-bottom: 70px;
    }

    .spec-sheet-card {
        background: #ffffff;
        border: 1px solid var(--border-main);
        border-radius: var(--radius-xl);
        overflow: hidden;
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        box-shadow: var(--shadow-card);
        transition: var(--transition);
    }

    .spec-sheet-card:hover {
        border-color: var(--brand-blue);
        box-shadow: var(--shadow-hover);
        transform: translateY(-2px);
    }

    .spec-sheet-card.reverse {
        grid-template-columns: 1.2fr 1fr;
    }

    .spec-media {
        height: 100%;
        min-height: 320px;
        overflow: hidden;
        position: relative;
    }

    .spec-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.5s ease;
    }

    .spec-sheet-card:hover .spec-media img {
        transform: scale(1.04);
    }

    .spec-content {
        padding: clamp(30px, 4vw, 48px);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .spec-index-no {
        font-family: var(--font-tech);
        font-size: 2.4rem;
        font-weight: 700;
        color: var(--border-main);
        line-height: 1;
        margin-bottom: 12px;
    }

    .spec-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-dark);
        letter-spacing: -0.02em;
        margin-bottom: 10px;
    }

    .spec-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
        margin: 20px 0;
    }

    .spec-table td {
        padding: 9px 0;
        border-bottom: 1px solid var(--border-main);
        vertical-align: top;
    }

    .spec-table td:first-child {
        width: 150px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        font-size: 0.74rem;
        letter-spacing: 0.04em;
    }

    .spec-table td:last-child {
        color: var(--text-dark);
        font-weight: 600;
    }

    /* ── Teaching Factory Industrial Portfolio ── */
    .tefa-strip {
        background: #090d16;
        border-radius: var(--radius-xl);
        padding: clamp(36px, 5vw, 54px);
        color: #ffffff;
        margin-bottom: 70px;
    }

    .tefa-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
        margin-top: 36px;
    }

    .tefa-item-box {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-lg);
        padding: 24px;
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .tefa-item-box:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.25);
        transform: translateY(-3px);
    }

    /* PPDB Fast Banner */
    .ppdb-banner-box {
        background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
        border-radius: var(--radius-xl);
        padding: clamp(36px, 5vw, 56px);
        color: #ffffff;
        margin-bottom: 70px;
        position: relative;
        overflow: hidden;
    }

    @media (max-width: 900px) {
        .hero-stage-grid { grid-template-columns: 1fr; }
        .hero-stage-right { min-height: 280px; }
        .telemetry-strip { grid-template-columns: 1fr 1fr; }
        .spec-sheet-card, .spec-sheet-card.reverse { grid-template-columns: 1fr; }
    }

    @media (max-width: 640px) {
        .hero-stage { margin-top: 14px; margin-bottom: 24px; border-radius: var(--radius-lg); }
        .hero-stage-left { padding: 24px 16px !important; }
        .hero-main-title { font-size: 1.85rem !important; }
        .hero-lead-text { font-size: 0.92rem !important; }
        .hero-badge-industrial { font-size: 0.68rem; padding: 4px 10px; margin-bottom: 16px; max-width: 100%; }
        .tefa-banner { padding: 28px 18px !important; }
        .ppdb-banner-box { padding: 28px 18px !important; }
    }

    /* Hero Multi-Slide & Carousel Controls */
    .hero-slider-track {
        position: relative;
    }
    .hero-slide {
        display: none;
        opacity: 0;
        transition: opacity 0.4s ease;
    }
    .hero-slide.active {
        display: block;
        opacity: 1;
        animation: heroFadeIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes heroFadeIn {
        from { opacity: 0; transform: translateY(3px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .hero-slider-controls {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: var(--bg-surface-alt);
        border: 1px solid var(--border-main);
        padding: 4px 10px;
        border-radius: 20px;
    }
    .hero-slider-dots {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .hero-dot {
        width: 7px;
        height: 7px;
        border-radius: 10px;
        background: var(--border-main);
        border: none;
        cursor: pointer;
        padding: 0;
        transition: all 0.25s ease;
    }
    .hero-dot.active {
        width: 20px;
        background: var(--brand-blue);
    }
    .hero-slider-counter {
        font-family: var(--font-tech);
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--text-muted);
        letter-spacing: 0.04em;
    }
    .hero-arrow-btn {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 1px solid var(--border-main);
        background: #ffffff;
        color: var(--text-dark);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.68rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .hero-arrow-btn:hover {
        background: var(--brand-blue);
        color: #ffffff;
        border-color: var(--brand-blue);
    }
</style>
@endpush

@section('content')
<div class="container">

    <!-- ═══ 1. HERO: THE PRECISION WORKSHOP STAGE ═══ -->
    <div class="hero-stage" id="heroStageSlider">
        <div class="hero-slider-track">
            @forelse($heroBanners as $index => $banner)
                <div class="hero-slide {{ $index === 0 ? 'active' : '' }}" data-slide-index="{{ $index }}">
                    <div class="hero-stage-grid">
                        <!-- Left Narrative -->
                        <div class="hero-stage-left">
                            <div>
                                @if($banner->badge_text)
                                    <div class="hero-badge-industrial">
                                        <span style="color: var(--brand-amber);"><i class="fa-solid fa-microchip"></i></span>
                                        {{ $banner->badge_text }}
                                    </div>
                                @endif

                                <h1 class="hero-main-title">
                                    {{ $banner->judul }}
                                </h1>

                                @if($banner->subjudul)
                                    <p class="hero-lead-text">
                                        {{ $banner->subjudul }}
                                    </p>
                                @endif
                            </div>

                            <div class="hero-actions-and-controls" style="display: flex; flex-wrap: wrap; gap: 14px; align-items: center; justify-content: space-between; margin-top: 24px;">
                                <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
                                    @if($banner->tombol_teks_1)
                                        <a href="{{ $banner->tombol_url_1 ?: route('web.jurusan.index') }}" class="btn-industrial btn-industrial-dark">
                                            <i class="fa-solid fa-compass"></i>
                                            {{ $banner->tombol_teks_1 }}
                                        </a>
                                    @endif
                                    @if($banner->tombol_teks_2)
                                        <a href="{{ $banner->tombol_url_2 ?: route('ppdb.index') }}" class="btn-industrial btn-industrial-primary">
                                            <i class="fa-solid fa-graduation-cap"></i>
                                            {{ $banner->tombol_teks_2 }}
                                        </a>
                                    @endif
                                </div>

                                @if($heroBanners->count() > 1)
                                    <div class="hero-slider-controls">
                                        <div class="hero-slider-dots">
                                            @foreach($heroBanners as $dIndex => $bDot)
                                                <button type="button" class="hero-dot {{ $dIndex === $index ? 'active' : '' }}" onclick="goToHeroSlide({{ $dIndex }})" aria-label="Slide {{ $dIndex + 1 }}"></button>
                                            @endforeach
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <button type="button" class="hero-arrow-btn" onclick="prevHeroSlide()" aria-label="Slide Sebelumnya">
                                                <i class="fa-solid fa-chevron-left"></i>
                                            </button>
                                            <span class="hero-slider-counter">{{ sprintf('%02d', $index + 1) }} / {{ sprintf('%02d', $heroBanners->count()) }}</span>
                                            <button type="button" class="hero-arrow-btn" onclick="nextHeroSlide()" aria-label="Slide Berikutnya">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Right Visual Stage -->
                        <div class="hero-stage-right">
                            <img src="{{ $banner->gambar_url }}" alt="{{ $banner->judul }}">
                            @if($banner->tag_overlay)
                                <div class="hero-stage-overlay-tag">
                                    <div style="font-size: 0.72rem; font-weight: 700; color: #38bdf8; text-transform: uppercase; font-family: var(--font-tech);">
                                        KAMPUS PENDIDIKAN VOKASI NEGERI
                                    </div>
                                    <div style="font-size: 0.95rem; font-weight: 800;">
                                        {{ $banner->tag_overlay }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <!-- Fallback Default -->
                <div class="hero-slide active">
                    <div class="hero-stage-grid">
                        <div class="hero-stage-left">
                            <div>
                                <div class="hero-badge-industrial">
                                    <span style="color: var(--brand-amber);"><i class="fa-solid fa-microchip"></i></span>
                                    PRECISION VOCATIONAL WORKSHOP • TANGGAMUS
                                </div>
                                <h1 class="hero-main-title">
                                    Menempa Keahlian Teknik, Rekayasa, &amp; Agro-Industri.
                                </h1>
                                <p class="hero-lead-text">
                                    SMKN 1 Air Naningan mempersiapkan lulusan berkompetensi tinggi yang siap terserap langsung di dunia industri, menguasai pengujian sertifikasi profesi resmi BNSP, serta memiliki mentalitas mandiri wirausaha.
                                </p>
                            </div>
                            <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
                                <a href="{{ route('web.jurusan.index') }}" class="btn-industrial btn-industrial-dark">
                                    <i class="fa-solid fa-compass"></i>
                                    Eksplorasi 3 Kejuruan
                                </a>
                                <a href="{{ route('ppdb.index') }}" class="btn-industrial btn-industrial-primary">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                    Pendaftaran PPDB 2026/2027
                                </a>
                            </div>
                        </div>
                        <div class="hero-stage-right">
                            <img src="{{ asset('images/web/hero_kampus.jpg') }}" alt="Kampus SMKN 1 Air Naningan Tanggamus">
                            <div class="hero-stage-overlay-tag">
                                <div style="font-size: 0.72rem; font-weight: 700; color: #38bdf8; text-transform: uppercase; font-family: var(--font-tech);">
                                    KAMPUS PENDIDIKAN VOKASI NEGERI
                                </div>
                                <div style="font-size: 0.95rem; font-weight: 800;">
                                    SMKN 1 Air Naningan • Tanggamus, Lampung
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- ═══ 2. WORKSHOP TELEMETRY STRIP ═══ -->
    <div class="telemetry-strip">
        <div class="telemetry-item">
            <div class="telemetry-value" style="color: var(--brand-blue);">70 : 30</div>
            <div class="telemetry-desc">Rasio Jam Praktik Bengkel vs Teori</div>
        </div>
        <div class="telemetry-item">
            <div class="telemetry-value" style="color: var(--brand-emerald);">LSP-P1</div>
            <div class="telemetry-desc">Sertifikasi Profesi Nasional BNSP</div>
        </div>
        <div class="telemetry-item">
            <div class="telemetry-value" style="color: var(--text-dark);">{{ $stats['total_siswa'] }}+</div>
            <div class="telemetry-desc">Siswa Aktif di Ekosistem Terpadu</div>
        </div>
        <div class="telemetry-item">
            <div class="telemetry-value" style="color: var(--brand-amber);">AKREDITASI B</div>
            <div class="telemetry-desc">Kualitas Terakreditasi BAN-S/M</div>
        </div>
    </div>

    <!-- ═══ 3. THE 3 SPECIFICATION SHEETS (JURUSAN VOKASI) ═══ -->
    <div class="section-header-clean">
        <span class="section-tag">PROGRAM KEAHLIAN REKAYASA</span>
        <h2 class="section-title-large">Lembar Spesifikasi Keahlian Berstandar Industri</h2>
        <p style="color: var(--text-muted); font-size: 0.95rem; max-width: 650px; margin-top: 6px;">
            Setiap program keahlian dirancang selaras dengan Standard Kompetensi Kerja Nasional Indonesia (SKKNI) dan didukung peralatan laboratorium modern.
        </p>
    </div>

    <div class="spec-sheet-grid">
        
        <!-- 01 RPL -->
        <div class="spec-sheet-card">
            <div class="spec-media">
                <img src="{{ asset('images/web/jurusan_rpl.jpg') }}" alt="Lab Rekayasa Perangkat Lunak">
            </div>
            <div class="spec-content">
                <div>
                    <div class="spec-index-no">01 / SOFTWARE</div>
                    <h3 class="spec-title">Rekayasa Perangkat Lunak (RPL)</h3>
                    <p style="font-size: 0.9rem; color: var(--text-body); line-height: 1.6;">
                        Fokus pada perancangan perangkat lunak modern, aplikasi web enterprise, komputasi awan, dan integrasi perangkat IoT berskala industri.
                    </p>

                    <table class="spec-table">
                        <tr>
                            <td>Core Stack</td>
                            <td>Fullstack Web, Android/Mobile App, Cloud Database, IoT Device Firmware</td>
                        </tr>
                        <tr>
                            <td>Fasilitas Praktik</td>
                            <td>Dedicated High-Spec PC Lab, Dedicated Local IoT Server, Fiber Optic Network</td>
                        </tr>
                        <tr>
                            <td>Standar Lisensi</td>
                            <td>Junior Web Developer &amp; Programmer (LSP-P1 BNSP)</td>
                        </tr>
                    </table>
                </div>

                <div>
                    <a href="{{ route('web.jurusan.show', 'rpl') }}" class="btn-industrial btn-industrial-outline" style="font-size: 0.82rem;">
                        Lihat Kurikulum Lengkap RPL →
                    </a>
                </div>
            </div>
        </div>

        <!-- 02 APHP -->
        <div class="spec-sheet-card reverse">
            <div class="spec-content">
                <div>
                    <div class="spec-index-no">02 / AGRO-TECH</div>
                    <h3 class="spec-title">Agribisnis Pengolahan Hasil Pertanian (APHP)</h3>
                    <p style="font-size: 0.9rem; color: var(--text-body); line-height: 1.6;">
                        Penguasaan teknologi pascapanen komoditas unggulan lokal Tanggamus (Kopi Robusta &amp; Hortikultura), pengolahan pangan higienis, serta manajemen mutu HACCP.
                    </p>

                    <table class="spec-table">
                        <tr>
                            <td>Core Focus</td>
                            <td>Hilirisasi Kopi Robusta Tanggamus, Standar Keamanan Pangan HACCP, Vacuum Packaging</td>
                        </tr>
                        <tr>
                            <td>Fasilitas Praktik</td>
                            <td>Digital Coffee Roaster, Mesin Grinding Presisi, Stainless Steel Processing Lab</td>
                        </tr>
                        <tr>
                            <td>Standar Lisensi</td>
                            <td>Pengolah Hasil Pertanian &amp; Mutu Pangan (LSP-P1 BNSP)</td>
                        </tr>
                    </table>
                </div>

                <div>
                    <a href="{{ route('web.jurusan.show', 'aphp') }}" class="btn-industrial btn-industrial-outline" style="font-size: 0.82rem;">
                        Lihat Kurikulum Lengkap APHP →
                    </a>
                </div>
            </div>
            <div class="spec-media">
                <img src="{{ asset('images/web/jurusan_aphp.jpg') }}" alt="Lab Agribisnis Hasil Pertanian">
            </div>
        </div>

        <!-- 03 TSM -->
        <div class="spec-sheet-card">
            <div class="spec-media">
                <img src="{{ asset('images/web/jurusan_tsm.jpg') }}" alt="Bengkel Teknik Sepeda Motor">
            </div>
            <div class="spec-content">
                <div>
                    <div class="spec-index-no">03 / AUTOMOTIVE</div>
                    <h3 class="spec-title">Teknik &amp; Bisnis Sepeda Motor (TSM)</h3>
                    <p style="font-size: 0.9rem; color: var(--text-body); line-height: 1.6;">
                        Pemeliharaan dan perbaikan sepeda motor modern dengan sistem injeksi elektronik (EFI), diagnosis kelistrikan komputerisasi, dan manajemen operasional bengkel resmi.
                    </p>

                    <table class="spec-table">
                        <tr>
                            <td>Core Focus</td>
                            <td>Electronic Fuel Injection (EFI), Computer Diagnostic Scanner, Overhaul Engine, Kelistrikan</td>
                        </tr>
                        <tr>
                            <td>Fasilitas Praktik</td>
                            <td>Hydraulic Bike-Lift, Multi-brand Diagnostic Scanner, Exhaust Gas Analyzer, Toolset APM</td>
                        </tr>
                        <tr>
                            <td>Standar Lisensi</td>
                            <td>Teknisi Perawatan Sepeda Motor Injeksi (LSP-P1 BNSP)</td>
                        </tr>
                    </table>
                </div>

                <div>
                    <a href="{{ route('web.jurusan.show', 'tsm') }}" class="btn-industrial btn-industrial-outline" style="font-size: 0.82rem;">
                        Lihat Kurikulum Lengkap TSM →
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- ═══ 4. TEACHING FACTORY SHOWCASE (BUKTI NYATA KARYA SISWA) ═══ -->
    <div class="tefa-strip">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; gap: 20px;">
            <div>
                <span style="font-family: var(--font-tech); font-size: 0.78rem; font-weight: 700; color: #fbbf24; text-transform: uppercase; letter-spacing: 0.08em;">
                    TEACHING FACTORY (TEFA) PRODUCTION
                </span>
                <h2 style="font-size: clamp(1.6rem, 3vw, 2.2rem); font-weight: 800; margin-top: 8px;">
                    Produk &amp; Portofolio Nyata Karya Siswa
                </h2>
            </div>
            <span style="font-size: 0.85rem; color: #94a3b8;">Pembuktian keterampilan hands-on berstandar komersial</span>
        </div>

        <div class="tefa-grid">
            <div class="tefa-item-box">
                <div>
                    <div style="font-size: 0.74rem; font-family: var(--font-tech); color: #60a5fa; font-weight: 700; margin-bottom: 8px;">
                        REKAYASA PERANGKAT LUNAK
                    </div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 10px; color: #ffffff;">
                        Sistem Presensi IoT SIRANI &amp; Smart Gate
                    </h3>
                    <p style="font-size: 0.85rem; color: #94a3b8; line-height: 1.6;">
                        Ekosistem absensi digital mandiri dengan kartu RFID dan QR-Code yang kini mengontrol kedisiplinan dan laporan presensi harian seluruh civitas sekolah.
                    </p>
                </div>
                <div style="margin-top: 20px; font-size: 0.82rem; font-weight: 700; color: #60a5fa;">
                    Dipakai Resmi di SMKN 1 Air Naningan
                </div>
            </div>

            <div class="tefa-item-box">
                <div>
                    <div style="font-size: 0.74rem; font-family: var(--font-tech); color: #34d399; font-weight: 700; margin-bottom: 8px;">
                        AGRIBISNIS HASIL PERTANIAN
                    </div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 10px; color: #ffffff;">
                        Kopi Robusta Naningan Roast &amp; Aneka Pangan
                    </h3>
                    <p style="font-size: 0.85rem; color: #94a3b8; line-height: 1.6;">
                        Produk kopi bubuk kemasan premium hasil roasting biji kopi petani Air Naningan serta aneka keripik dan produk olahan pangan berstandar kebersihan tinggi.
                    </p>
                </div>
                <div style="margin-top: 20px; font-size: 0.82rem; font-weight: 700; color: #34d399;">
                    Produksi Teaching Factory Komersial
                </div>
            </div>

            <div class="tefa-item-box">
                <div>
                    <div style="font-size: 0.74rem; font-family: var(--font-tech); color: #fbbf24; font-weight: 700; margin-bottom: 8px;">
                        TEKNIK SEPEDA MOTOR
                    </div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 10px; color: #ffffff;">
                        Unit Servis Ringan Binaan Bengkel APM
                    </h3>
                    <p style="font-size: 0.85rem; color: #94a3b8; line-height: 1.6;">
                        Layanan servis berkala, ganti oli, tune up injeksi, dan perbaikan kelistrikan kendaraan bermotor roda dua untuk masyarakat sekitar kampus sekolah.
                    </p>
                </div>
                <div style="margin-top: 20px; font-size: 0.82rem; font-weight: 700; color: #fbbf24;">
                    Layanan Servis Binaan Industri
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ 5. PPDB CALLOUT BANNER ═══ -->
    <div class="ppdb-banner-box">
        <div style="max-width: 720px;">
            <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.12); padding: 5px 14px; border-radius: 20px; font-size: 0.76rem; font-weight: 800; margin-bottom: 16px;">
                <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #34d399;"></span>
                PENERIMAAN PESERTA DIDIK BARU TP {{ date('Y') }}/{{ date('Y') + 1 }}
            </div>
            <h2 style="font-size: clamp(1.8rem, 3.2vw, 2.4rem); font-weight: 800; line-height: 1.2; margin-bottom: 14px;">
                Daftar Online Mudah dari Rumah Tanpa Biaya Pendaftaran
            </h2>
            <p style="font-size: 0.95rem; color: #cbd5e1; line-height: 1.65; margin-bottom: 28px;">
                Tersedia 3 Jalur: <strong>Reguler</strong> (Nilai Rapor), <strong>Prestasi</strong> (Piagam Lomba &amp; Tahfidz), dan <strong>Afirmasi</strong> (Pemegang KIP/PKH). Bebas biaya pendaftaran (100% Gratis).
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                <a href="{{ route('ppdb.formulir') }}" class="btn-industrial" style="background: #ffffff; color: #0f172a; font-weight: 800; padding: 12px 26px;">
                    <i class="fa-solid fa-file-signature"></i> Isi Formulir PPDB Sekarang
                </a>
                <a href="{{ route('ppdb.status') }}" class="btn-industrial" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255,255,255,0.25); color: #ffffff; padding: 12px 22px;">
                    <i class="fa-solid fa-id-badge"></i> Cek Status &amp; Cetak Kartu
                </a>
            </div>
        </div>
    </div>

    <!-- ═══ 6. WARTA & ARTIKEL RESMI SEKOLAH ═══ -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
        <div>
            <span class="section-tag">WARTA &amp; INFORMASI</span>
            <h2 style="font-size: 1.6rem; font-weight: 800; color: var(--text-dark);">Kabar Kegiatan &amp; Prestasi Kampus</h2>
        </div>
        <a href="{{ route('web.berita.index') }}" style="font-size: 0.88rem; font-weight: 700; color: var(--brand-blue);">
            Lihat Semua Warta →
        </a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-bottom: 60px;">
        @forelse($beritas->take(3) as $b)
            <div style="background: #ffffff; border: 1px solid var(--border-main); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-card); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="height: 170px; overflow: hidden;">
                        <img src="{{ $b->url_gambar_sampul ?? asset('images/web/hero_kampus.jpg') }}" alt="{{ $b->judul }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="padding: 20px;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 0.74rem;">
                            <span style="font-weight: 800; color: var(--brand-blue); text-transform: uppercase;">{{ $b->kategori }}</span>
                            <span style="color: var(--text-subtle);">•</span>
                            <span style="color: var(--text-muted);">{{ $b->tanggal_publikasi ? \Carbon\Carbon::parse($b->tanggal_publikasi)->translatedFormat('d M Y') : '' }}</span>
                        </div>
                        <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-dark); line-height: 1.4; margin-bottom: 10px;">
                            <a href="{{ route('web.berita.show', $b->slug) }}" style="color: inherit;">{{ $b->judul }}</a>
                        </h3>
                        <p style="font-size: 0.84rem; color: var(--text-body); line-height: 1.55;">
                            {{ Str::limit(strip_tags($b->konten), 100) }}
                        </p>
                    </div>
                </div>
                <div style="padding: 0 20px 20px 20px;">
                    <a href="{{ route('web.berita.show', $b->slug) }}" style="font-size: 0.84rem; font-weight: 700; color: var(--brand-blue); display: inline-flex; align-items: center; gap: 4px;">
                        Baca Selengkapnya <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        @empty
            <p style="color: var(--text-muted);">Belum ada warta dipublikasikan.</p>
        @endforelse
    </div>

</div>
@endsection

@push('scripts')
<script>
    let currentHeroIndex = 0;
    const heroSlides = document.querySelectorAll('.hero-slide');
    const totalHeroSlides = heroSlides.length;
    let heroAutoTimer = null;

    function showHeroSlide(index) {
        if (!totalHeroSlides) return;
        if (index >= totalHeroSlides) index = 0;
        if (index < 0) index = totalHeroSlides - 1;
        currentHeroIndex = index;

        heroSlides.forEach((slide, i) => {
            if (i === currentHeroIndex) {
                slide.classList.add('active');
            } else {
                slide.classList.remove('active');
            }
        });
    }

    function nextHeroSlide() {
        showHeroSlide(currentHeroIndex + 1);
        resetHeroTimer();
    }

    function prevHeroSlide() {
        showHeroSlide(currentHeroIndex - 1);
        resetHeroTimer();
    }

    function goToHeroSlide(index) {
        showHeroSlide(index);
        resetHeroTimer();
    }

    function resetHeroTimer() {
        if (heroAutoTimer) clearInterval(heroAutoTimer);
        if (totalHeroSlides > 1) {
            heroAutoTimer = setInterval(nextHeroSlide, 7000);
        }
    }

    if (totalHeroSlides > 1) {
        const sliderEl = document.getElementById('heroStageSlider');
        if (sliderEl) {
            sliderEl.addEventListener('mouseenter', () => clearInterval(heroAutoTimer));
            sliderEl.addEventListener('mouseleave', resetHeroTimer);
        }
        resetHeroTimer();
    }
</script>
@endpush
