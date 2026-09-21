@extends('dcc.akademik.layout')

@section('title', 'Kalender Pendidikan & RPE Sekolah — Akademik SMKN 1 Air Naningan')
@section('breadcrumb')
  <span>Kalender Pendidikan (Kaldik) &amp; RPE</span>
@endsection

@section('content')
<div class="akademik-page-head" style="margin-bottom:20px;">
  <div>
    <h1 class="akademik-page-title">
      <i class="bi bi-calendar2-week-fill text-primary me-2"></i>
      Kalender Pendidikan (Kaldik) &amp; Penetapan RPE Sekolah
    </h1>
    <div class="akademik-page-desc">
      Master acuan pekan efektif KBM, pekan asesmen, dan hari libur resmi SMKN 1 Air Naningan yang terintegrasi langsung ke Program Tahunan (Prota) dan Program Semester (Promes) seluruh guru.
    </div>
  </div>

  <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
    @if($kalender && $canManage)
      <form action="{{ route('akademik.kalender.toggle-lock', $kalender->id) }}" method="POST" style="margin:0;">
        @csrf
        @if($kalender->is_locked)
          <button type="submit" class="ak-btn ak-btn-secondary" style="font-size:12.5px;" title="Buka kunci agar dapat disesuaikan">
            <i class="bi bi-unlock me-1"></i> Buka Kunci Kaldik
          </button>
        @else
          <button type="submit" class="ak-btn ak-btn-success" style="font-size:12.5px;" title="Kunci kalender sebagai acuan resmi satu sekolah">
            <i class="bi bi-lock-fill me-1"></i> Kunci Acuan Resmi Sekolah
          </button>
        @endif
      </form>
    @endif

    @if($canManage)
      <form action="{{ route('akademik.kalender.generate') }}" method="POST" style="margin:0;" onsubmit="return confirm('Generate template standar SMK? Jika data kalender semester ini sudah ada, butir pekan akan diatur ulang sesuai template standar.')">
        @csrf
        <input type="hidden" name="tahun_ajaran_id" value="{{ $selectedTa?->id }}">
        <input type="hidden" name="semester" value="{{ $semester }}">
        <button type="submit" class="ak-btn ak-btn-primary" style="font-size:12.5px;">
          <i class="bi bi-magic me-1"></i> {{ $kalender ? 'Reset Template Standar SMK' : 'Buat Template Standar SMK' }}
        </button>
      </form>
    @endif
  </div>
</div>

{{-- Filter Tahun Ajaran & Semester --}}
<div class="akademik-card" style="margin-bottom:20px; padding:16px 20px;">
  <form method="GET" action="{{ route('akademik.kalender.index') }}" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin:0;">
    <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
      <div style="display:flex; align-items:center; gap:8px;">
        <label style="font-size:12.5px; font-weight:700; color:#475569; margin:0;">Tahun Ajaran:</label>
        <select name="tahun_ajaran_id" class="ak-input" style="font-size:12.5px; height:34px; padding:2px 10px; width:180px;" onchange="this.form.submit()">
          @foreach($tahunAjarans as $ta)
            <option value="{{ $ta->id }}" {{ $selectedTa?->id == $ta->id ? 'selected' : '' }}>
              {{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}
            </option>
          @endforeach
        </select>
      </div>

      <div style="display:flex; align-items:center; gap:6px;">
        <label style="font-size:12.5px; font-weight:700; color:#475569; margin:0;">Semester:</label>
        <a href="{{ route('akademik.kalender.index', ['tahun_ajaran_id' => $selectedTa?->id, 'semester' => 1]) }}" 
           class="ak-btn {{ $semester == 1 ? 'ak-btn-primary' : 'ak-btn-secondary' }}" 
           style="font-size:12px; height:32px; padding:4px 14px;">
           Semester 1 (Ganjil)
        </a>
        <a href="{{ route('akademik.kalender.index', ['tahun_ajaran_id' => $selectedTa?->id, 'semester' => 2]) }}" 
           class="ak-btn {{ $semester == 2 ? 'ak-btn-primary' : 'ak-btn-secondary' }}" 
           style="font-size:12px; height:32px; padding:4px 14px;">
           Semester 2 (Genap)
        </a>
      </div>
    </div>

    <div>
      @if($kalender)
        @if($kalender->is_locked)
          <span class="ak-badge ak-badge-success" style="font-size:12px; padding:6px 12px;">
            <i class="bi bi-shield-check me-1"></i> DITETAPKAN RESMI OLEH WAKA KURIKULUM
          </span>
        @else
          <span class="ak-badge ak-badge-warning" style="font-size:12px; padding:6px 12px;">
            <i class="bi bi-pencil-square me-1"></i> Draf Penyesuaian (Belum Dikunci)
          </span>
        @endif
      @else
        <span class="ak-badge ak-badge-secondary" style="font-size:12px; padding:6px 12px;">
          <i class="bi bi-info-circle me-1"></i> Belum Dibuat
        </span>
      @endif
    </div>
  </form>
</div>

@if($kalender)
  {{-- Kartu Ringkasan RPE Resmi --}}
  <div class="akademik-card" style="margin-bottom:24px; padding:18px 22px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:14px;">
      <div>
        <h2 style="font-size:16px; font-weight:800; color:var(--ak-dark); margin:0 0 2px;">
          Rincian Pekan Efektif (RPE) Baku Sekolah
        </h2>
        <div style="font-size:12px; color:#64748b;">
          Tahun Ajaran <b>{{ $selectedTa?->nama }}</b> — Semester <b>{{ $semester == 1 ? '1 (Ganjil: Juli - Des)' : '2 (Genap: Jan - Jun)' }}</b>
        </div>
      </div>
      <div style="font-size:12px; color:#0369a1; background:#f0f9ff; border:1px solid #bae6fd; padding:6px 14px; border-radius:8px;">
        <i class="bi bi-link-45deg me-1"></i> Otomatis menjadi dasar alokasi JP pada menu <b>Prota &amp; Promes</b> guru
      </div>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:14px;">
      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px 16px;">
        <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Total Pekan Semester</div>
        <div style="font-size:24px; font-weight:900; color:var(--ak-dark); margin-top:3px;">
          {{ $kalender->total_pekan }} <span style="font-size:13px; font-weight:700; color:#64748b;">Pekan</span>
        </div>
        <div style="font-size:11px; color:#64748b; margin-top:2px;">6 Bulan Kalender</div>
      </div>

      <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:14px 16px;">
        <div style="font-size:11px; font-weight:700; color:#1e40af; text-transform:uppercase; letter-spacing:0.5px;">Pekan Efektif KBM</div>
        <div style="font-size:24px; font-weight:900; color:#1e40af; margin-top:3px;">
          {{ $kalender->pekan_efektif }} <span style="font-size:13px; font-weight:700; color:#1e40af;">Pekan</span>
        </div>
        <div style="font-size:11px; color:#1e40af; margin-top:2px;">KBM Tatap Muka Aktif</div>
      </div>

      <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:14px 16px;">
        <div style="font-size:11px; font-weight:700; color:#92400e; text-transform:uppercase; letter-spacing:0.5px;">Pekan Non-Efektif / Cadangan</div>
        <div style="font-size:24px; font-weight:900; color:#92400e; margin-top:3px;">
          {{ $kalender->pekan_cadangan }} <span style="font-size:13px; font-weight:700; color:#92400e;">Pekan</span>
        </div>
        <div style="font-size:11px; color:#92400e; margin-top:2px;">MPLS, Asesmen, Rapor &amp; Libur</div>
      </div>

      <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:14px 16px;">
        <div style="font-size:11px; font-weight:700; color:#166534; text-transform:uppercase; letter-spacing:0.5px;">Simulasi JP Mapel 4 JP/Mgg</div>
        <div style="font-size:24px; font-weight:900; color:#166534; margin-top:3px;">
          {{ $kalender->pekan_efektif * 4 }} <span style="font-size:13px; font-weight:700; color:#166534;">JP / Smt</span>
        </div>
        <div style="font-size:11px; color:#166534; margin-top:2px;">Rumus: Pekan Efektif × JP/Mgg</div>
      </div>
    </div>
  </div>

  {{-- Matriks Pekan Per Bulan --}}
  <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap:18px; margin-bottom:24px;">
    @foreach($itemsByMonth as $bulanName => $monthItems)
      @php
        $efektifInMonth = $monthItems->where('jenis', 'efektif')->count();
        $nonEfektifInMonth = $monthItems->where('jenis', 'non_efektif')->count();
      @endphp
      <div class="akademik-card" style="margin:0; overflow:hidden; border:1px solid #e2e8f0; display:flex; flex-direction:column;">
        <div style="background:#f8fafc; padding:12px 16px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <h3 style="font-size:15px; font-weight:800; color:var(--ak-dark); margin:0;">
              {{ $bulanName }}
            </h3>
            <div style="font-size:11.5px; color:#64748b;">
              {{ $monthItems->count() }} Pekan ({{ $efektifInMonth }} Efektif · {{ $nonEfektifInMonth }} Non-Efektif)
            </div>
          </div>
          <span class="badge" style="background:#e0e7ff; color:#3730a3; font-weight:700; font-size:11px;">
            Pekan {{ $monthItems->first()?->minggu_ke_semester }} - {{ $monthItems->last()?->minggu_ke_semester }}
          </span>
        </div>

        <div style="padding:10px 12px; flex:1; display:flex; flex-direction:column; gap:8px;">
          @foreach($monthItems as $item)
            @php
              $isEfektif = $item->isEfektif();
              $bgCard = $isEfektif ? '#f8fafc' : '#fff7ed';
              $borderColor = $isEfektif ? '#e2e8f0' : '#fed7aa';
            @endphp
            <div style="background:{{ $bgCard }}; border:1px solid {{ $borderColor }}; border-radius:8px; padding:8px 12px; display:flex; justify-content:space-between; align-items:center; gap:10px;">
              <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                <div style="width:28px; height:28px; border-radius:6px; background:{{ $item->warna ?: ($isEfektif ? '#2563eb' : '#f59e0b') }}; color:#ffffff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:11.5px; flex-shrink:0;">
                  M{{ $item->minggu_ke }}
                </div>
                <div style="min-width:0;">
                  <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                    <span class="badge" style="background:{{ $item->warna ?: ($isEfektif ? '#2563eb' : '#f59e0b') }}; color:#ffffff; font-size:9.5px; font-weight:800; padding:2px 6px;">
                      {{ $item->getLabelSingkat() }}
                    </span>
                    <span style="font-size:11px; font-weight:700; color:#64748b;">
                      (Pekan Semester ke-{{ $item->minggu_ke_semester }})
                    </span>
                  </div>
                  <div style="font-size:12px; font-weight:700; color:var(--ak-dark); margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $item->keterangan }}">
                    {{ $item->keterangan ?: ($isEfektif ? 'KBM Efektif Tatap Muka' : 'Agenda Non-KBM') }}
                  </div>
                </div>
              </div>

              @if($canManage && !$kalender->is_locked)
                <button type="button" 
                        class="ak-btn ak-btn-secondary" 
                        style="padding:3px 8px; font-size:11px; height:26px;" 
                        onclick="openEditPekanModal({{ $item->id }}, '{{ $item->bulan }}', {{ $item->minggu_ke }}, '{{ $item->jenis }}', '{{ $item->kategori }}', '{{ addslashes($item->keterangan ?? '') }}')">
                  <i class="bi bi-pencil"></i>
                </button>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>

  {{-- Catatan Kebijakan Kurikulum --}}
  <div class="akademik-card" style="padding:16px 20px;">
    <h3 style="font-size:14px; font-weight:800; color:var(--ak-dark); margin:0 0 8px;">
      <i class="bi bi-card-text text-primary me-2"></i> Catatan Kebijakan Kaldik &amp; KBM Waka Kurikulum
    </h3>
    @if($canManage && !$kalender->is_locked)
      <form action="{{ route('akademik.kalender.catatan', $kalender->id) }}" method="POST" style="margin:0;">
        @csrf
        <div style="display:flex; gap:10px; align-items:flex-start;">
          <textarea name="catatan" class="ak-input" rows="2" style="font-size:12.5px; flex:1;" placeholder="Tuliskan catatan khusus Kaldik, misalnya instruksi pelaksanaan STS/SAS, agenda uji sertifikasi kompetensi, atau penyesuaian jam KBM...">{{ $kalender->catatan }}</textarea>
          <button type="submit" class="ak-btn ak-btn-secondary" style="font-size:12.5px; height:38px;">
            <i class="bi bi-floppy me-1"></i> Simpan Catatan
          </button>
        </div>
      </form>
    @else
      <div style="font-size:13px; color:#475569; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px 14px;">
        {{ $kalender->catatan ?: 'Belum ada catatan kebijakan khusus.' }}
      </div>
    @endif
  </div>

@else
  {{-- Empty State: Belum ada Kalender --}}
  <div class="akademik-card" style="padding:50px 20px; text-align:center;">
    <i class="bi bi-calendar2-range" style="font-size:42px; color:#94a3b8; display:block; margin-bottom:14px;"></i>
    <h3 style="font-size:17px; font-weight:800; color:var(--ak-dark); margin:0 0 6px;">
      Kalender Pendidikan Semester {{ $semester == 1 ? '1 (Ganjil)' : '2 (Genap)' }} Belum Dibuat
    </h3>
    <p style="font-size:13px; color:#64748b; max-width:540px; margin:0 auto 20px;">
      Kalender Pendidikan dan Penetapan RPE semester ini belum diterbitkan oleh Waka Kurikulum. Klik tombol di bawah untuk membuat template otomatis standar SMK (MPLS, KBM, STS, SAS/SAT, UKK, Rapor &amp; Libur).
    </p>

    @if($canManage)
      <form action="{{ route('akademik.kalender.generate') }}" method="POST" style="margin:0;">
        @csrf
        <input type="hidden" name="tahun_ajaran_id" value="{{ $selectedTa?->id }}">
        <input type="hidden" name="semester" value="{{ $semester }}">
        <button type="submit" class="ak-btn ak-btn-primary" style="font-size:13px; padding:8px 20px;">
          <i class="bi bi-magic me-1"></i> Buat Template Standar Kaldik SMK Sekarang
        </button>
      </form>
    @endif
  </div>
@endif

{{-- Modal Edit Pekan --}}
<div class="modal fade" id="modalEditPekan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:14px; border:none; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
      <form id="formEditPekan" method="POST">
        @csrf
        <div class="modal-header" style="border-bottom:1px solid #e2e8f0; padding:14px 18px;">
          <h5 class="modal-title" style="font-size:15px; font-weight:800; color:var(--ak-dark);" id="modalEditPekanTitle">
            Edit Status Pekan
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" style="padding:16px 18px; display:flex; flex-direction:column; gap:12px;">
          <div>
            <label style="font-size:12px; font-weight:700; color:#475569; margin-bottom:4px; display:block;">Jenis Pekan:</label>
            <select name="jenis" id="inputJenis" class="ak-input" style="font-size:12.5px;" onchange="handleJenisChange()">
              <option value="efektif">Efektif (KBM Tatap Muka Aktif)</option>
              <option value="non_efektif">Non-Efektif (Asesmen, MPLS, Agenda Khusus, Libur)</option>
            </select>
          </div>

          <div>
            <label style="font-size:12px; font-weight:700; color:#475569; margin-bottom:4px; display:block;">Kategori Agenda:</label>
            <select name="kategori" id="inputKategori" class="ak-input" style="font-size:12.5px;">
              <option value="kbm">KBM Tatap Muka</option>
              <option value="mpls">MPLS &amp; Pengenalan Budaya Kerja</option>
              <option value="sts">Sumatif Tengah Semester (STS)</option>
              <option value="sas">Sumatif Akhir Semester (SAS / ASAS)</option>
              <option value="sat">Sumatif Akhir Tahun (SAT)</option>
              <option value="ukk">Uji Kompetensi Keahlian (UKK) Kejuruan</option>
              <option value="pkl">Praktik Kerja Lapangan (PKL)</option>
              <option value="rapor">Pengolahan Nilai &amp; Pembagian Rapor</option>
              <option value="libur">Hari Libur Semester / Nasional</option>
              <option value="lainnya">Agenda Lainnya</option>
            </select>
          </div>

          <div>
            <label style="font-size:12px; font-weight:700; color:#475569; margin-bottom:4px; display:block;">Keterangan / Nama Kegiatan:</label>
            <input type="text" name="keterangan" id="inputKeterangan" class="ak-input" style="font-size:12.5px;" placeholder="Contoh: KBM Efektif Pekan 1 atau Sumatif Tengah Semester">
          </div>
        </div>
        <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:12px 18px;">
          <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal" style="font-size:12px;">Batal</button>
          <button type="submit" class="ak-btn ak-btn-primary" style="font-size:12px;">
            <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  function openEditPekanModal(id, bulan, mingguKe, jenis, kategori, keterangan) {
    const form = document.getElementById('formEditPekan');
    form.action = "{{ url('dcc/akademik/kalender/item') }}/" + id;
    
    document.getElementById('modalEditPekanTitle').innerText = 'Edit Pekan ke-' + mingguKe + ' (' + bulan + ')';
    document.getElementById('inputJenis').value = jenis;
    document.getElementById('inputKategori').value = kategori;
    document.getElementById('inputKeterangan').value = keterangan;

    const modal = new bootstrap.Modal(document.getElementById('modalEditPekan'));
    modal.show();
  }

  function handleJenisChange() {
    const jenis = document.getElementById('inputJenis').value;
    const kategori = document.getElementById('inputKategori');
    if (jenis === 'efektif') {
      kategori.value = 'kbm';
    } else if (kategori.value === 'kbm') {
      kategori.value = 'sts';
    }
  }
</script>
@endpush

@endsection
