<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>CBT SMKN 1 Air Naningan: {{ $asesmen->judul }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
  <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"></script>
  <link rel="stylesheet" href="{{ asset('css/akademik-app.css') }}?v={{ time() }}">

  <style>
    :root {
      --cbt-primary: #2563eb;
      --cbt-dark: #0f172a;
      --cbt-bg: #f8fafc;
      --cbt-card: #ffffff;
      --cbt-border: #e2e8f0;
    }

    body {
      background: var(--cbt-bg);
      font-family: 'Plus Jakarta Sans', sans-serif;
      color: var(--cbt-dark);
      margin: 0;
      padding: 0;
      -webkit-user-select: {{ $asesmen->blokir_copy_paste ? 'none' : 'auto' }};
      -moz-user-select: {{ $asesmen->blokir_copy_paste ? 'none' : 'auto' }};
      -ms-user-select: {{ $asesmen->blokir_copy_paste ? 'none' : 'auto' }};
      user-select: {{ $asesmen->blokir_copy_paste ? 'none' : 'auto' }};
    }

    .cbt-topbar {
      position: sticky;
      top: 0;
      z-index: 100;
      background: #ffffff;
      border-bottom: 2px solid #e2e8f0;
      padding: 12px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .cbt-app-brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .cbt-brand-badge {
      background: #eff6ff;
      color: #2563eb;
      border: 1px solid #bfdbfe;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .cbt-status-group {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .cbt-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 700;
    }

    .cbt-pill-timer {
      background: #fef2f2;
      color: #b91c1c;
      border: 1px solid #fecaca;
      font-family: monospace;
      font-size: 17px;
      letter-spacing: 1px;
    }

    .cbt-pill-integrity {
      background: #ecfdf5;
      color: #047857;
      border: 1px solid #a7f3d0;
    }

    .cbt-pill-integrity.warning {
      background: #fffbeb;
      color: #b45309;
      border-color: #fde68a;
    }

    .cbt-pill-integrity.danger {
      background: #fef2f2;
      color: #b91c1c;
      border-color: #fca5a5;
    }

    .cbt-body-layout {
      max-width: 1240px;
      margin: 24px auto 80px;
      padding: 0 20px;
      display: grid;
      grid-template-columns: 1fr 310px;
      gap: 24px;
      align-items: start;
    }

    .cbt-question-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      padding: 24px;
      margin-bottom: 20px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
      transition: border-color 0.2s;
    }

    .cbt-question-card:focus-within {
      border-color: #93c5fd;
    }

    .cbt-q-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
      padding-bottom: 12px;
      border-bottom: 1px solid #f1f5f9;
    }

    .cbt-q-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 4px 12px;
      border-radius: 6px;
      background: #1e293b;
      color: #ffffff;
      font-size: 13px;
      font-weight: 800;
    }

    .cbt-q-text {
      font-size: 15.5px;
      line-height: 1.65;
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 20px;
    }

    .cbt-opt-container {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .cbt-opt-label {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 12px 18px;
      border: 1.5px solid #e2e8f0;
      border-radius: 10px;
      cursor: pointer;
      background: #ffffff;
      transition: all 0.15s ease-in-out;
    }

    .cbt-opt-label:hover {
      background: #f8fafc;
      border-color: #cbd5e1;
    }

    .cbt-opt-label input[type="radio"] {
      width: 18px;
      height: 18px;
      accent-color: #2563eb;
      margin: 0;
    }

    .cbt-opt-label.active {
      background: #eff6ff;
      border-color: #3b82f6;
    }

    .cbt-opt-label.active .cbt-opt-txt {
      font-weight: 700;
      color: #1d4ed8;
    }

    .cbt-nav-box {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      padding: 20px;
      position: sticky;
      top: 90px;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .cbt-nav-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 8px;
      margin-top: 14px;
    }

    .cbt-nav-btn {
      aspect-ratio: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 8px;
      border: 1px solid #cbd5e1;
      background: #f8fafc;
      color: #475569;
      font-weight: 800;
      font-size: 13px;
      text-decoration: none;
      transition: all 0.15s;
    }

    .cbt-nav-btn:hover {
      background: #e2e8f0;
    }

    .cbt-nav-btn.answered {
      background: #10b981;
      border-color: #059669;
      color: #ffffff;
    }

    /* Modal Overlay */
    .cbt-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(15, 23, 42, 0.88);
      backdrop-filter: blur(6px);
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .cbt-modal-card {
      background: #ffffff;
      border-radius: 18px;
      max-width: 520px;
      width: 100%;
      padding: 32px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
      text-align: center;
      animation: cbtPop 0.25s ease-out;
    }

    @keyframes cbtPop {
      0% { transform: scale(0.92); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }

    /* Mobile Bottom Navigation Bar */
    .cbt-mobile-bottom-bar {
      display: none;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(255, 255, 255, 0.96);
      backdrop-filter: blur(10px);
      border-top: 1.5px solid #e2e8f0;
      padding: 10px 14px;
      z-index: 90;
      box-shadow: 0 -4px 14px rgba(0, 0, 0, 0.08);
      align-items: center;
      justify-content: space-between;
      gap: 10px;
    }

    /* Responsive Mobile Screen Tuning */
    @media (max-width: 900px) {
      .cbt-topbar {
        padding: 10px 12px;
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
      }
      .cbt-app-brand {
        justify-content: space-between;
      }
      .cbt-status-group {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 6px;
      }
      .cbt-pill {
        padding: 5px 8px;
        font-size: 11px;
      }
      .cbt-pill-timer {
        font-size: 14.5px;
      }
      .cbt-body-layout {
        margin: 10px auto 85px;
        padding: 0 10px;
        grid-template-columns: 1fr;
        gap: 14px;
      }
      .cbt-question-card {
        padding: 16px 14px;
        border-radius: 12px;
        margin-bottom: 14px;
      }
      .cbt-q-text {
        font-size: 14.5px;
        line-height: 1.55;
        margin-bottom: 14px;
        -webkit-touch-callout: {{ $asesmen->blokir_copy_paste ? 'none' : 'default' }};
      }
      .cbt-opt-label {
        padding: 12px 14px;
        min-height: 48px;
        touch-action: manipulation;
        -webkit-touch-callout: none;
      }
      .cbt-opt-label input[type="radio"] {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
      }
      .cbt-opt-txt {
        font-size: 13.5px;
        line-height: 1.4;
      }
      .cbt-nav-box {
        position: static;
        margin-top: 10px;
      }
      .cbt-mobile-bottom-bar {
        display: flex;
      }
    }
  </style>
</head>
<body>

@php
  $jawabanTersimpan = $hasil?->jawaban ?? [];
  $isSelesai = $hasil?->is_selesai ?? false;
  $violationsNow = $hasil?->jumlah_pelanggaran ?? 0;
  $maxToleransi = $asesmen->max_toleransi_keluar ?? 3;
  $isLocked = ($violationsNow >= $maxToleransi) && $asesmen->anti_cheat_mode;
@endphp

{{-- ===================================================================== --}}
{{-- 1. LAYAR JIKA SUDAH SELESAI / SUDAH DI-SUBMIT                          --}}
{{-- ===================================================================== --}}
@if($isSelesai)
  <div class="cbt-overlay" style="display:flex;">
    <div class="cbt-modal-card" style="max-width:580px;">
      <div style="width:68px; height:68px; border-radius:50%; background:#ecfdf5; color:#059669; display:inline-flex; align-items:center; justify-content:center; font-size:32px; margin-bottom:16px;">
        <i class="bi bi-check-circle-fill"></i>
      </div>
      <h2 style="font-weight:900; font-size:22px; color:#0f172a; margin-bottom:8px;">Lembar Jawaban Berhasil Terkirim</h2>
      <p style="color:#64748b; font-size:13.5px; margin-bottom:20px;">
        Asesmen online <strong>{{ $asesmen->judul }}</strong> telah resmi diselesaikan.
      </p>

      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; margin-bottom:24px; text-align:left;">
        <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:13px;">
          <span style="color:#64748b;">Nama Peserta:</span>
          <strong>{{ $siswa?->nama ?? $siswa?->nama_lengkap }}</strong>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:13px;">
          <span style="color:#64748b;">Waktu Selesai:</span>
          <span>{{ \Carbon\Carbon::parse($hasil->selesai_pada ?? now())->isoFormat('D MMM Y, HH:mm') }} WIB</span>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:13px;">
          <span style="color:#64748b;">Status Integritas:</span>
          @if($hasil->status_kejujuran === 'jujur')
            <span class="ak-badge ak-badge-success"><i class="bi bi-shield-check me-1"></i> Sangat Baik (Jujur)</span>
          @elseif($hasil->status_kejujuran === 'waspada')
            <span class="ak-badge ak-badge-warning"><i class="bi bi-exclamation-triangle me-1"></i> Peringatan ({{ $hasil->jumlah_pelanggaran }}x Pelanggaran)</span>
          @else
            <span class="ak-badge ak-badge-danger"><i class="bi bi-shield-x me-1"></i> Terindikasi Curang</span>
          @endif
        </div>

        @if($asesmen->tampilkan_nilai)
          <div style="border-top:1px dashed #cbd5e1; margin-top:12px; padding-top:12px; display:flex; justify-content:space-between; align-items:center;">
            <span style="font-weight:700; color:#0f172a; font-size:14px;">Skor Nilai Akhir:</span>
            <span style="font-size:26px; font-weight:900; color:{{ $hasil->nilai >= $asesmen->passing_grade ? '#059669' : '#dc2626' }};">
              {{ $hasil->nilai }}
            </span>
          </div>
        @endif
      </div>

      <div style="display:flex; gap:10px; justify-content:center;">
        <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-primary" style="padding:10px 24px;">
          <i class="bi bi-house-door"></i> Kembali ke Modul Asesmen
        </a>
      </div>
    </div>
  </div>
@endif

{{-- ===================================================================== --}}
{{-- 2. LAYAR JIKA TERKUNCI KARENA MELEBIHI TOLERANSI PELANGGARAN          --}}
{{-- ===================================================================== --}}
@if(!$isSelesai && $isLocked)
  <div class="cbt-overlay" style="display:flex;">
    <div class="cbt-modal-card">
      <div style="width:68px; height:68px; border-radius:50%; background:#fef2f2; color:#dc2626; display:inline-flex; align-items:center; justify-content:center; font-size:32px; margin-bottom:16px;">
        <i class="bi bi-shield-x"></i>
      </div>
      <h2 style="font-weight:900; font-size:20px; color:#991b1b; margin-bottom:8px;">Sesi Ujian Dikunci Otomatis</h2>
      <p style="color:#64748b; font-size:13px; margin-bottom:20px; line-height:1.5;">
        Sistem anti-kecurangan mendeteksi Anda telah keluar dari layar/jendela CBT sebanyak <strong>{{ $violationsNow }} kali</strong>, yang melebihi batas toleransi maksimal ({{ $maxToleransi }} kali).
      </p>

      <div style="background:#fff1f2; border:1px solid #fecdd3; border-radius:10px; padding:14px; margin-bottom:20px; font-size:12.5px; color:#9f1239; text-align:left;">
        <i class="bi bi-info-circle-fill me-1"></i> Silakan lapor ke <strong>Pengawas Ruang atau Guru Pengampu</strong> untuk meminta reset izin sesi ujian Anda.
      </div>

      <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary" style="width:100%; justify-content:center;">
        Kembali ke Halaman Utama
      </a>
    </div>
  </div>
@endif

{{-- ===================================================================== --}}
{{-- 3. MODAL GERBANG MULAI & TOKEN UJIAN                                  --}}
{{-- ===================================================================== --}}
<div class="cbt-overlay" id="overlayTokenGate" style="{{ (!$isSelesai && !$isLocked) ? 'display:flex;' : 'display:none;' }}">
  <div class="cbt-modal-card" style="max-width:540px;">
    <div style="display:flex; align-items:center; justify-content:center; gap:10px; margin-bottom:12px;">
      <span class="cbt-brand-badge"><i class="bi bi-shield-lock-fill me-1"></i> CBT Anti-Cheating Engine</span>
    </div>
    
    <h2 style="font-weight:900; font-size:22px; color:#0f172a; margin-bottom:6px;">{{ $asesmen->judul }}</h2>
    <div style="font-size:13px; color:#64748b; margin-bottom:20px;">
      {{ $asesmen->distribusi?->mataPelajaran?->nama_mapel }} · Peserta: <strong>{{ $siswa?->nama ?? $siswa?->nama_lengkap }}</strong>
    </div>

    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:20px; text-align:left;">
      <div style="font-weight:700; font-size:13px; margin-bottom:8px; color:#0f172a;">Tata Tertib &amp; Proteksi Sistem:</div>
      <ul style="margin:0; padding-left:18px; font-size:12.5px; color:#475569; display:flex; flex-direction:column; gap:6px;">
        <li>Durasi pengerjaan: <strong>{{ $asesmen->durasi_menit }} Menit</strong>.</li>
        <li>Wajib menggunakan <strong>Mode Layar Penuh (Fullscreen)</strong>.</li>
        <li>Dilarang berpindah tab browser atau membuka aplikasi lain (Toleransi: <strong>{{ $maxToleransi }}x</strong>).</li>
        <li>Setiap jawaban yang Anda pilih otomatis tersimpan ke server secara realtime.</li>
      </ul>
    </div>

    @if($asesmen->token_ujian)
      <div style="margin-bottom:20px;">
        <label style="display:block; font-size:12.5px; font-weight:800; color:#1e293b; margin-bottom:6px;">
          MASUKKAN TOKEN UJIAN RESMI <span style="color:#dc2626;">*</span>
        </label>
        <input type="text" id="tokenInput" class="ak-input" maxlength="10" placeholder="Ketik 6 digit token" style="text-align:center; font-family:monospace; font-weight:900; font-size:20px; letter-spacing:4px; text-transform:uppercase;">
        <div id="tokenError" style="font-size:12px; color:#dc2626; margin-top:4px; display:none; font-weight:700;">
          Token yang Anda masukkan salah. Tanyakan ke Pengawas Ruang.
        </div>
      </div>
    @endif

    <button type="button" class="ak-btn ak-btn-primary" style="width:100%; justify-content:center; padding:14px; font-size:15px; font-weight:800;" onclick="mulaiUjianSekarang()">
      <i class="bi bi-fullscreen me-1"></i> Mulai Ujian (Masuk Layar Penuh)
    </button>
  </div>
</div>

{{-- ===================================================================== --}}
{{-- 4. MODAL PERINGATAN KELUAR LAYAR PENUH                                --}}
{{-- ===================================================================== --}}
<div class="cbt-overlay" id="overlayFullscreenWarning" style="display:none; background:rgba(220, 38, 38, 0.92);">
  <div class="cbt-modal-card" style="border:3px solid #f87171;">
    <div style="width:70px; height:70px; border-radius:50%; background:#fee2e2; color:#dc2626; display:inline-flex; align-items:center; justify-content:center; font-size:36px; margin-bottom:16px;">
      <i class="bi bi-exclamation-octagon-fill"></i>
    </div>
    <h2 style="font-weight:900; font-size:22px; color:#991b1b; margin-bottom:8px;">PERINGATAN INTEGRITAS!</h2>
    <p style="color:#334155; font-size:14px; margin-bottom:16px; line-height:1.5;">
      Anda terdeteksi <strong>keluar dari mode layar penuh / berpindah jendela</strong>! Aktivitas ini dicatat sebagai pelanggaran ujian CBT.
    </p>
    <div style="font-weight:800; font-size:16px; color:#dc2626; margin-bottom:20px;">
      Pelanggaran: <span id="warnCount">1</span> dari {{ $maxToleransi }} Kali
    </div>
    <button type="button" class="ak-btn ak-btn-primary" style="width:100%; justify-content:center; padding:12px; font-size:14px; font-weight:800;" onclick="kembalikanFullscreen()">
      <i class="bi bi-arrows-fullscreen me-1"></i> Kembali ke Layar Penuh Sekarang
    </button>
  </div>
</div>

{{-- ===================================================================== --}}
{{-- 5. CBT MAIN APP CONTAINER                                             --}}
{{-- ===================================================================== --}}
<div id="cbtAppRoot">

  {{-- Top Sticky Header --}}
  <header class="cbt-topbar">
    <div class="cbt-app-brand">
      <span class="cbt-brand-badge"><i class="bi bi-laptop me-1"></i> SMKN 1 AIR NANINGAN</span>
      <div>
        <div style="font-weight:800; font-size:14px; color:#0f172a;">{{ $asesmen->judul }}</div>
        <div style="font-size:11.5px; color:#64748b;">
          {{ $siswa?->nama ?? $siswa?->nama_lengkap }} ({{ $siswa?->nisn ?? 'CBT-USER' }}) · {{ $asesmen->distribusi?->mataPelajaran?->nama_mapel }}
        </div>
      </div>
    </div>

    <div class="cbt-status-group">
      {{-- Autosave pill --}}
      <div class="cbt-pill" id="pillAutosave" style="background:#f1f5f9; color:#475569;">
        <i class="bi bi-cloud-check"></i>
        <span id="txtAutosave">Tersimpan</span>
      </div>

      {{-- Integrity Violation Pill --}}
      @if($asesmen->anti_cheat_mode)
        <div class="cbt-pill cbt-pill-integrity" id="pillViolation">
          <i class="bi bi-shield-check"></i>
          <span>Pelanggaran: <strong id="lblViolationCount">{{ $violationsNow }}</strong>/{{ $maxToleransi }}</span>
        </div>
      @endif

      {{-- Timer Pill --}}
      <div class="cbt-pill cbt-pill-timer" id="pillTimer">
        <i class="bi bi-clock-history"></i>
        <span id="lblCountdown">--:--</span>
      </div>
    </div>
  </header>

  {{-- Main Question Layout --}}
  <div class="cbt-body-layout">
    
    {{-- Left: Questions List --}}
    <main>
      @if($soals->isEmpty())
        <div class="akademik-card" style="padding:60px 20px; text-align:center;">
          <i class="bi bi-journal-x" style="font-size:48px; color:#94a3b8; display:block; margin-bottom:12px;"></i>
          <h3 style="font-weight:800; color:#1e293b;">Belum Ada Butir Soal</h3>
          <p style="font-size:13px; color:#64748b;">Guru pengampu belum mengunggah butir pertanyaan untuk asesmen ini.</p>
          <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary">Kembali</a>
        </div>
      @else
        <form action="{{ route('akademik.asesmen.submit', $asesmen->id) }}" method="POST" id="formExam">
          @csrf
          <input type="hidden" name="siswa_id" value="{{ $siswa?->id }}">

          @foreach($soals as $index => $soal)
            @php
              $no = $index + 1;
              $opsiList = $opsiAcakPerSoal[$soal->id] ?? [
                'A' => $soal->opsi_a,
                'B' => $soal->opsi_b,
                'C' => $soal->opsi_c,
                'D' => $soal->opsi_d,
                'E' => $soal->opsi_e,
              ];
              $answeredVal = $jawabanTersimpan[$soal->id] ?? null;
            @endphp

            <div class="cbt-question-card" id="q-card-{{ $no }}">
              <div class="cbt-q-header">
                <span class="cbt-q-badge">Soal No. {{ $no }}</span>
                <span class="ak-badge ak-badge-secondary" style="font-size:11px;">Bobot: {{ $soal->bobot }} Poin</span>
              </div>

              <div class="cbt-q-text">
                {!! $soal->pertanyaan !!}
              </div>

              @if($soal->gambar_url)
                <div style="margin-bottom:18px; max-width:480px;">
                  <img src="{{ $soal->gambar_url }}" alt="Ilustrasi Soal" style="width:100%; border-radius:8px; border:1px solid #cbd5e1;">
                </div>
              @endif

              <div class="cbt-opt-container">
                @php $labelAbjad = ['A', 'B', 'C', 'D', 'E']; $abjadIdx = 0; @endphp
                @foreach($opsiList as $key => $teks)
                  @if(!empty($teks))
                    @php 
                      $currentAbjad = $labelAbjad[$abjadIdx++] ?? $key;
                      $isSelected = ($answeredVal === $key);
                    @endphp
                    <label class="cbt-opt-label {{ $isSelected ? 'active' : '' }}" id="lbl-{{ $soal->id }}-{{ $key }}">
                      <input type="radio" 
                             name="jawaban[{{ $soal->id }}]" 
                             value="{{ $key }}" 
                             data-soal-no="{{ $no }}" 
                             {{ $isSelected ? 'checked' : '' }} 
                             onchange="pilihJawaban({{ $soal->id }}, '{{ $key }}', {{ $no }})">
                      <div class="cbt-opt-txt">
                        <strong style="margin-right:6px;">{{ $currentAbjad }}.</strong> {!! $teks !!}
                      </div>
                    </label>
                  @endif
                @endforeach
              </div>
            </div>
          @endforeach

          {{-- Final Submit Action Card --}}
          <div style="background:#ffffff; border:1.5px solid #cbd5e1; border-radius:14px; padding:20px 24px; display:flex; justify-content:space-between; align-items:center; margin-top:24px;">
            <div>
              <div style="font-weight:800; font-size:14px; color:#0f172a;">Sudah Memeriksa Seluruh Lembar Jawaban?</div>
              <div style="font-size:12px; color:#64748b;">
                Periksa kembali navigasi nomor di sebelah kanan sebelum menyelesaikan ujian.
              </div>
            </div>
            <button type="button" class="ak-btn ak-btn-primary" style="padding:12px 24px; font-weight:800; font-size:14px;" onclick="konfirmasiSelesai()">
              <i class="bi bi-send-check me-1"></i> Selesai &amp; Kirim Ujian
            </button>
          </div>

        </form>
      @endif
    </main>

    {{-- Right Sidebar: Palette Navigasi Soal --}}
    <aside>
      <div class="cbt-nav-box" id="cbtNavBoxWrapper">
        <div style="font-weight:800; font-size:13.5px; color:#0f172a; display:flex; justify-content:space-between; align-items:center;">
          <span>Navigasi Soal</span>
          <span style="font-size:12px; color:#64748b;">
            <span id="countAnswered">0</span>/{{ $soals->count() }} Terjawab
          </span>
        </div>

        <div class="cbt-nav-grid">
          @foreach($soals as $idx => $s)
            @php
              $n = $idx + 1;
              $isFilled = !empty($jawabanTersimpan[$s->id]);
            @endphp
            <a href="#q-card-{{ $n }}" 
               class="cbt-nav-btn {{ $isFilled ? 'answered' : '' }}" 
               id="nav-btn-{{ $n }}" 
               onclick="smoothScrollTo('q-card-{{ $n }}', event)">
              {{ $n }}
            </a>
          @endforeach
        </div>

        <div style="margin-top:20px; padding-top:14px; border-top:1px solid #f1f5f9; display:flex; flex-direction:column; gap:6px; font-size:11px; color:#64748b;">
          <div style="display:flex; align-items:center; gap:8px;">
            <span style="width:12px; height:12px; border-radius:3px; background:#10b981; display:inline-block;"></span>
            <span>Hijau: Soal sudah dijawab</span>
          </div>
          <div style="display:flex; align-items:center; gap:8px;">
            <span style="width:12px; height:12px; border-radius:3px; background:#f8fafc; border:1px solid #cbd5e1; display:inline-block;"></span>
            <span>Abu-abu: Belum dijawab</span>
          </div>
        </div>

        <div style="margin-top:20px;">
          <button type="button" class="ak-btn ak-btn-secondary" style="width:100%; justify-content:center; font-size:12px;" onclick="konfirmasiSelesai()">
            Kirim Lembar Jawaban
          </button>
        </div>
      </div>
    </aside>

  </div>

  {{-- Mobile Floating Action Bar (HP Mode) --}}
  <div class="cbt-mobile-bottom-bar">
    <button type="button" class="ak-btn ak-btn-secondary" style="font-size:12px; padding:8px 12px; font-weight:700;" onclick="smoothScrollTo('cbtNavBoxWrapper', event)">
      <i class="bi bi-grid-3x3-gap-fill me-1"></i>
      <span>Daftar Soal (<span id="mobCountAnswered">0</span>/{{ $soals->count() }})</span>
    </button>
    <button type="button" class="ak-btn ak-btn-primary" style="font-size:12.5px; padding:8px 14px; font-weight:800;" onclick="konfirmasiSelesai()">
      <i class="bi bi-send-check me-1"></i> Kirim Jawaban
    </button>
  </div>

</div>

<script>
  const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const ASESMEN_ID = {{ $asesmen->id }};
  const SISWA_ID = {{ $siswa?->id ?? 0 }};
  const TOKEN_RESMI = "{{ $asesmen->token_ujian }}";
  const WAJIB_FULLSCREEN = {{ $asesmen->wajib_fullscreen ? 'true' : 'false' }};
  const ANTI_CHEAT = {{ $asesmen->anti_cheat_mode ? 'true' : 'false' }};
  const BLOKIR_SHORTCUT = {{ $asesmen->blokir_copy_paste ? 'true' : 'false' }};
  const MAX_TOLERANSI = {{ $maxToleransi }};
  let currentViolations = {{ $violationsNow }};
  let isExamActive = false;

  // Sound generator for violation alert
  function playBeep() {
    try {
      const ctx = new (window.AudioContext || window.webkitAudioContext)();
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = 'sawtooth';
      osc.frequency.setValueAtTime(440, ctx.currentTime); // A4
      osc.frequency.setValueAtTime(880, ctx.currentTime + 0.1);
      gain.gain.setValueAtTime(0.3, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start();
      osc.stop(ctx.currentTime + 0.35);
    } catch(e) {}
  }

  // Smooth scroll accounting for sticky header
  function smoothScrollTo(id, e) {
    if (e) e.preventDefault();
    const el = document.getElementById(id);
    if (el) {
      const offset = 70;
      const bodyRect = document.body.getBoundingClientRect().top;
      const elementRect = el.getBoundingClientRect().top;
      const elementPosition = elementRect - bodyRect;
      const offsetPosition = elementPosition - offset;

      window.scrollTo({
        top: offsetPosition,
        behavior: 'smooth'
      });
    }
  }

  // Count answered (desktop & mobile synced)
  function refreshAnsweredCount() {
    const answered = document.querySelectorAll('.cbt-nav-btn.answered').length;
    const countEl = document.getElementById('countAnswered');
    const mobCountEl = document.getElementById('mobCountAnswered');
    if (countEl) countEl.innerText = answered;
    if (mobCountEl) mobCountEl.innerText = answered;
  }
  refreshAnsweredCount();

  // Answer selection handler + realtime autosave
  function pilihJawaban(soalId, opsiKey, no) {
    // Styling
    const card = document.getElementById('q-card-' + no);
    if (card) {
      card.querySelectorAll('.cbt-opt-label').forEach(lbl => lbl.classList.remove('active'));
      const activeLbl = document.getElementById('lbl-' + soalId + '-' + opsiKey);
      if (activeLbl) activeLbl.classList.add('active');
    }

    // Mark palette green
    const navBtn = document.getElementById('nav-btn-' + no);
    if (navBtn) navBtn.classList.add('answered');
    refreshAnsweredCount();

    // Autosave to Server
    const autosavePill = document.getElementById('pillAutosave');
    const txtAutosave = document.getElementById('txtAutosave');
    if (txtAutosave) txtAutosave.innerText = 'Menyimpan...';

    const payload = {
      siswa_id: SISWA_ID,
      jawaban: { [soalId]: opsiKey }
    };

    fetch('{{ route("akademik.asesmen.autosave", $asesmen->id) }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
      if (txtAutosave) txtAutosave.innerText = 'Tersimpan ' + (data.saved_at || '');
      if (autosavePill) autosavePill.style.background = '#ecfdf5';
    })
    .catch(() => {
      if (txtAutosave) txtAutosave.innerText = 'Tersimpan lokal';
    });
  }

  // Exam Start Handler (Safe for Mobile & Desktop)
  function mulaiUjianSekarang() {
    if (TOKEN_RESMI) {
      const inputVal = document.getElementById('tokenInput').value.trim().toUpperCase();
      if (inputVal !== TOKEN_RESMI) {
        document.getElementById('tokenError').style.display = 'block';
        return;
      }
    }

    // Enter Fullscreen if required (Safe on Android & iOS Safari)
    if (WAJIB_FULLSCREEN) {
      const root = document.documentElement;
      try {
        if (root.requestFullscreen) {
          root.requestFullscreen().catch(() => {});
        } else if (root.webkitRequestFullscreen) {
          root.webkitRequestFullscreen();
        }
      } catch(e) {}
    }

    document.getElementById('overlayTokenGate').style.display = 'none';
    isExamActive = true;
  }

  function kembalikanFullscreen() {
    const root = document.documentElement;
    try {
      if (root.requestFullscreen) {
        root.requestFullscreen().catch(() => {});
      } else if (root.webkitRequestFullscreen) {
        root.webkitRequestFullscreen();
      }
    } catch(e) {}
    document.getElementById('overlayFullscreenWarning').style.display = 'none';
  }

  // Violation Logger
  function catatPelanggaran(tipe, keterangan) {
    if (!isExamActive || !ANTI_CHEAT) return;

    playBeep();
    currentViolations++;

    // Update UI badge
    const lblCount = document.getElementById('lblViolationCount');
    const pill = document.getElementById('pillViolation');
    if (lblCount) lblCount.innerText = currentViolations;
    if (pill) {
      if (currentViolations >= MAX_TOLERANSI) {
        pill.className = 'cbt-pill cbt-pill-integrity danger';
      } else {
        pill.className = 'cbt-pill cbt-pill-integrity warning';
      }
    }

    // Call server to log infraction
    fetch('{{ route("akademik.asesmen.log_pelanggaran", $asesmen->id) }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        siswa_id: SISWA_ID,
        tipe: tipe,
        keterangan: keterangan
      })
    })
    .then(r => r.json())
    .then(data => {
      if (data.is_locked) {
        alert("Batas toleransi pelanggaran integritas telah habis. Ujian Anda otomatis dikunci.");
        document.getElementById('formExam').submit();
      }
    });

    if (currentViolations >= MAX_TOLERANSI) {
      alert("PERINGATAN KERAS: Batas toleransi pelanggaran telah tercapai. Ujian Anda akan otomatis dikirim.");
      document.getElementById('formExam').submit();
    } else {
      document.getElementById('warnCount').innerText = currentViolations;
      document.getElementById('overlayFullscreenWarning').style.display = 'flex';
    }
  }

  // 1. Fullscreen Change Listener
  document.addEventListener('fullscreenchange', () => {
    if (!document.fullscreenElement && isExamActive && WAJIB_FULLSCREEN) {
      catatPelanggaran('keluar_fullscreen', 'Keluar dari mode layar penuh (Fullscreen)');
    }
  });

  // 2. Visibility / Tab & App Switching Listener (Paling Efektif di HP)
  document.addEventListener('visibilitychange', () => {
    if (document.hidden && isExamActive && ANTI_CHEAT) {
      catatPelanggaran('pindah_tab', 'Membuka aplikasi lain, WhatsApp, atau meminimalkan browser di HP');
    }
  });

  // 3. Window Blur Listener (Dengan filter virtual keyboard HP)
  window.addEventListener('blur', () => {
    if (document.activeElement && (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA' || document.activeElement.tagName === 'SELECT')) {
      return; // Abaikan interaksi keyboard virtual HP
    }
    if (isExamActive && ANTI_CHEAT) {
      catatPelanggaran('window_blur', 'Beralih ke aplikasi lain atau split-screen di HP');
    }
  });

  // 4. Hotkeys & Right Click Blocker
  if (BLOKIR_SHORTCUT) {
    document.addEventListener('contextmenu', e => e.preventDefault());

    document.addEventListener('keydown', e => {
      // F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U, Ctrl+C, Ctrl+V, Ctrl+S, Ctrl+P
      if (
        e.key === 'F12' ||
        (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j')) ||
        (e.ctrlKey && (e.key === 'u' || e.key === 'U' || e.key === 'c' || e.key === 'C' || e.key === 'v' || e.key === 'V' || e.key === 's' || e.key === 'S' || e.key === 'p' || e.key === 'P'))
      ) {
        e.preventDefault();
        e.stopPropagation();
        if (isExamActive) {
          catatPelanggaran('shortcut_terlarang', 'Mencoba menekan shortcut terlarang: ' + e.key);
        }
        return false;
      }
    });
  }

  // 5. Countdown Timer
  const totalSeconds = {{ $asesmen->durasi_menit * 60 }};
  let timeLeft = totalSeconds;
  const lblCountdown = document.getElementById('lblCountdown');
  const pillTimer = document.getElementById('pillTimer');

  function updateCountdown() {
    if (!isExamActive) return;

    if (timeLeft <= 0) {
      clearInterval(timerInterval);
      if (lblCountdown) lblCountdown.innerText = '00:00';
      alert('Waktu pengerjaan telah habis! Seluruh jawaban Anda akan otomatis dikirimkan.');
      document.getElementById('formExam').submit();
      return;
    }

    const m = Math.floor(timeLeft / 60);
    const s = timeLeft % 60;
    if (lblCountdown) {
      lblCountdown.innerText = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }

    if (timeLeft < 300 && pillTimer) {
      pillTimer.style.animation = 'pulse 1s infinite';
    }

    timeLeft--;
  }

  const timerInterval = setInterval(updateCountdown, 1000);

  // Final Confirmation
  function konfirmasiSelesai() {
    const totalSoal = {{ $soals->count() }};
    const answered = document.querySelectorAll('.cbt-nav-btn.answered').length;
    const unanswered = totalSoal - answered;

    let msg = `Anda telah menjawab ${answered} dari ${totalSoal} butir soal.\n`;
    if (unanswered > 0) {
      msg += `PERHATIAN: Masih ada ${unanswered} butir soal yang belum Anda jawab!\n\n`;
    }
    msg += 'Apakah Anda yakin ingin mengakhiri dan mengirim lembar jawaban sekarang?';

    if (confirm(msg)) {
      isExamActive = false;
      document.getElementById('formExam').submit();
    }
  }

  // Render Rumus Matematika (KaTeX) untuk Siswa di HP & PC
  document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
      if (typeof renderMathInElement === 'function') {
        renderMathInElement(document.body, {
          delimiters: [
            {left: '$$', right: '$$', display: true},
            {left: '$', right: '$', display: false},
            {left: '\\(', right: '\\)', display: false},
            {left: '\\[', right: '\\]', display: true}
          ],
          throwOnError: false
        });
      }
    }, 250);
  });
</script>

</body>
</html>
