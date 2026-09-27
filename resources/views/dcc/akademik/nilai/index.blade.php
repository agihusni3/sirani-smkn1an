@extends('dcc.akademik.layout')

@section('title', 'Daftar Mata Pelajaran & Buku Nilai')
@section('breadcrumb', 'Input Nilai')

@section('content')
<style>
  /* List Container */
  .mapel-accordion-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-bottom: 24px;
  }

  /* List Item Card */
  .mapel-accordion-item {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  }
  .mapel-accordion-item:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  }
  .mapel-accordion-item.is-open {
    border-color: #6366f1;
    box-shadow: 0 6px 20px -4px rgba(99, 102, 241, 0.12);
  }

  /* Clickable Header Row */
  .mapel-accordion-header {
    padding: 16px 20px;
    background: #ffffff;
    cursor: pointer;
    user-select: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    transition: background 0.15s ease;
  }
  .mapel-accordion-item.is-open .mapel-accordion-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
  }
  .mapel-accordion-header:hover {
    background: #f8fafc;
  }

  .mapel-lead-area {
    display: flex;
    align-items: center;
    gap: 14px;
    flex: 1;
    min-width: 0;
  }
  .mapel-avatar-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    box-shadow: 0 2px 8px rgba(79, 70, 229, 0.22);
    flex-shrink: 0;
    transition: transform 0.2s ease;
  }
  .mapel-accordion-item.is-open .mapel-avatar-icon {
    transform: scale(1.05);
  }

  .mapel-info-title {
    font-size: 16.5px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px 0;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .mapel-badges-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }
  .mapel-badge-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 2px 8px;
    border-radius: 6px;
    color: #475569;
    font-weight: 600;
    font-size: 11.5px;
  }
  .mapel-badge-tag.highlight {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #1d4ed8;
    font-weight: 700;
  }
  .mapel-badge-tag.teacher {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #334155;
    font-weight: 700;
  }

  /* Toggle Indicator Action on Right */
  .mapel-toggle-action {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
  }
  .mapel-toggle-btn {
    padding: 6px 14px;
    border-radius: 8px;
    background: #f1f5f9;
    color: #475569;
    font-size: 12.5px;
    font-weight: 700;
    border: 1px solid #cbd5e1;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
  }
  .mapel-accordion-item.is-open .mapel-toggle-btn {
    background: #e0e7ff;
    border-color: #a5b4fc;
    color: #4338ca;
  }
  .chevron-indicator {
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    font-size: 13px;
  }
  .mapel-accordion-item.is-open .chevron-indicator {
    transform: rotate(180deg);
  }

  /* Expandable Content Area */
  .mapel-accordion-body {
    display: none;
    padding: 20px;
    background: #ffffff;
    animation: fadeInSlide 0.2s ease;
  }
  .mapel-accordion-item.is-open .mapel-accordion-body {
    display: block;
  }

  @keyframes fadeInSlide {
    from {
      opacity: 0;
      transform: translateY(-6px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* Class Cards Grid */
  .class-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
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
</style>

{{-- Page Header --}}
<div class="akademik-page-head" style="margin-bottom: 20px;">
  <div>
    <h1 class="akademik-page-title">Buku Nilai Formatif &amp; Sumatif</h1>
    <div class="akademik-page-desc">
      Klik salah satu mata pelajaran di bawah untuk menampilkan daftar kelas dan mengakses buku nilai siswa.
    </div>
  </div>

  <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
    <a href="{{ route('akademik.nilai.leger') }}" class="ak-btn ak-btn-secondary">
      <i class="bi bi-table"></i>
      <span>Lihat Leger Nilai Kelas</span>
    </a>
  </div>
</div>

{{-- Bar Kontrol & Pencarian Cepat --}}
<div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 18px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
  <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
    <span style="font-size:13px; font-weight:700; color:#334155;">
      Total: <span class="ak-badge ak-badge-primary" style="font-size:11.5px;">{{ $groupedByMapel->count() }} Mata Pelajaran</span>
      <span class="ak-badge ak-badge-secondary" style="font-size:11.5px;">{{ $distribusis->count() }} Kelas</span>
    </span>

    <div style="display:flex; gap:6px; margin-left:8px;">
      <button type="button" onclick="bukaSemuaMapel()" class="ak-btn ak-btn-secondary ak-btn-sm" style="padding:4px 10px; font-size:11.5px; font-weight:700;">
        <i class="bi bi-arrows-expand me-1"></i> Buka Semua
      </button>
      <button type="button" onclick="tutupSemuaMapel()" class="ak-btn ak-btn-secondary ak-btn-sm" style="padding:4px 10px; font-size:11.5px; font-weight:700;">
        <i class="bi bi-arrows-collapse me-1"></i> Tutup Semua
      </button>
    </div>
  </div>

  {{-- Search Box --}}
  <div style="min-width:240px; max-width:320px; flex:1;">
    <div style="position:relative;">
      <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:13px;"></i>
      <input type="text" id="inputSearchMapel" class="ak-input" 
             placeholder="Cari mapel atau kelas..." 
             oninput="cariMapelDanKelas()" 
             style="padding-left:34px; font-size:13px; height:38px;">
    </div>
  </div>
</div>

{{-- List Mata Pelajaran (Klik untuk Membuka Datanya) --}}
<div class="mapel-accordion-list" id="mapelListContainer">
  @forelse($groupedByMapel as $mapelId => $items)
    @php
      $mapel = $items->first()->mataPelajaran;
      $namaMapel = $mapel?->nama_mapel ?? 'Mata Pelajaran #' . $mapelId;
      $guruNames = $items->pluck('guru.nama')->filter()->unique()->values();
      $totalSiswaMapel = $items->sum(fn($d) => $d->rombel?->siswas_count ?? 0);
      $isFirst = $loop->first;
    @endphp

    <div class="mapel-accordion-item {{ $isFirst ? 'is-open' : '' }}" id="accordion-mapel-{{ $mapelId }}" data-mapel-name="{{ strtolower($namaMapel) }}">
      {{-- Header Bar Klik --}}
      <div class="mapel-accordion-header" onclick="toggleMapel('accordion-mapel-{{ $mapelId }}')" title="Klik untuk membuka/menutup daftar kelas">
        <div class="mapel-lead-area">
          <div class="mapel-avatar-icon">
            <i class="bi bi-book-half"></i>
          </div>

          <div style="flex:1; min-width:0;">
            <h3 class="mapel-info-title">
              <span>{{ $namaMapel }}</span>
              <span class="ak-badge ak-badge-primary" style="font-size:11px; font-weight:800;">
                {{ $items->count() }} Kelas
              </span>
            </h3>

            <div class="mapel-badges-wrap">
              <span class="mapel-badge-tag highlight">
                <i class="bi bi-tag-fill me-1"></i> Kode: <strong>{{ $mapel?->kode_mapel ?? '-' }}</strong>
              </span>
              <span class="mapel-badge-tag">
                Fase <strong>{{ $mapel?->fase ?? 'E/F' }}</strong>
              </span>
              <span class="mapel-badge-tag">
                <i class="bi bi-people me-1"></i> <strong>{{ $totalSiswaMapel }}</strong> Total Siswa
              </span>

              @if($guruNames->isNotEmpty())
                <span class="mapel-badge-tag teacher">
                  <i class="bi bi-person-badge-fill text-primary me-1"></i>
                  <span>{{ $guruNames->implode(', ') }}</span>
                </span>
              @endif
            </div>
          </div>
        </div>

        <div class="mapel-toggle-action">
          <div class="mapel-toggle-btn">
            <span class="label-status-toggle">{{ $isFirst ? 'Tutup Data' : 'Buka Data' }}</span>
            <i class="bi bi-chevron-down chevron-indicator"></i>
          </div>
        </div>
      </div>

      {{-- Konten Data Kelas yang Muncul Saat Diklik --}}
      <div class="mapel-accordion-body">
        <div style="margin-bottom:14px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
          <div style="font-size:13px; font-weight:700; color:#334155;">
            Daftar Kelas yang Diampu pada <strong>{{ $namaMapel }}</strong>:
          </div>
          <span style="font-size:12px; color:#64748b;">
            Pilih kelas untuk membuka lembar penilaian formatif dan sumatif
          </span>
        </div>

        <div class="class-grid">
          @foreach($items as $d)
            @php
              $jmlSiswa = $d->rombel?->siswas_count ?? 0;
              $namaRombel = $d->rombel?->nama_rombel ?? 'Kelas';
            @endphp
            <div class="class-card item-kelas-card" data-search="{{ strtolower($namaRombel . ' ' . $namaMapel . ' semester ' . $d->semester . ' ' . ($d->guru?->nama ?? '')) }}">
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
        item.classList.add('is-open'); // Otomatis buka jika cocok dengan pencarian
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
