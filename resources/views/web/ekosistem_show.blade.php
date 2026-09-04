@extends('web.layouts.app')

@section('title', $module['judul'] . ' — Master Plan SMKN 1 Air Naningan')
@section('meta_description', $module['tagline'])

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 70px;">

    <!-- Breadcrumbs -->
    <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--text-muted); flex-wrap: wrap;">
        <a href="{{ route('web.beranda') }}" style="color: var(--brand-blue); font-weight: 600;">Beranda</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
        <a href="{{ route('web.ekosistem.index') }}" style="color: var(--brand-blue); font-weight: 600;">Ekosistem Digital</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
        <span style="color: var(--text-dark); font-weight: 700;">{{ $module['kode'] }}</span>
    </div>

    <!-- Hero Card Blueprint -->
    <div class="bento-card" style="margin-bottom: 36px; border-left: 6px solid {{ $module['color'] }}; padding: clamp(28px, 4vw, 42px);">
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px;">
            <div style="display: inline-flex; align-items: center; gap: 8px; padding: 5px 14px; background: {{ $module['subtle'] }}; border-radius: 20px; font-family: var(--font-tech); font-size: 0.75rem; font-weight: 700; color: {{ $module['color'] }}; border: 1px solid rgba(0,0,0,0.06);">
                <i class="{{ $module['icon'] }}"></i>
                {{ $module['pilar'] }}
            </div>

            @if($module['status'] === 'aktif')
                <span style="display: inline-flex; align-items: center; gap: 6px; font-family: var(--font-tech); font-size: 0.75rem; font-weight: 800; background: #ecfdf5; color: #059669; padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(5,150,105,0.25);">
                    <span class="live-status-dot"></span> {{ $module['status_label'] }}
                </span>
            @else
                <span style="display: inline-flex; align-items: center; gap: 6px; font-family: var(--font-tech); font-size: 0.75rem; font-weight: 800; background: #eff6ff; color: #4338ca; padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(67,56,202,0.25);">
                    <i class="fa-regular fa-clock"></i> {{ $module['status_label'] }}
                </span>
            @endif
        </div>

        <h1 style="font-size: clamp(1.8rem, 3.2vw, 2.5rem); font-weight: 800; color: var(--text-dark); letter-spacing: -0.03em; line-height: 1.2; margin-bottom: 12px;">
            {{ $module['judul'] }}
        </h1>
        <p style="font-size: 1.1rem; font-weight: 600; color: {{ $module['color'] }}; margin-bottom: 18px; line-height: 1.5;">
            {{ $module['tagline'] }}
        </p>
        <p style="color: var(--text-body); font-size: 1rem; line-height: 1.7; max-width: 900px; margin-bottom: 28px;">
            {{ $module['deskripsi'] }}
        </p>

        <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
            @if($module['action_url'])
                <a href="{{ $module['action_url'] }}" class="btn-industrial" style="background: {{ $module['color'] }}; color: #ffffff; padding: 11px 22px;">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    {{ $module['action_label'] }}
                </a>
            @else
                <button class="btn-industrial" style="background: #1e293b; color: #94a3b8; cursor: default; padding: 11px 22px;">
                    <i class="fa-solid fa-lock" style="margin-right: 6px;"></i> Roadmap Masa Depan (Tahap Berkelanjutan)
                </button>
            @endif

            <a href="{{ route('web.ekosistem.index') }}" class="btn-industrial btn-industrial-outline">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Peta Ekosistem
            </a>
        </div>
    </div>

    <!-- Detail Specifications Grid -->
    <div style="display: grid; grid-template-columns: 1.3fr 1fr; gap: 32px; align-items: start;" class="contact-layout-grid">
        
        <!-- Left: Features Breakdown -->
        <div style="background: var(--bg-surface); border: 1px solid var(--border-main); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-card);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--border-main);">
                <div style="width: 38px; height: 38px; border-radius: 10px; background: var(--brand-blue-subtle); color: var(--brand-blue); display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <div>
                    <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--text-dark);">Spesifikasi Fitur Utama</h2>
                    <p style="font-size: 0.78rem; color: var(--text-muted); margin: 0;">Fungsionalitas yang disediakan untuk civitas sekolah</p>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 14px;">
                @foreach($module['fitur_unggulan'] as $index => $fitur)
                    <div style="display: flex; align-items: flex-start; gap: 14px; padding: 12px 14px; border-radius: var(--radius-sm); background: var(--bg-surface-alt); border: 1px solid var(--border-main);">
                        <div style="width: 24px; height: 24px; border-radius: 50%; background: {{ $module['color'] }}; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 0.72rem; font-weight: 800; flex-shrink: 0; margin-top: 2px;">
                            {{ $index + 1 }}
                        </div>
                        <div style="font-size: 0.92rem; color: var(--text-dark); font-weight: 600; line-height: 1.5;">
                            {{ $fitur }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right: Architecture & SIRANI Integration -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            <!-- Integration Card -->
            <div style="background: var(--bg-surface); border: 1px solid var(--border-main); border-radius: var(--radius-lg); padding: 28px; box-shadow: var(--shadow-card);">
                <div style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; color: var(--brand-blue); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 8px;">
                    <i class="fa-solid fa-network-wired" style="margin-right: 4px;"></i> Single Source of Truth
                </div>
                <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-dark); margin-bottom: 12px;">
                    Integrasi ke Mesin SIRANI Core
                </h3>
                <p style="font-size: 0.88rem; color: var(--text-body); line-height: 1.6; margin-bottom: 16px;">
                    {{ $module['integrasi_sirani'] }}
                </p>

                <div style="padding: 12px 16px; background: #0f172a; border-radius: var(--radius-md); color: #94a3b8; font-family: var(--font-tech); font-size: 0.78rem;">
                    <div style="color: #34d399; font-weight: 700; margin-bottom: 4px;">&check; Server Terpusat SMKN 1 Air Naningan</div>
                    <div>Database: MySQL Terpadu | Satu Enkripsi | Auto-Backup</div>
                </div>
            </div>

            <!-- Target User Card -->
            <div style="background: var(--bg-surface); border: 1px solid var(--border-main); border-radius: var(--radius-lg); padding: 28px; box-shadow: var(--shadow-card);">
                <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-dark); margin-bottom: 12px;">
                    Pengguna Sasaran
                </h3>
                <div style="font-size: 0.9rem; font-weight: 700; color: {{ $module['color'] }}; margin-bottom: 8px;">
                    {{ $module['sasaran'] }}
                </div>
                <p style="font-size: 0.82rem; color: var(--text-muted); line-height: 1.5;">
                    Hak akses modul diatur berdasarkan peran otentikasi role-based access control (RBAC) pada sistem SIRANI.
                </p>
            </div>

            <!-- Other Modules Shortcut -->
            <div style="background: var(--bg-surface-alt); border: 1px solid var(--border-main); border-radius: var(--radius-lg); padding: 22px;">
                <div style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 10px;">
                    Jelajahi Modul Lainnya:
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    @foreach($modules as $s => $m)
                        @if($s !== $module['kode'])
                            <a href="{{ route('web.ekosistem.show', $s) }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; background: #ffffff; border: 1px solid var(--border-main); border-radius: 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-dark); text-decoration: none;">
                                <i class="{{ $m['icon'] }}" style="color: {{ $m['color'] }}; font-size: 0.7rem;"></i>
                                {{ Str::limit($m['judul'], 20) }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
