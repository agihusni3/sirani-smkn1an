<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Jam Sekolah &amp; Sesi Operasional — SIRANI SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/jadwal-sekolah.css') }}?v={{ filemtime(public_path('css/jadwal-sekolah.css')) }}">
</head>
<body>

<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    @php
      $currentTime = now()->format('H:i');
      $jamMasuk = substr($jadwalHariIni->jam_masuk_toleransi ?? '07:15', 0, 5);
      $jamPulang = substr($jadwalHariIni->jam_pulang_mulai ?? '15:30', 0, 5);
      $jamTutup = substr($jadwalHariIni->jam_tutup_gerbang ?? '17:00', 0, 5);

      // Determine current active session phase
      $currentPhase = 'tutup';
      if ($currentTime >= '06:00' && $currentTime < $jamMasuk) {
        $currentPhase = 'masuk';
      } elseif ($currentTime >= $jamMasuk && $currentTime < $jamPulang) {
        $currentPhase = 'kbm';
      } elseif ($currentTime >= $jamPulang && $currentTime <= $jamTutup) {
        $currentPhase = 'pulang';
      }
    @endphp

    {{-- ULTRA COMPACT SLIM HEADER BAR --}}
    <div class="panel no-print" style="background:var(--bg-2); border:1px solid var(--border); padding:10px 16px; margin-bottom:14px; border-radius:var(--r-md); box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <h1 style="margin:0; font-size:16px; font-weight:900; color:var(--text); display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-clock-history" style="color:#000000; font-size:16px;"></i> Jam Operasional Sekolah
          </h1>
        </div>

        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          {{-- Tombol Mode Sumatif --}}
          @if(auth()->user()->isAdmin() || auth()->user()->isWakaKurikulum() || auth()->user()->isWakaKesiswaan() || auth()->user()->isKepalaSekolah())
            <button type="button" class="btn btn-sm" onclick="openModal('modalKelolaModeUjian')" style="background:linear-gradient(135deg, #4f46e5, #7c3aed); color:#fff; border:none; padding:7px 13px; border-radius:8px; font-weight:800; font-size:12px; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 6px rgba(79, 70, 229, 0.25);" title="Konfigurasi Mode Pekan Sumatif (STS / SAS) & Panitia">
              <i class="bi bi-mortarboard-fill"></i> Mode Sumatif
            </button>
          @endif

          {{-- Tombol Liburkan Hari Ini / Batal Libur --}}
          @if(!$isLibur && (auth()->user()->isAdmin() || auth()->user()->isPiketHariIni() || auth()->user()->isKepalaSekolah() || auth()->user()->isWakaKurikulum() || auth()->user()->isWakaKesiswaan()))
            <button type="button" class="btn btn-sm" onclick="openModal('modalLiburDarurat')" style="background:linear-gradient(135deg, #ef4444, #dc2626); color:#fff; border:none; padding:7px 13px; border-radius:8px; font-weight:800; font-size:12px; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 6px rgba(239, 68, 68, 0.25);" title="Liburkan sekolah mendadak hari ini & bersihkan status Alpha">
              <i class="bi bi-calendar-x-fill"></i> Liburkan Hari Ini
            </button>
          @elseif($isLibur && $liburDetail?->is_libur_darurat && (auth()->user()->isAdmin() || auth()->user()->isKepalaSekolah()))
            <form method="POST" action="{{ route('piket.libur-darurat.batal', $liburDetail->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan status libur darurat hari ini?');" style="margin:0; display:inline;">
              @csrf
              <button type="submit" class="btn btn-sm" style="background:#059669; color:#fff; border:none; padding:7px 13px; border-radius:8px; font-weight:800; font-size:12px; cursor:pointer; display:inline-flex; align-items:center; gap:6px;" title="Batalkan status libur darurat & aktifkan kembali presensi hari ini">
                <i class="bi bi-arrow-counterclockwise"></i> Batal Libur Hari Ini
              </button>
            </form>
          @endif

          <span style="background:var(--bg-3); border:1px solid var(--border-2); color:#000000; font-family:var(--font-mono); font-size:12px; font-weight:800; padding:4px 10px; border-radius:6px; display:inline-flex; align-items:center; gap:5px;">
            <i class="bi bi-broadcast" style="color:#000000; font-size:11px;"></i>
            <span id="liveClockDisplay">{{ now()->format('H:i:s') }}</span> WIB
          </span>

          @include('partials.header_actions')
        </div>
      </div>
    </div>

    {{-- BANNER NOTIFIKASI MODE SUMATIF AKTIF JIKA ADA --}}
    @if($modeUjian && $modeUjian->isAktifHariIni($today))
      <div style="margin-bottom:14px; display:flex; align-items:center; justify-content:space-between; gap:14px; padding:12px 16px; border-radius:12px; background:linear-gradient(135deg, rgba(79, 70, 229, 0.10) 0%, rgba(147, 51, 234, 0.08) 100%); border:1.5px solid rgba(99, 102, 241, 0.35);">
        <div style="display:flex; align-items:center; gap:10px;">
          <div style="width:36px; height:36px; border-radius:8px; background:linear-gradient(135deg, #4f46e5, #7c3aed); display:flex; align-items:center; justify-content:center; font-size:18px; color:#fff; flex-shrink:0;">
            <i class="bi bi-mortarboard-fill"></i>
          </div>
          <div>
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
              <strong style="font-size:13.5px; color:#3730a3;">MODE PEKAN SUMATIF AKTIF: {{ $modeUjian->nama_ujian }}</strong>
              <span style="font-size:10.5px; padding:1px 6px; border-radius:4px; background:#4f46e5; color:#fff; font-weight:700;">{{ $modeUjian->tipe }}</span>
            </div>
            <div style="font-size:12px; color:#4b5563; margin-top:1px;">
              Jam Pulang Gerbang Sumatif: <strong style="color:#4338ca;">{{ substr($modeUjian->jam_pulang_mulai, 0, 5) }} WIB</strong> • Berlangsung {{ \Carbon\Carbon::parse($modeUjian->tanggal_mulai)->locale('id')->isoFormat('D MMM') }} s.d. {{ \Carbon\Carbon::parse($modeUjian->tanggal_selesai)->locale('id')->isoFormat('D MMM Y') }}
            </div>
          </div>
        </div>
        <button type="button" class="btn btn-sm" onclick="openModal('modalKelolaModeUjian')" style="background:#4f46e5; color:#fff; border:none; padding:6px 12px; border-radius:6px; font-weight:700; font-size:11.5px; cursor:pointer; flex-shrink:0;">
          <i class="bi bi-gear-fill"></i> Kelola
        </button>
      </div>
    @endif

    {{-- BANNER NOTIFIKASI HARI LIBUR JIKA ADA --}}
    @if($isLibur)
      <div style="margin-bottom:14px; display:flex; align-items:center; justify-content:space-between; gap:14px; padding:12px 16px; border-radius:12px; background:rgba(239, 68, 68, 0.08); border:1px solid rgba(239, 68, 68, 0.22); color:#991b1b;">
        <div style="display:flex; align-items:center; gap:10px;">
          <div style="width:36px; height:36px; border-radius:8px; background:rgba(239, 68, 68, 0.15); display:flex; align-items:center; justify-content:center; font-size:18px; color:#dc2626; flex-shrink:0;">
            <i class="bi bi-calendar-x-fill"></i>
          </div>
          <div>
            <strong style="font-size:13.5px; color:#b91c1c; display:block;">Hari Ini Libur Sekolah: {{ $liburDetail->nama_libur ?? 'Libur Terjadwal' }}</strong>
            <span style="font-size:12px; color:#7f1d1d;">{{ $liburDetail->keterangan ?? 'Seluruh peserta didik dan dewan guru bebas kewajiban presensi hari ini. Status Alpha otomatis dinonaktifkan.' }}</span>
          </div>
        </div>
      </div>
    @endif

    @if(session('success'))
      <div class="alert-success" style="margin-bottom:12px;">
        <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="alert-error" style="margin-bottom:12px;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}
      </div>
    @endif

    @php
      $currentUser = auth()->user();
      $canManageJadwal = $currentUser && ($currentUser->isAdmin() || $currentUser->isWakaKurikulum() || $currentUser->isWakaKesiswaan() || $currentUser->isKepalaSekolah() || $currentUser->isStafTu());
    @endphp

    {{-- ══ JAM OPERASIONAL MINGGUAN (SENIN - JUMAT) ══ --}}
    <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:20px;">

      <div style="padding:14px 18px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="font-size:14px; font-weight:900; color:var(--text); display:flex; align-items:center; gap:8px;">
          <i class="bi bi-calendar-week-fill" style="color:#000000; font-size:15px;"></i>
          <span>Pengaturan Jadwal Masuk &amp; Pulang (Senin – Jumat)</span>
        </div>
        <div style="font-size:12px; color:var(--text-3); font-weight:600; display:flex; align-items:center; gap:6px;">
          <i class="bi bi-arrow-repeat" style="color:#16A34A;"></i>
          <span>Otomatis berulang setiap pekan</span>
        </div>
      </div>

      <form action="{{ route('admin.jadwal.mingguan.update') }}" method="POST" style="margin:0;">
        @csrf
        <div class="table-responsive" style="overflow-x:auto;">
          <table class="data-table" style="width:100%; border-collapse:collapse;">
            <thead>
              <tr style="background:var(--bg-3);">
                <th style="padding:11px 16px; color:#000000; font-weight:800; width:140px;">Hari</th>
                <th style="padding:11px 16px; color:#000000; font-weight:800; text-align:center; width:160px;">Batas Masuk</th>
                <th style="padding:11px 16px; color:#000000; font-weight:800; text-align:center; width:160px;">Mulai Pulang</th>
                <th style="padding:11px 16px; color:#000000; font-weight:800; text-align:center; width:160px;">Tutup Gerbang</th>
                <th style="padding:11px 16px; color:#000000; font-weight:800;">Keterangan</th>

              </tr>
            </thead>
            <tbody>
              @foreach($jadwalMingguanList as $jm)
                @php
                  $isTodayDay = strtolower($jm->hari) === strtolower(\Carbon\Carbon::parse($today)->locale('id')->isoFormat('dddd'));
                @endphp
                <tr style="border-bottom:1px solid var(--border); background:{{ $isTodayDay ? 'rgba(0,0,0,0.02)' : 'transparent' }};">
                  <td style="padding:12px 16px; vertical-align:middle;">
                    <div style="display:flex; align-items:center; gap:8px;">
                      <span style="font-weight:900; font-size:13.5px; color:#000000;">{{ $jm->hari }}</span>
                      @if($isTodayDay)
                        <span style="background:#000000; color:#FFFFFF; font-size:9.5px; font-weight:800; padding:2px 6px; border-radius:4px;">HARI INI</span>
                      @endif
                    </div>
                    <input type="hidden" name="mingguan[{{ $jm->hari }}][is_aktif]" value="1" />
                  </td>
                  <td style="padding:9px 16px; text-align:center; vertical-align:middle;">
                    <div class="time-input-wrap" style="max-width:130px; margin:0 auto;">
                      <input type="time" name="mingguan[{{ $jm->hari }}][jam_masuk_toleransi]" value="{{ substr($jm->jam_masuk_toleransi, 0, 5) }}" required {{ !$canManageJadwal ? 'disabled' : '' }} style="text-align:center; font-weight:800; font-family:var(--font-mono); font-size:13px;" />
                    </div>
                  </td>
                  <td style="padding:9px 16px; text-align:center; vertical-align:middle;">
                    <div class="time-input-wrap" style="max-width:130px; margin:0 auto;">
                      <input type="time" name="mingguan[{{ $jm->hari }}][jam_pulang_mulai]" value="{{ substr($jm->jam_pulang_mulai, 0, 5) }}" required {{ !$canManageJadwal ? 'disabled' : '' }} style="text-align:center; font-weight:800; font-family:var(--font-mono); font-size:13px;" />
                    </div>
                  </td>
                  <td style="padding:9px 16px; text-align:center; vertical-align:middle;">
                    <div class="time-input-wrap" style="max-width:130px; margin:0 auto;">
                      <input type="time" name="mingguan[{{ $jm->hari }}][jam_tutup_gerbang]" value="{{ substr($jm->jam_tutup_gerbang ?? '17:00', 0, 5) }}" required {{ !$canManageJadwal ? 'disabled' : '' }} style="text-align:center; font-weight:800; font-family:var(--font-mono); font-size:13px;" />
                    </div>
                  </td>
                  <td style="padding:9px 16px; vertical-align:middle;">
                    <input type="text" name="mingguan[{{ $jm->hari }}][keterangan]" value="{{ $jm->keterangan }}" placeholder="Keterangan hari {{ $jm->hari }}" {{ !$canManageJadwal ? 'disabled' : '' }} style="width:100%; height:34px; font-size:12px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:6px; padding:0 10px;" />
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        @if($canManageJadwal)
          <div style="padding:12px 18px; border-top:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; margin:0; font-size:12.5px; font-weight:700; color:var(--text);">
              <input type="checkbox" name="terapkan_hari_ini" value="1" checked style="width:16px; height:16px; cursor:pointer;" />
              <span>Terapkan langsung ke jadwal hari ini ({{ \Carbon\Carbon::parse($today)->locale('id')->isoFormat('dddd') }})</span>
            </label>
            <button type="submit" class="btn btn-primary" style="background:#000000; color:#FFFFFF; height:38px; padding:0 22px; font-weight:800; font-size:12.5px; border-radius:8px; display:inline-flex; align-items:center; gap:6px;">
              <i class="bi bi-floppy-fill"></i> Simpan Jam Operasional
            </button>
          </div>
        @else
          <div style="padding:12px 18px; border-top:1px solid var(--border); background:var(--bg-3); font-size:12px; color:var(--text-3); font-weight:600;">
            <i class="bi bi-lock-fill"></i> Pengubahan jam operasional mingguan dibatasi untuk Admin, Kepala Sekolah, Waka Kurikulum, dan Waka Kesiswaan.
          </div>
        @endif
      </form>
    </div>

    {{-- ══ 5. RIWAYAT PERUBAHAN JADWAL (TABEL TERPADU) ══ --}}
    <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
      <div style="padding:14px 18px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="font-size:15px; font-weight:800; color:#000000; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-clock-history" style="color:#000000;"></i>
          <span>Riwayat Jadwal Operasional (10 Hari Terakhir)</span>
        </div>
      </div>

      @if($riwayatJadwal->isEmpty())
        <div style="text-align:center; padding:32px; color:var(--text-3); font-size:13px;">
          <i class="bi bi-calendar-x" style="font-size:32px; display:block; margin-bottom:8px; opacity:0.6;"></i>
          Belum ada riwayat jadwal tersimpan.
        </div>
      @else
        <div class="table-responsive" style="overflow-x:auto;">
          <table class="data-table" style="width:100%; border-collapse:collapse;">
            <thead>
              <tr style="background:var(--bg-3);">
                <th style="padding:12px 14px; color:#000000; font-weight:800;">Tanggal</th>
                <th style="padding:12px 14px; color:#000000; font-weight:800;">Hari</th>
                <th style="padding:12px 14px; text-align:center; color:#000000; font-weight:800;">Batas Masuk</th>
                <th style="padding:12px 14px; text-align:center; color:#000000; font-weight:800;">Mulai Pulang</th>
                <th style="padding:12px 14px; text-align:center; color:#000000; font-weight:800;">Tutup Gerbang</th>
                <th style="padding:12px 14px; color:#000000; font-weight:800;">Keterangan</th>
                <th style="padding:12px 14px; color:#000000; font-weight:800;">Diubah Oleh</th>
              </tr>
            </thead>
            <tbody>
              @foreach($riwayatJadwal as $rj)
                @php $isToday = $rj->tanggal === $today; @endphp
                <tr style="border-bottom:1px solid var(--border);">
                  <td style="padding:12px 14px;">
                    <span style="font-family:var(--font-mono); font-weight:700; color:#000000;">
                      {{ \Carbon\Carbon::parse($rj->tanggal)->format('d/m/Y') }}
                    </span>
                    @if($isToday)
                      &nbsp;<span style="color:#000000; font-size:10px; font-weight:800;">HARI INI</span>
                    @endif
                  </td>
                  <td style="padding:12px 14px; color:#000000; font-weight:700;">
                    {{ \Carbon\Carbon::parse($rj->tanggal)->translatedFormat('l') }}
                  </td>
                  <td style="padding:12px 14px; text-align:center;">
                    <span style="font-family:var(--font-mono); font-weight:800; font-size:13px; color:#000000;">
                      {{ substr($rj->jam_masuk_toleransi, 0, 5) }}
                    </span>
                  </td>
                  <td style="padding:12px 14px; text-align:center;">
                    <span style="font-family:var(--font-mono); font-weight:800; font-size:13px; color:#000000;">
                      {{ substr($rj->jam_pulang_mulai, 0, 5) }}
                    </span>
                  </td>
                  <td style="padding:12px 14px; text-align:center;">
                    <span style="font-family:var(--font-mono); font-weight:800; font-size:13px; color:#000000;">
                      {{ substr($rj->jam_tutup_gerbang ?? '17:00', 0, 5) }}

                    </span>
                  </td>
                  <td style="padding:12px 14px; color:#000000; font-size:12.5px; font-weight:600;">
                    {{ $rj->keterangan ?? '—' }}
                  </td>
                  <td style="padding:12px 14px; color:#000000; font-size:11.5px; font-family:var(--font-mono); font-weight:700;">
                    {{ $rj->diubah_oleh ?? 'Sistem' }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>

    {{-- MODAL KELOLA MODE SUMATIF (STS / SAS) --}}
    @if(auth()->user()->isAdmin() || auth()->user()->isWakaKurikulum() || auth()->user()->isWakaKesiswaan() || auth()->user()->isKepalaSekolah())
    <div class="modal-overlay" id="modalKelolaModeUjian" onclick="if(event.target===this) closeModal('modalKelolaModeUjian')">
      <div class="modal-card" style="max-width:680px; padding:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:10px;">
          <div>
            <h3 style="font-size:16px; font-weight:900; color:var(--text); margin:0; display:flex; align-items:center; gap:8px;">
              <i class="bi bi-mortarboard-fill" style="color:#4f46e5;"></i> Pengaturan Mode Pekan Sumatif (STS / SAS)
            </h3>
            <div style="font-size:11.5px; color:var(--text-3); margin-top:2px;">Atur jam kepulangan Smart Gate dan dewan guru yang bertugas sebagai panitia ujian sumatif</div>
          </div>
          <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('modalKelolaModeUjian')"><i class="bi bi-x-lg"></i></button>
        </div>

        <form action="{{ route('mode-ujian.simpan') }}" method="POST">
          @csrf
          @if($modeUjian)
            <input type="hidden" name="id" value="{{ $modeUjian->id }}">
          @endif

          <div style="display:grid; grid-template-columns: 2fr 1fr; gap:14px; margin-bottom:14px;">
            <div class="form-group">
              <label style="font-size:11.5px; font-weight:800; text-transform:uppercase; color:var(--text); margin-bottom:5px; display:block;">
                Nama Pelaksanaan Ujian <span style="color:var(--red);">*</span>
              </label>
              <input type="text" name="nama_ujian" value="{{ $modeUjian->nama_ujian ?? 'Sumatif Tengah Semester (STS) Ganjil TP 2026/2027' }}" required class="form-control" style="width:100%; height:38px; border-radius:6px; border:1px solid var(--border-2); padding:0 10px; font-size:13px;" />
            </div>
            <div class="form-group">
              <label style="font-size:11.5px; font-weight:800; text-transform:uppercase; color:var(--text); margin-bottom:5px; display:block;">
                Tipe Ujian <span style="color:var(--red);">*</span>
              </label>
              <select name="tipe" class="form-control" style="width:100%; height:38px; border-radius:6px; border:1px solid var(--border-2); padding:0 10px; font-size:13px;">
                @php $currentTipe = $modeUjian->tipe ?? 'STS'; @endphp
                <option value="STS" {{ $currentTipe === 'STS' ? 'selected' : '' }}>STS (Sumatif Tengah Semester)</option>
                <option value="SAS" {{ $currentTipe === 'SAS' ? 'selected' : '' }}>SAS (Sumatif Akhir Semester)</option>
                <option value="SAT" {{ $currentTipe === 'SAT' ? 'selected' : '' }}>SAT (Sumatif Akhir Tahun)</option>
                <option value="USBK" {{ $currentTipe === 'USBK' ? 'selected' : '' }}>USBK / US</option>
                <option value="Lainnya" {{ $currentTipe === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
              </select>
            </div>
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:14px; margin-bottom:14px;">
            <div class="form-group">
              <label style="font-size:11.5px; font-weight:800; text-transform:uppercase; color:var(--text); margin-bottom:5px; display:block;">
                Tanggal Mulai <span style="color:var(--red);">*</span>
              </label>
              <input type="date" name="tanggal_mulai" value="{{ $modeUjian->tanggal_mulai ?? '2026-09-21' }}" required class="form-control" style="width:100%; height:38px; border-radius:6px; border:1px solid var(--border-2); padding:0 10px; font-size:13px;" />
            </div>
            <div class="form-group">
              <label style="font-size:11.5px; font-weight:800; text-transform:uppercase; color:var(--text); margin-bottom:5px; display:block;">
                Tanggal Selesai <span style="color:var(--red);">*</span>
              </label>
              <input type="date" name="tanggal_selesai" value="{{ $modeUjian->tanggal_selesai ?? '2026-09-25' }}" required class="form-control" style="width:100%; height:38px; border-radius:6px; border:1px solid var(--border-2); padding:0 10px; font-size:13px;" />
            </div>
            <div class="form-group">
              <label style="font-size:11.5px; font-weight:800; text-transform:uppercase; color:#4f46e5; margin-bottom:5px; display:block;">
                <i class="bi bi-door-open-fill"></i> Jam Pulang Ujian <span style="color:var(--red);">*</span>
              </label>
              <input type="time" name="jam_pulang_mulai" value="{{ substr($modeUjian->jam_pulang_mulai ?? '11:30', 0, 5) }}" required class="form-control" style="width:100%; height:38px; border-radius:6px; border:1.5px solid #6366f1; padding:0 10px; font-size:14px; font-weight:800; color:#4f46e5;" />
            </div>
          </div>

          <div style="background:rgba(79, 70, 229, 0.05); border:1px solid rgba(99, 102, 241, 0.2); border-radius:8px; padding:10px 14px; margin-bottom:14px;">
            <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer; font-size:12.5px; font-weight:700; color:var(--text);">
              <input type="checkbox" name="nonaktifkan_piket_reguler" value="1" {{ ($modeUjian?->nonaktifkan_piket_reguler ?? true) ? 'checked' : '' }} style="width:16px; height:16px; accent-color:#4f46e5;">
              <span>Nonaktifkan Jadwal Guru Piket Reguler (Meja piket dialihkan penuh ke Panitia Pelaksana Sumatif)</span>
            </label>
          </div>

          {{-- Pilih Panitia Pelaksana Sumatif --}}
          <div style="margin-bottom:16px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
              <label style="font-size:11.5px; font-weight:800; text-transform:uppercase; color:var(--text); margin:0;">
                Pilih Personel Panitia Sumatif (Akses Operasional Meja Piket):
              </label>
              <input type="text" id="filterPanitiaGtk" placeholder="Cari guru..." oninput="filterChecklistPanitia(this.value)" style="height:28px; width:160px; font-size:11.5px; border-radius:6px; border:1px solid var(--border-2); padding:0 8px;">
            </div>

            @php
              $selectedPanitiaIds = $modeUjian->panitia_guru_ids ?? [];
            @endphp
            <div style="max-height:180px; overflow-y:auto; border:1px solid var(--border-2); border-radius:8px; padding:8px 12px; background:var(--bg-2); display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:6px;" id="listPanitiaChecklist">
              @foreach($semuaGuru as $g)
                @php $checked = in_array($g->id, $selectedPanitiaIds); @endphp
                <label class="panitia-item-label" style="display:flex; align-items:center; gap:8px; padding:4px 6px; border-radius:6px; cursor:pointer; font-size:12px; background:{{ $checked ? 'rgba(79, 70, 229, 0.08)' : 'transparent' }};">
                  <input type="checkbox" name="panitia_guru_ids[]" value="{{ $g->id }}" {{ $checked ? 'checked' : '' }} style="accent-color:#4f46e5;">
                  <span class="panitia-nama" style="font-weight:{{ $checked ? '700' : '500' }}; color:var(--text);">{{ $g->nama }}</span>
                </label>
              @endforeach
            </div>
          </div>

          <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--border); padding-top:14px;">
            <button type="button" class="btn btn-outline" onclick="closeModal('modalKelolaModeUjian')">Batal</button>
            <button type="submit" class="btn" style="background:#4f46e5; color:#fff; font-weight:800; padding:8px 18px; border-radius:6px; border:none; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
              <i class="bi bi-check2-circle"></i> Simpan Pengaturan Sumatif
            </button>
          </div>
        </form>
      </div>
    </div>
    @endif

    {{-- MODAL LIBUR DARURAT / MENDADAK --}}
    @if(!$isLibur && (auth()->user()->isAdmin() || auth()->user()->isPiketHariIni() || auth()->user()->isKepalaSekolah() || auth()->user()->isWakaKurikulum() || auth()->user()->isWakaKesiswaan()))
    <div class="modal-overlay" id="modalLiburDarurat">
      <div class="modal-card" style="max-width:520px; padding:24px; border-top:4px solid #dc2626;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
          <div style="display:flex; align-items:center; gap:10px;">
            <div style="width:38px; height:38px; border-radius:10px; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; font-size:18px;">
              <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div>
              <h3 style="font-size:16px; font-weight:900; color:var(--text); margin:0;">Liburkan Sekolah Hari Ini</h3>
              <div style="font-size:11.5px; color:var(--text-3);">Akomodasi Libur Mendadak / Darurat Khusus Sekolah</div>
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-outline" onclick="closeModal('modalLiburDarurat')"><i class="bi bi-x-lg"></i></button>
        </div>

        <form method="POST" action="{{ route('piket.libur-darurat') }}">
          @csrf
          <div style="display:flex; flex-direction:column; gap:14px;">
            <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:12px; font-size:12px; color:#991b1b; line-height:1.5;">
              <strong><i class="bi bi-shield-check"></i> Mekanisme Pengamanan Alpha Otomatis:</strong>
              <ul style="margin:6px 0 0 16px; padding:0;">
                <li>Status <strong>Alpha</strong> siswa &amp; guru hari ini akan <strong>dibatalkan &amp; dihapus otomatis</strong>.</li>
                <li>Poin pelanggaran kedisiplinan siswa akibat Alpha hari ini akan <strong>direset kembali</strong>.</li>
                <li>Draf pesan WhatsApp Alpha ke wali murid yang belum terkirim akan <strong>dibatalkan</strong>.</li>
              </ul>
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label style="font-size:11.5px; font-weight:700; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">
                Nama / Perihal Libur <span style="color:var(--red);">*</span>
              </label>
              <input 
                type="text" 
                name="nama_libur" 
                value="Libur Khusus Sekolah" 
                placeholder="Misal: Bencana Banjir / Rapat Dinas Mendadak / Cuaca Ekstrem" 
                required 
                style="width:100%; height:40px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px; font-weight:700;"
              />
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label style="font-size:11.5px; font-weight:700; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">
                Keterangan / Alasan Libur Mendadak
              </label>
              <textarea 
                name="keterangan" 
                rows="3" 
                placeholder="Tuliskan alasan resmi mengapa proses KBM diliburkan hari ini..." 
                style="width:100%; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:8px 12px; color:var(--text); font-size:12.5px; font-family:inherit; resize:none;"
              ></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:8px;">
              <button type="button" class="btn btn-outline" onclick="closeModal('modalLiburDarurat')" style="padding:8px 16px; font-size:12.5px; font-weight:700;">
                Batal
              </button>
              <button type="submit" class="btn btn-danger" style="background:#dc2626; color:#fff; border:none; padding:8px 18px; font-size:12.5px; font-weight:800; border-radius:8px; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                <i class="bi bi-calendar-x-fill"></i> Konfirmasi Liburkan Hari Ini
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
    @endif

  </main>
</div>

<script>
  // Modal Handlers
  function openModal(id) {
    const el = document.getElementById(id);
    if (el) {
      el.classList.add('active', 'open');
      el.style.display = 'flex';
      el.style.opacity = '1';
      el.style.visibility = 'visible';
      el.style.pointerEvents = 'auto';
      document.body.style.overflow = 'hidden';
    }
  }

  function closeModal(id) {
    const el = document.getElementById(id);
    if (el) {
      el.classList.remove('active', 'open');
      el.style.display = 'none';
      el.style.opacity = '0';
      el.style.visibility = 'hidden';
      el.style.pointerEvents = 'none';
      document.body.style.overflow = '';
    }
  }

  function filterChecklistPanitia(val) {
    const q = (val || '').toLowerCase().trim();
    document.querySelectorAll('#listPanitiaChecklist .panitia-item-label').forEach(label => {
      const nama = (label.querySelector('.panitia-nama')?.innerText || '').toLowerCase();
      label.style.display = (!q || nama.includes(q)) ? 'flex' : 'none';
    });
  }

  // Live Clock Updater
  setInterval(function() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    const el = document.getElementById('liveClockDisplay');
    if (el) el.innerText = h + ':' + m + ':' + s;
  }, 1000);

  // Scenario Selector Function
  function selectScenario(masuk, pulang, tutup, keterangan, cardEl) {
    document.getElementById('jam_masuk_toleransi').value = masuk;
    document.getElementById('jam_pulang_mulai').value = pulang;
    document.getElementById('jam_tutup_gerbang').value = tutup;
    document.getElementById('keterangan').value = keterangan;

    // Highlight card
    document.querySelectorAll('.scenario-card').forEach(c => c.classList.remove('selected'));
    if (cardEl) cardEl.classList.add('selected');

    // Highlight inputs momentarily
    ['jam_masuk_toleransi', 'jam_pulang_mulai', 'jam_tutup_gerbang', 'keterangan'].forEach(id => {
      const el = document.getElementById(id);
      if (el) {
        el.style.borderColor = '#000000';
        setTimeout(() => el.style.borderColor = '', 900);
      }
    });
  }

  // Form submit state
  const formJadwal = document.getElementById('form-jadwal');
  if (formJadwal) {
    formJadwal.addEventListener('submit', function () {
      const btn = document.getElementById('btn-simpan-jadwal');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menerapkan Jadwal…';
      }
    });
  }
</script>

</body>
</html>
