@extends('web.layouts.app')

@section('title', 'Hubungi Kami & Lokasi Kampus — SMKN 1 Air Naningan')
@section('meta_description', 'Layanan komunikasi resmi, alamat kampus, jam operasional, dan helpdesk pendaftaran SMKN 1 Air Naningan Kabupaten Tanggamus.')

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 60px;">

    <!-- Section Title -->
    <div style="max-width: 760px; margin-bottom: 36px;">
        <span class="section-tag">Pusat Informasi & Komunikasi</span>
        <h1 class="section-title-large">Hubungi Kampus SMKN 1 Air Naningan</h1>
        <p style="color: var(--text-body); font-size: 1.05rem; margin-top: 12px; line-height: 1.65;">
            Kami membuka pintu kemitraan industri, layanan informasi kurikulum kejuruan, konsultasi pendaftaran peserta didik baru (PPDB), serta verifikasi administrasi alumni.
        </p>
    </div>

    <div class="contact-layout-grid">
        
        <!-- Left: Industrial Contact Cards -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            <div style="background: var(--bg-surface); border: 1px solid var(--border-main); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-card);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--border-main);">
                    <div>
                        <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--text-dark);">Sekretariat & Tata Usaha</h3>
                        <p style="font-size: 0.8rem; color: var(--text-muted);">Gedung Administrasi Utama Kampus</p>
                    </div>
                    <span style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; background: var(--brand-emerald-subtle); color: var(--brand-emerald); padding: 4px 10px; border-radius: 20px; border: 1px solid rgba(4,120,87,0.2);">
                        <i class="fa-solid fa-circle-dot" style="font-size: 0.6rem; margin-right: 4px;"></i> Jam Kerja Aktif
                    </span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 22px;">
                    <!-- Alamat -->
                    <div style="display: flex; align-items: flex-start; gap: 16px;">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--brand-blue-subtle); display: flex; align-items: center; justify-content: center; color: var(--brand-blue); flex-shrink: 0; font-size: 1.15rem; border: 1px solid rgba(37,99,235,0.2);">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <div style="font-family: var(--font-tech); font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Alamat Kampus</div>
                            <div style="font-size: 0.95rem; font-weight: 600; color: var(--text-dark); line-height: 1.5; margin-top: 2px;">
                                {{ $sekolah->alamat ?? 'Jl. Raya Air Naningan, Pekon Air Naningan, Kec. Air Naningan, Kab. Tanggamus, Lampung 35379' }}
                            </div>
                        </div>
                    </div>

                    <!-- Jam KBM -->
                    <div style="display: flex; align-items: flex-start; gap: 16px;">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--bg-surface-alt); display: flex; align-items: center; justify-content: center; color: var(--text-dark); flex-shrink: 0; font-size: 1.15rem; border: 1px solid var(--border-main);">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <div style="font-family: var(--font-tech); font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Jam Operasional & KBM</div>
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-dark); margin-top: 2px;">
                                Senin – Jumat : 07.15 – 15.30 WIB
                            </div>
                            <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">Sabtu & Minggu : Agenda Ekstrakurikuler / Libur</div>
                        </div>
                    </div>

                    <!-- Telepon / Helpdesk -->
                    <div style="display: flex; align-items: flex-start; gap: 16px;">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--brand-emerald-subtle); display: flex; align-items: center; justify-content: center; color: var(--brand-emerald); flex-shrink: 0; font-size: 1.15rem; border: 1px solid rgba(4,120,87,0.2);">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <div style="font-family: var(--font-tech); font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Telepon / Helpdesk PPDB</div>
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-dark); margin-top: 2px;">
                                {{ $sekolah->telepon ?? '0812-3456-7890' }}
                            </div>
                            <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">Layanan WhatsApp & Panggilan Suara</div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div style="display: flex; align-items: flex-start; gap: 16px;">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--brand-amber-subtle); display: flex; align-items: center; justify-content: center; color: var(--brand-amber); flex-shrink: 0; font-size: 1.15rem; border: 1px solid rgba(180,83,9,0.2);">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <div style="font-family: var(--font-tech); font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Email Korespondensi Resmi</div>
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--brand-blue); margin-top: 2px;">
                                <a href="mailto:{{ $sekolah->email ?? 'info@smkn1airnaningan.sch.id' }}">{{ $sekolah->email ?? 'info@smkn1airnaningan.sch.id' }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--border-main);">
                    <a href="https://wa.me/6281234567890?text=Halo%20Admin%20SMKN%201%20Air%20Naningan,%20saya%20ingin%20bertanya%20seputar%20sekolah" target="_blank" class="btn-industrial" style="width: 100%; justify-content: center; background: #059669; color: #ffffff; padding: 12px 20px;">
                        <i class="fa-brands fa-whatsapp" style="font-size: 1.2rem;"></i> Buka Chat WhatsApp Helpdesk
                    </a>
                </div>
            </div>

            <!-- Kartu Layanan Cepat -->
            <div style="background: var(--bg-surface-alt); border: 1px solid var(--border-main); border-radius: var(--radius-lg); padding: 22px 26px; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-dark);">Pendaftaran Siswa Baru (PPDB)</div>
                    <div style="font-size: 0.82rem; color: var(--text-muted); margin-top: 2px;">Informasi kuota & pendaftaran online 2026/2027</div>
                </div>
                <a href="{{ route('ppdb.index') }}" class="btn-industrial btn-industrial-dark" style="font-size: 0.8rem; padding: 8px 16px;">
                    Portal PPDB <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem;"></i>
                </a>
            </div>

        </div>

        <!-- Right: Map Location & Directions -->
        <div style="background: var(--bg-surface); border: 1px solid var(--border-main); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-card); display: flex; flex-direction: column;">
            
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px;">
                <div>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--text-dark);">Peta & Navigasi Kampus</h3>
                    <p style="font-size: 0.82rem; color: var(--text-muted); margin-top: 2px;">Akses lokasi melalui Google Maps atau GPS</p>
                </div>
                <a href="https://maps.google.com/maps?q=SMK+Negeri+1+Air+Naningan+Tanggamus" target="_blank" class="btn-industrial btn-industrial-outline" style="font-size: 0.78rem; padding: 6px 14px;">
                    <i class="fa-solid fa-diamond-turn-right" style="color: var(--brand-blue);"></i> Rute Google Maps
                </a>
            </div>

            <div style="flex: 1; min-height: 420px; border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border-main); position: relative; background: #e2e8f0;">
                <iframe 
                    width="100%" 
                    height="100%" 
                    frameborder="0" 
                    scrolling="no" 
                    marginheight="0" 
                    marginwidth="0" 
                    src="https://maps.google.com/maps?q=SMK+Negeri+1+Air+Naningan+Tanggamus&t=&z=14&ie=UTF8&iwloc=&output=embed"
                    style="border: 0; min-height: 420px; width: 100%; display: block;"
                    loading="lazy">
                </iframe>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-top: 20px;">
                <div style="padding: 12px 16px; background: var(--bg-surface-alt); border-radius: var(--radius-sm); border: 1px solid var(--border-main);">
                    <div style="font-family: var(--font-tech); font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Akses Transportasi</div>
                    <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-dark); margin-top: 2px;">Dapat dilalui kendaraan roda dua & empat</div>
                </div>
                <div style="padding: 12px 16px; background: var(--bg-surface-alt); border-radius: var(--radius-sm); border: 1px solid var(--border-main);">
                    <div style="font-family: var(--font-tech); font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Area Parkir</div>
                    <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-dark); margin-top: 2px;">Tersedia lapangan parkir tamu & siswa luas</div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

