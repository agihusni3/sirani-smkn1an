@extends('layouts.app')

@section('title', 'Loket Pelayanan Surat Siswa — SITUAN SMKN 1 AN')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">

  {{-- Flash Alerts --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 py-2 px-3 mb-3 d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-check-circle-fill fs-5 text-success"></i>
      <div class="small fw-semibold">{{ session('success') }}</div>
      <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('info'))
    <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm rounded-3 py-2 px-3 mb-3 d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-info-circle-fill fs-5 text-info"></i>
      <div class="small fw-semibold">{{ session('info') }}</div>
      <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 py-2 px-3 mb-3 d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
      <div class="small fw-semibold">{{ session('error') }}</div>
      <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  {{-- Page Header --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
      <div class="d-flex align-items-center gap-2 mb-1">
        <span class="badge bg-teal-subtle text-success border border-success-subtle px-2 py-1 rounded-pill" style="font-size:11px; font-weight:700;">
          <i class="bi bi-person-badge-fill me-1"></i> Loket Mandiri Kesiswaan
        </span>
        <span class="text-muted" style="font-size:12px;">One-Click Auto Generation</span>
      </div>
      <h1 class="h3 mb-0 fw-bold" style="color:var(--text); letter-spacing:-0.02em;">Loket Pelayanan Surat Siswa</h1>
      <p class="text-muted mb-0 small">Layanan instan penerbitan Surat Keterangan Siswa Aktif, Mutasi, Berkelakuan Baik, dan SKL dengan QR-Code verifikasi publik anti-pemalsuan.</p>
    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('situan.index') }}" class="btn btn-outline-secondary btn-sm" style="font-weight:600;">
        <i class="bi bi-arrow-left me-1"></i> Dasbor SITUAN
      </a>
      <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalBuatSuratSiswa" style="font-weight:700;">
        <i class="bi bi-magic me-1"></i> Terbitkan Surat Siswa
      </button>
    </div>
  </div>

  {{-- Top Stat Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:var(--surface); border:1px solid var(--border)!important;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted small fw-bold text-uppercase">Total Surat Diterbitkan</div>
            <div class="h3 mb-0 fw-bold text-primary">{{ $totalSuratDiterbitkan }}</div>
          </div>
          <div class="rounded-3 p-3 bg-primary-subtle text-primary">
            <i class="bi bi-file-earmark-text fs-4"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:var(--surface); border:1px solid var(--border)!important;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted small fw-bold text-uppercase">Suket Siswa Aktif</div>
            <div class="h3 mb-0 fw-bold text-success">{{ $totalSuketAktif }}</div>
          </div>
          <div class="rounded-3 p-3 bg-success-subtle text-success">
            <i class="bi bi-check2-circle fs-4"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:var(--surface); border:1px solid var(--border)!important;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted small fw-bold text-uppercase">Surat Mutasi Keluar</div>
            <div class="h3 mb-0 fw-bold text-warning">{{ $totalMutasi }}</div>
          </div>
          <div class="rounded-3 p-3 bg-warning-subtle text-warning">
            <i class="bi bi-arrow-left-right fs-4"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Search & Filter --}}
  <div class="card border-0 shadow-sm rounded-3 mb-4" style="background:var(--surface); border:1px solid var(--border)!important;">
    <div class="card-body p-3">
      <form method="GET" action="{{ route('situan.pelayanan.index') }}" class="row g-2">
        <div class="col-md-6">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-start-0" placeholder="Cari nama siswa, NISN, atau keperluan..." />
          </div>
        </div>
        <div class="col-md-4">
          <select name="jenis" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
            <option value="">-- Semua Jenis Pelayanan Surat --</option>
            <option value="suket_aktif" {{ request('jenis') === 'suket_aktif' ? 'selected' : '' }}>Surat Keterangan Siswa Aktif</option>
            <option value="suket_berkelakuan_baik" {{ request('jenis') === 'suket_berkelakuan_baik' ? 'selected' : '' }}>Surat Berkelakuan Baik</option>
            <option value="suket_mutasi_keluar" {{ request('jenis') === 'suket_mutasi_keluar' ? 'selected' : '' }}>Surat Rekomendasi Pindah / Mutasi</option>
            <option value="suket_skl" {{ request('jenis') === 'suket_skl' ? 'selected' : '' }}>Surat Keterangan Lulus (SKL)</option>
            <option value="suket_pengantar_pkl" {{ request('jenis') === 'suket_pengantar_pkl' ? 'selected' : '' }}>Surat Pengantar PKL</option>
          </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
          <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">Filter</button>
          @if(request()->hasAny(['q', 'jenis']))
            <a href="{{ route('situan.pelayanan.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>
          @endif
        </div>
      </form>
    </div>
  </div>

  {{-- Pelayanan History Table --}}
  <div class="situan-table-card shadow-sm border rounded-4 overflow-hidden mb-4" style="background:var(--surface); border:1px solid var(--border)!important;">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
        <thead style="background:#f8fafc; border-bottom:1.5px solid var(--border);">
          <tr>
            <th class="py-3 px-3" style="width:230px; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; color:#475569;">Nomor Agenda &amp; Surat</th>
            <th class="py-3 px-3" style="width:200px; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; color:#475569;">Nama Siswa / Rombel</th>
            <th class="py-3 px-3" style="width:170px; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; color:#475569;">Jenis Layanan</th>
            <th class="py-3 px-3" style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; color:#475569;">Keperluan / Keterangan</th>
            <th class="py-3 px-3" style="width:125px; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; color:#475569;">Tgl Terbit</th>
            <th class="py-3 px-3 text-center" style="width:175px; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; color:#475569;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($pelayanans as $item)
            @php
              $badgeClass = match($item->jenis_pelayanan) {
                'suket_aktif' => 'bg-success-subtle text-success border border-success-subtle',
                'suket_mutasi_keluar' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                'suket_berkelakuan_baik' => 'bg-primary-subtle text-primary border border-primary-subtle',
                'suket_skl' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
                default => 'bg-secondary-subtle text-secondary',
              };
              $namaLayanan = match($item->jenis_pelayanan) {
                'suket_aktif' => 'Siswa Aktif',
                'suket_berkelakuan_baik' => 'Berkelakuan Baik',
                'suket_mutasi_keluar' => 'Mutasi Keluar',
                'suket_skl' => 'SKL Sementara',
                'suket_pengantar_pkl' => 'Pengantar PKL',
                default => 'Surat Keterangan',
              };
              $rombelNama = $item->siswa?->siswaRombels?->first()?->rombel?->nama_rombel ?? ($item->payload_snapshot['rombel'] ?? '-');
              $namaSiswa = $item->siswa->nama ?? ($item->payload_snapshot['nama'] ?? 'Siswa');
              $nisnSiswa = $item->siswa->nisn ?? ($item->payload_snapshot['nisn'] ?? '-');
            @endphp
            <tr>
              <td class="px-3">
                @if($item->suratKeluar)
                  <div>
                    <span class="situan-nomor-badge">{{ $item->suratKeluar->nomor_surat_lengkap }}</span>
                  </div>
                  <div class="text-muted d-flex align-items-center gap-1 mt-1" style="font-size:11px;">
                    <span class="situan-agenda-badge" style="padding:1px 5px; font-size:10px;">Agenda #{{ str_pad((string)$item->suratKeluar->nomor_agenda, 3, '0', STR_PAD_LEFT) }}</span>
                  </div>
                @else
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 rounded-pill" style="font-size:10.5px; font-weight:700;">No. Pending</span>
                    <form action="{{ route('situan.pelayanan.generate-nomor', $item->id) }}" method="POST" class="d-inline m-0">
                      @csrf
                      <button type="submit" class="btn btn-outline-primary btn-xs px-2 rounded-pill d-inline-flex align-items-center gap-1" style="font-size:10.5px; font-weight:700; height:22px;" title="Terbitkan Nomor Agenda Otomatis">
                        <i class="bi bi-hash"></i> Buat Agenda
                      </button>
                    </form>
                  </div>
                @endif
              </td>
              <td class="px-3">
                <div class="fw-bold" style="color:var(--text); font-size:13px;">{{ $namaSiswa }}</div>
                <div class="text-muted small">NISN: {{ $nisnSiswa }} &bull; {{ $rombelNama }}</div>
              </td>
              <td class="px-3">
                <span class="badge {{ $badgeClass }} px-2 py-1 rounded-pill" style="font-size:11px;">
                  {{ $namaLayanan }}
                </span>
              </td>
              <td class="px-3">
                <div class="text-dark" style="font-size:12.5px; line-height:1.4;">{{ $item->keperluan ?: '-' }}</div>
                @if(!empty($item->payload_snapshot['sekolah_tujuan']))
                  <div class="text-muted mt-1" style="font-size:11px;">
                    <i class="bi bi-building me-1"></i>Tujuan: <strong>{{ $item->payload_snapshot['sekolah_tujuan'] }}</strong>
                  </div>
                @endif
              </td>
              <td class="px-3 text-muted">
                <div class="fw-semibold text-dark" style="font-size:12px;">{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y') }}</div>
                <div style="font-size:10.5px;">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</div>
              </td>
              <td class="text-center px-3" style="white-space:nowrap;">
                <div class="situan-action-group justify-content-center">
                  {{-- 1. Detail / Preview --}}
                  <button type="button" class="situan-action-icon-btn btn-view" data-bs-toggle="modal" data-bs-target="#modalDetailSurat_{{ $item->id }}" title="Lihat Rincian Data Surat">
                    <i class="bi bi-eye-fill"></i>
                  </button>

                  {{-- 2. Cetak Surat A4 --}}
                  <a href="{{ route('situan.pelayanan.cetak', $item->id) }}" target="_blank" class="situan-action-icon-btn btn-print" title="Cetak Dokumen Resmi A4">
                    <i class="bi bi-printer-fill"></i>
                  </a>

                  {{-- 3. QR Verifikasi Publik --}}
                  <a href="{{ route('situan.verifikasi-surat', $item->kode_verifikasi_qr) }}" target="_blank" class="situan-action-icon-btn btn-pdf" title="Cek Halaman Verifikasi QR Publik">
                    <i class="bi bi-qr-code-scan"></i>
                  </a>

                  {{-- 4. Edit Surat --}}
                  <button type="button" class="situan-action-icon-btn btn-edit" data-bs-toggle="modal" data-bs-target="#modalEditSurat_{{ $item->id }}" title="Edit / Ubah Data Surat">
                    <i class="bi bi-pencil-square"></i>
                  </button>

                  {{-- 5. Hapus Surat --}}
                  <button type="button" class="situan-action-icon-btn btn-delete" onclick="hapusSuratPelayanan('{{ $item->id }}', '{{ addslashes($namaSiswa) }}')" title="Hapus Surat">
                    <i class="bi bi-trash-fill"></i>
                  </button>
                </div>

                {{-- Form Hidden Hapus --}}
                <form id="formHapusPelayanan_{{ $item->id }}" action="{{ route('situan.pelayanan.destroy', $item->id) }}" method="POST" class="d-none">
                  @csrf
                  @method('DELETE')
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                <p class="mb-0 fw-semibold">Belum ada pelayanan surat kesiswaan yang diterbitkan.</p>
                <small>Klik tombol "Terbitkan Surat Siswa" di kanan atas untuk membuat surat instan.</small>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($pelayanans->hasPages())
      <div class="p-3 border-top" style="border-color:var(--border)!important;">
        {{ $pelayanans->links() }}
      </div>
    @endif
  </div>

</div>

{{-- Semua Modal Detail & Edit untuk Setiap Baris Surat (Ditempatkan di Luar Tabel) --}}
@foreach($pelayanans as $item)
  @php
    $rombelNama = $item->siswa?->siswaRombels?->first()?->rombel?->nama_rombel ?? ($item->payload_snapshot['rombel'] ?? '-');
    $namaSiswa = $item->siswa->nama ?? ($item->payload_snapshot['nama'] ?? 'Siswa');
    $nisnSiswa = $item->siswa->nisn ?? ($item->payload_snapshot['nisn'] ?? '-');
  @endphp

  {{-- Modal Detail Pelayanan Surat --}}
  <div class="modal fade" id="modalDetailSurat_{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-light border-bottom px-4 py-3">
          <div>
            <h5 class="modal-title fw-bold fs-6 mb-1 text-dark">
              <i class="bi bi-file-earmark-text text-primary me-2"></i>Rincian Surat Kesiswaan
            </h5>
            <div class="text-muted small">ID Dokumen: #{{ $item->id }} &bull; Verifikasi Hash: <code>{{ substr($item->kode_verifikasi_qr, 0, 12) }}...</code></div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4 text-start">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="p-3 bg-light rounded-3 border h-100">
                <div class="fw-bold small text-primary mb-2"><i class="bi bi-card-heading me-1"></i> Data Surat &amp; Agenda:</div>
                <table class="table table-sm table-borderless mb-0 small">
                  <tr>
                    <td class="text-muted" style="width:120px;">Jenis Surat:</td>
                    <td class="fw-bold">{{ $item->jenis_label }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted">Nomor Surat:</td>
                    <td class="fw-bold font-monospace text-primary">{{ $item->suratKeluar?->nomor_surat_lengkap ?? 'Pending' }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted">Nomor Agenda:</td>
                    <td class="fw-bold">#{{ $item->suratKeluar?->nomor_agenda ? str_pad((string)$item->suratKeluar->nomor_agenda, 3, '0', STR_PAD_LEFT) : '-' }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted">Tgl Surat:</td>
                    <td>{{ $item->suratKeluar?->tanggal_surat ? \Carbon\Carbon::parse($item->suratKeluar->tanggal_surat)->translatedFormat('d F Y') : \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted">Keperluan:</td>
                    <td class="fw-semibold">{{ $item->keperluan ?: '-' }}</td>
                  </tr>
                  @if(!empty($item->payload_snapshot['sekolah_tujuan']))
                    <tr>
                      <td class="text-muted">Tujuan Pindah:</td>
                      <td class="fw-bold text-warning-emphasis">{{ $item->payload_snapshot['sekolah_tujuan'] }}</td>
                    </tr>
                  @endif
                  @if(!empty($item->payload_snapshot['alasan_mutasi']))
                    <tr>
                      <td class="text-muted">Alasan Pindah:</td>
                      <td>{{ $item->payload_snapshot['alasan_mutasi'] }}</td>
                    </tr>
                  @endif
                </table>
              </div>
            </div>

            <div class="col-md-6">
              <div class="p-3 bg-light rounded-3 border h-100">
                <div class="fw-bold small text-success mb-2"><i class="bi bi-person-vcard me-1"></i> Data Snapshot Siswa:</div>
                <table class="table table-sm table-borderless mb-0 small">
                  <tr>
                    <td class="text-muted" style="width:120px;">Nama Siswa:</td>
                    <td class="fw-bold">{{ $namaSiswa }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted">NIS / NISN:</td>
                    <td>{{ $item->payload_snapshot['nis'] ?? '-' }} / {{ $nisnSiswa }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted">Tempat, Tgl Lahir:</td>
                    <td>{{ $item->payload_snapshot['tempat_lahir'] ?? '-' }}, {{ $item->payload_snapshot['tanggal_lahir'] ?? '-' }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted">Jenis Kelamin:</td>
                    <td>{{ $item->payload_snapshot['jenis_kelamin'] ?? '-' }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted">Kelas / Rombel:</td>
                    <td class="fw-semibold">{{ $rombelNama }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted">Nama Orang Tua:</td>
                    <td>{{ $item->payload_snapshot['nama_ortu'] ?? '-' }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted">Alamat Siswa:</td>
                    <td>{{ $item->payload_snapshot['alamat'] ?? '-' }}</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer px-4 py-3 bg-light border-top d-flex justify-content-between">
          <div class="text-muted small">
            Diterbitkan oleh: <strong>{{ $item->creator?->name ?? 'Staf Tata Usaha' }}</strong>
          </div>
          <div class="d-flex gap-2">
            <a href="{{ route('situan.pelayanan.cetak', $item->id) }}" target="_blank" class="btn btn-sm btn-primary fw-bold px-3">
              <i class="bi bi-printer me-1"></i> Cetak A4
            </a>
            <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal Edit Pelayanan Surat --}}
  <div class="modal fade" id="modalEditSurat_{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow">
        <form method="POST" action="{{ route('situan.pelayanan.update', $item->id) }}">
          @csrf
          @method('PUT')
          <div class="modal-header bg-warning text-dark px-4 py-3">
            <h5 class="modal-title fw-bold fs-6">
              <i class="bi bi-pencil-square me-1"></i> Edit Pelayanan Surat Siswa
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4 text-start">
            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label small fw-bold">Pilih Siswa <span class="text-danger">*</span></label>
                <select name="siswa_id" class="form-select form-select-sm" required>
                  @foreach($allSiswaAktif as $s)
                    @php
                      $r = $s->siswaRombels->first()?->rombel?->nama_rombel ?? 'Belum ada kelas';
                    @endphp
                    <option value="{{ $s->id }}" {{ $item->siswa_id == $s->id ? 'selected' : '' }}>
                      {{ $s->nama }} (NISN: {{ $s->nisn ?: '-' }} / Kelas: {{ $r }})
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label small fw-bold">Tanggal Surat <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_surat" class="form-control form-control-sm" value="{{ $item->suratKeluar?->tanggal_surat ? \Carbon\Carbon::parse($item->suratKeluar->tanggal_surat)->format('Y-m-d') : \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') }}" required />
              </div>

              <div class="col-md-6">
                <label class="form-label small fw-bold">Jenis Pelayanan Surat <span class="text-danger">*</span></label>
                <select name="jenis_pelayanan" id="editJenisSelect_{{ $item->id }}" class="form-select form-select-sm" required onchange="toggleEditMutasiFields('{{ $item->id }}')">
                  <option value="suket_aktif" {{ $item->jenis_pelayanan === 'suket_aktif' ? 'selected' : '' }}>Surat Keterangan Siswa Aktif</option>
                  <option value="suket_berkelakuan_baik" {{ $item->jenis_pelayanan === 'suket_berkelakuan_baik' ? 'selected' : '' }}>Surat Keterangan Berkelakuan Baik</option>
                  <option value="suket_mutasi_keluar" {{ $item->jenis_pelayanan === 'suket_mutasi_keluar' ? 'selected' : '' }}>Surat Rekomendasi Pindah Sekolah (Mutasi)</option>
                  <option value="suket_skl" {{ $item->jenis_pelayanan === 'suket_skl' ? 'selected' : '' }}>Surat Keterangan Lulus Sementara (SKL)</option>
                  <option value="suket_pengantar_pkl" {{ $item->jenis_pelayanan === 'suket_pengantar_pkl' ? 'selected' : '' }}>Surat Pengantar PKL / Magang</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label small fw-bold">Keperluan Surat <span class="text-danger">*</span></label>
                <input type="text" name="keperluan" class="form-control form-control-sm" value="{{ $item->keperluan }}" required />
              </div>

              {{-- Mutasi Extra Fields --}}
              <div id="editMutasiFields_{{ $item->id }}" class="col-12 {{ $item->jenis_pelayanan === 'suket_mutasi_keluar' ? '' : 'd-none' }}">
                <div class="p-3 bg-light rounded-3 border">
                  <div class="fw-bold small text-warning-emphasis mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> Data Khusus Rekomendasi Pindah Sekolah:</div>
                  <div class="row g-2">
                    <div class="col-md-6">
                      <label class="form-label small fw-bold">Sekolah Tujuan Pindah</label>
                      <input type="text" name="sekolah_tujuan" class="form-control form-control-sm" value="{{ $item->payload_snapshot['sekolah_tujuan'] ?? '' }}" placeholder="Contoh: SMK Negeri 2 Bandar Lampung" />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label small fw-bold">Alasan Pindah / Mutasi</label>
                      <input type="text" name="alasan_mutasi" class="form-control form-control-sm" value="{{ $item->payload_snapshot['alasan_mutasi'] ?? '' }}" placeholder="Contoh: Mengikuti perpindahan orang tua" />
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
          <div class="modal-footer px-4 py-3 bg-light d-flex justify-content-end gap-2 border-top">
            <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-sm btn-warning fw-bold px-3">
              <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endforeach

{{-- Modal Terbitkan Surat Siswa Mandiri (Create) --}}
<div class="modal fade" id="modalBuatSuratSiswa" tabindex="-1" aria-labelledby="modalBuatSuratSiswaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow">
      <form action="{{ route('situan.pelayanan.buat') }}" method="POST">
        @csrf
        <div class="modal-header bg-light border-bottom px-4 py-3">
          <h5 class="modal-title fw-bold" id="modalBuatSuratSiswaLabel">
            <i class="bi bi-magic text-primary me-2"></i>One-Click Generator Surat Kesiswaan
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="alert alert-success py-2 px-3 small mb-3">
            <i class="bi bi-info-circle-fill me-1"></i> Sistem akan otomatis menarik biodata siswa dari pangkalan data sekolah, membuat nomor surat agenda resmi, dan menyematkan stempel QR-code verifikasi instan.
          </div>

          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label small fw-bold">Pilih Siswa <span class="text-danger">*</span></label>
              <select name="siswa_id" class="form-select form-select-sm select2-siswa" required style="width:100%;">
                <option value="">-- Ketik nama atau NISN siswa --</option>
                @foreach($allSiswaAktif as $s)
                  @php
                    $rombel = $s->siswaRombels->first()?->rombel?->nama_rombel ?? 'Belum ada kelas';
                  @endphp
                  <option value="{{ $s->id }}">
                    {{ $s->nama }} (NISN: {{ $s->nisn ?: '-' }} / Kelas: {{ $rombel }})
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label small fw-bold">Tanggal Surat <span class="text-danger">*</span></label>
              <input type="date" name="tanggal_surat" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required />
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-bold">Jenis Pelayanan Surat <span class="text-danger">*</span></label>
              <select name="jenis_pelayanan" id="jenisPelayananSelect" class="form-select form-select-sm" required onchange="toggleMutasiFields()">
                <option value="suket_aktif">Surat Keterangan Siswa Aktif</option>
                <option value="suket_berkelakuan_baik">Surat Keterangan Berkelakuan Baik</option>
                <option value="suket_mutasi_keluar">Surat Rekomendasi Pindah Sekolah (Mutasi)</option>
                <option value="suket_skl">Surat Keterangan Lulus Sementara (SKL)</option>
                <option value="suket_pengantar_pkl">Surat Pengantar PKL / Magang</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-bold">Keperluan Surat <span class="text-danger">*</span></label>
              <input type="text" name="keperluan" class="form-control form-control-sm" placeholder="Contoh: Persyaratan Beasiswa PIP / Daftar BPJS / Buka Rekening Bank" required />
            </div>

            {{-- Bidang Tambahan Khusus Mutasi --}}
            <div id="mutasiFields" class="col-12 d-none">
              <div class="p-3 bg-light rounded-3 border">
                <div class="fw-bold small text-warning-emphasis mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> Data Khusus Rekomendasi Pindah Sekolah:</div>
                <div class="row g-2">
                  <div class="col-md-6">
                    <label class="form-label small fw-bold">Sekolah Tujuan Pindah</label>
                    <input type="text" name="sekolah_tujuan" class="form-control form-control-sm" placeholder="Contoh: SMK Negeri 2 Bandar Lampung" />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label small fw-bold">Alasan Pindah / Mutasi</label>
                    <input type="text" name="alasan_mutasi" class="form-control form-control-sm" placeholder="Contoh: Mengikuti perpindahan domisili orang tua" />
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
        <div class="modal-footer px-4 py-3 bg-light d-flex justify-content-end gap-2 border-top">
          <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal" style="font-weight:600; border-radius:8px;">Batal</button>
          <button type="submit" class="btn btn-sm btn-primary fw-bold px-3" style="border-radius:8px;">
            <i class="bi bi-printer me-1"></i> Terbitkan &amp; Siapkan Cetak
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function toggleMutasiFields() {
  const jenis = document.getElementById('jenisPelayananSelect').value;
  const mutasiDiv = document.getElementById('mutasiFields');
  if (jenis === 'suket_mutasi_keluar') {
    mutasiDiv.classList.remove('d-none');
  } else {
    mutasiDiv.classList.add('d-none');
  }
}

function toggleEditMutasiFields(id) {
  const jenis = document.getElementById('editJenisSelect_' + id).value;
  const mutasiDiv = document.getElementById('editMutasiFields_' + id);
  if (mutasiDiv) {
    if (jenis === 'suket_mutasi_keluar') {
      mutasiDiv.classList.remove('d-none');
    } else {
      mutasiDiv.classList.add('d-none');
    }
  }
}

function hapusSuratPelayanan(id, nama) {
  if (confirm('Yakin ingin menghapus dokumen pelayanan surat untuk ' + nama + '? Surat keluar terkait juga akan dihapus dari agenda.')) {
    const form = document.getElementById('formHapusPelayanan_' + id);
    if (form) {
      form.submit();
    }
  }
}
</script>
@endsection
