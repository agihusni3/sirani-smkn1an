@extends('layouts.app')

@section('title', 'Biodata & Berkas Digital Saya — ' . $guru->nama)

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" style="max-width:1400px; margin:0 auto;">

  {{-- Alert Flash Notifikasi --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2" role="alert" style="background:#ECFDF5; color:#065F46; border-left:4px solid #10B981!important;">
      <i class="bi bi-check-circle-fill fs-5 text-success"></i>
      <div>{{ session('success') }}</div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center gap-2" role="alert" style="background:#FEF2F2; color:#991B1B; border-left:4px solid #EF4444!important;">
      <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
      <div>{{ session('error') }}</div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
      <div class="fw-bold mb-1"><i class="bi bi-x-circle me-1"></i> Terjadi kesalahan input:</div>
      <ul class="mb-0 ps-3">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  {{-- HERO PROFILE BANNER --}}
  <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); color:#FFFFFF;">
    <div class="card-body p-4 p-md-4">
      <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-4">
        
        {{-- Identitas Avatar & Info --}}
        <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-4 text-center text-sm-start">
          <div class="position-relative">
            <img src="{{ $guru->foto_url }}" alt="{{ $guru->nama }}" class="rounded-circle shadow" style="width:96px; height:96px; object-fit:cover; border:3.5px solid rgba(255,255,255,0.2);" />
            <span class="position-absolute bottom-0 end-0 p-2 rounded-circle border border-2 border-dark" style="background: {{ $guru->status === 'aktif' ? '#10B981' : '#64748B' }};" title="Status: {{ ucfirst($guru->status) }}"></span>
          </div>

          <div>
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start gap-2 mb-1">
              <span class="badge px-2.5 py-1 rounded-pill" style="background:rgba(59,130,246,0.2); color:#60A5FA; border:1px solid rgba(96,165,250,0.3); font-size:11px; font-weight:700;">
                <i class="bi bi-person-badge-fill me-1"></i> {{ $guru->label_kepegawaian ?? ($guru->status_kepegawaian ?: 'PTK') }}
              </span>
              @if($guru->golongan_ruang)
                <span class="badge px-2.5 py-1 rounded-pill" style="background:rgba(245,158,11,0.2); color:#FBBF24; border:1px solid rgba(251,191,36,0.3); font-size:11px; font-weight:700;">
                  Gol. {{ $guru->golongan_ruang }}
                </span>
              @endif
              <span class="badge px-2.5 py-1 rounded-pill" style="background:rgba(16,185,129,0.2); color:#34D399; border:1px solid rgba(52,211,153,0.3); font-size:11px; font-weight:700;">
                {{ $arsips->count() }} Dokumen Digital
              </span>
            </div>

            <h2 class="h4 fw-bold mb-1" style="letter-spacing:-0.02em; color:#FFFFFF;">
              {{ $guru->nama_lengkap_gelar ?: $guru->nama }}
            </h2>

            <div class="text-white-50 small d-flex flex-wrap justify-content-center justify-content-sm-start gap-3">
              <span><i class="bi bi-hash"></i> NIP: <strong class="text-white">{{ $guru->nip ?: '—' }}</strong></span>
              <span><i class="bi bi-award"></i> NUPTK: <strong class="text-white">{{ $guru->nuptk ?: '—' }}</strong></span>
              <span><i class="bi bi-briefcase"></i> Jabatan: <strong class="text-white">{{ $guru->jabatan ?: 'Guru' }}</strong></span>
            </div>
          </div>
        </div>

        {{-- Action Buttons & Switcher for Admin --}}
        <div class="d-flex flex-column align-items-stretch align-items-lg-end gap-2 w-100 w-lg-auto">
          @if(auth()->user()->isAdmin() && $semuaGuru->count() > 0)
            <div class="d-flex align-items-center gap-2 mb-2 p-2 rounded-3" style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15);">
              <span class="text-white-50 small" style="white-space:nowrap;"><i class="bi bi-eye"></i> Pratinjau PTK:</span>
              <select class="form-select form-select-sm" onchange="location.href='?guru_id=' + this.value" style="background:#0F172A; color:#FFF; border-color:rgba(255,255,255,0.2); min-width:200px; font-size:12px;">
                @foreach($semuaGuru as $g)
                  <option value="{{ $g->id }}" {{ $g->id == $guru->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                @endforeach
              </select>
            </div>
          @endif

          <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-end">
            <a href="{{ route('kartu.digital.guru', $guru->id) }}" target="_blank" class="btn btn-sm px-3 text-white" style="background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.25); font-weight:600; border-radius:8px;">
              <i class="bi bi-qr-code me-1.5"></i> Kartu Digital Barcode
            </a>
            <button type="button" class="btn btn-sm btn-primary px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUnggahBerkas" style="font-weight:700; border-radius:8px; background:#2563EB; border-color:#2563EB;">
              <i class="bi bi-cloud-arrow-up-fill me-1.5"></i> Unggah Berkas Saya
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>

  {{-- NAV TABS INTERFACE --}}
  <ul class="nav nav-pills gap-2 mb-4 p-1.5 rounded-3 shadow-sm" id="ptkTab" role="tablist" style="background:var(--surface); border:1px solid var(--border); width:fit-content;">
    <li class="nav-item" role="presentation">
      <button class="nav-link active px-3 py-2 fw-bold d-flex align-items-center gap-2" id="tab-biodata-btn" data-bs-toggle="pill" data-bs-target="#tab-biodata" type="button" role="tab" style="border-radius:6px; font-size:13px;">
        <i class="bi bi-person-vcard"></i> Biodata Kepegawaian
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link px-3 py-2 fw-bold d-flex align-items-center gap-2" id="tab-berkas-btn" data-bs-toggle="pill" data-bs-target="#tab-berkas" type="button" role="tab" style="border-radius:6px; font-size:13px;">
        <i class="bi bi-folder2-open"></i> Lemari Berkas Digital
        <span class="badge rounded-pill bg-primary" style="font-size:10px;">{{ $arsips->count() }}</span>
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link px-3 py-2 fw-bold d-flex align-items-center gap-2" id="tab-presensi-btn" data-bs-toggle="pill" data-bs-target="#tab-presensi" type="button" role="tab" style="border-radius:6px; font-size:13px;">
        <i class="bi bi-calendar-check"></i> Catatan Presensi Saya
      </button>
    </li>
  </ul>

  {{-- TAB CONTENTS --}}
  <div class="tab-content" id="ptkTabContent">

    {{-- TAB 1: BIODATA KEPEGAWAIAN --}}
    <div class="tab-pane fade show active" id="tab-biodata" role="tabpanel" tabindex="0">
      <div class="row g-4">
        
        {{-- Kolom Kiri: Identitas Diri & Kontak --}}
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm rounded-4 h-100" style="background:var(--surface); border:1px solid var(--border)!important;">
            <div class="card-header bg-transparent py-3 px-4 d-flex align-items-center gap-2" style="border-bottom:1px solid var(--border);">
              <i class="bi bi-person-lines-fill text-primary fs-5"></i>
              <h5 class="card-title fw-bold mb-0" style="font-size:15px; color:var(--text);">Identitas Diri &amp; Kependudukan</h5>
            </div>
            <div class="card-body p-4">
              <table class="table table-borderless align-middle mb-0" style="font-size:13px;">
                <tbody>
                  <tr>
                    <td class="text-muted ps-0" style="width:160px;">Nama Lengkap</td>
                    <td>: <strong style="color:var(--text);">{{ $guru->nama_lengkap ?: $guru->nama }}</strong></td>
                  </tr>
                  <tr>
                    <td class="text-muted ps-0">Gelar</td>
                    <td>: {{ ($guru->gelar_depan ? $guru->gelar_depan . ' ' : '') . ($guru->gelar_belakang ?: '—') }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted ps-0">NIK (No. KTP)</td>
                    <td>: <span class="font-monospace" style="color:var(--text);">{{ $guru->nik ?: '—' }}</span></td>
                  </tr>
                  <tr>
                    <td class="text-muted ps-0">Tempat, Tgl Lahir</td>
                    <td>: {{ $guru->tempat_lahir ?: '—' }}, {{ $guru->tanggal_lahir ? \Carbon\Carbon::parse($guru->tanggal_lahir)->translatedFormat('d F Y') : '—' }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted ps-0">Jenis Kelamin</td>
                    <td>: {{ $guru->jenis_kelamin === 'L' ? 'Laki-laki' : ($guru->jenis_kelamin === 'P' ? 'Perempuan' : '—') }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted ps-0">Agama</td>
                    <td>: {{ $guru->agama ?: '—' }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted ps-0">Nomor WhatsApp / HP</td>
                    <td>: 
                      @if($guru->no_hp)
                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $guru->no_hp)) }}" target="_blank" class="text-decoration-none fw-bold" style="color:#10B981;">
                          <i class="bi bi-whatsapp me-1"></i>{{ $guru->no_hp }}
                        </a>
                      @else
                        —
                      @endif
                    </td>
                  </tr>
                  <tr>
                    <td class="text-muted ps-0 align-top">Alamat Domisili</td>
                    <td class="align-top">: {{ $guru->alamat ?: '—' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {{-- Kolom Kanan: Kepegawaian & Riwayat Pangkat --}}
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm rounded-4 h-100" style="background:var(--surface); border:1px solid var(--border)!important;">
            <div class="card-header bg-transparent py-3 px-4 d-flex align-items-center gap-2" style="border-bottom:1px solid var(--border);">
              <i class="bi bi-briefcase-fill text-warning fs-5"></i>
              <h5 class="card-title fw-bold mb-0" style="font-size:15px; color:var(--text);">Status Kepegawaian &amp; Riwayat Jabatan</h5>
            </div>
            <div class="card-body p-4">
              <table class="table table-borderless align-middle mb-0" style="font-size:13px;">
                <tbody>
                  <tr>
                    <td class="text-muted ps-0" style="width:170px;">Status Kepegawaian</td>
                    <td>: 
                      <span class="badge px-2.5 py-1 rounded-pill" style="background:rgba(59,130,246,0.15); color:#2563EB; font-weight:700;">
                        {{ $guru->label_kepegawaian ?? ($guru->status_kepegawaian ?: 'Pegawai') }}
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-muted ps-0">Pangkat / Gol. Ruang</td>
                    <td>: <strong style="color:var(--text);">{{ $guru->pangkat ?: '—' }} ({{ $guru->golongan_ruang ?: '—' }})</strong></td>
                  </tr>
                  <tr>
                    <td class="text-muted ps-0">Jenis PTK / Jabatan</td>
                    <td>: {{ $guru->jenis_ptk ?: ($guru->jabatan ?: 'Guru Mata Pelajaran') }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted ps-0">Tugas Tambahan</td>
                    <td>: {{ $guru->tugas_tambahan ?: '—' }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted ps-0">TMT CPNS / Mulai Tugas</td>
                    <td>: {{ $guru->tmt_cpns ? \Carbon\Carbon::parse($guru->tmt_cpns)->translatedFormat('d F Y') : '—' }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted ps-0">TMT Pangkat Terakhir</td>
                    <td>: {{ $guru->tmt_pangkat ? \Carbon\Carbon::parse($guru->tmt_pangkat)->translatedFormat('d F Y') : '—' }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted ps-0">TMT Berkala (KGB) Terakhir</td>
                    <td>: {{ $guru->tmt_kgb_terakhir ? \Carbon\Carbon::parse($guru->tmt_kgb_terakhir)->translatedFormat('d F Y') : '—' }}</td>
                  </tr>
                  <tr>
                    <td class="text-muted ps-0">Jadwal KGB Berikutnya</td>
                    <td>: 
                      @if($guru->tmt_kgb_berikutnya)
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 rounded-pill">
                          <i class="bi bi-clock-history me-1"></i> {{ \Carbon\Carbon::parse($guru->tmt_kgb_berikutnya)->translatedFormat('d F Y') }}
                        </span>
                      @else
                        —
                      @endif
                    </td>
                  </tr>
                </tbody>
              </table>

              <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between">
                <span class="text-muted small">Kelengkapan berkas digital Anda:</span>
                <span class="badge bg-primary px-3 py-1.5 rounded-pill">{{ $arsips->count() }} Dokumen Tersimpan</span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    {{-- TAB 2: LEMARI BERKAS DIGITAL SAYA --}}
    <div class="tab-pane fade" id="tab-berkas" role="tabpanel" tabindex="0">
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background:var(--surface); border:1px solid var(--border)!important;">
        
        <div class="card-header bg-transparent py-3 px-4 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3" style="border-bottom:1px solid var(--border);">
          <div>
            <h5 class="fw-bold mb-0" style="font-size:15px; color:var(--text);">
              <i class="bi bi-folder-symlink-fill text-primary me-2"></i>Lemari Berkas Digital PTK (E-Arsip Pribadi)
            </h5>
            <div class="text-muted small">Seluruh dokumen kedinasan &amp; kependudukan yang tersimpan aman di server sekolah.</div>
          </div>

          <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm rounded-3" data-bs-toggle="modal" data-bs-target="#modalUnggahBerkas" style="font-weight:700;">
            <i class="bi bi-cloud-arrow-up-fill me-1.5"></i> Unggah Berkas Baru
          </button>
        </div>

        <div class="card-body p-0">
          @if($arsips->isEmpty())
            <div class="text-center py-5 px-3">
              <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width:64px; height:64px; background:var(--bg-2); color:var(--text-muted);">
                <i class="bi bi-folder2-open fs-2"></i>
              </div>
              <h6 class="fw-bold mb-1" style="color:var(--text);">Belum Ada Berkas Digital Tersimpan</h6>
              <p class="text-muted small mb-3" style="max-width:400px; margin:0 auto;">
                Anda dapat mengunggah scan KTP, SK Pangkat, SK KGB, Ijazah, atau Sertifikat Pendidik secara mandiri untuk arsip digital pribadi.
              </p>
              <button type="button" class="btn btn-primary btn-sm px-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalUnggahBerkas">
                <i class="bi bi-plus-lg me-1"></i> Unggah Dokumen Pertama
              </button>
            </div>
          @else
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                <thead class="table-light" style="border-bottom:1.5px solid var(--border);">
                  <tr>
                    <th class="py-3 px-4" style="width:60px;">No</th>
                    <th class="py-3 px-3" style="width:180px;">Kategori Berkas</th>
                    <th class="py-3 px-3">Nama &amp; Keterangan Dokumen</th>
                    <th class="py-3 px-3" style="width:190px;">Nomor Dokumen</th>
                    <th class="py-3 px-3" style="width:130px;">Tgl Dokumen</th>
                    <th class="py-3 px-4 text-center" style="width:180px;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($arsips as $idx => $dok)
                    @php
                      $badgeCat = match($dok->kategori_berkas) {
                        'sk_cpns', 'sk_pns', 'sk_pppk' => ['bg' => 'rgba(59,130,246,0.15)', 'color' => '#2563EB', 'label' => 'SK Pengangkatan'],
                        'sk_pangkat_terakhir'          => ['bg' => 'rgba(16,185,129,0.15)', 'color' => '#059669', 'label' => 'SK Pangkat'],
                        'sk_kgb_terakhir'              => ['bg' => 'rgba(245,158,11,0.15)', 'color' => '#D97706', 'label' => 'SK KGB'],
                        'ktp', 'kk'                    => ['bg' => 'rgba(107,114,128,0.15)', 'color' => '#4B5563', 'label' => 'Kependudukan'],
                        'ijazah', 'transkrip'          => ['bg' => 'rgba(139,92,246,0.15)', 'color' => '#7C3AED', 'label' => 'Pendidikan'],
                        'sertifikat_pendidik'          => ['bg' => 'rgba(236,72,153,0.15)', 'color' => '#DB2777', 'label' => 'Serdik'],
                        'kartu_pegawai'                => ['bg' => 'rgba(20,184,166,0.15)', 'color' => '#0D9488', 'label' => 'Kartu Pegawai'],
                        default                        => ['bg' => 'rgba(100,116,139,0.15)', 'color' => '#475569', 'label' => 'Lainnya'],
                      };
                      $fileUrl = asset('storage/' . $dok->file_path);
                      $isPdf = str_ends_with(strtolower($dok->file_path), '.pdf');
                    @endphp
                    <tr>
                      <td class="py-3 px-4 text-muted text-center">{{ $idx + 1 }}</td>
                      <td class="py-3 px-3">
                        <span class="badge px-2.5 py-1 rounded-pill" style="background:{{ $badgeCat['bg'] }}; color:{{ $badgeCat['color'] }}; font-weight:700; font-size:11.5px;">
                          {{ $badgeCat['label'] }}
                        </span>
                      </td>
                      <td class="py-3 px-3">
                        <div class="fw-bold" style="color:var(--text);">{{ $dok->nama_dokumen }}</div>
                        <div class="text-muted" style="font-size:11px;">
                          Diunggah: {{ $dok->created_at ? $dok->created_at->translatedFormat('d M Y, H:i') : '—' }}
                        </div>
                      </td>
                      <td class="py-3 px-3 font-monospace small" style="color:var(--text);">
                        {{ $dok->nomor_dokumen ?: '—' }}
                      </td>
                      <td class="py-3 px-3 text-muted small">
                        {{ $dok->tanggal_dokumen ? \Carbon\Carbon::parse($dok->tanggal_dokumen)->translatedFormat('d M Y') : '—' }}
                      </td>
                      <td class="py-3 px-4 text-center">
                        <div class="d-inline-flex gap-1">
                          {{-- Tombol Buka Preview Modal --}}
                          <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1 rounded-2" onclick="bukaPreviewDokumen('{{ $fileUrl }}', '{{ addslashes($dok->nama_dokumen) }}', {{ $isPdf ? 'true' : 'false' }})" title="Lihat Pratinjau">
                            <i class="bi bi-eye"></i> Buka
                          </button>

                          {{-- Tombol Unduh Langsung --}}
                          <a href="{{ $fileUrl }}" download="{{ $dok->nama_dokumen }}" class="btn btn-sm btn-outline-secondary px-2 py-1 rounded-2" title="Unduh File">
                            <i class="bi bi-download"></i>
                          </a>

                          {{-- Tombol Hapus Dokumen --}}
                          <form action="{{ route('ptk.hapus-berkas', $dok->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas \'{{ addslashes($dok->nama_dokumen) }}\'?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1 rounded-2" title="Hapus Dokumen">
                              <i class="bi bi-trash"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>

      </div>
    </div>

    {{-- TAB 3: CATATAN PRESENSI SAYA --}}
    <div class="tab-pane fade" id="tab-presensi" role="tabpanel" tabindex="0">
      
      {{-- Stats Cards --}}
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 text-center" style="background:#ECFDF5; border:1px solid #A7F3D0!important;">
            <div class="display-6 fw-bold" style="color:#059669;">{{ $stats['hadir'] }}</div>
            <div class="small fw-bold text-uppercase" style="color:#065F46; font-size:11px;">Hadir Tepat Waktu</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 text-center" style="background:#FFFBEB; border:1px solid #FDE68A!important;">
            <div class="display-6 fw-bold" style="color:#D97706;">{{ $stats['terlambat'] }}</div>
            <div class="small fw-bold text-uppercase" style="color:#92400E; font-size:11px;">Terlambat</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 text-center" style="background:#EFF6FF; border:1px solid #BFDBFE!important;">
            <div class="display-6 fw-bold" style="color:#2563EB;">{{ $stats['izin'] }}</div>
            <div class="small fw-bold text-uppercase" style="color:#1E40AF; font-size:11px;">Izin / Sakit / Dinas</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 text-center" style="background:#F8FAFC; border:1px solid #E2E8F0!important;">
            <div class="display-6 fw-bold text-primary">{{ $stats['persen'] }}%</div>
            <div class="small fw-bold text-uppercase text-muted" style="font-size:11px;">Skor Disiplin ({{ $stats['nama_bulan'] }})</div>
          </div>
        </div>
      </div>

      {{-- Attendance Logs Table --}}
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background:var(--surface); border:1px solid var(--border)!important;">
        <div class="card-header bg-transparent py-3 px-4" style="border-bottom:1px solid var(--border);">
          <h5 class="fw-bold mb-0" style="font-size:15px; color:var(--text);">
            <i class="bi bi-clock-history text-primary me-2"></i>Log Kehadiran Bulan {{ $stats['nama_bulan'] }}
          </h5>
        </div>
        <div class="card-body p-0">
          @if($absensisBulanIni->isEmpty())
            <div class="text-center py-5 text-muted small">
              Belum ada data presensi tercatat untuk bulan ini.
            </div>
          @else
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                <thead class="table-light" style="border-bottom:1.5px solid var(--border);">
                  <tr>
                    <th class="py-3 px-4" style="width:60px;">No</th>
                    <th class="py-3 px-3">Hari &amp; Tanggal</th>
                    <th class="py-3 px-3 text-center">Jam Masuk</th>
                    <th class="py-3 px-3 text-center">Jam Pulang</th>
                    <th class="py-3 px-3 text-center">Status</th>
                    <th class="py-3 px-4">Keterangan Sumber</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($absensisBulanIni as $i => $absen)
                    <tr>
                      <td class="py-3 px-4 text-muted text-center">{{ $i + 1 }}</td>
                      <td class="py-3 px-3 fw-bold" style="color:var(--text);">
                        {{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('l, d F Y') }}
                      </td>
                      <td class="py-3 px-3 text-center font-monospace">
                        {{ $absen->jam_masuk ? substr($absen->jam_masuk, 0, 5) . ' WIB' : '—' }}
                      </td>
                      <td class="py-3 px-3 text-center font-monospace">
                        {{ $absen->jam_pulang ? substr($absen->jam_pulang, 0, 5) . ' WIB' : '—' }}
                      </td>
                      <td class="py-3 px-3 text-center">
                        @if($absen->status === 'hadir')
                          <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill">Hadir</span>
                        @elseif($absen->status === 'terlambat')
                          <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1 rounded-pill">Terlambat</span>
                        @elseif($absen->status === 'alpha')
                          <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill">Alpha</span>
                        @else
                          <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 rounded-pill">{{ ucfirst($absen->status) }}</span>
                        @endif
                      </td>
                      <td class="py-3 px-4 text-muted small">
                        {{ $absen->keterangan ?: 'Terekam Smart Gate Presensi' }}
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>

    </div>

  </div>

</div>

{{-- MODAL UNGGAH BERKAS MANDIRI --}}
<div class="modal fade" id="modalUnggahBerkas" tabindex="-1" aria-labelledby="modalUnggahBerkasLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="modalUnggahBerkasLabel" style="color:var(--text);">
          <i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i>Unggah Berkas Digital Saya
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="{{ route('ptk.unggah-berkas') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(auth()->user()->isAdmin())
          <input type="hidden" name="guru_id" value="{{ $guru->id }}" />
        @endif

        <div class="modal-body pt-3">
          <div class="mb-3">
            <label class="form-label fw-bold small">Kategori Dokumen <span class="text-danger">*</span></label>
            <select name="kategori_berkas" class="form-select form-select-sm rounded-3" required>
              <option value="">-- Pilih Kategori Berkas --</option>
              <option value="sk_cpns">SK CPNS</option>
              <option value="sk_pns">SK PNS / PPPK Awal</option>
              <option value="sk_pangkat_terakhir">SK Kenaikan Pangkat Terakhir</option>
              <option value="sk_kgb_terakhir">SK Kenaikan Gaji Berkala (KGB) Terakhir</option>
              <option value="ijazah">Ijazah Terakhir</option>
              <option value="transkrip">Transkrip Nilai</option>
              <option value="sertifikat_pendidik">Sertifikat Pendidik (Serdik)</option>
              <option value="kartu_pegawai">Kartu Pegawai (Karpeg / e-KTP)</option>
              <option value="ktp">KTP Pribadi</option>
              <option value="kk">Kartu Keluarga (KK)</option>
              <option value="lainnya">Dokumen Kedinasan Lainnya</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold small">Nama / Judul Dokumen <span class="text-danger">*</span></label>
            <input type="text" name="nama_dokumen" class="form-control form-control-sm rounded-3" placeholder="Contoh: SK KGB Periode 2026 atau Ijazah S1" required />
          </div>

          <div class="row g-2 mb-3">
            <div class="col-sm-7">
              <label class="form-label fw-bold small">Nomor Dokumen/SK <span class="text-muted fw-normal">(Opsional)</span></label>
              <input type="text" name="nomor_dokumen" class="form-control form-control-sm rounded-3" placeholder="Nomor surat / nomor SK" />
            </div>
            <div class="col-sm-5">
              <label class="form-label fw-bold small">Tanggal Dokumen <span class="text-muted fw-normal">(Opsional)</span></label>
              <input type="date" name="tanggal_dokumen" class="form-control form-control-sm rounded-3" />
            </div>
          </div>

          <div class="mb-2">
            <label class="form-label fw-bold small">Pilih Berkas (File PDF / Gambar) <span class="text-danger">*</span></label>
            <input type="file" name="file_dokumen" class="form-control form-control-sm rounded-3" accept=".pdf,.jpg,.jpeg,.png" required />
            <div class="form-text small text-muted">Format: PDF, JPG, PNG (Maksimal 10 MB).</div>
          </div>
        </div>

        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill fw-bold">
            <i class="bi bi-cloud-arrow-up me-1"></i> Simpan ke Lemari Berkas
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- MODAL PREVIEW DOKUMEN (IN-APP VIEWER) --}}
<div class="modal fade" id="modalPreviewDokumen" tabindex="-1" aria-labelledby="modalPreviewDokumenLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="height:90vh;">
      <div class="modal-header py-2.5 px-4 bg-dark text-white border-0">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
          <h6 class="modal-title fw-bold mb-0 text-white" id="modalPreviewTitle">Pratinjau Dokumen</h6>
        </div>
        <div class="d-flex align-items-center gap-2">
          <a href="#" id="modalPreviewDownloadLink" download="" class="btn btn-sm btn-outline-light rounded-pill px-3" style="font-size:12px;">
            <i class="bi bi-download me-1"></i> Unduh
          </a>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body p-0 bg-secondary-subtle d-flex align-items-center justify-content-center" style="height:100%;">
        <div id="modalPreviewContainer" style="width:100%; height:100%;" class="d-flex align-items-center justify-content-center">
          {{-- Iframe PDF atau Img tag diisi via JS --}}
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  function bukaPreviewDokumen(url, nama, isPdf) {
    document.getElementById('modalPreviewTitle').innerText = nama;
    const downloadLink = document.getElementById('modalPreviewDownloadLink');
    downloadLink.href = url;
    downloadLink.setAttribute('download', nama);

    const container = document.getElementById('modalPreviewContainer');
    container.innerHTML = '';

    if (isPdf) {
      const iframe = document.createElement('iframe');
      iframe.src = url;
      iframe.style.width = '100%';
      iframe.style.height = '100%';
      iframe.style.border = 'none';
      container.appendChild(iframe);
    } else {
      const img = document.createElement('img');
      img.src = url;
      img.alt = nama;
      img.style.maxWidth = '100%';
      img.style.maxHeight = '100%';
      img.style.objectFit = 'contain';
      img.className = 'p-3 shadow-sm rounded';
      container.appendChild(img);
    }

    const modal = new bootstrap.Modal(document.getElementById('modalPreviewDokumen'));
    modal.show();
  }
</script>
@endpush
@endsection
