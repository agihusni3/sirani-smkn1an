<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ruang Ujian Siswa — {{ $siswa->nama }} | SMKN 1 Air Naningan</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: #f1f5f9;
      color: #0f172a;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .cbt-topbar {
      background: #ffffff;
      border-bottom: 1.5px solid #e2e8f0;
      padding: 14px 24px;
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }
    .cbt-topbar-inner {
      max-width: 1060px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
    }
    .cbt-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      color: inherit;
    }
    .cbt-brand img {
      width: 40px;
      height: 40px;
      object-fit: contain;
    }
    .cbt-brand-title {
      font-size: 15px;
      font-weight: 900;
      color: #1e3a8a;
      line-height: 1.2;
    }
    .cbt-brand-sub {
      font-size: 11.5px;
      color: #64748b;
    }
    .cbt-user-panel {
      display: flex;
      align-items: center;
      gap: 14px;
    }
    .btn-logout {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 7px 14px;
      background: #fee2e2;
      color: #dc2626;
      border: 1px solid #fca5a5;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s ease;
    }
    .btn-logout:hover {
      background: #fecaca;
    }

    .main-container {
      max-width: 1060px;
      margin: 24px auto;
      padding: 0 16px;
      width: 100%;
      flex: 1;
    }

    /* Student Identity Card */
    .student-id-card {
      background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
      color: #ffffff;
      border-radius: 16px;
      padding: 24px 28px;
      margin-bottom: 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 18px;
      box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.3);
    }
    .student-info-left {
      display: flex;
      align-items: center;
      gap: 18px;
    }
    .student-avatar {
      width: 64px;
      height: 64px;
      border-radius: 16px;
      background: rgba(255,255,255,0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      border: 2px solid rgba(255,255,255,0.4);
    }
    .student-name {
      font-size: 20px;
      font-weight: 900;
      letter-spacing: -0.3px;
      margin-bottom: 4px;
    }
    .student-meta-pills {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }
    .meta-pill {
      font-size: 12px;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 6px;
      background: rgba(255,255,255,0.18);
      backdrop-filter: blur(4px);
    }

    /* Alerts */
    .alert-box {
      padding: 12px 18px;
      border-radius: 10px;
      font-size: 13.5px;
      font-weight: 600;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .alert-success {
      background: #ecfdf5;
      border: 1px solid #a7f3d0;
      color: #065f46;
    }
    .alert-danger {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #991b1b;
    }

    /* Section Head */
    .section-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
    }
    .section-title {
      font-size: 16px;
      font-weight: 800;
      color: #1e293b;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .badge-count {
      font-size: 11.5px;
      background: #e2e8f0;
      color: #334155;
      padding: 2px 8px;
      border-radius: 20px;
      font-weight: 800;
    }

    /* Exam Cards Grid */
    .exam-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 16px;
      margin-bottom: 32px;
    }
    .exam-card {
      background: #ffffff;
      border-radius: 14px;
      border: 1.5px solid #e2e8f0;
      padding: 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: all 0.2s ease;
      box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .exam-card:hover {
      border-color: #93c5fd;
      box-shadow: 0 10px 20px -5px rgba(0,0,0,0.06);
      transform: translateY(-2px);
    }
    .exam-card-top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      margin-bottom: 12px;
      gap: 8px;
    }
    .exam-badge-type {
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      padding: 3px 8px;
      border-radius: 6px;
      background: #eff6ff;
      color: #2563eb;
    }
    .exam-badge-status-done {
      font-size: 11px;
      font-weight: 800;
      padding: 3px 8px;
      border-radius: 6px;
      background: #ecfdf5;
      color: #059669;
    }
    .exam-title {
      font-size: 15.5px;
      font-weight: 800;
      color: #0f172a;
      line-height: 1.35;
      margin-bottom: 6px;
    }
    .exam-mapel {
      font-size: 13px;
      font-weight: 700;
      color: #2563eb;
      margin-bottom: 14px;
    }
    .exam-meta-specs {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
      padding: 10px 12px;
      background: #f8fafc;
      border-radius: 8px;
      margin-bottom: 16px;
      font-size: 12px;
    }
    .meta-spec-item {
      color: #64748b;
    }
    .meta-spec-item strong {
      color: #0f172a;
    }

    .btn-exam-start {
      width: 100%;
      padding: 11px;
      background: #2563eb;
      color: #ffffff;
      border: none;
      border-radius: 8px;
      font-size: 13.5px;
      font-weight: 800;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.15s ease;
    }
    .btn-exam-start:hover {
      background: #1d4ed8;
    }

    .btn-exam-done {
      width: 100%;
      padding: 11px;
      background: #f1f5f9;
      color: #64748b;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      cursor: default;
    }

    /* Empty state */
    .empty-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 16px;
      padding: 40px 20px;
      text-align: center;
      color: #64748b;
      box-shadow: 0 4px 12px -2px rgba(0,0,0,0.03);
    }

    /* Modal Token */
    .token-modal-backdrop {
      display: none;
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(15, 23, 42, 0.6);
      backdrop-filter: blur(4px);
      z-index: 999;
      align-items: center;
      justify-content: center;
      padding: 16px;
    }
    .token-modal-card {
      background: #ffffff;
      border-radius: 16px;
      max-width: 420px;
      width: 100%;
      padding: 24px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.25);
    }

    .footer-bar {
      text-align: center;
      padding: 20px;
      font-size: 12px;
      color: #94a3b8;
      border-top: 1px solid #e2e8f0;
      background: #ffffff;
    }

    /* Responsive Styles & Mobile Optimization */
    @media (max-width: 640px) {
      .cbt-topbar {
        padding: 10px 14px;
      }
      .cbt-brand {
        gap: 8px;
      }
      .cbt-brand img {
        width: 32px;
        height: 32px;
      }
      .cbt-brand-title {
        font-size: 13px;
        line-height: 1.15;
      }
      .cbt-brand-sub {
        font-size: 10px;
      }
      .btn-logout {
        padding: 5px 10px;
        font-size: 11px;
        border-radius: 7px;
      }
      .main-container {
        margin: 12px auto 20px;
        padding: 0 12px;
      }
      .student-id-card {
        padding: 16px;
        border-radius: 14px;
        margin-bottom: 16px;
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
      }
      .student-info-left {
        gap: 12px;
      }
      .student-avatar {
        width: 46px;
        height: 46px;
        font-size: 22px;
        border-radius: 12px;
        flex-shrink: 0;
      }
      .student-name {
        font-size: 16.5px;
        margin-bottom: 4px;
      }
      .student-meta-pills {
        gap: 5px;
      }
      .meta-pill {
        font-size: 11px;
        padding: 2.5px 7px;
        border-radius: 5px;
      }
      .student-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 10px;
        border-top: 1px solid rgba(255,255,255,0.15);
        margin-top: 2px;
        width: 100%;
      }
      .student-card-footer .time-text {
        font-size: 11.5px !important;
        text-align: left !important;
      }
      .student-card-footer .status-pill {
        display: inline-flex !important;
      }
      .section-head {
        margin-bottom: 12px;
        gap: 8px;
      }
      .section-title {
        font-size: 14px;
      }
      .badge-count {
        font-size: 10.5px;
        padding: 2px 7px;
      }
      .empty-card {
        padding: 28px 14px;
        border-radius: 14px;
      }
      .empty-icon-wrap {
        width: 52px !important;
        height: 52px !important;
        font-size: 22px !important;
        margin-bottom: 10px !important;
      }
      .empty-title {
        font-size: 14.5px !important;
      }
      .empty-desc {
        font-size: 12px !important;
        margin-bottom: 14px !important;
      }
      .exam-grid {
        grid-template-columns: 1fr;
        gap: 12px;
        margin-bottom: 24px;
      }
      .exam-card {
        padding: 14px;
        border-radius: 12px;
      }
      .exam-title {
        font-size: 14.5px;
      }
      .exam-mapel {
        font-size: 12px;
        margin-bottom: 10px;
      }
      .exam-meta-specs {
        gap: 6px;
        padding: 8px 10px;
        font-size: 11.5px;
        margin-bottom: 12px;
      }
      .btn-exam-start, .btn-exam-done {
        padding: 9px 12px;
        font-size: 12.5px;
      }
      .footer-bar {
        padding: 14px 10px;
        font-size: 11px;
      }
    }

    @keyframes pulse-dot {
      0% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.4; transform: scale(1.2); }
      100% { opacity: 1; transform: scale(1); }
    }
    .pulse-dot {
      display: inline-block;
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: #4ade80;
      animation: pulse-dot 2s infinite;
      vertical-align: middle;
    }
  </style>
</head>
<body>

  {{-- Topbar --}}
  <header class="cbt-topbar">
    <div class="cbt-topbar-inner">
      <div class="cbt-brand">
        <img src="{{ asset('logo.png') }}" alt="Logo" onerror="this.src='{{ asset('img/logo.png') }}'">
        <div>
          <div class="cbt-brand-title">SMK NEGERI 1 AIR NANINGAN</div>
          <div class="cbt-brand-sub">Ruang Asesmen &amp; CBT Online Mandiri</div>
        </div>
      </div>

      <div class="cbt-user-panel">
        <form action="{{ route('portal.asesmen.keluar') }}" method="POST">
          @csrf
          <button type="submit" class="btn-logout">
            <i class="bi bi-box-arrow-right"></i>
            <span>Keluar</span>
          </button>
        </form>
      </div>
    </div>
  </header>

  <main class="main-container">

    {{-- Flash Notifications --}}
    @if(session('success'))
      <div class="alert-box alert-success">
        <i class="bi bi-check-circle-fill text-success" style="font-size:18px;"></i>
        <div>{{ session('success') }}</div>
      </div>
    @endif

    @if(session('error'))
      <div class="alert-box alert-danger">
        <i class="bi bi-exclamation-triangle-fill" style="font-size:18px;"></i>
        <div>{{ session('error') }}</div>
      </div>
    @endif

    {{-- Student Identity Card --}}
    <div class="student-id-card">
      <div class="student-info-left">
        <div class="student-avatar">
          <i class="bi bi-person"></i>
        </div>
        <div>
          <h2 class="student-name">{{ $siswa->nama }}</h2>
          <div class="student-meta-pills">
            <span class="meta-pill"><i class="bi bi-mortarboard-fill me-1"></i> {{ $siswa->rombels->first()?->nama_rombel ?? 'Umum' }}</span>
            <span class="meta-pill"><i class="bi bi-person-vcard me-1"></i> NISN: {{ $siswa->nisn ?: $siswa->nis }}</span>
            <span class="meta-pill"><span class="pulse-dot me-1"></span> Siswa Aktif</span>
          </div>
        </div>
      </div>
      <div class="student-card-footer">
        <div class="time-text" style="font-size:12.5px; color:rgba(255,255,255,0.85); text-align:right;">
          <i class="bi bi-clock-history me-1"></i> Waktu Server: <strong style="color:#ffffff;">{{ now()->translatedFormat('d M Y, H:i') }} WIB</strong>
        </div>
        <div class="status-pill" style="display:none; align-items:center; gap:5px; font-size:11px; font-weight:700; background:rgba(255,255,255,0.18); padding:3px 9px; border-radius:20px; color:#ffffff;">
          <span class="pulse-dot" style="width:6px; height:6px;"></span> Ruang CBT
        </div>
      </div>
    </div>

    {{-- Daftar Ujian Aktif Rombel --}}
    <div class="section-head">
      <h3 class="section-title">
        <i class="bi bi-laptop text-primary"></i>
        <span>Daftar Asesmen &amp; Ujian Aktif Kelas Anda</span>
      </h3>
      <span class="badge-count">{{ $asesmens->count() }} Paket Tersedia</span>
    </div>

    @if($asesmens->isEmpty())
      <div class="empty-card">
        <div class="empty-icon-wrap" style="width:60px; height:60px; background:#eff6ff; color:#2563eb; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; font-size:26px;">
          <i class="bi bi-journal-check" style="color:#2563eb;"></i>
        </div>
        <h4 class="empty-title" style="font-size:16px; font-weight:800; color:#1e293b; margin-bottom:6px;">Tidak Ada Ujian yang Sedang Dibuka</h4>
        <p class="empty-desc" style="font-size:13px; color:#64748b; max-width:440px; margin:0 auto 16px; line-height:1.5;">
          Saat ini belum ada paket asesmen atau ujian CBT yang dijadwalkan aktif untuk rombel kelas Anda. Silakan tunggu instruksi dari Bapak/Ibu Guru pengajar.
        </p>
        <div>
          <button type="button" onclick="location.reload()" style="display:inline-flex; align-items:center; gap:6px; padding:7px 16px; background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; font-size:12px; font-weight:700; color:#334155; cursor:pointer;">
            <i class="bi bi-arrow-clockwise"></i>
            <span>Segarkan Halaman</span>
          </button>
        </div>
      </div>
    @else
      <div class="exam-grid">
        @foreach($asesmens as $a)
          @php
            $h = $hasils[$a->id] ?? null;
            $isCompleted = $h && $h->is_selesai;
            $hasToken = !empty($a->token_ujian);
          @endphp

          <div class="exam-card">
            <div>
              <div class="exam-card-top">
                <span class="exam-badge-type">{{ $a->jenis_label }}</span>
                @if($isCompleted)
                  <span class="exam-badge-status-done"><i class="bi bi-check2-circle me-1"></i> Selesai Dikerjakan</span>
                @else
                  <span style="font-size:11px; font-weight:800; color:#059669; background:#ecfdf5; padding:3px 8px; border-radius:6px;">
                    <i class="bi bi-broadcast me-1"></i> Ujian Dibuka
                  </span>
                @endif
              </div>

              <h4 class="exam-title">{{ $a->judul }}</h4>
              <div class="exam-mapel">{{ $a->distribusi?->mataPelajaran?->nama_mapel ?? 'Mata Pelajaran' }}</div>

              <div class="exam-meta-specs">
                <div class="meta-spec-item">
                  Durasi: <strong>{{ $a->durasi_menit }} Menit</strong>
                </div>
                <div class="meta-spec-item">
                  Soal: <strong>{{ $a->soals->count() }} Butir</strong>
                </div>
                <div class="meta-spec-item">
                  KKM: <strong>{{ $a->passing_grade }}</strong>
                </div>
                <div class="meta-spec-item">
                  Guru: <strong>{{ $a->distribusi?->guru?->nama ?? 'Guru Mapel' }}</strong>
                </div>
              </div>
            </div>

            <div>
              @if($isCompleted)
                <div class="btn-exam-done">
                  <i class="bi bi-check-circle-fill text-success"></i>
                  @if($a->tampilkan_nilai)
                    <span>Nilai: <strong>{{ $h->nilai }}</strong> ({{ $h->nilai >= $a->passing_grade ? 'Tuntas' : 'Remedial' }})</span>
                  @else
                    <span>Jawaban Telah Terkirim</span>
                  @endif
                </div>
              @else
                <button type="button" class="btn-exam-start" onclick="bukaModalToken({{ $a->id }}, '{{ addslashes($a->judul) }}', {{ $hasToken ? 'true' : 'false' }})">
                  <span>Mulai Kerjakan Asesmen</span>
                  <i class="bi bi-arrow-right"></i>
                </button>
              @endif
            </div>

          </div>
        @endforeach
      </div>
    @endif

  </main>

  {{-- Modal Konfirmasi Token Ujian --}}
  <div id="tokenModal" class="token-modal-backdrop">
    <div class="token-modal-card">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h4 style="font-size:16px; font-weight:800; color:#0f172a;" id="modalExamTitle">Mulai Asesmen</h4>
        <button type="button" onclick="tutupModalToken()" style="background:none; border:none; font-size:20px; color:#94a3b8; cursor:pointer;">&times;</button>
      </div>

      <form id="formToken" method="POST" action="">
        @csrf
        
        <div id="tokenInputContainer" style="margin-bottom:18px;">
          <label style="display:block; font-size:13px; font-weight:800; color:#334155; margin-bottom:6px;">
            Token Ujian <span class="text-danger">*</span>
          </label>
          <input type="text" name="token_ujian" id="inputToken" 
                 placeholder="Contoh: 6 Digit Kode" 
                 maxlength="10"
                 style="width:100%; padding:12px 14px; border:2px solid #cbd5e1; border-radius:10px; font-size:16px; font-weight:800; letter-spacing:2px; text-transform:uppercase; text-align:center;">
          <div style="font-size:11.5px; color:#64748b; margin-top:6px;">
            <i class="bi bi-key-fill text-warning me-1"></i> Tanyakan token kepada Guru Pengawas yang bertugas.
          </div>
        </div>

        <div style="padding:12px; background:#eff6ff; border-radius:8px; margin-bottom:20px; font-size:12px; color:#1e40af; line-height:1.5;">
          <i class="bi bi-shield-lock-fill me-1"></i>
          Ujian ini dilengkapi <strong>Anti-Kecurangan Terpadu</strong>. Dilarang membuka tab/aplikasi lain selama ujian berlangsung.
        </div>

        <div style="display:flex; gap:10px;">
          <button type="button" onclick="tutupModalToken()" style="flex:1; padding:11px; background:#f1f5f9; border:1px solid #cbd5e1; border-radius:8px; font-weight:700; cursor:pointer;">
            Batal
          </button>
          <button type="submit" style="flex:2; padding:11px; background:#2563eb; color:#ffffff; border:none; border-radius:8px; font-weight:800; cursor:pointer;">
            Konfirmasi &amp; Mulai
          </button>
        </div>
      </form>
    </div>
  </div>

  <footer class="footer-bar">
    &copy; {{ date('Y') }} Data Control Center — SMKN 1 Air Naningan. Sistem Asesmen Penilaian Berbasis Online.
  </footer>

  <script>
    function bukaModalToken(asesmenId, judul, hasToken) {
      document.getElementById('modalExamTitle').innerText = judul;
      const form = document.getElementById('formToken');
      form.action = '/asesmen/' + asesmenId + '/buka';

      const tokenBox = document.getElementById('tokenInputContainer');
      const inputToken = document.getElementById('inputToken');

      if (hasToken) {
        tokenBox.style.display = 'block';
        inputToken.required = true;
      } else {
        tokenBox.style.display = 'none';
        inputToken.required = false;
      }

      const modal = document.getElementById('tokenModal');
      modal.style.display = 'flex';
      if (hasToken) {
        setTimeout(() => inputToken.focus(), 100);
      }
    }

    function tutupModalToken() {
      document.getElementById('tokenModal').style.display = 'none';
    }
  </script>

</body>
</html>
