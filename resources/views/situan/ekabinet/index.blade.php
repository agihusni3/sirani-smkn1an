@extends('layouts.app')

@section('title', 'E-Kabinet & E-Arsip Digital — SITUAN SMKN 1 AN')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">

  {{-- Page Header --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
      <div class="d-flex align-items-center gap-2 mb-1">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill" style="font-size:11px; font-weight:700;">
          <i class="bi bi-archive-fill me-1"></i> E-Kabinet Digital Terpusat
        </span>
        <span class="text-muted" style="font-size:12px;">Sentral Berkas Kepegawaian &amp; Lembaga</span>
      </div>
      <h1 class="h3 mb-0 fw-bold" style="color:var(--text); letter-spacing:-0.02em;">Lemari Arsip &amp; E-Kabinet Sekolah</h1>
      <p class="text-muted mb-0 small">Penyimpanan cloud terpadu berkas PTK, regulasi kedinasan, akreditasi, dan naskah kerjasama (MoU) industri.</p>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('situan.index') }}" class="btn btn-outline-secondary btn-sm" style="font-weight:600;">
        <i class="bi bi-arrow-left me-1"></i> Dasbor SITUAN
      </a>
      <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalUploadArsipPtk" style="font-weight:700;">
        <i class="bi bi-person-badge-fill me-1"></i> Unggah Berkas PTK
      </button>
      <button type="button" class="btn btn-dark btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalUploadArsipLembaga" style="font-weight:700;">
        <i class="bi bi-building-fill-add me-1"></i> Unggah Dokumen Lembaga / MoU
      </button>
    </div>
  </div>

  {{-- Quick Metric Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:var(--surface); border:1px solid var(--border)!important;">
        <div class="text-muted small fw-semibold mb-1">Total Arsip PTK</div>
        <div class="h3 mb-0 fw-bolder text-primary">{{ $totalArsipPtk }}</div>
        <div class="text-muted" style="font-size:11px;">Berkas digital pegawai tersimpan</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:var(--surface); border:1px solid var(--border)!important;">
        <div class="text-muted small fw-semibold mb-1">PTK Terarsip</div>
        <div class="h3 mb-0 fw-bolder text-success">{{ $totalGuruWithArsip }} <span class="fs-6 fw-normal text-muted">/ {{ $gurus->count() }} Guru</span></div>
        <div class="text-success" style="font-size:11px;"><i class="bi bi-check-circle-fill me-1"></i>Memiliki data berkas</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:var(--surface); border:1px solid var(--border)!important;">
        <div class="text-muted small fw-semibold mb-1">Arsip Lembaga &amp; Aset</div>
        <div class="h3 mb-0 fw-bolder text-info">{{ $totalArsipSekolah }}</div>
        <div class="text-muted" style="font-size:11px;">Akreditasi, SK, &amp; Kurikulum</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:var(--surface); border:1px solid var(--border)!important;">
        <div class="text-muted small fw-semibold mb-1">MoU Industri Aktif</div>
        <div class="h3 mb-0 fw-bolder text-warning">{{ $totalMouAktif }}</div>
        <div class="text-muted" style="font-size:11px;">Kerjasama DUDI &amp; PKL berjalan</div>
      </div>
    </div>
  </div>

  {{-- Navigation Tabs --}}
  <ul class="nav nav-pills mb-3 gap-2" id="ekabinetTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link {{ $activeTab === 'ptk' ? 'active' : '' }} fw-bold" href="{{ route('situan.ekabinet.index', ['tab' => 'ptk']) }}">
        <i class="bi bi-person-vcard me-1"></i> Laci Arsip PTK &amp; Kepegawaian
        <span class="badge bg-light text-dark ms-1">{{ $totalArsipPtk }}</span>
      </a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link {{ $activeTab === 'lembaga' ? 'active' : '' }} fw-bold" href="{{ route('situan.ekabinet.index', ['tab' => 'lembaga']) }}">
        <i class="bi bi-buildings-fill me-1"></i> Laci Arsip Lembaga &amp; MoU DUDI
        <span class="badge bg-light text-dark ms-1">{{ $totalArsipSekolah }}</span>
      </a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link {{ $activeTab === 'kelengkapan' ? 'active' : '' }} fw-bold" href="{{ route('situan.ekabinet.index', ['tab' => 'kelengkapan']) }}">
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

  {{-- Tab 2: Lemari Berkas Lembaga & MoU --}}
  @if($activeTab === 'lembaga')
    <div class="card border-0 shadow-sm rounded-3 mb-4" style="background:var(--surface); border:1px solid var(--border)!important;">
      <div class="card-body p-3">
        <form method="GET" action="{{ route('situan.ekabinet.index') }}" class="row g-2 align-items-center">
          <input type="hidden" name="tab" value="lembaga">
          <div class="col-md-6 col-12">
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
              <input type="text" name="q_lembaga" value="{{ request('q_lembaga') }}" class="form-control border-start-0" placeholder="Cari nama dokumen, mitra instansi, atau nomor SK...">
            </div>
          </div>
          <div class="col-md-4 col-8">
            <select name="kategori_lembaga" class="form-select form-select-sm">
              <option value="">— Semua Kategori Dokumen Sekolah —</option>
              <option value="akreditasi" {{ request('kategori_lembaga') == 'akreditasi' ? 'selected' : '' }}>Akreditasi Sekolah (BAN-SM)</option>
              <option value="mou_industri" {{ request('kategori_lembaga') == 'mou_industri' ? 'selected' : '' }}>MoU Kemitraan DUDI / Industri (PKL)</option>
              <option value="izin_operasional" {{ request('kategori_lembaga') == 'izin_operasional' ? 'selected' : '' }}>Izin Operasional &amp; Pendirian</option>
              <option value="sertifikat_aset" {{ request('kategori_lembaga') == 'sertifikat_aset' ? 'selected' : '' }}>Sertifikat Tanah &amp; Aset Gedung</option>
              <option value="kurikulum_kosp" {{ request('kategori_lembaga') == 'kurikulum_kosp' ? 'selected' : '' }}>Dokumen Kurikulum (KOSP)</option>
              <option value="pedoman_sop" {{ request('kategori_lembaga') == 'pedoman_sop' ? 'selected' : '' }}>Pedoman Mutu &amp; SOP</option>
              <option value="sk_kelembagaan" {{ request('kategori_lembaga') == 'sk_kelembagaan' ? 'selected' : '' }}>SK Kelembagaan / Komite</option>
              <option value="lainnya" {{ request('kategori_lembaga') == 'lainnya' ? 'selected' : '' }}>Dokumen Lainnya</option>
            </select>
          </div>
          <div class="col-md-2 col-4 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">Filter</button>
            <a href="{{ route('situan.ekabinet.index', ['tab' => 'lembaga']) }}" class="btn btn-sm btn-outline-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
          </div>
        </form>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
          <thead class="table-light" style="border-bottom:1.5px solid var(--border);">
            <tr>
              <th class="py-3 px-3" style="width:160px;">Kategori Berkas</th>
              <th class="py-3 px-3">Nama Dokumen / Arsip</th>
              <th class="py-3 px-3" style="width:180px;">Mitra / Lembaga</th>
              <th class="py-3 px-3" style="width:160px;">Nomor Dokumen</th>
              <th class="py-3 px-3" style="width:150px;">Masa Berlaku</th>
              <th class="py-3 px-3 text-center" style="width:130px;">Aksi</th>
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
                    <div class="text-muted" style="font-size:11px;">{{ Str::limit($arsip->keterangan, 60) }}</div>
                  @endif
                </td>
                <td class="px-3 text-muted">
                  @if($arsip->mitra_instansi)
                    <span class="fw-semibold text-dark"><i class="bi bi-building me-1 text-primary"></i>{{ $arsip->mitra_instansi }}</span>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td class="px-3 text-muted">
                  {{ $arsip->nomor_dokumen ?: '-' }}
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
                <td colspan="6" class="text-center py-5 text-muted">
                  <i class="bi bi-buildings fs-1 d-block mb-2 text-secondary"></i>
                  Belum ada dokumen lembaga atau MoU yang tersimpan.<br>
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
              <th class="py-3 px-2 text-center" style="width:110px;">SK KGB</th>
              <th class="py-3 px-2 text-center" style="width:110px;">SK Pangkat</th>
              <th class="py-3 px-2 text-center" style="width:110px;">Ijazah</th>
              <th class="py-3 px-2 text-center" style="width:110px;">Serdik</th>
              <th class="py-3 px-2 text-center" style="width:110px;">KTP / KK</th>
              <th class="py-3 px-3 text-center" style="width:140px;">Aksi</th>
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
      <div class="modal-header bg-primary text-white">
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
            <select name="guru_id" class="form-select" required>
              <option value="">— Pilih Guru Tujuan —</option>
              @foreach($gurus as $g)
                <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>
                  {{ $g->nama }} (NIP: {{ $g->nip ?: '-' }})
                </option>
              @endforeach
            </select>
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
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold">Simpan ke E-Kabinet</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- MODAL 2: UNGGAH DOKUMEN LEMBAGA / MOU INDUSTRI --}}
<div class="modal fade" id="modalUploadArsipLembaga" tabindex="-1" aria-labelledby="modalUploadArsipLembagaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title fw-bold" id="modalUploadArsipLembagaLabel">
          <i class="bi bi-building-fill-add me-1"></i> Unggah Dokumen Lembaga / MoU
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('situan.ekabinet.lembaga.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold small">Kategori Arsip Lembaga <span class="text-danger">*</span></label>
            <select name="kategori_arsip" class="form-select" required>
              <option value="mou_industri">MoU Kemitraan DUDI / Mitra Industri (PKL)</option>
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
            <input type="text" name="nama_arsip" class="form-control" placeholder="Contoh: MoU Kerjasama PKL PT Indomobil Prima" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Mitra Instansi / Perusahaan (Khusus MoU)</label>
            <input type="text" name="mitra_instansi" class="form-control" placeholder="Contoh: PT Telkom Akses / RSUD Pringsewu">
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-semibold small">Nomor Surat / Perjanjian</label>
              <input type="text" name="nomor_dokumen" class="form-control" placeholder="421.5/022/SMKN1/2026">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold small">Tanggal Mulai / Dokumen</label>
              <input type="date" name="tanggal_dokumen" class="form-control">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Masa Berlaku Berakhir (Khusus Akreditasi / MoU)</label>
            <input type="date" name="tanggal_berakhir" class="form-control">
            <div class="form-text">Biarkan kosong jika berlaku seterusnya / permanen.</div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Pilih File Berkas (PDF/JPG/PNG, Maks 10MB) <span class="text-danger">*</span></label>
            <input type="file" name="file_dokumen" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
          </div>

          <div class="mb-2">
            <label class="form-label fw-semibold small">Catatan / Keterangan Tambahan</label>
            <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan bidang keahlian, ruang lingkup MoU, atau lokasi penyimpanan fisik..."></textarea>
          </div>
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-dark btn-sm px-3 fw-bold">Simpan Dokumen Lembaga</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
