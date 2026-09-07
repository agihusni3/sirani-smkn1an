<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dasbor SITUAN — Sistem Informasi Tata Usaha SMKN 1 Air Naningan</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/situan-app.css') }}?v={{ filemtime(public_path('css/situan-app.css')) }}">
</head>
<body class="situan-body">
<div class="situan-layout">
  @include('partials.sidebar_situan')
  
  <main class="situan-main">
    <div class="situan-content">
      {{-- Page Header --}}
      <div class="situan-page-header">
        <div style="display:flex; align-items:center; gap:12px;">
          <button type="button" class="situan-mobile-menu-btn" onclick="window.toggleSituanSidebar()" aria-label="Buka Menu" style="background:#f8fafc; border:1.5px solid #cbd5e1; border-radius:8px; padding:6px 10px; font-size:17px; cursor:pointer; color:#000000;">
            <i class="bi bi-list"></i>
          </button>
          <div>
            <div class="situan-breadcrumb">
              <a href="{{ route('admin.portal') }}" style="display:inline-flex; align-items:center; gap:4px;"><i class="bi bi-grid-fill" style="color:#0284c7; font-size:12px;"></i> DCC</a>
              <span class="sep">/</span>
              <span style="color:#0284c7; font-weight:800;">SITUAN — Data Induk</span>
            </div>
            <h1 class="situan-page-title" style="margin-top:2px; font-size:20px;">Pusat Administrasi &amp; Tata Usaha</h1>
          </div>
        </div>
        
        <div class="situan-topbar-actions">
          @include('partials.header_actions')
        </div>
      </div>

      {{-- Hero Banner SITUAN --}}
      <div class="situan-hero-card">
        <div>
          <div class="situan-hero-title-row">
            <span>SITUAN — SMKN 1 AN</span>
            <span class="situan-hero-tag">Pusat Data Induk</span>
          </div>
          <p class="situan-hero-text">
            Sistem Informasi Tata Usaha &amp; Administrasi Kelembagaan. Kelola pangkalan data pokok guru, staf tendik, siswa aktif, rombongan belajar, dan legalitas sekolah dalam satu ekosistem terpadu.
          </p>
        </div>
        <div class="situan-hero-btns">
          <a href="/siswa" class="situan-hero-btn-white">
            <i class="bi bi-person-plus-fill"></i> Kelola Siswa
          </a>
          <a href="/guru" class="situan-hero-btn-trans">
            <i class="bi bi-person-badge"></i> Kelola PTK
          </a>
        </div>
      </div>

      {{-- Modern TU Workload & Alerts Bar --}}
      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(230px, 1fr)); gap:14px; margin-bottom:20px;">
        {{-- Alert 1: Surat Masuk & Disposisi --}}
        <a href="{{ route('situan.surat-masuk.index') }}" style="display:flex; align-items:center; gap:12px; padding:14px 16px; background:#ffffff; border:1.5px solid #e2e8f0; border-radius:12px; text-decoration:none; box-shadow:0 1px 3px rgba(0,0,0,0.04); transition:all 0.15s ease;">
          <div style="width:42px; height:42px; border-radius:10px; background:rgba(2,132,199,0.12); color:#0284c7; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;">
            <i class="bi bi-inbox-fill"></i>
          </div>
          <div>
            <div style="font-size:11px; font-weight:800; color:#64748b; text-transform:uppercase;">Surat Masuk &amp; Disposisi</div>
            <div style="font-size:15px; font-weight:900; color:#0f172a; margin-top:2px;">
              {{ $suratMasukPending }} <span style="font-size:12px; font-weight:700; color:{{ $suratMasukPending > 0 ? '#dc2626' : '#10b981' }};">Menunggu Disposisi</span>
            </div>
          </div>
        </a>

        {{-- Alert 2: Agenda Surat Keluar --}}
        <a href="{{ route('situan.surat-keluar.index') }}" style="display:flex; align-items:center; gap:12px; padding:14px 16px; background:#ffffff; border:1.5px solid #e2e8f0; border-radius:12px; text-decoration:none; box-shadow:0 1px 3px rgba(0,0,0,0.04); transition:all 0.15s ease;">
          <div style="width:42px; height:42px; border-radius:10px; background:rgba(16,185,129,0.12); color:#10b981; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;">
            <i class="bi bi-send-fill"></i>
          </div>
          <div>
            <div style="font-size:11px; font-weight:800; color:#64748b; text-transform:uppercase;">Surat Keluar {{ date('Y') }}</div>
            <div style="font-size:15px; font-weight:900; color:#0f172a; margin-top:2px;">
              {{ $suratKeluarTahunIni }} <span style="font-size:12px; font-weight:700; color:#10b981;">No. Terbit Resmi</span>
            </div>
          </div>
        </a>

        {{-- Alert 3: Loket Pelayanan Mandiri Siswa --}}
        <a href="{{ route('situan.pelayanan.index') }}" style="display:flex; align-items:center; gap:12px; padding:14px 16px; background:#ffffff; border:1.5px solid #e2e8f0; border-radius:12px; text-decoration:none; box-shadow:0 1px 3px rgba(0,0,0,0.04); transition:all 0.15s ease;">
          <div style="width:42px; height:42px; border-radius:10px; background:rgba(99,102,241,0.12); color:#6366f1; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;">
            <i class="bi bi-magic"></i>
          </div>
          <div>
            <div style="font-size:11px; font-weight:800; color:#64748b; text-transform:uppercase;">Loket Surat Siswa (QR)</div>
            <div style="font-size:15px; font-weight:900; color:#0f172a; margin-top:2px;">
              {{ $pelayananSiswaTotal }} <span style="font-size:12px; font-weight:700; color:#6366f1;">Suket Diterbitkan</span>
            </div>
          </div>
        </a>

        {{-- Alert 4: Radar KGB & Pangkat --}}
        <a href="{{ route('situan.radar-kgb.index') }}" style="display:flex; align-items:center; gap:12px; padding:14px 16px; background:{{ $radarKgbAlerts > 0 ? '#fff5f5' : '#ffffff' }}; border:1.5px solid {{ $radarKgbAlerts > 0 ? '#fecdd3' : '#e2e8f0' }}; border-radius:12px; text-decoration:none; box-shadow:0 1px 3px rgba(0,0,0,0.04); transition:all 0.15s ease;">
          <div style="width:42px; height:42px; border-radius:10px; background:{{ $radarKgbAlerts > 0 ? 'rgba(239,68,68,0.15)' : 'rgba(16,185,129,0.12)' }}; color:{{ $radarKgbAlerts > 0 ? '#dc2626' : '#10b981' }}; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;">
            <i class="bi bi-broadcast-pin"></i>
          </div>
          <div>
            <div style="font-size:11px; font-weight:800; color:{{ $radarKgbAlerts > 0 ? '#dc2626' : '#64748b' }}; text-transform:uppercase;">Radar KGB &amp; Pangkat</div>
            <div style="font-size:15px; font-weight:900; color:#0f172a; margin-top:2px;">
              {{ $radarKgbAlerts }} <span style="font-size:12px; font-weight:700; color:{{ $radarKgbAlerts > 0 ? '#dc2626' : '#10b981' }};">{{ $radarKgbAlerts > 0 ? 'Perlu Pengusulan' : 'Semua Aman' }}</span>
            </div>
          </div>
        </a>
      </div>

      {{-- 4 Stat Cards --}}
      <div class="situan-grid-4">
        {{-- Card 1: Siswa Aktif --}}
        <div class="situan-stat-box green">
          <div>
            <div class="situan-stat-head">
              <span class="situan-stat-title">Peserta Didik Aktif</span>
              <div class="situan-stat-icon" style="background:rgba(16,185,129,0.12); color:#10b981;">
                <i class="bi bi-people-fill"></i>
              </div>
            </div>
            <div class="situan-stat-val">{{ number_format($totalSiswaAktif) }}</div>
          </div>
          <div class="situan-stat-sub">
            <span><i class="bi bi-gender-male"></i> {{ $totalSiswaLaki }} Laki</span> · 
            <span><i class="bi bi-gender-female"></i> {{ $totalSiswaPerempuan }} Perempuan</span>
            @if($totalSiswaPkl > 0)
              · <span style="color:#d97706; font-weight:800;">{{ $totalSiswaPkl }} PKL</span>
            @endif
          </div>
        </div>

        {{-- Card 2: Guru & Tendik (PTK) --}}
        <div class="situan-stat-box">
          <div>
            <div class="situan-stat-head">
              <span class="situan-stat-title">Pendidik &amp; Tendik (PTK)</span>
              <div class="situan-stat-icon" style="background:rgba(2,132,199,0.12); color:#0284c7;">
                <i class="bi bi-person-badge-fill"></i>
              </div>
            </div>
            <div class="situan-stat-val">{{ number_format($totalGuruAktif) }}</div>
          </div>
          <div class="situan-stat-sub">
            <span>{{ $totalAkunLoginGtk }} Akun Terdaftar</span> · <span style="color:#10b981; font-weight:800;">100% Aktif</span>
          </div>
        </div>

        {{-- Card 3: Rombongan Belajar --}}
        <div class="situan-stat-box amber">
          <div>
            <div class="situan-stat-head">
              <span class="situan-stat-title">Rombel &amp; Tingkat</span>
              <div class="situan-stat-icon" style="background:rgba(217,119,6,0.12); color:#d97706;">
                <i class="bi bi-building"></i>
              </div>
            </div>
            <div class="situan-stat-val">{{ $totalRombel }} <span style="font-size:16px; font-weight:700; color:#475569;">Kelas</span></div>
          </div>
          <div class="situan-stat-sub">
            <span>X: {{ $rombelTingkat10 }}</span> · <span>XI: {{ $rombelTingkat11 }}</span> · <span>XII: {{ $rombelTingkat12 }}</span>
          </div>
        </div>

        {{-- Card 4: Tahun Ajaran --}}
        <div class="situan-stat-box purple">
          <div>
            <div class="situan-stat-head">
              <span class="situan-stat-title">Tahun Ajaran Aktif</span>
              <div class="situan-stat-icon" style="background:rgba(99,102,241,0.12); color:#6366f1;">
                <i class="bi bi-calendar-range-fill"></i>
              </div>
            </div>
            <div class="situan-stat-val" style="font-size:22px;">{{ $tahunAjaranAktif ? $tahunAjaranAktif->nama : '2025/2026' }}</div>
          </div>
          <div class="situan-stat-sub">
            <span style="color:#6366f1; font-weight:800;">Semester {{ $tahunAjaranAktif ? ucfirst($tahunAjaranAktif->semester ?? 'Aktif') : 'Ganjil' }}</span>
          </div>
        </div>
      </div>

      {{-- Dual Panel: Audit Kelengkapan Data & Aksi Cepat Tata Usaha --}}
      <div class="situan-dual-grid">
        {{-- Panel Kiri: Audit Kelengkapan Data Pokok Siswa --}}
        <div class="situan-panel">
          <div class="situan-panel-header">
            <div class="situan-panel-title">
              <i class="bi bi-shield-check" style="color:#0284c7; font-size:18px;"></i>
              <span>Audit Kelengkapan Data Pokok Siswa</span>
            </div>
            <span style="font-size:11px; font-weight:800; color:#000000; background:#f1f5f9; padding:2px 8px; border-radius:999px; border:1px solid #cbd5e1;">Standar Dapodik</span>
          </div>

          <div>
            {{-- Progress 1: NISN --}}
            <div class="situan-audit-item">
              <div class="situan-audit-label">
                <span>Nomor Induk Siswa Nasional (NISN)</span>
                <span>{{ $persenNisn }}% <span style="font-size:11px; font-weight:700; color:#475569;">({{ $siswaDenganNisn }}/{{ $totalSiswaAktif }})</span></span>
              </div>
              <div class="situan-audit-bar">
                <div class="situan-audit-fill" style="width:{{ $persenNisn }}%; background:#10b981;"></div>
              </div>
            </div>

            {{-- Progress 2: No HP / WA Orang Tua --}}
            <div class="situan-audit-item">
              <div class="situan-audit-label">
                <span>Kontak WhatsApp Orang Tua (Kanal Notifikasi)</span>
                <span>{{ $persenNoOrtu }}% <span style="font-size:11px; font-weight:700; color:#475569;">({{ $siswaDenganNoOrtu }}/{{ $totalSiswaAktif }})</span></span>
              </div>
              <div class="situan-audit-bar">
                <div class="situan-audit-fill" style="width:{{ $persenNoOrtu }}%; background:#0284c7;"></div>
              </div>
            </div>

            {{-- Progress 3: Kartu RFID / Smart Gate --}}
            <div class="situan-audit-item">
              <div class="situan-audit-label">
                <span>Registrasi Kartu RFID Smart Gate</span>
                <span>{{ $persenRfid }}% <span style="font-size:11px; font-weight:700; color:#475569;">({{ $siswaDenganRfid }}/{{ $totalSiswaAktif }})</span></span>
              </div>
              <div class="situan-audit-bar">
                <div class="situan-audit-fill" style="width:{{ $persenRfid }}%; background:#d97706;"></div>
              </div>
            </div>

            {{-- Progress 4: Foto Profil Siswa --}}
            <div class="situan-audit-item" style="margin-bottom:0;">
              <div class="situan-audit-label">
                <span>Pasfoto Siswa</span>
                <span>{{ $persenFoto }}% <span style="font-size:11px; font-weight:700; color:#475569;">({{ $siswaDenganFoto }}/{{ $totalSiswaAktif }})</span></span>
              </div>
              <div class="situan-audit-bar">
                <div class="situan-audit-fill" style="width:{{ $persenFoto }}%; background:#6366f1;"></div>
              </div>
            </div>
          </div>

          {{-- Aksi Cepat Tata Usaha --}}
          <div style="margin-top:22px; padding-top:16px; border-top:1.5px solid var(--situan-border-subtle);">
            <div style="font-size:13px; font-weight:900; color:#000000; margin-bottom:10px;">
              Aksi Cepat Administrasi Tata Usaha
            </div>
            <div class="situan-quick-grid">
              <a href="{{ route('situan.surat-masuk.index') }}" class="situan-quick-card">
                <div class="situan-quick-icon" style="background:rgba(2,132,199,0.12); color:#0284c7;">
                  <i class="bi bi-inbox"></i>
                </div>
                <div class="situan-quick-name">Surat Masuk</div>
                <div class="situan-quick-desc">Disposisi Digital Kepsek</div>
              </a>

              <a href="{{ route('situan.surat-keluar.index') }}" class="situan-quick-card">
                <div class="situan-quick-icon" style="background:rgba(16,185,129,0.12); color:#10b981;">
                  <i class="bi bi-send"></i>
                </div>
                <div class="situan-quick-name">Surat Keluar</div>
                <div class="situan-quick-desc">No. Otomatis Kemendikdasmen</div>
              </a>

              <a href="{{ route('situan.pelayanan.index') }}" class="situan-quick-card">
                <div class="situan-quick-icon" style="background:rgba(99,102,241,0.12); color:#6366f1;">
                  <i class="bi bi-magic"></i>
                </div>
                <div class="situan-quick-name">Surat Siswa</div>
                <div class="situan-quick-desc">Suket Aktif &amp; Mutasi (QR)</div>
              </a>

              <a href="{{ route('situan.radar-kgb.index') }}" class="situan-quick-card">
                <div class="situan-quick-icon" style="background:rgba(239,68,68,0.12); color:#ef4444;">
                  <i class="bi bi-broadcast-pin"></i>
                </div>
                <div class="situan-quick-name">Radar KGB</div>
                <div class="situan-quick-desc">Kenaikan Gaji Berkala &amp; Pangkat</div>
              </a>

              <a href="{{ route('situan.buku-sk.index') }}" class="situan-quick-card">
                <div class="situan-quick-icon" style="background:rgba(217,119,6,0.12); color:#d97706;">
                  <i class="bi bi-file-earmark-lock"></i>
                </div>
                <div class="situan-quick-name">Register SK</div>
                <div class="situan-quick-desc">Buku SK Kepala Sekolah</div>
              </a>

              <a href="/guru" class="situan-quick-card">
                <div class="situan-quick-icon" style="background:rgba(2,132,199,0.12); color:#0284c7;">
                  <i class="bi bi-person-badge"></i>
                </div>
                <div class="situan-quick-name">Data Pegawai</div>
                <div class="situan-quick-desc">Kelola GTK &amp; Akun Login</div>
              </a>

              <a href="/siswa" class="situan-quick-card">
                <div class="situan-quick-icon" style="background:rgba(16,185,129,0.12); color:#10b981;">
                  <i class="bi bi-people"></i>
                </div>
                <div class="situan-quick-name">Data Siswa</div>
                <div class="situan-quick-desc">Biodata, NISN &amp; Ortu</div>
              </a>

              <a href="/rombel" class="situan-quick-card">
                <div class="situan-quick-icon" style="background:rgba(217,119,6,0.12); color:#d97706;">
                  <i class="bi bi-building"></i>
                </div>
                <div class="situan-quick-name">Rombel &amp; TA</div>
                <div class="situan-quick-desc">Penataan Kelas &amp; Jurusan</div>
              </a>

              <a href="/siklus-siswa" class="situan-quick-card">
                <div class="situan-quick-icon" style="background:rgba(99,102,241,0.12); color:#6366f1;">
                  <i class="bi bi-arrow-repeat"></i>
                </div>
                <div class="situan-quick-name">Siklus Akademik</div>
                <div class="situan-quick-desc">Kenaikan Kelas &amp; Alumni</div>
              </a>

              <a href="/kartu-rfid" class="situan-quick-card">
                <div class="situan-quick-icon" style="background:rgba(14,165,233,0.12); color:#0ea5e9;">
                  <i class="bi bi-person-vcard"></i>
                </div>
                <div class="situan-quick-name">Cetak Kartu</div>
                <div class="situan-quick-desc">Barcode &amp; Pairing RFID</div>
              </a>

              @if($user && $user->isAdmin())
                <a href="/pengaturan-sekolah" class="situan-quick-card">
                  <div class="situan-quick-icon" style="background:rgba(71,85,105,0.12); color:#334155;">
                    <i class="bi bi-bank2"></i>
                  </div>
                  <div class="situan-quick-name">Profil Sekolah</div>
                  <div class="situan-quick-desc">Kop Surat &amp; Data Legalitas</div>
                </a>
              @else
                <a href="{{ route('admin.portal') }}" class="situan-quick-card">
                  <div class="situan-quick-icon" style="background:rgba(2,132,199,0.12); color:#0284c7;">
                    <i class="bi bi-command"></i>
                  </div>
                  <div class="situan-quick-name">Pusat DCC</div>
                  <div class="situan-quick-desc">Kembali ke Komando</div>
                </a>
              @endif
            </div>
          </div>
        </div>

        {{-- Panel Kanan: Rombel per Program Keahlian & Riwayat Mutasi --}}
        <div style="display:flex; flex-direction:column; gap:20px;">
          {{-- Program Keahlian / Jurusan --}}
          <div class="situan-panel">
            <div class="situan-panel-header">
              <div class="situan-panel-title">
                <i class="bi bi-mortarboard" style="color:#d97706; font-size:18px;"></i>
                <span>Program Keahlian (Jurusan)</span>
              </div>
              <a href="/rombel" style="font-size:12px; color:#0284c7; font-weight:800; text-decoration:none;">Kelola</a>
            </div>

            <div style="display:flex; flex-direction:column; gap:10px;">
              @foreach($jurusans as $j)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:11px 14px; border-radius:10px; background:#f8fafc; border:1.5px solid var(--situan-border);">
                  <div>
                    <div style="font-weight:900; font-size:13px; color:#000000;">{{ $j->nama_jurusan }}</div>
                    <div style="font-size:11px; color:#475569; font-weight:700; font-family:ui-monospace, monospace; margin-top:2px;">Kode: {{ $j->kode_jurusan }}</div>
                  </div>
                  <span style="background:rgba(2,132,199,0.12); color:#0284c7; border:1px solid rgba(2,132,199,0.25); font-weight:900; font-size:11.5px; padding:3px 9px; border-radius:6px;">
                    {{ $j->rombels_count }} Rombel
                  </span>
                </div>
              @endforeach
            </div>
          </div>

          {{-- Log Mutasi & Aktivitas Tata Usaha --}}
          <div class="situan-panel" style="flex:1;">
            <div class="situan-panel-header">
              <div class="situan-panel-title">
                <i class="bi bi-clock-history" style="color:#6366f1; font-size:18px;"></i>
                <span>Aktivitas Mutasi Terakhir</span>
              </div>
              <a href="/audit" style="font-size:12px; color:#0284c7; font-weight:800; text-decoration:none;">Lihat Semua</a>
            </div>

            @if($recentAuditLogs->isEmpty())
              <div style="text-align:center; padding:24px 0; color:#64748b; font-size:13px; font-weight:600;">
                Belum ada riwayat aktivitas tata usaha tercatat.
              </div>
            @else
              <div style="overflow-x:auto;">
                <table class="situan-audit-table">
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
                        <td style="white-space:nowrap; font-size:11px; color:#475569; font-weight:700;">
                          {{ $log->created_at->diffForHumans() }}
                        </td>
                        <td>
                          <span style="font-size:10px; font-weight:800; padding:2px 7px; border-radius:4px; background:#f1f5f9; border:1px solid #cbd5e1; color:#000000;">{{ strtoupper($log->aksi) }}</span>
                        </td>
                        <td style="max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-weight:600;">
                          {{ $log->deskripsi }}
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
  </main>
</div>
</body>
</html>
