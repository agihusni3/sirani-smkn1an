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
    $mapel = $activePerangkat->mataPelajaran;
    $hasCustomCp = !empty($activePerangkat->capaian_pembelajaran);
    $activeCpText = $activePerangkat->resolved_cp;
    $activeElemen = $activePerangkat->resolved_elemen_cp;
    $hasCustomElemen = !empty($activePerangkat->elemen_cp) && count($activePerangkat->elemen_cp) > 0;
  @endphp

  {{-- Card Header Status Guru & Aksi Input --}}
  <div class="akademik-card" style="margin-bottom:20px; padding:20px 24px; border-left:4px solid var(--ak-primary);">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px;">
      <div>
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px; flex-wrap:wrap;">
          <span class="ak-badge ak-badge-primary" style="font-size:12px; font-weight:800;">
            Kode: {{ $mapel?->kode_mapel }}
          </span>
          <span class="badge" style="background:#e0e7ff; color:#3730a3; font-size:12px; font-weight:700;">
            Kelas {{ $activePerangkat->tingkat }} · Fase {{ $activePerangkat->fase }}
          </span>
          <span class="badge" style="background:#f1f5f9; color:#475569; font-size:12px;">
            Semester {{ $activePerangkat->semester == 1 ? 'Ganjil' : 'Genap' }}
          </span>
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

        <h2 style="font-weight:900; font-size:22px; color:var(--ak-dark); margin:0 0 4px 0;">
          {{ $mapel?->nama_mapel }}
        </h2>
        <div style="font-size:12.5px; color:#64748b;">
          Guru Pengampu: <strong style="color:#1e293b;">{{ $activePerangkat->guru?->nama }}</strong>
          (NIP: {{ $activePerangkat->guru?->nip ?? '-' }})
        </div>
      </div>

      {{-- Action Buttons untuk Guru Pengampu / Admin --}}
      @if($canEdit)
        <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
          {{-- Tombol Edit CP Guru --}}
          <button type="button" class="ak-btn ak-btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditCp" style="padding:8px 16px; font-size:13px; font-weight:700;">
            <i class="bi bi-pencil-square"></i>
            <span>{{ $hasCustomCp ? 'Edit Capaian Pembelajaran' : 'Input Capaian Pembelajaran' }}</span>
          </button>

          {{-- Tombol Salin Standar Resmi --}}
          <form action="{{ route('akademik.perangkat.cp.salin-template', $activePerangkat->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Salin rumusan standar resmi BSKAP 032/2024 ke dokumen perangkat ajar Anda?')">
            @csrf
            <button type="submit" class="ak-btn ak-btn-secondary" style="padding:8px 14px; font-size:13px;" title="Salin otomatis dari acuan kurikulum resmi">
              <i class="bi bi-lightning-charge text-amber-500"></i>
              <span>Salin Template BSKAP</span>
            </button>
          </form>
        </div>
      @endif
    </div>
  </div>

  {{-- Card Utama Capaian Pembelajaran --}}
  <div style="display:grid; grid-template-columns: 1fr; gap:20px; margin-bottom:24px;">
    
    {{-- Teks CP Guru --}}
    <div class="akademik-card" style="border-top:4px solid #0284c7;">
      <div class="akademik-card-header" style="background:#f8fafc; display:flex; justify-content:space-between; align-items:center;">
        <h3 class="akademik-card-title" style="font-size:15px; display:flex; align-items:center; gap:8px;">
          <span class="badge bg-primary" style="font-size:11px;">Fase {{ $activePerangkat->fase }}</span>
          <span>Capaian Pembelajaran (Kelas {{ $activePerangkat->tingkat }} SMK)</span>
        </h3>
        @if($hasCustomCp)
          <span style="font-size:11.5px; color:#059669; font-weight:700;">
            <i class="bi bi-person-check-fill me-1"></i> Rumusan Guru
          </span>
        @endif
      </div>

      <div class="akademik-card-body" style="padding:22px 24px; font-size:13.5px; line-height:1.75; color:#1e293b;">
        @if(!empty($activeCpText))
          <div style="white-space:pre-line; background:#f8fafc; border-left:4px solid #0284c7; padding:16px 20px; border-radius:8px;">{{ $activeCpText }}</div>
        @else
          <div style="padding:30px; text-align:center; color:#94a3b8; font-style:italic;">
            <i class="bi bi-journal-x" style="font-size:32px; display:block; margin-bottom:8px; opacity:0.6;"></i>
            Capaian Pembelajaran belum diinput oleh guru untuk perangkat ini.
            @if($canEdit)
              <div style="margin-top:12px;">
                <button type="button" class="ak-btn ak-btn-primary ak-btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditCp">
                  <i class="bi bi-pencil-square me-1"></i> Input Capaian Pembelajaran Sekarang
                </button>
              </div>
            @endif
          </div>
        @endif

        @if(!empty($activePerangkat->rasional_tujuan))
          <div style="margin-top:20px; padding-top:18px; border-top:1px dashed #e2e8f0;">
            <h4 style="font-size:13px; font-weight:800; color:#334155; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">
              <i class="bi bi-bullseye text-primary me-1"></i> Rasional &amp; Tujuan Mata Pelajaran:
            </h4>
            <div style="white-space:pre-line; font-size:13px; color:#475569; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:14px 16px;">
              {{ $activePerangkat->rasional_tujuan }}
            </div>
          </div>
        @endif
      </div>
    </div>

  </div>

  {{-- Rincian Elemen CP --}}
  <div class="akademik-card" style="margin-bottom:24px;">
    <div class="akademik-card-header" style="background:#f8fafc; display:flex; justify-content:space-between; align-items:center;">
      <h3 class="akademik-card-title" style="font-size:15px;">
        <i class="bi bi-diagram-3 text-primary me-2"></i>
        Elemen Kompetensi &amp; Deskripsi Capaian per Elemen
      </h3>
      <div style="display:flex; align-items:center; gap:8px;">
        @if($hasCustomElemen)
          <span class="badge" style="background:#ecfdf5; color:#065f46; font-size:11px;">
            <i class="bi bi-check me-1"></i> {{ count($activeElemen) }} Elemen Kustom Guru
          </span>
        @endif
        @if($canEdit)
          <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditCp" style="font-size:12px;">
            <i class="bi bi-plus-circle me-1"></i> Kelola Elemen CP
          </button>
        @endif
      </div>
    </div>

    <div class="akademik-card-body" style="padding:0;">
      @if(!empty($activeElemen) && count($activeElemen) > 0)
        <div class="akademik-table-wrap">
          <table class="akademik-table">
            <thead>
              <tr>
                <th style="width:50px; text-align:center;">No</th>
                <th style="width:260px;">Nama Elemen CP</th>
                <th>Deskripsi Capaian Pembelajaran Elemen</th>
              </tr>
            </thead>
            <tbody>
              @foreach($activeElemen as $idx => $elem)
                <tr>
                  <td style="text-align:center; font-weight:800; color:#94a3b8;">{{ $loop->iteration }}</td>
                  <td style="font-weight:700; color:var(--ak-dark);">
                    <div style="font-size:13.5px;">{{ is_array($elem) ? ($elem['nama'] ?? $elem['elemen'] ?? '-') : $elem }}</div>
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
        <div style="padding:36px; text-align:center; color:#64748b;">
          <i class="bi bi-diagram-2" style="font-size:32px; color:#cbd5e1; display:block; margin-bottom:8px;"></i>
          <div style="font-weight:700; font-size:13.5px; color:#475569;">Belum Ada Rincian Elemen CP</div>
          <div style="font-size:12px; color:#94a3b8; margin-top:3px;">
            Klik tombol <strong>"Kelola Elemen CP"</strong> untuk menambahkan elemen kompetensi dan capaian tiap elemen.
          </div>
        </div>
      @endif
    </div>
  </div>

  {{-- Modal Form Input / Edit CP Guru --}}
  @if($canEdit)
  <div class="modal fade" id="modalEditCp" tabindex="-1" aria-labelledby="modalEditCpLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content" style="border-radius:12px; border:none; box-shadow:0 15px 35px rgba(0,0,0,0.15);">
        <div class="modal-header" style="border-bottom:1px solid var(--ak-slate-200); padding:16px 20px;">
          <div>
            <h5 class="modal-title" id="modalEditCpLabel" style="font-size:15px; font-weight:800; color:var(--ak-dark);">
              <i class="bi bi-pencil-square text-primary me-1"></i> Input Capaian Pembelajaran (CP) Guru
            </h5>
            <div style="font-size:11.5px; color:#64748b; margin-top:2px;">
              Mata Pelajaran: <strong>{{ $mapel?->nama_mapel }}</strong> · Kelas {{ $activePerangkat->tingkat }} (Fase {{ $activePerangkat->fase }})
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form action="{{ route('akademik.perangkat.cp.store', $activePerangkat->id) }}" method="POST">
          @csrf
          <div class="modal-body" style="padding:20px; max-height:75vh; overflow-y:auto;">

            {{-- Template loader helper --}}
            @if(!empty($templateCpText))
              <div style="display:flex; justify-content:space-between; align-items:center; background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:10px 14px; margin-bottom:16px;">
                <div style="font-size:12px; color:#1e40af;">
                  <i class="bi bi-lightning-charge-fill me-1 text-primary"></i>
                  Tersedia teks acuan resmi standar nasional SK BSKAP 032/2024.
                </div>
                <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" onclick="loadTemplateToForm()" style="font-size:11.5px; background:#fff;">
                  Muat ke Form
                </button>
              </div>
            @endif

            {{-- Capaian Pembelajaran Umum / Fase --}}
            <div style="margin-bottom:16px;">
              <label class="ak-form-label">
                Teks Capaian Pembelajaran Fase {{ $activePerangkat->fase }} (Kelas {{ $activePerangkat->tingkat }}) <span class="text-danger">*</span>
              </label>
              <textarea name="capaian_pembelajaran" id="cpTextInput" class="ak-textarea" rows="6" required 
                placeholder="Tuliskan rumusan Capaian Pembelajaran yang harus dicapai peserta didik pada mata pelajaran ini...">{{ old('capaian_pembelajaran', $activePerangkat->capaian_pembelajaran ?: $activeCpText) }}</textarea>
              <div style="font-size:11px; color:#64748b; margin-top:4px;">
                Rumusan CP ini menjadi dasar utama perumusan Alur Tujuan Pembelajaran (ATP) dan modul ajar.
              </div>
            </div>

            {{-- Rasional & Tujuan Pembelajaran --}}
            <div style="margin-bottom:20px;">
              <label class="ak-form-label">Rasional &amp; Tujuan Mata Pelajaran (Opsional)</label>
              <textarea name="rasional_tujuan" class="ak-textarea" rows="3" 
                placeholder="Latar belakang pentingnya mapel ini serta sasaran kompetensi lulusan SMK...">{{ old('rasional_tujuan', $activePerangkat->rasional_tujuan) }}</textarea>
            </div>

            {{-- Elemen-Elemen CP Repeater --}}
            <div style="border-top:1px solid #e2e8f0; padding-top:16px;">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <div>
                  <label class="ak-form-label" style="margin-bottom:2px;">Elemen Kompetensi &amp; Capaian Tiap Elemen</label>
                  <div style="font-size:11px; color:#64748b;">Tambahkan elemen kompetensi spesifik untuk memetakan Tujuan Pembelajaran.</div>
                </div>
                <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" onclick="addElemenRow()" style="font-size:12px;">
                  <i class="bi bi-plus-circle me-1"></i> + Tambah Elemen
                </button>
              </div>

              <div id="elemenContainer" style="display:flex; flex-direction:column; gap:12px;">
                @php
                  $elemenForForm = !empty($activePerangkat->elemen_cp) ? $activePerangkat->elemen_cp : $activeElemen;
                @endphp

                @if(!empty($elemenForForm) && count($elemenForForm) > 0)
                  @foreach($elemenForForm as $i => $el)
                    @php
                      $namaEl = is_array($el) ? ($el['nama'] ?? $el['elemen'] ?? '') : $el;
                      $deskripsiEl = is_array($el) ? ($el['deskripsi'] ?? $el['capaian'] ?? '') : '';
                    @endphp
                    <div class="elemen-row" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; position:relative;">
                      <div style="display:grid; grid-template-columns: 240px 1fr 36px; gap:10px; align-items:start;">
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
                    <div style="display:grid; grid-template-columns: 240px 1fr 36px; gap:10px; align-items:start;">
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

          </div>

          <div class="modal-footer" style="border-top:1px solid var(--ak-slate-200); padding:12px 20px;">
            <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="ak-btn ak-btn-primary">
              <i class="bi bi-check2-circle"></i>
              <span>Simpan Capaian Pembelajaran</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
  let elemenIndex = {{ max(1, count(!empty($activePerangkat->elemen_cp) ? $activePerangkat->elemen_cp : $activeElemen)) + 5 }};

  function addElemenRow(nama = '', deskripsi = '') {
    const container = document.getElementById('elemenContainer');
    const row = document.createElement('div');
    row.className = 'elemen-row';
    row.style.cssText = 'background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; position:relative;';
    row.innerHTML = `
      <div style="display:grid; grid-template-columns: 240px 1fr 36px; gap:10px; align-items:start;">
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
    const templateCp = @json($templateCpText);
    const templateElems = @json($templateElemenCp);

    if (templateCp) {
      document.getElementById('cpTextInput').value = templateCp;
    }

    if (templateElems && templateElems.length > 0) {
      const container = document.getElementById('elemenContainer');
      container.innerHTML = '';
      templateElems.forEach(el => {
        const nama = (typeof el === 'object' && el !== null) ? (el.nama || el.elemen || '') : el;
        const desk = (typeof el === 'object' && el !== null) ? (el.deskripsi || el.capaian || '') : '';
        addElemenRow(nama, desk);
      });
    }

    alert('Teks acuan resmi SK BSKAP berhasil dimuat ke formulir. Anda dapat mengedit sebelum menyimpan.');
  }
  </script>
  @endif

@else
  {{-- Ketika Belum Ada Dokumen Perangkat yang Aktif --}}
  <div class="akademik-card" style="padding:40px 24px; text-align:center; color:#64748b; margin-bottom:20px;">
    <i class="bi bi-journal-text" style="font-size:42px; color:#cbd5e1; display:block; margin-bottom:12px;"></i>
    <div style="font-weight:800; font-size:16px; color:var(--ak-dark); margin-bottom:6px;">Belum Ada Folder Perangkat Pembelajaran Terpilih</div>
    <div style="font-size:13px; max-width:540px; margin:0 auto 16px auto; line-height:1.6;">
      Untuk mulai menginput Capaian Pembelajaran (CP), silakan buat folder perangkat ajar untuk mata pelajaran yang Anda ampu pada Semester {{ $semester }}.
    </div>

    @if($isGuru || $isAdminOrWaka)
      <a href="{{ route('akademik.perangkat.index') }}" class="ak-btn ak-btn-primary" style="display:inline-flex; align-items:center; gap:8px; padding:9px 18px; font-size:13px;">
        <i class="bi bi-plus-circle"></i>
        <span>Buat Folder Perangkat Ajar &amp; Input CP</span>
      </a>
    @endif
  </div>

  @if($selectedMapel)
    {{-- Tampilkan acuan master mapel sebagai preview referensi --}}
    <div class="akademik-card">
      <div class="akademik-card-header" style="background:#f8fafc;">
        <h3 class="akademik-card-title" style="font-size:14.5px;">
          <i class="bi bi-book text-primary me-2"></i>
          Referensi Acuan Capaian Pembelajaran: {{ $selectedMapel->nama_mapel }}
        </h3>
      </div>
      <div class="akademik-card-body" style="padding:20px; font-size:13px; line-height:1.7; color:#334155;">
        <div style="margin-bottom:12px;">
          <strong class="badge bg-primary me-2">Fase E (Kelas X)</strong>
          <div>{{ $selectedMapel->capaian_pembelajaran_fase_e ?: ($selectedMapel->deskripsi_cp ?: 'Belum ada data acuan master.') }}</div>
        </div>
        <div style="border-top:1px dashed #e2e8f0; padding-top:12px;">
          <strong class="badge bg-purple me-2" style="background:#7c3aed; color:#fff;">Fase F (Kelas XI - XII)</strong>
          <div>{{ $selectedMapel->capaian_pembelajaran_fase_f ?: 'Belum ada data acuan master.' }}</div>
        </div>
      </div>
    </div>
  @endif

@endif

@endsection
