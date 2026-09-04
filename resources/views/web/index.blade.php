@extends('web.layouts.app')

@section('title', 'SMKN 1 Air Naningan — Precision Vocational Academy')
@section('meta_description', 'Official Portal SMKN 1 Air Naningan. Lembaga Pendidikan Vokasi Unggulan Rekayasa Perangkat Lunak, Agro-Teknologi Pangan, dan Teknik Sepeda Motor di Tanggamus.')

@push('styles')
<style>
    /* ═══ FULL PHOTO HERO BANNER SLIDER ═══ */
    .hero-full-wrapper {
        position: relative;
        margin-top: 24px;
        margin-bottom: 40px;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.18);
        background: #090d16;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }

    .hero-full-slider {
        position: relative;
        width: 100%;
        min-height: 560px;
        height: 580px;
        max-height: 82vh;
        overflow: hidden;
    }

    .hero-full-slide {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        transform: scale(1.02);
        display: flex;
        align-items: center;
        z-index: 1;
    }

    .hero-full-slide.active {
        opacity: 1;
        visibility: visible;
        transform: scale(1);
        z-index: 2;
    }

    /* Background Full Photo */
    .hero-full-photo {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center 35%;
        z-index: 1;
        transition: transform 8s ease;
    }

    .hero-full-slide.active .hero-full-photo {
        transform: scale(1.04);
    }

    /* Dynamic Scrim Overlays based on Text Alignment */
    .hero-scrim-overlay {
        position: absolute;
        inset: 0;
        z-index: 2;
        pointer-events: none;
    }

    /* Tata Letak 1: Kiri (Left Alignment) */
    .hero-scrim-left {
        background: 
            linear-gradient(90deg, rgba(15, 23, 42, 0.94) 0%, rgba(15, 23, 42, 0.78) 42%, rgba(15, 23, 42, 0.3) 75%, rgba(15, 23, 42, 0.1) 100%),
            linear-gradient(0deg, rgba(15, 23, 42, 0.7) 0%, transparent 40%);
    }

    /* Tata Letak 2: Tengah (Center Alignment) */
    .hero-scrim-center {
        background: 
            radial-gradient(ellipse at center, rgba(15, 23, 42, 0.62) 0%, rgba(15, 23, 42, 0.88) 100%),
            linear-gradient(180deg, rgba(15, 23, 42, 0.4) 0%, rgba(15, 23, 42, 0.8) 100%);
    }

    /* Tata Letak 3: Kanan (Right Alignment) */
    .hero-scrim-right {
        background: 
            linear-gradient(270deg, rgba(15, 23, 42, 0.94) 0%, rgba(15, 23, 42, 0.78) 42%, rgba(15, 23, 42, 0.3) 75%, rgba(15, 23, 42, 0.1) 100%),
            linear-gradient(0deg, rgba(15, 23, 42, 0.7) 0%, transparent 40%);
    }

    /* Inner Narrative Container */
    .hero-full-content-box {
        position: relative;
        z-index: 3;
        width: 100%;
        padding: 55px 76px;
    }

    /* Text Alignments */
    .hero-text-align-left {
        max-width: 680px;
        margin-right: auto;
        text-align: left;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .hero-text-align-center {
        max-width: 820px;
        margin: 0 auto;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .hero-text-align-right {
        max-width: 680px;
        margin-left: auto;
        text-align: right;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    /* Badge */
    .hero-pill-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        background: rgba(255, 255, 255, 0.14);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 9999px;
        font-family: var(--font-tech);
        font-size: 0.78rem;
        font-weight: 700;
        color: #ffffff;
        letter-spacing: 0.05em;
        margin-bottom: 20px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    }

    .hero-pill-badge i {
        color: #38bdf8;
    }

    /* Headline Title */
    .hero-banner-title {
        font-size: clamp(2.2rem, 4.2vw, 3.4rem);
        font-weight: 800;
        color: #ffffff;
        line-height: 1.15;
        letter-spacing: -0.025em;
        margin-bottom: 18px;
        text-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    }

    /* Subtitle */
    .hero-banner-sub {
        font-size: clamp(0.98rem, 1.6vw, 1.15rem);
        color: rgba(241, 245, 249, 0.94);
        line-height: 1.65;
        margin-bottom: 32px;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        max-width: 640px;
    }

    /* Button Group */
    .hero-btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        align-items: center;
    }

    .hero-text-align-center .hero-btn-group {
        justify-content: center;
    }

    .hero-text-align-right .hero-btn-group {
        justify-content: flex-end;
    }

    .btn-hero-solid {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #2563eb;
        color: #ffffff;
        font-size: 0.95rem;
        font-weight: 700;
        padding: 13px 26px;
        border-radius: 12px;
        text-decoration: none;
        box-shadow: 0 10px 25px -4px rgba(37, 99, 235, 0.55);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-hero-solid:hover {
        background: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 14px 30px -4px rgba(37, 99, 235, 0.7);
        color: #ffffff;
    }

    .btn-hero-glass {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.16);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        color: #ffffff;
        font-size: 0.95rem;
        font-weight: 700;
        padding: 13px 26px;
        border-radius: 12px;
        text-decoration: none;
        border: 1px solid rgba(255, 255, 255, 0.35);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-hero-glass:hover {
        background: rgba(255, 255, 255, 0.28);
        border-color: rgba(255, 255, 255, 0.6);
        transform: translateY(-2px);
        color: #ffffff;
    }

    /* Prev & Next Floating Navigation Arrows */
    .hero-slider-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        cursor: pointer;
        z-index: 10;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .hero-slider-nav-btn:hover {
        background: #2563eb;
        border-color: #3b82f6;
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4);
    }

    .hero-slider-nav-btn.prev {
        left: 24px;
    }

    .hero-slider-nav-btn.next {
        right: 24px;
    }

    /* Bottom Slider Indicator Bar */
    .hero-slider-indicator-bar {
        position: absolute;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 12px;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 6px 18px;
        border-radius: 9999px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
    }

    .hero-indicator-dots {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .hero-indicator-dot {
        width: 8px;
        height: 8px;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.35);
        border: none;
        padding: 0;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .hero-indicator-dot.active {
        width: 26px;
        background: #38bdf8;
    }

    .hero-slide-num {
        font-family: var(--font-tech);
        font-size: 0.76rem;
        font-weight: 700;
        color: #e2e8f0;
        letter-spacing: 0.05em;
    }

    /* Mobile Responsive for Full Slider */
    @media (max-width: 900px) {
        .hero-full-slider {
            height: auto;
            min-height: 520px;
        }
        .hero-full-content-box {
            padding: 44px 32px;
        }
        .hero-slider-nav-btn {
            display: none;
        }
    }

    @media (max-width: 640px) {
        .hero-full-wrapper {
            margin-top: 14px;
            margin-bottom: 28px;
            border-radius: 18px;
        }
        .hero-full-slider {
            min-height: 500px;
        }
        .hero-full-content-box {
            padding: 46px 20px 76px 20px !important;
        }
        .hero-scrim-left,
        .hero-scrim-center,
        .hero-scrim-right {
            background: 
                linear-gradient(180deg, rgba(15, 23, 42, 0.88) 0%, rgba(15, 23, 42, 0.65) 50%, rgba(15, 23, 42, 0.94) 100%) !important;
        }
        .hero-banner-title {
            font-size: 1.85rem !important;
        }
        .hero-banner-sub {
            font-size: 0.92rem !important;
        }
        .hero-text-align-left,
        .hero-text-align-center,
        .hero-text-align-right {
            max-width: 100% !important;
            text-align: left !important;
            align-items: flex-start !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        .hero-btn-group {
            width: 100%;
            flex-direction: column;
            align-items: stretch !important;
        }
        .btn-hero-solid, .btn-hero-glass {
            justify-content: center;
            width: 100%;
        }
        .hero-slider-indicator-bar {
            bottom: 16px;
        }
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
        background: linear-gradient(180deg, #090d16 0%, #0f172a 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: var(--radius-xl);
        padding: clamp(36px, 5vw, 54px);
        color: #ffffff;
        margin-bottom: 70px;
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.35);
        position: relative;
        overflow: hidden;
    }

    .tefa-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(310px, 1fr));
        gap: 24px;
        margin-top: 36px;
    }

    .tefa-item-box {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .tefa-item-box:hover {
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(255, 255, 255, 0.22);
        transform: translateY(-4px);
        box-shadow: 0 14px 30px -5px rgba(0, 0, 0, 0.4);
    }

    .tefa-thumb-box {
        position: relative;
        height: 180px;
        width: 100%;
        overflow: hidden;
        background: #020617;
    }

    .tefa-thumb-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .tefa-item-box:hover .tefa-thumb-img {
        transform: scale(1.05);
    }

    .tefa-badge-float {
        position: absolute;
        top: 12px;
        left: 12px;
        background: rgba(15, 23, 42, 0.82);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 4px 10px;
        border-radius: 6px;
        font-family: var(--font-tech);
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.04em;
    }

    .tefa-badge-float.rpl { color: #38bdf8; border-color: rgba(56, 189, 248, 0.35); }
    .tefa-badge-float.aphp { color: #34d399; border-color: rgba(52, 211, 153, 0.35); }
    .tefa-badge-float.tsm { color: #fbbf24; border-color: rgba(251, 191, 36, 0.35); }

    .tefa-body {
        padding: 22px;
        display: flex;
        flex-direction: column;
        flex: 1;
        justify-content: space-between;
    }

    .tefa-item-title {
        font-size: 1.15rem;
        font-weight: 800;
        margin-bottom: 8px;
        color: #ffffff;
        line-height: 1.3;
    }

    .tefa-item-desc {
        font-size: 0.86rem;
        color: #94a3b8;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .tefa-footer-bar {
        border-top: 1px solid rgba(255, 255, 255, 0.07);
        padding-top: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .tefa-status-pill {
        font-size: 0.74rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .tefa-status-pill .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .btn-tefa-cta {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 7px 14px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-tefa-cta.blue {
        background: rgba(37, 99, 235, 0.2);
        color: #60a5fa;
        border: 1px solid rgba(96, 165, 250, 0.35);
    }
    .btn-tefa-cta.blue:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    .btn-tefa-cta.green {
        background: rgba(16, 185, 129, 0.2);
        color: #34d399;
        border: 1px solid rgba(52, 211, 153, 0.35);
    }
    .btn-tefa-cta.green:hover {
        background: #059669;
        color: #ffffff;
        border-color: #059669;
    }

    .btn-tefa-cta.amber {
        background: rgba(245, 158, 11, 0.2);
        color: #fbbf24;
        border: 1px solid rgba(251, 191, 36, 0.35);
    }
    .btn-tefa-cta.amber:hover {
        background: #d97706;
        color: #ffffff;
        border-color: #d97706;
    }

    /* PPDB Fast Banner Enhancements */
    .ppdb-banner-box {
        position: relative;
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: var(--radius-xl);
        padding: clamp(36px, 5vw, 56px);
        color: #ffffff;
        margin-bottom: 70px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.45);
        background-color: #0b1329;
    }

    .ppdb-bg-media {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center right;
        z-index: 1;
        transition: transform 0.6s ease;
    }

    .ppdb-banner-box:hover .ppdb-bg-media {
        transform: scale(1.02);
    }

    .ppdb-scrim-overlay {
        position: absolute;
        inset: 0;
        z-index: 2;
        background: linear-gradient(90deg, 
            rgba(8, 14, 30, 0.96) 0%, 
            rgba(11, 20, 45, 0.92) 45%, 
            rgba(15, 23, 42, 0.75) 72%, 
            rgba(15, 23, 42, 0.48) 100%);
    }

    @media (max-width: 768px) {
        .ppdb-scrim-overlay {
            background: linear-gradient(180deg, 
                rgba(8, 14, 30, 0.90) 0%, 
                rgba(11, 20, 45, 0.95) 100%);
        }
    }

    .ppdb-content-relative {
        position: relative;
        z-index: 3;
        max-width: 800px;
    }

    .ppdb-benefit-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 26px;
    }

    .ppdb-benefit-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.09);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 9999px;
        padding: 5px 14px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #f1f5f9;
    }

    .ppdb-benefit-pill i {
        color: #34d399;
    }

    .btn-ppdb-wa {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(16, 185, 129, 0.18);
        border: 1px solid rgba(52, 211, 153, 0.4);
        color: #34d399;
        font-size: 0.92rem;
        font-weight: 700;
        padding: 13px 22px;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-ppdb-wa:hover {
        background: #059669;
        color: #ffffff;
        border-color: #059669;
        transform: translateY(-2px);
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

</style>
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
    @php
        $ppdbImgUrl = ($ppdbBanner && $ppdbBanner->gambar) 
            ? $ppdbBanner->gambar_url 
            : asset('images/web/ppdb_banner_bg.jpg');
        $ppdbBadge = ($ppdbBanner && $ppdbBanner->badge_text) 
            ? $ppdbBanner->badge_text 
            : ('GELOMBANG 1 TP ' . date('Y') . '/' . (date('Y') + 1) . ' • BEBAS BIAYA PENDAFTARAN (100% GRATIS)');
        $ppdbJudul = ($ppdbBanner && $ppdbBanner->judul) 
            ? $ppdbBanner->judul 
            : 'Daftar Online Mudah dari Rumah, Siap Cetak Generasi Vokasi Berkarakter';
        $ppdbSubjudul = ($ppdbBanner && $ppdbBanner->subjudul) 
            ? $ppdbBanner->subjudul 
            : 'Membuka 3 Jalur: Reguler (Nilai Rapor), Prestasi (Piagam Lomba & Tahfidz), dan Afirmasi (KIP / PKH). Bebas uang gedung (SPI), didukung laboratorium bengkel presisi modern, serta terhubung sertifikasi kerja resmi BNSP.';
        
        $rawPills = ($ppdbBanner && $ppdbBanner->tag_overlay) 
            ? $ppdbBanner->tag_overlay 
            : 'Bebas Biaya Pendaftaran | Tanpa Uang Gedung (SPI) | Lisensi Sertifikasi BNSP | Penyaluran Kerja & Industri';
        $pills = array_filter(array_map('trim', preg_split('/[|,]/', $rawPills)));
        if (empty($pills)) {
            $pills = [
                'Bebas Biaya Pendaftaran',
                'Tanpa Uang Gedung (SPI)',
                'Lisensi Sertifikasi BNSP',
                'Penyaluran Kerja & Industri'
            ];
        }

        $btn1Text = ($ppdbBanner && $ppdbBanner->tombol_teks_1) ? $ppdbBanner->tombol_teks_1 : 'Isi Formulir PPDB Sekarang';
        $btn1Url  = ($ppdbBanner && $ppdbBanner->tombol_url_1) ? $ppdbBanner->tombol_url_1 : route('ppdb.formulir');
        $btn2Text = ($ppdbBanner && $ppdbBanner->tombol_teks_2) ? $ppdbBanner->tombol_teks_2 : 'Cek Status Seleksi';
        $btn2Url  = ($ppdbBanner && $ppdbBanner->tombol_url_2) ? $ppdbBanner->tombol_url_2 : route('ppdb.status');
    @endphp

    <div class="ppdb-banner-box">
        <img src="{{ $ppdbImgUrl }}" alt="{{ $ppdbJudul }}" class="ppdb-bg-media">
        <div class="ppdb-scrim-overlay"></div>

        <div class="ppdb-content-relative">
            <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.14); backdrop-filter: blur(8px); padding: 6px 16px; border-radius: 9999px; font-size: 0.76rem; font-weight: 800; margin-bottom: 18px; border: 1px solid rgba(255,255,255,0.25);">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #34d399; box-shadow: 0 0 0 2px rgba(52,211,153,0.3);"></span>
                <span>{{ $ppdbBadge }}</span>
            </div>
            
            <h2 style="font-size: clamp(1.8rem, 3.4vw, 2.5rem); font-weight: 800; line-height: 1.18; margin-bottom: 16px; text-shadow: 0 2px 8px rgba(0,0,0,0.4);">
                {{ $ppdbJudul }}
            </h2>
            
            <p style="font-size: 0.98rem; color: #cbd5e1; line-height: 1.65; margin-bottom: 22px; text-shadow: 0 1px 4px rgba(0,0,0,0.3);">
                {!! nl2br(e($ppdbSubjudul)) !!}
            </p>

            <div class="ppdb-benefit-pills">
                @foreach($pills as $pill)
                    <span class="ppdb-benefit-pill"><i class="fa-solid fa-check"></i> {{ $pill }}</span>
                @endforeach
            </div>

            <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
                @if($btn1Text && $btn1Url)
                    <a href="{{ $btn1Url }}" class="btn-industrial" style="background: #ffffff; color: #0f172a; font-weight: 800; padding: 13px 28px; border-radius: 12px; box-shadow: 0 10px 25px -4px rgba(0,0,0,0.3);">
                        <i class="fa-solid fa-file-signature"></i> {{ $btn1Text }}
                    </a>
                @endif

                @if($btn2Text && $btn2Url)
                    <a href="{{ $btn2Url }}" class="btn-industrial" style="background: rgba(255, 255, 255, 0.14); border: 1px solid rgba(255,255,255,0.3); color: #ffffff; padding: 13px 22px; border-radius: 12px;">
                        <i class="fa-solid fa-id-badge"></i> {{ $btn2Text }}
                    </a>
                @endif

                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sekolah->telepon ?? '6281234567890') }}?text=Halo%20Panitia%20PPDB%20SMKN%201%20Air%20Naningan,%20saya%20ingin%20bertanya%20seputar%20pendaftaran" target="_blank" rel="noopener" class="btn-ppdb-wa">
                    <i class="fa-brands fa-whatsapp"></i> Tanya Panitia PPDB
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
</script>
@endpush
