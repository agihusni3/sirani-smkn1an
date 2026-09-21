@extends('dcc.akademik.layout')

@section('title', 'Capaian Pembelajaran (CP) — Perangkat Pembelajaran')
@section('breadcrumb')
  <a href="{{ route('akademik.perangkat.cp') }}">Perangkat Pembelajaran</a>
  <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
  <span>Capaian Pembelajaran (CP)</span>
@endsection

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">
      <i class="bi bi-bookmark-star text-primary me-2"></i>
      1. Capaian Pembelajaran (CP)
    </h1>
    <div class="akademik-page-desc">
      Pengelolaan Capaian Pembelajaran (CP) dan elemen kompetensi mata pelajaran Kurikulum Merdeka oleh Guru Pengampu.
    </div>
  </div>
</div>

{{-- Pemilih Semester & Perangkat Mapel --}}
@include('dcc.akademik.perangkat.partials.selector', ['showMapelSelector' => true])

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:10px; margin-bottom:18px;">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:10px; margin-bottom:18px;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

@if($activePerangkat)
  @php
    $mapel        = $activePerangkat->mataPelajaran;
    $hasCustomCp  = !empty($activePerangkat->capaian_pembelajaran);
    $activeCpText = $activePerangkat->resolved_cp;
    $activeElemen = $activePerangkat->resolved_elemen_cp;
    $hasCustomElemen = !empty($activePerangkat->elemen_cp) && count($activePerangkat->elemen_cp) > 0;
    $elemenForForm = !empty($activePerangkat->elemen_cp) ? $activePerangkat->elemen_cp : $activeElemen;
    $showForm = $canEdit;
  @endphp

  {{-- =============================== LAYOUT 2 KOLOM =============================== --}}
  <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; align-items:start;">

    {{-- ===== KOLOM KIRI: TAMPILAN (PREVIEW) ===== --}}
    <div>

      {{-- Identitas Perangkat --}}
      <div class="akademik-card" style="margin-bottom:18px; padding:16px 20px; border-left:4px solid var(--ak-primary);">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:8px;">
          <span class="ak-badge ak-badge-primary" style="font-size:12px; font-weight:800;">{{ $mapel?->kode_mapel }}</span>
          <span class="badge" style="background:#e0e7ff; color:#3730a3; font-size:12px; font-weight:700;">Kelas {{ $activePerangkat->tingkat }} · Fase {{ $activePerangkat->fase }}</span>
          <span class="badge" style="background:#f1f5f9; color:#475569; font-size:12px;">Semester {{ $activePerangkat->semester == 1 ? 'Ganjil' : 'Genap' }}</span>
          @if($hasCustomCp)
            <span class="badge" style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; font-size:11.5px; font-weight:700;">
              <i class="bi bi-check-circle-fill me-1"></i> Telah Diinput Guru
            </span>
          @else
            <span class="badge" style="background:#fffbeb; color:#92400e; border:1px solid #fde68a; font-size:11.5px; font-weight:700;">
              <i class="bi bi-info-circle me-1"></i> Referensi Standar Nasional
            </span>
          @endif
        </div>
        <h2 style="font-weight:900; font-size:20px; color:var(--ak-dark); margin:0 0 4px 0;">{{ $mapel?->nama_mapel }}</h2>
        <div style="font-size:12px; color:#64748b;">
          Guru Pengampu: <strong style="color:#1e293b;">{{ $activePerangkat->guru?->nama }}</strong>
          (NIP: {{ $activePerangkat->guru?->nip ?? '-' }})
        </div>
      </div>

      {{-- Preview CP Aktif --}}
      <div class="akademik-card" style="margin-bottom:18px; border-top:4px solid #0284c7;">
        <div class="akademik-card-header" style="background:#f8fafc; display:flex; justify-content:space-between; align-items:center;">
          <h3 class="akademik-card-title" style="font-size:14.5px; display:flex; align-items:center; gap:8px;">
            <span class="badge bg-primary" style="font-size:11px;">Fase {{ $activePerangkat->fase }}</span>
            <span>Capaian Pembelajaran Aktif</span>
          </h3>
          @if($hasCustomCp)
            <span style="font-size:11px; color:#059669; font-weight:700;"><i class="bi bi-person-check-fill me-1"></i>Rumusan Guru</span>
          @endif
        </div>
        <div class="akademik-card-body" style="padding:18px 20px; font-size:13px; line-height:1.75; color:#1e293b;">
          @if(!empty($activeCpText))
            <div style="white-space:pre-line; background:#f8fafc; border-left:4px solid #0284c7; padding:14px 18px; border-radius:8px;">{{ $activeCpText }}</div>
          @else
            <div style="padding:24px; text-align:center; color:#94a3b8; font-style:italic;">
              <i class="bi bi-journal-x" style="font-size:28px; display:block; margin-bottom:8px; opacity:0.6;"></i>
              Belum ada teks CP — isi form di sebelah kanan.
            </div>
          @endif

          @if(!empty($activePerangkat->rasional_tujuan))
            <div style="margin-top:18px; padding-top:16px; border-top:1px dashed #e2e8f0;">
              <h4 style="font-size:12.5px; font-weight:800; color:#334155; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">
                <i class="bi bi-bullseye text-primary me-1"></i> Rasional & Tujuan Mata Pelajaran:
              </h4>
              <div style="white-space:pre-line; font-size:12.5px; color:#475569; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:12px 14px;">
                {{ $activePerangkat->rasional_tujuan }}
              </div>
            </div>
          @endif
        </div>
      </div>

      {{-- Elemen Kompetensi (Preview) --}}
      <div class="akademik-card">
        <div class="akademik-card-header" style="background:#f8fafc; display:flex; justify-content:space-between; align-items:center;">
          <h3 class="akademik-card-title" style="font-size:14.5px;">
            <i class="bi bi-diagram-3 text-primary me-2"></i>Elemen Kompetensi
          </h3>
          @if($hasCustomElemen)
            <span class="badge" style="background:#ecfdf5; color:#065f46; font-size:11px;">
              <i class="bi bi-check me-1"></i> {{ count($activeElemen) }} Elemen
            </span>
          @endif
        </div>
        <div class="akademik-card-body" style="padding:0;">
          @if(!empty($activeElemen) && count($activeElemen) > 0)
            <div class="akademik-table-wrap">
              <table class="akademik-table">
                <thead>
                  <tr>
                    <th style="width:40px; text-align:center;">No</th>
                    <th style="width:200px;">Nama Elemen</th>
                    <th>Deskripsi Capaian</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($activeElemen as $idx => $elem)
                    <tr>
                      <td style="text-align:center; font-weight:800; color:#94a3b8;">{{ $loop->iteration }}</td>
                      <td style="font-weight:700; color:var(--ak-dark); font-size:13px;">
                        {{ is_array($elem) ? ($elem['nama'] ?? $elem['elemen'] ?? '-') : $elem }}
                      </td>
                      <td style="font-size:12.5px; line-height:1.6; color:#334155;">
                        {{ is_array($elem) ? ($elem['deskripsi'] ?? $elem['capaian'] ?? '-') : '-' }}
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div style="padding:28px; text-align:center; color:#64748b;">
              <i class="bi bi-diagram-2" style="font-size:28px; color:#cbd5e1; display:block; margin-bottom:8px;"></i>
              <div style="font-weight:700; font-size:13px; color:#475569;">Belum Ada Elemen CP</div>
              <div style="font-size:12px; color:#94a3b8; margin-top:3px;">Tambahkan elemen di form sebelah kanan.</div>
            </div>
          @endif
        </div>
      </div>

    </div>{{-- /KOLOM KIRI --}}

    {{-- ===== KOLOM KANAN: FORM INPUT / EDIT ===== --}}
    @if($canEdit)
    <div>
      <form action="{{ route('akademik.perangkat.cp.store', $activePerangkat->id) }}" method="POST" id="formInputCp">
        @csrf
        <div class="akademik-card" style="border-top:4px solid #7c3aed; margin-bottom:0;">
          <div class="akademik-card-header" style="background:linear-gradient(135deg,#ede9fe,#f5f3ff); border-bottom:1px solid #ddd6fe; padding:14px 20px;">
            <h3 class="akademik-card-title" style="font-size:15px; font-weight:800; color:#4c1d95; display:flex; align-items:center; gap:8px; margin:0;">
              <i class="bi bi-pencil-square"></i>
              {{ $hasCustomCp ? 'Edit Capaian Pembelajaran' : 'Input Capaian Pembelajaran' }}
            </h3>
            <div style="font-size:11.5px; color:#6d28d9; margin-top:3px;">
              {{ $mapel?->nama_mapel }} · Kelas {{ $activePerangkat->tingkat }} (Fase {{ $activePerangkat->fase }})
            </div>
          </div>

          <div class="akademik-card-body" style="padding:20px;">

            {{-- Notif Template Tersedia --}}
            @if(!empty($templateCpText))
              <div style="display:flex; justify-content:space-between; align-items:center; background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:10px 14px; margin-bottom:16px;">
                <div style="font-size:12px; color:#1e40af;">
                  <i class="bi bi-lightning-charge-fill me-1 text-primary"></i>
                  Tersedia teks acuan resmi SK BSKAP 032/2024.
                </div>
                <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" onclick="loadTemplateToForm()" style="font-size:11.5px; background:#fff;">
                  Muat ke Form
                </button>
              </div>
            @endif

            {{-- Salin Template BSKAP --}}
            <div style="margin-bottom:16px; display:flex; justify-content:flex-end;">
              <form action="{{ route('akademik.perangkat.cp.salin-template', $activePerangkat->id) }}" method="POST" style="display:inline;"
                    onsubmit="return confirm('Salin rumusan standar resmi BSKAP 032/2024 ke dokumen perangkat ajar Anda?')">
                @csrf
                <button type="submit" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:12px;">
                  <i class="bi bi-lightning-charge me-1"></i> Salin Template BSKAP Resmi
                </button>
              </form>
            </div>

            {{-- Teks CP Utama --}}
            <div style="margin-bottom:18px;">
              <label class="ak-form-label">
                Teks Capaian Pembelajaran Fase {{ $activePerangkat->fase }} (Kelas {{ $activePerangkat->tingkat }})
                <span class="text-danger">*</span>
              </label>
              <textarea name="capaian_pembelajaran" id="cpTextInput" class="ak-textarea" rows="7" required
                placeholder="Tuliskan rumusan Capaian Pembelajaran yang harus dicapai peserta didik...">{{ old('capaian_pembelajaran', $activePerangkat->capaian_pembelajaran ?: $activeCpText) }}</textarea>
              <div style="font-size:11px; color:#64748b; margin-top:4px;">
                Rumusan CP ini menjadi dasar perumusan Alur Tujuan Pembelajaran (ATP) dan modul ajar.
              </div>
            </div>

            {{-- Rasional & Tujuan --}}
            <div style="margin-bottom:20px;">
              <label class="ak-form-label">Rasional &amp; Tujuan Mata Pelajaran <span style="color:#64748b; font-weight:400;">(Opsional)</span></label>
              <textarea name="rasional_tujuan" class="ak-textarea" rows="3"
                placeholder="Latar belakang pentingnya mapel ini serta sasaran kompetensi lulusan SMK...">{{ old('rasional_tujuan', $activePerangkat->rasional_tujuan) }}</textarea>
            </div>

            {{-- Elemen CP Repeater --}}
            <div style="border-top:1px solid #e2e8f0; padding-top:18px;">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                <div>
                  <label class="ak-form-label" style="margin-bottom:2px;">Elemen Kompetensi &amp; Capaian per Elemen</label>
                  <div style="font-size:11px; color:#64748b;">Tambahkan elemen kompetensi spesifik untuk memetakan Tujuan Pembelajaran.</div>
                </div>
                <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" onclick="addElemenRow()" style="font-size:12px; white-space:nowrap;">
                  <i class="bi bi-plus-circle me-1"></i> + Tambah Elemen
                </button>
              </div>

              <div id="elemenContainer" style="display:flex; flex-direction:column; gap:10px;">
                @if(!empty($elemenForForm) && count($elemenForForm) > 0)
                  @foreach($elemenForForm as $i => $el)
                    @php
                      $namaEl     = is_array($el) ? ($el['nama'] ?? $el['elemen'] ?? '') : $el;
                      $deskripsiEl = is_array($el) ? ($el['deskripsi'] ?? $el['capaian'] ?? '') : '';
                    @endphp
                    <div class="elemen-row" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; position:relative;">
                      <div style="display:grid; grid-template-columns: 200px 1fr 34px; gap:8px; align-items:start;">
                        <div>
                          <label style="font-size:10.5px; font-weight:700; color:#475569; display:block; margin-bottom:3px;">Nama Elemen:</label>
                          <input type="text" name="elemen_cp[{{ $i }}][nama]" class="ak-input" value="{{ $namaEl }}" placeholder="Contoh: Berpikir Komputasional" style="font-size:12px;">
                        </div>
                        <div>
                          <label style="font-size:10.5px; font-weight:700; color:#475569; display:block; margin-bottom:3px;">Deskripsi Capaian Elemen:</label>
                          <textarea name="elemen_cp[{{ $i }}][deskripsi]" class="ak-textarea" rows="2" placeholder="Peserta didik mampu..." style="font-size:12px;">{{ $deskripsiEl }}</textarea>
                        </div>
                        <div style="padding-top:20px; text-align:center;">
                          <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeElemenRow(this)" style="padding:4px 8px;" title="Hapus Elemen">
                            <i class="bi bi-trash"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  @endforeach
                @else
                  <div class="elemen-row" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; position:relative;">
                    <div style="display:grid; grid-template-columns: 200px 1fr 34px; gap:8px; align-items:start;">
                      <div>
                        <label style="font-size:10.5px; font-weight:700; color:#475569; display:block; margin-bottom:3px;">Nama Elemen:</label>
                        <input type="text" name="elemen_cp[0][nama]" class="ak-input" placeholder="Contoh: Elemen 1" style="font-size:12px;">
                      </div>
                      <div>
                        <label style="font-size:10.5px; font-weight:700; color:#475569; display:block; margin-bottom:3px;">Deskripsi Capaian Elemen:</label>
                        <textarea name="elemen_cp[0][deskripsi]" class="ak-textarea" rows="2" placeholder="Peserta didik mampu..." style="font-size:12px;"></textarea>
                      </div>
                      <div style="padding-top:20px; text-align:center;">
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeElemenRow(this)" style="padding:4px 8px;" title="Hapus Elemen">
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                @endif
              </div>
            </div>

          </div>{{-- /card-body --}}

          {{-- Footer Tombol Simpan --}}
          <div style="border-top:1px solid #e2e8f0; padding:14px 20px; background:#fafafa; border-radius:0 0 12px 12px; display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('akademik.perangkat.cp') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" class="ak-btn ak-btn-secondary">
              <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
            </a>
            <button type="submit" class="ak-btn ak-btn-primary" style="background:linear-gradient(135deg,#7c3aed,#4f46e5); border:none; padding:10px 24px; font-weight:700; font-size:13.5px;">
              <i class="bi bi-check2-circle me-1"></i> Simpan Capaian Pembelajaran
            </button>
          </div>

        </div>{{-- /akademik-card --}}
      </form>
    </div>
    @else
      {{-- Jika hanya view saja (bukan guru pemilik) --}}
      <div>
        <div class="akademik-card" style="padding:36px; text-align:center; color:#64748b; border-top:4px solid #e2e8f0;">
          <i class="bi bi-shield-lock" style="font-size:36px; color:#cbd5e1; display:block; margin-bottom:12px;"></i>
          <div style="font-weight:700; font-size:14px; color:#475569; margin-bottom:6px;">Hanya Dapat Dilihat</div>
          <div style="font-size:12.5px; color:#94a3b8;">Form input CP hanya tersedia bagi guru pengampu dan admin kurikulum.</div>
        </div>
      </div>
    @endif
    {{-- /KOLOM KANAN --}}

  </div>{{-- /grid --}}

@else
  {{-- Belum Ada Dokumen Perangkat Aktif --}}
  <div class="akademik-card" style="padding:40px 24px; text-align:center; color:#64748b; margin-bottom:20px;">
    <i class="bi bi-journal-text" style="font-size:42px; color:#cbd5e1; display:block; margin-bottom:12px;"></i>
    <div style="font-weight:800; font-size:16px; color:var(--ak-dark); margin-bottom:6px;">Belum Ada Folder Perangkat Pembelajaran Terpilih</div>
    <div style="font-size:13px; max-width:540px; margin:0 auto 16px auto; line-height:1.6;">
      Untuk mulai menginput Capaian Pembelajaran (CP), silakan buat folder perangkat ajar untuk mata pelajaran yang Anda ampu pada Semester {{ $semester }}.
    </div>
    @if($isGuru || $isAdminOrWaka)
      <a href="{{ route('akademik.perangkat.index') }}" class="ak-btn ak-btn-primary" style="display:inline-flex; align-items:center; gap:8px; padding:9px 18px; font-size:13px;">
        <i class="bi bi-plus-circle"></i>
        <span>Buat Folder Perangkat Ajar</span>
      </a>
    @endif
  </div>

  @if($selectedMapel)
    <div class="akademik-card">
      <div class="akademik-card-header" style="background:#f8fafc;">
        <h3 class="akademik-card-title" style="font-size:14.5px;">
          <i class="bi bi-book text-primary me-2"></i>
          Referensi CP: {{ $selectedMapel->nama_mapel }}
        </h3>
      </div>
      <div class="akademik-card-body" style="padding:20px; font-size:13px; line-height:1.7; color:#334155;">
        <div style="margin-bottom:12px;">
          <strong class="badge bg-primary me-2">Fase E (Kelas X)</strong>
          <div>{{ $selectedMapel->capaian_pembelajaran_fase_e ?: ($selectedMapel->deskripsi_cp ?: 'Belum ada data acuan master.') }}</div>
        </div>
        <div style="border-top:1px dashed #e2e8f0; padding-top:12px;">
          <strong class="badge me-2" style="background:#7c3aed; color:#fff;">Fase F (Kelas XI - XII)</strong>
          <div>{{ $selectedMapel->capaian_pembelajaran_fase_f ?: 'Belum ada data acuan master.' }}</div>
        </div>
      </div>
    </div>
  @endif
@endif

@endsection

@push('scripts')
<script>
let elemenIndex = {{ !empty($activePerangkat) && !empty($elemenForForm ?? null) ? max(1, count($elemenForForm ?? [])) + 5 : 6 }};

function addElemenRow(nama = '', deskripsi = '') {
  const container = document.getElementById('elemenContainer');
  if (!container) return;
  const row = document.createElement('div');
  row.className = 'elemen-row';
  row.style.cssText = 'background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; position:relative;';
  row.innerHTML = `
    <div style="display:grid; grid-template-columns: 200px 1fr 34px; gap:8px; align-items:start;">
      <div>
        <label style="font-size:10.5px; font-weight:700; color:#475569; display:block; margin-bottom:3px;">Nama Elemen:</label>
        <input type="text" name="elemen_cp[${elemenIndex}][nama]" class="ak-input" value="${nama}" placeholder="Contoh: Elemen ${elemenIndex + 1}" style="font-size:12px;">
      </div>
      <div>
        <label style="font-size:10.5px; font-weight:700; color:#475569; display:block; margin-bottom:3px;">Deskripsi Capaian Elemen:</label>
        <textarea name="elemen_cp[${elemenIndex}][deskripsi]" class="ak-textarea" rows="2" placeholder="Peserta didik mampu..." style="font-size:12px;">${deskripsi}</textarea>
      </div>
      <div style="padding-top:20px; text-align:center;">
        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeElemenRow(this)" style="padding:4px 8px;" title="Hapus Elemen">
          <i class="bi bi-trash"></i>
        </button>
      </div>
    </div>
  `;
  container.appendChild(row);
  elemenIndex++;
}

function removeElemenRow(btn) {
  const rows = document.querySelectorAll('.elemen-row');
  if (rows.length > 1) {
    btn.closest('.elemen-row').remove();
  } else {
    const inputs = btn.closest('.elemen-row').querySelectorAll('input, textarea');
    inputs.forEach(i => i.value = '');
  }
}

function loadTemplateToForm() {
  const templateCp    = @json($templateCpText ?? '');
  const templateElems = @json($templateElemenCp ?? []);

  if (templateCp) {
    const el = document.getElementById('cpTextInput');
    if (el) el.value = templateCp;
  }

  if (templateElems && templateElems.length > 0) {
    const container = document.getElementById('elemenContainer');
    if (container) {
      container.innerHTML = '';
      templateElems.forEach(el => {
        const nama = (typeof el === 'object' && el !== null) ? (el.nama || el.elemen || '') : el;
        const desk = (typeof el === 'object' && el !== null) ? (el.deskripsi || el.capaian || '') : '';
        addElemenRow(nama, desk);
      });
    }
  }

  alert('Teks acuan resmi SK BSKAP berhasil dimuat ke formulir. Anda dapat mengedit sebelum menyimpan.');
}
</script>
@endpush
