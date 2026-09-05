<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dasbor SITUAN — Sistem Informasi Tata Usaha SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <style>
    .situan-hero {
      background: linear-gradient(135deg, #0c4a6e 0%, #0369a1 40%, #0284c7 100%);
      border-radius: 18px;
      padding: 24px 28px;
      color: #ffffff;
      margin-bottom: 22px;
      box-shadow: 0 12px 28px rgba(2, 132, 199, 0.2);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
      border: 1px solid rgba(255, 255, 255, 0.15);
      position: relative;
      overflow: hidden;
    }

    .situan-hero::after {
      content: '';
      position: absolute;
      right: -30px;
      top: -30px;
      width: 180px;
      height: 180px;
      background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
      pointer-events: none;
    }

    .situan-hero-title {
      font-size: 22px;
      font-weight: 900;
      letter-spacing: -0.03em;
      margin-bottom: 4px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .situan-hero-badge {
      font-size: 10.5px;
      font-weight: 800;
      letter-spacing: 0.06em;
      padding: 3px 10px;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.3);
      text-transform: uppercase;
    }

    .situan-hero-desc {
      font-size: 13px;
      color: rgba(255, 255, 255, 0.85);
      max-width: 600px;
      line-height: 1.5;
    }

    .situan-hero-actions {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .btn-hero-action {
      background: #ffffff;
      color: #0369a1;
      font-size: 12.5px;
      font-weight: 800;
      padding: 9px 16px;
      border-radius: 10px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      transition: all 0.2s ease;
    }

    .btn-hero-action:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
      color: #0c4a6e;
    }

    /* ─── Grid Statistik ─── */
    .situan-stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
      margin-bottom: 22px;
    }

    .stat-card-situan {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 18px 20px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
      overflow: hidden;
      transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .stat-card-situan:hover {
      transform: translateY(-3px);
      border-color: rgba(2, 132, 199, 0.4);
    }

    .stat-card-situan::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 4px;
      height: 100%;
      background: #0284c7;
    }

    .stat-card-situan.green::before { background: #10b981; }
    .stat-card-situan.amber::before { background: #d97706; }
    .stat-card-situan.purple::before { background: #6366f1; }

    .stat-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 10px;
    }

    .stat-title {
      font-size: 12px;
      font-weight: 700;
      color: var(--text-3);
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    .stat-icon-wrap {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
    }

    .stat-val {
      font-size: 26px;
      font-weight: 900;
      color: var(--text);
      letter-spacing: -0.03em;
      line-height: 1.1;
      margin-bottom: 4px;
    }

    .stat-sub {
      font-size: 11.5px;
      color: var(--text-2);
      font-weight: 600;
    }

    /* ─── Dual Panel Layout ─── */
    .situan-dual-grid {
      display: grid;
      grid-template-columns: 3fr 2fr;
      gap: 20px;
      margin-bottom: 22px;
    }

    .panel-card {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 18px;
      padding: 22px 24px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
    }

    .panel-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 18px;
      padding-bottom: 12px;
      border-bottom: 1px solid var(--border);
    }

    .panel-title {
      font-size: 15px;
      font-weight: 800;
      color: var(--text);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* ─── Progress Item Kelengkapan Data ─── */
    .audit-progress-item {
      margin-bottom: 14px;
    }

    .audit-progress-label {
      display: flex;
      justify-content: space-between;
      font-size: 12.5px;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 6px;
    }

    .audit-progress-bar {
      height: 8px;
      background: var(--surface-2, rgba(0, 0, 0, 0.06));
      border-radius: 999px;
      overflow: hidden;
    }

    .audit-progress-fill {
      height: 100%;
      border-radius: 999px;
      transition: width 0.6s ease;
    }

    /* ─── Action Cards ─── */
    .quick-actions-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 14px;
      margin-top: 16px;
    }

    .quick-action-btn {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 16px;
      text-decoration: none;
      color: var(--text);
      display: flex;
      flex-direction: column;
      gap: 8px;
      transition: all 0.2s ease;
    }

    .quick-action-btn:hover {
      transform: translateY(-2px);
      border-color: #0284c7;
      box-shadow: 0 6px 16px rgba(2, 132, 199, 0.08);
    }

    .quick-action-icon {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      margin-bottom: 2px;
    }

    .quick-action-name {
      font-size: 13px;
      font-weight: 800;
      color: var(--text);
    }

    .quick-action-sub {
      font-size: 11px;
      color: var(--text-3);
      line-height: 1.3;
    }

    /* ─── Audit Log Table ─── */
    .audit-mini-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 12px;
    }

    .audit-mini-table th {
      text-align: left;
      padding: 8px 10px;
      color: var(--text-3);
      font-weight: 700;
      border-bottom: 1px solid var(--border);
    }

    .audit-mini-table td {
      padding: 10px 10px;
      border-bottom: 1px solid var(--border);
      color: var(--text-2);
    }

    .audit-mini-table tr:last-child td {
      border-bottom: none;
    }

    @media (max-width: 1100px) {
      .situan-stats-grid { grid-template-columns: repeat(2, 1fr); }
      .situan-dual-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
      .situan-stats-grid { grid-template-columns: 1fr; }
      .quick-actions-grid { grid-template-columns: 1fr; }
      .situan-hero { padding: 20px; }
      .situan-hero-title { font-size: 19px; }
    }
  </style>
</head>
<body>
<div class="app-container">
  @include('partials.sidebar_situan')
  
  <main class="main-content">
    {{-- Header Actions Bar --}}
    <header class="topbar no-print" style="margin-bottom:20px; display:flex; justify-content:space-between; align-items:center;">
      <div style="display:flex; align-items:center; gap:10px;">
        <span style="font-size:12px; font-weight:700; color:var(--text-3);">
          <a href="{{ route('admin.portal') }}" style="color:var(--text-3); text-decoration:none;"><i class="bi bi-command"></i> DCC</a>
          <span style="margin:0 4px;">/</span>
          <span style="color:#0284c7;">SITUAN — Data Pokok</span>
        </span>
      </div>
      @include('partials.header_actions')
    </header>

    {{-- Hero Banner SITUAN --}}
    <div class="situan-hero">
      <div>
        <div class="situan-hero-title">
          <span>SITUAN — SMKN 1 AN</span>
          <span class="situan-hero-badge">Pusat Data Induk</span>
        </div>
        <p class="situan-hero-desc">
          Sistem Informasi Tata Usaha &amp; Administrasi Kelembagaan. Kelola pangkalan data pokok guru, staf tendik, siswa aktif, rombongan belajar, dan legalitas sekolah.
        </p>
      </div>
      <div class="situan-hero-actions">
        <a href="/siswa" class="btn-hero-action">
          <i class="bi bi-person-plus-fill"></i> Kelola Siswa
        </a>
        <a href="/guru" class="btn-hero-action" style="background:rgba(255,255,255,0.15); color:#ffffff; border:1px solid rgba(255,255,255,0.3);">
          <i class="bi bi-person-badge"></i> Kelola PTK
        </a>
      </div>
    </div>

    {{-- 4 Stat Cards --}}
    <div class="situan-stats-grid">
      {{-- Card 1: Siswa Aktif --}}
      <div class="stat-card-situan green">
        <div>
          <div class="stat-header">
            <span class="stat-title">Peserta Didik Aktif</span>
            <div class="stat-icon-wrap" style="background:rgba(16,185,129,0.1); color:#10b981;">
              <i class="bi bi-people-fill"></i>
            </div>
          </div>
          <div class="stat-val">{{ number_format($totalSiswaAktif) }}</div>
        </div>
        <div class="stat-sub">
          <span><i class="bi bi-gender-male"></i> {{ $totalSiswaLaki }} Laki</span> · 
          <span><i class="bi bi-gender-female"></i> {{ $totalSiswaPerempuan }} Perempuan</span>
          @if($totalSiswaPkl > 0)
            · <span style="color:#d97706;">{{ $totalSiswaPkl }} PKL</span>
          @endif
        </div>
      </div>

      {{-- Card 2: Guru & Tendik (PTK) --}}
      <div class="stat-card-situan">
        <div>
          <div class="stat-header">
            <span class="stat-title">Pendidik &amp; Tendik (PTK)</span>
            <div class="stat-icon-wrap" style="background:rgba(2,132,199,0.1); color:#0284c7;">
              <i class="bi bi-person-badge-fill"></i>
            </div>
          </div>
          <div class="stat-val">{{ number_format($totalGuruAktif) }}</div>
        </div>
        <div class="stat-sub">
          <span>{{ $totalAkunLoginGtk }} Akun Terdaftar</span> · <span>100% Aktif</span>
        </div>
      </div>

      {{-- Card 3: Rombongan Belajar --}}
      <div class="stat-card-situan amber">
        <div>
          <div class="stat-header">
            <span class="stat-title">Rombel &amp; Tingkat</span>
            <div class="stat-icon-wrap" style="background:rgba(217,119,6,0.1); color:#d97706;">
              <i class="bi bi-building"></i>
            </div>
          </div>
          <div class="stat-val">{{ $totalRombel }} <span style="font-size:15px; font-weight:700; color:var(--text-3);">Kelas</span></div>
        </div>
        <div class="stat-sub">
          <span>Kls X: {{ $rombelTingkat10 }}</span> · <span>Kls XI: {{ $rombelTingkat11 }}</span> · <span>Kls XII: {{ $rombelTingkat12 }}</span>
        </div>
      </div>

      {{-- Card 4: Tahun Ajaran --}}
      <div class="stat-card-situan purple">
        <div>
          <div class="stat-header">
            <span class="stat-title">Tahun Ajaran Aktif</span>
            <div class="stat-icon-wrap" style="background:rgba(99,102,241,0.1); color:#6366f1;">
              <i class="bi bi-calendar-range-fill"></i>
            </div>
          </div>
          <div class="stat-val" style="font-size:21px;">{{ $tahunAjaranAktif ? $tahunAjaranAktif->nama : '2025/2026' }}</div>
        </div>
        <div class="stat-sub">
          <span style="color:#6366f1; font-weight:800;">Semester {{ $tahunAjaranAktif ? ucfirst($tahunAjaranAktif->semester ?? 'Aktif') : 'Ganjil' }}</span>
        </div>
      </div>
    </div>

    {{-- Dual Panel: Audit Kelengkapan Data & Aksi Cepat Tata Usaha --}}
    <div class="situan-dual-grid">
      {{-- Panel Kiri: Audit Kelengkapan Data Pokok --}}
      <div class="panel-card">
        <div class="panel-head">
          <div class="panel-title">
            <i class="bi bi-shield-check" style="color:#0284c7;"></i>
            <span>Audit Kelengkapan Data Pokok Siswa</span>
          </div>
          <span style="font-size:11px; font-weight:700; color:var(--text-3);">Standar Dapodik</span>
        </div>

        <div>
          {{-- Progress 1: NISN --}}
          <div class="audit-progress-item">
            <div class="audit-progress-label">
              <span>Nomor Induk Siswa Nasional (NISN)</span>
              <span>{{ $persenNisn }}% <span style="font-size:11px; font-weight:600; color:var(--text-3);">({{ $siswaDenganNisn }}/{{ $totalSiswaAktif }})</span></span>
            </div>
            <div class="audit-progress-bar">
              <div class="audit-progress-fill" style="width:{{ $persenNisn }}%; background:#10b981;"></div>
            </div>
          </div>

          {{-- Progress 2: No HP / WA Orang Tua --}}
          <div class="audit-progress-item">
            <div class="audit-progress-label">
              <span>Kontak WhatsApp Orang Tua (Kanal Notifikasi)</span>
              <span>{{ $persenNoOrtu }}% <span style="font-size:11px; font-weight:600; color:var(--text-3);">({{ $siswaDenganNoOrtu }}/{{ $totalSiswaAktif }})</span></span>
            </div>
            <div class="audit-progress-bar">
              <div class="audit-progress-fill" style="width:{{ $persenNoOrtu }}%; background:#0284c7;"></div>
            </div>
          </div>

          {{-- Progress 3: Kartu RFID / Smart Gate --}}
          <div class="audit-progress-item">
            <div class="audit-progress-label">
              <span>Registrasi Kartu RFID Smart Gate</span>
              <span>{{ $persenRfid }}% <span style="font-size:11px; font-weight:600; color:var(--text-3);">({{ $siswaDenganRfid }}/{{ $totalSiswaAktif }})</span></span>
            </div>
            <div class="audit-progress-bar">
              <div class="audit-progress-fill" style="width:{{ $persenRfid }}%; background:#d97706;"></div>
            </div>
          </div>

          {{-- Progress 4: Foto Profil Siswa --}}
          <div class="audit-progress-item" style="margin-bottom:0;">
            <div class="audit-progress-label">
              <span>Pasfoto Siswa</span>
              <span>{{ $persenFoto }}% <span style="font-size:11px; font-weight:600; color:var(--text-3);">({{ $siswaDenganFoto }}/{{ $totalSiswaAktif }})</span></span>
            </div>
            <div class="audit-progress-bar">
              <div class="audit-progress-fill" style="width:{{ $persenFoto }}%; background:#6366f1;"></div>
            </div>
          </div>
        </div>

        {{-- Aksi Cepat Tata Usaha --}}
        <div style="margin-top:22px; padding-top:16px; border-top:1px solid var(--border);">
          <div style="font-size:13px; font-weight:800; color:var(--text); margin-bottom:12px;">
            Aksi Cepat Administrasi Tata Usaha
          </div>
          <div class="quick-actions-grid">
            <a href="/guru" class="quick-action-btn">
              <div class="quick-action-icon" style="background:rgba(2,132,199,0.1); color:#0284c7;">
                <i class="bi bi-person-badge"></i>
              </div>
              <div class="quick-action-name">Data Pegawai</div>
              <div class="quick-action-sub">Kelola GTK &amp; Akun Login</div>
            </a>

            <a href="/siswa" class="quick-action-btn">
              <div class="quick-action-icon" style="background:rgba(16,185,129,0.1); color:#10b981;">
                <i class="bi bi-people"></i>
              </div>
              <div class="quick-action-name">Data Siswa</div>
              <div class="quick-action-sub">Biodata, NISN &amp; Ortu</div>
            </a>

            <a href="/rombel" class="quick-action-btn">
              <div class="quick-action-icon" style="background:rgba(217,119,6,0.1); color:#d97706;">
                <i class="bi bi-building"></i>
              </div>
              <div class="quick-action-name">Rombel &amp; TA</div>
              <div class="quick-action-sub">Penataan Kelas &amp; Jurusan</div>
            </a>

            <a href="/siklus-siswa" class="quick-action-btn">
              <div class="quick-action-icon" style="background:rgba(99,102,241,0.1); color:#6366f1;">
                <i class="bi bi-arrow-repeat"></i>
              </div>
              <div class="quick-action-name">Siklus Akademik</div>
              <div class="quick-action-sub">Kenaikan Kelas &amp; Alumni</div>
            </a>

            <a href="/kartu-rfid" class="quick-action-btn">
              <div class="quick-action-icon" style="background:rgba(14,165,233,0.1); color:#0ea5e9;">
                <i class="bi bi-person-vcard"></i>
              </div>
              <div class="quick-action-name">Cetak Kartu</div>
              <div class="quick-action-sub">Barcode &amp; Pairing RFID</div>
            </a>

            @if($user && $user->isAdmin())
              <a href="/pengaturan-sekolah" class="quick-action-btn">
                <div class="quick-action-icon" style="background:rgba(71,85,105,0.1); color:#475569;">
                  <i class="bi bi-bank2"></i>
                </div>
                <div class="quick-action-name">Profil Sekolah</div>
                <div class="quick-action-sub">Kop Surat &amp; Data Legalitas</div>
              </a>
            @else
              <a href="{{ route('admin.portal') }}" class="quick-action-btn">
                <div class="quick-action-icon" style="background:rgba(56,189,248,0.1); color:#0284c7;">
                  <i class="bi bi-command"></i>
                </div>
                <div class="quick-action-name">Pusat DCC</div>
                <div class="quick-action-sub">Kembali ke Komando</div>
              </a>
            @endif
          </div>
        </div>
      </div>

      {{-- Panel Kanan: Rombel per Program Keahlian & Riwayat Mutasi --}}
      <div style="display:flex; flex-direction:column; gap:20px;">
        {{-- Program Keahlian / Jurusan --}}
        <div class="panel-card">
          <div class="panel-head">
            <div class="panel-title">
              <i class="bi bi-mortarboard" style="color:#d97706;"></i>
              <span>Program Keahlian (Jurusan)</span>
            </div>
            <a href="/rombel" style="font-size:11.5px; color:#0284c7; font-weight:700; text-decoration:none;">Kelola</a>
          </div>

          <div style="display:flex; flex-direction:column; gap:10px;">
            @foreach($jurusans as $j)
              <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 12px; border-radius:10px; background:var(--surface); border:1px solid var(--border);">
                <div>
                  <div style="font-weight:800; font-size:13px; color:var(--text);">{{ $j->nama_jurusan }}</div>
                  <div style="font-size:11px; color:var(--text-3); font-weight:600;">Kode: {{ $j->kode_jurusan }}</div>
                </div>
                <span class="badge" style="background:#e0f2fe; color:#0284c7; border:1px solid #bae6fd; font-weight:800; font-size:11px;">
                  {{ $j->rombels_count }} Rombel
                </span>
              </div>
            @endforeach
          </div>
        </div>

        {{-- Log Mutasi & Aktivitas Tata Usaha --}}
        <div class="panel-card" style="flex:1;">
          <div class="panel-head">
            <div class="panel-title">
              <i class="bi bi-clock-history" style="color:#6366f1;"></i>
              <span>Aktivitas Mutasi Terakhir</span>
            </div>
            <a href="/audit" style="font-size:11.5px; color:#0284c7; font-weight:700; text-decoration:none;">Lihat Semua</a>
          </div>

          @if($recentAuditLogs->isEmpty())
            <div style="text-align:center; padding:24px 0; color:var(--text-3); font-size:12.5px;">
              Belum ada riwayat aktivitas tata usaha tercatat.
            </div>
          @else
            <table class="audit-mini-table">
              <thead>
                <tr>
                  <th>Waktu</th>
                  <th>Aksi</th>
                  <th>Keterangan</th>
                </tr>
              </thead>
              <tbody>
                @foreach($recentAuditLogs as $log)
                  <tr>
                    <td style="white-space:nowrap; font-size:11px; color:var(--text-3);">
                      {{ $log->created_at->diffForHumans() }}
                    </td>
                    <td>
                      <span class="badge" style="font-size:10px; font-weight:700;">{{ strtoupper($log->aksi) }}</span>
                    </td>
                    <td style="max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                      {{ $log->deskripsi }}
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>
    </div>
  </main>
</div>
</body>
</html>
