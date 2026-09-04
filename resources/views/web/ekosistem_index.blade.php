@extends('web.layouts.app')

@section('title', 'Peta Arsitektur Ekosistem Digital — SMKN 1 Air Naningan')
@section('meta_description', 'Peta arsitektur terpadu sistem informasi vokasi SMKN 1 Air Naningan berbasis One Data, One Card, One Ecosystem.')

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 70px;">

    <!-- Breadcrumb -->
    <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--text-muted);">
        <a href="{{ route('web.beranda') }}" style="color: var(--brand-blue); font-weight: 600;">Beranda</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
        <span style="color: var(--text-dark); font-weight: 700;">Ekosistem Digital (Master Plan)</span>
    </div>

    <!-- Header Section -->
    <div style="max-width: 820px; margin-bottom: 40px;">
        <span class="section-tag">Master Plan Teknologi Terpadu</span>
        <h1 class="section-title-large">Ekosistem Informasi SMKN 1 Air Naningan</h1>
        <p style="color: var(--text-body); font-size: 1.05rem; margin-top: 14px; line-height: 1.65;">
            Filosofi <strong>"One Data, One Card, One Ecosystem"</strong> — Menyatukan seluruh data pendaftaran siswa, presensi gerbang RFID, monitoring PKL industri, hingga penelusuran karir alumni dalam satu server mandiri tanpa ketergantungan API pihak ketiga.
        </p>
    </div>

    <!-- Master Architecture Banner -->
    <div class="bento-card" style="background: linear-gradient(135deg, #090d16 0%, #0f172a 100%); color: #ffffff; border-color: rgba(255,255,255,0.1); margin-bottom: 36px; padding: clamp(28px, 4vw, 42px);">
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 24px; margin-bottom: 24px;">
            <div>
                <span style="font-family: var(--font-tech); font-size: 0.74rem; font-weight: 700; color: #60a5fa; text-transform: uppercase; letter-spacing: 0.08em; display: block; margin-bottom: 6px;">
                    <i class="fa-solid fa-server" style="margin-right: 6px;"></i> Arsitektur Server Mandiri Ubuntu
                </span>
                <h2 style="font-size: clamp(1.4rem, 2.5vw, 1.8rem); font-weight: 800; color: #ffffff;">
                    Satu Repositori Terpadu, Satu Sumber Kebenaran Data
                </h2>
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <span style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 20px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; font-family: var(--font-tech); font-size: 0.78rem; font-weight: 700;">
                    <span class="live-status-dot"></span> Server Online Aktif
                </span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
            <div style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: var(--radius-md); padding: 18px;">
                <div style="font-family: var(--font-tech); font-size: 0.72rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">Kecepatan & Latensi</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #ffffff; margin-top: 4px;">Zero API Latency</div>
                <p style="font-size: 0.78rem; color: #94a3b8; margin-top: 6px; line-height: 1.5;">Data mengalir langsung antar modul (PPDB &rarr; SIRANI &rarr; Web Publik) seketika tanpa jeda sinkronisasi.</p>
            </div>

            <div style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: var(--radius-md); padding: 18px;">
                <div style="font-family: var(--font-tech); font-size: 0.72rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">Kebijakan Akses Kartu</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #ffffff; margin-top: 4px;">One Card Policy</div>
                <p style="font-size: 0.78rem; color: #94a3b8; margin-top: 6px; line-height: 1.5;">Satu kartu pelajar RFID yang sama untuk presensi gerbang, perpustakaan, dan peminjaman alat bengkel.</p>
            </div>

            <div style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: var(--radius-md); padding: 18px;">
                <div style="font-family: var(--font-tech); font-size: 0.72rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">Pemeliharaan Sistem</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #ffffff; margin-top: 4px;">1-Command Deploy</div>
                <p style="font-size: 0.78rem; color: #94a3b8; margin-top: 6px; line-height: 1.5;">Pembaruan seluruh modul ke server Ubuntu sekolah dilakukan dengan satu eksekusi skrip otomatis.</p>
            </div>
        </div>
    </div>

    <!-- Modules Grid Section -->
    <div style="margin-bottom: 24px;">
        <h2 style="font-size: 1.4rem; font-weight: 800; color: var(--text-dark); margin-bottom: 6px;">
            Pilar Modul Sistem Informasi Sekolah
        </h2>
        <p style="color: var(--text-muted); font-size: 0.9rem;">
            Klik setiap modul untuk melihat spesifikasi cetak biru teknis (*technical blueprint*), fungsi pengguna, dan alur integrasi ke SIRANI Core.
        </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 24px;">
        @foreach($modules as $slug => $mod)
            <div class="bento-card" style="display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid {{ $mod['color'] }};">
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                        <span style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; color: {{ $mod['color'] }}; text-transform: uppercase; letter-spacing: 0.05em;">
                            {{ $mod['pilar'] }}
                        </span>

                        @if($mod['status'] === 'aktif')
                            <span style="display: inline-flex; align-items: center; gap: 5px; font-family: var(--font-tech); font-size: 0.7rem; font-weight: 800; background: #ecfdf5; color: #059669; padding: 3px 10px; border-radius: 14px; border: 1px solid rgba(5,150,105,0.2);">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981;"></span> AKTIF
                            </span>
                        @else
                            <span style="display: inline-flex; align-items: center; gap: 5px; font-family: var(--font-tech); font-size: 0.7rem; font-weight: 800; background: #eff6ff; color: #4338ca; padding: 3px 10px; border-radius: 14px; border: 1px solid rgba(67,56,202,0.2);">
                                <i class="fa-regular fa-clock" style="font-size: 0.65rem;"></i> {{ $mod['status_label'] }}
                            </span>
                        @endif
                    </div>

                    <div style="display: flex; align-items: flex-start; gap: 14px; margin-bottom: 14px;">
                        <div style="width: 46px; height: 46px; border-radius: 12px; background: {{ $mod['subtle'] }}; color: {{ $mod['color'] }}; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; border: 1px solid rgba(0,0,0,0.06);">
                            <i class="{{ $mod['icon'] }}"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.12rem; font-weight: 800; color: var(--text-dark); line-height: 1.3;">
                                {{ $mod['judul'] }}
                            </h3>
                            <div style="font-family: var(--font-tech); font-size: 0.74rem; color: var(--text-muted); margin-top: 3px;">
                                Sasaran: {{ $mod['sasaran'] }}
                            </div>
                        </div>
                    </div>

                    <p style="font-size: 0.88rem; color: var(--text-body); line-height: 1.6; margin-bottom: 20px;">
                        {{ $mod['tagline'] }}
                    </p>

                    <div style="background: var(--bg-surface-alt); border-radius: var(--radius-sm); padding: 12px 14px; border: 1px solid var(--border-main); margin-bottom: 20px;">
                        <div style="font-family: var(--font-tech); font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">
                            Konektivitas SIRANI:
                        </div>
                        <div style="font-size: 0.82rem; font-weight: 600; color: var(--text-dark); margin-top: 3px; line-height: 1.4;">
                            {{ $mod['integrasi_sirani'] }}
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; padding-top: 16px; border-top: 1px solid var(--border-main);">
                    <a href="{{ route('web.ekosistem.show', $slug) }}" class="btn-industrial btn-industrial-outline" style="flex: 1; justify-content: center; font-size: 0.8rem; padding: 8px 14px;">
                        <i class="fa-solid fa-compass-drafting"></i> Detail Blueprint
                    </a>

                    @if($mod['action_url'])
                        <a href="{{ $mod['action_url'] }}" class="btn-industrial" style="background: {{ $mod['color'] }}; color: #ffffff; font-size: 0.8rem; padding: 8px 14px;">
                            Buka <i class="fa-solid fa-arrow-right" style="font-size: 0.72rem;"></i>
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
