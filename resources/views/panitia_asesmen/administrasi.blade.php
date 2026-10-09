@extends('akademik.layout')

@section('title', 'Pusat Cetak Administrasi Ujian - Panitia Asesmen')
@section('breadcrumb', 'Administrasi Ujian CBT')

@section('content')
<div class="container-fluid py-3">
  {{-- Header --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-2 border-bottom">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('panitia-asesmen.dashboard') }}" class="btn btn-outline-secondary btn-sm p-2 rounded-circle">
        <i class="bi bi-arrow-left fs-6"></i>
      </a>
      <div>
        <h4 class="fw-bold mb-0 text-dark">Pusat Cetak Administrasi Ujian &amp; CBT</h4>
        <p class="text-muted small mb-0">Cetak massal Kartu Peserta Ujian, Daftar Hadir per Ruang/Sesi, dan Berita Acara Pelaksanaan.</p>
      </div>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('panitia-asesmen.control-room') }}" class="btn btn-outline-warning text-dark btn-sm px-3 shadow-sm d-flex align-items-center gap-2">
        <i class="bi bi-broadcast"></i>
        <span>Ruang Kendali CBT</span>
      </a>
    </div>
  </div>

  <div class="row g-4">
    {{-- 1. Kartu Peserta Ujian --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm rounded-3 h-100">
        <div class="card-header bg-primary text-white py-3">
          <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-person-badge-fill fs-5"></i>
            <span>1. Kartu Peserta Ujian</span>
          </h6>
        </div>
        <div class="card-body p-4 d-flex flex-column justify-content-between">
          <div>
            <p class="text-muted small mb-3">
              Mencetak kartu identitas peserta asesmen format A4 (grid kartu per lembar) lengkap dengan NISN, nama peserta, rombel, dan barcode verifikasi login CBT.
            </p>
            <form action="{{ route('panitia-asesmen.cetak-kartu') }}" method="GET" target="_blank">
              @if($periode)
                <input type="hidden" name="periode_id" value="{{ $periode->id }}">
              @endif
              <div class="mb-3">
                <label class="form-label small fw-bold text-dark">Pilih Rombongan Belajar (Kelas)</label>
                <select name="rombel_id" class="form-select form-select-sm">
                  <option value="">-- Cetak Semua Kelas / Rombel --</option>
                  @foreach($rombels as $r)
                    <option value="{{ $r->id }}">{{ $r->nama_rombel }} (Tingkat {{ $r->tingkat }})</option>
                  @endforeach
                </select>
              </div>
              <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-printer-fill"></i>
                <span>Cetak Kartu Peserta (A4)</span>
              </button>
            </form>
          </div>
          <div class="border-top pt-3 mt-3 text-muted small" style="font-size:11px;">
            <i class="bi bi-info-circle text-primary me-1"></i> Dibagikan kepada peserta sebelum ujian dimulai sebagai kartu tes resmi.
          </div>
        </div>
      </div>
    </div>

    {{-- 2. Daftar Hadir Peserta Ujian --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm rounded-3 h-100">
        <div class="card-header bg-success text-white py-3">
          <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-card-checklist fs-5"></i>
            <span>2. Daftar Hadir Peserta (Presensi)</span>
          </h6>
        </div>
        <div class="card-body p-4 d-flex flex-column justify-content-between">
          <div>
            <p class="text-muted small mb-3">
              Mencetak lembar daftar presensi peserta ujian per ruang dan per sesi dengan kolom tanda tangan dinas model ganjil-genap dan tanda tangan pengawas.
            </p>
            <form action="{{ route('panitia-asesmen.cetak-daftar-hadir') }}" method="GET" target="_blank">
              @if($periode)
                <input type="hidden" name="periode_id" value="{{ $periode->id }}">
              @endif
              <div class="mb-2">
                <label class="form-label small fw-bold text-dark">Rombel / Kelas</label>
                <select name="rombel_id" class="form-select form-select-sm">
                  <option value="">-- Semua Siswa --</option>
                  @foreach($rombels as $r)
                    <option value="{{ $r->id }}">{{ $r->nama_rombel }}</option>
                  @endforeach
                </select>
              </div>
              <div class="row g-2 mb-2">
                <div class="col-6">
                  <label class="form-label small fw-bold text-dark">Ruang Ujian</label>
                  <input type="text" name="ruang" class="form-control form-control-sm" value="Ruang Lab 01" placeholder="Lab 1 / R.01">
                </div>
                <div class="col-6">
                  <label class="form-label small fw-bold text-dark">Sesi Ujian</label>
                  <input type="text" name="sesi" class="form-control form-control-sm" value="Sesi 1 (07:30 - 09:30)">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label small fw-bold text-dark">Mata Pelajaran</label>
                <input type="text" name="mapel_nama" class="form-control form-control-sm" placeholder="Contoh: Matematika / Bahasa Indonesia">
              </div>
              <button type="submit" class="btn btn-success btn-sm w-100 fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-printer-fill"></i>
                <span>Cetak Daftar Hadir Ruang (A4)</span>
              </button>
            </form>
          </div>
          <div class="border-top pt-3 mt-3 text-muted small" style="font-size:11px;">
            <i class="bi bi-info-circle text-success me-1"></i> Diletakkan di meja pengawas ruang ujian untuk absensi fisik peserta.
          </div>
        </div>
      </div>
    </div>

    {{-- 3. Berita Acara Pelaksanaan Asesmen --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm rounded-3 h-100">
        <div class="card-header bg-dark text-white py-3">
          <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-text-fill fs-5"></i>
            <span>3. Berita Acara Pelaksanaan</span>
          </h6>
        </div>
        <div class="card-body p-4 d-flex flex-column justify-content-between">
          <div>
            <p class="text-muted small mb-3">
              Mencetak form berita acara ujian resmi untuk mencatat jumlah kehadiran, ketidakhadiran, catatan insiden/kendala teknis, serta pengesahan Proktor &amp; Pengawas.
            </p>
            <form action="{{ route('panitia-asesmen.cetak-berita-acara') }}" method="GET" target="_blank">
              @if($periode)
                <input type="hidden" name="periode_id" value="{{ $periode->id }}">
              @endif
              <div class="row g-2 mb-2">
                <div class="col-6">
                  <label class="form-label small fw-bold text-dark">Ruang Ujian</label>
                  <input type="text" name="ruang" class="form-control form-control-sm" value="Ruang Lab 01">
                </div>
                <div class="col-6">
                  <label class="form-label small fw-bold text-dark">Sesi Ujian</label>
                  <input type="text" name="sesi" class="form-control form-control-sm" value="Sesi 1 (07:30 - 09:30)">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label small fw-bold text-dark">Mata Pelajaran Asesmen</label>
                <input type="text" name="mapel_nama" class="form-control form-control-sm" placeholder="Contoh: Dasar-dasar Kejuruan TJKT">
              </div>
              <button type="submit" class="btn btn-dark btn-sm w-100 fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-printer-fill"></i>
                <span>Cetak Berita Acara (A4)</span>
              </button>
            </form>
          </div>
          <div class="border-top pt-3 mt-3 text-muted small" style="font-size:11px;">
            <i class="bi bi-info-circle text-dark me-1"></i> Wajib diisi dan ditandatangani pengawas pada setiap akhir sesi ujian.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
