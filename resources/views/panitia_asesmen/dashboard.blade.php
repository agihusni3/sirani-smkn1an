@extends('akademik.layout')

@section('title', 'Workspace Panitia Asesmen & CBT')
@section('breadcrumb', 'Workspace Panitia CBT')

@section('content')
<div class="container-fluid py-3">
  {{-- Header & Periode Switcher --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-2 border-bottom">
    <div>
      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success text-white" style="font-size:11px; padding:4px 8px; border-radius:6px;">
          <i class="bi bi-shield-check"></i> Ruang Panitia Asesmen
        </span>
        @if($myPanitia)
          <span class="badge bg-primary text-white" style="font-size:11px; padding:4px 8px; border-radius:6px;">
            Peran Anda: {{ $myPanitia->peran_label }}
          </span>
        @endif
      </div>
      <h4 class="fw-bold mb-0 text-dark mt-1">Pusat Komando Operasional Asesmen &amp; CBT</h4>
      <p class="text-muted small mb-0">Pengelolaan teknis ujian sekolah, rilis token pengerjaan, monitoring ruang lab, dan administrasi naskah.</p>
    </div>

    {{-- Switcher Event Asesmen --}}
    <div class="d-flex align-items-center gap-2">
      <form action="{{ route('panitia-asesmen.dashboard') }}" method="GET" class="d-flex align-items-center gap-2">
        <label class="small text-muted fw-bold text-nowrap">Pilih Event:</label>
        <select name="periode_id" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width:240px;">
          @foreach($periodes as $p)
            <option value="{{ $p->id }}" {{ $periode && $periode->id == $p->id ? 'selected' : '' }}>
              {{ $p->nama_event }} ({{ ucfirst($p->status) }})
            </option>
          @endforeach
        </select>
      </form>
      <a href="{{ route('akademik.kepanitiaan.index') }}" class="btn btn-outline-secondary btn-sm" title="Kembali ke Waka Kurikulum">
        <i class="bi bi-gear-fill"></i> SK Kurikulum
      </a>
    </div>
  </div>

  @if($periode)
  {{-- Card Status Event Asesmen Aktif --}}
  <div class="card border-0 bg-primary text-white shadow-sm rounded-3 mb-4 p-4 position-relative overflow-hidden">
    <div class="position-absolute end-0 top-0 opacity-10 p-4 pe-5">
      <i class="bi bi-laptop-fill" style="font-size: 130px;"></i>
    </div>
    <div class="position-relative">
      <span class="badge bg-white text-primary fw-bold text-uppercase px-2.5 py-1 mb-2">Event Aktif</span>
      <h3 class="fw-bold mb-1">{{ $periode->nama_event }}</h3>
      <p class="text-white-50 mb-3" style="max-width: 650px;">
        {{ $periode->jenis_label }} &bull; SK Kepanitiaan: <strong>{{ $periode->sk_nomor ?? 'Proses Penerbitan' }}</strong> &bull; Jadwal: {{ $periode->tanggal_mulai->format('d/m/Y') }} s/d {{ $periode->tanggal_selesai->format('d/m/Y') }}
      </p>

      <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('panitia-asesmen.control-room', ['periode_id' => $periode->id]) }}" class="btn btn-warning text-dark fw-bold btn-sm px-3 shadow-sm d-flex align-items-center gap-2">
          <i class="bi bi-broadcast-pin"></i>
          <span>Buka Ruang Kendali CBT &amp; Token</span>
        </a>
        <a href="{{ route('panitia-asesmen.administrasi', ['periode_id' => $periode->id]) }}" class="btn btn-light btn-sm px-3 shadow-sm d-flex align-items-center gap-2">
          <i class="bi bi-printer-fill text-primary"></i>
          <span>Cetak Dokumen &amp; Kartu Peserta</span>
        </a>
        <a href="{{ route('akademik.bank_soal.index') }}" class="btn btn-outline-light btn-sm px-3 d-flex align-items-center gap-2">
          <i class="bi bi-folder2-open"></i>
          <span>Bank Soal Disetor Guru</span>
        </a>
      </div>
    </div>
  </div>
  @endif

  {{-- Metrik Ringkasan Ujian --}}
  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-3 p-3">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted small fw-bold text-uppercase">Peserta Siswa Aktif</div>
            <h4 class="fw-bold text-dark mb-0 mt-1">{{ number_format($totalSiswa) }}</h4>
          </div>
          <div class="p-3 bg-primary-subtle text-primary rounded-circle">
            <i class="bi bi-people-fill fs-4"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-3 p-3">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted small fw-bold text-uppercase">Naskah Ujian Dibuat</div>
            <h4 class="fw-bold text-dark mb-0 mt-1">{{ number_format($totalUjian) }}</h4>
          </div>
          <div class="p-3 bg-info-subtle text-info rounded-circle">
            <i class="bi bi-journal-check fs-4"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-3 p-3">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted small fw-bold text-uppercase">Ujian Terbuka (Aktif)</div>
            <h4 class="fw-bold text-success mb-0 mt-1">{{ number_format($ujianAktif) }}</h4>
          </div>
          <div class="p-3 bg-success-subtle text-success rounded-circle">
            <i class="bi bi-unlock-fill fs-4"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-3 p-3">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted small fw-bold text-uppercase">Lembar Jawaban Masuk</div>
            <h4 class="fw-bold text-dark mb-0 mt-1">{{ number_format($totalHasil) }}</h4>
          </div>
          <div class="p-3 bg-warning-subtle text-warning rounded-circle">
            <i class="bi bi-clipboard2-data-fill fs-4"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Menu Navigasi Modul Panitia & Ujian Terbaru --}}
  <div class="row g-4">
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
          <i class="bi bi-grid-fill text-primary"></i>
          <span>Menu Kerja Khusus Panitia Asesmen</span>
        </h6>
        <div class="list-group list-group-flush">
          <a href="{{ route('panitia-asesmen.control-room') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3">
            <div class="d-flex align-items-center gap-3">
              <div class="p-2 bg-primary-subtle text-primary rounded-3">
                <i class="bi bi-broadcast fs-5"></i>
              </div>
              <div>
                <div class="fw-bold text-dark">Ruang Kendali CBT &amp; Token</div>
                <div class="text-muted small">Rilis token 6-digit, aktifkan/tutup ujian, reset login siswa.</div>
              </div>
            </div>
            <i class="bi bi-chevron-right text-muted"></i>
          </a>

          <a href="{{ route('panitia-asesmen.administrasi') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3">
            <div class="d-flex align-items-center gap-3">
              <div class="p-2 bg-success-subtle text-success rounded-3">
                <i class="bi bi-printer-fill fs-5"></i>
              </div>
              <div>
                <div class="fw-bold text-dark">Pusat Cetak Administrasi Ujian</div>
                <div class="text-muted small">Kartu peserta, daftar hadir pengawas/ruang, berita acara.</div>
              </div>
            </div>
            <i class="bi bi-chevron-right text-muted"></i>
          </a>

          <a href="{{ route('akademik.bank_soal.index') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3">
            <div class="d-flex align-items-center gap-3">
              <div class="p-2 bg-warning-subtle text-warning rounded-3">
                <i class="bi bi-folder-check fs-5"></i>
              </div>
              <div>
                <div class="fw-bold text-dark">Verifikasi Bank Soal Guru</div>
                <div class="text-muted small">Periksa butir naskah soal yang disetor guru per mata pelajaran.</div>
              </div>
            </div>
            <i class="bi bi-chevron-right text-muted"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
          <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-secondary"></i>
            <span>Naskah Asesmen Terbaru</span>
          </h6>
          <a href="{{ route('panitia-asesmen.control-room') }}" class="small text-primary fw-bold text-decoration-none">Lihat Semua di Control Room &rarr;</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" style="font-size:13px;">
            <thead class="table-light text-muted">
              <tr>
                <th class="ps-3">Mata Pelajaran &amp; Judul</th>
                <th>Guru Pengampu</th>
                <th class="text-center">Status CBT</th>
                <th class="text-end pe-3">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentAsesmens as $a)
              <tr>
                <td class="ps-3">
                  <div class="fw-bold text-dark">{{ $a->judul }}</div>
                  <div class="text-muted small">{{ $a->distribusi->mataPelajaran->nama_mapel ?? '-' }}</div>
                </td>
                <td>
                  <div>{{ $a->distribusi->guru->nama ?? '-' }}</div>
                </td>
                <td class="text-center">
                  @if($a->is_active)
                    <span class="badge bg-success">Dibuka / Aktif</span>
                  @else
                    <span class="badge bg-secondary">Terkunci / Ditutup</span>
                  @endif
                </td>
                <td class="text-end pe-3">
                  <a href="{{ route('akademik.asesmen.hasil', $a->id) }}" class="btn btn-sm btn-outline-info" title="Lihat Hasil Peserta">
                    <i class="bi bi-bar-chart-fill"></i> Hasil
                  </a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted">Belum ada naskah asesmen yang dibuat.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
