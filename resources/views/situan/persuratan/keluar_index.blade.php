@extends('layouts.app')

@section('title', 'Buku Agenda Surat Keluar — SITUAN SMKN 1 AN')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">

  {{-- Page Header --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
      <div class="d-flex align-items-center gap-2 mb-1">
        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill" style="font-size:11px; font-weight:700;">
          <i class="bi bi-send-fill me-1"></i> Tata Naskah Dinas Keluar
        </span>
        <span class="text-muted" style="font-size:12px;">Tahun {{ $thisYear }}</span>
      </div>
      <h1 class="h3 mb-0 fw-bold" style="color:var(--text); letter-spacing:-0.02em;">Buku Agenda Surat Keluar &amp; No. Otomatis</h1>
      <p class="text-muted mb-0 small">Generator penomoran surat dinas otomatis berstandar Kemendikdasmen (anti-nomor ganda) dan buku arsip surat keluar.</p>
    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('situan.index') }}" class="btn btn-outline-secondary btn-sm" style="font-weight:600;">
        <i class="bi bi-arrow-left me-1"></i> Dasbor SITUAN
      </a>
      <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTerbitkanNomorSurat" style="font-weight:700;">
        <i class="bi bi-plus-circle-fill me-1"></i> Ambil Nomor Surat Keluar
      </button>
    </div>
  </div>

  {{-- Top Highlight Card: Standar Format Nomor Surat --}}
  <div class="card border-0 shadow-sm rounded-3 mb-4 p-3" style="background:linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color:#fff;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
      <div>
        <div class="text-info fw-bold small text-uppercase" style="letter-spacing:0.05em;"><i class="bi bi-shield-check me-1"></i> Standar Format Baku Penomoran Dinas Pendidikan:</div>
        <div class="h4 mb-1 fw-bold font-monospace" style="color:#f8fafc;">
          [No. Urut] / [Kode Klasifikasi] / SMKN1AN / [Bulan Romawi] / [Tahun]
        </div>
        <div class="text-secondary small">
          Contoh nomor surat berikutnya untuk pembinaan SMK: <strong class="text-warning font-monospace">{{ $previewNomor['nomor_surat_lengkap'] }}</strong>
        </div>
      </div>
      <div class="text-end">
        <span class="badge bg-primary px-3 py-2 fs-6 fw-bold">
          Total Terbit: {{ $totalKeluarTahunIni }} Surat
        </span>
      </div>
    </div>
  </div>

  {{-- Filters & Search --}}
  <div class="card border-0 shadow-sm rounded-3 mb-4" style="background:var(--surface); border:1px solid var(--border)!important;">
    <div class="card-body p-3">
      <form method="GET" action="{{ route('situan.surat-keluar.index') }}" class="row g-2">
        <div class="col-md-6">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-start-0" placeholder="Cari nomor surat lengkap, tujuan, perihal, atau kode..." />
          </div>
        </div>
        <div class="col-md-4">
          <select name="klasifikasi" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
            <option value="">-- Semua Kode Klasifikasi Dinas --</option>
            @foreach($klasifikasis as $k)
              <option value="{{ $k->kode }}" {{ request('klasifikasi') === $k->kode ? 'selected' : '' }}>
                {{ $k->kode }} — {{ $k->nama }} ({{ $k->kategori }})
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
          <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">Filter</button>
          @if(request()->hasAny(['q', 'klasifikasi']))
            <a href="{{ route('situan.surat-keluar.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter"><i class="bi bi-x-lg"></i></a>
          @endif
        </div>
      </form>
    </div>
  </div>

  {{-- Agenda Surat Keluar Table --}}
  <div class="situan-table-card shadow-sm border rounded-4 overflow-hidden mb-4" style="background:var(--surface); border:1px solid var(--border)!important;">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
        <thead style="background:#f8fafc; border-bottom:1.5px solid var(--border);">
          <tr>
            <th class="py-3 px-3 text-center" style="width:85px; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; color:#475569;">No. Agenda</th>
            <th class="py-3 px-3" style="width:235px; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; color:#475569;">Nomor Surat Resmi</th>
            <th class="py-3 px-3" style="width:130px; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; color:#475569;">Tanggal Terbit</th>
            <th class="py-3 px-3" style="width:200px; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; color:#475569;">Tujuan Surat</th>
            <th class="py-3 px-3" style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; color:#475569;">Perihal &amp; Klasifikasi</th>
            <th class="py-3 px-3 text-center" style="width:150px; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; color:#475569;">Aksi &amp; Dokumen</th>
          </tr>
        </thead>
        <tbody>
          @forelse($suratKeluars as $item)
            <tr>
              <td class="text-center px-3">
                <span class="situan-agenda-badge">
                  #{{ str_pad((string)$item->nomor_agenda, 3, '0', STR_PAD_LEFT) }}
                </span>
                @if($item->is_nomor_manual)
                  <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-1 py-0 d-block mt-1" style="font-size:9.5px;" title="Nomor surat disesuaikan/disisip secara manual">
                    <i class="bi bi-pencil-square"></i> Manual
                  </span>
                @endif
              </td>
              <td class="px-3">
                <div>
                  <span class="situan-nomor-badge">
                    {{ $item->nomor_surat_lengkap }}
                  </span>
                </div>
                <div class="text-muted d-flex align-items-center gap-1 mt-1" style="font-size:11px;">
                  <i class="bi bi-pen text-secondary" style="font-size:10px;"></i>
                  <span>Penandatangan: <strong>{{ $item->penandatangan }}</strong></span>
                </div>
              </td>
              <td class="px-3">
                <div class="fw-bold" style="color:var(--text); font-size:12.5px;">{{ $item->tanggal_surat->translatedFormat('d M Y') }}</div>
                <div class="text-muted" style="font-size:10.5px;">{{ $item->tanggal_surat->diffForHumans() }}</div>
              </td>
              <td class="px-3">
                <div class="fw-bold" style="color:var(--text); line-height:1.4;">{{ $item->tujuan_surat }}</div>
              </td>
              <td class="px-3">
                <div class="fw-semibold mb-1" style="color:var(--text); line-height:1.45;">{{ $item->perihal }}</div>
                <div class="d-flex flex-wrap gap-1 align-items-center">
                  <span class="situan-tag-badge kode">
                    <i class="bi bi-tag-fill"></i>Kode: {{ $item->kode_klasifikasi }}
                  </span>

                  @if($item->sumber_modul === 'sirani_kesiswaan')
                    <span class="situan-tag-badge kesiswaan">
                      <i class="bi bi-shield-exclamation"></i>SIRANI Kesiswaan
                    </span>
                  @elseif($item->sumber_modul === 'sirani_bk')
                    <span class="situan-tag-badge bk">
                      <i class="bi bi-chat-heart-fill"></i>SIRANI BK
                    </span>
                  @elseif($item->sumber_modul === 'sirani_disiplin')
                    <span class="situan-tag-badge disiplin">
                      <i class="bi bi-journal-check"></i>SIRANI Disiplin
                    </span>
                  @elseif($item->sumber_modul === 'situan_kepegawaian')
                    <span class="situan-tag-badge kepegawaian">
                      <i class="bi bi-briefcase-fill"></i>SITUAN Kepegawaian
                    </span>
                  @elseif($item->sumber_modul === 'situan_pelayanan')
                    <span class="situan-tag-badge pelayanan">
                      <i class="bi bi-qr-code"></i>SITUAN Pelayanan
                    </span>
                  @else
                    <span class="situan-tag-badge tu">
                      <i class="bi bi-building"></i>SITUAN TU
                    </span>
                  @endif

                  @if($item->kategori_surat)
                    <span class="situan-tag-badge tu">
                      {{ $item->kategori_surat }}
                    </span>
                  @elseif($item->jenis_surat === 'suket_siswa')
                    <span class="situan-tag-badge suket">
                      <i class="bi bi-person-check-fill"></i>Suket Siswa
                    </span>
                  @elseif($item->jenis_surat === 'sk_kepsek')
                    <span class="situan-tag-badge sk">
                      <i class="bi bi-file-earmark-ruled"></i>SK Kepsek
                    </span>
                  @endif
                </div>
              </td>
              <td class="text-center px-3" style="white-space:nowrap;">
                @php
                  $printUrl = $item->link_cetak;
                  if (!$printUrl) {
                    if ($item->pelayanans && $item->pelayanans->isNotEmpty()) {
                      $printUrl = route('situan.pelayanan.cetak', $item->pelayanans->first()->id);
                    } elseif (!empty($item->isi_surat)) {
                      $printUrl = route('situan.surat-keluar.cetak', $item->id);
                    }
                  }
                @endphp
                <div class="situan-action-group">
                  @if($printUrl)
                    <a href="{{ $printUrl }}" target="_blank" class="situan-action-icon-btn btn-print" title="Buka Lembar Cetak Dokumen A4 Resmi">
                      <i class="bi bi-printer-fill"></i>
                    </a>
                  @endif

                  @if($item->file_arsip)
                    <a href="{{ asset('storage/' . $item->file_arsip) }}" target="_blank" class="situan-action-icon-btn btn-pdf" title="Buka Berkas Scan PDF">
                      <i class="bi bi-file-earmark-pdf-fill"></i>
                    </a>
                  @endif

                  {{-- Tombol Edit Surat --}}
                  <button type="button" class="situan-action-icon-btn btn-edit" data-bs-toggle="modal" data-bs-target="#modalEditSurat_{{ $item->id }}" title="Edit / Sesuaikan Nomor &amp; Data Surat">
                    <i class="bi bi-pencil-square"></i>
                  </button>

                  {{-- Tombol Hapus Surat --}}
                  <button type="button" class="situan-action-icon-btn btn-delete" onclick="if(confirm('Yakin ingin menghapus nomor surat keluar ini?')) { document.getElementById('formHapusSurat_{{ $item->id }}').submit(); }" title="Hapus dari Buku Agenda">
                    <i class="bi bi-trash-fill"></i>
                  </button>
                </div>

                <form id="formHapusSurat_{{ $item->id }}" action="{{ route('situan.surat-keluar.destroy', $item->id) }}" method="POST" class="d-none">
                  @csrf
                  @method('DELETE')
                </form>
              </td>
            </tr>

            {{-- Modal Edit Surat Keluar --}}
            <div class="modal fade" id="modalEditSurat_{{ $item->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                  <form method="POST" action="{{ route('situan.surat-keluar.update', $item->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-warning text-dark px-4 py-3">
                      <h5 class="modal-title fw-bold fs-6">
                        <i class="bi bi-pencil-square me-1"></i> Edit &amp; Sesuaikan Surat Keluar #{{ $item->nomor_agenda }}
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                      <div class="row g-3">
                        <div class="col-md-8">
                          <label class="form-label fw-bold small">Nomor Surat Lengkap: <span class="text-danger">*</span></label>
                          <input type="text" name="nomor_surat_lengkap" value="{{ $item->nomor_surat_lengkap }}" class="form-control form-control-sm font-monospace fw-bold" required />
                          <small class="text-muted" style="font-size:10.5px;">Bisa disesuaikan manual (misal format dinas khusus atau nomor sisipan).</small>
                        </div>
                        <div class="col-md-4">
                          <label class="form-label fw-bold small">No. Urut Agenda: <span class="text-danger">*</span></label>
                          <input type="number" name="nomor_agenda" value="{{ $item->nomor_agenda }}" class="form-control form-control-sm font-monospace" min="1" required />
                        </div>
                        <div class="col-md-6">
                          <label class="form-label fw-bold small">Kode Klasifikasi Dinas: <span class="text-danger">*</span></label>
                          <select name="kode_klasifikasi" class="form-select form-select-sm" required>
                            @foreach($klasifikasis as $k)
                              <option value="{{ $k->kode }}" {{ $item->kode_klasifikasi === $k->kode ? 'selected' : '' }}>
                                {{ $k->kode }} — {{ $k->nama }}
                              </option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label fw-bold small">Tanggal Surat: <span class="text-danger">*</span></label>
                          <input type="date" name="tanggal_surat" value="{{ $item->tanggal_surat->format('Y-m-d') }}" class="form-control form-control-sm" required />
                        </div>
                        <div class="col-md-8">
                          <label class="form-label fw-bold small">Tujuan Surat (Kepada Yth): <span class="text-danger">*</span></label>
                          <input type="text" name="tujuan_surat" value="{{ $item->tujuan_surat }}" class="form-control form-control-sm" required />
                        </div>
                        <div class="col-md-4">
                          <label class="form-label fw-bold small">Penandatangan:</label>
                          <input type="text" name="penandatangan" value="{{ $item->penandatangan }}" class="form-control form-control-sm" required />
                        </div>
                        <div class="col-md-6">
                          <label class="form-label fw-bold small">Jabatan Penandatangan:</label>
                          <input type="text" name="jabatan_penandatangan" value="{{ $item->jabatan_penandatangan ?: 'Kepala Sekolah' }}" class="form-control form-control-sm" />
                        </div>
                        <div class="col-md-6">
                          <label class="form-label fw-bold small">NIP Penandatangan:</label>
                          <input type="text" name="nip_penandatangan" value="{{ $item->nip_penandatangan }}" class="form-control form-control-sm" placeholder="Opsional jika ada NIP" />
                        </div>
                        <div class="col-12">
                          <label class="form-label fw-bold small">Perihal Surat: <span class="text-danger">*</span></label>
                          <textarea name="perihal" class="form-control form-control-sm" rows="2" required>{{ $item->perihal }}</textarea>
                        </div>
                        <div class="col-md-4">
                          <label class="form-label fw-bold small">Sifat Surat:</label>
                          <select name="sifat_surat" class="form-select form-select-sm">
                            <option value="Biasa" {{ $item->sifat_surat === 'Biasa' ? 'selected' : '' }}>Biasa</option>
                            <option value="Penting" {{ $item->sifat_surat === 'Penting' ? 'selected' : '' }}>Penting</option>
                            <option value="Segera" {{ $item->sifat_surat === 'Segera' ? 'selected' : '' }}>Segera</option>
                            <option value="Rahasia" {{ $item->sifat_surat === 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
                          </select>
                        </div>
                        <div class="col-md-4">
                          <label class="form-label fw-bold small">Lampiran:</label>
                          <input type="text" name="lampiran" value="{{ $item->lampiran ?: '-' }}" class="form-control form-control-sm" />
                        </div>
                        <div class="col-md-4">
                          <label class="form-label fw-bold small">Kategori / Jenis:</label>
                          <select name="jenis_surat" class="form-select form-select-sm">
                            <option value="umum" {{ $item->jenis_surat === 'umum' ? 'selected' : '' }}>Umum / Dinas</option>
                            <option value="surat_tugas" {{ $item->jenis_surat === 'surat_tugas' ? 'selected' : '' }}>Surat Tugas PTK</option>
                            <option value="suket_siswa" {{ $item->jenis_surat === 'suket_siswa' ? 'selected' : '' }}>Surat Keterangan Siswa</option>
                            <option value="rekomendasi_mutasi" {{ $item->jenis_surat === 'rekomendasi_mutasi' ? 'selected' : '' }}>Rekomendasi Mutasi</option>
                            <option value="sk_kepsek" {{ $item->jenis_surat === 'sk_kepsek' ? 'selected' : '' }}>SK Kepsek</option>
                            <option value="lainnya" {{ $item->jenis_surat === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                          </select>
                        </div>
                        <div class="col-12">
                          <label class="form-label fw-bold small">Isi Naskah Surat (Opsional - Jika Diketik Langsung):</label>
                          <textarea name="isi_surat" class="form-control form-control-sm font-monospace" rows="4" placeholder="Tuliskan isi surat dinas jika ingin dicetak langsung ke A4...">{{ $item->isi_surat }}</textarea>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label fw-bold small">Tembusan (Opsional):</label>
                          <textarea name="tembusan" class="form-control form-control-sm" rows="2" placeholder="1. Kepala Dinas Pendidikan&#10;2. Arsip">{{ $item->tembusan }}</textarea>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label fw-bold small">Perbarui File Berkas PDF / Scan (Opsional):</label>
                          <input type="file" name="file_arsip" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" />
                          @if($item->file_arsip)
                            <small class="text-success d-block mt-1" style="font-size:10.5px;">
                              <i class="bi bi-file-earmark-check"></i> File saat ini tersimpan: {{ basename($item->file_arsip) }}
                            </small>
                          @endif
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer px-4 py-3 bg-light d-flex justify-content-end gap-2 border-top">
                      <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal" style="font-weight:600; border-radius:8px;">Batal</button>
                      <button type="submit" class="btn btn-sm btn-warning fw-bold px-3" style="border-radius:8px;">
                        <i class="bi bi-check-circle-fill me-1"></i> Simpan Perubahan
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          @empty
            <tr>
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="bi bi-send fs-1 d-block mb-2 text-secondary opacity-50"></i>
                Belum ada data surat keluar yang diterbitkan pada filter ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($suratKeluars->hasPages())
      <div class="p-3 border-top">
        {{ $suratKeluars->links() }}
      </div>
    @endif
  </div>

</div>

{{-- Modal Terbitkan / Buat Surat Keluar Baru (Mendukung Otomatis & Manual / Ad-Hoc) --}}
<div class="modal fade" id="modalTerbitkanNomorSurat" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow">
      <form method="POST" action="{{ route('situan.surat-keluar.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title fw-bold fs-6">
            <i class="bi bi-file-earmark-plus-fill me-1"></i> Terbitkan / Buat Surat Keluar Resmi
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          
          {{-- Pilihan Mode Penomoran: Otomatis vs Penyesuaian Manual --}}
          <div class="p-3 mb-3 rounded-3" style="background:#f8fafc; border:1.5px solid #e2e8f0;">
            <div class="fw-bold small text-dark mb-2"><i class="bi bi-gear-fill me-1 text-primary"></i> Mode Penomoran Surat:</div>
            <div class="d-flex gap-4">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="mode_penomoran" id="modeOtomatis" value="otomatis" checked onchange="toggleModePenomoran('otomatis')" />
                <label class="form-check-label fw-bold small text-dark" for="modeOtomatis">
                  <i class="bi bi-magic text-success me-1"></i> Otomatis Berdasarkan Buku Agenda (Rekomendasi)
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="mode_penomoran" id="modeManual" value="manual" onchange="toggleModePenomoran('manual')" />
                <label class="form-check-label fw-bold small text-dark" for="modeManual">
                  <i class="bi bi-pencil-square text-warning me-1"></i> Kustom / Sisip Nomor Manual
                </label>
              </div>
            </div>

            {{-- Kolom Khusus Mode Manual --}}
            <div id="sectionNomorManual" class="mt-3 pt-3 border-top d-none">
              <div class="row g-2">
                <div class="col-md-8">
                  <label class="form-label fw-bold small text-danger">Nomor Surat Manual / Kustom: <span class="text-danger">*</span></label>
                  <input type="text" name="nomor_surat_manual" id="inputNomorManual" class="form-control form-control-sm font-monospace fw-bold" placeholder="Contoh: 015.A/421.3/SMKN1AN/IX/2026 atau No. dari Dinas" />
                  <small class="text-muted" style="font-size:10.5px;">Gunakan opsi ini jika Anda menerbitkan surat susulan, nomor titipan, atau surat dengan format khusus.</small>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold small">No. Urut Agenda (Opsional):</label>
                  <input type="number" name="nomor_agenda_manual" class="form-control form-control-sm" placeholder="Otomatis jika kosong" min="1" />
                </div>
              </div>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold small">Kode Klasifikasi Dinas: <span class="text-danger">*</span></label>
              <select name="kode_klasifikasi" class="form-select form-select-sm" required id="selectKodeKlasifikasi">
                @foreach($klasifikasis as $k)
                  <option value="{{ $k->kode }}" {{ $k->kode === '421.3' ? 'selected' : '' }}>
                    {{ $k->kode }} — {{ $k->nama }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-bold small">Tanggal Surat: <span class="text-danger">*</span></label>
              <input type="date" name="tanggal_surat" value="{{ date('Y-m-d') }}" class="form-control form-control-sm" required />
            </div>

            <div class="col-md-8">
              <label class="form-label fw-bold small">Tujuan Surat (Kepada Yth): <span class="text-danger">*</span></label>
              <input type="text" name="tujuan_surat" class="form-control form-control-sm" placeholder="Contoh: Kepala Dinas Pendidikan / Pimpinan PT Telkom / Pengurus Komite" required />
            </div>

            <div class="col-md-4">
              <label class="form-label fw-bold small">Penandatangan:</label>
              <input type="text" name="penandatangan" value="Kepala Sekolah" class="form-control form-control-sm" required />
            </div>

            <div class="col-12">
              <label class="form-label fw-bold small">Perihal Surat: <span class="text-danger">*</span></label>
              <textarea name="perihal" class="form-control form-control-sm" rows="2" placeholder="Tuliskan perihal surat keluar..." required></textarea>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-bold small">Sifat Surat:</label>
              <select name="sifat_surat" class="form-select form-select-sm">
                <option value="Biasa" selected>Biasa</option>
                <option value="Penting">Penting</option>
                <option value="Segera">Segera</option>
                <option value="Rahasia">Rahasia</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-bold small">Lampiran:</label>
              <input type="text" name="lampiran" value="-" class="form-control form-control-sm" placeholder="Contoh: 1 (satu) Berkas / -" />
            </div>

            <div class="col-md-4">
              <label class="form-label fw-bold small">Kategori / Jenis Surat:</label>
              <select name="jenis_surat" class="form-select form-select-sm">
                <option value="umum" selected>Umum / Dinas</option>
                <option value="surat_tugas">Surat Tugas PTK</option>
                <option value="rekomendasi_mutasi">Rekomendasi Mutasi</option>
                <option value="suket_siswa">Surat Keterangan Siswa</option>
                <option value="lainnya">Lainnya / Khusus</option>
              </select>
            </div>

            {{-- Kolom Pembuat Surat Ad-Hoc / Teks Bebas (Bisa Dicetak Langsung) --}}
            <div class="col-12">
              <div class="card border border-info-subtle bg-info-subtle p-3 rounded-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="fw-bold small text-info-emphasis">
                    <i class="bi bi-file-text-fill me-1"></i> Buat / Ketik Teks Surat di Sini (Untuk Surat Ad-Hoc &amp; Cetak A4 Langsung)
                  </div>
                  <span class="badge bg-info text-white" style="font-size:10px;">Opsional</span>
                </div>
                <p class="text-muted small mb-2" style="font-size:11px;">
                  Jika surat ini belum memiliki template di aplikasi, Anda bisa mengetik isinya di bawah ini. Sistem otomatis menyematkan <strong>Kop Surat Resmi 2 Logo</strong> dan format A4 standar saat dicetak. Jika surat sudah dibuat di Word, Anda cukup mengunggah file scan-nya saja di bawah.
                </p>
                <textarea name="isi_surat" class="form-control form-control-sm font-monospace bg-white" rows="5" placeholder="Contoh isi surat:&#10;&#10;Dengan hormat,&#10;Sehubungan dengan pelaksanaan program kerja sekolah, kami mengharapkan kehadiran Bapak/Ibu pada:&#10;Hari/Tanggal : Senin, 15 September 2026&#10;Waktu : 09.00 WIB s.d Selesai&#10;Tempat : Ruang Rapat SMKN 1 Air Naningan&#10;&#10;Demikian surat ini disampaikan. Atas perhatian dan kerja samanya kami ucapkan terima kasih."></textarea>
                
                <div class="row g-2 mt-2">
                  <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted" style="font-size:11px;">Jabatan Penandatangan:</label>
                    <input type="text" name="jabatan_penandatangan" value="Kepala Sekolah" class="form-control form-control-sm bg-white" />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted" style="font-size:11px;">Tembusan (Jika Ada):</label>
                    <input type="text" name="tembusan" class="form-control form-control-sm bg-white" placeholder="Contoh: 1. Kepala Dinas Pendidikan Prov. Lampung&#10;2. Arsip" />
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label fw-bold small">Unggah Berkas Scan / PDF (Jika Dibuat dari Luar):</label>
              <input type="file" name="file_arsip" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" />
              <small class="text-muted" style="font-size:10.5px;">Unggah hasil scan surat bertanda tangan basah atau file PDF untuk pengarsipan digital TU.</small>
            </div>
          </div>
        </div>
        <div class="modal-footer px-4 py-3 bg-light d-flex justify-content-end gap-2 border-top">
          <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal" style="font-weight:600; border-radius:8px;">Batal</button>
          <button type="submit" class="btn btn-sm btn-success fw-bold px-3" style="border-radius:8px;">
            <i class="bi bi-check-circle-fill me-1"></i> Terbitkan &amp; Simpan Surat Keluar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function toggleModePenomoran(mode) {
    const sec = document.getElementById('sectionNomorManual');
    const inputManual = document.getElementById('inputNomorManual');
    if (mode === 'manual') {
      sec.classList.remove('d-none');
      inputManual.setAttribute('required', 'required');
      inputManual.focus();
    } else {
      sec.classList.add('d-none');
      inputManual.removeAttribute('required');
      inputManual.value = '';
    }
  }
</script>
@endsection

