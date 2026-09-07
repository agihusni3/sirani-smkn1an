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
  <div class="card border-0 shadow-sm rounded-3" style="background:var(--surface); border:1px solid var(--border)!important;">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
        <thead class="table-light" style="border-bottom:1.5px solid var(--border);">
          <tr>
            <th class="py-3 px-3 text-center" style="width:75px;">No. Agenda</th>
            <th class="py-3 px-3" style="width:230px;">Nomor Surat Resmi</th>
            <th class="py-3 px-3" style="width:130px;">Tanggal Terbit</th>
            <th class="py-3 px-3" style="width:200px;">Tujuan Surat</th>
            <th class="py-3 px-3">Perihal &amp; Klasifikasi</th>
            <th class="py-3 px-3 text-center" style="width:120px;">Arsip File</th>
          </tr>
        </thead>
        <tbody>
          @forelse($suratKeluars as $item)
            <tr>
              <td class="text-center px-3">
                <span class="badge bg-secondary-subtle text-secondary px-2 py-1 fw-bold" style="font-size:12px; font-family:var(--font-mono, monospace);">
                  #{{ str_pad((string)$item->nomor_agenda, 3, '0', STR_PAD_LEFT) }}
                </span>
              </td>
              <td class="px-3">
                <div class="fw-bold text-primary font-monospace" style="font-size:12.5px;">
                  {{ $item->nomor_surat_lengkap }}
                </div>
                <div class="text-muted" style="font-size:11px;">
                  Penandatangan: <strong>{{ $item->penandatangan }}</strong>
                </div>
              </td>
              <td class="px-3">
                <div class="fw-semibold" style="color:var(--text);">{{ $item->tanggal_surat->translatedFormat('d M Y') }}</div>
              </td>
              <td class="px-3">
                <div class="fw-bold" style="color:var(--text);">{{ $item->tujuan_surat }}</div>
              </td>
              <td class="px-3">
                <div class="fw-semibold mb-1" style="color:var(--text);">{{ $item->perihal }}</div>
                <div>
                  <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-0" style="font-size:10px;">
                    Kode: {{ $item->kode_klasifikasi }}
                  </span>
                  @if($item->jenis_surat === 'suket_siswa')
                    <span class="badge bg-success-subtle text-success border border-success-subtle ms-1 px-2 py-0" style="font-size:10px;">
                      <i class="bi bi-person-check-fill me-1"></i>Suket Siswa
                    </span>
                  @endif
                </div>
              </td>
              <td class="text-center px-3">
                @if($item->file_arsip)
                  <a href="{{ asset('storage/' . $item->file_arsip) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Buka Dokumen Hasil Scan/PDF">
                    <i class="bi bi-file-earmark-pdf-fill"></i> Unduh
                  </a>
                @else
                  <span class="text-muted small" style="font-size:11px;">-</span>
                @endif
              </td>
            </tr>
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

{{-- Modal Ambil Nomor Surat Keluar Baru --}}
<div class="modal fade" id="modalTerbitkanNomorSurat" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow">
      <form method="POST" action="{{ route('situan.surat-keluar.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title fw-bold fs-6">
            <i class="bi bi-send-plus-fill me-1"></i> Ambil &amp; Terbitkan Nomor Surat Keluar Resmi
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="alert alert-info py-2 px-3 small border-0 mb-3">
            <i class="bi bi-info-circle-fill me-1"></i> Nomor urut surat akan dibuat secara otomatis dan berurutan oleh sistem untuk mencegah nomor ganda antar-staf TU.
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
              <input type="text" name="tujuan_surat" class="form-control form-control-sm" placeholder="Contoh: Kepala Dinas Pendidikan Provinsi Lampung / Pimpinan PT Telkom" required />
            </div>

            <div class="col-md-4">
              <label class="form-label fw-bold small">Penandatangan:</label>
              <input type="text" name="penandatangan" value="Kepala Sekolah" class="form-control form-control-sm" required />
            </div>

            <div class="col-12">
              <label class="form-label fw-bold small">Perihal Surat: <span class="text-danger">*</span></label>
              <textarea name="perihal" class="form-control form-control-sm" rows="2" placeholder="Tuliskan perihal surat keluar..." required></textarea>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-bold small">Kategori / Jenis Surat:</label>
              <select name="jenis_surat" class="form-select form-select-sm">
                <option value="umum" selected>Umum / Dinas</option>
                <option value="surat_tugas">Surat Tugas PTK</option>
                <option value="rekomendasi_mutasi">Rekomendasi Mutasi</option>
                <option value="suket_siswa">Surat Keterangan Siswa</option>
                <option value="lainnya">Lainnya</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-bold small">Upload File Dokumen Arsip (Opsional):</label>
              <input type="file" name="file_arsip" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" />
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light p-2">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-sm btn-success fw-bold px-3">
            <i class="bi bi-check-circle-fill me-1"></i> Terbitkan Nomor Resmi
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
