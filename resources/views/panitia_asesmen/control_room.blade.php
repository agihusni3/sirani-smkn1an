@extends('akademik.layout')

@section('title', 'Ruang Kendali CBT & Token - Panitia Asesmen')
@section('breadcrumb', 'Ruang Kendali CBT & Token')

@section('content')
<div class="container-fluid py-3">
  {{-- Header --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-2 border-bottom">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('panitia-asesmen.dashboard') }}" class="btn btn-outline-secondary btn-sm p-2 rounded-circle">
        <i class="bi bi-arrow-left fs-6"></i>
      </a>
      <div>
        <h4 class="fw-bold mb-0 text-dark">Ruang Kendali CBT &amp; Token Ujian</h4>
        <p class="text-muted small mb-0">Rilis token harian/sesi, kontrol status aktif ujian di lab, dan pemulihan sesi login siswa.</p>
      </div>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('panitia-asesmen.administrasi') }}" class="btn btn-outline-primary btn-sm px-3 shadow-sm d-flex align-items-center gap-2">
        <i class="bi bi-printer-fill"></i>
        <span>Cetak Administrasi</span>
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

  {{-- Panel Rilis Token CBT --}}
  <div class="card border-0 bg-dark text-white shadow-sm rounded-3 mb-4 p-4">
    <div class="row align-items-center">
      <div class="col-md-7">
        <div class="d-flex align-items-center gap-2 mb-2">
          <span class="badge bg-danger text-white text-uppercase px-2.5 py-1">LIVE TOKEN CBT</span>
          <span class="text-white-50 small">&bull; Digunakan peserta untuk memulai ujian sesi aktif</span>
        </div>
        <h5 class="fw-bold text-white mb-1">Token Ujian Saat Ini:</h5>
        <div class="display-3 fw-bold font-monospace text-warning letter-spacing-2 my-2">
          {{ $currentToken }}
        </div>
        <p class="text-white-50 small mb-0">
          Peserta wajib memasukkan token ini saat menekan tombol "Mulai Ujian" di perangkat masing-masing.
        </p>
      </div>
      <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <form action="{{ route('panitia-asesmen.generate-token') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin merilis Token CBT baru? Token lama tidak akan berlaku lagi.');">
          @csrf
          <button type="submit" class="btn btn-warning text-dark fw-bold btn-lg px-4 shadow py-3 d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-repeat fs-4"></i>
            <span>Rilis / Perbarui Token Baru</span>
          </button>
        </form>
        <div class="text-white-50 small mt-2">Token diperbarui secara terpusat untuk seluruh ruang ujian.</div>
      </div>
    </div>
  </div>

  {{-- Tabel Kendali Ujian Online --}}
  <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
      <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
        <i class="bi bi-sliders text-primary"></i>
        <span>Daftar Ujian &amp; Kontrol Akses Peserta ({{ $asesmens->total() }} Ujian)</span>
      </h6>
      <span class="text-muted small">Aktifkan saat sesi dimulai, tutup setelah waktu selesai</span>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:13px;">
        <thead class="table-light text-muted fw-bold">
          <tr>
            <th class="ps-3 py-3" style="width:50px;">No</th>
            <th>Mata Pelajaran &amp; Judul Ujian</th>
            <th>Guru Pembuat</th>
            <th class="text-center">Durasi / Passing</th>
            <th class="text-center">Soal / Peserta</th>
            <th class="text-center">Status Akses Lab</th>
            <th class="text-end pe-3">Aksi Kontrol</th>
          </tr>
        </thead>
        <tbody>
          @forelse($asesmens as $idx => $a)
          <tr>
            <td class="ps-3 fw-bold text-muted">{{ $asesmens->firstItem() + $idx }}</td>
            <td>
              <div class="fw-bold text-dark fs-6">{{ $a->judul }}</div>
              <div class="text-muted small">
                Mapel: <strong>{{ $a->distribusi->mataPelajaran->nama_mapel ?? '-' }}</strong> &bull; 
                Jenis: <span class="badge bg-light text-dark border">{{ strtoupper($a->jenis) }}</span>
              </div>
            </td>
            <td>
              <div>{{ $a->distribusi->guru->nama ?? '-' }}</div>
            </td>
            <td class="text-center">
              <div>{{ $a->durasi_menit }} Menit</div>
              <div class="text-muted small">KKM: {{ $a->passing_grade }}</div>
            </td>
            <td class="text-center">
              <div><strong class="text-primary">{{ $a->soals_count }}</strong> Butir Soal</div>
              <div class="text-muted small"><strong class="text-success">{{ $a->hasils_count }}</strong> Mengerjakan</div>
            </td>
            <td class="text-center">
              @if($a->is_active)
                <span class="badge bg-success px-2.5 py-1">
                  <i class="bi bi-unlock-fill me-1"></i> DIBUKA (AKTIF)
                </span>
              @else
                <span class="badge bg-secondary px-2.5 py-1">
                  <i class="bi bi-lock-fill me-1"></i> TERKUNCI (DITUTUP)
                </span>
              @endif
            </td>
            <td class="text-end pe-3">
              <div class="d-inline-flex gap-1">
                {{-- Toggle Status Ujian --}}
                <form action="{{ route('panitia-asesmen.toggle-ujian', $a->id) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-sm {{ $a->is_active ? 'btn-outline-danger' : 'btn-success' }}" title="{{ $a->is_active ? 'Tutup Ujian Ini' : 'Buka Ujian Ini' }}">
                    <i class="bi {{ $a->is_active ? 'bi-lock-fill' : 'bi-unlock-fill' }}"></i>
                    <span>{{ $a->is_active ? 'Kunci Ujian' : 'Buka Ujian' }}</span>
                  </button>
                </form>

                <a href="{{ route('akademik.asesmen.hasil', $a->id) }}" class="btn btn-sm btn-outline-info" title="Pantau Hasil & Reset Peserta">
                  <i class="bi bi-bar-chart-fill"></i> Hasil &amp; Reset
                </a>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">Belum ada asesmen yang terdata.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($asesmens->hasPages())
    <div class="p-3 border-top">
      {{ $asesmens->links() }}
    </div>
    @endif
  </div>
</div>
@endsection
