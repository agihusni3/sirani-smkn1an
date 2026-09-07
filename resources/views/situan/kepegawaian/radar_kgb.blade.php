@extends('layouts.app')

@section('title', 'Radar KGB & Pangkat PTK — SITUAN SMKN 1 AN')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">

  {{-- Page Header --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
      <div class="d-flex align-items-center gap-2 mb-1">
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill" style="font-size:11px; font-weight:700;">
          <i class="bi bi-broadcast-pin me-1"></i> E-Kepegawaian &amp; Peringatan Dini
        </span>
        <span class="text-muted" style="font-size:12px;">Siklus 2 Tahun KGB &amp; 4 Tahun Pangkat</span>
      </div>
      <h1 class="h3 mb-0 fw-bold" style="color:var(--text); letter-spacing:-0.02em;">Radar KGB &amp; Pangkat PTK</h1>
      <p class="text-muted mb-0 small">Sistem deteksi dini otomatis pengusulan Kenaikan Gaji Berkala (KGB) dan Kenaikan Pangkat Guru &amp; Tenaga Kependidikan ke Disdik Provinsi Lampung.</p>
    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('situan.index') }}" class="btn btn-outline-secondary btn-sm" style="font-weight:600;">
        <i class="bi bi-arrow-left me-1"></i> Dasbor SITUAN
      </a>
      <a href="{{ route('guru.index') }}" class="btn btn-primary btn-sm" style="font-weight:700;">
        <i class="bi bi-person-lines-fill me-1"></i> Master PTK
      </a>
    </div>
  </div>

  {{-- Radar Stat Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:#fff1f2; border:1px solid #fecdd3!important;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-danger small fw-bold text-uppercase"><i class="bi bi-exclamation-octagon-fill me-1"></i> Jatuh Tempo / Lewat</div>
            <div class="h2 mb-0 fw-bold text-danger">{{ $countJatuhTempo }} <span class="fs-6 fw-normal text-muted">PTK</span></div>
            <small class="text-danger-emphasis" style="font-size:11px;">Harus segera diterbitkan surat pengantar</small>
          </div>
          <div class="rounded-3 p-3 bg-danger text-white">
            <i class="bi bi-bell-fill fs-3"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:#fffbeb; border:1px solid #fde68a!important;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-warning-emphasis small fw-bold text-uppercase"><i class="bi bi-hourglass-split me-1"></i> Mendekati (Dalam 60 Hari)</div>
            <div class="h2 mb-0 fw-bold text-warning-emphasis">{{ $countSegera }} <span class="fs-6 fw-normal text-muted">PTK</span></div>
            <small class="text-warning-emphasis" style="font-size:11px;">Mulai kumpulkan berkas SK terakhir</small>
          </div>
          <div class="rounded-3 p-3 bg-warning text-dark">
            <i class="bi bi-clock-history fs-3"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:#f0fdf4; border:1px solid #bbf7d0!important;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-success small fw-bold text-uppercase"><i class="bi bi-shield-check me-1"></i> Status Aman (> 60 Hari)</div>
            <div class="h2 mb-0 fw-bold text-success">{{ $countAman }} <span class="fs-6 fw-normal text-muted">PTK</span></div>
            <small class="text-success-emphasis" style="font-size:11px;">Masa kerja berkala masih aktif</small>
          </div>
          <div class="rounded-3 p-3 bg-success text-white">
            <i class="bi bi-check-circle-fill fs-3"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Radar PTK List --}}
  <div class="card border-0 shadow-sm rounded-3" style="background:var(--surface); border:1px solid var(--border)!important;">
    <div class="card-header bg-transparent border-0 pt-3 px-3 d-flex justify-content-between align-items-center">
      <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-radar me-1 text-primary"></i> Daftar Pemantauan Berkala Tenaga Pendidik &amp; Kependidikan</h6>
      <span class="badge bg-secondary-subtle text-secondary">{{ count($radarKgb) }} Pegawai Aktif</span>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
        <thead class="table-light" style="border-bottom:1.5px solid var(--border);">
          <tr>
            <th class="py-3 px-3" style="width:230px;">Nama PTK &amp; NIP/NUPTK</th>
            <th class="py-3 px-3" style="width:130px;">Status / Golongan</th>
            <th class="py-3 px-3" style="width:150px;">TMT KGB Terakhir</th>
            <th class="py-3 px-3" style="width:170px;">Jatuh Tempo KGB</th>
            <th class="py-3 px-3" style="width:150px;">Status Radar</th>
            <th class="py-3 px-3 text-center" style="width:200px;">Aksi Kepegawaian</th>
          </tr>
        </thead>
        <tbody>
          @forelse($radarKgb as $row)
            @php
              $guru = $row['guru'];
              $status = $row['statusKgb'];
              $rowBg = match($status) {
                'jatuh_tempo' => 'background-color: #fff5f5;',
                'segera'      => 'background-color: #fffdf5;',
                default       => '',
              };
            @endphp
            <tr style="{{ $rowBg }}">
              <td class="px-3">
                <div class="fw-bold text-dark">{{ $guru->nama }}</div>
                <div class="text-secondary" style="font-size:11.5px;">
                  NIP: {{ $guru->nip ?: '-' }} &bull; {{ $guru->jenis_ptk ?: 'Guru' }}
                </div>
              </td>
              <td class="px-3">
                <span class="badge bg-light text-dark border">{{ $guru->status_kepegawaian ?: 'PNS/PPPK/Honorer' }}</span>
                @if($guru->golongan_ruang)
                  <div class="fw-bold text-primary small mt-1">Gol: {{ $guru->golongan_ruang }}</div>
                @else
                  <div class="text-muted small mt-1">-</div>
                @endif
              </td>
              <td class="px-3">
                @if($row['tmtKgb'])
                  <div class="fw-semibold text-dark">{{ $row['tmtKgb']->translatedFormat('d M Y') }}</div>
                  <small class="text-muted">TMT Pangkat: {{ $row['tmtPangkat'] ? $row['tmtPangkat']->translatedFormat('d M Y') : '-' }}</small>
                @else
                  <span class="text-danger small"><i class="bi bi-exclamation-circle me-1"></i>Belum diset</span>
                @endif
              </td>
              <td class="px-3">
                @if($row['nextKgb'])
                  <div class="fw-bold {{ $status === 'jatuh_tempo' ? 'text-danger' : ($status === 'segera' ? 'text-warning-emphasis' : 'text-success') }}">
                    {{ $row['nextKgb']->translatedFormat('d F Y') }}
                  </div>
                  @if($row['daysToKgb'] < 0)
                    <span class="badge bg-danger text-white" style="font-size:10.5px;">Lewat {{ abs($row['daysToKgb']) }} hari</span>
                  @elseif($row['daysToKgb'] == 0)
                    <span class="badge bg-danger text-white" style="font-size:10.5px;">Jatuh Tempo Hari Ini</span>
                  @else
                    <span class="badge bg-secondary-subtle text-secondary" style="font-size:10.5px;">Sisa {{ $row['daysToKgb'] }} hari</span>
                  @endif
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td class="px-3">
                @if($status === 'jatuh_tempo')
                  <span class="badge bg-danger px-2 py-1 rounded-pill" style="font-size:11px;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Pengusulan KGB
                  </span>
                @elseif($status === 'segera')
                  <span class="badge bg-warning text-dark px-2 py-1 rounded-pill" style="font-size:11px;">
                    <i class="bi bi-clock-fill me-1"></i> Siapkan Berkas
                  </span>
                @elseif($status === 'aman')
                  <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill" style="font-size:11px;">
                    <i class="bi bi-check-circle me-1"></i> Aman
                  </span>
                @else
                  <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill" style="font-size:11px;">
                    Data TMT Kosong
                  </span>
                @endif
              </td>
              <td class="text-center px-3">
                <div class="btn-group btn-group-sm">
                  {{-- Update TMT Modal Trigger --}}
                  <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditTmt{{ $guru->id }}" title="Edit TMT KGB / Pangkat">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  {{-- E-Arsip PTK --}}
                  <a href="{{ route('situan.arsip-ptk.index', $guru->id) }}" class="btn btn-outline-primary" title="Lemari Berkas Digital PTK">
                    <i class="bi bi-folder2-open me-1"></i> Arsip
                  </a>
                  {{-- Cetak Surat Pengantar KGB Disdik --}}
                  <a href="{{ route('situan.radar-kgb.cetak-pengantar', $guru->id) }}" target="_blank" class="btn btn-primary" title="Cetak Draf Pengantar Usulan KGB ke Disdik">
                    <i class="bi bi-printer-fill me-1"></i> Pengantar
                  </a>
                </div>

                {{-- Modal Quick Edit TMT --}}
                <div class="modal fade text-start" id="modalEditTmt{{ $guru->id }}" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                      <form action="{{ route('situan.radar-kgb.update', $guru->id) }}" method="POST">
                        @csrf
                        <div class="modal-header border-bottom py-3 px-4">
                          <h6 class="modal-title fw-bold">Update Data KGB &amp; Pangkat — {{ $guru->nama }}</h6>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                          <div class="mb-3">
                            <label class="form-label small fw-bold">Golongan / Ruang</label>
                            <input type="text" name="golongan_ruang" value="{{ $guru->golongan_ruang }}" class="form-control form-control-sm" placeholder="Contoh: III/a, III/b, IX (PPPK)" />
                          </div>
                          <div class="row g-2 mb-3">
                            <div class="col-md-6">
                              <label class="form-label small fw-bold">TMT KGB Terakhir</label>
                              <input type="date" name="tmt_kgb_terakhir" value="{{ $guru->tmt_kgb_terakhir }}" class="form-control form-control-sm" />
                              <div class="form-text" style="font-size:10.5px;">KGB jatuh tempo 2 thn kemudian.</div>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label small fw-bold">TMT Pangkat Terakhir</label>
                              <input type="date" name="tmt_pangkat_terakhir" value="{{ $guru->tmt_pangkat_terakhir }}" class="form-control form-control-sm" />
                              <div class="form-text" style="font-size:10.5px;">Kenaikan pangkat 4 thn sekali.</div>
                            </div>
                          </div>
                          <div class="row g-2">
                            <div class="col-md-6">
                              <label class="form-label small fw-bold">Pendidikan Terakhir</label>
                              <input type="text" name="pendidikan_terakhir" value="{{ $guru->pendidikan_terakhir }}" class="form-control form-control-sm" placeholder="Contoh: S1 / S2" />
                            </div>
                            <div class="col-md-6">
                              <label class="form-label small fw-bold">Jurusan</label>
                              <input type="text" name="jurusan_pendidikan" value="{{ $guru->jurusan_pendidikan }}" class="form-control form-control-sm" placeholder="Contoh: Pendidikan Matematika" />
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer border-top px-4 py-2">
                          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                          <button type="submit" class="btn btn-sm btn-primary fw-bold">Simpan Perubahan</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="bi bi-people fs-1 d-block mb-2 text-secondary opacity-50"></i>
                <p class="mb-0 fw-semibold">Belum ada data guru / PTK aktif.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
