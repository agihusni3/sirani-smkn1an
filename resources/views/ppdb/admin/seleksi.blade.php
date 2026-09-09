<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Seleksi CBT & Wawancara PPDB 2026 — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/admin-ppdb.css') }}?v={{ filemtime(public_path('css/admin-ppdb.css')) }}">
  <style>
    .seleksi-hero-bar {
      background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
      border-radius: 16px;
      padding: 24px;
      color: #ffffff;
      margin-bottom: 24px;
      box-shadow: 0 10px 25px rgba(30, 27, 75, 0.2);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
      border: 1px solid rgba(255, 255, 255, 0.12);
    }
    .seleksi-tabs {
      display: flex;
      gap: 8px;
      border-bottom: 2px solid var(--border);
      margin-bottom: 24px;
      overflow-x: auto;
      padding-bottom: 2px;
    }
    .seleksi-tab-link {
      padding: 10px 18px;
      font-size: 13px;
      font-weight: 800;
      color: var(--text-2);
      text-decoration: none;
      border-radius: 8px 8px 0 0;
      border-bottom: 3px solid transparent;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      white-space: nowrap;
      transition: all 0.15s;
    }
    .seleksi-tab-link:hover {
      color: #2563eb;
      background: rgba(37,99,235,0.04);
    }
    .seleksi-tab-link.active {
      color: #2563eb;
      border-bottom-color: #2563eb;
      background: #ffffff;
    }
    .tab-content-pane {
      display: none;
    }
    .tab-content-pane.active {
      display: block;
    }
    .stat-pill {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 14px 18px;
      display: flex;
      align-items: center;
      gap: 14px;
    }
    .key-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(85px, 1fr));
      gap: 8px;
    }
    .key-box {
      background: #f8fafc;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      padding: 8px;
      text-align: center;
    }
    .key-box select {
      width: 100%;
      border-radius: 6px;
      font-weight: 800;
      font-family: monospace;
      font-size: 14px;
      text-align: center;
      padding: 4px;
      border: 1.5px solid #94a3b8;
      background: #ffffff;
    }
  </style>
</head>
<body>
<div class="app-container">
  @include('partials.sidebar_ppdb')
  
  <main class="main-content">
    
    {{-- HERO HEADER --}}
    <div class="seleksi-hero-bar no-print">
      <div>
        <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(56,189,248,0.18); color:#7dd3fc; font-size:11px; font-weight:800; padding:3px 10px; border-radius:20px; margin-bottom:8px; border:1px solid rgba(56,189,248,0.35);">
          <i class="bi bi-laptop"></i> MODUL CBT &amp; WAWANCARA PPDB 2026
        </div>
        <h1 style="margin:0 0 6px; font-size:22px; font-weight:900; letter-spacing:-0.02em;">
          Seleksi Terpadu Masuk Calon Siswa Baru
        </h1>
        <p style="margin:0; font-size:13px; color:#c7d2fe; max-width:680px;">
          Kelola naskah soal PDF, kunci jawaban pilihan ganda, penjadwalan sesi ruang lab, koreksi esai, penilaian wawancara kejuruan, dan kalkulasi ranking kelulusan.
        </p>
      </div>

      <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
        <button type="button" class="btn btn-sm" onclick="document.getElementById('modalKalkulasi').style.display = 'flex'" style="background:#10b981; color:#ffffff; font-weight:900; border-radius:8px; font-size:12px; padding:10px 18px; border:none; display:inline-flex; align-items:center; gap:6px; box-shadow:0 4px 12px rgba(16,185,129,0.3); cursor:pointer;">
          <i class="bi bi-calculator-fill"></i> Kalkulasi &amp; Ranking Kelulusan
        </button>
      </div>
    </div>

    @if(session('success'))
      <div style="background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-size:13px; font-weight:700; display:flex; align-items:center; gap:10px;">
        <i class="bi bi-check-circle-fill text-success" style="font-size:16px;"></i>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    @if(session('warning'))
      <div style="background:#fffbeb; border:1px solid #fde68a; color:#b45309; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-size:13px; font-weight:700; display:flex; align-items:center; gap:10px;">
        <i class="bi bi-exclamation-circle-fill text-warning" style="font-size:16px;"></i>
        <span>{{ session('warning') }}</span>
      </div>
    @endif

    @if(session('error'))
      <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-size:13px; font-weight:700; display:flex; align-items:center; gap:10px;">
        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:16px;"></i>
        <span>{{ session('error') }}</span>
      </div>
    @endif

    {{-- STATS BAR --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:12px; margin-bottom:24px;">
      <div class="stat-pill">
        <div style="width:42px; height:42px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:18px;">
          <i class="bi bi-people-fill"></i>
        </div>
        <div>
          <div style="font-size:11px; font-weight:800; color:var(--text-3); text-transform:uppercase;">Peserta Berkas Valid</div>
          <div style="font-size:20px; font-weight:900; color:var(--text-1);">{{ $stats['total_valid'] ?? 0 }}</div>
        </div>
      </div>

      <div class="stat-pill">
        <div style="width:42px; height:42px; border-radius:10px; background:#f0fdf4; color:#16a34a; display:flex; align-items:center; justify-content:center; font-size:18px;">
          <i class="bi bi-calendar2-check-fill"></i>
        </div>
        <div>
          <div style="font-size:11px; font-weight:800; color:var(--text-3); text-transform:uppercase;">Terjadwal Ujian</div>
          <div style="font-size:20px; font-weight:900; color:var(--text-1);">{{ $stats['sudah_jadwal'] ?? 0 }}</div>
        </div>
      </div>

      <div class="stat-pill">
        <div style="width:42px; height:42px; border-radius:10px; background:#fdf4ff; color:#c026d3; display:flex; align-items:center; justify-content:center; font-size:18px;">
          <i class="bi bi-laptop-fill"></i>
        </div>
        <div>
          <div style="font-size:11px; font-weight:800; color:var(--text-3); text-transform:uppercase;">Selesai Tes CBT</div>
          <div style="font-size:20px; font-weight:900; color:var(--text-1);">{{ $stats['sudah_pg'] ?? 0 }}</div>
        </div>
      </div>

      <div class="stat-pill">
        <div style="width:42px; height:42px; border-radius:10px; background:#fffbeb; color:#d97706; display:flex; align-items:center; justify-content:center; font-size:18px;">
          <i class="bi bi-pencil-square"></i>
        </div>
        <div>
          <div style="font-size:11px; font-weight:800; color:var(--text-3); text-transform:uppercase;">Perlu Koreksi Esai</div>
          <div style="font-size:20px; font-weight:900; color:#d97706;">{{ $stats['menunggu_esai'] ?? 0 }}</div>
        </div>
      </div>

      <div class="stat-pill">
        <div style="width:42px; height:42px; border-radius:10px; background:#f0f9ff; color:#0284c7; display:flex; align-items:center; justify-content:center; font-size:18px;">
          <i class="bi bi-chat-quote-fill"></i>
        </div>
        <div>
          <div style="font-size:11px; font-weight:800; color:var(--text-3); text-transform:uppercase;">Selesai Wawancara</div>
          <div style="font-size:20px; font-weight:900; color:var(--text-1);">{{ $stats['sudah_wawancara'] ?? 0 }}</div>
        </div>
      </div>
    </div>

    {{-- TAB NAVIGATION --}}
    @php
      $curTab = request('tab', $tabAktif ?? 'pengaturan');
    @endphp
    <div class="seleksi-tabs no-print">
      <a href="javascript:void(0)" class="seleksi-tab-link {{ $curTab === 'pengaturan' ? 'active' : '' }}" onclick="switchTab('tab-pengaturan', this)">
        1. Pengaturan &amp; Bank Soal
      </a>
      <a href="javascript:void(0)" class="seleksi-tab-link {{ $curTab === 'penjadwalan' ? 'active' : '' }}" onclick="switchTab('tab-jadwal', this)">
        2. Penjadwalan Peserta &amp; Ruang
      </a>
      <a href="javascript:void(0)" class="seleksi-tab-link {{ $curTab === 'tertulis' ? 'active' : '' }}" onclick="switchTab('tab-koreksi', this)">
        3. Koreksi Esai &amp; CBT
      </a>
      <a href="javascript:void(0)" class="seleksi-tab-link {{ $curTab === 'wawancara' ? 'active' : '' }}" onclick="switchTab('tab-wawancara', this)">
        4. Penilaian Wawancara
      </a>
      <a href="javascript:void(0)" class="seleksi-tab-link {{ $curTab === 'leaderboard' ? 'active' : '' }}" onclick="switchTab('tab-leaderboard', this)">
        5. Peringkat &amp; Hasil Akhir
      </a>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 1: PENGATURAN & BANK SOAL CBT --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div id="tab-pengaturan" class="tab-content-pane {{ $curTab === 'pengaturan' ? 'active' : '' }}">
      
      {{-- KARTU BANK SOAL CBT --}}
      <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px 24px; margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 16px;">
          <div>
            <h3 style="font-size: 16px; font-weight: 800; margin: 0 0 4px; color: #0f172a;">
              Bank Soal Ujian CBT PPDB
            </h3>
            <div style="font-size: 13px; color: #64748b;">
              Soal diinput satu persatu ke dalam sistem. Saat siswa mengerjakan ujian, urutan soal akan teracak otomatis per peserta untuk meminimalisir pencontekan.
            </div>
          </div>

          <div style="display: flex; gap: 10px; align-items: center;">
            <a href="{{ route('admin.ppdb.soal.index', $setting->id) }}" class="btn" style="background: #2563eb; color: #ffffff; font-weight: 700; padding: 9px 18px; border-radius: 8px; text-decoration: none; font-size: 13px;">
              Kelola Bank Soal ({{ $setting->soals()->count() }} Soal)
            </a>
            <a href="{{ route('admin.ppdb.soal.create', $setting->id) }}" class="btn" style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #1e293b; font-weight: 700; padding: 9px 16px; border-radius: 8px; text-decoration: none; font-size: 13px;">
              + Tambah Soal
            </a>
          </div>
        </div>

        {{-- Ringkasan Soal --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; background: #f8fafc; border-radius: 10px; padding: 14px 18px; border: 1px solid #e2e8f0;">
          <div>
            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Soal Pilihan Ganda (PG)</div>
            <div style="font-size: 20px; font-weight: 800; color: #2563eb; margin-top: 2px;">{{ $setting->soalPg()->count() }} butir</div>
            <div style="font-size: 11.5px; color: #64748b;">Koreksi otomatis berdasarkan kunci tiap soal</div>
          </div>
          <div>
            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Soal Esai / Uraian</div>
            <div style="font-size: 20px; font-weight: 800; color: #059669; margin-top: 2px;">{{ $setting->soalEsai()->count() }} butir</div>
            <div style="font-size: 11.5px; color: #64748b;">Koreksi manual di tab Koreksi Esai</div>
          </div>
          <div>
            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Anti-Contek (Random Soal)</div>
            <div style="font-size: 14px; font-weight: 700; color: #059669; margin-top: 6px;">Aktif (Diacak per Siswa)</div>
            <div style="font-size: 11.5px; color: #64748b;">Urutan soal berbeda untuk setiap siswa</div>
          </div>
        </div>
      </div>

      {{-- FORM PENGATURAN PARAMETER CBT --}}
      <form action="{{ route('admin.ppdb.seleksi.setting') }}" method="POST">
        @csrf

        <div style="background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:24px; margin-bottom:24px;">
          <h3 style="font-size:16px; font-weight:800; margin:0 0 16px; color:var(--text-1);">
            Parameter Sesi Ujian CBT
          </h3>

          <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:18px; margin-bottom:20px;">
            <div>
              <label style="display:block; font-size:12px; font-weight:700; color:var(--text-2); margin-bottom:6px;">Judul Ujian:</label>
              <input type="text" name="judul_ujian" class="form-control" value="{{ old('judul_ujian', $setting->judul_ujian ?? 'Tes Potensi Akademik & Minat Bakat PPDB 2026') }}" required style="font-weight:700;">
            </div>

            <div>
              <label style="display:block; font-size:12px; font-weight:700; color:var(--text-2); margin-bottom:6px;">Durasi Pengerjaan (Menit):</label>
              <input type="number" name="durasi_menit" class="form-control" value="{{ old('durasi_menit', $setting->durasi_menit ?? 60) }}" min="15" max="180" required style="font-weight:800; font-family:monospace;">
            </div>

            <div>
              <label style="display:block; font-size:12px; font-weight:700; color:var(--text-2); margin-bottom:6px;">Status Akses Ujian Peserta:</label>
              <div style="display:flex; align-items:center; gap:10px; margin-top:8px;">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ !empty($setting->is_active) ? 'checked' : '' }} style="width:20px; height:20px; accent-color:#2563eb; cursor:pointer;">
                <label for="is_active" style="font-weight:700; font-size:13px; color:var(--text-1); cursor:pointer;">
                  Aktifkan Sesi Ujian (Peserta dapat login &amp; mulai tes)
                </label>
              </div>
            </div>
          </div>

          <!-- JADWAL RESMI JUKNIS (JAM MULAI - SELESAI) -->
          <div style="background:linear-gradient(135deg, rgba(37,99,235,0.05), rgba(79,70,229,0.02)); border:1.5px solid #bfdbfe; border-radius:12px; padding:18px; margin-bottom:20px;">
            <div style="font-size:13px; font-weight:800; color:#1e40af; margin-bottom:14px; display:flex; align-items:center; gap:8px;">
              <i class="bi bi-calendar-check-fill text-primary"></i> Jadwal Resmi Pelaksanaan Ujian (1x Gelombang Sesuai Juknis)
            </div>
            
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
              <div>
                <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:5px;">Tanggal Pelaksanaan Ujian:</label>
                <input type="date" name="tanggal_pelaksanaan" class="form-control" value="{{ old('tanggal_pelaksanaan', $setting->tanggal_pelaksanaan ? $setting->tanggal_pelaksanaan->format('Y-m-d') : now()->format('Y-m-d')) }}" style="font-weight:700;">
                <div style="font-size:11px; color:#64748b; margin-top:3px;">Tanggal resmi yang tercetak di kartu peserta</div>
              </div>

              <div>
                <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:5px;">Jam Mulai:</label>
                <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai', $setting->jam_mulai ?? '08:00') }}" required style="font-weight:700;">
                <div style="font-size:11px; color:#64748b; margin-top:3px;">Contoh: 08:00 WIB</div>
              </div>

              <div>
                <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:5px;">Jam Selesai:</label>
                <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai', $setting->jam_selesai ?? '10:00') }}" required style="font-weight:700;">
                <div style="font-size:11px; color:#64748b; margin-top:3px;">Contoh: 10:00 WIB</div>
              </div>

              <div>
                <label style="display:block; font-size:11.5px; font-weight:700; color:#475569; margin-bottom:5px;">Ruang Ujian Default:</label>
                <input type="text" name="ruang_default" class="form-control" value="{{ old('ruang_default', $setting->ruang_default ?? 'Lab Komputer SMKN 1 Air Naningan') }}" style="font-weight:700;">
                <div style="font-size:11px; color:#64748b; margin-top:3px;">Lokasi ujian CBT &amp; pos presensi barcode</div>
              </div>
            </div>

            <div style="margin-top:14px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:10px 14px; display:flex; align-items:center; gap:8px;">
              <i class="bi bi-clock text-primary" style="font-size:16px;"></i>
              <span style="font-size:12px; color:#1e40af;">Waktu Ujian Terjadwal: <strong>{{ $setting->waktu_pelaksanaan }}</strong> (Otomatis tercetak pada kartu peserta &amp; portal pengumuman)</span>
            </div>
          </div>

          <!-- Bobot Nilai -->
          <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:18px; margin-bottom:20px; padding:16px; background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0;">
            <div>
              <label style="display:block; font-size:12px; font-weight:700; color:var(--text-2); margin-bottom:6px;">Bobot Pilihan Ganda (%):</label>
              <input type="number" name="bobot_pg" class="form-control" value="{{ old('bobot_pg', $setting->bobot_pg ?? 70) }}" min="10" max="90" required style="font-weight:800;">
              <div style="font-size:11px; color:#64748b; margin-top:4px;">Persentase nilai PG dalam nilai tertulis</div>
            </div>

            <div>
              <label style="display:block; font-size:12px; font-weight:700; color:var(--text-2); margin-bottom:6px;">Bobot Soal Esai (%):</label>
              <input type="number" name="bobot_esai" class="form-control" value="{{ old('bobot_esai', $setting->bobot_esai ?? 30) }}" min="10" max="90" required style="font-weight:800;">
              <div style="font-size:11px; color:#64748b; margin-top:4px;">Persentase nilai Esai dalam nilai tertulis</div>
            </div>
          </div>

          <div style="margin-bottom:24px;">
            <label style="display:block; font-size:12px; font-weight:700; color:var(--text-2); margin-bottom:6px;">Petunjuk Pengerjaan Ujian:</label>
            <textarea name="petunjuk_ujian" class="form-control" rows="2" style="font-size:13px;">{{ old('petunjuk_ujian', $setting->petunjuk_ujian ?? '') }}</textarea>
          </div>

          <div style="margin-top:20px; text-align:right;">
            <button type="submit" class="btn" style="background:#2563eb; color:#ffffff; font-weight:700; padding:10px 24px; border-radius:8px; border:none; cursor:pointer; font-size:13px;">
              Simpan Pengaturan Ujian
            </button>
          </div>

        </div>
      </form>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 2: PENJADWALAN PESERTA & RUANG LAB --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div id="tab-jadwal" class="tab-content-pane {{ $curTab === 'penjadwalan' ? 'active' : '' }}">
      
      {{-- Banner Quick Action: Tetapkan Jadwal Serentak (1x Gelombang Juknis) --}}
      <div style="background:#f0fdf4; border:1.5px solid #86efac; border-radius:12px; padding:16px 20px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
        <div>
          <div style="font-size:13.5px; font-weight:900; color:#15803d; display:flex; align-items:center; gap:8px;">
            <i class="bi bi-patch-check-fill text-emerald-600"></i> Penetapan Jadwal Ujian Serentak (1x Gelombang Juknis)
          </div>
          <div style="font-size:12px; color:#166534; margin-top:2px;">
            Tanggal: <strong>{{ $setting->tanggal_pelaksanaan ? \Carbon\Carbon::parse($setting->tanggal_pelaksanaan)->translatedFormat('l, d F Y') : \Carbon\Carbon::today()->translatedFormat('l, d F Y') }}</strong> &bull; 
            Waktu: <strong>{{ $setting->waktu_pelaksanaan }}</strong> &bull; Ruang: <strong>{{ $setting->ruang_default ?? 'Lab Komputer SMKN 1' }}</strong>
          </div>
        </div>

        <form action="{{ route('admin.ppdb.seleksi.jadwalkan_serentak') }}" method="POST" onsubmit="return confirm('Tetapkan jadwal ujian serentak resmi ({{ $setting->waktu_pelaksanaan }}) untuk seluruh calon siswa terverifikasi?')">
          @csrf
          <button type="submit" class="btn" style="background:#15803d; color:#ffffff; font-weight:800; font-size:12.5px; border-radius:8px; padding:9px 18px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:8px; box-shadow:0 2px 6px rgba(21,128,61,0.2);">
            <i class="bi bi-lightning-charge-fill text-yellow-300"></i>
            <span>Tetapkan Serentak Semua Peserta</span>
          </button>
        </form>
      </div>

      <form action="{{ route('admin.ppdb.seleksi.jadwalkan') }}" method="POST">
        @csrf

        {{-- Toolbar Penjadwalan Massal Tercentang --}}
        <div style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:12px; padding:18px 20px; margin-bottom:20px; display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
          <div style="font-weight:900; font-size:13px; color:var(--text-1); display:flex; align-items:center; gap:6px;">
            <i class="bi bi-check2-square text-primary" style="font-size:18px;"></i> Jadwalkan Tercentang:
          </div>

          <div style="display:flex; align-items:center; gap:8px;">
            <label style="font-size:12px; font-weight:700;">Tanggal:</label>
            <input type="date" name="jadwal_tes_tanggal" class="form-control form-control-sm" value="{{ $setting->tanggal_pelaksanaan ? $setting->tanggal_pelaksanaan->format('Y-m-d') : now()->format('Y-m-d') }}" required style="width:145px; font-weight:700;">
          </div>

          <div style="display:flex; align-items:center; gap:8px;">
            <label style="font-size:12px; font-weight:700;">Waktu / Jam:</label>
            <input type="text" name="jadwal_tes_sesi" class="form-control form-control-sm" value="{{ $setting->waktu_pelaksanaan }}" required placeholder="08.00 - 10.00 WIB" style="width:160px; font-weight:700;">
          </div>

          <div style="display:flex; align-items:center; gap:8px;">
            <label style="font-size:12px; font-weight:700;">Ruangan:</label>
            <input type="text" name="jadwal_tes_ruang" class="form-control form-control-sm" value="{{ $setting->ruang_default ?? 'Lab Komputer SMKN 1 Air Naningan' }}" required style="width:180px; font-weight:700;">
          </div>

          <button type="submit" class="btn btn-sm" style="background:#2563eb; color:#ffffff; font-weight:800; border-radius:6px; border:none; padding:7px 18px; cursor:pointer;">
            <i class="bi bi-calendar-plus-fill me-1"></i> Simpan Pilihan
          </button>
        </div>

        {{-- Tabel Peserta Terverifikasi --}}
        <div style="background:var(--surface); border:1px solid var(--border); border-radius:12px; overflow:hidden;">
          <table class="table" style="width:100%; margin:0; border-collapse:collapse; font-size:12.5px;">
            <thead style="background:#f1f5f9; border-bottom:1px solid #cbd5e1; font-weight:800; color:var(--text-2);">
              <tr>
                <th style="padding:12px 16px; width:40px; text-align:center;">
                  <input type="checkbox" id="checkAllPeserta" onclick="toggleAllCheckboxes(this)">
                </th>
                <th style="padding:12px 16px;">No. Registrasi &amp; Calon Siswa</th>
                <th style="padding:12px 16px;">Pilihan Jurusan</th>
                <th style="padding:12px 16px;">Jadwal Tanggal &amp; Waktu</th>
                <th style="padding:12px 16px;">Ruang Lab</th>
                <th style="padding:12px 16px; text-align:center;">Kartu Ujian</th>
              </tr>
            </thead>
            <tbody>
              @forelse($pesertaUjian as $peserta)
                <tr style="border-bottom:1px solid var(--border);">
                  <td style="padding:12px 16px; text-align:center;">
                    <input type="checkbox" name="pendaftar_ids[]" value="{{ $peserta->id }}" class="peserta-cb">
                  </td>
                  <td style="padding:12px 16px;">
                    <strong style="color:var(--text-1); font-size:13px; display:block;">{{ $peserta->nama_lengkap }}</strong>
                    <span style="font-family:monospace; color:#2563eb; font-weight:700;">{{ $peserta->no_pendaftaran }}</span> • NISN: {{ $peserta->nisn }}
                  </td>
                  <td style="padding:12px 16px;">
                    <span style="font-weight:700; color:var(--text-1);">{{ $peserta->jurusan1->nama_jurusan ?? ($peserta->jurusanPilihan1->nama_jurusan ?? '-') }}</span>
                  </td>
                  <td style="padding:12px 16px;">
                    @if($peserta->jadwal_tes_tanggal)
                      <div style="font-weight:700; color:#0f172a; display:flex; align-items:center; gap:6px;">
                        <i class="bi bi-calendar-event text-primary"></i>
                        {{ \Carbon\Carbon::parse($peserta->jadwal_tes_tanggal)->translatedFormat('d M Y') }}
                      </div>
                      <div style="margin-top:4px; display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                        <span style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:11px; font-weight:700; padding:2px 8px; border-radius:4px;">
                          <i class="bi bi-clock me-1"></i> {{ $peserta->jadwal_sesi_resmi }}
                        </span>
                        <button type="button" class="btn btn-sm" 
                          onclick="bukaModalEditSesi('{{ $peserta->id }}', '{{ addslashes($peserta->nama_lengkap) }}', '{{ $peserta->jadwal_tes_tanggal ? \Carbon\Carbon::parse($peserta->jadwal_tes_tanggal)->format('Y-m-d') : '' }}', '{{ addslashes($peserta->jadwal_sesi_resmi ?? '') }}', '{{ addslashes($peserta->jadwal_tes_ruang ?? '') }}')" 
                          title="Ubah Jadwal Siswa Ini" 
                          style="background:#f1f5f9; border:1px solid #cbd5e1; color:#475569; padding:2px 6px; font-size:10.5px; font-weight:700; border-radius:4px; cursor:pointer;">
                          <i class="bi bi-pencil-square"></i> Ubah
                        </button>
                      </div>
                    @else
                      <span style="color:#ef4444; font-weight:700; font-size:11px;"><i class="bi bi-clock"></i> Belum Dijadwalkan</span>
                    @endif
                  </td>
                  <td style="padding:12px 16px;">
                    <span style="font-weight:700; background:#f1f5f9; padding:3px 8px; border-radius:6px;">
                      {{ $peserta->jadwal_tes_ruang ?? '-' }}
                    </span>
                  </td>
                  <td style="padding:12px 16px; text-align:center;">
                    <a href="{{ route('ppdb.cetak', $peserta->no_pendaftaran) }}" target="_blank" class="btn btn-sm" style="background:#ffffff; border:1px solid #cbd5e1; color:#334155; font-size:11.5px; font-weight:700; padding:4px 10px; border-radius:6px; text-decoration:none;">
                      <i class="bi bi-printer me-1"></i> Cetak Kartu
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" style="padding:30px; text-align:center; color:var(--text-3);">
                    Belum ada calon peserta dengan status berkas valid/terverifikasi.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </form>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 3: KOREKSI ESAI & NILAI CBT TERTULIS --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div id="tab-koreksi" class="tab-content-pane {{ $curTab === 'tertulis' ? 'active' : '' }}">
      <div style="background:var(--surface); border:1px solid var(--border); border-radius:12px; overflow:hidden;">
        <div style="padding:16px 20px; background:#f8fafc; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
          <div>
            <h3 style="font-size:15px; font-weight:900; margin:0; color:var(--text-1);">Daftar Pengerjaan Tes CBT Tertulis</h3>
            <div style="font-size:12px; color:var(--text-3);">Koreksi dan berikan skor pada 5 butir soal esai siswa di bawah ini</div>
          </div>
        </div>

        <table class="table" style="width:100%; margin:0; border-collapse:collapse; font-size:12.5px;">
          <thead style="background:#f1f5f9; border-bottom:1px solid #cbd5e1; font-weight:800; color:var(--text-2);">
            <tr>
              <th style="padding:12px 16px;">Peserta CBT</th>
              <th style="padding:12px 16px; text-align:center;">Status CBT</th>
              <th style="padding:12px 16px; text-align:center;">Benar / Salah (PG)</th>
              <th style="padding:12px 16px; text-align:center;">Nilai PG</th>
              <th style="padding:12px 16px; text-align:center;">Nilai Esai</th>
              <th style="padding:12px 16px; text-align:center;">Total Tertulis</th>
              <th style="padding:12px 16px; text-align:center;">Aksi Koreksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pesertaUjian as $p)
              @php
                $u = $p->ujianPeserta;
              @endphp
              <tr style="border-bottom:1px solid var(--border);">
                <td style="padding:12px 16px;">
                  <strong style="color:var(--text-1); font-size:13px; display:block;">{{ $p->nama_lengkap }}</strong>
                  <span style="font-family:monospace; color:#2563eb; font-weight:700;">{{ $p->no_pendaftaran }}</span> • {{ $p->jurusan1->nama_jurusan ?? ($p->jurusanPilihan1->nama_jurusan ?? '-') }}
                </td>
                <td style="padding:12px 16px; text-align:center;">
                  @if($u)
                    @if($u->status_pengerjaan === 'selesai_dinilai')
                      <span style="background:#ecfdf5; color:#059669; font-weight:800; font-size:11px; padding:3px 8px; border-radius:6px; border:1px solid #a7f3d0;">
                        <i class="bi bi-check2-circle"></i> Selesai Dinilai
                      </span>
                    @elseif($u->status_pengerjaan === 'selesai_menunggu_koreksi' || $u->status_pengerjaan === 'selesai')
                      <span style="background:#fef3c7; color:#d97706; font-weight:800; font-size:11px; padding:3px 8px; border-radius:6px; border:1px solid #fde68a;">
                        <i class="bi bi-clock-history"></i> Butuh Koreksi Esai
                      </span>
                    @elseif($u->status_pengerjaan === 'sedang_mengerjakan')
                      <span style="background:#eff6ff; color:#2563eb; font-weight:800; font-size:11px; padding:3px 8px; border-radius:6px;">
                        Sedang Mengerjakan
                      </span>
                    @else
                      <span style="background:#f1f5f9; color:#64748b; font-weight:700; font-size:11px; padding:3px 8px; border-radius:6px;">
                        {{ ucfirst($u->status_pengerjaan) }}
                      </span>
                    @endif
                  @else
                    <span style="color:#94a3b8; font-size:11px; font-weight:700;">Belum Mengikuti</span>
                  @endif
                </td>
                <td style="padding:12px 16px; text-align:center; font-family:monospace; font-weight:800;">
                  @if($u)
                    <span style="color:#059669;">{{ $u->jumlah_pg_benar ?? 0 }}B</span> / 
                    <span style="color:#dc2626;">{{ $u->jumlah_pg_salah ?? 0 }}S</span>
                  @else
                    -
                  @endif
                </td>
                <td style="padding:12px 16px; text-align:center; font-family:monospace; font-weight:800; color:#2563eb;">
                  {{ $u && $u->nilai_pg !== null ? number_format($u->nilai_pg, 1) : '-' }}
                </td>
                <td style="padding:12px 16px; text-align:center; font-family:monospace; font-weight:800; color:#059669;">
                  {{ $u && $u->nilai_esai !== null ? number_format($u->nilai_esai, 1) : '-' }}
                </td>
                <td style="padding:12px 16px; text-align:center; font-family:monospace; font-weight:900; font-size:13.5px; color:#000000;">
                  {{ $p->nilai_tes_tertulis !== null ? number_format($p->nilai_tes_tertulis, 1) : '-' }}
                </td>
                <td style="padding:12px 16px; text-align:center;">
                  @if($u)
                    <button type="button" class="btn btn-sm" onclick="bukaModalKoreksi({{ json_encode($u) }}, {{ json_encode($p) }})" style="background:#4338ca; color:#ffffff; font-weight:800; font-size:11.5px; border-radius:6px; border:none; padding:5px 12px; cursor:pointer;">
                      <i class="bi bi-pencil-square me-1"></i> Koreksi Esai
                    </button>
                  @else
                    <button type="button" disabled class="btn btn-sm" style="background:#e2e8f0; color:#94a3b8; font-size:11px; border:none; border-radius:6px; padding:4px 10px;">
                      Belum Ada Jawaban
                    </button>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" style="padding:30px; text-align:center; color:var(--text-3);">
                  Belum ada peserta yang mengumpulkan lembar jawaban CBT.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 4: PENILAIAN WAWANCARA KEJURUAN --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div id="tab-wawancara" class="tab-content-pane {{ $curTab === 'wawancara' ? 'active' : '' }}">
      <div style="background:var(--surface); border:1px solid var(--border); border-radius:12px; overflow:hidden;">
        <div style="padding:16px 20px; background:#f8fafc; border-bottom:1px solid var(--border);">
          <h3 style="font-size:15px; font-weight:900; margin:0; color:var(--text-1);">Penilaian Wawancara, Fisik &amp; Minat Kejuruan</h3>
          <div style="font-size:12px; color:var(--text-3);">Input skor wawancara motivasi, kesiapan kejuruan, dan komitmen orang tua calon siswa</div>
        </div>

        <table class="table" style="width:100%; margin:0; border-collapse:collapse; font-size:12.5px;">
          <thead style="background:#f1f5f9; border-bottom:1px solid #cbd5e1; font-weight:800; color:var(--text-2);">
            <tr>
              <th style="padding:12px 16px;">Peserta Seleksi</th>
              <th style="padding:12px 16px;">Pilihan Kejuruan</th>
              <th style="padding:12px 16px; text-align:center;">Status Wawancara</th>
              <th style="padding:12px 16px; text-align:center;">Skor Wawancara</th>
              <th style="padding:12px 16px;">Pewawancara</th>
              <th style="padding:12px 16px; text-align:center;">Aksi Penilaian</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pesertaUjian as $p)
              <tr style="border-bottom:1px solid var(--border);">
                <td style="padding:12px 16px;">
                  <strong style="color:var(--text-1); font-size:13px; display:block;">{{ $p->nama_lengkap }}</strong>
                  <span style="font-family:monospace; color:#2563eb; font-weight:700;">{{ $p->no_pendaftaran }}</span>
                </td>
                <td style="padding:12px 16px;">
                  <span style="font-weight:700; color:var(--text-1);">{{ $p->jurusan1->nama_jurusan ?? ($p->jurusanPilihan1->nama_jurusan ?? '-') }}</span>
                </td>
                <td style="padding:12px 16px; text-align:center;">
                  @if($p->nilai_wawancara_total !== null)
                    <span style="background:#ecfdf5; color:#059669; font-weight:800; font-size:11px; padding:3px 8px; border-radius:6px; border:1px solid #a7f3d0;">
                      <i class="bi bi-check-circle-fill"></i> Sudah Dinilai
                    </span>
                  @else
                    <span style="background:#f1f5f9; color:#64748b; font-weight:700; font-size:11px; padding:3px 8px; border-radius:6px;">
                      Belum Wawancara
                    </span>
                  @endif
                </td>
                <td style="padding:12px 16px; text-align:center; font-family:monospace; font-weight:900; font-size:14px; color:#059669;">
                  {{ $p->nilai_wawancara_total !== null ? number_format($p->nilai_wawancara_total, 1) : '-' }}
                </td>
                <td style="padding:12px 16px; color:#475569; font-size:11.5px;">
                  {{ $p->pewawancara->name ?? '-' }}
                </td>
                <td style="padding:12px 16px; text-align:center;">
                  <button type="button" class="btn btn-sm" onclick="bukaModalWawancara({{ json_encode($p) }})" style="background:#0284c7; color:#ffffff; font-weight:800; font-size:11.5px; border-radius:6px; border:none; padding:5px 12px; cursor:pointer;">
                    <i class="bi bi-mic-fill me-1"></i> Form Wawancara
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="padding:30px; text-align:center; color:var(--text-3);">
                  Belum ada peserta di daftar sesi ujian / wawancara.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 5: PERINGKAT & LEADERBOARD HASIL AKHIR --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div id="tab-leaderboard" class="tab-content-pane {{ $curTab === 'leaderboard' ? 'active' : '' }}">
      <div style="background:var(--surface); border:1px solid var(--border); border-radius:12px; overflow:hidden;">
        <div style="padding:16px 20px; background:#f8fafc; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
          <div>
            <h3 style="font-size:15px; font-weight:900; margin:0; color:var(--text-1);">
              Leaderboard &amp; Peringkat Kelulusan
            </h3>
            <div style="font-size:12px; color:var(--text-3);">Kalkulasi formula: Rapor (30%) + CBT (35%) + Wawancara (35%) &bull; Diterima: {{ $stats['total_diterima'] ?? 0 }} siswa</div>
          </div>

          <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            @if(($stats['total_diterima'] ?? 0) > 0)
              <a href="{{ route('admin.ppdb.index', ['status' => 'diterima']) }}" class="btn btn-sm" style="background:#059669; color:#ffffff; font-weight:800; border-radius:6px; padding:6px 14px; font-size:12px; text-decoration:none;">
                Lanjut Mutasi Massal ({{ $stats['total_diterima'] }}) &rarr;
              </a>
            @endif
            <button type="button" class="btn btn-sm" onclick="document.getElementById('modalKalkulasi').style.display = 'flex'" style="background:#2563eb; color:#ffffff; font-weight:800; border-radius:6px; border:none; padding:6px 14px; font-size:12px; cursor:pointer;">
              Kalkulasi &amp; Ranking Otomatis
            </button>
          </div>
        </div>

        <table class="table" style="width:100%; margin:0; border-collapse:collapse; font-size:12.5px;">
          <thead style="background:#f1f5f9; border-bottom:1px solid #cbd5e1; font-weight:800; color:var(--text-2);">
            <tr>
              <th style="padding:12px 16px; text-align:center; width:50px;">Rank</th>
              <th style="padding:12px 16px;">Nama Calon Siswa</th>
              <th style="padding:12px 16px;">Pilihan 1 &amp; 2</th>
              <th style="padding:12px 16px; text-align:center;">Rapor (30%)</th>
              <th style="padding:12px 16px; text-align:center;">Tertulis (35%)</th>
              <th style="padding:12px 16px; text-align:center;">Wawancara (35%)</th>
              <th style="padding:12px 16px; text-align:center;">NILAI AKHIR</th>
              <th style="padding:12px 16px; text-align:center;">Diterima di Jurusan</th>
              <th style="padding:12px 16px; text-align:center;">Status PPDB</th>
            </tr>
          </thead>
          <tbody>
            @forelse($leaderboard as $idx => $row)
              <tr style="border-bottom:1px solid var(--border);">
                <td style="padding:12px 16px; text-align:center;">
                  @if($idx == 0)
                    <span style="display:inline-block; width:26px; height:26px; border-radius:50%; background:#fef08a; color:#854d0e; font-weight:900; line-height:26px;">1</span>
                  @elseif($idx == 1)
                    <span style="display:inline-block; width:26px; height:26px; border-radius:50%; background:#e2e8f0; color:#334155; font-weight:900; line-height:26px;">2</span>
                  @elseif($idx == 2)
                    <span style="display:inline-block; width:26px; height:26px; border-radius:50%; background:#fed7aa; color:#9a3412; font-weight:900; line-height:26px;">3</span>
                  @else
                    <span style="font-weight:700; color:var(--text-3);">{{ $row->peringkat_jurusan ?? ($idx + 1) }}</span>
                  @endif
                </td>
                <td style="padding:12px 16px;">
                  <strong style="color:var(--text-1); font-size:13px; display:block;">{{ $row->nama_lengkap }}</strong>
                  <span style="font-family:monospace; color:#2563eb; font-weight:700;">{{ $row->no_pendaftaran }}</span>
                </td>
                <td style="padding:12px 16px;">
                  <div style="font-weight:700; color:var(--text-1);">1. {{ $row->jurusanPilihan1->kode_jurusan ?? '-' }}</div>
                  @if($row->jurusanPilihan2)
                    <div style="font-size:11px; color:var(--text-3);">2. {{ $row->jurusanPilihan2->kode_jurusan }}</div>
                  @endif
                </td>
                <td style="padding:12px 16px; text-align:center; font-family:monospace; font-weight:800;">
                  {{ number_format($row->nilai_rapor ?? 0, 1) }}
                </td>
                <td style="padding:12px 16px; text-align:center; font-family:monospace; font-weight:800; color:#2563eb;">
                  {{ number_format($row->nilai_tes_tertulis ?? 0, 1) }}
                </td>
                <td style="padding:12px 16px; text-align:center; font-family:monospace; font-weight:800; color:#059669;">
                  {{ number_format($row->nilai_wawancara_total ?? 0, 1) }}
                </td>
                <td style="padding:12px 16px; text-align:center; font-family:monospace; font-weight:900; font-size:15px; color:#1e1b4b; background:#e0e7ff;">
                  {{ number_format($row->nilai_akhir ?? 0, 2) }}
                </td>
                <td style="padding:12px 16px; text-align:center;">
                  @if($row->jurusanDiterima)
                    <span style="font-weight:800; color:#15803d; background:#dcfce7; padding:3px 10px; border-radius:6px; font-size:11.5px; border:1px solid #a7f3d0;">
                      {{ $row->jurusanDiterima->nama_jurusan }}
                    </span>
                  @else
                    <span style="color:#94a3b8; font-size:12px;">—</span>
                  @endif
                </td>
                <td style="padding:12px 16px; text-align:center;">
                  @if($row->status === 'diterima')
                    <span style="background:#ecfdf5; color:#059669; font-weight:900; font-size:11px; padding:4px 10px; border-radius:6px; border:1px solid #a7f3d0;">
                      DITERIMA
                    </span>
                  @elseif($row->status === 'cadangan')
                    <span style="background:#fffbeb; color:#d97706; font-weight:900; font-size:11px; padding:4px 10px; border-radius:6px; border:1px solid #fde68a;">
                      CADANGAN
                    </span>
                  @elseif($row->status === 'ditolak')
                    <span style="background:#fef2f2; color:#dc2626; font-weight:900; font-size:11px; padding:4px 10px; border-radius:6px; border:1px solid #fecaca;">
                      TIDAK LULUS
                    </span>
                  @else
                    <span style="background:#f1f5f9; color:#64748b; font-weight:700; font-size:11px; padding:3px 8px; border-radius:6px;">
                      {{ ucfirst(str_replace('_', ' ', $row->status)) }}
                    </span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" style="padding:30px; text-align:center; color:var(--text-3);">
                  Belum ada data nilai pendaftar yang siap diperingkatkan.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL INTERAKTIF KOREKSI ESAI --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modalKoreksi" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center; padding:20px;">
  <div style="background:#ffffff; border-radius:16px; max-width:760px; width:100%; max-height:90vh; overflow-y:auto; padding:24px; box-shadow:0 20px 40px rgba(0,0,0,0.25);">
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:14px; margin-bottom:16px;">
      <div>
        <h3 style="font-size:17px; font-weight:900; margin:0; color:#000000;">Koreksi Soal Esai Peserta</h3>
        <div id="koreksiPesertaMeta" style="font-size:12px; color:#64748b;"></div>
      </div>
      <button type="button" onclick="document.getElementById('modalKoreksi').style.display='none'" style="border:none; background:none; font-size:20px; cursor:pointer; color:#94a3b8;">&times;</button>
    </div>

    <form id="formKoreksiEsai" method="POST">
      @csrf
      <div id="wadahSoalEsai" style="display:flex; flex-direction:column; gap:16px; margin-bottom:24px;"></div>

      <div style="margin-bottom:20px;">
        <label style="display:block; font-size:12px; font-weight:800; margin-bottom:4px;">Catatan Korektor / Penguji:</label>
        <textarea name="catatan_koreksi_esai" class="form-control" rows="2" placeholder="Catatan kelebihan atau kekurangan jawaban siswa"></textarea>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #e2e8f0; padding-top:14px;">
        <button type="button" onclick="document.getElementById('modalKoreksi').style.display='none'" class="btn" style="background:#f1f5f9; color:#475569; font-weight:700;">Tutup</button>
        <button type="submit" class="btn" style="background:#2563eb; color:#ffffff; font-weight:800;">Simpan Hasil Koreksi Esai</button>
      </div>
    </form>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL INTERAKTIF FORM WAWANCARA --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modalWawancara" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center; padding:20px;">
  <div style="background:#ffffff; border-radius:16px; max-width:640px; width:100%; max-height:90vh; overflow-y:auto; padding:24px; box-shadow:0 20px 40px rgba(0,0,0,0.25);">
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:14px; margin-bottom:16px;">
      <div>
        <h3 style="font-size:17px; font-weight:900; margin:0; color:#000000;">Rubrik Penilaian Wawancara Kejuruan</h3>
        <div id="wawancaraPesertaMeta" style="font-size:12px; color:#64748b;"></div>
      </div>
      <button type="button" onclick="document.getElementById('modalWawancara').style.display='none'" style="border:none; background:none; font-size:20px; cursor:pointer; color:#94a3b8;">&times;</button>
    </div>

    <form id="formWawancara" method="POST">
      @csrf
      
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:16px;">
        <div>
          <label style="display:block; font-size:12px; font-weight:800; margin-bottom:4px;">Motivasi &amp; Minat (0-100):</label>
          <input type="number" id="w_motivasi" name="nilai_wawancara_motivasi" class="form-control" min="0" max="100" required oninput="hitungTotalWawancara()">
        </div>
        <div>
          <label style="display:block; font-size:12px; font-weight:800; margin-bottom:4px;">Karakter, Sikap &amp; Disiplin (0-100):</label>
          <input type="number" id="w_karakter" name="nilai_wawancara_karakter" class="form-control" min="0" max="100" required oninput="hitungTotalWawancara()">
        </div>
        <div>
          <label style="display:block; font-size:12px; font-weight:800; margin-bottom:4px;">Kesiapan Kejuruan/Fisik (0-100):</label>
          <input type="number" id="w_kejuruan" name="nilai_wawancara_kejuruan" class="form-control" min="0" max="100" required oninput="hitungTotalWawancara()">
        </div>
        <div>
          <label style="display:block; font-size:12px; font-weight:800; margin-bottom:4px;">Dukungan Orang Tua (0-100):</label>
          <input type="number" id="w_ortu" name="nilai_wawancara_ortu" class="form-control" min="0" max="100" required oninput="hitungTotalWawancara()">
        </div>
      </div>

      <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:12px 16px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center;">
        <span style="font-size:13px; font-weight:700; color:#1e40af;">Estimasi Skor Wawancara Terbobot:</span>
        <strong id="w_preview_total" style="font-size:18px; font-weight:900; color:#1d4ed8; font-family:monospace;">0.00</strong>
      </div>

      <div style="margin-bottom:20px;">
        <label style="display:block; font-size:12px; font-weight:800; margin-bottom:4px;">Catatan Observasi Khusus:</label>
        <textarea id="w_catatan" name="catatan_wawancara" class="form-control" rows="2" placeholder="Catatan fisik (buta warna, tindik, tato), komitmen kehadiran, dll."></textarea>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #e2e8f0; padding-top:14px;">
        <button type="button" onclick="document.getElementById('modalWawancara').style.display='none'" class="btn" style="background:#f1f5f9; color:#475569; font-weight:700;">Tutup</button>
        <button type="submit" class="btn" style="background:#0284c7; color:#ffffff; font-weight:800;">Simpan Nilai Wawancara</button>
      </div>
    </form>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL KALKULASI KELULUSAN --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modalKalkulasi" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center; padding:20px;">
  <div style="background:#ffffff; border-radius:14px; max-width:500px; width:100%; padding:26px; box-shadow:0 20px 40px rgba(0,0,0,0.25);">
    <h3 style="font-size:17px; font-weight:800; margin:0 0 6px; color:#0f172a;">Kalkulasi &amp; Penetapan Kelulusan</h3>
    <p style="font-size:12.5px; color:#64748b; line-height:1.5; margin-bottom:18px;">
      Sistem akan menghitung nilai akhir terbobot (Rapor 30% + CBT 35% + Wawancara 35%), lalu mengalokasikan kelulusan sesuai kuota jurusan Pilihan 1 &amp; Pilihan 2.
    </p>

    <form action="{{ route('admin.ppdb.seleksi.kalkulasi') }}" method="POST">
      @csrf

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:18px; text-align:left;">
        <div>
          <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:4px;">Kuota per Rombel:</label>
          <input type="number" name="kuota_per_rombel" value="36" min="1" max="100" class="form-control" required style="font-weight:800;">
          <div style="font-size:11px; color:#64748b; margin-top:3px;">Standar SMK: 36 siswa/kelas</div>
        </div>

        <div>
          <label style="display:block; font-size:12px; font-weight:700; color:#1e293b; margin-bottom:4px;">Nilai Minimal Lulus:</label>
          <input type="number" name="passing_grade" value="50" min="0" max="100" step="0.5" class="form-control" required style="font-weight:800;">
          <div style="font-size:11px; color:#64748b; margin-top:3px;">Passing grade nilai akhir (0-100)</div>
        </div>
      </div>

      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; margin-bottom:20px; font-size:11.5px; color:#475569; text-align:left; line-height:1.5;">
        &bull; Siswa yang lolos kuota Pilihan 1 langsung berstatus <strong>Diterima</strong>.<br>
        &bull; Jika kuota Pilihan 1 penuh, sistem otomatis mengevaluasi ketersediaan di Pilihan 2.<br>
        &bull; Siswa yang belum tertampung kuota akan dialihkan ke status <strong>Cadangan</strong>.
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px;">
        <button type="button" onclick="document.getElementById('modalKalkulasi').style.display='none'" class="btn" style="background:#f1f5f9; color:#475569; font-weight:700; padding:8px 18px; border-radius:6px; font-size:13px; cursor:pointer;">Batal</button>
        <button type="submit" class="btn" style="background:#2563eb; color:#ffffff; font-weight:700; padding:8px 20px; border-radius:6px; font-size:13px; cursor:pointer;">
          Jalankan Kalkulasi &amp; Ranking
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function switchTab(tabId, el) {
    document.querySelectorAll('.tab-content-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.seleksi-tab-link').forEach(l => l.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    el.classList.add('active');
  }

  function toggleAllCheckboxes(source) {
    const cbs = document.querySelectorAll('.peserta-cb');
    cbs.forEach(cb => cb.checked = source.checked);
  }

  const bankSoalEsai = @json($setting->soalEsai);

  // Koreksi Esai Popup Fill
  function bukaModalKoreksi(ujian, pendaftar) {
    document.getElementById('koreksiPesertaMeta').innerText = `${pendaftar.nama_lengkap} (${pendaftar.no_pendaftaran}) — Skor PG: ${ujian.nilai_pg || 0}`;
    document.getElementById('formKoreksiEsai').action = `/admin/ppdb/seleksi/nilai-esai/${pendaftar.id}`;

    const container = document.getElementById('wadahSoalEsai');
    container.innerHTML = '';

    const esaiData = ujian.jawaban_esai || {};
    const nilaiTersimpan = ujian.nilai_per_nomor_esai || {};

    if (bankSoalEsai && bankSoalEsai.length > 0) {
      bankSoalEsai.forEach((soal, idx) => {
        const key = soal.id;
        const teksJawaban = esaiData[key] || esaiData[soal.nomor_urut] || '<span style="color:#ef4444; font-style:italic;">(Peserta tidak mengisi jawaban)</span>';
        const skorDefault = (nilaiTersimpan[key] !== undefined) ? nilaiTersimpan[key] : (nilaiTersimpan[soal.nomor_urut] !== undefined ? nilaiTersimpan[soal.nomor_urut] : 8);

        const itemHtml = `
          <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px; margin-bottom:12px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px; gap:12px;">
              <div>
                <strong style="font-size:13px; color:#0f172a;">Soal Esai #${soal.nomor_urut}</strong>
                <div style="font-size:12px; color:#475569; margin-top:2px;">${soal.pertanyaan}</div>
              </div>
              <div style="display:flex; align-items:center; gap:6px; flex-shrink:0;">
                <span style="font-size:12px; font-weight:700;">Skor (0 - 10):</span>
                <input type="number" name="nilai_esai[${key}]" min="0" max="10" step="0.5" value="${skorDefault}" required class="form-control form-control-sm" style="width:70px; font-weight:800; text-align:center;">
              </div>
            </div>
            <div style="background:#ffffff; border:1px solid #cbd5e1; border-radius:8px; padding:10px; font-size:12.5px; color:#1e293b; white-space:pre-wrap; max-height:140px; overflow-y:auto;">
              ${teksJawaban}
            </div>
          </div>
        `;
        container.insertAdjacentHTML('beforeend', itemHtml);
      });
    } else {
      for (let num = 31; num <= 35; num++) {
        const teksJawaban = esaiData[num] || '<span style="color:#ef4444; font-style:italic;">(Peserta tidak mengisi jawaban)</span>';
        const skorDefault = nilaiTersimpan[num] !== undefined ? nilaiTersimpan[num] : 8;
        
        const itemHtml = `
          <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px; margin-bottom:12px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
              <strong style="font-size:13px; color:#0f172a;">Soal Esai No. ${num}</strong>
              <div style="display:flex; align-items:center; gap:6px;">
                <span style="font-size:12px; font-weight:700;">Skor (0 - 10):</span>
                <input type="number" name="nilai_esai_${num}" min="0" max="10" step="0.5" value="${skorDefault}" required class="form-control form-control-sm" style="width:70px; font-weight:800; text-align:center;">
              </div>
            </div>
            <div style="background:#ffffff; border:1px solid #cbd5e1; border-radius:8px; padding:10px; font-size:12.5px; color:#1e293b; white-space:pre-wrap; max-height:140px; overflow-y:auto;">
              ${teksJawaban}
            </div>
          </div>
        `;
        container.insertAdjacentHTML('beforeend', itemHtml);
      }
    }

    document.getElementById('modalKoreksi').style.display = 'flex';
  }

  // Wawancara Popup Fill
  function bukaModalWawancara(pendaftar) {
    document.getElementById('wawancaraPesertaMeta').innerText = `${pendaftar.nama_lengkap} (${pendaftar.no_pendaftaran})`;
    document.getElementById('formWawancara').action = `/admin/ppdb/seleksi/nilai-wawancara/${pendaftar.id}`;
    
    document.getElementById('w_motivasi').value = pendaftar.nilai_wawancara_motivasi !== null ? pendaftar.nilai_wawancara_motivasi : 80;
    document.getElementById('w_karakter').value = pendaftar.nilai_wawancara_karakter !== null ? pendaftar.nilai_wawancara_karakter : 80;
    document.getElementById('w_kejuruan').value = pendaftar.nilai_wawancara_kejuruan !== null ? pendaftar.nilai_wawancara_kejuruan : 80;
    document.getElementById('w_ortu').value = pendaftar.nilai_wawancara_ortu !== null ? pendaftar.nilai_wawancara_ortu : 80;
    document.getElementById('w_catatan').value = pendaftar.catatan_wawancara || '';
    hitungTotalWawancara();

    document.getElementById('modalWawancara').style.display = 'flex';
  }

  function hitungTotalWawancara() {
    const m = parseFloat(document.getElementById('w_motivasi').value) || 0;
    const k = parseFloat(document.getElementById('w_karakter').value) || 0;
    const j = parseFloat(document.getElementById('w_kejuruan').value) || 0;
    const o = parseFloat(document.getElementById('w_ortu').value) || 0;

    const total = (m * 0.25) + (k * 0.25) + (j * 0.30) + (o * 0.20);
    document.getElementById('w_preview_total').innerText = total.toFixed(2);
  }

  // Manajemen Multi-Sesi Ujian Fleksibel Admin
  function tambahBarisSesi() {
    const container = document.getElementById('container-daftar-sesi');
    const count = container.querySelectorAll('.baris-sesi').length + 1;
    const namaDefault = `Sesi ${count}`;
    const waktuDefault = `14.00 - 16.00 WIB`;
    const labelDefault = `${namaDefault} (${waktuDefault})`;

    const html = `
      <div class="baris-sesi" style="display:flex; align-items:center; gap:10px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px 14px; flex-wrap:wrap;">
        <div style="flex:1; min-width:130px;">
          <label style="font-size:10.5px; font-weight:700; color:#64748b; margin-bottom:3px; display:block;">Nama Sesi:</label>
          <input type="text" name="sesi_nama[]" class="form-control form-control-sm input-sesi-nama" value="${namaDefault}" required placeholder="Contoh: Sesi ${count}" oninput="syncLabelSesi(this)" style="font-weight:700; font-size:12px;">
        </div>
        <div style="flex:1.8; min-width:170px;">
          <label style="font-size:10.5px; font-weight:700; color:#64748b; margin-bottom:3px; display:block;">Rentang Waktu:</label>
          <input type="text" name="sesi_waktu[]" class="form-control form-control-sm input-sesi-waktu" value="${waktuDefault}" required placeholder="Contoh: 08.00 - 10.00 WIB" oninput="syncLabelSesi(this)" style="font-weight:700; font-size:12px;">
        </div>
        <div style="min-width:135px; text-align:center;">
          <label style="font-size:10.5px; font-weight:700; color:#64748b; margin-bottom:3px; display:block;">Default Baru:</label>
          <div style="display:flex; align-items:center; justify-content:center; gap:6px; margin-top:4px;">
            <input type="radio" name="sesi_default" value="${labelDefault}" class="radio-sesi-default" style="cursor:pointer; accent-color:#2563eb;">
            <span style="font-size:11px; font-weight:700; color:#334155;">Jadikan Default</span>
          </div>
        </div>
        <div style="padding-top:16px;">
          <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusBarisSesi(this)" title="Hapus Sesi" style="border:none; color:#ef4444; background:transparent; font-size:16px; cursor:pointer; padding:4px 8px;">
            <i class="bi bi-trash3-fill"></i>
          </button>
        </div>
      </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
  }

  function syncLabelSesi(inputEl) {
  // Edit Jadwal Single Siswa Modal
  function bukaModalEditSesi(id, nama, tanggal, waktu, ruang) {
    document.getElementById('editSesiNamaPeserta').innerText = nama;
    document.getElementById('editSesiTanggal').value = tanggal;
    document.getElementById('editSesiInput').value = waktu || '{{ $setting->waktu_pelaksanaan }}';
    document.getElementById('editSesiRuang').value = ruang || 'Lab Komputer SMKN 1 Air Naningan';

    document.getElementById('formEditSesiSingle').action = `/admin/ppdb/seleksi/jadwalkan-single/${id}`;
    document.getElementById('modalEditSesiSingle').style.display = 'flex';
  }

  function tutupModalEditSesi() {
    document.getElementById('modalEditSesiSingle').style.display = 'none';
  }
</script>

<!-- Modal Ubah Jadwal & Waktu Calon Siswa (Single) -->
<div id="modalEditSesiSingle" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center; padding:16px;">
  <div style="background:#ffffff; border-radius:14px; width:100%; max-width:480px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); overflow:hidden;">
    <div style="padding:16px 20px; background:#0f172a; color:#ffffff; display:flex; justify-content:space-between; align-items:center;">
      <div>
        <h4 style="font-size:14px; font-weight:800; margin:0; display:flex; align-items:center; gap:6px;">
          <i class="bi bi-calendar-check text-primary"></i> Ubah Jadwal &amp; Waktu Ujian
        </h4>
        <div id="editSesiNamaPeserta" style="font-size:12px; color:#94a3b8; margin-top:2px;">Nama Calon Siswa</div>
      </div>
      <button type="button" onclick="tutupModalEditSesi()" style="background:transparent; border:none; color:#ffffff; font-size:20px; cursor:pointer; line-height:1;">&times;</button>
    </div>
    <form id="formEditSesiSingle" method="POST" style="padding:20px;">
      @csrf
      <div style="margin-bottom:14px;">
        <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">Tanggal Pelaksanaan Ujian:</label>
        <input type="date" name="jadwal_tes_tanggal" id="editSesiTanggal" class="form-control" required style="font-weight:700;">
      </div>

      <div style="margin-bottom:14px;">
        <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">Waktu / Jam Ujian (Mulai - Selesai):</label>
        <input type="text" name="jadwal_tes_sesi" id="editSesiInput" class="form-control" required style="font-weight:700;" placeholder="Contoh: 08.00 - 10.00 WIB">
        <div style="font-size:11px; color:#64748b; margin-top:3px;">Format: Jam Mulai - Selesai (contoh: 08.00 - 10.00 WIB)</div>
      </div>

      <div style="margin-bottom:20px;">
        <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">Ruang Ujian / Lab:</label>
        <input type="text" name="jadwal_tes_ruang" id="editSesiRuang" class="form-control" required style="font-weight:700;">
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px;">
        <button type="button" onclick="tutupModalEditSesi()" class="btn btn-sm" style="background:#f1f5f9; color:#475569; font-weight:700; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;">Batal</button>
        <button type="submit" class="btn btn-sm" style="background:#2563eb; color:#ffffff; font-weight:800; border:none; padding:8px 20px; border-radius:6px; cursor:pointer;">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
