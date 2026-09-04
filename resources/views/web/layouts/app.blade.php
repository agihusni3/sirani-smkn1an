<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Official Portal SMKN 1 Air Naningan - Pusat Pendidikan Vokasi Unggulan berbasis Rekayasa Teknologi, Agro-Industri, dan Otomotif di Kabupaten Tanggamus.')">
    <title>@yield('title', 'SMKN 1 Air Naningan — Precision Vocational Academy')</title>
    
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <!-- Google Fonts: Plus Jakarta Sans & Space Grotesk -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/web-layout.css') }}?v={{ filemtime(public_path('css/web-layout.css')) }}">
    @stack('styles')
</head>
<body>

    <!-- Sticky Header Navbar -->
    <header class="site-header">
        <div class="container">
            <div class="navbar-inner">
                <!-- Brand Identity -->
                <a href="{{ route('web.beranda') }}" class="brand-logo-area">
                    <img src="{{ asset('logo.png') }}" alt="Logo SMKN 1 Air Naningan" class="brand-logo-img" onerror="this.src='{{ asset('img/logo.png') }}'">
                    <div class="brand-titles">
                        <h1>SMK NEGERI 1 AIR NANINGAN</h1>
                        <p>Vocational Technical Academy • Tanggamus</p>
                    </div>
                </a>

                <!-- Desktop Nav Menu with Master Plan Ecosystem Submenus -->
                <ul class="main-menu">
                    <li class="{{ request()->routeIs('web.beranda') ? 'active' : '' }}">
                        <a href="{{ route('web.beranda') }}">Beranda</a>
                    </li>

                    <!-- Profil Dropdown -->
                    <li class="nav-dropdown {{ request()->routeIs('web.profil*') ? 'active' : '' }}">
                        <a href="{{ route('web.profil') }}" class="nav-dropdown-toggle">
                            Profil &amp; Lembaga <i class="fa-solid fa-chevron-down" style="font-size: 0.62rem; opacity: 0.7;"></i>
                        </a>
                        <div class="nav-dropdown-menu">
                            <div class="dropdown-header-label">Kelembagaan Kampus</div>
                            <a href="{{ route('web.profil') }}#visimisi" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: var(--brand-blue-subtle); color: var(--brand-blue);">
                                    <i class="fa-solid fa-bullseye"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Visi, Misi &amp; Sejarah</div>
                                    <div class="dropdown-item-sub">Landasan nilai &amp; komitmen mutu</div>
                                </div>
                            </a>
                            <a href="{{ route('web.profil') }}#fasilitas" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #fef3c7; color: #b45309;">
                                    <i class="fa-solid fa-building-columns"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Sarana, Prasarana &amp; Lab</div>
                                    <div class="dropdown-item-sub">Bengkel industri &amp; ruang praktik</div>
                                </div>
                            </a>
                            <a href="{{ route('web.profil') }}#gtk" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #ecfdf5; color: #047857;">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Pendidik &amp; Tenaga Kependidikan</div>
                                    <div class="dropdown-item-sub">Dewan guru kejuruan &amp; staf TU</div>
                                </div>
                            </a>
                        </div>
                    </li>

                    <!-- Konsentrasi Keahlian Dropdown -->
                    <li class="nav-dropdown {{ request()->routeIs('web.jurusan*') ? 'active' : '' }}">
                        <a href="{{ route('web.jurusan.index') }}" class="nav-dropdown-toggle">
                            Konsentrasi Keahlian <i class="fa-solid fa-chevron-down" style="font-size: 0.62rem; opacity: 0.7;"></i>
                        </a>
                        <div class="nav-dropdown-menu">
                            <div class="dropdown-header-label">3 Program Vokasi Unggulan</div>
                            <a href="{{ route('web.jurusan.show', 'rpl') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #eff6ff; color: #2563eb;">
                                    <i class="fa-solid fa-code"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Rekayasa Perangkat Lunak (RPL)</div>
                                    <div class="dropdown-item-sub">Coding, web app &amp; software engineer</div>
                                </div>
                            </a>
                            <a href="{{ route('web.jurusan.show', 'aphp') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #ecfdf5; color: #059669;">
                                    <i class="fa-solid fa-seedling"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Agribisnis Pengolahan Hasil Pertanian</div>
                                    <div class="dropdown-item-sub">Olahan pangan modern &amp; agroindustri</div>
                                </div>
                            </a>
                            <a href="{{ route('web.jurusan.show', 'tsm') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #fef3c7; color: #d97706;">
                                    <i class="fa-solid fa-motorcycle"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Teknik Sepeda Motor (TSM)</div>
                                    <div class="dropdown-item-sub">Mekanik otomotif standar pabrikan</div>
                                </div>
                            </a>
                            <a href="{{ route('web.jurusan.index') }}" class="dropdown-item-card" style="border-top: 1px solid var(--border-main);">
                                <div class="dropdown-item-icon" style="background: var(--bg-surface-alt); color: var(--text-dark);">
                                    <i class="fa-solid fa-certificate"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Sertifikasi Profesi LSP-P1 BNSP</div>
                                    <div class="dropdown-item-sub">Standar kompetensi uji kompetensi kejuruan</div>
                                </div>
                            </a>
                            <a href="{{ route('web.ekosistem.show', 'teaching-factory') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #fff7ed; color: #ea580c;">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Teaching Factory (TeFa) Produk</div>
                                    <div class="dropdown-item-sub">Showcase karya inovasi &amp; unit produksi siswa</div>
                                </div>
                            </a>
                        </div>
                    </li>

                    <!-- Ekosistem Digital (Master Plan) Mega Dropdown -->
                    <li class="nav-dropdown {{ request()->routeIs('web.ekosistem*') || request()->routeIs('ppdb*') ? 'active' : '' }}">
                        <a href="{{ route('web.ekosistem.index') }}" class="nav-dropdown-toggle">
                            <span>Ekosistem Digital</span>
                            <span class="badge-subtle-status live" style="font-size: 0.58rem; padding: 1px 5px;">Plan</span>
                            <i class="fa-solid fa-chevron-down" style="font-size: 0.62rem; opacity: 0.7;"></i>
                        </a>
                        <div class="nav-dropdown-menu wide">
                            <div class="dropdown-header-label">Modul Sistem Informasi Terpadu</div>

                            <!-- Modul Aktif: PPDB -->
                            <a href="{{ route('ppdb.index') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #fee2e2; color: #dc2626;">
                                    <i class="fa-solid fa-file-signature"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">
                                        <span>PPDB Online 2026/2027</span>
                                        <span class="badge-subtle-status live">Buka</span>
                                    </div>
                                    <div class="dropdown-item-sub">Pendaftaran mandiri siswa baru online</div>
                                </div>
                            </a>

                            <!-- Modul Aktif: Presensi Mandiri SIRANI -->
                            <a href="{{ route('portal.ortu.index') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: var(--brand-blue-subtle); color: var(--brand-blue);">
                                    <i class="fa-solid fa-id-card-clip"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">
                                        <span>Presensi Digital (SIRANI)</span>
                                        <span class="badge-subtle-status aktif">Aktif</span>
                                    </div>
                                    <div class="dropdown-item-sub">Kartu RFID &amp; gateway kehadiran orang tua</div>
                                </div>
                            </a>

                            <!-- Modul Roadmap: SIM-PKL -->
                            <a href="{{ route('web.ekosistem.show', 'sim-pkl') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #e0f2fe; color: #0284c7;">
                                    <i class="fa-solid fa-business-time"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">
                                        <span>SIM-PKL &amp; Magang Industri</span>
                                        <span class="badge-subtle-status segera">Segera</span>
                                    </div>
                                    <div class="dropdown-item-sub">GPS geotagging &amp; jurnal kegiatan siswa</div>
                                </div>
                            </a>

                            <!-- Modul Roadmap: Smart Toolman -->
                            <a href="{{ route('web.ekosistem.show', 'smart-toolman') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #fef3c7; color: #b45309;">
                                    <i class="fa-solid fa-wrench"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">
                                        <span>Smart Toolman RFID Bengkel</span>
                                        <span class="badge-subtle-status segera">Segera</span>
                                    </div>
                                    <div class="dropdown-item-sub">Peminjaman alat bengkel APHP, TSM, RPL</div>
                                </div>
                            </a>

                            <!-- Modul Roadmap: BKK Tracer -->
                            <a href="{{ route('web.ekosistem.show', 'bkk-tracer') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #ecfdf5; color: #059669;">
                                    <i class="fa-solid fa-user-tie"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">
                                        <span>BKK &amp; Tracer Study Alumni</span>
                                        <span class="badge-subtle-status segera">Segera</span>
                                    </div>
                                    <div class="dropdown-item-sub">Bursa kerja vokasi &amp; pelacakan karir BMW</div>
                                </div>
                            </a>

                            <!-- Modul Roadmap: E-Library -->
                            <a href="{{ route('web.ekosistem.show', 'e-library') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #f5f3ff; color: #7c3aed;">
                                    <i class="fa-solid fa-book-bookmark"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">
                                        <span>Perpustakaan Digital &amp; Modul</span>
                                        <span class="badge-subtle-status segera">Segera</span>
                                    </div>
                                    <div class="dropdown-item-sub">E-book kejuruan &amp; jobsheet instruksi bengkel</div>
                                </div>
                            </a>

                            <!-- Peta Arsitektur Link -->
                            <a href="{{ route('web.ekosistem.index') }}" class="dropdown-item-card" style="border-top: 1px solid var(--border-main); justify-content: center; background: var(--bg-surface-alt) !important; padding: 8px !important;">
                                <span style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; color: var(--brand-blue);">
                                    <i class="fa-solid fa-diagram-project" style="margin-right: 4px;"></i> Lihat Seluruh Peta Arsitektur Ekosistem &rarr;
                                </span>
                            </a>
                        </div>
                    </li>

                    <!-- Warta Dropdown -->
                    <li class="nav-dropdown {{ request()->routeIs('web.berita*') ? 'active' : '' }}">
                        <a href="{{ route('web.berita.index') }}" class="nav-dropdown-toggle">
                            Warta &amp; Prestasi <i class="fa-solid fa-chevron-down" style="font-size: 0.62rem; opacity: 0.7;"></i>
                        </a>
                        <div class="nav-dropdown-menu">
                            <a href="{{ route('web.berita.index') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #eff6ff; color: #2563eb;">
                                    <i class="fa-solid fa-newspaper"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Kabar &amp; Pengumuman Sekolah</div>
                                    <div class="dropdown-item-sub">Agenda kegiatan resmi dinas &amp; sekolah</div>
                                </div>
                            </a>
                            <a href="{{ route('web.berita.index') }}?kategori=Prestasi" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #fef3c7; color: #b45309;">
                                    <i class="fa-solid fa-trophy"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Galeri Prestasi Siswa &amp; Guru</div>
                                    <div class="dropdown-item-sub">Raihan kejuaraan LKS &amp; inovasi vokasi</div>
                                </div>
                            </a>
                        </div>
                    </li>

                    <!-- Kontak -->
                    <li class="{{ request()->routeIs('web.kontak') ? 'active' : '' }}">
                        <a href="{{ route('web.kontak') }}">Kontak</a>
                    </li>
                </ul>

                <!-- Desktop Action Gateways & Mobile Hamburger -->
                <div class="nav-right-actions">
                    <div class="desktop-nav-actions">
                        <a href="{{ route('portal.ortu.index') }}" class="btn-nav-presensi" title="Cek Presensi Mandiri Siswa &amp; Orang Tua">
                            <i class="fa-solid fa-id-card-clip" style="color: var(--brand-blue);"></i>
                            <span>Cek Presensi</span>
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn-nav-sirani" title="Masuk Sistem SIRANI GTK &amp; Guru">
                            <i class="fa-solid fa-shield-halved" style="color: #60a5fa;"></i>
                            <span>SIRANI Hub</span>
                        </a>
                    </div>

                    <button class="mobile-hamburger" id="navToggleBtn" aria-label="Menu Navigasi Mobile">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- 3. Modern Responsive Mobile Drawer (Master Plan Structured) -->
        <div id="navMobileDrawer" class="mobile-drawer">
            <div class="mobile-drawer-inner">
                <!-- Status Badge -->
                <div class="drawer-announcement">
                    <span class="live-status-dot"></span>
                    <span>Master Plan Ekosistem Digital SMKN 1 Air Naningan</span>
                </div>

                <!-- Group 1: Navigasi Kampus Utama -->
                <div class="drawer-gateways" style="padding-top: 0; border-top: none;">
                    <div class="drawer-section-label">Navigasi Kampus Utama</div>
                    <div class="drawer-menu-grid">
                        <a href="{{ route('web.beranda') }}" class="drawer-nav-item {{ request()->routeIs('web.beranda') ? 'active' : '' }}">
                            <div class="drawer-nav-icon"><i class="fa-solid fa-house"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Beranda Utama</span>
                                <span class="drawer-nav-desc">Portal resmi kampus kejuruan</span>
                            </div>
                            <i class="fa-solid fa-chevron-right drawer-chevron"></i>
                        </a>

                        <a href="{{ route('web.profil') }}" class="drawer-nav-item {{ request()->routeIs('web.profil') ? 'active' : '' }}">
                            <div class="drawer-nav-icon"><i class="fa-solid fa-school"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Profil &amp; Fasilitas</span>
                                <span class="drawer-nav-desc">Visi misi, sarana &amp; bengkel industri</span>
                            </div>
                            <i class="fa-solid fa-chevron-right drawer-chevron"></i>
                        </a>

                        <a href="{{ route('web.jurusan.index') }}" class="drawer-nav-item {{ request()->routeIs('web.jurusan*') ? 'active' : '' }}">
                            <div class="drawer-nav-icon"><i class="fa-solid fa-microchip"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Konsentrasi Keahlian</span>
                                <span class="drawer-nav-desc">RPL • APHP • TSM • LSP BNSP</span>
                            </div>
                            <i class="fa-solid fa-chevron-right drawer-chevron"></i>
                        </a>

                        <a href="{{ route('web.berita.index') }}" class="drawer-nav-item {{ request()->routeIs('web.berita*') ? 'active' : '' }}">
                            <div class="drawer-nav-icon"><i class="fa-solid fa-newspaper"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Warta &amp; Agenda</span>
                                <span class="drawer-nav-desc">Informasi kegiatan dan prestasi</span>
                            </div>
                            <i class="fa-solid fa-chevron-right drawer-chevron"></i>
                        </a>

                        <a href="{{ route('web.kontak') }}" class="drawer-nav-item {{ request()->routeIs('web.kontak') ? 'active' : '' }}">
                            <div class="drawer-nav-icon"><i class="fa-solid fa-location-dot"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Kontak &amp; Peta Kampus</span>
                                <span class="drawer-nav-desc">Alamat, peta &amp; saluran telepon</span>
                            </div>
                            <i class="fa-solid fa-chevron-right drawer-chevron"></i>
                        </a>
                    </div>
                </div>

                <!-- Group 2: Modul Ekosistem Digital Master Plan -->
                <div class="drawer-gateways">
                    <div class="drawer-section-label" style="display: flex; align-items: center; justify-content: space-between;">
                        <span>Ekosistem Layanan (Master Plan)</span>
                        <a href="{{ route('web.ekosistem.index') }}" style="color: var(--brand-blue); text-transform: none; font-size: 0.68rem; font-weight: 700;">Lihat Peta Blueprint &rarr;</a>
                    </div>
                    <div class="drawer-menu-grid">
                        <a href="{{ route('ppdb.index') }}" class="drawer-nav-item {{ request()->routeIs('ppdb*') ? 'active' : '' }}">
                            <div class="drawer-nav-icon ppdb-icon"><i class="fa-solid fa-file-signature"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">PPDB Online 2026/2027</span>
                                <span class="drawer-nav-desc">Pendaftaran Online Bebas Biaya</span>
                            </div>
                            <span class="badge-subtle-status live">Buka</span>
                        </a>

                        <a href="{{ route('portal.ortu.index') }}" class="drawer-nav-item">
                            <div class="drawer-nav-icon" style="background: var(--brand-blue-subtle); color: var(--brand-blue);"><i class="fa-solid fa-id-card-clip"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Presensi Digital SIRANI</span>
                                <span class="drawer-nav-desc">Kartu RFID &amp; portal orang tua</span>
                            </div>
                            <span class="badge-subtle-status aktif">Aktif</span>
                        </a>

                        <a href="{{ route('web.ekosistem.show', 'sim-pkl') }}" class="drawer-nav-item">
                            <div class="drawer-nav-icon" style="background: #e0f2fe; color: #0284c7;"><i class="fa-solid fa-business-time"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">SIM-PKL Magang Industri</span>
                                <span class="drawer-nav-desc">Presensi GPS &amp; logbook praktik</span>
                            </div>
                            <span class="badge-subtle-status segera">Segera</span>
                        </a>

                        <a href="{{ route('web.ekosistem.show', 'smart-toolman') }}" class="drawer-nav-item">
                            <div class="drawer-nav-icon" style="background: #fef3c7; color: #b45309;"><i class="fa-solid fa-wrench"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Smart Toolman RFID Bengkel</span>
                                <span class="drawer-nav-desc">Peminjaman alat lab &amp; bengkel</span>
                            </div>
                            <span class="badge-subtle-status segera">Segera</span>
                        </a>

                        <a href="{{ route('web.ekosistem.show', 'bkk-tracer') }}" class="drawer-nav-item">
                            <div class="drawer-nav-icon" style="background: #ecfdf5; color: #059669;"><i class="fa-solid fa-user-tie"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">BKK &amp; Tracer Study Alumni</span>
                                <span class="drawer-nav-desc">Bursa kerja &amp; survei lulusan BMW</span>
                            </div>
                            <span class="badge-subtle-status segera">Segera</span>
                        </a>

                        <a href="{{ route('web.ekosistem.show', 'e-library') }}" class="drawer-nav-item">
                            <div class="drawer-nav-icon" style="background: #f5f3ff; color: #7c3aed;"><i class="fa-solid fa-book-bookmark"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">E-Perpustakaan &amp; Modul</span>
                                <span class="drawer-nav-desc">E-book &amp; jobsheet bengkel</span>
                            </div>
                            <span class="badge-subtle-status segera">Segera</span>
                        </a>
                    </div>
                </div>

                <!-- Group 3: Akses Gerbang Mandiri & Dasbor -->
                <div class="drawer-gateways">
                    <div class="drawer-section-label">Akses Gerbang Mandiri &amp; Dasbor</div>
                    <div class="drawer-gateway-grid">
                        <a href="{{ route('portal.ortu.index') }}" class="drawer-gateway-card">
                            <div class="gateway-icon-box blue">
                                <i class="fa-solid fa-id-card-clip"></i>
                            </div>
                            <div>
                                <div class="gateway-card-title">Cek Presensi Mandiri</div>
                                <div class="gateway-card-sub">Khusus Siswa &amp; Orang Tua</div>
                            </div>
                        </a>

                        <a href="{{ route('dashboard') }}" class="drawer-gateway-card">
                            <div class="gateway-icon-box dark">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <div class="gateway-card-title">SIRANI Hub System</div>
                                <div class="gateway-card-sub">Login GTK, Guru &amp; Presensi</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Group 4: WhatsApp Helpdesk -->
                <div class="drawer-helpdesk">
                    <a href="https://wa.me/6281234567890?text=Halo%20Admin%20SMKN%201%20Air%20Naningan,%20saya%20ingin%20bertanya%20seputar%20sekolah" target="_blank" class="btn-drawer-whatsapp">
                        <i class="fa-brands fa-whatsapp" style="font-size: 1.1rem;"></i> Chat Helpdesk WhatsApp Kampus
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <!-- 3. Clean Streamlined Minimalist Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-main-row">
                <!-- Brand & Akreditasi -->
                <div class="footer-brand-box">
                    <img src="{{ asset('logo.png') }}" alt="Logo SMK" class="footer-logo" onerror="this.src='{{ asset('img/logo.png') }}'">
                    <div>
                        <div class="footer-brand-title">SMK NEGERI 1 AIR NANINGAN</div>
                        <div class="footer-meta-line">
                            <span class="footer-badge">
                                <span class="status-dot"></span> Akreditasi B • NPSN: {{ $sekolah->npsn ?? '70011825' }}
                            </span>
                            <span class="footer-loc-text">
                                <i class="fa-solid fa-location-dot"></i> Air Naningan, Tanggamus, Lampung
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Inline Links -->
                <nav class="footer-nav-inline" aria-label="Navigasi Footer">
                    <a href="{{ route('web.beranda') }}">Beranda</a>
                    <a href="{{ route('web.jurusan.index') }}">Kejuruan</a>
                    <a href="{{ route('ppdb.index') }}">PPDB Online</a>
                    <a href="{{ route('web.berita.index') }}">Warta</a>
                    <a href="{{ route('portal.ortu.index') }}">Presensi</a>
                    <a href="{{ route('web.kontak') }}">Kontak</a>
                </nav>

                <!-- Action & Socials -->
                <div class="footer-actions-cluster">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sekolah->telepon ?? '6281234567890') }}?text=Halo%20Admin%20SMKN%201%20Air%20Naningan"
                       target="_blank" rel="noopener" class="footer-wa-pill" title="Konsultasi WhatsApp">
                        <i class="fa-brands fa-whatsapp"></i>
                        <span>Helpdesk</span>
                    </a>
                    <div class="footer-social-icons">
                        <a href="https://facebook.com" target="_blank" rel="noopener" class="footer-social-circle fb" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://instagram.com" target="_blank" rel="noopener" class="footer-social-circle ig" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://youtube.com" target="_blank" rel="noopener" class="footer-social-circle yt" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="footer-bottom-bar">
                <div>&copy; {{ date('Y') }} <strong>SMKN 1 Air Naningan</strong>. Hak Cipta Dilindungi.</div>
                <div class="footer-badge-sys">SIRANI Integrated System</div>
            </div>
        </div>
    </footer>

    <script>
        const navBtn = document.getElementById('navToggleBtn');
        const navDrawer = document.getElementById('navMobileDrawer');
        if (navBtn && navDrawer) {
            navBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = navDrawer.classList.toggle('open');
                navBtn.classList.toggle('is-active', isOpen);
                const icon = navBtn.querySelector('i');
                if (icon) {
                    if (isOpen) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-xmark');
                    } else {
                        icon.classList.remove('fa-xmark');
                        icon.classList.add('fa-bars');
                    }
                }
            });

            // Close when clicking outside
            document.addEventListener('click', (e) => {
                if (navDrawer.classList.contains('open') && !navDrawer.contains(e.target) && !navBtn.contains(e.target)) {
                    navDrawer.classList.remove('open');
                    navBtn.classList.remove('is-active');
                    const icon = navBtn.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-xmark');
                        icon.classList.add('fa-bars');
                    }
                }
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
