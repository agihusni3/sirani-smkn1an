@extends('web.layouts.app')

@section('title', 'Kabar Kampus & Warta Resmi — SMKN 1 Air Naningan')
@section('meta_description', 'Berita terkini, agenda kegiatan akademik vokasi, dan prestasi kejuaraan siswa SMKN 1 Air Naningan Tanggamus.')

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 60px;">

    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; gap: 20px; margin-bottom: 36px;">
        <div style="max-width: 680px;">
            <span class="section-tag">Warta & Informasi Terkini</span>
            <h1 class="section-title-large">Kabar Kegiatan & Agenda Sekolah</h1>
            <p style="color: var(--text-body); font-size: 1rem; margin-top: 8px;">
                Dokumentasi prestasi siswa, perkembangan fasilitas bengkel, dan pengumuman resmi kelembagaan.
            </p>
        </div>

        <!-- Filter & Search -->
        <form method="GET" action="{{ route('web.berita.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <select name="kategori" onchange="this.form.submit()" style="padding: 10px 14px; font-size: 0.88rem; font-weight: 600; border-radius: var(--radius-sm);">
                <option value="">Semua Kategori</option>
                <option value="berita" {{ request('kategori') == 'berita' ? 'selected' : '' }}>Berita</option>
                <option value="pengumuman" {{ request('kategori') == 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                <option value="prestasi" {{ request('kategori') == 'prestasi' ? 'selected' : '' }}>Prestasi</option>
                <option value="agenda" {{ request('kategori') == 'agenda' ? 'selected' : '' }}>Agenda</option>
            </select>
            <div style="display: flex; background: #ffffff; border: 1px solid var(--border-main); border-radius: var(--radius-sm); overflow: hidden;">
                <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari warta..." style="border: none; padding: 10px 14px; font-size: 0.88rem; outline: none; width: 180px;">
                <button type="submit" style="background: transparent; border: none; padding: 0 14px; color: var(--text-muted); cursor: pointer;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Bento Grid Berita -->
    <div class="bento-grid">
        @forelse($beritas as $b)
            <div class="bento-card" style="grid-column: span 4; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="height: 180px; border-radius: var(--radius-sm); background: var(--bg-surface-alt); margin-bottom: 16px; overflow: hidden; display: flex; align-items: center; justify-content: center; position: relative; border: 1px solid var(--border-main);">
                        @if($b->url_gambar_sampul)
                            <img src="{{ $b->url_gambar_sampul }}" alt="{{ $b->judul }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="font-size: 2.5rem; color: var(--text-muted);">
                                <i class="fa-solid fa-newspaper"></i>
                            </div>
                        @endif
                        <span style="position: absolute; top: 10px; left: 10px; font-family: var(--font-tech); font-size: 0.68rem; font-weight: 700; text-transform: uppercase; background: rgba(15,23,42,0.85); backdrop-filter: blur(8px); padding: 4px 10px; border-radius: 6px; color: #ffffff; letter-spacing: 0.04em;">
                            {{ $b->kategori }}
                        </span>
                    </div>

                    <div style="font-family: var(--font-tech); font-size: 0.75rem; color: var(--text-muted); margin-bottom: 8px;">
                        <i class="fa-regular fa-calendar" style="margin-right: 4px;"></i>
                        {{ $b->tanggal_publikasi ? \Carbon\Carbon::parse($b->tanggal_publikasi)->translatedFormat('d F Y') : '-' }}
                    </div>

                    <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--text-dark); line-height: 1.35; margin-bottom: 10px;">
                        <a href="{{ route('web.berita.show', $b->slug) }}" style="color: inherit; transition: var(--transition);">
                            {{ $b->judul }}
                        </a>
                    </h2>

                    <p style="font-size: 0.86rem; color: var(--text-body); line-height: 1.55; margin-bottom: 18px;">
                        {{ Str::limit($b->ringkasan ?? strip_tags($b->konten), 100) }}
                    </p>
                </div>

                <div style="padding-top: 14px; border-top: 1px solid var(--border-main); display: flex; justify-content: space-between; align-items: center;">
                    <a href="{{ route('web.berita.show', $b->slug) }}" style="font-size: 0.85rem; font-weight: 700; color: var(--brand-blue); display: inline-flex; align-items: center; gap: 6px;">
                        Baca Rinci <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem;"></i>
                    </a>
                    <span style="font-family: var(--font-tech); font-size: 0.72rem; color: var(--text-muted);">
                        <i class="fa-regular fa-eye"></i> {{ $b->views }} tayang
                    </span>
                </div>
            </div>
        @empty
            <div class="bento-card" style="grid-column: span 12; text-align: center; padding: 60px 20px;">
                <div style="font-size: 2.5rem; color: var(--text-muted); margin-bottom: 12px;">
                    <i class="fa-regular fa-folder-open"></i>
                </div>
                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-dark);">Belum ada warta dalam kategori ini</h3>
                <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 6px;">Silakan pilih kategori lain atau gunakan pencarian.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div style="margin-top: 30px; display: flex; justify-content: center;">
        {{ $beritas->withQueryString()->links() }}
    </div>

</div>
@endsection

