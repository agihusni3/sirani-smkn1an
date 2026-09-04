@extends('web.layouts.app')

@section('title', $berita->judul . ' — SMKN 1 Air Naningan')
@section('meta_description', Str::limit($berita->ringkasan ?? strip_tags($berita->konten), 160))

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 60px;">

    <!-- Breadcrumbs -->
    <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--text-muted);">
        <a href="{{ route('web.beranda') }}" style="color: var(--brand-blue); font-weight: 600;">Beranda</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
        <a href="{{ route('web.berita.index') }}" style="color: var(--brand-blue); font-weight: 600;">Kabar Sekolah</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
        <span style="color: var(--text-dark); font-weight: 700;">{{ Str::limit($berita->judul, 30) }}</span>
    </div>

    <div class="bento-grid">
        
        <!-- Artikel Utama (Span 8) -->
        <article class="bento-card" style="grid-column: span 8; padding: clamp(24px, 4vw, 42px);">
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px; margin-bottom: 16px;">
                <span style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; text-transform: uppercase; background: var(--brand-blue-subtle); color: var(--brand-blue); padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(37,99,235,0.2);">
                    {{ $berita->kategori }}
                </span>
                <span style="font-family: var(--font-tech); font-size: 0.78rem; color: var(--text-muted);">
                    <i class="fa-regular fa-calendar" style="margin-right: 4px;"></i>
                    {{ $berita->tanggal_publikasi ? \Carbon\Carbon::parse($berita->tanggal_publikasi)->translatedFormat('d F Y, H:i') : '' }} WIB
                </span>
                <span style="font-size: 0.78rem; color: var(--text-muted);">
                    <i class="fa-regular fa-user" style="margin-right: 4px;"></i>
                    {{ $berita->author_name ?? 'Humas SMKN 1 Air Naningan' }}
                </span>
            </div>

            <h1 style="font-size: clamp(1.8rem, 3.2vw, 2.4rem); font-weight: 800; color: var(--text-dark); line-height: 1.25; letter-spacing: -0.03em; margin-bottom: 24px;">
                {{ $berita->judul }}
            </h1>

            @if($berita->url_gambar_sampul)
                <div style="border-radius: var(--radius-md); overflow: hidden; margin-bottom: 28px; max-height: 440px; border: 1px solid var(--border-main);">
                    <img src="{{ $berita->url_gambar_sampul }}" alt="{{ $berita->judul }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                </div>
            @endif

            <div style="color: var(--text-dark); font-size: 1.05rem; line-height: 1.85; margin-bottom: 36px;">
                {!! nl2br(e($berita->konten)) !!}
            </div>

            <div style="padding-top: 24px; border-top: 1px solid var(--border-main); display: flex; justify-content: space-between; align-items: center;">
                <a href="{{ route('web.berita.index') }}" class="btn-industrial btn-industrial-outline" style="font-size: 0.85rem;">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Indeks Berita
                </a>
                <span style="font-family: var(--font-tech); font-size: 0.78rem; color: var(--text-muted);">
                    <i class="fa-regular fa-eye"></i> Dilihat {{ $berita->views }} kali
                </span>
            </div>
        </article>

        <!-- Sidebar Info & Terkait (Span 4) -->
        <aside style="grid-column: span 4; display: flex; flex-direction: column; gap: 24px;">
            <!-- Call to Action PPDB -->
            <div class="bento-card" style="background: var(--brand-navy); color: #ffffff; border-color: transparent;">
                <span class="badge-pulse" style="background: rgba(37,99,235,0.25); color: #93c5fd; border-color: rgba(147,197,253,0.3); margin-bottom: 12px; display: inline-block;">
                    PPDB 2026/2027
                </span>
                <h3 style="font-size: 1.2rem; font-weight: 800; color: #ffffff; margin-bottom: 8px;">
                    Siap Menjadi Insan Vokasi Unggul?
                </h3>
                <p style="font-size: 0.88rem; color: #94a3b8; line-height: 1.6; margin-bottom: 20px;">
                    Pendaftaran Peserta Didik Baru SMKN 1 Air Naningan Tahun Pelajaran 2026/2027 telah dibuka secara online bebas biaya.
                </p>
                <a href="{{ route('ppdb.formulir') }}" class="btn-industrial btn-industrial-primary" style="width: 100%; justify-content: center; font-size: 0.9rem;">
                    Daftar Online Sekarang →
                </a>
            </div>

            <!-- Berita Terkait -->
            <div class="bento-card">
                <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-dark); margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-main);">
                    Warta Terkait Lainnya
                </h3>
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    @forelse($beritaTerkait as $terkait)
                        <a href="{{ route('web.berita.show', $terkait->slug) }}" style="display: block; padding: 12px 14px; background: var(--bg-surface-alt); border-radius: var(--radius-sm); border: 1px solid var(--border-main); transition: var(--transition);">
                            <span style="font-family: var(--font-tech); font-size: 0.68rem; color: var(--brand-blue); font-weight: 700; text-transform: uppercase;">{{ $terkait->kategori }}</span>
                            <div style="font-size: 0.88rem; font-weight: 700; color: var(--text-dark); line-height: 1.4; margin-top: 4px;">
                                {{ $terkait->judul }}
                            </div>
                        </a>
                    @empty
                        <p style="font-size: 0.85rem; color: var(--text-muted);">Belum ada warta terkait lainnya.</p>
                    @endforelse
                </div>
            </div>
        </aside>

    </div>

</div>
@endsection

