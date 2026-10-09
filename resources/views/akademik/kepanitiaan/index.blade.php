@extends('akademik.layout')

@section('title', 'Kepanitiaan Asesmen - Kurikulum DCC')
@section('breadcrumb', 'Kepanitiaan Asesmen')

@section('content')
<div class="container-fluid py-3">
  {{-- Header --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary text-white" style="font-size:11px; padding:4px 8px; border-radius:6px;">Waka Kurikulum</span>
        <h4 class="fw-bold mb-0 text-dark">Kepanitiaan Asesmen & Ujian Sekolah</h4>
      </div>
      <p class="text-muted small mb-0 mt-1">Penetapan event asesmen sumatif terjadwal (STS, SAS, SAT, ASAJ, UKK, ANBK) serta pembentukan SK personalia panitia pelaksana.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahEvent">
        <i class="bi bi-plus-circle-fill"></i>
        <span>Tetapkan Event Asesmen</span>
      </button>
      <a href="{{ route('panitia-asesmen.dashboard') }}" class="btn btn-outline-success btn-sm px-3 shadow-sm d-flex align-items-center gap-2" title="Buka Workspace Panitia CBT">
        <i class="bi bi-laptop-fill"></i>
        <span>Workspace Panitia CBT</span>
      </a>
    </div>
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-check-circle-fill text-success fs-5"></i>
      <div>{{ session('success') }}</div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  {{-- Filter & List Card --}}
  <div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
      <form action="{{ route('akademik.kepanitiaan.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0" placeholder="Cari nama event asesmen atau no SK...">
          </div>
        </div>
        <div class="col-md-3">
          <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">-- Semua Status --</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft / Persiapan</option>
            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif Berjalan</option>
            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-sm btn-secondary w-100">Filter</button>
        </div>
        @if(request()->hasAny(['q', 'status']))
        <div class="col-md-2">
          <a href="{{ route('akademik.kepanitiaan.index') }}" class="btn btn-sm btn-outline-danger w-100">Reset</a>
        </div>
        @endif
      </form>
    </div>
  </div>

  {{-- Tabel Event Asesmen --}}
  <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:13px;">
        <thead class="table-light text-muted fw-bold">
          <tr>
            <th class="ps-3 py-3" style="width:50px;">No</th>
            <th>Event Asesmen</th>
            <th>Tahun Ajaran / Semester</th>
            <th>Jadwal Pelaksanaan</th>
            <th>SK Kepanitiaan</th>
            <th class="text-center">Personalia</th>
            <th class="text-center">Status</th>
            <th class="text-end pe-3">Aksi &amp; SK</th>
          </tr>
        </thead>
        <tbody>
          @forelse($periodes as $idx => $item)
          <tr>
            <td class="ps-3 fw-bold text-muted">{{ $periodes->firstItem() + $idx }}</td>
            <td>
              <div class="fw-bold text-dark fs-6">{{ $item->nama_event }}</div>
              <div class="text-muted small">{{ $item->jenis_label }}</div>
            </td>
            <td>
              <div>{{ $item->tahunAjaran->nama ?? '-' }}</div>
              <span class="badge bg-light text-dark border">Semester {{ $item->semester }}</span>
            </td>
            <td>
              <div class="fw-semibold text-dark">{{ $item->tanggal_mulai->format('d/m/Y') }} s/d {{ $item->tanggal_selesai->format('d/m/Y') }}</div>
              <div class="text-muted small">{{ $item->tanggal_mulai->diffInDays($item->tanggal_selesai) + 1 }} Hari Kerja</div>
            </td>
            <td>
              <div class="font-monospace small text-primary fw-bold">{{ $item->sk_nomor ?? '-' }}</div>
              @if($item->sk_tanggal)
                <div class="text-muted small">Tgl: {{ $item->sk_tanggal->format('d/m/Y') }}</div>
              @endif
            </td>
            <td class="text-center">
              <span class="badge bg-info text-dark rounded-pill px-2.5 py-1">
                <i class="bi bi-people-fill me-1"></i> {{ $item->panitias_count }} Orang
              </span>
            </td>
            <td class="text-center">
              {!! $item->status_badge !!}
            </td>
            <td class="text-end pe-3">
              <div class="d-inline-flex gap-1">
                <a href="{{ route('akademik.kepanitiaan.show', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Susun Personalia Panitia">
                  <i class="bi bi-person-gear"></i> Susun Panitia
                </a>
                <a href="{{ route('akademik.kepanitiaan.cetak-sk', $item->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Cetak Dokumen SK Panitia A4">
                  <i class="bi bi-printer-fill"></i> Cetak SK
                </a>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-5 text-muted">
              <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
              Belum ada event asesmen yang ditetapkan. Silakan buat event asesmen baru di atas.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($periodes->hasPages())
    <div class="p-3 border-top">
      {{ $periodes->links() }}
    </div>
    @endif
  </div>
</div>

{{-- Modal Tambah Event Asesmen --}}
<div class="modal fade" id="modalTambahEvent" tabindex="-1" aria-labelledby="modalTambahEventLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <form action="{{ route('akademik.kepanitiaan.store') }}" method="POST">
        @csrf
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-bold fs-6" id="modalTambahEventLabel">
            <i class="bi bi-calendar-plus me-1"></i> Tetapkan Event Asesmen Baru
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-bold text-dark">Nama Event Asesmen <span class="text-danger">*</span></label>
            <input type="text" name="nama_event" class="form-control form-control-sm" required placeholder="Contoh: Sumatif Akhir Semester (SAS) Ganjil 2026/2027">
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark">Tahun Ajaran <span class="text-danger">*</span></label>
              <select name="tahun_ajaran_id" class="form-select form-select-sm" required>
                @foreach($tahunAjarans as $t)
                  <option value="{{ $t->id }}" {{ $t->is_active ? 'selected' : '' }}>{{ $t->nama }} {{ $t->is_active ? '(Aktif)' : '' }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark">Semester <span class="text-danger">*</span></label>
              <select name="semester" class="form-select form-select-sm" required>
                <option value="1">Semester Ganjil (1)</option>
                <option value="2">Semester Genap (2)</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold text-dark">Jenis Asesmen <span class="text-danger">*</span></label>
            <select name="jenis_asesmen" class="form-select form-select-sm" required>
              <option value="pts">Sumatif Tengah Semester (STS / PTS)</option>
              <option value="pas" selected>Sumatif Akhir Semester (SAS / PAS)</option>
              <option value="pat">Sumatif Akhir Tahun (SAT / PAT)</option>
              <option value="us">Asesmen Sumatif Akhir Jenjang (ASAJ / Ujian Sekolah)</option>
              <option value="anbk">Asesmen Nasional Berbasis Komputer (ANBK)</option>
              <option value="ukk">Uji Kompetensi Keahlian (UKK)</option>
              <option value="lainnya">Asesmen Lainnya</option>
            </select>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark">Tanggal Mulai <span class="text-danger">*</span></label>
              <input type="date" name="tanggal_mulai" class="form-control form-control-sm" required value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-dark">Tanggal Selesai <span class="text-danger">*</span></label>
              <input type="date" name="tanggal_selesai" class="form-control form-control-sm" required value="{{ date('Y-m-d', strtotime('+7 days')) }}">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold text-dark">Nomor SK Kepanitiaan (Opsional)</label>
            <input type="text" name="sk_nomor" class="form-control form-control-sm font-monospace" placeholder="Kosongkan jika ingin digenerate otomatis">
          </div>

          <div class="mb-2">
            <label class="form-label small fw-bold text-dark">Catatan / Keterangan Tambahan</label>
            <textarea name="keterangan" rows="2" class="form-control form-control-sm" placeholder="Catatan kebijakan, sistem pengerjaan CBT di lab, dll..."></textarea>
          </div>
        </div>
        <div class="modal-footer bg-light p-3">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-sm btn-primary px-3 fw-bold">Tetapkan Event &amp; Buka Form Panitia</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
