@extends('dcc.akademik.layout')

@section('title', $perangkat->mataPelajaran?->nama_mapel . ' — Perangkat Pembelajaran')
@section('breadcrumb')
  <a href="{{ route('akademik.perangkat.index') }}">Perangkat Pembelajaran</a>
  <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
  <span>Detail Dokumen</span>
@endsection

@section('content')
<div class="akademik-page-head">
  <div>
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
      <span class="badge" style="background:#e0e7ff; color:#3730a3; font-size:12px; font-weight:800;">
        Kelas {{ $perangkat->tingkat }} · Fase {{ $perangkat->fase }}
      </span>
      <span class="badge" style="background:#f1f5f9; color:#475569; font-size:12px; font-weight:700;">
        Semester {{ $perangkat->semester == 1 ? 'Ganjil' : 'Genap' }} · TA {{ $perangkat->tahunAjaran?->nama ?? '2026/2027' }}
      </span>
      <span class="badge" style="background:{{ $statusBadge['bg'] }}; color:{{ $statusBadge['color'] }}; border:1px solid {{ $statusBadge['border'] }}; font-size:12px; font-weight:700;">
        <i class="bi {{ $statusBadge['icon'] }} me-1"></i> {{ $statusBadge['label'] }}
      </span>
    </div>
    <h1 class="akademik-page-title">{{ $perangkat->mataPelajaran?->nama_mapel }}</h1>
    <div class="akademik-page-desc">
      Guru Pengampu: <strong>{{ $perangkat->guru?->nama }}</strong> (NIP: {{ $perangkat->guru?->nip ?? '-' }})
    </div>
  </div>

  <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
    <a href="{{ route('akademik.perangkat.index', ['semester' => $perangkat->semester]) }}" class="ak-btn ak-btn-secondary">
      <i class="bi bi-arrow-left"></i>
      <span>Kembali</span>
    </a>

    {{-- Tombol Ajukan Guru --}}
    @if($perangkat->status !== 'disahkan' && ($isOwner || $isAdminOrWaka))
      <form action="{{ route('akademik.perangkat.ajukan', $perangkat->id) }}" method="POST" style="margin:0;">
        @csrf
        <button type="submit" class="ak-btn ak-btn-primary" onclick="return confirm('Ajukan perangkat pembelajaran ini ke Waka Kurikulum & Kepala Sekolah?')">
          <i class="bi bi-send-check"></i>
          <span>Ajukan Supervisi</span>
        </button>
      </form>
    @endif

    {{-- Tombol Cetak Pengesahan jika Disahkan --}}
    @if($perangkat->status === 'disahkan')
      <a href="{{ route('akademik.perangkat.cetak-pengesahan', $perangkat->id) }}" target="_blank" class="ak-btn ak-btn-secondary" style="border-color:#059669; color:#059669; font-weight:700;">
        <i class="bi bi-printer"></i>
        <span>Cetak Lembar Pengesahan Resmi</span>
      </a>
    @endif

    {{-- Tombol Export PDF & DOCX (selalu tersedia) --}}
    <div class="dropdown d-inline-block">
      <button type="button" class="ak-btn" style="background:linear-gradient(135deg,#1d4ed8,#6d28d9); color:#fff; font-weight:700; border:none; display:flex; align-items:center; gap:6px;" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-cloud-download"></i>
        <span>Unduh Dokumen</span>
        <i class="bi bi-chevron-down" style="font-size:11px;"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="border:none; border-radius:12px; min-width:220px; padding:8px;">
        <li>
          <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 rounded-2" href="{{ route('akademik.perangkat.export-pdf', $perangkat->id) }}" target="_blank" style="font-weight:600; color:#dc2626;">
            <i class="bi bi-file-earmark-pdf-fill" style="font-size:18px; color:#dc2626;"></i>
            <div>
              <div style="font-size:13px;">Unduh PDF</div>
              <div style="font-size:10px; color:#888; font-weight:400;">Perangkat Lengkap (5 Bab)</div>
            </div>
          </a>
        </li>
        <li><hr class="dropdown-divider my-1"></li>
        <li>
          <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 rounded-2" href="{{ route('akademik.perangkat.export-docx', $perangkat->id) }}" style="font-weight:600; color:#2563eb;">
            <i class="bi bi-file-earmark-word-fill" style="font-size:18px; color:#2563eb;"></i>
            <div>
              <div style="font-size:13px;">Unduh DOCX (Word)</div>
              <div style="font-size:10px; color:#888; font-weight:400;">Dapat diedit di Microsoft Word</div>
            </div>
          </a>
        </li>
      </ul>
    </div>


    {{-- Tombol Supervisi Wakakur / Kepsek --}}
    @if($isAdminOrWaka)
      <button type="button" class="ak-btn ak-btn-secondary" data-bs-toggle="modal" data-bs-target="#modalSupervisi">
        <i class="bi bi-shield-check"></i>
        <span>Supervisi &amp; Validasi</span>
      </button>
    @endif
  </div>
</div>

{{-- Bar Progress Kelengkapan --}}
<div class="akademik-card" style="margin-bottom:20px; padding:16px 20px;">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; flex-wrap:wrap; gap:8px;">
    <div style="font-size:13px; font-weight:800; color:var(--ak-dark);">
      <i class="bi bi-clipboard2-check text-primary me-1"></i> Keterpenuhan 5 Pilar Standar Perangkat Kurikulum Merdeka:
    </div>
    <div style="font-size:13px; font-weight:900; color:{{ $kelengkapan['persen'] == 100 ? '#059669' : '#0284c7' }};">
      {{ $kelengkapan['persen'] }}% Lengkap
    </div>
  </div>
  <div class="progress" style="height:8px; background:#e2e8f0; border-radius:4px; margin-bottom:12px;">
    <div class="progress-bar" style="width: {{ $kelengkapan['persen'] }}%; background-color: {{ $kelengkapan['persen'] == 100 ? '#10b981' : '#0284c7' }};"></div>
  </div>

  <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:10px; font-size:12px;">
    <div style="display:flex; align-items:center; gap:6px; color:{{ $kelengkapan['has_cp'] ? '#059669' : '#64748b' }};">
      <i class="bi {{ $kelengkapan['has_cp'] ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted' }}"></i>
      <span>1. Capaian Pembelajaran (CP)</span>
    </div>
    <div style="display:flex; align-items:center; gap:6px; color:{{ $kelengkapan['has_atp'] ? '#059669' : '#64748b' }};">
      <i class="bi {{ $kelengkapan['has_atp'] ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted' }}"></i>
      <span>2. Alur Tujuan Pembelajaran (ATP)</span>
    </div>
    <div style="display:flex; align-items:center; gap:6px; color:{{ $kelengkapan['has_rpe'] ? '#059669' : '#64748b' }};">
      <i class="bi {{ $kelengkapan['has_rpe'] ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted' }}"></i>
      <span>3. Pekan Efektif &amp; Prota/Promes</span>
    </div>
    <div style="display:flex; align-items:center; gap:6px; color:{{ $kelengkapan['has_modul'] ? '#059669' : '#64748b' }};">
      <i class="bi {{ $kelengkapan['has_modul'] ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted' }}"></i>
      <span>4. Modul Ajar &amp; LKPD Praktik</span>
    </div>
    <div style="display:flex; align-items:center; gap:6px; color:{{ $kelengkapan['has_kktp'] ? '#059669' : '#64748b' }};">
      <i class="bi {{ $kelengkapan['has_kktp'] ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted' }}"></i>
      <span>5. Kriteria Ketuntasan (KKTP)</span>
    </div>
  </div>

  @if($perangkat->status === 'perlu_revisi' && $perangkat->catatan_supervisi)
    <div style="margin-top:14px; background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:12px 14px; font-size:12.5px; color:#991b1b;">
      <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Catatan Revisi dari Waka Kurikulum / Kepala Sekolah:</strong>
      <div style="margin-top:4px; line-height:1.4;">{{ $perangkat->catatan_supervisi }}</div>
    </div>
  @endif
</div>

{{-- Sub-Nav Tabs --}}
@php
  $activeTab = request('tab', 'atp');
@endphp
<div style="display:flex; gap:8px; border-bottom:2px solid var(--ak-slate-200); margin-bottom:20px; overflow-x:auto; padding-bottom:2px;">
  <a href="{{ route('akademik.perangkat.show', ['id' => $perangkat->id, 'tab' => 'cp']) }}"
     class="ak-btn {{ $activeTab === 'cp' ? 'ak-btn-primary' : 'ak-btn-secondary' }}"
     style="font-size:12.5px; font-weight:700; border-radius:8px 8px 0 0; padding:8px 16px;">
    <i class="bi bi-file-earmark-text"></i>
    <span>1. Capaian Pembelajaran (CP)</span>
  </a>

  <a href="{{ route('akademik.perangkat.show', ['id' => $perangkat->id, 'tab' => 'atp']) }}"
     class="ak-btn {{ $activeTab === 'atp' ? 'ak-btn-primary' : 'ak-btn-secondary' }}"
     style="font-size:12.5px; font-weight:700; border-radius:8px 8px 0 0; padding:8px 16px;">
    <i class="bi bi-list-ol"></i>
    <span>2. Alur Tujuan Pembelajaran (ATP)</span>
    <span class="badge rounded-pill bg-white text-dark ms-1" style="font-size:10px;">{{ $perangkat->atpItems->count() }}</span>
  </a>

  <a href="{{ route('akademik.perangkat.show', ['id' => $perangkat->id, 'tab' => 'rpe']) }}"
     class="ak-btn {{ $activeTab === 'rpe' ? 'ak-btn-primary' : 'ak-btn-secondary' }}"
     style="font-size:12.5px; font-weight:700; border-radius:8px 8px 0 0; padding:8px 16px;">
    <i class="bi bi-calendar3"></i>
    <span>3. RPE, Prota &amp; Promes</span>
  </a>

  <a href="{{ route('akademik.perangkat.show', ['id' => $perangkat->id, 'tab' => 'modul']) }}"
     class="ak-btn {{ $activeTab === 'modul' ? 'ak-btn-primary' : 'ak-btn-secondary' }}"
     style="font-size:12.5px; font-weight:700; border-radius:8px 8px 0 0; padding:8px 16px;">
    <i class="bi bi-journal-richtext"></i>
    <span>4. Modul Ajar &amp; LKPD</span>
    <span class="badge rounded-pill bg-white text-dark ms-1" style="font-size:10px;">{{ $perangkat->modulAjars->count() }}</span>
  </a>

  <a href="{{ route('akademik.perangkat.show', ['id' => $perangkat->id, 'tab' => 'kktp']) }}"
     class="ak-btn {{ $activeTab === 'kktp' ? 'ak-btn-primary' : 'ak-btn-secondary' }}"
     style="font-size:12.5px; font-weight:700; border-radius:8px 8px 0 0; padding:8px 16px;">
    <i class="bi bi-speedometer2"></i>
    <span>5. KKTP (Ketuntasan)</span>
  </a>
</div>

{{-- TAB 1: CAPAIAN PEMBELAJARAN (CP) --}}
@if($activeTab === 'cp')
<div class="akademik-card">
  <div class="akademik-card-header" style="display:flex; justify-content:space-between; align-items:center;">
    <h3 class="akademik-card-title">
      <i class="bi bi-bookmark-star text-primary"></i>
      <span>Capaian Pembelajaran (CP) — Fase {{ $perangkat->fase }} (Kelas {{ $perangkat->tingkat }})</span>
    </h3>
    <div style="display:flex; align-items:center; gap:8px;">
      @if(!empty($perangkat->capaian_pembelajaran))
        <span class="badge" style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; font-size:11.5px; font-weight:700;">
          <i class="bi bi-person-check-fill me-1"></i> Rumusan Guru
        </span>
      @else
        <span class="ak-badge ak-badge-secondary">Referensi SK BSKAP 032/2024</span>
      @endif

      @if($isOwner || $isAdminOrWaka)
        <a href="{{ route('akademik.perangkat.cp', ['perangkat_id' => $perangkat->id, 'semester' => $perangkat->semester]) }}" class="ak-btn ak-btn-primary ak-btn-sm" style="font-size:12px;">
          <i class="bi bi-pencil-square me-1"></i> Edit CP Guru
        </a>
      @endif
    </div>
  </div>
  <div class="akademik-card-body">
    @php
      $cpText = $perangkat->resolved_cp;
      $elemenList = $perangkat->resolved_elemen_cp;
    @endphp

    @if(!empty($cpText))
      <div style="background:#f8fafc; border-left:4px solid #0284c7; border-radius:8px; padding:18px 20px; font-size:13.5px; color:#1e293b; margin-bottom:20px;">
        {!! format_narasi_kbm($cpText) !!}
      </div>
    @else
      <div style="text-align:center; padding:30px; color:#94a3b8;">
        <i class="bi bi-info-circle" style="font-size:32px; display:block; margin-bottom:8px;"></i>
        Deskripsi Capaian Pembelajaran belum diinput oleh guru.
        @if($isOwner || $isAdminOrWaka)
          <div style="margin-top:10px;">
            <a href="{{ route('akademik.perangkat.cp', ['perangkat_id' => $perangkat->id, 'semester' => $perangkat->semester]) }}" class="ak-btn ak-btn-primary ak-btn-sm">
              <i class="bi bi-pencil-square me-1"></i> Input Capaian Pembelajaran Sekarang
            </a>
          </div>
        @endif
      </div>
    @endif

    @if(!empty($perangkat->rasional_tujuan))
      <div style="margin-bottom:20px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:14px 18px;">
        <h4 style="font-size:12.5px; font-weight:800; color:#334155; margin-bottom:6px; text-transform:uppercase;">
          <i class="bi bi-bullseye text-primary me-1"></i> Rasional &amp; Tujuan Mata Pelajaran:
        </h4>
        <div style="font-size:13px; color:#475569;">
          {!! format_narasi_kbm($perangkat->rasional_tujuan, ['line_height' => '1.7', 'margin_bottom' => '8px']) !!}
        </div>
      </div>
    @endif

    {{-- Elemen CP --}}
    @if(!empty($elemenList) && count($elemenList) > 0)
      <div style="margin-bottom:20px;">
        <h4 style="font-size:13px; font-weight:800; color:#334155; margin-bottom:8px;">
          <i class="bi bi-diagram-3 text-primary me-1"></i> Elemen Kompetensi Capaian Pembelajaran:
        </h4>
        <div class="akademik-table-wrap">
          <table class="akademik-table">
            <thead>
              <tr>
                <th style="width:40px; text-align:center;">#</th>
                <th style="width:240px;">Nama Elemen</th>
                <th>Deskripsi Capaian Pembelajaran Elemen</th>
              </tr>
            </thead>
            <tbody>
              @foreach($elemenList as $idx => $el)
                <tr>
                  <td style="text-align:center; font-weight:700; color:#94a3b8;">{{ $loop->iteration }}</td>
                  <td style="font-weight:700; color:var(--ak-dark); font-size:13px;">
                    {{ is_array($el) ? ($el['nama'] ?? $el['elemen'] ?? '-') : $el }}
                  </td>
                  <td style="font-size:12.5px; color:#334155; line-height:1.55;">
                    {{ is_array($el) ? ($el['deskripsi'] ?? $el['capaian'] ?? '-') : '-' }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endif

    <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:14px 18px; font-size:12.5px; color:#166534;">
      <strong><i class="bi bi-lightbulb me-1"></i> Prinsip Kurikulum Merdeka SMK:</strong>
      <div style="margin-top:4px;">
        Capaian Pembelajaran (CP) dirumuskan oleh Guru Pengampu mata pelajaran sebagai kompetensi pembelajaran yang harus dicapai peserta didik pada Fase {{ $perangkat->fase }}. CP ini kemudian diturunkan menjadi butir-butir <strong>Alur Tujuan Pembelajaran (ATP)</strong> pada Tab 2.
      </div>
    </div>
  </div>
</div>
@endif

{{-- TAB 2: ALUR TUJUAN PEMBELAJARAN (ATP) --}}
@if($activeTab === 'atp')
<div class="akademik-card">
  <div class="akademik-card-header" style="justify-content:space-between; flex-wrap:wrap; gap:10px;">
    <div>
      <h3 class="akademik-card-title">
        <i class="bi bi-list-check text-primary"></i>
        <span>Alur Tujuan Pembelajaran (ATP) &amp; Materi Pokok</span>
      </h3>
      <div style="font-size:12px; color:var(--ak-slate-500); margin-top:2px;">
        Rangkaian Tujuan Pembelajaran yang tersusun sistematis dan logis di dalam fase secara utuh dan menurut urutan pembelajaran sejak awal hingga akhir suatu fase.
      </div>
    </div>

    @if($perangkat->status !== 'disahkan' || $isAdminOrWaka)
      <button type="button" class="ak-btn ak-btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahAtp">
        <i class="bi bi-plus-lg"></i>
        <span>Tambah Butir TP</span>
      </button>
    @endif
  </div>

  <div class="table-responsive">
    <table class="table align-middle" style="margin-bottom:0; font-size:12.5px; color:#1e293b;">
      <thead style="background:#f8fafc; border-bottom:1.5px solid var(--ak-slate-200); color:#475569; font-weight:700;">
        <tr>
          <th style="width:40px; text-align:center;">Urutan</th>
          <th style="width:90px;">Kode TP</th>
          <th style="width:160px;">Elemen CP</th>
          <th>Tujuan Pembelajaran (TP)</th>
          <th style="width:180px;">Materi Pokok</th>
          <th style="width:60px; text-align:center;">Alokasi</th>
          <th style="width:140px;">Profil Pancasila</th>
          <th style="width:80px; text-align:center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($perangkat->atpItems as $atp)
          <tr style="border-bottom:1px solid #f1f5f9;">
            <td style="text-align:center; font-weight:700; color:#64748b;">{{ $atp->urutan }}</td>
            <td>
              <span class="badge" style="background:#f1f5f9; color:#1e293b; font-weight:800; border:1px solid #cbd5e1;">
                {{ $atp->kode_tp }}
              </span>
            </td>
            <td>
              <span style="font-weight:600; color:#0369a1;">{{ $atp->elemen_cp ?? 'Elemen Umum' }}</span>
            </td>
            <td>
              <div style="font-weight:600; line-height:1.5; color:#0f172a;">
                {{ $atp->tujuan_pembelajaran }}
              </div>
              @if($atp->asesmen_rencana)
                <div style="font-size:11px; color:#64748b; margin-top:3px;">
                  <i class="bi bi-patch-question me-1"></i> Rencana Asesmen: {{ $atp->asesmen_rencana }}
                </div>
              @endif
            </td>
            <td>
              <div style="font-weight:700; color:#334155;">{{ $atp->materi_pokok }}</div>
            </td>
            <td style="text-align:center; font-weight:800; color:#0284c7;">
              {{ $atp->alokasi_jp }} JP
            </td>
            <td>
              <span style="font-size:11px; color:#475569;">{{ $atp->profil_pancasila ?? 'Mandiri, Bernalar Kritis' }}</span>
            </td>
            <td style="text-align:center;">
              @if($perangkat->status !== 'disahkan' || $isAdminOrWaka)
                <form action="{{ route('akademik.perangkat.atp.destroy', [$perangkat->id, $atp->id]) }}" method="POST" style="margin:0; display:inline;" onsubmit="return confirm('Hapus butir Tujuan Pembelajaran ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="ak-btn ak-btn-secondary" style="padding:3px 8px; color:#dc2626;" title="Hapus">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" style="text-align:center; padding:35px 20px; color:#94a3b8;">
              <i class="bi bi-journal-plus" style="font-size:36px; display:block; margin-bottom:6px;"></i>
              <div style="font-weight:700; color:#475569;">Belum Ada Butir Alur Tujuan Pembelajaran (ATP)</div>
              <div style="font-size:12px;">Klik tombol "+ Tambah Butir TP" di atas untuk menambahkan tujuan pembelajaran turunan dari CP.</div>
            </td>
          </tr>
        @endforelse
      </tbody>
      @if($perangkat->atpItems->isNotEmpty())
        <tfoot style="background:#f8fafc; font-weight:800; border-top:1.5px solid var(--ak-slate-200);">
          <tr>
            <td colspan="5" style="text-align:right; padding:10px 14px;">Total Jam Pelajaran ATP:</td>
            <td style="text-align:center; color:#0284c7; padding:10px 0;">{{ $perangkat->atpItems->sum('alokasi_jp') }} JP</td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      @endif
    </table>
  </div>
</div>

{{-- Modal Tambah Butir ATP --}}
<div class="modal fade" id="modalTambahAtp" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius:12px; border:none; box-shadow:0 15px 35px rgba(0,0,0,0.15);">
      <div class="modal-header" style="border-bottom:1px solid var(--ak-slate-200); padding:16px 20px;">
        <h5 class="modal-title" style="font-size:15px; font-weight:800; color:var(--ak-dark);">
          <i class="bi bi-plus-circle-fill text-primary me-1"></i> Tambah Butir Tujuan Pembelajaran (ATP)
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('akademik.perangkat.atp.store', $perangkat->id) }}" method="POST">
        @csrf
        <div class="modal-body" style="padding:20px;">
          <div style="display:grid; grid-template-columns: 100px 120px 1fr; gap:12px; margin-bottom:14px;">
            <div>
              <label class="ak-form-label">Urutan</label>
              <input type="number" name="urutan" class="ak-input" value="{{ $perangkat->atpItems->count() + 1 }}" min="1" required>
            </div>
            <div>
              <label class="ak-form-label">Kode TP</label>
              <input type="text" name="kode_tp" class="ak-input" value="TP {{ $perangkat->semester }}.{{ $perangkat->atpItems->count() + 1 }}" required>
            </div>
            <div>
              <label class="ak-form-label">Elemen Capaian Pembelajaran (CP)</label>
              <input type="text" name="elemen_cp" class="ak-input" placeholder="contoh: Pemrograman Berorientasi Objek / Berpikir Komputasional">
            </div>
          </div>

          <div style="margin-bottom:14px;">
            <label class="ak-form-label">Rumusan Tujuan Pembelajaran (TP) <span class="text-danger">*</span></label>
            <textarea name="tujuan_pembelajaran" class="ak-textarea" rows="2" placeholder="Peserta didik mampu memahami dan mengimplementasikan..." required></textarea>
          </div>

          <div style="display:grid; grid-template-columns: 2fr 100px; gap:12px; margin-bottom:14px;">
            <div>
              <label class="ak-form-label">Materi Pokok / Lingkup Materi <span class="text-danger">*</span></label>
              <input type="text" name="materi_pokok" class="ak-input" placeholder="contoh: CRUD Laravel & Relasi Database" required>
            </div>
            <div>
              <label class="ak-form-label">Alokasi JP</label>
              <input type="number" name="alokasi_jp" class="ak-input" value="4" min="1" max="40" required>
            </div>
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
            <div>
              <label class="ak-form-label">Dimensi Profil Pelajar Pancasila</label>
              <input type="text" name="profil_pancasila" class="ak-input" value="Mandiri, Bernalar Kritis, Kreatif">
            </div>
            <div>
              <label class="ak-form-label">Rencana Asesmen (Formatif/Sumatif)</label>
              <input type="text" name="asesmen_rencana" class="ak-input" placeholder="contoh: Praktik Lab, Kuis Formatif">
            </div>
          </div>
        </div>
        <div class="modal-footer" style="border-top:1px solid var(--ak-slate-200); padding:12px 20px;">
          <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="ak-btn ak-btn-primary">Simpan Butir TP</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

{{-- TAB 3: RPE, PROTA & PROMES --}}
@if($activeTab === 'rpe')
<div class="akademik-card">
  <div class="akademik-card-header">
    <h3 class="akademik-card-title">
      <i class="bi bi-calendar2-range text-primary"></i>
      <span>Rincian Pekan Efektif (RPE) &amp; Program Tahunan / Semester</span>
    </h3>
  </div>
  <div class="akademik-card-body">
    <form action="{{ route('akademik.perangkat.update-info', $perangkat->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:20px;">
        <div style="background:#f8fafc; padding:16px; border-radius:8px; border:1px solid #e2e8f0;">
          <label class="ak-form-label">Jumlah Pekan Efektif KBM</label>
          <input type="number" name="rpe_pekan_efektif" class="ak-input" value="{{ $perangkat->rpe_pekan_efektif }}" min="1" max="26" required>
          <span style="font-size:11.5px; color:#64748b; margin-top:4px; display:block;">Pekan aktif tatap muka efektif di kelas/lab.</span>
        </div>

        <div style="background:#f8fafc; padding:16px; border-radius:8px; border:1px solid #e2e8f0;">
          <label class="ak-form-label">Pekan Cadangan / STS / SAS</label>
          <input type="number" name="rpe_pekan_cadangan" class="ak-input" value="{{ $perangkat->rpe_pekan_cadangan }}" min="0" max="10" required>
          <span style="font-size:11.5px; color:#64748b; margin-top:4px; display:block;">Pekan ujian tengah/akhir semester &amp; remedial.</span>
        </div>

        <div style="background:#f8fafc; padding:16px; border-radius:8px; border:1px solid #e2e8f0;">
          <label class="ak-form-label">Berkas Kaldik / Matriks Promes</label>
          <input type="file" name="file_kaldik" class="ak-input" accept=".pdf,.jpg,.png">
          @if($perangkat->file_kaldik_rpe)
            <div style="margin-top:6px; font-size:12px;">
              <a href="{{ asset('storage/' . $perangkat->file_kaldik_rpe) }}" target="_blank" class="text-primary fw-bold">
                <i class="bi bi-file-earmark-pdf me-1"></i> Lihat Berkas Terunggah
              </a>
            </div>
          @endif
        </div>
      </div>

      <div style="margin-bottom:18px;">
        <label class="ak-form-label">Catatan Perencanaan Guru (Prota / Promes)</label>
        <textarea name="catatan_guru" class="ak-textarea" rows="3" placeholder="Catatan kesepakatan pembagian modul ajar per minggu efektif...">{{ $perangkat->catatan_guru }}</textarea>
      </div>

      <button type="submit" class="ak-btn ak-btn-primary">
        <i class="bi bi-save"></i>
        <span>Simpan Pengaturan RPE</span>
      </button>
    </form>
  </div>
</div>
@endif

{{-- TAB 4: MODUL AJAR (MA) & LKPD --}}
@if($activeTab === 'modul')
<div class="akademik-card">
  <div class="akademik-card-header" style="justify-content:space-between; flex-wrap:wrap; gap:10px;">
    <div>
      <h3 class="akademik-card-title">
        <i class="bi bi-journal-code text-primary"></i>
        <span>Modul Ajar (MA) / RPP Merdeka &amp; Bahan Ajar Vokasi</span>
      </h3>
      <div style="font-size:12px; color:var(--ak-slate-500); margin-top:2px;">
        Perencanaan pembelajaran operasional per materi/bab lengkap dengan model pembelajaran vokasi (PjBL/TeFa), LKPD, dan jobsheet praktik.
      </div>
    </div>

    @if($perangkat->status !== 'disahkan' || $isAdminOrWaka)
      <button type="button" class="ak-btn ak-btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahModul">
        <i class="bi bi-plus-lg"></i>
        <span>Tambah Modul Ajar</span>
      </button>
    @endif
  </div>

  <div class="akademik-card-body">
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:16px;">
      @forelse($perangkat->modulAjars as $modul)
        <div style="border:1px solid #e2e8f0; border-radius:10px; padding:16px; background:#ffffff; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
            <div>
              <span class="badge" style="background:#e0f2fe; color:#0369a1; font-weight:800; font-size:11px;">
                Pertemuan {{ $modul->pertemuan_ke_mulai }} s.d. {{ $modul->pertemuan_ke_selesai }} ({{ $modul->alokasi_jp }} JP)
              </span>
              @if($modul->model_pembelajaran)
                <span class="badge" style="background:#f1f5f9; color:#475569; font-weight:700; font-size:11px;">
                  {{ $modul->model_pembelajaran }}
                </span>
              @endif
            </div>
            @if($perangkat->status !== 'disahkan' || $isAdminOrWaka)
              <form action="{{ route('akademik.perangkat.modul.destroy', [$perangkat->id, $modul->id]) }}" method="POST" style="margin:0;" onsubmit="return confirm('Hapus modul ajar ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm text-danger p-0" title="Hapus Modul"><i class="bi bi-x-circle-fill"></i></button>
              </form>
            @endif
          </div>

          <h4 style="font-size:14px; font-weight:800; color:#0f172a; margin-bottom:6px;">{{ $modul->judul_modul }}</h4>

          @if($modul->atpItem)
            <div style="font-size:11.5px; color:#0369a1; margin-bottom:8px;">
              <i class="bi bi-link-45deg"></i> Terhubung: {{ $modul->atpItem->kode_tp }} — {{ Str::limit($modul->atpItem->tujuan_pembelajaran, 60) }}
            </div>
          @endif

          @if($modul->pertanyaan_pemantik)
            <div style="font-size:12px; background:#f8fafc; padding:8px 10px; border-radius:6px; margin-bottom:10px; font-style:italic; color:#475569;">
              "{{ $modul->pertanyaan_pemantik }}"
            </div>
          @endif

          {{-- File Downloads --}}
          <div style="display:flex; gap:6px; flex-wrap:wrap; border-top:1px solid #f1f5f9; padding-top:10px; margin-top:8px;">
            @if($modul->file_modul_pdf)
              <a href="{{ asset('storage/' . $modul->file_modul_pdf) }}" target="_blank" class="ak-btn ak-btn-secondary" style="font-size:11px; padding:3px 8px; color:#0284c7;">
                <i class="bi bi-file-earmark-pdf"></i> Modul PDF
              </a>
            @endif
            @if($modul->file_lkpd_pdf)
              <a href="{{ asset('storage/' . $modul->file_lkpd_pdf) }}" target="_blank" class="ak-btn ak-btn-secondary" style="font-size:11px; padding:3px 8px; color:#059669;">
                <i class="bi bi-file-earmark-ruled"></i> LKPD Siswa
              </a>
            @endif
            @if($modul->file_jobsheet_praktik)
              <a href="{{ asset('storage/' . $modul->file_jobsheet_praktik) }}" target="_blank" class="ak-btn ak-btn-secondary" style="font-size:11px; padding:3px 8px; color:#d97706;">
                <i class="bi bi-tools"></i> Jobsheet Praktik
              </a>
            @endif
            @if($modul->link_media_pembelajaran)
              <a href="{{ $modul->link_media_pembelajaran }}" target="_blank" class="ak-btn ak-btn-secondary" style="font-size:11px; padding:3px 8px;">
                <i class="bi bi-link-45deg"></i> Media Online
              </a>
            @endif
          </div>
        </div>
      @empty
        <div style="grid-column:1 / -1; text-align:center; padding:35px; color:#94a3b8;">
          <i class="bi bi-journal-x" style="font-size:36px; display:block; margin-bottom:6px;"></i>
          <div style="font-weight:700; color:#475569;">Belum Ada Modul Ajar / RPP Merdeka</div>
          <div style="font-size:12px;">Gunakan tombol "+ Tambah Modul Ajar" di atas untuk menambahkan dokumen pembelajaran &amp; LKPD.</div>
        </div>
      @endforelse
    </div>
  </div>
</div>

{{-- Modal Tambah Modul Ajar --}}
<div class="modal fade" id="modalTambahModul" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius:12px; border:none; box-shadow:0 15px 35px rgba(0,0,0,0.15);">
      <div class="modal-header" style="border-bottom:1px solid var(--ak-slate-200); padding:16px 20px;">
        <h5 class="modal-title" style="font-size:15px; font-weight:800; color:var(--ak-dark);">
          <i class="bi bi-journal-plus text-primary me-1"></i> Tambah Modul Ajar / RPP Merdeka
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('akademik.perangkat.modul.store', $perangkat->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body" style="padding:20px;">
          <div style="display:grid; grid-template-columns: 2fr 1fr; gap:12px; margin-bottom:14px;">
            <div>
              <label class="ak-form-label">Judul Modul / Pokok Bahasan <span class="text-danger">*</span></label>
              <input type="text" name="judul_modul" class="ak-input" placeholder="contoh: Modul 1: Pemrograman Web dengan Laravel" required>
            </div>
            <div>
              <label class="ak-form-label">Rujukan Tujuan Pembelajaran (ATP)</label>
              <select name="atp_item_id" class="ak-select">
                <option value="">-- Pilih Rujukan TP --</option>
                @foreach($perangkat->atpItems as $item)
                  <option value="{{ $item->id }}">{{ $item->kode_tp }} — {{ Str::limit($item->tujuan_pembelajaran, 40) }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:12px; margin-bottom:14px;">
            <div>
              <label class="ak-form-label">Pertemuan Ke Mulai</label>
              <input type="number" name="pertemuan_ke_mulai" class="ak-input" value="1" min="1" required>
            </div>
            <div>
              <label class="ak-form-label">Pertemuan Ke Selesai</label>
              <input type="number" name="pertemuan_ke_selesai" class="ak-input" value="2" min="1" required>
            </div>
            <div>
              <label class="ak-form-label">Total Alokasi JP</label>
              <input type="number" name="alokasi_jp" class="ak-input" value="4" min="1" max="40" required>
            </div>
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:14px;">
            <div>
              <label class="ak-form-label">Model Pembelajaran Vokasi</label>
              <input type="text" name="model_pembelajaran" class="ak-input" placeholder="contoh: Project-Based Learning (PjBL) / Teaching Factory">
            </div>
            <div>
              <label class="ak-form-label">Metode Pembelajaran</label>
              <input type="text" name="metode_pembelajaran" class="ak-input" placeholder="contoh: Praktikum Laboratorium Komputer">
            </div>
          </div>

          <div style="margin-bottom:14px;">
            <label class="ak-form-label">Pertanyaan Pemantik</label>
            <input type="text" name="pertanyaan_pemantik" class="ak-input" placeholder="contoh: Mengapa sistem informasi sekolah membutuhkan basis data terpusat?">
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:12px; margin-bottom:14px;">
            <div>
              <label class="ak-form-label">Upload Modul (PDF/DOCX)</label>
              <input type="file" name="file_modul" class="ak-input" accept=".pdf,.doc,.docx">
            </div>
            <div>
              <label class="ak-form-label">Upload LKPD Siswa (PDF)</label>
              <input type="file" name="file_lkpd" class="ak-input" accept=".pdf,.doc,.docx">
            </div>
            <div>
              <label class="ak-form-label">Jobsheet Praktik Bengkel/Lab</label>
              <input type="file" name="file_jobsheet" class="ak-input" accept=".pdf,.doc,.docx">
            </div>
          </div>

          <div>
            <label class="ak-form-label">Link Media / Video Pembelajaran (Opsional)</label>
            <input type="url" name="link_media_pembelajaran" class="ak-input" placeholder="https://youtube.com/... atau link Google Drive">
          </div>
        </div>
        <div class="modal-footer" style="border-top:1px solid var(--ak-slate-200); padding:12px 20px;">
          <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="ak-btn ak-btn-primary">Simpan Modul Ajar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

{{-- TAB 5: KKTP (KRITERIA KETUNTASAN) --}}
@if($activeTab === 'kktp')
<div class="akademik-card">
  <div class="akademik-card-header">
    <div>
      <h3 class="akademik-card-title">
        <i class="bi bi-speedometer2 text-primary"></i>
        <span>Kriteria Ketercapaian Tujuan Pembelajaran (KKTP)</span>
      </h3>
      <div style="font-size:12px; color:var(--ak-slate-500); margin-top:2px;">
        Penetapan kriteria ketercapaian kompetensi per Tujuan Pembelajaran (TP) menggunakan pendekatan interval nilai, rubrik skala, atau deskripsi kriteria.
      </div>
    </div>
  </div>

  <div class="akademik-card-body">
    @if($perangkat->atpItems->isEmpty())
      <div style="text-align:center; padding:30px; color:#94a3b8;">
        Silakan isi Alur Tujuan Pembelajaran (ATP) pada Tab 2 terlebih dahulu sebelum menetapkan KKTP.
      </div>
    @else
      <div style="display:grid; gap:16px;">
        @foreach($perangkat->atpItems as $tp)
          @php
            $kktp = $perangkat->kktpItems->firstWhere('atp_item_id', $tp->id);
          @endphp
          <div style="border:1px solid #e2e8f0; border-radius:8px; padding:16px; background:#f8fafc;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap; gap:8px;">
              <div>
                <span class="badge" style="background:#f1f5f9; color:#1e293b; font-weight:800;">{{ $tp->kode_tp }}</span>
                <strong style="color:#0f172a; margin-left:6px; font-size:13px;">{{ $tp->materi_pokok }}</strong>
              </div>
              <span class="badge" style="background:#e0f2fe; color:#0369a1; font-weight:700;">
                Pendekatan: {{ strtoupper(str_replace('_', ' ', $kktp?->pendekatan ?? 'interval_nilai')) }}
              </span>
            </div>

            <form action="{{ route('akademik.perangkat.kktp.store', $perangkat->id) }}" method="POST">
              @csrf
              <input type="hidden" name="atp_item_id" value="{{ $tp->id }}">
              <div style="display:grid; grid-template-columns: 200px 1fr 1fr; gap:12px;">
                <div>
                  <label class="ak-form-label">Pendekatan KKTP</label>
                  <select name="pendekatan" class="ak-select">
                    <option value="interval_nilai" {{ ($kktp?->pendekatan ?? 'interval_nilai') === 'interval_nilai' ? 'selected' : '' }}>Interval Nilai (0-100)</option>
                    <option value="rubrik" {{ ($kktp?->pendekatan) === 'rubrik' ? 'selected' : '' }}>Rubrik Skala (1-4)</option>
                    <option value="deskripsi" {{ ($kktp?->pendekatan) === 'deskripsi' ? 'selected' : '' }}>Deskripsi Kriteria</option>
                  </select>
                </div>
                <div>
                  <label class="ak-form-label">Keterangan Tuntas / Mencapai</label>
                  <input type="text" name="keterangan_tuntas" class="ak-input" value="{{ $kktp?->keterangan_tuntas ?? 'Nilai >= 75: Mencapai tujuan pembelajaran (Tuntas)' }}">
                </div>
                <div>
                  <label class="ak-form-label">Tindak Lanjut Remedial</label>
                  <input type="text" name="keterangan_remedial" class="ak-input" value="{{ $kktp?->keterangan_remedial ?? 'Nilai < 75: Pendampingan tutor sebaya & penugasan ulang' }}">
                </div>
              </div>
              <div style="margin-top:10px; text-align:right;">
                <button type="submit" class="ak-btn ak-btn-secondary" style="font-size:12px; padding:4px 12px;">
                  <i class="bi bi-save"></i> Simpan KKTP TP Ini
                </button>
              </div>
            </form>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</div>
@endif

{{-- MODAL SUPERVISI & VALIDASI WAKAKUR / KEPSEK --}}
@if($isAdminOrWaka)
<div class="modal fade" id="modalSupervisi" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:12px; border:none; box-shadow:0 15px 35px rgba(0,0,0,0.15);">
      <div class="modal-header" style="border-bottom:1px solid var(--ak-slate-200); padding:16px 20px;">
        <h5 class="modal-title" style="font-size:15px; font-weight:800; color:var(--ak-dark);">
          <i class="bi bi-shield-check text-primary me-1"></i> Telaah Supervisi &amp; Pengesahan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="{{ route('akademik.perangkat.supervisi', $perangkat->id) }}" method="POST">
        @csrf
        <div class="modal-body" style="padding:20px;">
          <div style="margin-bottom:14px;">
            <label class="ak-form-label">Status Validasi <span class="text-danger">*</span></label>
            <select name="status" class="ak-select" required>
              <option value="disahkan" {{ $perangkat->status === 'disahkan' ? 'selected' : '' }}>
                ✅ Setujui &amp; Sahkan Resmi (Disahkan Kepala Sekolah)
              </option>
              <option value="perlu_revisi" {{ $perangkat->status === 'perlu_revisi' ? 'selected' : '' }}>
                ⚠️ Perlu Revisi (Kirim Catatan Perbaikan ke Guru)
              </option>
              <option value="draft" {{ $perangkat->status === 'draft' ? 'selected' : '' }}>
                📝 Kembalikan ke Draft
              </option>
            </select>
          </div>

          <div style="margin-bottom:14px;">
            <label class="ak-form-label">Catatan Telaah / Catatan Supervisi</label>
            <textarea name="catatan_supervisi" class="ak-textarea" rows="4" placeholder="Tuliskan catatan evaluasi kesesuaian alokasi waktu, model pembelajaran, atau tindak lanjut supervisi...">{{ $perangkat->catatan_supervisi }}</textarea>
          </div>

          <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:10px 14px; font-size:12px; color:#166534;">
            <i class="bi bi-info-circle me-1"></i> Jika disahkan, sistem akan otomatis menerbitkan <strong>QR Code Token Keabsahan Resmi</strong> untuk Lembar Pengesahan Perangkat Ajar.
          </div>
        </div>

        <div class="modal-footer" style="border-top:1px solid var(--ak-slate-200); padding:12px 20px;">
          <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="ak-btn ak-btn-primary">
            <i class="bi bi-check2-circle"></i>
            <span>Simpan Keputusan Supervisi</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif
@endsection
