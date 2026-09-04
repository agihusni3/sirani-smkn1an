@extends('web.layouts.app')

@section('title', 'Hubungi Kami & Lokasi Kampus — SMKN 1 Air Naningan')
@section('meta_description', 'Pusat layanan komunikasi resmi, alamat kampus, jam operasional, dan helpdesk pendaftaran SMKN 1 Air Naningan Kabupaten Tanggamus.')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/web-kontak.css') }}?v={{ filemtime(public_path('css/web-kontak.css')) }}">
@endpush

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 70px;">

    <!-- Section Title & Status -->
    <div class="kontak-hero-wrap">
        <span class="kontak-tag">
            <i class="fa-solid fa-headset"></i> Pusat Informasi &amp; Komunikasi
        </span>
        <h1 class="kontak-title">Hubungi Kampus SMKN 1 Air Naningan</h1>
        <p class="kontak-desc">
            Kami membuka pintu kemitraan industri, layanan informasi kurikulum kejuruan, konsultasi pendaftaran peserta didik baru (PPDB), serta verifikasi administrasi alumni.
        </p>
    </div>

    <!-- Top Row: 3 Modern Quick-Connect Channel Cards -->
    <div class="kontak-channels-grid">
        
        <!-- WhatsApp Helpdesk -->
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sekolah->telepon ?? '6281234567890') }}?text=Halo%20Admin%20SMKN%201%20Air%20Naningan,%20saya%20ingin%20berkonsultasi" 
           target="_blank" rel="noopener" class="channel-card wa">
            <div>
                <div class="channel-card-top">
                    <div class="channel-icon-box wa">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <span class="channel-badge-status" style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">
                        <span class="status-dot-pulse"></span> Respon Cepat
                    </span>
                </div>
                <div class="channel-label">Konsultasi &amp; Helpdesk</div>
                <div class="channel-value">WhatsApp Resmi</div>
                <div class="channel-subtext">Layanan informasi PPDB &amp; administrasi siswa</div>
            </div>
            <div class="channel-action-arrow wa">
                <span>Buka Percakapan Chat</span>
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

        <!-- Telephone / Voice -->
        <a href="tel:{{ preg_replace('/[^0-9]/', '', $sekolah->telepon ?? '081234567890') }}" class="channel-card tel">
            <div>
                <div class="channel-card-top">
                    <div class="channel-icon-box tel">
                        <i class="fa-solid fa-phone-volume"></i>
                    </div>
                    <span class="channel-badge-status" style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;">
                        Telepon Kampus
                    </span>
                </div>
                <div class="channel-label">Panggilan Kantor</div>
                <div class="channel-value">{{ $sekolah->telepon ?? '(0721) 892110' }}</div>
                <div class="channel-subtext">Aktif pada hari &amp; jam operasional kerja</div>
            </div>
            <div class="channel-action-arrow tel">
                <span>Hubungi Langsung</span>
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

        <!-- Email Official -->
        <a href="mailto:{{ $sekolah->email ?? 'smkn1airnaningan@gmail.com' }}" class="channel-card mail">
            <div>
                <div class="channel-card-top">
                    <div class="channel-icon-box mail">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <span class="channel-badge-status" style="background: #fffbeb; color: #d97706; border: 1px solid #fde68a;">
                        Korespondensi
                    </span>
                </div>
                <div class="channel-label">Surat Elektronik</div>
                <div class="channel-value" style="font-size: 0.95rem; word-break: break-all;">
                    {{ $sekolah->email ?? 'smkn1airnaningan@gmail.com' }}
                </div>
                <div class="channel-subtext">Kerjasama kemitraan industri &amp; instansi resmi</div>
            </div>
            <div class="channel-action-arrow mail">
                <span>Kirim Pesan Email</span>
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

    </div>

    <!-- Main Bento Grid: Office Details & Cinematic Map -->
    <div class="kontak-bento-grid">
        
        <!-- Left: Office & Operational Hours -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            <div class="kontak-details-card">
                <div>
                    <div class="kontak-card-header">
                        <div>
                            <div class="kontak-card-title">Sekretariat &amp; Tata Usaha</div>
                            <div class="kontak-card-subtitle">Gedung Pelayanan Administrasi Terpadu</div>
                        </div>
                        <span class="channel-badge-status" style="background: var(--brand-emerald-subtle); color: var(--brand-emerald); border: 1px solid rgba(4,120,87,0.2);">
                            <span class="status-dot-pulse"></span> Buka Pelayanan
                        </span>
                    </div>

                    <div class="kontak-items-list" style="margin-top: 18px;">
                        <!-- Alamat Kampus -->
                        <div class="kontak-item-row">
                            <div class="kontak-item-icon loc">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div>
                                <div class="kontak-item-title">Alamat Lengkap Kampus</div>
                                <div class="kontak-item-val">
                                    {{ $sekolah->alamat ?? 'Jl. Makam Baturuguk, Pekon Karang Sari, Kec. Air Naningan, Kab. Tanggamus, Lampung' }}
                                </div>
                                <div class="kontak-item-sub">Kawasan Pendidikan Terpadu Air Naningan</div>
                            </div>
                        </div>

                        <!-- Jam Operasional -->
                        <div class="kontak-item-row">
                            <div class="kontak-item-icon clock">
                                <i class="fa-solid fa-business-time"></i>
                            </div>
                            <div>
                                <div class="kontak-item-title">Waktu Operasional &amp; KBM</div>
                                <div class="kontak-item-val">
                                    Senin – Jumat : 07.15 – 15.30 WIB
                                </div>
                                <div class="kontak-item-sub">Sabtu &amp; Minggu : Pembinaan Ekstrakurikuler / Libur</div>
                            </div>
                        </div>

                        <!-- NPSN & Akreditasi -->
                        <div class="kontak-item-row">
                            <div class="kontak-item-icon npsn">
                                <i class="fa-solid fa-award"></i>
                            </div>
                            <div>
                                <div class="kontak-item-title">Legalitas Lembaga Vokasi</div>
                                <div class="kontak-item-val">
                                    Akreditasi B • NPSN: {{ $sekolah->npsn ?? '69888999' }}
                                </div>
                                <div class="kontak-item-sub">Sekolah Menengah Kejuruan Negeri Tanggamus</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sekolah->telepon ?? '6281234567890') }}?text=Halo%20Admin%20SMKN%201%20Air%20Naningan" 
                       target="_blank" rel="noopener" class="footer-wa-pill" style="width: 100%; justify-content: center; padding: 12px 20px; font-size: 0.88rem; border-radius: var(--radius-md);">
                        <i class="fa-brands fa-whatsapp" style="font-size: 1.15rem;"></i> Buka Percakapan WhatsApp Helpdesk
                    </a>
                </div>
            </div>

            <!-- PPDB Quick Gateway Callout -->
            <div class="kontak-ppdb-callout">
                <div>
                    <div class="kontak-ppdb-title">
                        <i class="fa-solid fa-graduation-cap" style="color: #38bdf8;"></i>
                        Pendaftaran Siswa Baru (PPDB)
                    </div>
                    <div class="kontak-ppdb-sub">
                        Pendaftaran siswa baru TP 2026/2027 telah dibuka bebas biaya untuk seluruh jurusan.
                    </div>
                </div>
                <a href="{{ route('ppdb.index') }}" class="kontak-ppdb-btn">
                    Portal PPDB <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

        </div>

        <!-- Right: Cinematic Interactive Map -->
        <div class="kontak-map-card">
            <div>
                <div class="kontak-map-header">
                    <div>
                        <div class="kontak-card-title">Peta Navigasi &amp; Penunjuk Arah</div>
                        <div class="kontak-card-subtitle">Petunjuk rute Google Maps GPS interaktif</div>
                    </div>
                    <a href="https://maps.google.com/maps?q=SMK+Negeri+1+Air+Naningan+Tanggamus" 
                       target="_blank" rel="noopener" class="kontak-map-btn">
                        <i class="fa-solid fa-diamond-turn-right" style="color: var(--brand-blue);"></i>
                        <span>Buka di Google Maps</span>
                    </a>
                </div>

                <div class="kontak-map-frame-box">
                    <iframe 
                        class="kontak-map-frame"
                        frameborder="0" 
                        scrolling="no" 
                        marginheight="0" 
                        marginwidth="0" 
                        src="https://maps.google.com/maps?q=SMK+Negeri+1+Air+Naningan+Tanggamus&t=&z=14&ie=UTF8&iwloc=&output=embed"
                        loading="lazy">
                    </iframe>
                </div>
            </div>

            <!-- Facility Chips -->
            <div class="kontak-fac-grid">
                <div class="kontak-fac-pill">
                    <div class="kontak-fac-icon">
                        <i class="fa-solid fa-road"></i>
                    </div>
                    <div>
                        <div class="kontak-fac-title">Akses Jalan Raya</div>
                        <div class="kontak-fac-text">Jalur aspal mulus untuk motor &amp; mobil</div>
                    </div>
                </div>
                <div class="kontak-fac-pill">
                    <div class="kontak-fac-icon" style="color: #059669;">
                        <i class="fa-solid fa-square-parking"></i>
                    </div>
                    <div>
                        <div class="kontak-fac-title">Fasilitas Area Parkir</div>
                        <div class="kontak-fac-text">Halaman parkir tamu, guru &amp; siswa aman</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
