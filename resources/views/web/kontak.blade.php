@extends('web.layouts.app')

@section('title', 'Hubungi Kampus & Navigasi Lokasi — SMKN 1 Air Naningan')
@section('meta_description', 'Pusat layanan informasi resmi, alamat kampus, navigasi Google Maps, dan saluran komunikasi terpadu SMKN 1 Air Naningan Kabupaten Tanggamus.')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/web-kontak.css') }}?v={{ filemtime(public_path('css/web-kontak.css')) }}">
@endpush

@section('content')
<div class="container kontak-page-container">

    <!-- Header Halaman -->
    <div class="kontak-header">
        <div class="kontak-category-badge">
            Pusat Informasi &amp; Navigasi Kampus
        </div>
        <h1 class="kontak-heading">Hubungi Kampus SMKN 1 Air Naningan</h1>
        <p class="kontak-subheading">
            Layanan terpadu kemitraan industri, informasi pendaftaran siswa baru (PPDB), kurikulum kejuruan, serta administrasi alumni.
        </p>
    </div>

    <!-- Panoramic Map Canvas & Floating Glass Island (Apple Maps Style) -->
    <div class="kontak-canvas-stage">
        
        <!-- Interactive Map Iframe -->
        <iframe 
            class="kontak-map-iframe"
            frameborder="0" 
            scrolling="no" 
            marginheight="0" 
            marginwidth="0" 
            src="https://maps.google.com/maps?q=SMK+Negeri+1+Air+Naningan+Tanggamus&t=&z=14&ie=UTF8&iwloc=&output=embed"
            loading="eager"
            title="Peta Lokasi SMK Negeri 1 Air Naningan">
        </iframe>

        <!-- Floating Glass Island Overlay -->
        <div class="floating-glass-island">
            
            <!-- Island Top Bar -->
            <div class="island-top-bar">
                <div class="island-status">
                    <span class="island-pulse-dot"></span>
                    <span>Layanan Aktif • Buka Hari Ini</span>
                </div>
            </div>

            <!-- Island Title -->
            <div class="island-title-block">
                <h2 class="island-school-name">{{ $sekolah->nama ?? 'SMK Negeri 1 Air Naningan' }}</h2>
                <p class="island-school-subtitle">Kawasan Pendidikan Terpadu Air Naningan, Tanggamus</p>
            </div>

            <!-- Information List -->
            <div class="island-info-list">
                
                <!-- Alamat -->
                <div class="island-info-row">
                    <div class="island-info-label">Alamat Kampus</div>
                    <div class="island-info-text" id="campusAddressText">
                        {{ $sekolah->alamat ?? 'Jl. Makam Baturuguk, Pekon Karang Sari, Kec. Air Naningan, Kab. Tanggamus, Lampung' }}
                    </div>
                </div>

                <!-- Jam Kerja -->
                <div class="island-info-row">
                    <div class="island-info-label">Jam Operasional &amp; KBM</div>
                    <div class="island-info-text">Senin – Jumat : 07.15 – 15.30 WIB</div>
                    <div class="island-info-sub">Sabtu &amp; Minggu agenda ekstrakurikuler / libur</div>
                </div>

                <!-- Legalitas -->
                <div class="island-info-row">
                    <div class="island-info-label">Legalitas Lembaga</div>
                    <div class="island-info-text">Akreditasi B • NPSN: {{ $sekolah->npsn ?? '69888999' }}</div>
                </div>

            </div>

            <!-- Action Buttons Grid -->
            <div class="island-actions-grid">
                
                <!-- Google Maps Direction -->
                <a href="https://maps.google.com/maps?q=SMK+Negeri+1+Air+Naningan+Tanggamus" 
                   target="_blank" rel="noopener" class="island-btn-primary">
                    Buka Rute di Google Maps
                </a>

                <!-- WhatsApp Helpdesk -->
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sekolah->telepon ?? '6281234567890') }}?text=Halo%20Admin%20SMKN%201%20Air%20Naningan,%20saya%20ingin%20berkonsultasi" 
                   target="_blank" rel="noopener" class="island-btn-wa">
                    Chat WhatsApp
                </a>

                <!-- Copy Address Button -->
                <button type="button" class="island-btn-outline" onclick="copyAddressToClipboard()">
                    Salin Alamat
                </button>

                <!-- Phone Call -->
                <a href="tel:{{ preg_replace('/[^0-9]/', '', $sekolah->telepon ?? '081234567890') }}" class="island-btn-outline" style="grid-column: span 2;">
                    Panggilan Kantor: {{ $sekolah->telepon ?? '(0721) 892110' }}
                </a>

            </div>

            <!-- Amenities Tags -->
            <div class="island-tags-row">
                <span class="island-tag">Akses Jalan Aspal Roda Dua &amp; Empat</span>
                <span class="island-tag">Area Parkir Luas &amp; Aman</span>
            </div>

        </div>

    </div>

    <!-- Bottom Service Strip (3 Terfokus Tanpa Ikon) -->
    <div class="kontak-services-grid">
        
        <!-- PPDB Gateway -->
        <div class="kontak-service-card ppdb">
            <div>
                <div class="service-card-meta">Penerimaan Siswa Baru</div>
                <h3 class="service-card-title">Pendaftaran Siswa Baru (PPDB)</h3>
                <p class="service-card-desc">
                    Informasi kuota jurusan, alur seleksi, serta pendaftaran online tahun pelajaran 2026/2027 bebas biaya pendidikan.
                </p>
            </div>
            <div>
                <a href="{{ route('ppdb.index') }}" class="service-card-link">
                    Menuju Portal PPDB &rarr;
                </a>
            </div>
        </div>

        <!-- Kerjasama Industri -->
        <div class="kontak-service-card industry">
            <div>
                <div class="service-card-meta">Kemitraan Vokasi</div>
                <h3 class="service-card-title">Kemitraan Industri &amp; Magang</h3>
                <p class="service-card-desc">
                    Kolaborasi kurikulum dunia usaha dunia industri (DUDI), penyaluran magang PKL siswa, serta unit bisnis Teaching Factory.
                </p>
            </div>
            <div>
                <a href="mailto:{{ $sekolah->email ?? 'smkn1airnaningan@gmail.com' }}?subject=Pengajuan%20Kerjasama%20Industri" class="service-card-link">
                    Kirim Surat Kerjasama &rarr;
                </a>
            </div>
        </div>

        <!-- Sekretariat & Tata Usaha -->
        <div class="kontak-service-card admin">
            <div>
                <div class="service-card-meta">Administrasi Sekolah</div>
                <h3 class="service-card-title">Sekretariat &amp; Tata Usaha</h3>
                <p class="service-card-desc">
                    Pelayanan verifikasi berkas kelulusan alumni, penerbitan surat keterangan siswa aktif, serta arsip data kependidikan.
                </p>
            </div>
            <div>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sekolah->telepon ?? '6281234567890') }}?text=Halo%20Tata%20Usaha%20SMKN%201%20Air%20Naningan,%20saya%20memerlukan%20layanan%20administrasi" 
                   target="_blank" rel="noopener" class="service-card-link">
                    Hubungi Staf Administrasi &rarr;
                </a>
            </div>
        </div>

    </div>

</div>

<!-- Interactive Toast Feedback -->
<div id="kontakToast" class="kontak-toast">
    Alamat kampus berhasil disalin ke papan klip.
</div>

<script>
function copyAddressToClipboard() {
    const addressEl = document.getElementById('campusAddressText');
    const textToCopy = addressEl ? addressEl.innerText.trim() : 'SMKN 1 Air Naningan, Tanggamus, Lampung';
    
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(textToCopy).then(() => {
            showToast();
        }).catch(() => {
            fallbackCopy(textToCopy);
        });
    } else {
        fallbackCopy(textToCopy);
    }
}

function fallbackCopy(text) {
    const tempInput = document.createElement('textarea');
    tempInput.value = text;
    tempInput.style.position = 'fixed';
    tempInput.style.left = '-9999px';
    document.body.appendChild(tempInput);
    tempInput.focus();
    tempInput.select();
    try {
        document.execCommand('copy');
        showToast();
    } catch (e) {}
    document.body.removeChild(tempInput);
}

function showToast() {
    const toast = document.getElementById('kontakToast');
    if (!toast) return;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 2800);
}
</script>
@endsection
