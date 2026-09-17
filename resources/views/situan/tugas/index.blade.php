@extends('layouts.app')

@section('title', 'Surat Tugas & SPPD — SITUAN SMKN 1 AN')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">

  {{-- Page Header --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
      <div class="d-flex align-items-center gap-2 mb-1">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill" style="font-size:11px; font-weight:700;">
          <i class="bi bi-briefcase-fill me-1"></i> Perjalanan Dinas &amp; Penugasan PTK
        </span>
        <span class="text-muted" style="font-size:12px;">Tahun {{ $thisYear }}</span>
      </div>
      <h1 class="h3 mb-0 fw-bold" style="color:var(--text); letter-spacing:-0.02em;">Surat Tugas &amp; SPPD Terpadu</h1>
      <p class="text-muted mb-0 small">Penerbitan otomatis Surat Perintah Tugas (SPT), SPPD (Lembar I), dan Lembar Visum (Lembar II) dengan auto-fill kepegawaian PTK.</p>
    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('situan.index') }}" class="btn btn-outline-secondary btn-sm" style="font-weight:600;">
        <i class="bi bi-arrow-left me-1"></i> Dasbor SITUAN
      </a>
      <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambahSuratTugas" style="font-weight:700;">
        <i class="bi bi-plus-circle-fill me-1"></i> Buat Surat Tugas &amp; SPPD
      </button>
    </div>
  </div>

  {{-- Top Stat Banner --}}
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:var(--surface); border:1px solid var(--border)!important;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted small fw-bold text-uppercase">Total SPT Diterbitkan ({{ $thisYear }})</div>
            <div class="h3 mb-0 fw-bold text-primary">{{ $totalTugas }}</div>
          </div>
          <div class="rounded-3 p-3 bg-primary-subtle text-primary">
            <i class="bi bi-briefcase fs-4"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:var(--surface); border:1px solid var(--border)!important;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted small fw-bold text-uppercase">Personil Ditugaskan</div>
            <div class="h3 mb-0 fw-bold text-success">{{ $totalPersonil }} <span class="small fw-normal text-muted" style="font-size:13px;">orang</span></div>
          </div>
          <div class="rounded-3 p-3 bg-success-subtle text-success">
            <i class="bi bi-people fs-4"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:var(--surface); border:1px solid var(--border)!important;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted small fw-bold text-uppercase">Lembar SPPD Aktif</div>
            <div class="h3 mb-0 fw-bold text-warning">{{ $totalSppd }}</div>
          </div>
          <div class="rounded-3 p-3 bg-warning-subtle text-warning">
            <i class="bi bi-journal-text fs-4"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Filter & Search --}}
  <div class="card border-0 shadow-sm rounded-3 mb-4" style="background:var(--surface); border:1px solid var(--border)!important;">
    <div class="card-body p-3">
      <form method="GET" action="{{ route('situan.surat-tugas.index') }}" class="row g-2">
        <div class="col-md-7">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-start-0" placeholder="Cari nomor surat tugas, maksud tugas, tujuan, atau nama guru..." />
          </div>
        </div>
        <div class="col-md-2">
          <select name="status" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
            <option value="draf" {{ request('status') === 'draf' ? 'selected' : '' }}>Draf</option>
            <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
          </select>
        </div>
        <div class="col-md-2">
          <select name="tahun" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
            @for($y = now()->year; $y >= 2024; $y--)
              <option value="{{ $y }}" {{ $thisYear == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
            @endfor
          </select>
        </div>
        <div class="col-md-1 d-flex gap-1">
          <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">Filter</button>
          @if(request()->filled('q') || request()->filled('status'))
            <a href="{{ route('situan.surat-tugas.index', ['tahun' => $thisYear]) }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter"><i class="bi bi-x-lg"></i></a>
          @endif
        </div>
      </form>
    </div>
  </div>

  {{-- Tabel Surat Tugas --}}
  <div class="card border-0 shadow-sm rounded-3 overflow-hidden" style="background:var(--surface); border:1px solid var(--border)!important;">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:13px;">
        <thead class="table-light border-bottom" style="font-size:11.5px; text-transform:uppercase; letter-spacing:0.5px;">
          <tr>
            <th class="ps-3" style="width:45px;">No</th>
            <th style="min-width:180px;">Nomor &amp; Tanggal Tugas</th>
            <th style="min-width:240px;">Maksud &amp; Tujuan Dinas</th>
            <th style="min-width:220px;">Personil yang Ditugaskan</th>
            <th style="min-width:140px;">Durasi &amp; Anggaran</th>
            <th class="text-center" style="width:110px;">SPPD</th>
            <th class="text-end pe-3" style="width:180px;">Aksi &amp; Cetak</th>
          </tr>
        </thead>
        <tbody>
          @forelse($suratTugasList as $idx => $st)
            <tr>
              <td class="ps-3 text-muted fw-bold">{{ $suratTugasList->firstItem() + $idx }}</td>
              
              {{-- Nomor & Tanggal --}}
              <td>
                <div class="fw-bold text-primary font-monospace" style="font-size:12.5px;">{{ $st->nomor_surat_tugas }}</div>
                <div class="text-muted small mt-1">
                  <i class="bi bi-calendar-event me-1"></i>
                  {{ $st->tanggal_mulai->format('d/m/Y') }}
                  @if($st->tanggal_mulai->ne($st->tanggal_selesai))
                    s.d. {{ $st->tanggal_selesai->format('d/m/Y') }}
                  @endif
                </div>
                <div class="mt-1">
                  <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size:10px;">
                    Kode: {{ $st->kode_klasifikasi }}
                  </span>
                </div>
              </td>

              {{-- Maksud & Tujuan --}}
              <td>
                <div class="fw-bold text-dark mb-1">{{ $st->maksud_tugas }}</div>
                <div class="small text-secondary">
                  <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                  <span class="fw-semibold">{{ $st->tempat_tujuan }}</span>
                  @if($st->lokasi_spesifik)
                    <span class="text-muted">({{ $st->lokasi_spesifik }})</span>
                  @endif
                </div>
                @if($st->alat_transportasi)
                  <div class="small text-muted mt-1" style="font-size:11px;">
                    <i class="bi bi-car-front me-1"></i> Transpor: {{ $st->alat_transportasi }}
                  </div>
                @endif
              </td>

              {{-- Personil --}}
              <td>
                @php
                  $anggotas = $st->anggotas;
                  $totalOrang = $anggotas->count();
                @endphp
                @if($totalOrang > 0)
                  <div class="fw-bold text-dark">
                    <i class="bi bi-person-badge-fill text-primary me-1"></i>
                    {{ $anggotas[0]->nama }}
                  </div>
                  <div class="text-muted small" style="font-size:11.5px;">
                    NIP: {{ $anggotas[0]->nip ?: '-' }} &bull; {{ $anggotas[0]->jabatan ?: 'Guru' }}
                  </div>
                  @if($totalOrang > 1)
                    <div class="mt-1">
                      <span class="badge bg-secondary-subtle text-secondary" style="font-size:10.5px;">
                        + {{ $totalOrang - 1 }} Personil Lainnya
                      </span>
                    </div>
                  @endif
                @else
                  <span class="text-muted italic">-</span>
                @endif
              </td>

              {{-- Durasi & Anggaran --}}
              <td>
                <div class="fw-semibold text-dark">
                  <i class="bi bi-clock-history me-1 text-muted"></i>
                  {{ $st->lama_hari }} Hari
                </div>
                <div class="text-muted small mt-1" style="font-size:11.5px;">
                  <i class="bi bi-wallet2 me-1"></i>
                  {{ $st->sumber_anggaran ?: 'BOS Reguler' }}
                </div>
              </td>

              {{-- Status SPPD --}}
              <td class="text-center">
                @php $countSppd = $st->sppds->count(); @endphp
                @if($countSppd > 0)
                  <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size:11px; font-weight:700;">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ $countSppd }} SPPD
                  </span>
                @else
                  <span class="badge bg-light text-muted border px-2 py-1" style="font-size:11px;">
                    Tanpa SPPD
                  </span>
                @endif
              </td>

              {{-- Aksi --}}
              <td class="text-end pe-3">
                <div class="btn-group btn-group-sm">
                  {{-- Tombol Cetak Paket Lengkap 3-in-1 --}}
                  <a href="{{ route('situan.surat-tugas.cetak-paket', $st->id) }}" target="_blank" class="btn btn-primary fw-bold" title="Cetak Paket Lengkap 3-in-1 (Surat Tugas + SPPD Lembar I + Visum)">
                    <i class="bi bi-printer-fill me-1"></i> Paket 3-in-1
                  </a>
                  
                  <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">Toggle Dropdown</span>
                  </button>

                  <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><h6 class="dropdown-header text-uppercase" style="font-size:11px;">Opsi Cetak Terpisah</h6></li>
                    <li>
                      <a class="dropdown-item" href="{{ route('situan.surat-tugas.cetak-surat', $st->id) }}" target="_blank">
                        <i class="bi bi-file-earmark-text text-primary me-2"></i> Cetak Surat Tugas (SPT) Saja
                      </a>
                    </li>
                    @if($st->sppds->count() > 0)
                      <li>
                        <a class="dropdown-item" href="{{ route('situan.surat-tugas.cetak-sppd', $st->id) }}" target="_blank">
                          <i class="bi bi-journal-check text-success me-2"></i> Cetak SPPD &amp; Visum (Ketua)
                        </a>
                      </li>
                      @if($st->sppds->count() > 1)
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header text-uppercase" style="font-size:11px;">Cetak SPPD Tiap Anggota</h6></li>
                        @foreach($st->sppds as $sp)
                          <li>
                            <a class="dropdown-item small" href="{{ route('situan.surat-tugas.cetak-sppd', [$st->id, $sp->id]) }}" target="_blank">
                              <i class="bi bi-person me-2 text-muted"></i> SPPD a.n. {{ $sp->nama_pelaksana }}
                            </a>
                          </li>
                        @endforeach
                      @endif
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li>
                      <form action="{{ route('situan.surat-tugas.destroy', $st->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Surat Tugas No. {{ $st->nomor_surat_tugas }} beserta seluruh lembar SPPD dan nomor agendanya?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger">
                          <i class="bi bi-trash3 text-danger me-2"></i> Hapus Surat Tugas
                        </button>
                      </form>
                    </li>
                  </ul>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-5 text-muted">
                <i class="bi bi-briefcase text-secondary opacity-50 display-6 d-block mb-3"></i>
                <div class="fw-bold">Belum Ada Surat Perintah Tugas Terdaftar</div>
                <div class="small">Klik tombol <strong>"Buat Surat Tugas &amp; SPPD"</strong> di atas untuk menerbitkan tugas dinas baru.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($suratTugasList->hasPages())
      <div class="p-3 border-top d-flex justify-content-between align-items-center">
        <div class="text-muted small">
          Menampilkan {{ $suratTugasList->firstItem() }} - {{ $suratTugasList->lastItem() }} dari total {{ $suratTugasList->total() }} surat tugas
        </div>
        <div>
          {{ $suratTugasList->links() }}
        </div>
      </div>
    @endif
  </div>

</div>

{{-- MODAL TAMBAH SURAT TUGAS & SPPD --}}
<div class="modal fade" id="modalTambahSuratTugas" tabindex="-1" aria-labelledby="modalTambahSuratTugasLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      
      <div class="modal-header bg-primary text-white py-3 px-4">
        <div>
          <h5 class="modal-title fw-bold mb-0" id="modalTambahSuratTugasLabel">
            <i class="bi bi-file-earmark-plus-fill me-2"></i> Penerbitan Surat Perintah Tugas (SPT) &amp; SPPD
          </h5>
          <div class="text-white-50 small mt-1">Otomatis mengunci nomor agenda resmi &amp; mengisi data kepegawaian PTK.</div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="{{ route('situan.surat-tugas.store') }}" method="POST">
        @csrf
        
        <div class="modal-body p-4">
          
          {{-- Section 1: Informasi Pokok Penugasan --}}
          <div class="mb-4">
            <div class="d-flex align-items-center gap-2 pb-2 border-bottom mb-3">
              <span class="badge bg-primary text-white rounded-circle p-2" style="width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center; font-size:11px;">1</span>
              <h6 class="fw-bold text-dark mb-0">Informasi Pokok Penugasan &amp; Perjalanan Dinas</h6>
            </div>

            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label fw-bold small">Maksud Tugas / Perihal Penugasan <span class="text-danger">*</span></label>
                <textarea name="maksud_tugas" class="form-control" rows="2" placeholder="Contoh: Mengikuti Rapat Koordinasi MKKS SMK dan Sosialisasi BOSP Tahun 2026..." required></textarea>
                <div class="form-text" style="font-size:11px;">Uraikan tujuan kedinasan secara jelas dan lugas.</div>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold small">Dasar Hukum / Surat Rujukan</label>
                <textarea name="dasar_penugasan" class="form-control" rows="2" placeholder="Contoh: Surat Undangan Disdikbud Provinsi Lampung Nomor: 420/123/V.01/DP.1/2026..."></textarea>
                <div class="form-text" style="font-size:11px;">Surat undangan atau program kerja sekolah (opsional).</div>
              </div>

              <div class="col-md-3">
                <label class="form-label fw-bold small">Tempat Berangkat <span class="text-danger">*</span></label>
                <input type="text" name="tempat_berangkat" class="form-control" value="Air Naningan, Tanggamus" required>
              </div>

              <div class="col-md-5">
                <label class="form-label fw-bold small">Kota / Instansi Tujuan <span class="text-danger">*</span></label>
                <input type="text" name="tempat_tujuan" class="form-control" placeholder="Contoh: Bandar Lampung" required>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold small">Lokasi Spesifik / Tempat Pelaksanaan</label>
                <input type="text" name="lokasi_spesifik" class="form-control" placeholder="Contoh: Hotel Horison / Aula Disdikbud">
              </div>

              <div class="col-md-3">
                <label class="form-label fw-bold small">Tanggal Mulai Tugas <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_mulai" id="inputTglMulai" class="form-control" value="{{ date('Y-m-d') }}" required onchange="hitungLamaHari()">
              </div>

              <div class="col-md-3">
                <label class="form-label fw-bold small">Tanggal Selesai Tugas <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_selesai" id="inputTglSelesai" class="form-control" value="{{ date('Y-m-d') }}" required onchange="hitungLamaHari()">
              </div>

              <div class="col-md-2">
                <label class="form-label fw-bold small">Durasi (Hari)</label>
                <input type="text" id="displayLamaHari" class="form-control bg-light fw-bold text-center" value="1 Hari" readonly>
              </div>

              <div class="col-md-2">
                <label class="form-label fw-bold small">Alat Transportasi</label>
                <select name="alat_transportasi" class="form-select">
                  <option value="Kendaraan Pribadi">Kendaraan Pribadi</option>
                  <option value="Kendaraan Dinas">Kendaraan Dinas</option>
                  <option value="Angkutan Umum / Bus">Angkutan Umum / Bus</option>
                  <option value="Kereta Api / Pesawat">Kereta Api / Pesawat</option>
                </select>
              </div>

              <div class="col-md-2">
                <label class="form-label fw-bold small">Sumber Anggaran</label>
                <select name="sumber_anggaran" class="form-select">
                  <option value="BOS Reguler SMK Negeri 1 Air Naningan">BOS Reguler</option>
                  <option value="BOPD Provinsi Lampung">BOPD Provinsi</option>
                  <option value="Panitia Penyelenggara">Panitia Kegiatan</option>
                  <option value="Mandiri">Mandiri</option>
                </select>
              </div>
            </div>
          </div>

          {{-- Section 2: Personil yang Ditugaskan (Auto-Fill Guru) --}}
          <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between pb-2 border-bottom mb-3">
              <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary text-white rounded-circle p-2" style="width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center; font-size:11px;">2</span>
                <h6 class="fw-bold text-dark mb-0">Personil yang Ditugaskan (Ketua &amp; Anggota Tim)</h6>
              </div>
              <button type="button" class="btn btn-outline-primary btn-sm fw-bold" onclick="tambahBarisPersonil()">
                <i class="bi bi-person-plus-fill me-1"></i> Tambah Anggota Rombongan
              </button>
            </div>

            <div class="table-responsive">
              <table class="table table-bordered align-middle" id="tabelPersonil" style="font-size:12.5px;">
                <thead class="table-light">
                  <tr>
                    <th style="width:30px;">#</th>
                    <th style="min-width:240px;">Pilih Guru / PTK (Auto-Fill)</th>
                    <th style="min-width:180px;">Nama Lengkap &amp; Gelar</th>
                    <th style="min-width:160px;">NIP</th>
                    <th style="min-width:150px;">Pangkat / Gol.</th>
                    <th style="min-width:160px;">Jabatan</th>
                    <th style="min-width:120px;">Peran</th>
                    <th style="width:40px;"></th>
                  </tr>
                </thead>
                <tbody id="personilBody">
                  {{-- Baris Pertama: Default Ketua Rombongan --}}
                  <tr class="personil-row" data-index="0">
                    <td class="text-center fw-bold text-muted row-num">1</td>
                    <td>
                      <select class="form-select form-select-sm select-guru" onchange="autoFillGuru(this, 0)">
                        <option value="">-- Pilih dari Database PTK --</option>
                        @foreach($gurus as $g)
                          <option value="{{ $g['id'] }}" 
                                  data-nama="{{ $g['nama'] }}" 
                                  data-nip="{{ $g['nip'] }}" 
                                  data-pangkat="{{ $g['pangkat_golongan'] }}" 
                                  data-jabatan="{{ $g['jabatan'] }}">
                            {{ $g['nama'] }} ({{ $g['nip'] }})
                          </option>
                        @endforeach
                      </select>
                      <input type="hidden" name="anggotas[0][guru_id]" class="input-guru-id">
                    </td>
                    <td>
                      <input type="text" name="anggotas[0][nama]" class="form-control form-control-sm input-nama" required placeholder="Nama Lengkap">
                    </td>
                    <td>
                      <input type="text" name="anggotas[0][nip]" class="form-control form-control-sm input-nip" placeholder="NIP jika ada">
                    </td>
                    <td>
                      <input type="text" name="anggotas[0][pangkat_golongan]" class="form-control form-control-sm input-pangkat" placeholder="cth: Penata / III-c">
                    </td>
                    <td>
                      <input type="text" name="anggotas[0][jabatan]" class="form-control form-control-sm input-jabatan" placeholder="cth: Guru Kejuruan">
                    </td>
                    <td>
                      <select name="anggotas[0][peran]" class="form-select form-select-sm">
                        <option value="Ketua Rombongan" selected>Ketua Rombongan</option>
                        <option value="Anggota">Anggota</option>
                        <option value="Pendamping">Pendamping</option>
                      </select>
                    </td>
                    <td class="text-center">
                      <button type="button" class="btn btn-outline-danger btn-sm p-1" onclick="hapusBarisPersonil(this)" title="Hapus baris" disabled>
                        <i class="bi bi-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="form-text" style="font-size:11.5px;">
              <i class="bi bi-info-circle text-primary me-1"></i> Pilih guru pada kolom *dropdown* untuk langsung mengisi otomatis Nama, NIP, Pangkat, dan Jabatan.
            </div>
          </div>

          {{-- Section 3: Pengaturan Lembar SPPD & Klasifikasi --}}
          <div>
            <div class="d-flex align-items-center gap-2 pb-2 border-bottom mb-3">
              <span class="badge bg-primary text-white rounded-circle p-2" style="width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center; font-size:11px;">3</span>
              <h6 class="fw-bold text-dark mb-0">Pengaturan Lembar SPPD (Surat Perintah Perjalanan Dinas)</h6>
            </div>

            <div class="p-3 bg-light rounded-3 border">
              <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" role="switch" name="terbitkan_sppd" value="1" id="switchSppd" checked onchange="toggleSppdSettings(this.checked)">
                <label class="form-check-label fw-bold text-dark" for="switchSppd">
                  Terbitkan Lembar SPPD &amp; Lembar Visum Otomatis untuk Seluruh Personil
                </label>
                <div class="text-muted small">Sistem akan membuat nomor SPPD resmi dan lembar visum kehadiran di tempat tujuan secara otomatis.</div>
              </div>

              <div class="row g-3" id="sppdOptions">
                <div class="col-md-4">
                  <label class="form-label small fw-bold">Tingkat Biaya Perjalanan Dinas</label>
                  <select name="tingkat_biaya" class="form-select form-select-sm">
                    <option value="Tingkat C">Tingkat C (Pejabat Eselon IV / Guru / Golongan III &amp; IV)</option>
                    <option value="Tingkat D">Tingkat D (Pegawai Golongan I &amp; II / Non-ASN)</option>
                    <option value="Tingkat B">Tingkat B (Kepala Sekolah / Pejabat Eselon III)</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label small fw-bold">Mata Anggaran / Kode Rekening</label>
                  <input type="text" name="mata_anggaran" class="form-control form-control-sm" value="5.1.02.04.01.0001 (Belanja Perjalanan Dinas Biasa)" placeholder="Kode Rekening">
                </div>
                <div class="col-md-4">
                  <label class="form-label small fw-bold">Klasifikasi Surat</label>
                  <select name="kode_klasifikasi" class="form-select form-select-sm">
                    <option value="094" selected>094 (Perjalanan Dinas / Tugas)</option>
                    <option value="424">424 (Tenaga Pengajar / Teknis Vokasi)</option>
                    <option value="421.3">421.3 (Kegiatan Persekolahan)</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="modal-footer bg-light py-3 px-4 d-flex justify-content-between">
          <button type="button" class="btn btn-secondary btn-sm fw-semibold" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4 fw-bold">
            <i class="bi bi-check-circle-fill me-1"></i> Terbitkan Surat Tugas &amp; SPPD
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  const masterGurus = @json($gurus);
  let personilCount = 1;

  function hitungLamaHari() {
    const tglMulai = document.getElementById('inputTglMulai').value;
    const tglSelesai = document.getElementById('inputTglSelesai').value;
    if (tglMulai && tglSelesai) {
      const d1 = new Date(tglMulai);
      const d2 = new Date(tglSelesai);
      const diffTime = d2 - d1;
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
      if (diffDays > 0) {
        document.getElementById('displayLamaHari').value = diffDays + ' Hari';
      } else {
        document.getElementById('displayLamaHari').value = '1 Hari';
      }
    }
  }

  function autoFillGuru(selectElem, index) {
    const selectedId = selectElem.value;
    const row = selectElem.closest('.personil-row');
    if (!selectedId) return;

    const opt = selectElem.options[selectElem.selectedIndex];
    row.querySelector('.input-guru-id').value = selectedId;
    row.querySelector('.input-nama').value = opt.getAttribute('data-nama') || '';
    row.querySelector('.input-nip').value = opt.getAttribute('data-nip') || '-';
    row.querySelector('.input-pangkat').value = opt.getAttribute('data-pangkat') || '-';
    row.querySelector('.input-jabatan').value = opt.getAttribute('data-jabatan') || 'Guru';
  }

  function tambahBarisPersonil() {
    const tbody = document.getElementById('personilBody');
    const newIdx = personilCount++;

    let guruOptions = '<option value="">-- Pilih dari Database PTK --</option>';
    masterGurus.forEach(g => {
      guruOptions += `<option value="${g.id}" data-nama="${g.nama}" data-nip="${g.nip}" data-pangkat="${g.pangkat_golongan}" data-jabatan="${g.jabatan}">${g.nama} (${g.nip})</option>`;
    });

    const tr = document.createElement('tr');
    tr.className = 'personil-row';
    tr.setAttribute('data-index', newIdx);
    tr.innerHTML = `
      <td class="text-center fw-bold text-muted row-num">${newIdx + 1}</td>
      <td>
        <select class="form-select form-select-sm select-guru" onchange="autoFillGuru(this, ${newIdx})">
          ${guruOptions}
        </select>
        <input type="hidden" name="anggotas[${newIdx}][guru_id]" class="input-guru-id">
      </td>
      <td>
        <input type="text" name="anggotas[${newIdx}][nama]" class="form-control form-control-sm input-nama" required placeholder="Nama Lengkap">
      </td>
      <td>
        <input type="text" name="anggotas[${newIdx}][nip]" class="form-control form-control-sm input-nip" placeholder="NIP jika ada">
      </td>
      <td>
        <input type="text" name="anggotas[${newIdx}][pangkat_golongan]" class="form-control form-control-sm input-pangkat" placeholder="cth: Penata / III-c">
      </td>
      <td>
        <input type="text" name="anggotas[${newIdx}][jabatan]" class="form-control form-control-sm input-jabatan" placeholder="cth: Guru Kejuruan">
      </td>
      <td>
        <select name="anggotas[${newIdx}][peran]" class="form-select form-select-sm">
          <option value="Anggota" selected>Anggota</option>
          <option value="Ketua Rombongan">Ketua Rombongan</option>
          <option value="Pendamping">Pendamping</option>
        </select>
      </td>
      <td class="text-center">
        <button type="button" class="btn btn-outline-danger btn-sm p-1" onclick="hapusBarisPersonil(this)" title="Hapus baris">
          <i class="bi bi-trash"></i>
        </button>
      </td>
    `;
    tbody.appendChild(tr);
    updateNomorBaris();
  }

  function hapusBarisPersonil(btn) {
    const row = btn.closest('.personil-row');
    const tbody = document.getElementById('personilBody');
    if (tbody.querySelectorAll('.personil-row').length > 1) {
      row.remove();
      updateNomorBaris();
    }
  }

  function updateNomorBaris() {
    const rows = document.querySelectorAll('#personilBody .personil-row');
    rows.forEach((r, idx) => {
      r.querySelector('.row-num').textContent = idx + 1;
      const deleteBtn = r.querySelector('button.btn-outline-danger');
      if (rows.length === 1) {
        deleteBtn.disabled = true;
      } else {
        deleteBtn.disabled = false;
      }
    });
  }

  function toggleSppdSettings(checked) {
    const el = document.getElementById('sppdOptions');
    if (checked) {
      el.style.display = 'flex';
    } else {
      el.style.display = 'none';
    }
  }
</script>
@endpush
@endsection
