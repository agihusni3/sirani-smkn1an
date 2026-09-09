<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Presensi 2D Barcode Ujian Seleksi PPDB — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/admin-ppdb.css') }}?v={{ filemtime(public_path('css/admin-ppdb.css')) }}">
  <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

  <style>
    .kios-hero {
      background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #312e81 100%);
      border-radius: 16px;
      padding: 24px 28px;
      color: #ffffff;
      margin-bottom: 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
      border: 1px solid rgba(255, 255, 255, 0.12);
      box-shadow: 0 10px 25px rgba(30, 27, 75, 0.2);
    }
    .kios-grid {
      display: grid;
      grid-template-columns: 1.15fr 0.85fr;
      gap: 24px;
      margin-bottom: 28px;
    }
    @media (max-width: 992px) {
      .kios-grid {
        grid-template-columns: 1fr;
      }
    }
    .scanner-box {
      background: #ffffff;
      border: 2px dashed #cbd5e1;
      border-radius: 18px;
      padding: 32px 24px;
      text-align: center;
      position: relative;
      transition: all 0.25s ease;
      box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }
    .scanner-box.active {
      border-color: #2563eb;
      background: #f8fafc;
      box-shadow: 0 0 0 4px rgba(37,99,235,0.12);
    }
    .scanner-radar {
      width: 88px;
      height: 88px;
      border-radius: 24px;
      background: linear-gradient(135deg, rgba(37,99,235,0.12), rgba(79,70,229,0.06));
      border: 1px solid rgba(37,99,235,0.25);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 16px auto;
      color: #2563eb;
      font-size: 40px;
      position: relative;
      animation: radarPulse 2.4s infinite ease-in-out;
    }
    @keyframes radarPulse {
      0% { box-shadow: 0 0 0 0 rgba(37,99,235,0.3); }
      70% { box-shadow: 0 0 0 16px rgba(37,99,235,0); }
      100% { box-shadow: 0 0 0 0 rgba(37,99,235,0); }
    }
    .stat-card-presensi {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      padding: 16px 20px;
      display: flex;
      align-items: center;
      gap: 16px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
    .stat-icon-circle {
      width: 46px;
      height: 46px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      flex-shrink: 0;
    }
    /* Modal Pop-up Kartu Kehadiran */
    .modal-presensi-overlay {
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(15, 23, 42, 0.7);
      backdrop-filter: blur(5px);
      z-index: 9999;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .modal-presensi-overlay.show {
      display: flex;
      animation: modalFadeIn 0.2s ease-out;
    }
    @keyframes modalFadeIn {
      from { opacity: 0; transform: scale(0.96); }
      to { opacity: 1; transform: scale(1); }
    }
    .modal-presensi-card {
      background: #ffffff;
      border-radius: 20px;
      max-width: 520px;
      width: 100%;
      box-shadow: 0 25px 50px -12px rgba(0,0,0,0.35);
      border: 2px solid #e2e8f0;
      overflow: hidden;
      position: relative;
    }
  </style>
</head>
<body>
<div class="app-container">
  @include('partials.sidebar_ppdb')
  
  <main class="main-content">
    
    {{-- 1. HERO HEADER KIOS PRESENSI --}}
    <div class="kios-hero">
      <div>
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
          <span style="background:rgba(217,119,6,0.25); color:#fde68a; border:1px solid rgba(245,158,11,0.4); padding:3px 10px; border-radius:20px; font-size:11px; font-weight:800; letter-spacing:0.04em;">
            <i class="bi bi-qr-code-scan me-1"></i> 2D BARCODE / QR SCANNER
          </span>
          <span style="font-size:12px; color:#cbd5e1;">PPDB TP. 2026/2027</span>
        </div>
        <h1 style="font-size:22px; font-weight:900; margin:0; letter-spacing:-0.02em;">
          Kios Presensi Ujian Tulis CBT
        </h1>
        <p style="margin:4px 0 0; color:#cbd5e1; font-size:13px;">
          Pindai kartu peserta calon siswa untuk verifikasi kehadiran dan membuka akses CBT di ruang tes.
        </p>
      </div>

      <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <a href="{{ route('admin.ppdb.presensi.cetak', ['tanggal' => $tanggal, 'sesi' => $sesiFilter, 'ruang' => $ruangFilter]) }}" target="_blank" class="btn" style="background:#ffffff; color:#0f172a; font-weight:800; font-size:12.5px; border-radius:8px; padding:9px 16px; border:1px solid #cbd5e1; display:inline-flex; align-items:center; gap:8px; text-decoration:none;">
          <i class="bi bi-printer-fill" style="color:#2563eb;"></i>
          <span>Cetak Berita Acara &amp; Hadir (A4)</span>
        </a>

        <a href="{{ route('admin.ppdb.seleksi') }}" class="btn" style="background:rgba(255,255,255,0.12); color:#ffffff; font-weight:700; font-size:12.5px; border-radius:8px; padding:9px 14px; border:1px solid rgba(255,255,255,0.2); display:inline-flex; align-items:center; gap:6px; text-decoration:none;">
          <i class="bi bi-laptop"></i>
          <span>Seleksi CBT</span>
        </a>
      </div>
    </div>

    @if(session('success'))
      <div style="background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:12px 18px; border-radius:10px; margin-bottom:20px; font-size:13.5px; font-weight:600; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-check-circle-fill text-emerald-600"></i>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    {{-- 2. STATISTIK RINGKAS REAL-TIME --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(210px, 1fr)); gap:16px; margin-bottom:24px;">
      
      <div class="stat-card-presensi">
        <div class="stat-icon-circle" style="background:#eff6ff; color:#2563eb;">
          <i class="bi bi-people-fill"></i>
        </div>
        <div>
          <div style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase;">Terjadwal Hari Ini</div>
          <div style="font-size:22px; font-weight:900; color:#0f172a;">{{ $totalTerjadwal }} <span style="font-size:13px; font-weight:600; color:#64748b;">Siswa</span></div>
        </div>
      </div>

      <div class="stat-card-presensi" style="border-left:4px solid #10b981;">
        <div class="stat-icon-circle" style="background:#ecfdf5; color:#059669;">
          <i class="bi bi-check2-circle"></i>
        </div>
        <div>
          <div style="font-size:11.5px; font-weight:700; color:#059669; text-transform:uppercase;">Sudah Hadir (Scan)</div>
          <div style="font-size:22px; font-weight:900; color:#065f46;">
            {{ $totalHadir }} 
            <span style="font-size:12px; font-weight:700; background:#d1fae5; color:#065f46; padding:2px 8px; border-radius:12px; margin-left:4px;">{{ $persenHadir }}%</span>
          </div>
        </div>
      </div>

      <div class="stat-card-presensi" style="border-left:4px solid #f59e0b;">
        <div class="stat-icon-circle" style="background:#fffbeb; color:#d97706;">
          <i class="bi bi-hourglass-top"></i>
        </div>
        <div>
          <div style="font-size:11.5px; font-weight:700; color:#b45309; text-transform:uppercase;">Belum Hadir</div>
          <div style="font-size:22px; font-weight:900; color:#92400e;">{{ $totalBelumHadir }} <span style="font-size:13px; font-weight:600; color:#64748b;">Siswa</span></div>
        </div>
      </div>

      <div class="stat-card-presensi">
        <div class="stat-icon-circle" style="background:#f1f5f9; color:#475569;">
          <i class="bi bi-clock-history"></i>
        </div>
        <div>
          <div style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase;">Waktu Sistem</div>
          <div id="liveDigitalClock" style="font-size:20px; font-weight:900; font-family:var(--font-mono, monospace); color:#0f172a;">--:--:--</div>
        </div>
      </div>

    </div>

    {{-- 3. FILTER SESI, RUANGAN & TANGGAL --}}
    <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:14px; padding:16px 20px; margin-bottom:24px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
      <form method="GET" action="{{ route('admin.ppdb.presensi.kios') }}" style="display:flex; gap:14px; align-items:flex-end; flex-wrap:wrap;">
        <div>
          <label style="font-size:11.5px; font-weight:800; color:#475569; display:block; margin-bottom:5px;">Tanggal Ujian:</label>
          <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control" style="font-size:12.5px; padding:7px 12px; border-radius:8px; border:1px solid #cbd5e1; font-weight:600;" onchange="this.form.submit()">
        </div>

        <div>
          <label style="font-size:11.5px; font-weight:800; color:#475569; display:block; margin-bottom:5px;">Waktu Ujian:</label>
          <select name="sesi" class="form-select" style="font-size:12.5px; padding:7px 12px; border-radius:8px; border:1px solid #cbd5e1; font-weight:600;" onchange="this.form.submit()">
            <option value="">Semua Waktu Ujian</option>
            @foreach($daftarSesi as $s)
              <option value="{{ $s }}" {{ $sesiFilter == $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label style="font-size:11.5px; font-weight:800; color:#475569; display:block; margin-bottom:5px;">Ruang Ujian:</label>
          <select name="ruang" class="form-select" style="font-size:12.5px; padding:7px 12px; border-radius:8px; border:1px solid #cbd5e1; font-weight:600;" onchange="this.form.submit()">
            <option value="">Semua Ruangan</option>
            @foreach($daftarRuang as $r)
              <option value="{{ $r }}" {{ $ruangFilter == $r ? 'selected' : '' }}>{{ $r }}</option>
            @endforeach
          </select>
        </div>

        @if($sesiFilter || $ruangFilter || $tanggal != \Carbon\Carbon::today()->toDateString())
          <a href="{{ route('admin.ppdb.presensi.kios') }}" class="btn" style="background:#f1f5f9; color:#475569; font-weight:700; font-size:12.5px; border-radius:8px; padding:7px 14px; text-decoration:none;">
            Reset Filter
          </a>
        @endif
      </form>
    </div>

    {{-- 4. DUAL SCANNER INTERFACE & LIVE FEED --}}
    <div class="kios-grid">
      
      {{-- KOLOM KIRI: SCANNER PORTAL --}}
      <div>
        <div class="scanner-box active" id="scannerBoxArea" onclick="focusScannerInput()">
          <div class="scanner-radar">
            <i class="bi bi-qr-code-scan"></i>
          </div>

          <h2 style="font-size:20px; font-weight:900; color:#0f172a; margin:0 0 6px 0;">
            Arahkan 2D Barcode / QR ke Pemindai
          </h2>
          <p style="color:#64748b; font-size:13px; max-width:440px; margin:0 auto 18px auto; line-height:1.4;">
            Tempelkan 2D Barcode kartu pendaftaran calon siswa ke sensor scanner USB atau gunakan kamera perangkat di bawah.
          </p>

          {{-- Input Otomatis untuk USB Barcode Scanner (Kecepatan Tinggi) --}}
          <div style="max-width:380px; margin:0 auto 16px auto; position:relative;">
            <input type="text" id="barcodeScannerInput" autocomplete="off" placeholder="Menunggu scan barcode 2D..." 
              style="width:100%; padding:12px 42px 12px 16px; border:2px solid #2563eb; border-radius:10px; font-size:14px; font-weight:800; font-family:var(--font-mono, monospace); text-align:center; outline:none; box-sizing:border-box; background:#ffffff; box-shadow:0 2px 8px rgba(37,99,235,0.08);" />
            <div id="scanIndicatorIcon" style="position:absolute; right:14px; top:50%; transform:translateY(-50%); color:#10b981; font-size:18px;">
              <i class="bi bi-broadcast"></i>
            </div>
          </div>

          <div style="display:flex; justify-content:center; gap:12px; align-items:center;">
            <button type="button" id="btnToggleCamera" class="btn" onclick="toggleCameraScanner()" style="background:#f8fafc; border:1px solid #cbd5e1; color:#334155; font-size:12.5px; font-weight:700; border-radius:8px; padding:7px 16px; display:inline-flex; align-items:center; gap:8px;">
              <i class="bi bi-camera-video"></i>
              <span>Nyalakan Pemindai Kamera</span>
            </button>
            <span style="font-size:11.5px; color:#94a3b8;">Scanner USB Siap (Autofocus On)</span>
          </div>

          {{-- Container Kamera Scanner HTML5 QR --}}
          <div id="cameraScannerWrapper" style="display:none; margin-top:20px; border-top:1px solid #e2e8f0; padding-top:16px;">
            <div id="reader" style="width:100%; max-width:420px; margin:0 auto; border-radius:12px; overflow:hidden;"></div>
            <div style="font-size:11.5px; color:#64748b; margin-top:8px;">
              Posisikan QR Code kartu peserta di dalam kotak kamera.
            </div>
          </div>

          {{-- Status Feedback Banner --}}
          <div id="scanStatusFeedback" style="display:none; margin-top:18px; padding:10px 16px; border-radius:8px; font-size:13px; font-weight:700;"></div>
        </div>
      </div>

      {{-- KOLOM KANAN: LIVE FEED KEHADIRAN TERBARU --}}
      <div>
        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:20px; box-shadow:0 2px 6px rgba(0,0,0,0.02); height:100%; box-sizing:border-box; display:flex; flex-direction:column;">
          
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid #f1f5f9;">
            <div style="display:flex; align-items:center; gap:8px;">
              <div style="width:8px; height:8px; border-radius:50%; background:#10b981; animation:livePulse 1.5s infinite;"></div>
              <h3 style="font-size:14px; font-weight:800; color:#0f172a; margin:0;">Live Feed Kehadiran Hari Ini</h3>
            </div>
            <span style="font-size:11.5px; font-weight:700; color:#64748b;" id="feedCount">{{ $riwayatTerbaru->count() }} Data</span>
          </div>

          <div id="liveFeedList" style="overflow-y:auto; max-height:360px; display:flex; flex-direction:column; gap:10px; flex:1;">
            @forelse($riwayatTerbaru as $item)
              <div class="live-feed-item" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:10px 12px; display:flex; justify-content:space-between; align-items:center;">
                <div style="display:flex; align-items:center; gap:10px;">
                  <div style="width:34px; height:34px; border-radius:8px; background:#eff6ff; color:#2563eb; font-weight:800; display:flex; align-items:center; justify-content:center; font-size:12px;">
                    <i class="bi bi-person-check-fill"></i>
                  </div>
                  <div>
                    <div style="font-weight:800; font-size:12.5px; color:#0f172a;">{{ $item->pendaftar->nama_lengkap ?? $item->no_pendaftaran }}</div>
                    <div style="font-size:11px; color:#64748b;">
                      <span style="font-family:monospace; font-weight:700; color:#2563eb;">{{ $item->no_pendaftaran }}</span> &bull; {{ $item->pendaftar->jurusanPilihan1->kode_jurusan ?? '' }}
                    </div>
                  </div>
                </div>

                <div style="text-align:right;">
                  <span style="font-size:11px; font-weight:800; background:#ecfdf5; color:#059669; padding:2px 8px; border-radius:12px; display:inline-block;">
                    {{ $item->waktu_hadir->format('H:i:s') }}
                  </span>
                  <form action="{{ route('admin.ppdb.presensi.batal', $item->id) }}" method="POST" onsubmit="return confirm('Batalkan presensi peserta ini?')" style="display:inline; margin-left:4px;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background:none; border:none; color:#94a3b8; font-size:13px; cursor:pointer; padding:2px;" title="Batalkan">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </div>
            @empty
              <div id="feedEmptyState" style="text-align:center; padding:32px 16px; color:#94a3b8; font-size:13px;">
                <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:6px;"></i>
                Belum ada calon siswa yang scan hari ini.
              </div>
            @endforelse
          </div>

        </div>
      </div>

    </div>

    {{-- 5. DAFTAR SELURUH PESERTA TERJADWAL & PENGESAHAN MANUAL --}}
    <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:22px; box-shadow:0 2px 6px rgba(0,0,0,0.02);">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
        <div>
          <h3 style="font-size:15px; font-weight:900; color:#0f172a; margin:0 0 2px 0;">
            Daftar Peserta Terjadwal Ujian Seleksi
          </h3>
          <p style="font-size:12.5px; color:#64748b; margin:0;">
            Pantau kehadiran atau sahkan presensi manual jika kartu peserta tertinggal.
          </p>
        </div>

        <div style="display:flex; align-items:center; gap:10px;">
          <input type="text" id="filterTableSearch" placeholder="Cari nama / nomor pendaftaran..." onkeyup="filterTabelPeserta()"
            style="padding:6px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:12.5px; width:220px;">
        </div>
      </div>

      <div style="overflow-x:auto;">
        <table class="table" id="tabelPesertaPresensi" style="width:100%; border-collapse:collapse; font-size:12.5px;">
          <thead>
            <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0; text-align:left;">
              <th style="padding:10px 14px; font-weight:800; color:#475569; width:50px;">No</th>
              <th style="padding:10px 14px; font-weight:800; color:#475569;">No. Pendaftaran / NISN</th>
              <th style="padding:10px 14px; font-weight:800; color:#475569;">Nama Lengkap Siswa</th>
              <th style="padding:10px 14px; font-weight:800; color:#475569;">Pilihan Jurusan</th>
              <th style="padding:10px 14px; font-weight:800; color:#475569;">Ruang &amp; Waktu</th>
              <th style="padding:10px 14px; font-weight:800; color:#475569; text-align:center;">Status Kehadiran</th>
              <th style="padding:10px 14px; font-weight:800; color:#475569; text-align:center; width:130px;">Aksi Panitia</th>
            </tr>
          </thead>
          <tbody>
            @forelse($listPeserta as $idx => $peserta)
              @php
                $isHadir = $peserta->absensiUjian !== null;
              @endphp
              <tr style="border-bottom:1px solid #f1f5f9; transition:background 0.15s;" class="row-peserta" data-search="{{ strtolower($peserta->nama_lengkap . ' ' . $peserta->no_pendaftaran . ' ' . $peserta->nisn) }}">
                <td style="padding:10px 14px; color:#64748b; font-weight:700;">{{ $idx + 1 }}</td>
                <td style="padding:10px 14px;">
                  <span style="font-family:monospace; font-weight:800; color:#0f172a;">{{ $peserta->no_pendaftaran }}</span>
                  <div style="font-size:11px; color:#64748b;">NISN: {{ $peserta->nisn }}</div>
                </td>
                <td style="padding:10px 14px; font-weight:800; color:#0f172a;">
                  {{ strtoupper($peserta->nama_lengkap) }}
                  <div style="font-size:11px; color:#64748b; font-weight:500;">{{ $peserta->asal_sekolah }}</div>
                </td>
                <td style="padding:10px 14px;">
                  <span style="font-weight:700; color:#2563eb;">{{ $peserta->jurusanPilihan1->kode_jurusan ?? '-' }}</span>
                  <div style="font-size:11px; color:#64748b;">{{ $peserta->jurusanPilihan1->nama_jurusan ?? '' }}</div>
                </td>
                <td style="padding:10px 14px;">
                  <div style="font-weight:700; color:#334155;">{{ $peserta->jadwal_tes_ruang ?: 'Lab Komputer' }}</div>
                  <div style="font-size:11px; color:#64748b;">{{ $peserta->jadwal_sesi_resmi }}</div>
                </td>
                <td style="padding:10px 14px; text-align:center;">
                  @if($isHadir)
                    <span style="display:inline-flex; align-items:center; gap:4px; background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:800;">
                      <i class="bi bi-check-circle-fill"></i> HADIR ({{ $peserta->absensiUjian->waktu_hadir->format('H:i') }})
                    </span>
                  @else
                    <span style="display:inline-flex; align-items:center; gap:4px; background:#fffbeb; color:#b45309; border:1px solid #fde68a; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:800;">
                      <i class="bi bi-clock"></i> BELUM HADIR
                    </span>
                  @endif
                </td>
                <td style="padding:10px 14px; text-align:center;">
                  @if(!$isHadir)
                    <form action="{{ route('admin.ppdb.presensi.manual', $peserta->id) }}" method="POST" style="display:inline;">
                      @csrf
                      <button type="submit" class="btn" style="background:#0284c7; color:#ffffff; font-size:11px; font-weight:800; border-radius:6px; padding:4px 10px; border:none; cursor:pointer;" title="Sahkan Kehadiran Manual">
                        <i class="bi bi-check2 me-1"></i> Hadir Manual
                      </button>
                    </form>
                  @else
                    <form action="{{ route('admin.ppdb.presensi.batal', $peserta->absensiUjian->id) }}" method="POST" onsubmit="return confirm('Batalkan status hadir siswa ini?')" style="display:inline;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn" style="background:#f1f5f9; color:#ef4444; font-size:11px; font-weight:700; border-radius:6px; padding:4px 8px; border:1px solid #fecaca; cursor:pointer;" title="Batalkan Presensi">
                        <i class="bi bi-x-circle"></i> Batal
                      </button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" style="text-align:center; padding:32px 14px; color:#94a3b8;">
                  Tidak ada data calon siswa terdaftar untuk filter ini.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

{{-- MODAL POP-UP VERIFIKASI KEHADIRAN SISWA --}}
<div class="modal-presensi-overlay" id="presensiSuccessModal">
  <div class="modal-presensi-card">
    
    {{-- Header Banner Status --}}
    <div id="modalHeaderStatus" style="background:#10b981; color:#ffffff; padding:16px 20px; display:flex; align-items:center; justify-content:space-between;">
      <div style="display:flex; align-items:center; gap:10px;">
        <i id="modalStatusIcon" class="bi bi-check-circle-fill" style="font-size:22px;"></i>
        <div style="font-size:15px; font-weight:900; letter-spacing:0.02em;" id="modalStatusTitle">
          PRESENSI UJIAN BERHASIL
        </div>
      </div>
      <span id="modalCountdownBadge" style="background:rgba(0,0,0,0.18); font-size:11.5px; font-weight:800; padding:3px 9px; border-radius:12px;">5s</span>
    </div>

    {{-- Detail Siswa --}}
    <div style="padding:24px 28px;">
      <div style="display:flex; gap:18px; align-items:center; margin-bottom:20px;">
        <div id="modalCandidatePhotoWrap" style="width:75px; height:95px; border-radius:10px; background:#f1f5f9; border:2px solid #cbd5e1; display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
          <img id="modalCandidatePhoto" src="" alt="Foto" style="width:100%; height:100%; object-fit:cover; display:none;" />
          <i id="modalCandidateIconFallback" class="bi bi-person-fill" style="font-size:38px; color:#94a3b8;"></i>
        </div>

        <div>
          <div style="font-size:11px; font-weight:800; color:#64748b; text-transform:uppercase;">Calon Peserta Didik</div>
          <h2 id="modalCandidateName" style="font-size:18px; font-weight:900; color:#0f172a; margin:2px 0 4px 0; line-height:1.2;">
            NAMA PESERTA
          </h2>
          <div style="font-size:13px; font-family:monospace; font-weight:800; color:#2563eb;" id="modalCandidateNo">
            PPDB-2026-XXXX
          </div>
          <div style="font-size:12px; color:#475569; margin-top:2px;" id="modalCandidateSchool">
            SMP Asal Siswa
          </div>
        </div>
      </div>

      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px 18px; display:grid; grid-template-columns:1fr 1fr; gap:12px; font-size:12px; margin-bottom:18px;">
        <div>
          <span style="color:#64748b; font-weight:700; display:block; font-size:10.5px; text-transform:uppercase;">Pilihan Keahlian:</span>
          <strong style="color:#0f172a; font-size:13px;" id="modalCandidateMajor">-</strong>
        </div>
        <div>
          <span style="color:#64748b; font-weight:700; display:block; font-size:10.5px; text-transform:uppercase;">Waktu Presensi:</span>
          <strong style="color:#059669; font-size:13px;" id="modalCandidateTime">--:--:-- WIB</strong>
        </div>
        <div>
          <span style="color:#64748b; font-weight:700; display:block; font-size:10.5px; text-transform:uppercase;">Ruang Ujian:</span>
          <strong style="color:#0f172a;" id="modalCandidateRoom">-</strong>
        </div>
        <div>
          <span style="color:#64748b; font-weight:700; display:block; font-size:10.5px; text-transform:uppercase;">Waktu Ujian:</span>
          <strong style="color:#0f172a;" id="modalCandidateSession">-</strong>
        </div>
      </div>

      <div style="text-align:center;">
        <button type="button" class="btn" onclick="closePresensiModal()" style="background:#0f172a; color:#ffffff; font-weight:800; font-size:13px; border-radius:10px; padding:10px 32px; border:none; cursor:pointer;">
          Lanjut Scan Peserta Berikutnya (Esc)
        </button>
      </div>
    </div>

  </div>
</div>

<script>
// ══ 1. WEB AUDIO API SYNTHESIZER (BEEP SOUND) ══
const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

function playBeep(type = 'success') {
  try {
    if (audioCtx.state === 'suspended') {
      audioCtx.resume();
    }
    const osc = audioCtx.createOscillator();
    const gain = audioCtx.createGain();
    osc.connect(gain);
    gain.connect(audioCtx.destination);

    if (type === 'success') {
      // 2-tone pleasant high beep (880Hz -> 1320Hz)
      osc.type = 'sine';
      osc.frequency.setValueAtTime(880, audioCtx.currentTime);
      osc.frequency.setValueAtTime(1320, audioCtx.currentTime + 0.08);
      gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.25);
      osc.start(audioCtx.currentTime);
      osc.stop(audioCtx.currentTime + 0.25);
    } else if (type === 'already') {
      // Gentle chime (523Hz)
      osc.type = 'triangle';
      osc.frequency.setValueAtTime(523.25, audioCtx.currentTime);
      gain.gain.setValueAtTime(0.35, audioCtx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.4);
      osc.start(audioCtx.currentTime);
      osc.stop(audioCtx.currentTime + 0.4);
    } else {
      // Low buzz error (220Hz)
      osc.type = 'sawtooth';
      osc.frequency.setValueAtTime(220, audioCtx.currentTime);
      gain.gain.setValueAtTime(0.4, audioCtx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.35);
      osc.start(audioCtx.currentTime);
      osc.stop(audioCtx.currentTime + 0.35);
    }
  } catch(e) {
    console.warn('Audio feedback fallback:', e);
  }
}

// ══ 2. DIGITAL CLOCK REAL-TIME ══
function updateClock() {
  const now = new Date();
  const h = String(now.getHours()).padStart(2, '0');
  const m = String(now.getMinutes()).padStart(2, '0');
  const s = String(now.getSeconds()).padStart(2, '0');
  const clockEl = document.getElementById('liveDigitalClock');
  if (clockEl) clockEl.innerText = `${h}:${m}:${s} WIB`;
}
setInterval(updateClock, 1000);
updateClock();

// ══ 3. AUTOFOCUS & SCANNER INPUT LISTENER ══
const scannerInput = document.getElementById('barcodeScannerInput');
let isProcessing = false;
let modalTimer = null;
let countdownVal = 5;

function focusScannerInput() {
  if (scannerInput && document.getElementById('presensiSuccessModal').style.display !== 'flex') {
    scannerInput.focus();
  }
}

// Listen for Enter key from hardware USB 2D Barcode scanner
scannerInput.addEventListener('keydown', function(e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    const code = scannerInput.value.trim();
    if (code.length > 0) {
      processScan(code, 'barcode_scanner');
    }
    scannerInput.value = '';
  }
});

// Auto focus on click anywhere
document.addEventListener('click', function(e) {
  if (!e.target.closest('input, select, textarea, button, a, .modal-presensi-card')) {
    focusScannerInput();
  }
});
window.addEventListener('load', focusScannerInput);

// ══ 4. PROCESS SCAN AJAX ══
function processScan(code, metode = 'barcode_scanner') {
  if (isProcessing) return;
  isProcessing = true;

  const statusFeedback = document.getElementById('scanStatusFeedback');
  statusFeedback.style.display = 'block';
  statusFeedback.style.background = '#eff6ff';
  statusFeedback.style.color = '#1d4ed8';
  statusFeedback.innerHTML = `<i class="bi bi-hourglass-split me-1"></i> Memproses scan: <strong>${code}</strong>...`;

  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  fetch("{{ route('admin.ppdb.presensi.scan') }}", {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json'
    },
    body: JSON.stringify({ input_code: code, metode: metode })
  })
  .then(res => res.json().then(data => ({ statusHttp: res.status, body: data })))
  .then(({ statusHttp, body }) => {
    isProcessing = false;

    if (body.status === 'success') {
      playBeep('success');
      showPresensiModal(body.pendaftar, 'HADIR', body.message);
      addLiveFeed(body.pendaftar);
      statusFeedback.style.background = '#ecfdf5';
      statusFeedback.style.color = '#065f46';
      statusFeedback.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i> ${body.message}`;
    } else if (body.status === 'already_attended') {
      playBeep('already');
      showPresensiModal(body.pendaftar, 'SUDAH_HADIR', body.message);
      statusFeedback.style.background = '#fef3c7';
      statusFeedback.style.color = '#92400e';
      statusFeedback.innerHTML = `<i class="bi bi-info-circle-fill me-1"></i> ${body.message}`;
    } else {
      playBeep('error');
      statusFeedback.style.background = '#fee2e2';
      statusFeedback.style.color = '#991b1b';
      statusFeedback.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> ${body.message || 'Gagal mengenali kode barcode.'}`;
    }
  })
  .catch(err => {
    isProcessing = false;
    playBeep('error');
    console.error('Scan error:', err);
    statusFeedback.style.background = '#fee2e2';
    statusFeedback.style.color = '#991b1b';
    statusFeedback.innerHTML = '<i class="bi bi-wifi-off me-1"></i> Terjadi kesalahan koneksi server.';
  });
}

// ══ 5. MODAL POPUP & COUNTDOWN AUTO-DISMISS ══
function showPresensiModal(pendaftar, status, message) {
  const modal = document.getElementById('presensiSuccessModal');
  const header = document.getElementById('modalHeaderStatus');
  const title = document.getElementById('modalStatusTitle');
  const icon = document.getElementById('modalStatusIcon');

  if (status === 'HADIR') {
    header.style.background = '#10b981';
    title.innerText = 'PRESENSI UJIAN BERHASIL';
    icon.className = 'bi bi-check-circle-fill';
  } else {
    header.style.background = '#f59e0b';
    title.innerText = 'SUDAH MELAKUKAN PRESENSI';
    icon.className = 'bi bi-info-circle-fill';
  }

  document.getElementById('modalCandidateName').innerText = pendaftar.nama_lengkap;
  document.getElementById('modalCandidateNo').innerText = pendaftar.no_pendaftaran + ' (NISN: ' + pendaftar.nisn + ')';
  document.getElementById('modalCandidateSchool').innerText = pendaftar.asal_sekolah || '-';
  document.getElementById('modalCandidateMajor').innerText = pendaftar.jurusan_kode + ' — ' + pendaftar.jurusan_1;
  document.getElementById('modalCandidateTime').innerText = pendaftar.waktu_hadir + ' WIB';
  document.getElementById('modalCandidateRoom').innerText = pendaftar.ruang || 'Lab Komputer';
  document.getElementById('modalCandidateSession').innerText = pendaftar.sesi || '08.00 - 10.00 WIB';

  const photoImg = document.getElementById('modalCandidatePhoto');
  const photoFallback = document.getElementById('modalCandidateIconFallback');
  if (pendaftar.foto) {
    photoImg.src = pendaftar.foto;
    photoImg.style.display = 'block';
    photoFallback.style.display = 'none';
  } else {
    photoImg.style.display = 'none';
    photoFallback.style.display = 'block';
  }

  modal.style.display = 'flex';
  modal.classList.add('show');

  // Countdown timer 5s
  countdownVal = 5;
  const badge = document.getElementById('modalCountdownBadge');
  badge.innerText = countdownVal + 's';

  clearInterval(modalTimer);
  modalTimer = setInterval(() => {
    countdownVal--;
    badge.innerText = countdownVal + 's';
    if (countdownVal <= 0) {
      closePresensiModal();
    }
  }, 1000);
}

function closePresensiModal() {
  clearInterval(modalTimer);
  const modal = document.getElementById('presensiSuccessModal');
  modal.style.display = 'none';
  modal.classList.remove('show');
  focusScannerInput();
}

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closePresensiModal();
  }
});

// ══ 6. DYNAMIC LIVE FEED UPDATE ══
function addLiveFeed(p) {
  const feedList = document.getElementById('liveFeedList');
  const emptyState = document.getElementById('feedEmptyState');
  if (emptyState) emptyState.remove();

  const item = document.createElement('div');
  item.className = 'live-feed-item';
  item.style.cssText = 'background:#ecfdf5; border:1px solid #a7f3d0; border-radius:10px; padding:10px 12px; display:flex; justify-content:space-between; align-items:center; animation:modalFadeIn 0.3s ease;';
  item.innerHTML = `
    <div style="display:flex; align-items:center; gap:10px;">
      <div style="width:34px; height:34px; border-radius:8px; background:#d1fae5; color:#059669; font-weight:800; display:flex; align-items:center; justify-content:center; font-size:12px;">
        <i class="bi bi-person-check-fill"></i>
      </div>
      <div>
        <div style="font-weight:800; font-size:12.5px; color:#0f172a;">${p.nama_lengkap}</div>
        <div style="font-size:11px; color:#64748b;">
          <span style="font-family:monospace; font-weight:700; color:#2563eb;">${p.no_pendaftaran}</span> &bull; ${p.jurusan_kode}
        </div>
      </div>
    </div>
    <div style="text-align:right;">
      <span style="font-size:11px; font-weight:800; background:#d1fae5; color:#065f46; padding:2px 8px; border-radius:12px; display:inline-block;">
        ${p.waktu_hadir}
      </span>
    </div>
  `;

  feedList.insertBefore(item, feedList.firstChild);
}

// ══ 7. KAMERA SCANNER (HTML5 QR CODE) ══
let html5QrCode = null;
let isCameraRunning = false;

function toggleCameraScanner() {
  const wrapper = document.getElementById('cameraScannerWrapper');
  const btn = document.getElementById('btnToggleCamera');

  if (isCameraRunning) {
    if (html5QrCode) {
      html5QrCode.stop().then(() => {
        html5QrCode.clear();
        wrapper.style.display = 'none';
        isCameraRunning = false;
        btn.innerHTML = '<i class="bi bi-camera-video"></i><span>Nyalakan Pemindai Kamera</span>';
        focusScannerInput();
      }).catch(err => console.error("Gagal stop kamera", err));
    }
  } else {
    wrapper.style.display = 'block';
    btn.innerHTML = '<i class="bi bi-camera-video-off"></i><span>Matikan Kamera</span>';
    
    html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start(
      { facingMode: "environment" },
      {
        fps: 10,
        qrbox: { width: 250, height: 250 }
      },
      (decodedText, decodedResult) => {
        // Didapat QR code
        processScan(decodedText, 'kamera_qr');
      },
      (errorMessage) => {
        // parse error, ignore
      }
    ).then(() => {
      isCameraRunning = true;
    }).catch(err => {
      alert("Tidak dapat mengakses kamera: " + err);
      wrapper.style.display = 'none';
      btn.innerHTML = '<i class="bi bi-camera-video"></i><span>Nyalakan Pemindai Kamera</span>';
      isCameraRunning = false;
    });
  }
}

// ══ 8. FILTER PENCARIAN TABEL PESERTA ══
function filterTabelPeserta() {
  const input = document.getElementById('filterTableSearch').value.toLowerCase();
  const rows = document.querySelectorAll('#tabelPesertaPresensi tbody tr.row-peserta');
  rows.forEach(r => {
    const searchData = r.getAttribute('data-search') || '';
    if (searchData.includes(input)) {
      r.style.display = '';
    } else {
      r.style.display = 'none';
    }
  });
}
</script>

</body>
</html>
