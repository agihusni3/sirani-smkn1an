@extends('layouts.app')

@section('title', 'Loket Pelayanan Surat Siswa — SITUAN SMKN 1 AN')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">

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
  <div class="card border-0 shadow-sm rounded-3" style="background:var(--surface); border:1px solid var(--border)!important;">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
        <thead class="table-light" style="border-bottom:1.5px solid var(--border);">
          <tr>
            <th class="py-3 px-3" style="width:220px;">Nomor Agenda &amp; Surat</th>
            <th class="py-3 px-3" style="width:200px;">Nama Siswa / Rombel</th>
            <th class="py-3 px-3" style="width:170px;">Jenis Layanan</th>
            <th class="py-3 px-3">Keperluan / Keterangan</th>
            <th class="py-3 px-3" style="width:120px;">Tgl Terbit</th>
            <th class="py-3 px-3 text-center" style="width:160px;">Aksi</th>
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
                default => 'Suket Kesiswaan',
              };
              $rombelNama = $item->siswa?->siswaRombels?->first()?->rombel?->nama_rombel ?? ($item->payload_snapshot['rombel'] ?? '-');
            @endphp
            <tr>
              <td class="px-3">
                <div class="fw-bold font-monospace text-primary" style="font-size:12px;">{{ $item->suratKeluar?->nomor_surat_lengkap ?? 'No. Pending' }}</div>
                <small class="text-muted">Kode Klasifikasi: 422.4</small>
              </td>
              <td class="px-3">
                <div class="fw-bold text-dark">{{ $item->siswa?->nama ?? ($item->payload_snapshot['nama'] ?? 'Siswa') }}</div>
                <div class="text-secondary" style="font-size:11.5px;">
                  NISN: {{ $item->siswa?->nisn ?: '-' }} &bull; <span class="badge bg-light text-dark border">{{ $rombelNama }}</span>
                </div>
              </td>
              <td class="px-3">
                <span class="badge {{ $badgeClass }} px-2 py-1 rounded-pill" style="font-size:11px; font-weight:600;">
                  {{ $namaLayanan }}
                </span>
              </td>
              <td class="px-3">
                <div class="text-dark">{{ $item->keperluan }}</div>
                @if($item->jenis_pelayanan === 'suket_mutasi_keluar' && !empty($item->payload_snapshot['sekolah_tujuan']))
                  <div class="text-muted small">Tujuan: <strong>{{ $item->payload_snapshot['sekolah_tujuan'] }}</strong></div>
                @endif
              </td>
              <td class="px-3 text-muted">
                <i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y') }}
              </td>
              <td class="text-center px-3">
                <div class="btn-group btn-group-sm">
                  <a href="{{ route('situan.pelayanan.cetak', $item->id) }}" target="_blank" class="btn btn-primary" title="Cetak Surat Resmi A4">
                    <i class="bi bi-printer-fill me-1"></i> Cetak
                  </a>
                  <a href="{{ route('situan.verifikasi-surat', $item->kode_verifikasi_qr) }}" target="_blank" class="btn btn-outline-secondary" title="Cek Halaman Verifikasi QR Publik">
                    <i class="bi bi-qr-code-scan"></i>
                  </a>
                </div>
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

{{-- Modal Terbitkan Surat Siswa Mandiri --}}
<div class="modal fade" id="modalBuatSuratSiswa" tabindex="-1" aria-labelledby="modalBuatSuratSiswaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow">
      <form action="{{ route('situan.pelayanan.buat') }}" method="POST">
        @csrf
        <div class="modal-header border-bottom py-3 px-4">
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
        <div class="modal-footer border-top px-4 py-2">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-sm btn-primary fw-bold">
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
</script>
@endsection
