@extends('web.layouts.app')

@section('title', 'SMKN 1 Air Naningan — Precision Vocational Academy')
@section('meta_description', 'Official Portal SMKN 1 Air Naningan. Lembaga Pendidikan Vokasi Unggulan Rekayasa Perangkat Lunak, Agro-Teknologi Pangan, dan Teknik Sepeda Motor di Tanggamus.')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/web-beranda.css') }}?v={{ filemtime(public_path('css/web-beranda.css')) }}">
@endpush

@section('content')
<div class="container">

    <!-- ═══ 1. HERO: FULL PHOTO DYNAMIC SLIDER ═══ -->
    <div class="hero-full-wrapper" id="heroFullSliderWrapper">
        <div class="hero-full-slider">
            @forelse($heroBanners as $index => $banner)
                @php
                    $posisiTeks = $banner->posisi_teks ?: 'left';
                    $scrimClass = 'hero-scrim-' . $posisiTeks;
                    $alignClass = 'hero-text-align-' . $posisiTeks;
                @endphp
                <div class="hero-full-slide {{ $index === 0 ? 'active' : '' }}" data-slide-index="{{ $index }}">
                    <!-- Full Background Photo -->
                    <img src="{{ $banner->gambar_url }}" class="hero-full-photo" alt="{{ $banner->judul }}">

                    <!-- Dynamic Scrim Gradient Overlay based on text placement -->
                    <div class="hero-scrim-overlay {{ $scrimClass }}"></div>

                    <!-- Slide Narrative Content -->
                    <div class="container hero-full-content-box">
                        <div class="{{ $alignClass }}">
                            @if($banner->badge_text)
                                <div class="hero-pill-badge">
                                    <i class="fa-solid fa-sparkles"></i>
                                    <span>{{ $banner->badge_text }}</span>
                                </div>
                            @endif

                            <h1 class="hero-banner-title">
                                {{ $banner->judul }}
                            </h1>

                            @if($banner->subjudul)
                                <p class="hero-banner-sub">
                                    {{ $banner->subjudul }}
                                </p>
                            @endif

                            <div class="hero-btn-group">
                                @if($banner->tombol_teks_1)
                                    <a href="{{ $banner->tombol_url_1 ?: route('web.jurusan.index') }}" class="btn-hero-solid">
                                        <span>{{ $banner->tombol_teks_1 }}</span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                @endif
                                @if($banner->tombol_teks_2)
                                    <a href="{{ $banner->tombol_url_2 ?: route('ppdb.index') }}" class="btn-hero-glass">
                                        <i class="fa-solid fa-graduation-cap"></i>
                                        <span>{{ $banner->tombol_teks_2 }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="hero-full-slide active" data-slide-index="0">
                    <img src="{{ asset('images/web/hero_kampus.jpg') }}" class="hero-full-photo" alt="SMKN 1 Air Naningan">
                    <div class="hero-scrim-overlay hero-scrim-left"></div>
                    <div class="container hero-full-content-box">
                        <div class="hero-text-align-left">
                            <div class="hero-pill-badge">
                                <i class="fa-solid fa-microchip"></i>
                                <span>PRECISION VOCATIONAL ACADEMY</span>
                            </div>
                            <h1 class="hero-banner-title">
                                Menempa Keahlian Teknik, Rekayasa, &amp; Agro-Industri.
                            </h1>
                            <p class="hero-banner-sub">
                                SMKN 1 Air Naningan mempersiapkan lulusan berkompetensi tinggi yang siap terserap langsung di dunia industri.
                            </p>
                            <div class="hero-btn-group">
                                <a href="{{ route('web.jurusan.index') }}" class="btn-hero-solid">
                                    <span>Eksplorasi 3 Kejuruan</span>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                                <a href="{{ route('ppdb.index') }}" class="btn-hero-glass">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                    <span>Pendaftaran PPDB</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        @if($heroBanners->count() > 1)
            <!-- Floating Navigation Arrows -->
            <button type="button" class="hero-slider-nav-btn prev" onclick="prevHeroSlide()" aria-label="Slide Sebelumnya">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" class="hero-slider-nav-btn next" onclick="nextHeroSlide()" aria-label="Slide Berikutnya">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <!-- Bottom Pagination Pill Bar -->
            <div class="hero-slider-indicator-bar">
                <div class="hero-indicator-dots">
                    @foreach($heroBanners as $dIndex => $bDot)
                        <button type="button" class="hero-indicator-dot {{ $dIndex === 0 ? 'active' : '' }}" onclick="goToHeroSlide({{ $dIndex }})" aria-label="Slide {{ $dIndex + 1 }}"></button>
                    @endforeach
                </div>
                <span class="hero-slide-num" id="heroSlideCounter">01 / {{ sprintf('%02d', $heroBanners->count()) }}</span>
            </div>
        @endif
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

    <!-- ═══ 3. INTERACTIVE EXPANDING PANORAMIC DECK ═══ -->
    <section class="expanding-deck-section" id="program-keahlian">
        <div class="section-header-clean" style="margin-bottom: 20px;">
            <span class="section-tag">PROGRAM KEAHLIAN UNGGULAN</span>
            <h2 class="section-title-large">Program Keahlian Berstandar Industri</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem; max-width: 680px; margin-top: 6px;">
                Arahkan kursor atau sentuh kartu kejuruan untuk melihat profil kurikulum, spesifikasi laboratorium, dan lisensi BNSP berstandar nasional.
            </p>
        </div>

        <!-- The 3 Interactive Expanding Cards -->
        <div class="expanding-deck" role="region" aria-label="Program Keahlian SMK">
            
            <!-- CARD 1: RPL (ACTIVE DEFAULT) -->
            <div class="deck-card active" tabindex="0" role="button" aria-expanded="true" aria-label="Rekayasa Perangkat Lunak">
                <img src="{{ asset('images/web/jurusan_rpl.jpg') }}" alt="Laboratorium Rekayasa Perangkat Lunak" class="deck-bg-img">
                <div class="deck-overlay"></div>

                <!-- Collapsed State (Visible when shrunk) -->
                <div class="deck-collapsed-content">
                    <div class="deck-collapsed-badge">💻</div>
                    <div class="deck-collapsed-title">01 / Rekayasa Perangkat Lunak</div>
                    <div class="deck-collapsed-index">01</div>
                </div>

                <!-- Expanded State (Visible when wide) -->
                <div class="deck-expanded-content">
                    <div class="deck-top-badge blue">
                        💻 01 / SOFTWARE ENGINEERING &amp; IOT
                    </div>
                    <h3 class="deck-title">Rekayasa Perangkat Lunak (RPL)</h3>
                    <p class="deck-desc">
                        Fokus pada perancangan arsitektur web enterprise fullstack, aplikasi mobile modern, komputasi awan (*cloud database*), serta integrasi mikrokontroler IoT presisi.
                    </p>
                    <div class="deck-chips">
                        <span class="deck-chip">✓ Fullstack Web &amp; Mobile</span>
                        <span class="deck-chip">✓ IoT Microcontroller SIRANI</span>
                        <span class="deck-chip">✓ Dedicated PC Lab Core i7</span>
                        <span class="deck-chip">✓ Lisensi LSP-P1 BNSP</span>
                    </div>
                    <a href="{{ route('web.jurusan.show', 'rpl') }}" class="deck-btn-cta">
                        <span>Eksplorasi Kurikulum &amp; Lab RPL</span>
                        <span>→</span>
                    </a>
                </div>
            </div>

            <!-- CARD 2: APHP -->
            <div class="deck-card" tabindex="0" role="button" aria-expanded="false" aria-label="Agribisnis Pengolahan Hasil Pertanian">
                <img src="{{ asset('images/web/jurusan_aphp.jpg') }}" alt="Laboratorium Agribisnis Hasil Pertanian" class="deck-bg-img">
                <div class="deck-overlay"></div>

                <!-- Collapsed State -->
                <div class="deck-collapsed-content">
                    <div class="deck-collapsed-badge">🌿</div>
                    <div class="deck-collapsed-title">02 / Agribisnis Hasil Pertanian</div>
                    <div class="deck-collapsed-index">02</div>
                </div>

                <!-- Expanded State -->
                <div class="deck-expanded-content">
                    <div class="deck-top-badge green">
                        🌿 02 / AGRO-TECHNOLOGY &amp; FOOD SCIENCE
                    </div>
                    <h3 class="deck-title">Agribisnis Pengolahan Hasil Pertanian</h3>
                    <p class="deck-desc">
                        Hilirisasi komoditas unggulan kopi robusta Tanggamus petik merah lereng Air Naningan, mesin roasting digital, tata kelola mutu higienis HACCP, dan wirausaha pangan.
                    </p>
                    <div class="deck-chips">
                        <span class="deck-chip">✓ Roasting Kopi Robusta TEFA</span>
                        <span class="deck-chip">✓ Pengolahan Pangan HACCP</span>
                        <span class="deck-chip">✓ Stainless Steel Processing Lab</span>
                        <span class="deck-chip">✓ Lisensi QC Pangan BNSP</span>
                    </div>
                    <a href="{{ route('web.jurusan.show', 'aphp') }}" class="deck-btn-cta">
                        <span>Eksplorasi Kurikulum &amp; Lab APHP</span>
                        <span>→</span>
                    </a>
                </div>
            </div>

            <!-- CARD 3: TSM -->
            <div class="deck-card" tabindex="0" role="button" aria-expanded="false" aria-label="Teknik & Bisnis Sepeda Motor">
                <img src="{{ asset('images/web/jurusan_tsm.jpg') }}" alt="Bengkel Teknik Sepeda Motor" class="deck-bg-img">
                <div class="deck-overlay"></div>

                <!-- Collapsed State -->
                <div class="deck-collapsed-content">
                    <div class="deck-collapsed-badge">⚙️</div>
                    <div class="deck-collapsed-title">03 / Teknik Sepeda Motor</div>
                    <div class="deck-collapsed-index">03</div>
                </div>

                <!-- Expanded State -->
                <div class="deck-expanded-content">
                    <div class="deck-top-badge amber">
                        ⚙️ 03 / AUTOMOTIVE ENGINEERING
                    </div>
                    <h3 class="deck-title">Teknik &amp; Bisnis Sepeda Motor (TSM)</h3>
                    <p class="deck-desc">
                        Teknologi injeksi bahan bakar elektronik (EFI), diagnostik kelistrikan komputerisasi, overhaul mesin presisi, dan tata kelola manajemen bengkel resmi berlisensi.
                    </p>
                    <div class="deck-chips">
                        <span class="deck-chip">✓ Diagnostic Scanner EFI</span>
                        <span class="deck-chip">✓ Hydraulic Bike-Lift Workshop</span>
                        <span class="deck-chip">✓ Overhaul Engine &amp; CVT</span>
                        <span class="deck-chip">✓ Lisensi Teknisi Motor BNSP</span>
                    </div>
                    <a href="{{ route('web.jurusan.show', 'tsm') }}" class="deck-btn-cta">
                        <span>Eksplorasi Kurikulum &amp; Bengkel TSM</span>
                        <span>→</span>
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- ═══ 4. TEACHING FACTORY SHOWCASE (BUKTI NYATA KARYA SISWA) ═══ -->
    <div class="tefa-strip">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; gap: 20px;">
            <div>
                <span style="font-family: var(--font-tech); font-size: 0.78rem; font-weight: 700; color: #fbbf24; text-transform: uppercase; letter-spacing: 0.08em;">
                    TEACHING FACTORY (TEFA) &amp; BENGKEL PRODUKSI
                </span>
                <h2 style="font-size: clamp(1.6rem, 3vw, 2.2rem); font-weight: 800; margin-top: 8px;">
                    Produk &amp; Portofolio Nyata Karya Siswa
                </h2>
            </div>
            <span style="font-size: 0.85rem; color: #94a3b8; max-width: 420px; text-align: right;">
                Pembuktian keahlian praktis berstandar komersial yang diproduksi langsung di workshop sekolah dan dinikmati masyarakat.
            </span>
        </div>

        <div class="tefa-grid">
            <!-- 01 RPL TEFA -->
            <div class="tefa-item-box">
                <div class="tefa-thumb-box">
                    <img src="{{ asset('images/web/jurusan_rpl.jpg') }}" alt="Proyek IoT SIRANI RPL" class="tefa-thumb-img">
                    <div class="tefa-badge-float rpl">
                        <i class="fa-solid fa-microchip"></i> IOT &amp; SOFTWARE PRODUCTION
                    </div>
                </div>
                <div class="tefa-body">
                    <div>
                        <h3 class="tefa-item-title">
                            Sistem Presensi IoT SIRANI &amp; Smart Gate
                        </h3>
                        <p class="tefa-item-desc">
                            Inovasi presensi digital terpadu berbasis pembaca kartu RFID dan mikrokontroler karya siswa RPL. Aktif mencatat kedisiplinan dan notifikasi kehadiran orang tua secara real-time.
                        </p>
                    </div>
                    <div class="tefa-footer-bar">
                        <div class="tefa-status-pill" style="color: #38bdf8;">
                            <span class="dot" style="background: #38bdf8; box-shadow: 0 0 0 2px rgba(56,189,248,0.25);"></span>
                            <span>Terpasang Resmi di Kampus</span>
                        </div>
                        <a href="{{ route('portal.ortu.index') }}" class="btn-tefa-cta blue">
                            <span>Lihat Demo</span> <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 02 APHP TEFA -->
            <div class="tefa-item-box">
                <div class="tefa-thumb-box">
                    <img src="{{ asset('images/web/jurusan_aphp.jpg') }}" alt="Kopi Robusta Naningan Roast APHP" class="tefa-thumb-img">
                    <div class="tefa-badge-float aphp">
                        <i class="fa-solid fa-mug-hot"></i> FINE ROBUSTA TANGGAMUS
                    </div>
                </div>
                <div class="tefa-body">
                    <div>
                        <h3 class="tefa-item-title">
                            Kopi Robusta Naningan Roast &amp; Aneka Pangan
                        </h3>
                        <p class="tefa-item-desc">
                            Biji kopi petik merah perkebunan lereng Air Naningan diproses sangrai (*medium-dark roast*) dan dikemas modern berkatup aroma, berpadu aneka keripik pisang oven higienis.
                        </p>
                    </div>
                    <div class="tefa-footer-bar">
                        <div class="tefa-status-pill" style="color: #34d399;">
                            <span class="dot" style="background: #34d399; box-shadow: 0 0 0 2px rgba(52,211,153,0.25);"></span>
                            <span>Produk Siap Pesan (Komersial)</span>
                        </div>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sekolah->telepon ?? '6281234567890') }}?text=Halo%20Admin%20TEFA%20APHP%20SMKN%201%20Air%20Naningan,%20saya%20tertarik%20memesan%20produk%20Kopi%20Naningan%20Roast" target="_blank" rel="noopener" class="btn-tefa-cta green">
                            <i class="fa-brands fa-whatsapp"></i> <span>Pesan Produk</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 03 TSM TEFA -->
            <div class="tefa-item-box">
                <div class="tefa-thumb-box">
                    <img src="{{ asset('images/web/jurusan_tsm.jpg') }}" alt="Bengkel Servis Motor TSM" class="tefa-thumb-img">
                    <div class="tefa-badge-float tsm">
                        <i class="fa-solid fa-screwdriver-wrench"></i> BENGKEL BINAAN INDUSTRI
                    </div>
                </div>
                <div class="tefa-body">
                    <div>
                        <h3 class="tefa-item-title">
                            Pos Servis Ringan &amp; Tune-Up Injeksi
                        </h3>
                        <p class="tefa-item-desc">
                            Layanan servis berkala kendaraan roda dua masyarakat sekitar: pembersihan injector, tune up kelistrikan, ganti oli, dan perawatan CVT standar operasional bengkel APM resmi.
                        </p>
                    </div>
                    <div class="tefa-footer-bar">
                        <div class="tefa-status-pill" style="color: #fbbf24;">
                            <span class="dot" style="background: #fbbf24; box-shadow: 0 0 0 2px rgba(251,191,36,0.25);"></span>
                            <span>Buka Jam KBM Praktik</span>
                        </div>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sekolah->telepon ?? '6281234567890') }}?text=Halo%20Bengkel%20TSM%20SMKN%201%20Air%20Naningan,%20saya%20ingin%20jadwal%20servis%20motor" target="_blank" rel="noopener" class="btn-tefa-cta amber">
                            <i class="fa-solid fa-wrench"></i> <span>Booking Servis</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ 5. PPDB CALLOUT BANNER (ADMIN CUSTOMIZABLE) ═══ -->
    @if(!$ppdbBanner || $ppdbBanner->is_active)
    @php
        $ppdbPosisiTeks = ($ppdbBanner && $ppdbBanner->posisi_teks) ? $ppdbBanner->posisi_teks : 'left';
        $ppdbImgUrl = ($ppdbBanner && $ppdbBanner->gambar) 
            ? $ppdbBanner->gambar_url 
            : asset('images/web/ppdb_banner_bg.jpg');
        $ppdbBadge = $ppdbBanner ? $ppdbBanner->badge_text : ('GELOMBANG 1 TP ' . date('Y') . '/' . (date('Y') + 1) . ' • BEBAS BIAYA PENDAFTARAN (100% GRATIS)');
        $ppdbJudul = ($ppdbBanner && $ppdbBanner->judul) 
            ? $ppdbBanner->judul 
            : 'Daftar Online Mudah dari Rumah, Siap Cetak Generasi Vokasi Berkarakter';
        $ppdbSubjudul = ($ppdbBanner && $ppdbBanner->subjudul) 
            ? $ppdbBanner->subjudul 
            : 'Membuka 3 Jalur: Reguler (Nilai Rapor), Prestasi (Piagam Lomba & Tahfidz), dan Afirmasi (KIP / PKH). Bebas uang gedung (SPI), didukung laboratorium bengkel presisi modern, serta terhubung sertifikasi kerja resmi BNSP.';
        
        $rawPills = $ppdbBanner 
            ? ($ppdbBanner->tag_overlay ?? '') 
            : 'Bebas Biaya Pendaftaran | Tanpa Uang Gedung (SPI) | Lisensi Sertifikasi BNSP | Penyaluran Kerja & Industri';
        $pills = array_filter(array_map('trim', preg_split('/[|,]/', $rawPills)));

        $btn1Text = $ppdbBanner ? $ppdbBanner->tombol_teks_1 : 'Isi Formulir PPDB Sekarang';
        $btn1Url  = ($ppdbBanner && $ppdbBanner->tombol_url_1) ? $ppdbBanner->tombol_url_1 : route('ppdb.formulir');
        
        $btn2Text = $ppdbBanner ? $ppdbBanner->tombol_teks_2 : 'Cek Status Seleksi';
        $btn2Url  = ($ppdbBanner && $ppdbBanner->tombol_url_2) ? $ppdbBanner->tombol_url_2 : route('ppdb.status');

        $btn3Text = ($ppdbBanner && $ppdbBanner->tombol_teks_3 !== null) ? $ppdbBanner->tombol_teks_3 : 'Tanya Panitia PPDB';
        $btn3Url  = ($ppdbBanner && $ppdbBanner->tombol_url_3) 
            ? $ppdbBanner->tombol_url_3 
            : ('https://wa.me/' . preg_replace('/[^0-9]/', '', $sekolah->telepon ?? '6281234567890') . '?text=' . urlencode('Halo Panitia PPDB ' . ($sekolah->nama_sekolah ?? 'SMKN 1 Air Naningan') . ', saya ingin bertanya seputar pendaftaran'));
        
        // Dynamic alignment styles
        $alignStyle = 'text-align: left; margin-right: auto; margin-left: 0;';
        $flexJustify = 'justify-content: flex-start;';
        $scrimStyle = 'background: linear-gradient(90deg, rgba(8, 14, 30, 0.96) 0%, rgba(11, 20, 45, 0.92) 45%, rgba(15, 23, 42, 0.75) 72%, rgba(15, 23, 42, 0.48) 100%);';

        if ($ppdbPosisiTeks === 'center') {
            $alignStyle = 'text-align: center; margin-left: auto; margin-right: auto;';
            $flexJustify = 'justify-content: center;';
            $scrimStyle = 'background: radial-gradient(circle at center, rgba(8, 14, 30, 0.88) 0%, rgba(11, 20, 45, 0.94) 100%);';
        } elseif ($ppdbPosisiTeks === 'right') {
            $alignStyle = 'text-align: right; margin-left: auto; margin-right: 0;';
            $flexJustify = 'justify-content: flex-end;';
            $scrimStyle = 'background: linear-gradient(270deg, rgba(8, 14, 30, 0.96) 0%, rgba(11, 20, 45, 0.92) 45%, rgba(15, 23, 42, 0.75) 72%, rgba(15, 23, 42, 0.48) 100%);';
        }
    @endphp

    <div class="ppdb-banner-box">
        <img src="{{ $ppdbImgUrl }}" alt="{{ $ppdbJudul }}" class="ppdb-bg-media">
        <div class="ppdb-scrim-overlay" style="{{ $scrimStyle }}"></div>

        <div class="ppdb-content-relative" style="{{ $alignStyle }}">
            @if(!empty($ppdbBadge))
                <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.14); backdrop-filter: blur(8px); padding: 6px 16px; border-radius: 9999px; font-size: 0.76rem; font-weight: 800; margin-bottom: 18px; border: 1px solid rgba(255,255,255,0.25);">
                    <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #34d399; box-shadow: 0 0 0 2px rgba(52,211,153,0.3);"></span>
                    <span>{{ $ppdbBadge }}</span>
                </div>
            @endif
            
            <h2 style="font-size: clamp(1.8rem, 3.4vw, 2.5rem); font-weight: 800; line-height: 1.18; margin-bottom: 16px; text-shadow: 0 2px 8px rgba(0,0,0,0.4);">
                {{ $ppdbJudul }}
            </h2>
            
            @if(!empty($ppdbSubjudul))
                <p style="font-size: 0.98rem; color: #cbd5e1; line-height: 1.65; margin-bottom: 22px; text-shadow: 0 1px 4px rgba(0,0,0,0.3);">
                    {!! nl2br(e($ppdbSubjudul)) !!}
                </p>
            @endif

            @if(count($pills) > 0)
                <div class="ppdb-benefit-pills" style="{{ $flexJustify }}">
                    @foreach($pills as $pill)
                        <span class="ppdb-benefit-pill"><i class="fa-solid fa-check"></i> {{ $pill }}</span>
                    @endforeach
                </div>
            @endif

            <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; {{ $flexJustify }}">
                @if(!empty($btn1Text) && !empty($btn1Url))
                    <a href="{{ $btn1Url }}" class="btn-industrial" style="background: #ffffff; color: #0f172a; font-weight: 800; padding: 13px 28px; border-radius: 12px; box-shadow: 0 10px 25px -4px rgba(0,0,0,0.3);">
                        <i class="fa-solid fa-file-signature"></i> {{ $btn1Text }}
                    </a>
                @endif

                @if(!empty($btn2Text) && !empty($btn2Url))
                    <a href="{{ $btn2Url }}" class="btn-industrial" style="background: rgba(255, 255, 255, 0.14); border: 1px solid rgba(255,255,255,0.3); color: #ffffff; padding: 13px 22px; border-radius: 12px;">
                        <i class="fa-solid fa-id-badge"></i> {{ $btn2Text }}
                    </a>
                @endif

                @if(!empty($btn3Text) && !empty($btn3Url))
                    <a href="{{ $btn3Url }}" target="_blank" rel="noopener" class="btn-ppdb-wa">
                        <i class="fa-brands fa-whatsapp"></i> {{ $btn3Text }}
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endif

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
    const heroSlides = document.querySelectorAll('.hero-full-slide');
    const heroDots = document.querySelectorAll('.hero-indicator-dot');
    const heroCounter = document.getElementById('heroSlideCounter');
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

        heroDots.forEach((dot, i) => {
            if (i === currentHeroIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });

        if (heroCounter) {
            const currentStr = String(currentHeroIndex + 1).padStart(2, '0');
            const totalStr = String(totalHeroSlides).padStart(2, '0');
            heroCounter.textContent = `${currentStr} / ${totalStr}`;
        }
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
            heroAutoTimer = setInterval(nextHeroSlide, 6000);
        }
    }

    if (totalHeroSlides > 1) {
        const sliderWrapper = document.getElementById('heroFullSliderWrapper');
        if (sliderWrapper) {
            sliderWrapper.addEventListener('mouseenter', () => clearInterval(heroAutoTimer));
            sliderWrapper.addEventListener('mouseleave', resetHeroTimer);

            // Touch Swipe Support for Mobile
            let touchStartX = 0;
            let touchEndX = 0;
            sliderWrapper.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
                clearInterval(heroAutoTimer);
            }, { passive: true });

            sliderWrapper.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                const diff = touchStartX - touchEndX;
                if (Math.abs(diff) > 40) {
                    if (diff > 0) {
                        nextHeroSlide();
                    } else {
                        prevHeroSlide();
                    }
                } else {
                    resetHeroTimer();
                }
            }, { passive: true });
        }
        resetHeroTimer();
    }

    // Interactive Expanding Panoramic Deck (Konsep 1: Otomatis Melebar Mulus saat Mouse Digeser)
    function setActiveDeckCard(card) {
        if (!card || card.classList.contains('active')) return;
        const allCards = document.querySelectorAll('.deck-card');
        allCards.forEach(c => {
            c.classList.remove('active');
            c.setAttribute('aria-expanded', 'false');
        });
        card.classList.add('active');
        card.setAttribute('aria-expanded', 'true');
    }

    const deckCards = document.querySelectorAll('.deck-card');
    deckCards.forEach(card => {
        // Otomatis melebar mulus saat mouse digeser ke kartu (hover)
        card.addEventListener('mouseenter', () => {
            setActiveDeckCard(card);
        });

        // Dukungan klik / sentuhan mobile
        card.addEventListener('click', () => {
            setActiveDeckCard(card);
        });

        // Aksesibilitas keyboard (Enter / Spasi)
        card.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                setActiveDeckCard(card);
            }
        });
    });
</script>
@endpush
