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

    <!-- Main Clean Split Grid (Lapang & Tanpa Tumpang Tindih) -->
    <div class="kontak-main-grid">

        <!-- Kolom Kiri: Pusat Informasi & Aksi Cepat -->
        <div class="kontak-info-card">
            
            <!-- Topbar Card -->
            <div>
                <div class="kontak-card-topbar">
                    <span class="kontak-status-pill">
                        <span class="kontak-pulse-dot"></span>
                        <span>Layanan Aktif • Buka Hari Ini</span>
                    </span>
                    <span class="kontak-legal-badge">
                        Akreditasi B • NPSN: {{ $sekolah->npsn ?? '69888999' }}
                    </span>
                </div>

                <div style="margin-top: 18px;">
                    <h2 class="kontak-school-title">{{ $sekolah->nama ?? 'SMK Negeri 1 Air Naningan' }}</h2>
                    <p class="kontak-school-region">Kawasan Pendidikan Terpadu Air Naningan, Kabupaten Tanggamus, Lampung</p>
                </div>
            </div>

            <!-- Details Stack -->
            <div class="kontak-details-stack">
                
                <!-- Alamat -->
                <div class="kontak-detail-row">
                    <span class="kontak-detail-label">Alamat Lengkap Kampus</span>
                    <span class="kontak-detail-value" id="campusAddressText">
                        {{ $sekolah->alamat ?? 'Jl. Makam Baturuguk, Pekon Karang Sari, Kec. Air Naningan, Kab. Tanggamus, Lampung' }}
                    </span>
                    <span class="kontak-detail-subtext">Pusat Administrasi &amp; Gedung Pembelajaran Utama</span>
                </div>

                <!-- Jam Kerja -->
                <div class="kontak-detail-row">
                    <span class="kontak-detail-label">Waktu Operasional &amp; KBM</span>
                    <span class="kontak-detail-value">Senin – Jumat : 07.15 – 15.30 WIB</span>
                    <span class="kontak-detail-subtext">Sabtu &amp; Minggu agenda ekstrakurikuler / libur</span>
                </div>

            </div>

            <!-- Action Buttons Group -->
            <div class="kontak-action-group">
                <!-- WhatsApp Helpdesk -->
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sekolah->telepon ?? '6281234567890') }}?text=Halo%20Admin%20SMKN%201%20Air%20Naningan,%20saya%20ingin%20berkonsultasi" 
                   target="_blank" rel="noopener" class="btn-kontak-wa">
                    Chat WhatsApp Helpdesk
                </a>

                <!-- Phone & Copy Address Subrow -->
                <div class="kontak-action-subrow">
                    <a href="tel:{{ preg_replace('/[^0-9]/', '', $sekolah->telepon ?? '081234567890') }}" class="btn-kontak-outline">
                        Panggilan Kantor: {{ $sekolah->telepon ?? '(0721) 892110' }}
                    </a>
                    <button type="button" class="btn-kontak-outline" onclick="copyAddressToClipboard()">
                        Salin Alamat
                    </button>
                </div>
            </div>

            <!-- Amenities Chips -->
            <div class="kontak-amenities-row">
                <span class="amenity-chip">Akses Jalan Aspal Dua &amp; Empat Roda</span>
                <span class="amenity-chip">Area Parkir Luas &amp; Aman</span>
            </div>

        </div>

        <!-- Kolom Kanan: Peta Navigasi Mandiri & Utuh (Tanpa Tertutup Apapun) -->
        <div class="kontak-map-card">
            
            <div class="kontak-map-topbar">
                <h3 class="kontak-map-title">Peta Navigasi &amp; Penunjuk Arah GPS</h3>
                <a href="https://maps.google.com/maps?q=SMK+Negeri+1+Air+Naningan+Tanggamus" 
                   target="_blank" rel="noopener" class="btn-open-gmaps">
                    Buka di Google Maps &rarr;
                </a>
            </div>

            <div class="kontak-map-viewport">
                <iframe 
                    class="kontak-clean-iframe"
                    frameborder="0" 
                    scrolling="no" 
                    marginheight="0" 
                    marginwidth="0" 
                    src="https://maps.google.com/maps?q=SMK+Negeri+1+Air+Naningan+Tanggamus&t=&z=14&ie=UTF8&iwloc=&output=embed"
                    loading="eager"
                    title="Peta Lokasi SMK Negeri 1 Air Naningan">
                </iframe>
            </div>

        </div>

    </div>

    <!-- Bottom Services Suite (3 Kartu Terfokus Tanpa Ikon) -->
    <div class="kontak-services-grid">
        
        <!-- PPDB Gateway -->
        <div class="kontak-service-card ppdb">
            <div>
                <div class="service-card-meta">Penerimaan Siswa Baru</div>
                <h3 class="service-card-title">Pendaftaran Siswa Baru (PPDB)</h3>
                <p class="service-card-desc">
                    Informasi kuota kejuruan, alur seleksi, serta pendaftaran online tahun pelajaran 2026/2027 bebas biaya pendidikan.
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
                    Kolaborasi kurikulum dunia industri (DUDI), penyaluran magang PKL siswa, serta unit produksi Teaching Factory.
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
