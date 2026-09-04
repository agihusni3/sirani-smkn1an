@extends('web.layouts.app')

@section('title', 'Kabar Kampus & Warta Resmi — SMKN 1 Air Naningan')
@section('meta_description', 'Berita terkini, agenda kegiatan akademik vokasi, dan dokumentasi prestasi siswa SMKN 1 Air Naningan Tanggamus.')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/web-berita.css') }}?v={{ filemtime(public_path('css/web-berita.css')) }}">
@endpush

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 70px;">

    <!-- Section Header -->
    <div class="warta-header-wrap">
        <div class="warta-header-info">
            <span class="warta-section-tag">
                <i class="fa-solid fa-satellite-dish"></i> Warta & Agenda Terkini
            </span>
            <h1 class="warta-title-large">Kabar Kegiatan & Agenda Sekolah</h1>
            <p class="warta-desc-header">
                Dokumentasi prestasi siswa, perkembangan fasilitas bengkel vokasi, inovasi Teaching Factory, dan pengumuman resmi kelembagaan SMKN 1 Air Naningan.
            </p>
        </div>
    </div>

    @php
        $activeKat = request('kategori', '');
        $kategoriList = [
            '' => ['label' => 'Semua Warta', 'icon' => 'fa-layer-group'],
            'berita' => ['label' => 'Berita', 'icon' => 'fa-newspaper'],
            'pengumuman' => ['label' => 'Pengumuman', 'icon' => 'fa-bullhorn'],
            'prestasi' => ['label' => 'Prestasi', 'icon' => 'fa-trophy'],
            'agenda' => ['label' => 'Agenda', 'icon' => 'fa-calendar-days'],
        ];
    @endphp

    <!-- Interactive Morphing Toolbar: Pills + Kinetic Search -->
    <div class="warta-toolbar">
        <div class="warta-pills">
            @foreach($kategoriList as $key => $item)
                <a href="{{ route('web.berita.index', array_filter(['kategori' => $key, 'cari' => request('cari')])) }}"
                   class="warta-pill {{ $activeKat === $key ? 'is-active' : '' }}">
                    <i class="fa-solid {{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('web.berita.index') }}" class="warta-search-form">
            @if(request('kategori'))
                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
            @endif
            <div class="warta-search-box">
                <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari kabar atau agenda..." class="warta-search-input">
                <button type="submit" class="warta-search-btn" title="Cari warta">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Bento Grid Berita with Cinematic Hover & Staggered Transitions -->
    <div class="warta-grid">
        @forelse($beritas as $b)
            <div class="warta-card">
                <div>
                    <!-- Media with Cinematic Image Zoom & Shimmer Light Sweep -->
                    <a href="{{ route('web.berita.show', $b->slug) }}" class="warta-card-media" style="text-decoration: none;">
                        @if($b->url_gambar_sampul)
                            <img src="{{ $b->url_gambar_sampul }}" alt="{{ $b->judul }}" class="warta-card-img" loading="lazy">
                        @else
                            <div class="warta-card-placeholder">
                                <i class="fa-solid fa-newspaper"></i>
                            </div>
                        @endif

                        @php
                            $badgeClass = match($b->kategori) {
                                'pengumuman' => 'warta-badge-pengumuman',
                                'prestasi' => 'warta-badge-prestasi',
                                'agenda' => 'warta-badge-agenda',
                                default => 'warta-badge-berita',
                            };
                            $badgeIcon = match($b->kategori) {
                                'pengumuman' => 'fa-bullhorn',
                                'prestasi' => 'fa-trophy',
                                'agenda' => 'fa-calendar-days',
                                default => 'fa-newspaper',
                            };
                        @endphp
                        <span class="warta-card-badge {{ $badgeClass }}">
                            <i class="fa-solid {{ $badgeIcon }}"></i> {{ ucfirst($b->kategori) }}
                        </span>
                    </a>

                    <!-- Metadata -->
                    <div class="warta-card-meta">
                        <span>
                            <i class="fa-regular fa-calendar" style="margin-right: 4px;"></i>
                            {{ $b->tanggal_publikasi ? \Carbon\Carbon::parse($b->tanggal_publikasi)->translatedFormat('d F Y') : '-' }}
                        </span>
                        <span class="warta-card-views">
                            <i class="fa-regular fa-eye"></i> {{ number_format($b->views ?? 0) }} tayang
                        </span>
                    </div>

                    <!-- Title -->
                    <h2 class="warta-card-title">
                        <a href="{{ route('web.berita.show', $b->slug) }}" style="color: inherit; text-decoration: none;">
                            {{ $b->judul }}
                        </a>
                    </h2>

                    <!-- Narrative Summary -->
                    <p class="warta-card-desc">
                        {{ Str::limit($b->ringkasan ?? strip_tags($b->konten), 115) }}
                    </p>
                </div>

                <!-- Footer with Kinetic Slide Arrow -->
                <div class="warta-card-footer">
                    <a href="{{ route('web.berita.show', $b->slug) }}" class="warta-card-cta">
                        Baca Rinci <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="warta-empty-state">
                <div class="warta-empty-icon">
                    <i class="fa-regular fa-folder-open"></i>
                </div>
                <h3 class="warta-empty-title">Belum Ada Warta dalam Kategori Ini</h3>
                <p class="warta-empty-desc">Silakan pilih kategori warta lain atau gunakan kotak pencarian di atas.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="warta-pagination">
        {{ $beritas->withQueryString()->links() }}
    </div>

</div>
@endsection
