<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Official Portal SMKN 1 Air Naningan - Pusat Pendidikan Vokasi Unggulan berbasis Rekayasa Teknologi, Agro-Industri, dan Otomotif di Kabupaten Tanggamus.')">
    <title>@yield('title', 'SMKN 1 Air Naningan')</title>
    
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
                            <a href="{{ route('web.jurusan.index') }}#tefa" class="dropdown-item-card">
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

                    <!-- PPDB Online Direct Link -->
                    <li class="{{ request()->routeIs('ppdb*') ? 'active' : '' }}">
                        <a href="{{ route('ppdb.index') }}">
                            <span>PPDB 2026/2027</span>
                            <span class="badge-live-ppdb">Buka</span>
                        </a>
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
                    <!-- Unified DCC Gateway Launcher (Clean & Harmonized) -->
                    <div class="dcc-launcher nav-dropdown">
                        <button type="button" class="btn-dcc-launcher" aria-label="Buka Layanan Data Control Center">
                            <span>Layanan DCC</span>
                            <i class="fa-solid fa-chevron-down launcher-chevron"></i>
                        </button>
                        <div class="nav-dropdown-menu align-right wide">
                            <div class="dropdown-header-label">Layanan Siswa &amp; Guru</div>

                            <a href="{{ route('portal.ortu.index') }}" class="dcc-menu-item" draggable="false">
                                <div class="dcc-menu-title">Monitoring Presensi</div>
                                <div class="dcc-menu-desc">Pantauan absensi mandiri kartu RFID siswa &amp; wali</div>
                            </a>

                            <a href="{{ route('portal.asesmen.index') }}" class="dcc-menu-item" draggable="false">
                                <div class="dcc-menu-title">Asesmen CBT Siswa</div>
                                <div class="dcc-menu-desc">Ruang ujian online berbasis NISN dan Tanggal Lahir</div>
                            </a>

                            <!-- Bottom Bar: DCC Portal Gateway -->
                            @auth
                                <a href="{{ route('admin.portal') }}" class="dcc-menu-footer-link" draggable="false">
                                    <span>Login DCC</span>
                                    <i class="fa-solid fa-arrow-right" style="font-size: 0.72rem;"></i>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="dcc-menu-footer-link" draggable="false">
                                    <span>Login DCC</span>
                                    <i class="fa-solid fa-arrow-right" style="font-size: 0.72rem;"></i>
                                </a>
                            @endauth
                        </div>
                    </div>

                    <button class="mobile-hamburger" id="navToggleBtn" aria-label="Menu Navigasi Mobile">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- 3. Modern Responsive Mobile Drawer (DCC Integrated Systems) -->
        <div id="navMobileDrawer" class="mobile-drawer">
            <div class="mobile-drawer-inner">
                <!-- Status Badge -->
                <div class="drawer-announcement">
                    <span class="live-status-dot"></span>
                    <span>Layanan Terpadu DCC SMKN 1 Air Naningan</span>
                </div>

                <!-- Group 0: Akses Gerbang Mandiri & Dasbor (ditampilkan paling atas di mobile) -->
                <div class="drawer-gateways" style="padding-top: 0; border-top: none;">
                    <div class="drawer-section-label">Akses Gerbang Mandiri &amp; Dasbor</div>
                    <div class="drawer-gateway-grid">
                        <a href="{{ route('portal.ortu.index') }}" class="drawer-gateway-card" draggable="false">
                            <div class="gateway-icon-box blue">
                                <i class="fa-solid fa-fingerprint"></i>
                            </div>
                            <div>
                                <div class="gateway-card-title">Monitoring Absen Mandiri</div>
                                <div class="gateway-card-sub">Khusus Siswa &amp; Orang Tua</div>
                            </div>
                        </a>

                        <a href="{{ route('portal.asesmen.index') }}" class="drawer-gateway-card" style="border-color:#bfdbfe; background:#eff6ff;" draggable="false">
                            <div class="gateway-icon-box blue" style="background:#2563eb; color:#ffffff;">
                                <i class="fa-solid fa-laptop-code"></i>
                            </div>
                            <div>
                                <div class="gateway-card-title" style="color:#1e3a8a;">Asesmen CBT Siswa</div>
                                <div class="gateway-card-sub">Login NISN &amp; Tanggal Lahir</div>
                            </div>
                        </a>

                        @auth
                            <a href="{{ route('admin.portal') }}" class="drawer-gateway-card" draggable="false">
                                <div class="gateway-icon-box slate">
                                    <i class="fa-solid fa-sliders"></i>
                                </div>
                                <div>
                                    <div class="gateway-card-title">Login DCC</div>
                                    <div class="gateway-card-sub">Pusat Komando &amp; Portal GTK</div>
                                </div>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="drawer-gateway-card" draggable="false">
                                <div class="gateway-icon-box slate">
                                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                </div>
                                <div>
                                    <div class="gateway-card-title">Login DCC</div>
                                    <div class="gateway-card-sub">Pusat Komando &amp; Portal GTK</div>
                                </div>
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Group 1: Navigasi Kampus Utama -->
                <div class="drawer-gateways" style="padding-top: 0; border-top: none;">
                    <div class="drawer-section-label">Navigasi Kampus Utama</div>
                    <div class="drawer-menu-grid">
                        <a href="{{ route('web.beranda') }}" class="drawer-nav-item {{ request()->routeIs('web.beranda') ? 'active' : '' }}">
                            <div class="drawer-nav-icon"><i class="fa-solid fa-compass"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Beranda Utama</span>
                                <span class="drawer-nav-desc">Portal resmi kampus kejuruan</span>
                            </div>
                            <i class="fa-solid fa-angle-right drawer-chevron"></i>
                        </a>

                        <a href="{{ route('web.profil') }}" class="drawer-nav-item {{ request()->routeIs('web.profil') ? 'active' : '' }}">
                            <div class="drawer-nav-icon"><i class="fa-solid fa-building-columns"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Profil &amp; Fasilitas</span>
                                <span class="drawer-nav-desc">Visi misi, sarana &amp; bengkel industri</span>
                            </div>
                            <i class="fa-solid fa-angle-right drawer-chevron"></i>
                        </a>

                        <a href="{{ route('web.jurusan.index') }}" class="drawer-nav-item {{ request()->routeIs('web.jurusan*') ? 'active' : '' }}">
                            <div class="drawer-nav-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Konsentrasi Keahlian</span>
                                <span class="drawer-nav-desc">RPL • APHP • TSM • LSP BNSP</span>
                            </div>
                            <i class="fa-solid fa-angle-right drawer-chevron"></i>
                        </a>

                        <a href="{{ route('web.berita.index') }}" class="drawer-nav-item {{ request()->routeIs('web.berita*') ? 'active' : '' }}">
                            <div class="drawer-nav-icon"><i class="fa-solid fa-bullhorn"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Warta &amp; Agenda</span>
                                <span class="drawer-nav-desc">Informasi kegiatan dan prestasi</span>
                            </div>
                            <i class="fa-solid fa-angle-right drawer-chevron"></i>
                        </a>

                        <a href="{{ route('web.kontak') }}" class="drawer-nav-item {{ request()->routeIs('web.kontak') ? 'active' : '' }}">
                            <div class="drawer-nav-icon"><i class="fa-solid fa-map-location-dot"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Kontak &amp; Peta Kampus</span>
                                <span class="drawer-nav-desc">Alamat, peta &amp; saluran telepon</span>
                            </div>
                            <i class="fa-solid fa-angle-right drawer-chevron"></i>
                        </a>
                    </div>
                </div>

                <!-- Group 2: Modul Operasional DCC SMKN 1 Air Naningan -->
                <div class="drawer-gateways">
                    <div class="drawer-section-label" style="display: flex; align-items: center; justify-content: space-between;">
                        <span>Layanan Terpadu DCC</span>
                        <a href="{{ route('admin.portal') }}" style="color: var(--brand-blue); text-transform: none; font-size: 0.68rem; font-weight: 700;">Masuk Portal DCC &rarr;</a>
                    </div>
                    <div class="drawer-menu-grid">
                        <a href="{{ route('portal.ortu.index') }}" class="drawer-nav-item {{ request()->routeIs('portal.ortu*') ? 'active' : '' }}">
                            <div class="drawer-nav-icon" style="background: #eff6ff; color: #2563eb;"><i class="fa-solid fa-fingerprint"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">SIRANI — Presensi Siswa</span>
                                <span class="drawer-nav-desc">Smart gate RFID &amp; pantau kehadiran mandiri</span>
                            </div>
                            <span class="badge-subtle-status aktif">Aktif</span>
                        </a>

                        <a href="{{ route('portal.asesmen.index') }}" class="drawer-nav-item {{ request()->routeIs('portal.asesmen*') ? 'active' : '' }}">
                            <div class="drawer-nav-icon" style="background: #f0fdf4; color: #059669;"><i class="fa-solid fa-laptop-code"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">AKADEMIK — Asesmen CBT</span>
                                <span class="drawer-nav-desc">Ruang ujian online siswa via NISN</span>
                            </div>
                            <span class="badge-subtle-status live" style="background: #ecfdf5; color: #059669;">Aktif</span>
                        </a>

                        <a href="{{ route('ppdb.index') }}" class="drawer-nav-item {{ request()->routeIs('ppdb*') ? 'active' : '' }}">
                            <div class="drawer-nav-icon ppdb-icon"><i class="fa-solid fa-file-signature"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">PPDB Online 2026/2027</span>
                                <span class="drawer-nav-desc">Pendaftaran mandiri calon murid baru</span>
                            </div>
                            <span class="badge-subtle-status live">Buka</span>
                        </a>

                        <a href="{{ route('ppdb.ujian.portal') }}" class="drawer-nav-item {{ request()->routeIs('ppdb.ujian*') ? 'active' : '' }}">
                            <div class="drawer-nav-icon" style="background: #e0f2fe; color: #0284c7;"><i class="fa-solid fa-clipboard-check"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">PPDB — Ujian Seleksi CBT</span>
                                <span class="drawer-nav-desc">Tes minat &amp; bakat calon siswa baru</span>
                            </div>
                            <span class="badge-subtle-status live" style="background: #e0f2fe; color: #0284c7;">CBT</span>
                        </a>

                        <a href="{{ route('situan.pelayanan.index') }}" class="drawer-nav-item {{ request()->routeIs('situan*') ? 'active' : '' }}">
                            <div class="drawer-nav-icon" style="background: #fef3c7; color: #d97706;"><i class="fa-solid fa-envelope-open-text"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">SITUAN — Pelayanan Surat</span>
                                <span class="drawer-nav-desc">Pengajuan surat keterangan aktif mandiri</span>
                            </div>
                            <span class="badge-subtle-status aktif">Aktif</span>
                        </a>
                    </div>
                </div>

                <!-- Group 4: WhatsApp Helpdesk -->
                <div class="drawer-helpdesk">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sekolah->telepon ?? '6281234567890') }}?text=Halo%20Admin%20SMKN%201%20Air%20Naningan,%20saya%20ingin%20bertanya%20seputar%20sekolah" target="_blank" class="btn-drawer-whatsapp">
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
            <!-- Bottom Copyright (Minimalist & Clean) -->
            <div class="footer-bottom-bar">
                <div>&copy; {{ date('Y') }} SMKN 1 Air Naningan. Seluruh hak cipta dilindungi.</div>
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
