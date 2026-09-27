@extends('dcc.akademik.layout')

@section('title', 'Buku Nilai & Asesmen Per Mapel')
@section('breadcrumb', 'Input Nilai')

@section('content')
<style>
  .mapel-section-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    overflow: hidden;
    transition: all 0.2s ease;
  }
  .mapel-section-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  }
  .mapel-header-bar {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
  }
  .mapel-title-area {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .mapel-icon-circle {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
    flex-shrink: 0;
  }
  .mapel-title-text {
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px 0;
  }
  .mapel-meta-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    font-size: 12px;
  }
  .mapel-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 2px 8px;
    border-radius: 6px;
    color: #475569;
    font-weight: 600;
    font-size: 11.5px;
  }
  .mapel-chip.highlight {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #1d4ed8;
    font-weight: 700;
  }
  .class-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
    padding: 20px;
  }
  .class-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.2s ease;
    overflow: hidden;
  }
  .class-card:hover {
    border-color: #6366f1;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px -2px rgba(99, 102, 241, 0.12);
  }
  .class-card-head {
    padding: 12px 16px;
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .class-card-body {
    padding: 14px 16px;
    flex: 1;
  }
  .class-card-foot {
    padding: 12px 16px;
    background: #fafafa;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .filter-pill {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .filter-pill:hover, .filter-pill.active {
    background: #4f46e5;
    border-color: #4f46e5;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(79, 70, 229, 0.2);
  }
  .filter-pill.active .pill-count {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
  }
  .pill-count {
    background: #f1f5f9;
    color: #475569;
    padding: 1px 6px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 800;
  }
</style>

{{-- Page Header --}}
<div class="akademik-page-head" style="margin-bottom: 20px;">
  <div>
    <h1 class="akademik-page-title">Buku Nilai Formatif &amp; Sumatif</h1>
    <div class="akademik-page-desc">
      Pilih kelas berdasarkan mata pelajaran yang diampu untuk memasukkan nilai asesmen siswa.
    </div>
  </div>

  <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
    <a href="{{ route('akademik.nilai.leger') }}" class="ak-btn ak-btn-secondary">
      <i class="bi bi-table"></i>
      <span>Lihat Leger Nilai Kelas</span>
    </a>
  </div>
</div>

{{-- Bar Filter & Pencarian Cepat --}}
<div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 18px; margin-bottom:24px; display:flex; flex-direction:column; gap:12px;">
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
    {{-- Filter Pills Permapel --}}
    <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;" id="filterPillsContainer">
      <button type="button" class="filter-pill active" onclick="filterMapel('all', this)">
        <span>Semua Mata Pelajaran</span>
        <span class="pill-count">{{ $distribusis->count() }} Kelas</span>
      </button>

      @foreach($groupedByMapel as $mapelId => $items)
        @php
          $m = $items->first()->mataPelajaran;
          $namaMapel = $m?->nama_mapel ?? 'Mapel #' . $mapelId;
        @endphp
        <button type="button" class="filter-pill" onclick="filterMapel('mapel-{{ $mapelId }}', this)">
          <span>{{ $namaMapel }}</span>
          <span class="pill-count">{{ $items->count() }}</span>
        </button>
      @endforeach
    </div>

    {{-- Pencarian Cepat --}}
    <div style="min-width:240px; max-width:320px; flex:1;">
      <div style="position:relative;">
        <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:13px;"></i>
        <input type="text" id="inputSearchMapel" class="ak-input" 
               placeholder="Cari kelas atau mapel..." 
               oninput="cariKelasDanMapel()" 
               style="padding-left:34px; font-size:13px; height:38px;">
      </div>
    </div>
  </div>
</div>

{{-- Daftar Dikelompokkan Per Mata Pelajaran --}}
<div id="containerMapelSections">
  @forelse($groupedByMapel as $mapelId => $items)
    @php
      $mapel = $items->first()->mataPelajaran;
      $guruNames = $items->pluck('guru.nama')->filter()->unique()->values();
      $totalSiswaMapel = $items->sum(fn($d) => $d->rombel?->siswas_count ?? 0);
    @endphp

    <div class="mapel-section-card" id="mapel-{{ $mapelId }}" data-mapel-name="{{ strtolower($mapel?->nama_mapel ?? '') }}">
      {{-- Header Section Mata Pelajaran --}}
      <div class="mapel-header-bar">
        <div class="mapel-title-area">
          <div class="mapel-icon-circle">
            <i class="bi bi-book-half"></i>
          </div>
          <div>
            <h3 class="mapel-title-text">{{ $mapel?->nama_mapel ?? 'Mata Pelajaran' }}</h3>
            <div class="mapel-meta-row">
              <span class="mapel-chip highlight">
                <i class="bi bi-tag-fill me-1"></i> Kode: <strong>{{ $mapel?->kode_mapel ?? '-' }}</strong>
              </span>
              <span class="mapel-chip">
                Fase <strong>{{ $mapel?->fase ?? 'E/F' }}</strong>
              </span>
              <span class="mapel-chip">
                <i class="bi bi-grid-3x3-gap me-1"></i> <strong>{{ $items->count() }}</strong> Rombel / Kelas
              </span>
              <span class="mapel-chip">
                <i class="bi bi-people me-1"></i> <strong>{{ $totalSiswaMapel }}</strong> Total Siswa
              </span>
            </div>
          </div>
        </div>

        {{-- Guru Pengampu --}}
        @if($guruNames->isNotEmpty())
          <div style="display:flex; align-items:center; gap:6px; font-size:12.5px; color:#334155; background:#ffffff; border:1px solid #e2e8f0; padding:6px 12px; border-radius:8px;">
            <i class="bi bi-person-badge-fill text-primary" style="font-size:15px;"></i>
            <div>
              <span style="font-size:11px; color:#64748b; display:block; line-height:1.2;">Guru Pengampu</span>
              <strong style="color:#0f172a;">{{ $guruNames->implode(', ') }}</strong>
            </div>
          </div>
        @endif
      </div>

      {{-- Grid Kelas / Rombel di Bawah Mapel Ini --}}
      <div class="class-grid">
        @foreach($items as $d)
          @php
            $jmlSiswa = $d->rombel?->siswas_count ?? 0;
            $namaRombel = $d->rombel?->nama_rombel ?? 'Kelas';
          @endphp
          <div class="class-card item-kelas-card" data-search="{{ strtolower($namaRombel . ' ' . ($mapel?->nama_mapel ?? '') . ' semester ' . $d->semester . ' ' . ($d->guru?->nama ?? '')) }}">
            <div>
              <div class="class-card-head">
                <span class="ak-badge ak-badge-secondary" style="font-size:12.5px; font-weight:800; color:#1e293b; background:#f1f5f9; border:1px solid #cbd5e1;">
                  {{ $namaRombel }}
                </span>
                <span class="ak-badge {{ $d->semester == 1 ? 'ak-badge-primary' : 'ak-badge-success' }}" style="font-size:11.5px; font-weight:700;">
                  Semester {{ $d->semester }}
                </span>
              </div>

              <div class="class-card-body">
                <div style="display:flex; align-items:center; gap:8px; font-size:12px; color:#64748b; margin-bottom:8px;">
                  <span style="display:flex; align-items:center; gap:4px;">
                    <i class="bi bi-clock-history text-muted"></i>
                    <strong>{{ $d->total_jam_per_minggu ?? 4 }} JP</strong> / Minggu
                  </span>
                  <span>·</span>
                  <span style="display:flex; align-items:center; gap:4px;">
                    <i class="bi bi-people text-muted"></i>
                    <strong>{{ $jmlSiswa }}</strong> Siswa
                  </span>
                </div>

                @if($guruNames->count() > 1 && $d->guru)
                  <div style="font-size:11.5px; color:#475569; display:flex; align-items:center; gap:5px; margin-top:6px;">
                    <i class="bi bi-person text-primary"></i>
                    <span>{{ $d->guru->nama }}</span>
                  </div>
                @endif
              </div>
            </div>

            <div class="class-card-foot">
              <span style="font-size:11.5px; font-weight:600; color:#64748b;">
                Tahun Ajaran {{ $ta?->tahun ?? date('Y') }}
              </span>
              <a href="{{ route('akademik.nilai.input', $d->id) }}" class="ak-btn ak-btn-primary ak-btn-sm" style="font-weight:700; padding:6px 14px;">
                <i class="bi bi-pencil-square me-1"></i>
                <span>Buka Buku Nilai</span>
              </a>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @empty
    <div style="padding:60px 20px; text-align:center; background:#ffffff; border-radius:14px; border:1px solid #e2e8f0; color:#64748b;">
      <div style="width:60px; height:60px; border-radius:50%; background:#f1f5f9; display:flex; align-items:center; justify-content:center; margin:0 auto 14px; font-size:26px; color:#94a3b8;">
        <i class="bi bi-journal-x"></i>
      </div>
      <h4 style="font-size:16px; font-weight:800; color:#0f172a; margin-bottom:6px;">Belum Ada Distribusi Mengajar</h4>
      <p style="font-size:13px; color:#64748b; max-width:400px; margin:0 auto;">
        Belum ada jadwal atau penugasan mata pelajaran yang terhubung dengan akun Anda pada tahun ajaran aktif.
      </p>
    </div>
  @endforelse

  {{-- State Saat Hasil Pencarian Kosong --}}
  <div id="emptySearchState" style="display:none; padding:50px 20px; text-align:center; background:#ffffff; border-radius:14px; border:1px dashed #cbd5e1; color:#64748b;">
    <i class="bi bi-search" style="font-size:32px; color:#94a3b8; display:block; margin-bottom:10px;"></i>
    <div style="font-size:15px; font-weight:700; color:#0f172a;">Tidak Ditemukan</div>
    <div style="font-size:12.5px; color:#64748b; margin-top:4px;">Tidak ada mata pelajaran atau kelas yang cocok dengan kata kunci pencarian.</div>
  </div>
</div>

<script>
  let activeFilterId = 'all';

  function filterMapel(targetId, btnEl) {
    activeFilterId = targetId;

    // Update active pill button
    const buttons = document.querySelectorAll('#filterPillsContainer .filter-pill');
    buttons.forEach(b => b.classList.remove('active'));
    if (btnEl) btnEl.classList.add('active');

    applyAllFilters();
  }

  function cariKelasDanMapel() {
    applyAllFilters();
  }

  function applyAllFilters() {
    const keyword = (document.getElementById('inputSearchMapel').value || '').trim().toLowerCase();
    const sections = document.querySelectorAll('.mapel-section-card');
    let totalVisibleSections = 0;

    sections.forEach(section => {
      const sectionId = section.id;
      const isFilterMatch = (activeFilterId === 'all' || sectionId === activeFilterId);

      if (!isFilterMatch) {
        section.style.display = 'none';
        return;
      }

      // Check classes inside this section against keyword
      const classCards = section.querySelectorAll('.item-kelas-card');
      let visibleCardsInSection = 0;

      classCards.forEach(card => {
        const searchData = card.getAttribute('data-search') || '';
        if (!keyword || searchData.includes(keyword)) {
          card.style.display = 'flex';
          visibleCardsInSection++;
        } else {
          card.style.display = 'none';
        }
      });

      if (visibleCardsInSection > 0) {
        section.style.display = 'block';
        totalVisibleSections++;
      } else {
        section.style.display = 'none';
      }
    });

    const emptyBox = document.getElementById('emptySearchState');
    if (emptyBox) {
      if (totalVisibleSections === 0 && sections.length > 0) {
        emptyBox.style.display = 'block';
      } else {
        emptyBox.style.display = 'none';
      }
    }
  }
</script>
@endsection
