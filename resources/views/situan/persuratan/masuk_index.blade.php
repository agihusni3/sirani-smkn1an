@extends('layouts.app')

@section('title', 'Buku Agenda Surat Masuk — SITUAN SMKN 1 AN')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">

  {{-- Page Header --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
      <div class="d-flex align-items-center gap-2 mb-1">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill" style="font-size:11px; font-weight:700;">
          <i class="bi bi-inbox-fill me-1"></i> Tata Naskah Dinas
        </span>
        <span class="text-muted" style="font-size:12px;">Tahun {{ $thisYear }}</span>
      </div>
      <h1 class="h3 mb-0 fw-bold" style="color:var(--text); letter-spacing:-0.02em;">Buku Agenda Surat Masuk</h1>
      <p class="text-muted mb-0 small">Pencatatan naskah dinas masuk, pengarsipan berkas scan, dan lembar disposisi digital Kepala Sekolah.</p>
    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('situan.index') }}" class="btn btn-outline-secondary btn-sm" style="font-weight:600;">
        <i class="bi bi-arrow-left me-1"></i> Dasbor SITUAN
      </a>
      <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambahSuratMasuk" style="font-weight:700;">
        <i class="bi bi-plus-circle-fill me-1"></i> Catat Surat Masuk
      </button>
    </div>
  </div>

  {{-- Quick Metric Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:var(--surface); border:1px solid var(--border)!important;">
        <div class="text-muted small fw-semibold mb-1">Total Surat Masuk ({{ $thisYear }})</div>
        <div class="h3 mb-0 fw-bolder text-primary">{{ $totalTahunIni }}</div>
        <div class="text-muted" style="font-size:11px;">Surat dinas teregistrasi</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:var(--surface); border:1px solid var(--border)!important;">
        <div class="text-muted small fw-semibold mb-1">Menunggu Disposisi</div>
        <div class="h3 mb-0 fw-bolder text-danger">{{ $menungguDisposisi }}</div>
        <div class="text-danger" style="font-size:11px;"><i class="bi bi-clock-history me-1"></i>Perlu arahan Kepsek</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:var(--surface); border:1px solid var(--border)!important;">
        <div class="text-muted small fw-semibold mb-1">Sedang Ditindaklanjuti</div>
        <div class="h3 mb-0 fw-bolder text-warning">{{ $didisposisi }}</div>
        <div class="text-muted" style="font-size:11px;">Didisposisikan ke staf</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:var(--surface); border:1px solid var(--border)!important;">
        <div class="text-muted small fw-semibold mb-1">Selesai / Terarsip</div>
        <div class="h3 mb-0 fw-bolder text-success">{{ $selesaiTindakLanjut }}</div>
        <div class="text-success" style="font-size:11px;"><i class="bi bi-check-all me-1"></i>Selesai diproses</div>
      </div>
    </div>
  </div>

  {{-- Filters & Search --}}
  <div class="card border-0 shadow-sm rounded-3 mb-4" style="background:var(--surface); border:1px solid var(--border)!important;">
    <div class="card-body p-3">
      <form method="GET" action="{{ route('situan.surat-masuk.index') }}" class="row g-2">
        <div class="col-md-5">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-start-0" placeholder="Cari nomor surat, pengirim, perihal, atau no agenda..." />
          </div>
        </div>
        <div class="col-md-3">
          <select name="status" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
            <option value="">-- Semua Status Disposisi --</option>
            <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>🔴 Menunggu Disposisi</option>
            <option value="didisposisi" {{ request('status') === 'didisposisi' ? 'selected' : '' }}>🟡 Didisposisikan</option>
            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>🟢 Selesai</option>
          </select>
        </div>
        <div class="col-md-2">
          <select name="tahun" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
            @for($y = (int)date('Y'); $y >= (int)date('Y') - 3; $y--)
              <option value="{{ $y }}" {{ request('tahun', $thisYear) == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
            @endfor
          </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
          <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">Filter</button>
          @if(request()->hasAny(['q', 'status', 'tahun']))
            <a href="{{ route('situan.surat-masuk.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter"><i class="bi bi-x-lg"></i></a>
          @endif
        </div>
      </form>
    </div>
  </div>

  {{-- Agenda Table --}}
  <div class="card border-0 shadow-sm rounded-3" style="background:var(--surface); border:1px solid var(--border)!important;">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
        <thead class="table-light" style="border-bottom:1.5px solid var(--border);">
          <tr>
            <th class="py-3 px-3 text-center" style="width:75px;">No. Agenda</th>
            <th class="py-3 px-3" style="width:160px;">Tgl Terima / Surat</th>
            <th class="py-3 px-3" style="width:220px;">Nomor &amp; Pengirim</th>
            <th class="py-3 px-3">Perihal &amp; Urgensi</th>
            <th class="py-3 px-3" style="width:180px;">Status Disposisi</th>
            <th class="py-3 px-3 text-center" style="width:170px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($suratMasuks as $item)
            <tr>
              {{-- No Agenda --}}
              <td class="text-center px-3">
                <span class="badge bg-secondary-subtle text-secondary px-2 py-1 fw-bold" style="font-size:12px; font-family:var(--font-mono, monospace);">
                  #{{ str_pad((string)$item->nomor_agenda, 3, '0', STR_PAD_LEFT) }}
                </span>
              </td>

              {{-- Tanggal --}}
              <td class="px-3">
                <div class="fw-bold" style="color:var(--text);">{{ $item->tanggal_diterima->translatedFormat('d M Y') }}</div>
                <div class="text-muted" style="font-size:11px;">Surat: {{ $item->tanggal_surat->translatedFormat('d/m/Y') }}</div>
              </td>

              {{-- Nomor & Pengirim --}}
              <td class="px-3">
                <div class="fw-bold text-primary" style="font-family:var(--font-mono, monospace); font-size:12px;">{{ $item->nomor_surat_asal }}</div>
                <div class="text-muted small"><i class="bi bi-building me-1"></i>{{ $item->pengirim }}</div>
              </td>

              {{-- Perihal & Urgensi --}}
              <td class="px-3">
                <div class="fw-semibold mb-1" style="color:var(--text);">{{ $item->perihal }}</div>
                <div>
                  @if($item->tingkat_urgensi === 'rahasia')
                    <span class="badge bg-danger text-white px-2 py-0" style="font-size:10px;">RAHASIA</span>
                  @elseif($item->tingkat_urgensi === 'segera')
                    <span class="badge bg-warning text-dark px-2 py-0" style="font-size:10px;">SEGERA</span>
                  @elseif($item->tingkat_urgensi === 'penting')
                    <span class="badge bg-info text-dark px-2 py-0" style="font-size:10px;">PENTING</span>
                  @else
                    <span class="badge bg-light text-muted border px-2 py-0" style="font-size:10px;">Biasa</span>
                  @endif

                  @if($item->file_lampiran)
                    <a href="{{ asset('storage/' . $item->file_lampiran) }}" target="_blank" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none ms-1 px-2 py-0" style="font-size:10px;">
                      <i class="bi bi-paperclip me-1"></i>Berkas Scan
                    </a>
                  @endif
                </div>
              </td>

              {{-- Status Disposisi --}}
              <td class="px-3">
                @if($item->status_disposisi === 'menunggu')
                  <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="font-size:11px; font-weight:700;">
                    <i class="bi bi-hourglass-split me-1"></i>Menunggu Kepsek
                  </span>
                @elseif($item->status_disposisi === 'didisposisi')
                  <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1 mb-1 d-inline-block" style="font-size:11px; font-weight:700;">
                    <i class="bi bi-arrow-right-circle me-1"></i>Didisposisikan
                  </span>
                  @if($item->disposisis->isNotEmpty())
                    <div class="text-muted" style="font-size:11px;">
                      Ke: <strong>{{ $item->disposisis->first()?->penerima?->name }}</strong>
                    </div>
                  @endif
                @else
                  <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size:11px; font-weight:700;">
                    <i class="bi bi-check-circle-fill me-1"></i>Selesai
                  </span>
                @endif
              </td>

              {{-- Aksi --}}
              <td class="text-center px-3">
                <div class="btn-group btn-group-sm">
                  {{-- Tombol Disposisi (Kepsek/Admin) --}}
                  @if(auth()->user()->isKepalaSekolah() || auth()->user()->isAdmin())
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalDisposisi{{ $item->id }}" title="Beri Disposisi Kepsek">
                      <i class="bi bi-pencil-square"></i> Disposisi
                    </button>
                  @endif

                  {{-- Cetak Lembar Disposisi Resmi A5 --}}
                  <a href="{{ route('situan.surat-masuk.cetak-disposisi', $item->id) }}" target="_blank" class="btn btn-outline-secondary" title="Cetak Lembar Disposisi Standar A5">
                    <i class="bi bi-printer-fill"></i>
                  </a>

                  {{-- Lihat Berkas Lampiran --}}
                  @if($item->file_lampiran)
                    <a href="{{ asset('storage/' . $item->file_lampiran) }}" target="_blank" class="btn btn-outline-info" title="Buka File Scan">
                      <i class="bi bi-file-earmark-pdf"></i>
                    </a>
                  @endif
                </div>

                {{-- Modal Disposisi Kepsek --}}
                @if(auth()->user()->isKepalaSekolah() || auth()->user()->isAdmin())
                  <div class="modal fade" id="modalDisposisi{{ $item->id }}" tabindex="-1" aria-hidden="true" style="text-align:left;">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content border-0 shadow">
                        <form method="POST" action="{{ route('situan.surat-masuk.disposisi', $item->id) }}">
                          @csrf
                          <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold fs-6">
                              <i class="bi bi-pencil-square text-primary me-1"></i> Lembar Disposisi Kepala Sekolah
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body p-3">
                            <div class="p-2 mb-3 bg-light rounded border" style="font-size:12px;">
                              <div><strong>No. Surat:</strong> {{ $item->nomor_surat_asal }}</div>
                              <div><strong>Pengirim:</strong> {{ $item->pengirim }}</div>
                              <div><strong>Perihal:</strong> {{ $item->perihal }}</div>
                            </div>

                            <div class="mb-3">
                              <label class="form-label fw-bold small">Diteruskan Kepada (Pejabat / Staf):</label>
                              <select name="penerima_user_id" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Penerima Disposisi --</option>
                                @foreach($pejabatTujuan as $p)
                                  <option value="{{ $p->id }}">{{ $p->name }} ({{ strtoupper(str_replace('_', ' ', $p->role)) }})</option>
                                @endforeach
                              </select>
                            </div>

                            <div class="mb-3">
                              <label class="form-label fw-bold small">Instruksi / Petunjuk Tindak Lanjut:</label>
                              <div class="row g-2" style="font-size:12px;">
                                <div class="col-6">
                                  <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="instruksi_flags[]" value="tindak_lanjuti" id="ins1_{{ $item->id }}" checked>
                                    <label class="form-check-label" for="ins1_{{ $item->id }}">Tindak lanjuti segera</label>
                                  </div>
                                </div>
                                <div class="col-6">
                                  <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="instruksi_flags[]" value="pelajari" id="ins2_{{ $item->id }}">
                                    <label class="form-check-label" for="ins2_{{ $item->id }}">Pelajari / Telaah</label>
                                  </div>
                                </div>
                                <div class="col-6">
                                  <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="instruksi_flags[]" value="hadiri_wakilkan" id="ins3_{{ $item->id }}">
                                    <label class="form-check-label" for="ins3_{{ $item->id }}">Hadiri / Wakilkan</label>
                                  </div>
                                </div>
                                <div class="col-6">
                                  <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="instruksi_flags[]" value="siapkan_laporan" id="ins4_{{ $item->id }}">
                                    <label class="form-check-label" for="ins4_{{ $item->id }}">Siapkan Bahan / Laporan</label>
                                  </div>
                                </div>
                                <div class="col-6">
                                  <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="instruksi_flags[]" value="koordinasikan" id="ins5_{{ $item->id }}">
                                    <label class="form-check-label" for="ins5_{{ $item->id }}">Koordinasikan</label>
                                  </div>
                                </div>
                                <div class="col-6">
                                  <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="instruksi_flags[]" value="arsipkan" id="ins6_{{ $item->id }}">
                                    <label class="form-check-label" for="ins6_{{ $item->id }}">Arsipkan / Ketahui</label>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <div class="mb-3">
                              <label class="form-label fw-bold small">Catatan Tambahan Kepala Sekolah:</label>
                              <textarea name="catatan_kepsek" class="form-control form-control-sm" rows="2" placeholder="Tulis instruksi spesifik di sini..."></textarea>
                            </div>

                            <div class="mb-2">
                              <label class="form-label fw-bold small">Batas Waktu Selesai (Opsional):</label>
                              <input type="date" name="batas_waktu" class="form-control form-control-sm" />
                            </div>
                          </div>
                          <div class="modal-footer bg-light p-2">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary fw-bold">Simpan &amp; Teruskan Disposisi</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                Belum ada data surat masuk yang dicatat pada filter ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($suratMasuks->hasPages())
      <div class="p-3 border-top">
        {{ $suratMasuks->links() }}
      </div>
    @endif
  </div>

</div>

{{-- Modal Catat Surat Masuk Baru --}}
<div class="modal fade" id="modalTambahSuratMasuk" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow">
      <form method="POST" action="{{ route('situan.surat-masuk.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-bold fs-6">
            <i class="bi bi-plus-circle me-1"></i> Catat Surat Masuk Baru (No. Agenda: #{{ str_pad((string)$nextAgenda, 3, '0', STR_PAD_LEFT) }}/{{ $thisYear }})
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold small">Nomor Surat (dari Pengirim): <span class="text-danger">*</span></label>
              <input type="text" name="nomor_surat_asal" class="form-control form-control-sm" placeholder="Contoh: 421.3/128/DISDIK/IX/2026" required />
            </div>

            <div class="col-md-6">
              <label class="form-label fw-bold small">Instansi / Organisasi Pengirim: <span class="text-danger">*</span></label>
              <input type="text" name="pengirim" class="form-control form-control-sm" placeholder="Contoh: Dinas Pendidikan Provinsi Lampung / PT Telkom" required />
            </div>

            <div class="col-md-4">
              <label class="form-label fw-bold small">Tanggal Surat: <span class="text-danger">*</span></label>
              <input type="date" name="tanggal_surat" value="{{ date('Y-m-d') }}" class="form-control form-control-sm" required />
            </div>

            <div class="col-md-4">
              <label class="form-label fw-bold small">Tanggal Diterima di TU: <span class="text-danger">*</span></label>
              <input type="date" name="tanggal_diterima" value="{{ date('Y-m-d') }}" class="form-control form-control-sm" required />
            </div>

            <div class="col-md-4">
              <label class="form-label fw-bold small">Tingkat Urgensi:</label>
              <select name="tingkat_urgensi" class="form-select form-select-sm">
                <option value="biasa" selected>Biasa</option>
                <option value="penting">Penting</option>
                <option value="segera">Segera</option>
                <option value="rahasia">Rahasia</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label fw-bold small">Perihal / Ringkasan Isi Surat: <span class="text-danger">*</span></label>
              <textarea name="perihal" class="form-control form-control-sm" rows="2" placeholder="Tuliskan perihal surat secara ringkas dan jelas..." required></textarea>
            </div>

            <div class="col-12">
              <label class="form-label fw-bold small">Lampiran Scan Berkas (PDF / Gambar):</label>
              <input type="file" name="file_lampiran" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" />
              <div class="form-text small">Unggah dokumen asli hasil scan (Maksimal 10 MB, format PDF/JPG).</div>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light p-2">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">
            <i class="bi bi-save me-1"></i> Simpan ke Buku Agenda
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
