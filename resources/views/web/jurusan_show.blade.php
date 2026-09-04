@extends('web.layouts.app')

@php
    $meta = $jurusan->getProfilMetadata();
    $colorMap = [
        'rpl' => ['color' => 'var(--brand-blue)', 'subtle' => 'var(--brand-blue-subtle)', 'spec' => '01 / REKAYASA PERANGKAT LUNAK'],
        'aphp' => ['color' => 'var(--brand-emerald)', 'subtle' => 'var(--brand-emerald-subtle)', 'spec' => '02 / AGRIBISNIS PENGOLAHAN PANGAN'],
        'tsm' => ['color' => 'var(--brand-amber)', 'subtle' => 'var(--brand-amber-subtle)', 'spec' => '03 / TEKNIK SEPEDA MOTOR'],
    ];
    $activeTheme = $colorMap[strtolower($jurusan->kode)] ?? ['color' => 'var(--brand-blue)', 'subtle' => 'var(--brand-blue-subtle)', 'spec' => 'SPEC / VOCATIONAL'];
@endphp

@section('title', $jurusan->nama_jurusan . ' (' . $jurusan->kode . ') — SMKN 1 Air Naningan')
@section('meta_description', $meta['deskripsi'] ?? 'Program keahlian ' . $jurusan->nama_jurusan . ' di SMKN 1 Air Naningan.')

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 60px;">

    <!-- Breadcrumb & Back -->
    <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--text-muted);">
        <a href="{{ route('web.beranda') }}" style="color: var(--brand-blue); font-weight: 600;">Beranda</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
        <a href="{{ route('web.jurusan.index') }}" style="color: var(--brand-blue); font-weight: 600;">Konsentrasi Keahlian</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
        <span style="color: var(--text-dark); font-weight: 700;">{{ $jurusan->kode }}</span>
    </div>

    <!-- Hero Jurusan -->
    <div class="bento-card" style="margin-bottom: 30px; border-left: 6px solid {{ $activeTheme['color'] }};">
        <div class="jurusan-hero-grid">
            <div>
                <div style="display: inline-flex; align-items: center; gap: 8px; padding: 5px 14px; background: {{ $activeTheme['subtle'] }}; border-radius: 20px; font-family: var(--font-tech); font-size: 0.75rem; font-weight: 700; color: {{ $activeTheme['color'] }}; margin-bottom: 14px; border: 1px solid rgba(0,0,0,0.06);">
                    <i class="{{ $meta['icon'] ?? 'fa-solid fa-graduation-cap' }}"></i>
                    {{ $activeTheme['spec'] }}
                </div>
                <h1 style="font-size: clamp(2rem, 3.2vw, 2.6rem); font-weight: 800; color: var(--text-dark); letter-spacing: -0.03em; line-height: 1.15; margin-bottom: 12px;">
                    {{ $jurusan->nama_jurusan }}
                </h1>
                <p style="font-size: 1.05rem; font-weight: 700; color: {{ $activeTheme['color'] }}; margin-bottom: 14px;">
                    {{ $meta['tagline'] ?? 'Mencetak Tenaga Ahli Terampil & Kompeten' }}
                </p>
                <p style="color: var(--text-body); font-size: 0.95rem; line-height: 1.65; margin-bottom: 24px;">
                    {{ $meta['deskripsi'] ?? 'Program pendidikan kejuruan yang dirancang dengan kurikulum berbasis industri.' }}
                </p>

                <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                    <a href="{{ route('ppdb.formulir') }}" class="btn-industrial" style="background: {{ $activeTheme['color'] }}; color: #ffffff; padding: 12px 24px;">
                        <i class="fa-solid fa-file-signature"></i>
                        Daftar PPDB di Jurusan Ini
                    </a>
                    <a href="{{ route('ppdb.index') }}" class="btn-industrial btn-industrial-outline">
                        Syarat & Ketentuan
                    </a>
                </div>
            </div>

            <div style="overflow: hidden; border-radius: var(--radius-md); border: 1px solid var(--border-main); height: 260px; box-shadow: var(--shadow-subtle);">
                <img src="{{ asset('images/web/jurusan_' . strtolower($jurusan->kode) . '.jpg') }}" alt="{{ $jurusan->nama_jurusan }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" onerror="this.style.display='none'">
            </div>
        </div>
    </div>

    <!-- Bento Detail Kompetensi & Karir -->
    <div class="bento-grid">
        
        <!-- Kompetensi Yang Dipelajari (Span 6) -->
        <div class="bento-card" style="grid-column: span 6;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: {{ $activeTheme['subtle'] }}; display: flex; align-items: center; justify-content: center; color: {{ $activeTheme['color'] }}; font-size: 1.1rem; border: 1px solid rgba(0,0,0,0.06);">
                    <i class="fa-solid fa-laptop-code"></i>
                </div>
                <div>
                    <span style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Silabus Praktik</span>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-dark);">Kompetensi Keahlian Teknis</h3>
                </div>
            </div>
            <p style="font-size: 0.88rem; color: var(--text-body); margin-bottom: 18px;">
                Peserta didik dibimbing langsung dengan porsi 70% praktikum di bengkel dan laboratorium resmi:
            </p>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @foreach($meta['kompetensi'] ?? [] as $k)
                    <div style="display: flex; align-items: flex-start; gap: 12px; background: var(--bg-surface-alt); padding: 14px 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-main);">
                        <i class="fa-solid fa-circle-check" style="color: {{ $activeTheme['color'] }}; margin-top: 3px; font-size: 0.9rem;"></i>
                        <div style="font-size: 0.9rem; font-weight: 700; color: var(--text-dark);">{{ $k }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Prospek Karir & Kerja (Span 6) -->
        <div class="bento-card" style="grid-column: span 6;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: var(--bg-surface-alt); display: flex; align-items: center; justify-content: center; color: var(--text-dark); font-size: 1.1rem; border: 1px solid var(--border-main);">
                    <i class="fa-solid fa-briefcase"></i>
                </div>
                <div>
                    <span style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Peluang Industri</span>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-dark);">Peluang Karir & Lulusan</h3>
                </div>
            </div>
            <p style="font-size: 0.88rem; color: var(--text-body); margin-bottom: 18px;">
                Kompetensi teruji standar BNSP membuka jalur karir di industri maupun wirausaha mandiri:
            </p>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @foreach($meta['karir'] ?? [] as $c)
                    <div style="display: flex; align-items: flex-start; gap: 12px; background: var(--bg-surface-alt); padding: 14px 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-main);">
                        <i class="fa-solid fa-arrow-trend-up" style="color: {{ $activeTheme['color'] }}; margin-top: 3px; font-size: 0.9rem;"></i>
                        <div style="font-size: 0.9rem; font-weight: 700; color: var(--text-dark);">{{ $c }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Jurusan Lainnya (Span 12) -->
        <div class="bento-card" style="grid-column: span 12;">
            <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-dark); margin-bottom: 18px;">
                Eksplorasi Konsentrasi Keahlian Lainnya
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                @foreach($semuaJurusan as $other)
                    @if($other->id !== $jurusan->id)
                        <a href="{{ route('web.jurusan.show', strtolower($other->kode)) }}" style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; background: var(--bg-surface-alt); border: 1px solid var(--border-main); border-radius: var(--radius-sm); transition: var(--transition);">
                            <div>
                                <span style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; color: var(--brand-blue); text-transform: uppercase;">{{ $other->kode }}</span>
                                <div style="font-size: 0.95rem; font-weight: 800; color: var(--text-dark); margin-top: 2px;">{{ $other->nama_jurusan }}</div>
                            </div>
                            <i class="fa-solid fa-arrow-right" style="color: var(--text-muted);"></i>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection

