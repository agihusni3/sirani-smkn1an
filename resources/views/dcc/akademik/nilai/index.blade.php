@extends('dcc.akademik.layout')

@section('title', 'Daftar Mata Pelajaran & Buku Nilai')
@section('breadcrumb', 'Input Nilai')

@section('content')
<style>
  /* List Container */
  .mapel-accordion-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 24px;
  }

  /* List Item Card */
  .mapel-accordion-item {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    transition: all 0.15s ease;
  }
  .mapel-accordion-item:hover {
    border-color: #cbd5e1;
    background: #fafafa;
  }
  .mapel-accordion-item.is-open {
    border-color: #94a3b8;
    background: #ffffff;
  }

  /* Clickable Header Row */
  .mapel-accordion-header {
    padding: 14px 18px;
    cursor: pointer;
    user-select: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
  }
  .mapel-accordion-item.is-open .mapel-accordion-header {
    border-bottom: 1px solid #f1f5f9;
    background: #f8fafc;
  }

  .mapel-title-main {
    font-size: 15.5px;
    font-weight: 700;
    color: #000000;
    margin: 0 0 2px 0;
  }
  .mapel-meta-text {
    font-size: 12.5px;
    color: #475569;
    margin: 0;
  }

  /* Toggle Indicator Action on Right */
  .mapel-toggle-action {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 600;
    color: #334155;
    flex-shrink: 0;
  }
  .chevron-indicator {
    transition: transform 0.2s ease;
    font-size: 11px;
    display: inline-block;
  }
  .mapel-accordion-item.is-open .chevron-indicator {
    transform: rotate(180deg);
  }

  /* Expandable Content Area */
  .mapel-accordion-body {
    display: none;
    padding: 16px 18px;
    background: #ffffff;
  }
  .mapel-accordion-item.is-open .mapel-accordion-body {
    display: block;
  }

  /* Class Cards Grid */
  .class-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 12px;
  }
  .class-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: border-color 0.15s ease;
  }
  .class-card:hover {
    border-color: #94a3b8;
  }
  .class-card-head {
    padding: 10px 14px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .class-card-body {
    padding: 12px 14px;
    flex: 1;
    font-size: 12px;
    color: #334155;
  }
  .class-card-foot {
    padding: 10px 14px;
    background: #fafafa;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
</style>

{{-- Page Header --}}
<div class="akademik-page-head" style="margin-bottom: 18px;">
  <div>
    <h1 class="akademik-page-title" style="color:#000000;">Buku Nilai Formatif &amp; Sumatif</h1>
    <div class="akademik-page-desc" style="color:#475569;">
      Pilih mata pelajaran untuk melihat daftar kelas dan menginput nilai siswa.
    </div>
  </div>

  <div style="display:flex; gap:8px; align-items:center;">
    <a href="{{ route('akademik.nilai.leger') }}" class="ak-btn ak-btn-secondary" style="font-size:12.5px; padding:6px 12px;">
      <span>Lihat Leger Nilai Kelas</span>
    </a>
  </div>
</div>

{{-- Bar Kontrol & Pencarian Cepat --}}
<div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:12px 16px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
  <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:#000000; font-weight:600;">
    <span>{{ $groupedByMapel->count() }} Mata Pelajaran · {{ $distribusis->count() }} Kelas</span>

    <div style="display:flex; gap:6px; margin-left:8px;">
      <button type="button" onclick="bukaSemuaMapel()" class="ak-btn ak-btn-secondary ak-btn-sm" style="padding:3px 9px; font-size:11px; font-weight:600;">
        Buka Semua
      </button>
      <button type="button" onclick="tutupSemuaMapel()" class="ak-btn ak-btn-secondary ak-btn-sm" style="padding:3px 9px; font-size:11px; font-weight:600;">
        Tutup Semua
      </button>
    </div>
  </div>

  {{-- Search Box --}}
  <div style="min-width:220px; max-width:280px; flex:1;">
    <input type="text" id="inputSearchMapel" class="ak-input" 
           placeholder="Cari mapel atau kelas..." 
           oninput="cariMapelDanKelas()" 
           style="font-size:12.5px; height:34px; padding:4px 10px;">
  </div>
</div>

{{-- List Mata Pelajaran (Minimalis & Tanpa Icon) --}}
<div class="mapel-accordion-list" id="mapelListContainer">
  @forelse($groupedByMapel as $mapelId => $items)
    @php
      $mapel = $items->first()->mataPelajaran;
      $namaMapel = $mapel?->nama_mapel ?? 'Mata Pelajaran #' . $mapelId;
      $guruNames = $items->map(fn($d) => $d->guru?->nama_lengkap_gelar ?? $d->guru?->nama)->filter()->unique()->values();
      $isFirst = $loop->first;
    @endphp

    <div class="mapel-accordion-item {{ $isFirst ? 'is-open' : '' }}" id="accordion-mapel-{{ $mapelId }}" data-mapel-name="{{ strtolower($namaMapel) }}">
      {{-- Header Bar Klik Minimalis --}}
      <div class="mapel-accordion-header" onclick="toggleMapel('accordion-mapel-{{ $mapelId }}')">
        <div>
          <div class="mapel-title-main">{{ $namaMapel }}</div>
          <div class="mapel-meta-text">
            Fase {{ $mapel?->fase ?? 'E' }} · {{ $items->count() }} Kelas @if($guruNames->isNotEmpty()) · {{ $guruNames->implode(', ') }} @endif
          </div>
        </div>

        <div class="mapel-toggle-action">
          <span class="label-status-toggle">{{ $isFirst ? 'Tutup Data' : 'Buka Data' }}</span>
          <span class="chevron-indicator">▼</span>
        </div>
      </div>

      {{-- Konten Data Kelas yang Muncul Saat Diklik --}}
      <div class="mapel-accordion-body">
        <div class="class-grid">
          @foreach($items as $d)
            @php
              $jmlSiswa = $d->rombel?->siswas_count ?? 0;
              $namaRombel = $d->rombel?->nama_rombel ?? 'Kelas';
              $guruDisplay = $d->guru?->nama_lengkap_gelar ?? $d->guru?->nama;
            @endphp
            <div class="class-card item-kelas-card" data-search="{{ strtolower($namaRombel . ' ' . $namaMapel . ' semester ' . $d->semester . ' ' . ($guruDisplay ?? '')) }}">
              <div class="class-card-head">
                <span style="font-size:13px; font-weight:800; color:#000000;">
                  {{ $namaRombel }}
                </span>
                <span style="font-size:11.5px; font-weight:600; color:#475569;">
                  Semester {{ $d->semester }}
                </span>
              </div>

              <div class="class-card-body">
                <div>{{ $d->total_jam_per_minggu ?? 4 }} JP/Minggu · {{ $jmlSiswa }} Siswa</div>
                @if($guruNames->count() > 1 && $guruDisplay)
                  <div style="color:#64748b; margin-top:4px;">{{ $guruDisplay }}</div>
                @endif
              </div>

              <div class="class-card-foot">
                <span style="font-size:11px; color:#64748b;">
                  Tahun {{ $ta?->tahun ?? date('Y') }}
                </span>
                <a href="{{ route('akademik.nilai.input', $d->id) }}" class="ak-btn ak-btn-primary ak-btn-sm" style="font-size:11.5px; padding:4px 10px; font-weight:700;">
                  <span>Buka Buku Nilai</span>
                </a>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  @empty
    <div style="padding:40px 20px; text-align:center; background:#ffffff; border-radius:10px; border:1px solid #e2e8f0; color:#64748b; font-size:13px;">
      Belum ada jadwal mengajar yang ditugaskan kepada Anda pada tahun ajaran aktif.
    </div>
  @endforelse

  {{-- State Saat Hasil Pencarian Kosong --}}
  <div id="emptySearchState" style="display:none; padding:30px 20px; text-align:center; background:#ffffff; border-radius:10px; border:1px dashed #cbd5e1; color:#64748b; font-size:13px;">
    Tidak ada mata pelajaran atau kelas yang cocok dengan kata kunci pencarian.
  </div>
</div>

<script>
  function toggleMapel(itemId) {
    const item = document.getElementById(itemId);
    if (!item) return;

    const isOpen = item.classList.contains('is-open');
    if (isOpen) {
      item.classList.remove('is-open');
      const label = item.querySelector('.label-status-toggle');
      if (label) label.innerText = 'Buka Data';
    } else {
      item.classList.add('is-open');
      const label = item.querySelector('.label-status-toggle');
      if (label) label.innerText = 'Tutup Data';
    }
  }

  function bukaSemuaMapel() {
    const items = document.querySelectorAll('.mapel-accordion-item');
    items.forEach(item => {
      item.classList.add('is-open');
      const label = item.querySelector('.label-status-toggle');
      if (label) label.innerText = 'Tutup Data';
    });
  }

  function tutupSemuaMapel() {
    const items = document.querySelectorAll('.mapel-accordion-item');
    items.forEach(item => {
      item.classList.remove('is-open');
      const label = item.querySelector('.label-status-toggle');
      if (label) label.innerText = 'Buka Data';
    });
  }

  function cariMapelDanKelas() {
    const keyword = (document.getElementById('inputSearchMapel').value || '').trim().toLowerCase();
    const items = document.querySelectorAll('.mapel-accordion-item');
    let totalVisibleItems = 0;

    items.forEach(item => {
      const mapelName = item.getAttribute('data-mapel-name') || '';
      const classCards = item.querySelectorAll('.item-kelas-card');
      let hasMatchingClass = false;

      classCards.forEach(card => {
        const searchData = card.getAttribute('data-search') || '';
        if (!keyword || searchData.includes(keyword)) {
          card.style.display = 'flex';
          hasMatchingClass = true;
        } else {
          card.style.display = 'none';
        }
      });

      if (!keyword) {
        item.style.display = 'block';
        totalVisibleItems++;
      } else if (mapelName.includes(keyword) || hasMatchingClass) {
        item.style.display = 'block';
        item.classList.add('is-open');
        const label = item.querySelector('.label-status-toggle');
        if (label) label.innerText = 'Tutup Data';
        totalVisibleItems++;
      } else {
        item.style.display = 'none';
      }
    });

    const emptyBox = document.getElementById('emptySearchState');
    if (emptyBox) {
      if (totalVisibleItems === 0 && items.length > 0) {
        emptyBox.style.display = 'block';
      } else {
        emptyBox.style.display = 'none';
      }
    }
  }
</script>
@endsection
