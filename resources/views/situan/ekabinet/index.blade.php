@extends('layouts.app')

@section('title', 'E-Kabinet & E-Arsip Digital — SITUAN SMKN 1 AN')

@push('styles')
<style>
  .searchable-select {
    position: relative;
  }
  .searchable-trigger {
    cursor: pointer;
    background-color: #ffffff;
    user-select: none;
    min-height: 40px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 8px 12px;
    transition: all .15s ease;
  }
  .searchable-trigger:hover, .searchable-trigger:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
  }
  .searchable-menu {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    z-index: 1070;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    box-shadow: 0 12px 28px -5px rgba(0, 0, 0, 0.22);
    overflow: hidden;
  }
  .searchable-item {
    cursor: pointer;
    transition: background 0.15s ease;
    border-bottom: 1px solid #f1f5f9;
  }
  .searchable-item:last-child {
    border-bottom: none;
  }
  .searchable-item:hover, .searchable-item.active {
    background-color: #f0fdf4;
  }
  .searchable-item.selected {
    background-color: #e0f2fe;
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">

  @php
    $headerConfigs = [
      'siswa' => [
        'badge' => 'E-Kabinet Siswa',
        'badge_class' => 'bg-success-subtle text-success border-success-subtle',
        'title' => 'E-Kabinet Digital Peserta Didik',
        'desc' => 'Penyimpanan terpadu berkas resmi siswa (Ijazah SMP, Akta, KK, Rapor, KIP/PIP, dan sinkronisasi PPDB).',
      ],
      'ptk' => [
        'badge' => 'E-Kabinet PTK',
        'badge_class' => 'bg-primary-subtle text-primary border-primary-subtle',
        'title' => 'E-Kabinet Digital PTK & Kepegawaian',
        'desc' => 'Penyimpanan terpusat dokumen kepegawaian Guru dan Tenaga Kependidikan (SK Pangkat, KGB, Serdik, Karpeg).',
      ],
      'lembaga' => [
        'badge' => 'E-Kabinet Lembaga',
        'badge_class' => 'bg-secondary-subtle text-dark border-secondary-subtle',
        'title' => 'E-Kabinet Dokumen Lembaga & Legalitas',
        'desc' => 'Penyimpanan arsip institusi (Akreditasi BAN-SM, Izin Operasional, Sertifikat Tanah/Aset, KOSP, SOP, SK Kelembagaan).',
      ],
      'mou' => [
        'badge' => 'E-Kabinet MoU',
        'badge_class' => 'bg-warning-subtle text-warning border-warning-subtle',
        'title' => 'E-Kabinet MoU & Kemitraan DUDI',
        'desc' => 'Sentral dokumen Perjanjian Kerjasama (PKS) dan MoU kemitraan industri, dunia usaha/kerja, serta magang PKL.',
      ],
      'kelengkapan' => [
        'badge' => 'Radar Kelengkapan',
        'badge_class' => 'bg-info-subtle text-info border-info-subtle',
        'title' => 'Radar Kelengkapan Berkas Administrasi Guru',
        'desc' => 'Monitoring kepatuhan dan matriks pemenuhan berkas kepegawaian seluruh GTK SMKN 1 Air Naningan.',
      ],
    ];
    $activeHeader = $headerConfigs[$activeTab] ?? $headerConfigs['siswa'];
  @endphp

  {{-- Page Header --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
      <div class="d-flex align-items-center gap-2 mb-1">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill" style="font-size:11px; font-weight:700;">
          <i class="bi bi-archive-fill me-1"></i> E-Kabinet Digital Terpusat
        </span>
        <span class="badge {{ $activeHeader['badge_class'] }} px-2 py-1 rounded-pill" style="font-size:11px; font-weight:700;">
          {{ $activeHeader['badge'] }}
        </span>
        <span class="text-muted" style="font-size:12px;">SITUAN SMKN 1 Air Naningan</span>
      </div>
      <h1 class="h3 mb-0 fw-bold" style="color:var(--text); letter-spacing:-0.02em;">Lemari Arsip &amp; E-Kabinet Sekolah</h1>
      <p class="text-muted mb-0 small">{{ $activeHeader['desc'] }}</p>
    </div>

    <div class="d-flex gap-2 flex-wrap align-items-center">
      <a href="{{ route('situan.index') }}" class="btn btn-outline-secondary btn-sm px-3 d-inline-flex align-items-center" style="font-weight:700; height:36px;">
        <i class="bi bi-arrow-left me-1.5"></i> Dasbor SITUAN
      </a>

      @if($activeTab === 'siswa')
        <button type="button" class="btn btn-success btn-sm px-3 d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalUploadArsipSiswa" style="font-weight:700; height:36px;">
          <i class="bi bi-mortarboard-fill me-1.5"></i> Unggah Berkas Siswa
        </button>
        @if(!empty($ppdbReadyCount) && $ppdbReadyCount > 0)
          <button type="button" class="btn btn-outline-info btn-sm px-3 d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalSyncPpdb" style="font-weight:700; height:36px;">
            <i class="bi bi-arrow-repeat me-1.5"></i> Tarik PPDB <span class="badge bg-info text-dark ms-1.5">{{ $ppdbReadyCount }}</span>
          </button>
        @endif
      @elseif($activeTab === 'ptk' || $activeTab === 'kelengkapan')
        <button type="button" class="btn btn-primary btn-sm px-3 d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalUploadArsipPtk" style="font-weight:700; height:36px;">
          <i class="bi bi-person-badge-fill me-1.5"></i> Unggah Berkas PTK
        </button>
      @elseif($activeTab === 'lembaga')
        <button type="button" class="btn btn-dark btn-sm px-3 d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalUploadArsipLembaga" style="font-weight:700; height:36px;">
          <i class="bi bi-building-fill-add me-1.5"></i> Unggah Dokumen Lembaga
        </button>
      @elseif($activeTab === 'mou')
        <button type="button" class="btn btn-warning btn-sm px-3 d-inline-flex align-items-center text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#modalUploadArsipMou" style="height:36px;">
          <i class="bi bi-briefcase-fill me-1.5"></i> Unggah Dokumen MoU
        </button>
      @endif
    </div>
  </div>

  {{-- Quick Metric Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <a href="{{ route('situan.ekabinet.siswa') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm rounded-3 p-3 {{ $activeTab === 'siswa' ? 'border-success' : '' }}" style="background:var(--surface); border:1px solid var(--border)!important;">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="text-muted small fw-semibold">Total Arsip Siswa</span>
            <i class="bi bi-mortarboard-fill text-success"></i>
          </div>
          <div class="h3 mb-0 fw-bolder text-success">{{ $totalArsipSiswa }}</div>
          <div class="text-muted" style="font-size:11px;">{{ $totalSiswaWithArsip }} siswa terdata</div>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="{{ route('situan.ekabinet.ptk') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm rounded-3 p-3 {{ $activeTab === 'ptk' ? 'border-primary' : '' }}" style="background:var(--surface); border:1px solid var(--border)!important;">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="text-muted small fw-semibold">Total Arsip PTK</span>
            <i class="bi bi-person-badge-fill text-primary"></i>
          </div>
          <div class="h3 mb-0 fw-bolder text-primary">{{ $totalArsipPtk }}</div>
          <div class="text-muted" style="font-size:11px;">{{ $totalGuruWithArsip }} dari {{ $gurus->count() }} Guru terarsip</div>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="{{ route('situan.ekabinet.lembaga') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm rounded-3 p-3 {{ $activeTab === 'lembaga' ? 'border-dark' : '' }}" style="background:var(--surface); border:1px solid var(--border)!important;">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="text-muted small fw-semibold">Arsip Lembaga &amp; Aset</span>
            <i class="bi bi-building text-dark"></i>
          </div>
          <div class="h3 mb-0 fw-bolder text-dark">{{ $totalArsipLembaga }}</div>
          <div class="text-muted" style="font-size:11px;">Akreditasi, SK, &amp; Aset Sekolah</div>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="{{ route('situan.ekabinet.mou') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm rounded-3 p-3 {{ $activeTab === 'mou' ? 'border-warning' : '' }}" style="background:var(--surface); border:1px solid var(--border)!important;">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="text-muted small fw-semibold">MoU Industri Aktif</span>
            <i class="bi bi-briefcase-fill text-warning"></i>
          </div>
          <div class="h3 mb-0 fw-bolder text-warning">{{ $totalArsipMou }}</div>
          <div class="text-muted" style="font-size:11px;">{{ $totalMouAktif }} Kerjasama Aktif</div>
        </div>
      </a>
    </div>
  </div>

  {{-- Navigation Tabs --}}
  <ul class="nav nav-pills mb-3 gap-2 flex-wrap" id="ekabinetTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link {{ $activeTab === 'siswa' ? 'active bg-success text-white' : '' }} fw-bold" href="{{ route('situan.ekabinet.siswa') }}">
        <i class="bi bi-mortarboard-fill me-1"></i> E-Kabinet Siswa (Peserta Didik)
        <span class="visually-hidden">Laci Arsip Siswa (Peserta Didik)</span>
        <span class="badge {{ $activeTab === 'siswa' ? 'bg-light text-success' : 'bg-success-subtle text-success border border-success-subtle' }} ms-1">{{ $totalArsipSiswa }}</span>
      </a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link {{ $activeTab === 'ptk' ? 'active bg-primary text-white' : '' }} fw-bold" href="{{ route('situan.ekabinet.ptk') }}">
        <i class="bi bi-person-badge-fill me-1"></i> E-Kabinet PTK &amp; Kepegawaian
        <span class="visually-hidden">Laci Arsip PTK &amp; Kepegawaian</span>
        <span class="badge {{ $activeTab === 'ptk' ? 'bg-light text-primary' : 'bg-primary-subtle text-primary border border-primary-subtle' }} ms-1">{{ $totalArsipPtk }}</span>
      </a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link {{ $activeTab === 'lembaga' ? 'active bg-dark text-white' : '' }} fw-bold" href="{{ route('situan.ekabinet.lembaga') }}">
        <i class="bi bi-building me-1"></i> E-Kabinet Lembaga &amp; Aset
        <span class="badge {{ $activeTab === 'lembaga' ? 'bg-light text-dark' : 'bg-secondary-subtle text-dark border' }} ms-1">{{ $totalArsipLembaga }}</span>
      </a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link {{ $activeTab === 'mou' ? 'active bg-warning text-dark' : '' }} fw-bold" href="{{ route('situan.ekabinet.mou') }}">
        <i class="bi bi-briefcase-fill me-1"></i> E-Kabinet MoU Kemitraan DUDI
        <span class="visually-hidden">Laci Arsip Lembaga &amp; MoU DUDI</span>
        <span class="badge {{ $activeTab === 'mou' ? 'bg-dark text-warning' : 'bg-warning-subtle text-warning border border-warning-subtle' }} ms-1">{{ $totalArsipMou }}</span>
      </a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link {{ $activeTab === 'kelengkapan' ? 'active bg-info text-dark' : '' }} fw-bold" href="{{ route('situan.ekabinet.index', ['tab' => 'kelengkapan']) }}">
        <i class="bi bi-radar me-1"></i> Radar Kelengkapan Berkas Guru
      </a>
    </li>
  </ul>

  {{-- Tab 1: Lemari Berkas PTK --}}
  @if($activeTab === 'ptk')
    <div class="card border-0 shadow-sm rounded-3 mb-4" style="background:var(--surface); border:1px solid var(--border)!important;">
      <div class="card-body p-3">
        <form method="GET" action="{{ route('situan.ekabinet.index') }}" class="row g-2 align-items-center">
          <input type="hidden" name="tab" value="ptk">
          <div class="col-md-4 col-12">
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
              <input type="text" name="q_ptk" value="{{ request('q_ptk') }}" class="form-control border-start-0" placeholder="Cari nama berkas, nomor SK, atau PTK...">
            </div>
          </div>
          <div class="col-md-3 col-6">
            <select name="guru_id" class="form-select form-select-sm">
              <option value="">— Semua Guru / PTK —</option>
              @foreach($gurus as $g)
                <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 col-6">
            <select name="kategori_ptk" class="form-select form-select-sm">
              <option value="">— Semua Kategori Berkas —</option>
              <option value="sk_pangkat_terakhir" {{ request('kategori_ptk') == 'sk_pangkat_terakhir' ? 'selected' : '' }}>SK Pangkat Terakhir</option>
              <option value="sk_kgb_terakhir" {{ request('kategori_ptk') == 'sk_kgb_terakhir' ? 'selected' : '' }}>SK KGB Terakhir</option>
              <option value="sk_cpns" {{ request('kategori_ptk') == 'sk_cpns' ? 'selected' : '' }}>SK CPNS</option>
              <option value="sk_pns" {{ request('kategori_ptk') == 'sk_pns' ? 'selected' : '' }}>SK PNS</option>
              <option value="sk_pppk" {{ request('kategori_ptk') == 'sk_pppk' ? 'selected' : '' }}>SK PPPK</option>
              <option value="ijazah" {{ request('kategori_ptk') == 'ijazah' ? 'selected' : '' }}>Ijazah Pendidikan</option>
              <option value="transkrip" {{ request('kategori_ptk') == 'transkrip' ? 'selected' : '' }}>Transkrip Nilai</option>
              <option value="sertifikat_pendidik" {{ request('kategori_ptk') == 'sertifikat_pendidik' ? 'selected' : '' }}>Sertifikat Pendidik (Serdik)</option>
              <option value="sertifikat_pelatihan" {{ request('kategori_ptk') == 'sertifikat_pelatihan' ? 'selected' : '' }}>Sertifikat Pelatihan / Diklat</option>
              <option value="sk_penugasan_sekolah" {{ request('kategori_ptk') == 'sk_penugasan_sekolah' ? 'selected' : '' }}>SK Penugasan / Kolektif Sekolah</option>
              <option value="kartu_pegawai" {{ request('kategori_ptk') == 'kartu_pegawai' ? 'selected' : '' }}>Karpeg / KPE</option>
              <option value="ktp" {{ request('kategori_ptk') == 'ktp' ? 'selected' : '' }}>KTP Elektronik</option>
              <option value="kk" {{ request('kategori_ptk') == 'kk' ? 'selected' : '' }}>Kartu Keluarga</option>
              <option value="lainnya" {{ request('kategori_ptk') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
          </div>
          <div class="col-md-2 col-12 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">Filter</button>
            <a href="{{ route('situan.ekabinet.index', ['tab' => 'ptk']) }}" class="btn btn-sm btn-outline-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
          </div>
        </form>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
          <thead class="table-light" style="border-bottom:1.5px solid var(--border);">
            <tr>
              <th class="py-3 px-3" style="width:200px;">Nama PTK</th>
              <th class="py-3 px-3" style="width:160px;">Kategori Berkas</th>
              <th class="py-3 px-3">Nama &amp; Keterangan Dokumen</th>
              <th class="py-3 px-3" style="width:180px;">Nomor Dokumen</th>
              <th class="py-3 px-3" style="width:120px;">Tgl Dokumen</th>
              <th class="py-3 px-3 text-center" style="width:140px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($arsipPtks as $arsip)
              @php
                $badgeCat = match($arsip->kategori_berkas) {
                  'sk_pangkat_terakhir', 'sk_kgb_terakhir' => 'bg-danger-subtle text-danger border border-danger-subtle',
                  'sk_cpns', 'sk_pns', 'sk_pppk' => 'bg-primary-subtle text-primary border border-primary-subtle',
                  'sk_penugasan_sekolah' => 'bg-info-subtle text-dark border border-info-subtle',
                  'ijazah', 'transkrip', 'sertifikat_pendidik' => 'bg-success-subtle text-success border border-success-subtle',
                  'sertifikat_pelatihan' => 'bg-warning-subtle text-dark border border-warning-subtle',
                  'ktp', 'kk', 'kartu_pegawai' => 'bg-info-subtle text-info border border-info-subtle',
                  default => 'bg-secondary-subtle text-secondary',
                };
                $namaKategori = match($arsip->kategori_berkas) {
                  'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
                  'sk_kgb_terakhir'     => 'SK KGB Terakhir',
                  'sk_cpns'             => 'SK CPNS',
                  'sk_pns'              => 'SK PNS Definitif',
                  'sk_pppk'             => 'SK PPPK',
                  'sk_penugasan_sekolah'=> 'SK Kolektif Sekolah',
                  'ijazah'              => 'Ijazah Pendidikan',
                  'transkrip'           => 'Transkrip Nilai',
                  'sertifikat_pendidik' => 'Sertifikat Pendidik (Serdik)',
                  'sertifikat_pelatihan'=> 'Sertifikat Pelatihan / Diklat',
                  'kartu_pegawai'       => 'Karpeg / KPE',
                  'ktp'                 => 'KTP Elektronik',
                  'kk'                  => 'Kartu Keluarga',
                  default               => 'Dokumen Lainnya',
                };
              @endphp
              <tr>
                <td class="px-3">
                  <div class="fw-bold text-dark">{{ $arsip->guru?->nama ?? 'PTK Tidak Ditemukan' }}</div>
                  <div class="text-muted" style="font-size:11px;">NIP: {{ $arsip->guru?->nip ?: '-' }}</div>
                </td>
                <td class="px-3">
                  <span class="badge {{ $badgeCat }} px-2 py-1 rounded-pill" style="font-size:11px;">
                    {{ $namaKategori }}
                  </span>
                </td>
                <td class="px-3">
                  <div class="fw-bold text-dark">{{ $arsip->nama_dokumen }}</div>
                  <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                    @if($arsip->buku_sk_id)
                      <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0 rounded-pill" style="font-size:10px;">
                        <i class="bi bi-link-45deg"></i> SK Kolektif Kepsek
                      </span>
                    @endif
                    <span class="text-muted" style="font-size:11px;">Diunggah: {{ $arsip->created_at->format('d/m/Y H:i') }}</span>
                  </div>
                </td>
                <td class="px-3 text-muted">
                  {{ $arsip->nomor_dokumen ?: '-' }}
                </td>
                <td class="px-3 text-muted">
                  {{ $arsip->tanggal_dokumen ? $arsip->tanggal_dokumen->format('d/m/Y') : '-' }}
                </td>
                <td class="px-3 text-center">
                  <div class="btn-group btn-group-sm">
                    @if($arsip->file_path)
                      <a href="{{ asset('storage/' . $arsip->file_path) }}" target="_blank" class="btn btn-outline-primary" title="Lihat / Unduh Dokumen">
                        <i class="bi bi-eye-fill"></i>
                      </a>
                    @endif
                    <a href="{{ route('situan.arsip-ptk.index', $arsip->guru_id) }}" class="btn btn-outline-secondary" title="Buka Lemari Pribadi PTK">
                      <i class="bi bi-folder2-open"></i>
                    </a>
                    <form action="{{ route('situan.ekabinet.ptk.destroy', $arsip->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus dokumen ini dari E-Kabinet?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-outline-danger" title="Hapus Dokumen">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                  <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                  Belum ada berkas PTK yang cocok dengan filter pencarian.<br>
                  <button type="button" class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#modalUploadArsipPtk">
                    <i class="bi bi-cloud-arrow-up-fill me-1"></i> Unggah Berkas PTK Baru
                  </button>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($arsipPtks->hasPages())
        <div class="card-footer bg-transparent py-3 border-0">
          {{ $arsipPtks->links() }}
        </div>
      @endif
    </div>
  @endif

  {{-- Tab 2: Laci Arsip Siswa (Peserta Didik) --}}
  @if($activeTab === 'siswa')
    <div class="card border-0 shadow-sm rounded-3 mb-4" style="background:var(--surface); border:1px solid var(--border)!important;">
      <div class="card-body p-3">
        <form method="GET" action="{{ route('situan.ekabinet.index') }}" class="row g-2 align-items-center">
          <input type="hidden" name="tab" value="siswa">
          @if(request('siswa_id'))
            <input type="hidden" name="siswa_id" value="{{ request('siswa_id') }}">
          @endif

          <div class="col-md-4 col-12">
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
              <input type="text" name="q_siswa" value="{{ request('q_siswa') }}" class="form-control border-start-0" placeholder="Cari nama berkas, no. dokumen, nama siswa, atau NISN...">
            </div>
          </div>

          <div class="col-md-3 col-6">
            <select name="rombel_id" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="">— Semua Rombel / Kelas —</option>
              @foreach($rombels as $r)
                <option value="{{ $r->id }}" {{ request('rombel_id') == $r->id ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3 col-6">
            <select name="kategori_siswa" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="">— Semua Kategori Berkas —</option>
              @foreach($kamusKategoriSiswa as $catKey => $catCfg)
                <option value="{{ $catKey }}" {{ request('kategori_siswa') === $catKey ? 'selected' : '' }}>
                  {{ $catCfg['label'] }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-2 col-12 d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">
              <i class="bi bi-filter"></i> Terapkan
            </button>
            @if(request()->hasAny(['q_siswa', 'rombel_id', 'kategori_siswa', 'siswa_id']))
              <a href="{{ route('situan.ekabinet.index', ['tab' => 'siswa']) }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                <i class="bi bi-x-lg"></i>
              </a>
            @endif
          </div>
        </form>

        @if($selectedSiswa)
          <div class="alert alert-info py-2 px-3 mt-3 mb-0 d-flex justify-content-between align-items-center" style="font-size:12.5px; border-radius:8px;">
            <div>
              <i class="bi bi-info-circle-fill me-1"></i> Menampilkan khusus lemari berkas: <strong>{{ $selectedSiswa->nama }}</strong> (NISN: {{ $selectedSiswa->nisn ?: '-' }})
              &bull; Kelas: <strong>{{ $selectedSiswa->siswaRombels->firstWhere('status_keanggotaan', 'aktif')?->rombel?->nama_rombel ?? 'Belum ada kelas' }}</strong>
            </div>
            <a href="{{ route('situan.ekabinet.index', ['tab' => 'siswa']) }}" class="btn btn-outline-info btn-sm py-0 px-2 fw-bold" style="font-size:11px;">
              Tampilkan Seluruh Siswa
            </a>
          </div>
        @endif
      </div>

      {{-- Table List Arsip Siswa --}}
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
          <thead class="table-light">
            <tr>
              <th class="py-3 px-3" style="width:40px;">No</th>
              <th class="py-3 px-3">Nama Dokumen &amp; Kategori</th>
              <th class="py-3 px-3">Peserta Didik / Rombel</th>
              <th class="py-3 px-3">Nomor &amp; Tanggal Dokumen</th>
              <th class="py-3 px-2 text-center" style="width:140px;">Sumber Dokumen</th>
              <th class="py-3 px-2 text-center" style="width:90px;">Ukuran</th>
              <th class="py-3 px-3 text-center" style="width:130px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($arsipSiswas as $idx => $arsip)
              @php
                $catCfg = $kamusKategoriSiswa[$arsip->kategori_berkas] ?? ['label' => ucfirst($arsip->kategori_berkas), 'badge' => 'secondary', 'icon' => 'bi-file-earmark'];
                $rombelNama = $arsip->siswa?->siswaRombels?->firstWhere('status_keanggotaan', 'aktif')?->rombel?->nama_rombel ?? '-';
              @endphp
              <tr>
                <td class="px-3 text-muted">{{ $arsipSiswas->firstItem() + $idx }}</td>
                <td class="px-3">
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-{{ $catCfg['badge'] }}-subtle text-{{ $catCfg['badge'] }} border border-{{ $catCfg['badge'] }}-subtle p-2 rounded-3">
                      <i class="bi {{ $catCfg['icon'] }} fs-6"></i>
                    </span>
                    <div>
                      <div class="fw-bold text-dark">{{ $arsip->nama_dokumen }}</div>
                      <div class="d-flex align-items-center gap-2 text-muted" style="font-size:11px;">
                        <span class="badge bg-light text-dark border">{{ $catCfg['label'] }}</span>
                        @if($arsip->keterangan)
                          <span class="text-truncate" style="max-width:200px;" title="{{ $arsip->keterangan }}">&bull; {{ $arsip->keterangan }}</span>
                        @endif
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-3">
                  <a href="{{ route('situan.ekabinet.index', ['tab' => 'siswa', 'siswa_id' => $arsip->siswa_id]) }}" class="text-decoration-none fw-bold text-dark" title="Filter berkas siswa ini">
                    {{ $arsip->siswa?->nama ?? 'Siswa Terhapus' }}
                  </a>
                  <div class="text-muted" style="font-size:11px;">
                    NISN: {{ $arsip->siswa?->nisn ?: '-' }} &bull; Rombel: <span class="badge bg-light text-secondary border">{{ $rombelNama }}</span>
                  </div>
                </td>
                <td class="px-3">
                  <div>{{ $arsip->nomor_dokumen ?: '-' }}</div>
                  <div class="text-muted" style="font-size:11px;">
                    <i class="bi bi-calendar-event me-1"></i>
                    {{ $arsip->tanggal_dokumen ? $arsip->tanggal_dokumen->translatedFormat('d M Y') : 'Tanpa tanggal' }}
                  </div>
                </td>
                <td class="px-2 text-center">
                  @if($arsip->ppdb_pendaftar_id)
                    <span class="badge bg-info-subtle text-info border border-info-subtle" title="Otomatis ditarik dari berkas pendaftaran PPDB">
                      <i class="bi bi-cloud-check-fill me-1"></i> PPDB
                    </span>
                  @elseif($arsip->pelayanan_surat_id)
                    <span class="badge bg-success-subtle text-success border border-success-subtle" title="Diterbitkan dari Loket Surat Resmi TU">
                      <i class="bi bi-file-earmark-check-fill me-1"></i> Loket TU
                    </span>
                  @else
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" title="Diunggah manual via E-Kabinet">
                      <i class="bi bi-person-fill-up me-1"></i> Staf TU
                    </span>
                  @endif
                </td>
                <td class="px-2 text-center text-muted" style="font-size:11.5px;">
                  {{ $arsip->formatted_file_size }}
                </td>
                <td class="px-3 text-center">
                  <div class="btn-group btn-group-sm">
                    @if($arsip->file_path)
                      <a href="{{ $arsip->file_url }}" target="_blank" class="btn btn-outline-primary" title="Lihat / Unduh Dokumen">
                        <i class="bi bi-eye-fill"></i>
                      </a>
                    @endif
                    <a href="{{ route('situan.ekabinet.index', ['tab' => 'siswa', 'siswa_id' => $arsip->siswa_id]) }}" class="btn btn-outline-secondary" title="Buka Lemari Siswa Ini">
                      <i class="bi bi-folder2-open"></i>
                    </a>
                    <form action="{{ route('situan.ekabinet.siswa.destroy', $arsip->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus berkas ini dari E-Kabinet siswa?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-outline-danger" title="Hapus Dokumen">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                  <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                  Belum ada berkas digital siswa yang cocok dengan filter pencarian.<br>
                  <div class="d-flex justify-content-center gap-2 mt-3">
                    <button type="button" class="btn btn-sm btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#modalUploadArsipSiswa">
                      <i class="bi bi-cloud-arrow-up-fill me-1"></i> Unggah Berkas Siswa
                    </button>
                    @if(!empty($ppdbReadyCount) && $ppdbReadyCount > 0)
                      <button type="button" class="btn btn-sm btn-outline-info fw-bold" data-bs-toggle="modal" data-bs-target="#modalSyncPpdb">
                        <i class="bi bi-arrow-repeat me-1"></i> Tarik Berkas dari PPDB
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($arsipSiswas->hasPages())
        <div class="card-footer bg-transparent py-3 border-0">
          {{ $arsipSiswas->links() }}
        </div>
      @endif
    </div>
  @endif

  {{-- Tab 3: Lemari Berkas Lembaga (Dokumen Sekolah & Aset) --}}
  @if($activeTab === 'lembaga')
    <div class="card border-0 shadow-sm rounded-3 mb-4" style="background:var(--surface); border:1px solid var(--border)!important;">
      <div class="card-body p-3">
        <form method="GET" action="{{ route('situan.ekabinet.lembaga') }}" class="row g-2 align-items-center">
          <input type="hidden" name="tab" value="lembaga">
          <div class="col-md-6 col-12">
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
              <input type="text" name="q_lembaga" value="{{ request('q_lembaga') }}" class="form-control border-start-0" placeholder="Cari nama dokumen legalitas atau nomor SK...">
            </div>
          </div>
          <div class="col-md-4 col-8">
            <select name="kategori_lembaga" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="">— Semua Kategori Dokumen Lembaga —</option>
              <option value="akreditasi" {{ request('kategori_lembaga') == 'akreditasi' ? 'selected' : '' }}>Akreditasi Sekolah (BAN-SM)</option>
              <option value="izin_operasional" {{ request('kategori_lembaga') == 'izin_operasional' ? 'selected' : '' }}>Izin Operasional &amp; Pendirian</option>
              <option value="sertifikat_aset" {{ request('kategori_lembaga') == 'sertifikat_aset' ? 'selected' : '' }}>Sertifikat Tanah &amp; Aset Gedung</option>
              <option value="kurikulum_kosp" {{ request('kategori_lembaga') == 'kurikulum_kosp' ? 'selected' : '' }}>Dokumen Kurikulum (KOSP)</option>
              <option value="pedoman_sop" {{ request('kategori_lembaga') == 'pedoman_sop' ? 'selected' : '' }}>Pedoman Mutu &amp; SOP</option>
              <option value="sk_kelembagaan" {{ request('kategori_lembaga') == 'sk_kelembagaan' ? 'selected' : '' }}>SK Kelembagaan / Komite</option>
              <option value="lainnya" {{ request('kategori_lembaga') == 'lainnya' ? 'selected' : '' }}>Dokumen Lainnya</option>
            </select>
          </div>
          <div class="col-md-2 col-4 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-dark w-100 fw-bold">Filter</button>
            <a href="{{ route('situan.ekabinet.lembaga') }}" class="btn btn-sm btn-outline-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
          </div>
        </form>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
          <thead class="table-light" style="border-bottom:1.5px solid var(--border);">
            <tr>
              <th class="py-3 px-3" style="width:180px;">Kategori Berkas</th>
              <th class="py-3 px-3">Nama Dokumen / Arsip</th>
              <th class="py-3 px-3" style="width:180px;">Nomor Dokumen</th>
              <th class="py-3 px-3" style="width:160px;">Tanggal / Masa Berlaku</th>
              <th class="py-3 px-3 text-center" style="width:120px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($arsipLembagas as $arsip)
              @php
                $isExpired = $arsip->tanggal_berakhir && $arsip->tanggal_berakhir->isPast();
              @endphp
              <tr>
                <td class="px-3">
                  <span class="badge {{ $arsip->badge_class }} px-2 py-1 rounded-pill" style="font-size:11px;">
                    {{ $arsip->label_kategori }}
                  </span>
                </td>
                <td class="px-3">
                  <div class="fw-bold text-dark">{{ $arsip->nama_arsip }}</div>
                  @if($arsip->keterangan)
                    <div class="text-muted" style="font-size:11px;">{{ Str::limit($arsip->keterangan, 70) }}</div>
                  @endif
                </td>
                <td class="px-3 text-muted">
                  <span class="font-monospace small">{{ $arsip->nomor_dokumen ?: '-' }}</span>
                </td>
                <td class="px-3">
                  @if($arsip->tanggal_berakhir)
                    <span class="badge {{ $isExpired ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} px-2 py-1">
                      s.d {{ $arsip->tanggal_berakhir->format('d/m/Y') }}
                    </span>
                  @elseif($arsip->tanggal_dokumen)
                    <span class="text-muted">{{ $arsip->tanggal_dokumen->format('d/m/Y') }}</span>
                  @else
                    <span class="text-muted">Permanen / -</span>
                  @endif
                </td>
                <td class="px-3 text-center">
                  <div class="btn-group btn-group-sm">
                    @if($arsip->file_path)
                      <a href="{{ asset('storage/' . $arsip->file_path) }}" target="_blank" class="btn btn-outline-primary" title="Lihat / Unduh Dokumen">
                        <i class="bi bi-eye-fill"></i>
                      </a>
                    @endif
                    <form action="{{ route('situan.ekabinet.lembaga.destroy', $arsip->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus dokumen arsip lembaga ini?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-outline-danger" title="Hapus Dokumen">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-5 text-muted">
                  <i class="bi bi-buildings fs-1 d-block mb-2 text-secondary"></i>
                  Belum ada dokumen legalitas atau arsip lembaga yang tersimpan.<br>
                  <button type="button" class="btn btn-sm btn-dark mt-2" data-bs-toggle="modal" data-bs-target="#modalUploadArsipLembaga">
                    <i class="bi bi-building-fill-add me-1"></i> Unggah Dokumen Lembaga Baru
                  </button>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($arsipLembagas->hasPages())
        <div class="card-footer bg-transparent py-3 border-0">
          {{ $arsipLembagas->links() }}
        </div>
      @endif
    </div>
  @endif

  {{-- Tab 4: E-Kabinet MoU (Kerjasama Industri & DUDI) --}}
  @if($activeTab === 'mou')
    <div class="card border-0 shadow-sm rounded-3 mb-4" style="background:var(--surface); border:1px solid var(--border)!important;">
      <div class="card-body p-3">
        <form method="GET" action="{{ route('situan.ekabinet.mou') }}" class="row g-2 align-items-center">
          <input type="hidden" name="tab" value="mou">
          <div class="col-md-6 col-12">
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
              <input type="text" name="q_mou" value="{{ request('q_mou') }}" class="form-control border-start-0" placeholder="Cari nama MoU, mitra instansi / perusahaan, atau nomor dokumen...">
            </div>
          </div>
          <div class="col-md-4 col-8">
            <select name="status_mou" class="form-select form-select-sm" onchange="this.form.submit()">
              <option value="">— Semua Status Masa Berlaku —</option>
              <option value="aktif" {{ request('status_mou') == 'aktif' ? 'selected' : '' }}>Masih Aktif / Berlaku</option>
              <option value="kedaluwarsa" {{ request('status_mou') == 'kedaluwarsa' ? 'selected' : '' }}>Telah Kedaluwarsa (Expired)</option>
            </select>
          </div>
          <div class="col-md-2 col-4 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-warning w-100 fw-bold text-dark">Filter</button>
            <a href="{{ route('situan.ekabinet.mou') }}" class="btn btn-sm btn-outline-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
          </div>
        </form>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
          <thead class="table-light" style="border-bottom:1.5px solid var(--border);">
            <tr>
              <th class="py-3 px-3" style="width:220px;">Mitra Instansi / DUDI</th>
              <th class="py-3 px-3">Judul Kerjasama &amp; Ruang Lingkup</th>
              <th class="py-3 px-3" style="width:170px;">Nomor Perjanjian</th>
              <th class="py-3 px-3" style="width:160px;">Masa Berlaku</th>
              <th class="py-3 px-3 text-center" style="width:120px;">Status</th>
              <th class="py-3 px-3 text-center" style="width:120px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($arsipMous as $mou)
              @php
                $isExpired = $mou->tanggal_berakhir && $mou->tanggal_berakhir->isPast();
              @endphp
              <tr>
                <td class="px-3">
                  <div class="d-flex align-items-center gap-2">
                    <div class="p-2 rounded-2 bg-warning-subtle text-warning border border-warning-subtle d-inline-flex">
                      <i class="bi bi-building fs-5"></i>
                    </div>
                    <div>
                      <div class="fw-bold text-dark" style="font-size:13px;">{{ $mou->mitra_instansi ?: 'Mitra Industri' }}</div>
                      <span class="text-muted" style="font-size:11px;">DUDI / Mitra PKL</span>
                    </div>
                  </div>
                </td>
                <td class="px-3">
                  <div class="fw-bold text-dark">{{ $mou->nama_arsip }}</div>
                  @if($mou->keterangan)
                    <div class="text-muted" style="font-size:11px;">{{ Str::limit($mou->keterangan, 75) }}</div>
                  @endif
                </td>
                <td class="px-3 text-muted">
                  <span class="font-monospace small">{{ $mou->nomor_dokumen ?: '-' }}</span>
                </td>
                <td class="px-3 text-muted" style="font-size:11.5px;">
                  @if($mou->tanggal_dokumen && $mou->tanggal_berakhir)
                    <div><i class="bi bi-calendar-event me-1"></i>{{ $mou->tanggal_dokumen->format('d/m/Y') }}</div>
                    <div><i class="bi bi-arrow-right-short"></i>{{ $mou->tanggal_berakhir->format('d/m/Y') }}</div>
                  @elseif($mou->tanggal_berakhir)
                    <div>s.d {{ $mou->tanggal_berakhir->format('d/m/Y') }}</div>
                  @elseif($mou->tanggal_dokumen)
                    <div>Mulai {{ $mou->tanggal_dokumen->format('d/m/Y') }}</div>
                  @else
                    <div>-</div>
                  @endif
                </td>
                <td class="px-3 text-center">
                  @if($mou->tanggal_berakhir)
                    @if($isExpired)
                      <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Kedaluwarsa
                      </span>
                    @else
                      <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                        <i class="bi bi-check-circle-fill me-1"></i> Aktif
                      </span>
                    @endif
                  @else
                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded-pill">
                      <i class="bi bi-infinity me-1"></i> Permanen
                    </span>
                  @endif
                </td>
                <td class="px-3 text-center">
                  <div class="btn-group btn-group-sm">
                    @if($mou->file_path)
                      <a href="{{ asset('storage/' . $mou->file_path) }}" target="_blank" class="btn btn-outline-primary" title="Lihat / Unduh MoU">
                        <i class="bi bi-eye-fill"></i>
                      </a>
                    @endif
                    <form action="{{ route('situan.ekabinet.mou.destroy', $mou->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus dokumen MoU ini dari E-Kabinet MoU?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-outline-danger" title="Hapus Dokumen MoU">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                  <i class="bi bi-briefcase fs-1 d-block mb-2 text-warning"></i>
                  Belum ada dokumen MoU atau Kerjasama Industri yang tersimpan.<br>
                  <button type="button" class="btn btn-sm btn-warning mt-2 fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#modalUploadArsipMou">
                    <i class="bi bi-briefcase-fill me-1"></i> Unggah Dokumen MoU Baru
                  </button>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($arsipMous->hasPages())
        <div class="card-footer bg-transparent py-3 border-0">
          {{ $arsipMous->links() }}
        </div>
      @endif
    </div>
  @endif

  {{-- Tab 3: Radar Kelengkapan Berkas Guru --}}
  @if($activeTab === 'kelengkapan')
    <div class="card border-0 shadow-sm rounded-3 mb-4" style="background:var(--surface); border:1px solid var(--border)!important;">
      <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-check2-all me-1 text-primary"></i> Checklist Kelengkapan Dokumen PTK</h5>
          <span class="text-muted small">Memudahkan staf TU memverifikasi kelengkapan berkas penting untuk kenaikan pangkat, KGB, dan database sekolah.</span>
        </div>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
          Total: {{ $radarKelengkapan->count() }} Guru Aktif
        </span>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
          <thead class="table-light">
            <tr>
              <th class="py-3 px-3">Nama PTK</th>
              <th class="py-3 px-2 text-center" style="width:100px;">SK KGB</th>
              <th class="py-3 px-2 text-center" style="width:100px;">SK Pangkat</th>
              <th class="py-3 px-2 text-center" style="width:100px;">Ijazah</th>
              <th class="py-3 px-2 text-center" style="width:100px;">Serdik</th>
              <th class="py-3 px-2 text-center" style="width:110px;">Sertifikat Diklat</th>
              <th class="py-3 px-2 text-center" style="width:100px;">KTP / KK</th>
              <th class="py-3 px-3 text-center" style="width:130px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($radarKelengkapan as $guruItem)
              @php
                $cats = $guruItem->arsipDokumens->pluck('kategori_berkas')->toArray();
                $hasKgb = in_array('sk_kgb_terakhir', $cats);
                $hasPangkat = in_array('sk_pangkat_terakhir', $cats) || in_array('sk_pns', $cats) || in_array('sk_pppk', $cats);
                $hasIjazah = in_array('ijazah', $cats);
                $hasSerdik = in_array('sertifikat_pendidik', $cats);
                $hasDiklat = in_array('sertifikat_pelatihan', $cats);
                $hasIdentitas = in_array('ktp', $cats) || in_array('kk', $cats);
              @endphp
              <tr>
                <td class="px-3">
                  <div class="fw-bold text-dark">{{ $guruItem->nama }}</div>
                  <div class="text-muted" style="font-size:11px;">NIP: {{ $guruItem->nip ?: '-' }} &bull; Gol: {{ $guruItem->golongan_ruang ?: '-' }}</div>
                </td>
                <td class="text-center px-2">
                  @if($hasKgb)
                    <span class="badge bg-success-subtle text-success"><i class="bi bi-check-lg"></i> Ada</span>
                  @else
                    <span class="badge bg-light text-muted border">-</span>
                  @endif
                </td>
                <td class="text-center px-2">
                  @if($hasPangkat)
                    <span class="badge bg-success-subtle text-success"><i class="bi bi-check-lg"></i> Ada</span>
                  @else
                    <span class="badge bg-light text-muted border">-</span>
                  @endif
                </td>
                <td class="text-center px-2">
                  @if($hasIjazah)
                    <span class="badge bg-success-subtle text-success"><i class="bi bi-check-lg"></i> Ada</span>
                  @else
                    <span class="badge bg-light text-muted border">-</span>
                  @endif
                </td>
                <td class="text-center px-2">
                  @if($hasSerdik)
                    <span class="badge bg-primary-subtle text-primary"><i class="bi bi-check-lg"></i> Ada</span>
                  @else
                    <span class="badge bg-light text-muted border">-</span>
                  @endif
                </td>
                <td class="text-center px-2">
                  @if($hasDiklat)
                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle"><i class="bi bi-award-fill text-warning"></i> Ada</span>
                  @else
                    <span class="badge bg-light text-muted border">-</span>
                  @endif
                </td>
                <td class="text-center px-2">
                  @if($hasIdentitas)
                    <span class="badge bg-info-subtle text-info"><i class="bi bi-check-lg"></i> Ada</span>
                  @else
                    <span class="badge bg-light text-muted border">-</span>
                  @endif
                </td>
                <td class="text-center px-3">
                  <div class="btn-group btn-group-sm">
                    <a href="{{ route('situan.arsip-ptk.index', $guruItem->id) }}" class="btn btn-outline-primary" title="Buka Lemari Berkas">
                      <i class="bi bi-folder-symlink-fill me-1"></i> Buka ({{ count($cats) }})
                    </a>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif

</div>

{{-- MODAL 1: UNGGAH BERKAS PTK DARI E-KABINET --}}
<div class="modal fade" id="modalUploadArsipPtk" tabindex="-1" aria-labelledby="modalUploadArsipPtkLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white px-4 py-3">
        <h5 class="modal-title fw-bold" id="modalUploadArsipPtkLabel">
          <i class="bi bi-cloud-arrow-up-fill me-1"></i> Unggah Berkas PTK ke E-Kabinet
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('situan.ekabinet.ptk.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold small">Pilih Guru / PTK <span class="text-danger">*</span></label>
            <div class="searchable-select" id="wrapper_guru">
              <input type="text" name="guru_id" id="hidden_guru_id" value="{{ request('guru_id') }}" required style="position: absolute; opacity: 0; pointer-events: none; width: 1px; height: 1px; left: 20px; bottom: 0;" tabindex="-1">
              
              <div class="searchable-trigger d-flex align-items-center justify-content-between" id="trigger_guru" onclick="toggleSearchable('guru')">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                  <i class="bi bi-person-badge text-primary fs-5"></i>
                  <span id="label_guru" class="text-truncate {{ request('guru_id') ? 'text-dark fw-semibold' : 'text-muted' }}" style="font-size:13.5px;">
                    @if(request('guru_id') && ($preGuru = $gurus->firstWhere('id', request('guru_id'))))
                      {{ $preGuru->nama }} (NIP: {{ $preGuru->nip ?: '-' }})
                    @else
                      — Cari / Pilih Guru Tujuan —
                    @endif
                  </span>
                </div>
                <div class="d-flex align-items-center gap-1">
                  <button type="button" class="btn btn-sm btn-link text-muted p-0 me-1 {{ request('guru_id') ? '' : 'd-none' }}" id="btn_clear_guru" onclick="clearSearchable('guru', event)" title="Hapus pilihan">
                    <i class="bi bi-x-circle-fill fs-6"></i>
                  </button>
                  <i class="bi bi-chevron-down text-muted small" id="chevron_guru"></i>
                </div>
              </div>

              <div class="searchable-menu d-none" id="menu_guru">
                <div class="p-2 border-bottom bg-light">
                  <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="search_input_guru" placeholder="Ketik nama atau NIP guru..." autocomplete="off" oninput="filterSearchable('guru')">
                  </div>
                </div>
                <div class="overflow-auto" style="max-height: 220px;" id="list_guru">
                  @foreach($gurus as $g)
                    <div class="searchable-item p-2 px-3 d-flex align-items-center justify-content-between {{ request('guru_id') == $g->id ? 'selected' : '' }}"
                         data-id="{{ $g->id }}"
                         data-nama="{{ strtolower($g->nama) }}"
                         data-nip="{{ strtolower($g->nip ?? '') }}"
                         data-display="{{ $g->nama }} (NIP: {{ $g->nip ?: '-' }})"
                         onclick="selectSearchable('guru', '{{ $g->id }}', '{{ addslashes($g->nama) }} (NIP: {{ addslashes($g->nip ?: '-') }})')">
                      <div>
                        <div class="fw-semibold text-dark" style="font-size:13px;">{{ $g->nama }}</div>
                        <div class="text-muted" style="font-size:11px;">
                          <span>NIP: {{ $g->nip ?: '-' }}</span>
                          @if($g->jabatan)
                            <span class="ms-1">&bull; {{ $g->jabatan }}</span>
                          @endif
                        </div>
                      </div>
                      <i class="bi bi-check2 text-primary fs-5 {{ request('guru_id') == $g->id ? '' : 'd-none' }} check-icon"></i>
                    </div>
                  @endforeach
                  <div class="p-3 text-center text-muted small d-none" id="empty_guru">
                    <i class="bi bi-search me-1"></i> Guru tidak ditemukan
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Kategori Berkas <span class="text-danger">*</span></label>
            <select name="kategori_berkas" class="form-select" required>
              <option value="sk_pangkat_terakhir">SK Pangkat Terakhir</option>
              <option value="sk_kgb_terakhir">SK Kenaikan Gaji Berkala (KGB)</option>
              <option value="sk_cpns">SK Calon Pegawai Negeri Sipil (CPNS)</option>
              <option value="sk_pns">SK Pengangkatan PNS Definitif</option>
              <option value="sk_pppk">SK Pengangkatan PPPK</option>
              <option value="ijazah">Ijazah Pendidikan Terakhir</option>
              <option value="transkrip">Transkrip Nilai Akademik</option>
              <option value="sertifikat_pendidik">Sertifikat Pendidik (Serdik)</option>
              <option value="sertifikat_pelatihan">Sertifikat Pelatihan / Diklat / Workshop</option>
              <option value="kartu_pegawai">Kartu Pegawai (Karpeg / KPE)</option>
              <option value="ktp">KTP Elektronik</option>
              <option value="kk">Kartu Keluarga (KK)</option>
              <option value="lainnya">Dokumen Kedinasan Lainnya</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Nama &amp; Judul Dokumen <span class="text-danger">*</span></label>
            <input type="text" name="nama_dokumen" class="form-control" placeholder="Contoh: SK KGB Golongan III/b Tahun 2025" required>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold small">Nomor Dokumen (Opsional)</label>
              <input type="text" name="nomor_dokumen" class="form-control" placeholder="Nomor SK / Ijazah">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold small">Tanggal Dokumen</label>
              <input type="date" name="tanggal_dokumen" class="form-control">
            </div>
          </div>

          <div class="mb-2">
            <label class="form-label fw-semibold small">Pilih File Berkas (PDF/JPG/PNG, Maks 10MB) <span class="text-danger">*</span></label>
            <input type="file" name="file_dokumen" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
            <div class="form-text">Pastikan file hasil scan jelas terbaca untuk kelancaran administrasi dinas.</div>
          </div>
        </div>
        <div class="modal-footer px-4 py-3 bg-light d-flex justify-content-end gap-2 border-top">
          <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal" style="font-weight:600; border-radius:8px;">Batal</button>
          <button type="submit" class="btn btn-sm btn-primary px-3 fw-bold" style="border-radius:8px;">
            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Simpan ke E-Kabinet
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- MODAL 2: UNGGAH DOKUMEN LEMBAGA & LEGALITAS SEKOLAH --}}
<div class="modal fade" id="modalUploadArsipLembaga" tabindex="-1" aria-labelledby="modalUploadArsipLembagaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-dark text-white px-4 py-3">
        <h5 class="modal-title fw-bold" id="modalUploadArsipLembagaLabel">
          <i class="bi bi-building-fill-add me-1"></i> Unggah Dokumen Lembaga &amp; Legalitas
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('situan.ekabinet.lembaga.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold small">Kategori Arsip Lembaga <span class="text-danger">*</span></label>
            <select name="kategori_arsip" class="form-select" required>
              <option value="akreditasi">Sertifikat Akreditasi Sekolah (BAN-SM)</option>
              <option value="izin_operasional">Izin Operasional / Pendirian Sekolah</option>
              <option value="sertifikat_aset">Sertifikat Tanah / Gedung / Aset</option>
              <option value="kurikulum_kosp">Dokumen Kurikulum (KOSP) &amp; Silabus</option>
              <option value="pedoman_sop">Pedoman Mutu &amp; Standar Operasional (SOP)</option>
              <option value="sk_kelembagaan">SK Kelembagaan / Komite Sekolah</option>
              <option value="lainnya">Dokumen Lembaga Lainnya</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Nama / Judul Dokumen <span class="text-danger">*</span></label>
            <input type="text" name="nama_arsip" class="form-control" placeholder="Contoh: Sertifikat Akreditasi BAN-SM Tahun 2025" required>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold small">Nomor Surat / Dokumen</label>
              <input type="text" name="nomor_dokumen" class="form-control" placeholder="421.5/022/SMKN1/2026">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold small">Tanggal Dokumen</label>
              <input type="date" name="tanggal_dokumen" class="form-control">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Masa Berlaku Berakhir (Khusus Akreditasi)</label>
            <input type="date" name="tanggal_berakhir" class="form-control">
            <div class="form-text">Biarkan kosong jika berlaku seterusnya / permanen.</div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Pilih File Berkas (PDF/JPG/PNG, Maks 10MB) <span class="text-danger">*</span></label>
            <input type="file" name="file_dokumen" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
          </div>

          <div class="mb-2">
            <label class="form-label fw-semibold small">Catatan / Keterangan Tambahan</label>
            <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan bidang keahlian, nomor register, atau lokasi fisik dokumen..."></textarea>
          </div>
        </div>
        <div class="modal-footer px-4 py-3 bg-light d-flex justify-content-end gap-2 border-top">
          <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal" style="font-weight:600; border-radius:8px;">Batal</button>
          <button type="submit" class="btn btn-sm btn-dark px-3 fw-bold" style="border-radius:8px;">
            <i class="bi bi-building-fill-add me-1"></i> Simpan Dokumen Lembaga
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- MODAL KHUSUS: UNGGAH DOKUMEN MOU & KEMITRAAN DUDI INDUSTRI --}}
<div class="modal fade" id="modalUploadArsipMou" tabindex="-1" aria-labelledby="modalUploadArsipMouLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-warning text-dark px-4 py-3">
        <h5 class="modal-title fw-bold" id="modalUploadArsipMouLabel">
          <i class="bi bi-briefcase-fill me-1"></i> Unggah Dokumen MoU &amp; Kerjasama DUDI
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('situan.ekabinet.mou.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold small">Mitra Instansi / Perusahaan (DUDI) <span class="text-danger">*</span></label>
            <input type="text" name="mitra_instansi" class="form-control" placeholder="Contoh: PT Astra Honda Motor / PT Auto 2000" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Nama / Judul Dokumen MoU <span class="text-danger">*</span></label>
            <input type="text" name="nama_arsip" class="form-control" placeholder="Contoh: MoU Praktek Kerja Lapangan &amp; Penyelarasan Kurikulum" required>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold small">Nomor Surat / Perjanjian</label>
              <input type="text" name="nomor_dokumen" class="form-control" placeholder="421.5/088/SMKN1AN/2026">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold small">Tanggal Penandatanganan</label>
              <input type="date" name="tanggal_dokumen" class="form-control">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Masa Berlaku Berakhir (Batas Akhir Kerjasama)</label>
            <input type="date" name="tanggal_berakhir" class="form-control">
            <div class="form-text">Biarkan kosong jika berlaku tanpa batas waktu / permanen.</div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Pilih File Berkas MoU (PDF/JPG/PNG, Maks 10MB) <span class="text-danger">*</span></label>
            <input type="file" name="file_dokumen" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
          </div>

          <div class="mb-2">
            <label class="form-label fw-semibold small">Ruang Lingkup Kerjasama / Catatan</label>
            <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: Kerjasama magang siswa TKRO/TBSM, guru tamu industri, uji kompetensi..."></textarea>
          </div>
        </div>
        <div class="modal-footer px-4 py-3 bg-light d-flex justify-content-end gap-2 border-top">
          <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal" style="font-weight:600; border-radius:8px;">Batal</button>
          <button type="submit" class="btn btn-sm btn-warning px-3 fw-bold text-dark" style="border-radius:8px;">
            <i class="bi bi-briefcase-fill me-1"></i> Simpan ke E-Kabinet MoU
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- MODAL 3: UNGGAH BERKAS DIGITAL SISWA (PESERTA DIDIK) --}}
<div class="modal fade" id="modalUploadArsipSiswa" tabindex="-1" aria-labelledby="modalUploadArsipSiswaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-success text-white px-4 py-3">
        <h5 class="modal-title fw-bold" id="modalUploadArsipSiswaLabel">
          <i class="bi bi-mortarboard-fill me-1"></i> Unggah Berkas Digital Siswa
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('situan.ekabinet.siswa.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold small">Pilih Peserta Didik <span class="text-danger">*</span></label>
            <div class="searchable-select" id="wrapper_siswa">
              <input type="text" name="siswa_id" id="hidden_siswa_id" value="{{ request('siswa_id') }}" required style="position: absolute; opacity: 0; pointer-events: none; width: 1px; height: 1px; left: 20px; bottom: 0;" tabindex="-1">
              
              <div class="searchable-trigger d-flex align-items-center justify-content-between" id="trigger_siswa" onclick="toggleSearchable('siswa')">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                  <i class="bi bi-mortarboard text-success fs-5"></i>
                  <span id="label_siswa" class="text-truncate {{ request('siswa_id') ? 'text-dark fw-semibold' : 'text-muted' }}" style="font-size:13.5px;">
                    @if(request('siswa_id') && ($preSiswa = $allSiswaAktif->firstWhere('id', request('siswa_id'))))
                      @php
                        $preRombel = $preSiswa->siswaRombels->firstWhere('status_keanggotaan', 'aktif')?->rombel?->nama_rombel ?? 'Tanpa Rombel';
                      @endphp
                      {{ $preSiswa->nama }} (NISN: {{ $preSiswa->nisn ?: '-' }} &bull; {{ $preRombel }})
                    @else
                      — Cari / Pilih Siswa Tujuan —
                    @endif
                  </span>
                </div>
                <div class="d-flex align-items-center gap-1">
                  <button type="button" class="btn btn-sm btn-link text-muted p-0 me-1 {{ request('siswa_id') ? '' : 'd-none' }}" id="btn_clear_siswa" onclick="clearSearchable('siswa', event)" title="Hapus pilihan">
                    <i class="bi bi-x-circle-fill fs-6"></i>
                  </button>
                  <i class="bi bi-chevron-down text-muted small" id="chevron_siswa"></i>
                </div>
              </div>

              <div class="searchable-menu d-none" id="menu_siswa">
                <div class="p-2 border-bottom bg-light">
                  <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="search_input_siswa" placeholder="Ketik nama, NISN, atau rombel siswa..." autocomplete="off" oninput="filterSearchable('siswa')">
                  </div>
                </div>
                <div class="overflow-auto" style="max-height: 220px;" id="list_siswa">
                  @foreach($allSiswaAktif as $s)
                    @php
                      $rombelName = $s->siswaRombels->firstWhere('status_keanggotaan', 'aktif')?->rombel?->nama_rombel ?? 'Tanpa Rombel';
                      $displaySiswa = $s->nama . ' (NISN: ' . ($s->nisn ?: '-') . ' • ' . $rombelName . ')';
                    @endphp
                    <div class="searchable-item p-2 px-3 d-flex align-items-center justify-content-between {{ request('siswa_id') == $s->id ? 'selected' : '' }}"
                         data-id="{{ $s->id }}"
                         data-nama="{{ strtolower($s->nama) }}"
                         data-nisn="{{ strtolower($s->nisn ?? '') }}"
                         data-rombel="{{ strtolower($rombelName) }}"
                         data-display="{{ $displaySiswa }}"
                         onclick="selectSearchable('siswa', '{{ $s->id }}', '{{ addslashes($displaySiswa) }}')">
                      <div>
                        <div class="fw-semibold text-dark" style="font-size:13px;">{{ $s->nama }}</div>
                        <div class="text-muted" style="font-size:11px;">
                          <span>NISN: {{ $s->nisn ?: '-' }}</span>
                          <span class="ms-1 badge bg-secondary-subtle text-dark border px-1" style="font-size:10.5px;">{{ $rombelName }}</span>
                        </div>
                      </div>
                      <i class="bi bi-check2 text-success fs-5 {{ request('siswa_id') == $s->id ? '' : 'd-none' }} check-icon"></i>
                    </div>
                  @endforeach
                  <div class="p-3 text-center text-muted small d-none" id="empty_siswa">
                    <i class="bi bi-search me-1"></i> Siswa tidak ditemukan
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Kategori Berkas Siswa <span class="text-danger">*</span></label>
            <select name="kategori_berkas" class="form-select" required>
              @foreach($kamusKategoriSiswa as $catKey => $catCfg)
                <option value="{{ $catKey }}">{{ $catCfg['label'] }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Nama &amp; Judul Dokumen <span class="text-danger">*</span></label>
            <input type="text" name="nama_dokumen" class="form-control" placeholder="Contoh: Ijazah Asli SMP Negeri 1 Air Naningan" required>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold small">Nomor Dokumen (Opsional)</label>
              <input type="text" name="nomor_dokumen" class="form-control" placeholder="No. Ijazah / Akta / KK">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold small">Tanggal Dokumen</label>
              <input type="date" name="tanggal_dokumen" class="form-control">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Pilih File Berkas (PDF/JPG/PNG, Maks 10MB) <span class="text-danger">*</span></label>
            <input type="file" name="file_dokumen" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
            <div class="form-text">Pastikan hasil scan berkas siswa terlihat jelas untuk kebutuhan verifikasi.</div>
          </div>

          <div class="mb-2">
            <label class="form-label fw-semibold small">Catatan / Keterangan Tambahan</label>
            <textarea name="keterangan" class="form-control" rows="2" placeholder="Keterangan tambahan atau catatan fisik dokumen..."></textarea>
          </div>
        </div>
        <div class="modal-footer px-4 py-3 bg-light d-flex justify-content-end gap-2 border-top">
          <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal" style="font-weight:600; border-radius:8px;">Batal</button>
          <button type="submit" class="btn btn-sm btn-success px-3 fw-bold" style="border-radius:8px;">
            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Simpan ke E-Kabinet Siswa
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- MODAL 4: SINKRONISASI BERKAS DARI PPDB --}}
<div class="modal fade" id="modalSyncPpdb" tabindex="-1" aria-labelledby="modalSyncPpdbLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-info text-dark px-4 py-3">
        <h5 class="modal-title fw-bold" id="modalSyncPpdbLabel">
          <i class="bi bi-cloud-arrow-down-fill me-1"></i> Sinkronisasi Berkas dari PPDB
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('situan.ekabinet.sync-ppdb') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="text-center mb-3">
            <div class="badge bg-info-subtle text-info p-3 rounded-circle mb-2">
              <i class="bi bi-arrow-repeat fs-1"></i>
            </div>
            <h6 class="fw-bold mb-1">Tarik Berkas Pendaftar ke E-Kabinet Siswa</h6>
            <p class="text-muted small mb-0">Fitur ini mengotomatiskan migrasi berkas pendaftaran calon siswa di PPDB yang telah diterima menjadi arsip resmi siswa di SITUAN.</p>
          </div>

          <div class="card border-0 bg-light p-3 rounded-3 mb-3" style="font-size:12.5px;">
            <div class="fw-bold text-dark mb-2"><i class="bi bi-check2-circle text-success me-1"></i> Berkas yang Akan Ditarik:</div>
            <ul class="mb-0 ps-3 text-muted">
              <li>Kartu Keluarga (KK)</li>
              <li>Akta Kelahiran Siswa</li>
              <li>Ijazah / SKL SMP Sederajat</li>
              <li>KTP Orang Tua / Wali</li>
              <li>Kartu Indonesia Pintar (KIP / PIP) jika ada</li>
            </ul>
          </div>

          <div class="alert alert-warning py-2 px-3 small mb-0">
            <i class="bi bi-info-circle-fill me-1"></i> Berkas yang sudah ada di lemari arsip tidak akan diduplikasi. Sistem akan mencocokkan data berdasarkan NISN atau Nama Pendaftar.
          </div>
        </div>
        <div class="modal-footer px-4 py-3 bg-light d-flex justify-content-end gap-2 border-top">
          <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal" style="font-weight:600; border-radius:8px;">Batal</button>
          <button type="submit" class="btn btn-sm btn-info px-3 fw-bold" style="border-radius:8px;">
            <i class="bi bi-arrow-repeat me-1"></i> Mulai Sinkronisasi Sekarang
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function toggleSearchable(type) {
  const menu = document.getElementById('menu_' + type);
  const input = document.getElementById('search_input_' + type);
  if (!menu) return;
  
  const isHidden = menu.classList.contains('d-none');
  
  // Tutup searchable dropdown lainnya
  ['guru', 'siswa'].forEach(t => {
    if (t !== type) {
      const otherMenu = document.getElementById('menu_' + t);
      if (otherMenu) otherMenu.classList.add('d-none');
    }
  });

  if (isHidden) {
    menu.classList.remove('d-none');
    // Hapus highlight error jika ada
    const trigger = document.getElementById('trigger_' + type);
    if (trigger) trigger.classList.remove('border-danger');

    setTimeout(() => {
      if (input) input.focus();
    }, 60);
  } else {
    menu.classList.add('d-none');
  }
}

function filterSearchable(type) {
  const input = document.getElementById('search_input_' + type);
  const query = (input ? input.value : '').trim().toLowerCase();
  const list = document.getElementById('list_' + type);
  if (!list) return;

  const items = list.querySelectorAll('.searchable-item');
  const emptyEl = document.getElementById('empty_' + type);
  let visibleCount = 0;

  items.forEach(item => {
    let match = false;
    if (type === 'guru') {
      const nama = item.getAttribute('data-nama') || '';
      const nip = item.getAttribute('data-nip') || '';
      match = nama.includes(query) || nip.includes(query);
    } else if (type === 'siswa') {
      const nama = item.getAttribute('data-nama') || '';
      const nisn = item.getAttribute('data-nisn') || '';
      const rombel = item.getAttribute('data-rombel') || '';
      match = nama.includes(query) || nisn.includes(query) || rombel.includes(query);
    }

    if (match) {
      item.style.setProperty('display', 'flex', 'important');
      visibleCount++;
    } else {
      item.style.setProperty('display', 'none', 'important');
    }
  });

  if (emptyEl) {
    if (visibleCount === 0) {
      emptyEl.classList.remove('d-none');
    } else {
      emptyEl.classList.add('d-none');
    }
  }
}

function selectSearchable(type, id, displayText) {
  const hiddenInput = document.getElementById('hidden_' + type + '_id');
  const label = document.getElementById('label_' + type);
  const btnClear = document.getElementById('btn_clear_' + type);
  const menu = document.getElementById('menu_' + type);
  const list = document.getElementById('list_' + type);
  const trigger = document.getElementById('trigger_' + type);

  if (hiddenInput) hiddenInput.value = id;
  if (trigger) trigger.classList.remove('border-danger');

  if (label) {
    label.textContent = displayText;
    label.classList.remove('text-muted');
    label.classList.add('text-dark', 'fw-semibold');
  }

  if (btnClear) btnClear.classList.remove('d-none');

  if (list) {
    list.querySelectorAll('.searchable-item').forEach(el => {
      const isSelected = el.getAttribute('data-id') === String(id);
      el.classList.toggle('selected', isSelected);
      const check = el.querySelector('.check-icon');
      if (check) {
        check.classList.toggle('d-none', !isSelected);
      }
    });
  }

  if (menu) menu.classList.add('d-none');
}

function clearSearchable(type, event) {
  if (event) {
    event.stopPropagation();
  }
  const hiddenInput = document.getElementById('hidden_' + type + '_id');
  const label = document.getElementById('label_' + type);
  const btnClear = document.getElementById('btn_clear_' + type);
  const list = document.getElementById('list_' + type);
  const searchInput = document.getElementById('search_input_' + type);
  const emptyEl = document.getElementById('empty_' + type);

  if (hiddenInput) hiddenInput.value = '';
  if (label) {
    label.textContent = (type === 'guru') ? '— Cari / Pilih Guru Tujuan —' : '— Cari / Pilih Siswa Tujuan —';
    label.classList.remove('text-dark', 'fw-semibold');
    label.classList.add('text-muted');
  }

  if (btnClear) btnClear.classList.add('d-none');

  if (list) {
    list.querySelectorAll('.searchable-item').forEach(el => {
      el.classList.remove('selected');
      const check = el.querySelector('.check-icon');
      if (check) check.classList.add('d-none');
      el.style.setProperty('display', 'flex', 'important');
    });
  }

  if (searchInput) searchInput.value = '';
  if (emptyEl) emptyEl.classList.add('d-none');
}

// Event listener tutup saat klik di luar
document.addEventListener('click', function(e) {
  ['guru', 'siswa'].forEach(type => {
    const wrapper = document.getElementById('wrapper_' + type);
    const menu = document.getElementById('menu_' + type);
    if (wrapper && menu && !wrapper.contains(e.target)) {
      menu.classList.add('d-none');
    }
  });
});

// Validasi form saat submit agar pilihan wajib diisi
document.addEventListener('DOMContentLoaded', function() {
  const formPtk = document.querySelector('#modalUploadArsipPtk form');
  if (formPtk) {
    formPtk.addEventListener('submit', function(e) {
      const hidden = document.getElementById('hidden_guru_id');
      if (!hidden || !hidden.value) {
        e.preventDefault();
        const trigger = document.getElementById('trigger_guru');
        if (trigger) {
          trigger.classList.add('border-danger');
          trigger.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        toggleSearchable('guru');
      }
    });
  }

  const formSiswa = document.querySelector('#modalUploadArsipSiswa form');
  if (formSiswa) {
    formSiswa.addEventListener('submit', function(e) {
      const hidden = document.getElementById('hidden_siswa_id');
      if (!hidden || !hidden.value) {
        e.preventDefault();
        const trigger = document.getElementById('trigger_siswa');
        if (trigger) {
          trigger.classList.add('border-danger');
          trigger.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        toggleSearchable('siswa');
      }
    });
  }
});
</script>
@endpush
