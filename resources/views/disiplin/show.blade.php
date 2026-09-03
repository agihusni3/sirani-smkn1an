<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dossier Kesiswaan: {{ $siswa->nama }} — SIRANI</title>
  @include('partials.styles')
  <style>
    .dossier-hero {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      padding: 20px 22px;
      margin-bottom: 22px;
      box-shadow: var(--shadow-sm);
    }
    .dossier-hero-top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
      padding-bottom: 16px;
      border-bottom: 1px solid var(--border-2);
    }
    .dossier-hero-bottom {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
      padding-top: 14px;
    }
    .dossier-grid {
      display: grid;
      grid-template-columns: 1fr 340px;
      gap: 20px;
      align-items: start;
    }
    @media (max-width: 1024px) {
      .dossier-grid { grid-template-columns: 1fr; }
    }

    /* ─── Timeline Styling ─── */
    .timeline-container {
      position: relative;
      padding-left: 24px;
    }
    .timeline-container::before {
      content: '';
      position: absolute;
      left: 7px;
      top: 4px;
      bottom: 4px;
      width: 2px;
      background: var(--border-2);
    }
    .timeline-item {
      position: relative;
      margin-bottom: 16px;
    }
    .timeline-item:last-child {
      margin-bottom: 0;
    }
    .timeline-dot {
      position: absolute;
      left: -24px;
      top: 6px;
      width: 16px;
      height: 16px;
      border-radius: 50%;
      background: #000000;
      border: 2px solid var(--bg-2);
      box-shadow: 0 0 0 2px var(--border-2);
    }
    .timeline-card {
      background: var(--bg-3);
      border: 1px solid var(--border);
      border-radius: var(--r-sm);
      padding: 12px 14px;
      transition: all .15s ease;
    }
    .timeline-card:hover {
      border-color: var(--border-2);
    }

    /* ─── Evidence Vault Grid ─── */
    .vault-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 12px;
    }
    .vault-card {
      background: var(--bg-3);
      border: 1px solid var(--border);
      border-radius: var(--r-sm);
      padding: 12px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: all .15s ease;
    }
    .vault-card:hover {
      border-color: var(--border-2);
    }

    /* ─── Standard Panel & Subheaders ─── */
    .panel-section {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      padding: 18px 20px;
      margin-bottom: 20px;
      box-shadow: var(--shadow-sm);
    }
    .panel-section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
      padding-bottom: 10px;
      border-bottom: 1px solid var(--border);
    }
    .panel-section-title {
      font-size: 13.5px;
      font-weight: 800;
      color: var(--text);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* ─── Score Row Metrics ─── */
    .metric-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 6px 0;
      font-size: 12px;
      border-bottom: 1px dashed var(--border);
    }
    .metric-row:last-child {
      border-bottom: none;
      padding-bottom: 0;
    }
  </style>
</head>
<body>

<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    {{-- BREADCRUMB & HEADER --}}
    <header class="header" style="margin-bottom:16px;">
      <div class="header-title">
        <div style="display:flex; align-items:center; gap:6px; font-size:11.5px; font-weight:700; color:var(--text-3); margin-bottom:4px;">
          <a href="{{ route('admin.disiplin.index') }}" style="color:var(--text-2); text-decoration:none;">Buku Kasus</a>
          <i class="bi bi-chevron-right" style="font-size:9px; color:var(--text-3);"></i>
          <span style="color:var(--text); font-weight:800;">Dossier Rekam Jejak Siswa</span>
        </div>
        <h1 style="margin:0; font-size:22px; font-weight:900; color:var(--text); display:flex; align-items:center; gap:8px;">
          <i class="bi bi-journal-bookmark-fill" style="color:#000000;"></i> {{ $siswa->nama }}
        </h1>
        <p style="margin-top:2px; font-size:12.5px; color:var(--text-3); font-weight:500;">
          Portofolio Digital Pembinaan Kedisiplinan &amp; Riwayat Interaksi Kesiswaan Terpadu.
        </p>
      </div>
      @include('partials.header_actions')
    </header>

    @if(session('success'))
      <div class="alert-success" style="margin-bottom:16px;">
        <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="alert-error" style="margin-bottom:16px;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}
      </div>
    @endif

    {{-- DOSSIER HERO CARD --}}
    @php
      $rombelAktif = $siswa->siswaRombels->where('status_keanggotaan', 'aktif')->first()?->rombel;
      $wali = $rombelAktif?->waliKelas;
      $cleanHpOrtu = $siswa->nomor_hp_ortu ? preg_replace('/[^0-9]/', '', $siswa->nomor_hp_ortu) : null;
      if ($cleanHpOrtu && str_starts_with($cleanHpOrtu, '0')) {
        $cleanHpOrtu = '62' . substr($cleanHpOrtu, 1);
      }
    @endphp
    <div class="dossier-hero">
      {{-- ATAS: IDENTITAS SISWA & TOMBOL DOKUMEN / ESKALASI --}}
      <div class="dossier-hero-top">
        <div style="display:flex; gap:16px; align-items:center;">
          <div style="width:54px; height:54px; border-radius:12px; background:var(--bg-3); border:1.5px solid var(--border-2); display:flex; align-items:center; justify-content:center; font-weight:900; font-size:22px; color:var(--text); overflow:hidden; flex-shrink:0;">
            @if($siswa->foto)
              <img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama }}" style="width:100%; height:100%; object-fit:cover;" />
            @else
              {{ substr($siswa->nama, 0, 1) }}
            @endif
          </div>

          <div>
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px; flex-wrap:wrap;">
              <h2 style="font-size:20px; font-weight:900; color:var(--text); margin:0; line-height:1.2;">{{ $siswa->nama }}</h2>
              {!! $kasus->badge_tahap !!}
            </div>
            
            <div style="font-size:12.5px; color:var(--text-2); display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
              <span>NISN: <strong style="color:var(--text); font-family:var(--font-mono); font-weight:700;">{{ $siswa->nisn ?: '-' }}</strong></span>
              <span style="color:var(--border-2);">•</span>
              <span>Kelas: <strong style="color:var(--text); font-weight:700;">{{ $rombelAktif->nama_rombel ?? '-' }}</strong></span>
              <span style="color:var(--border-2);">•</span>
              <span>Wali Kelas: <strong style="color:var(--text); font-weight:700;">{{ $wali->nama ?? 'Belum Ditentukan' }}</strong></span>
            </div>
          </div>
        </div>

        @php
          $canProcessStage = $user->isAdmin()
            || ($kasus->status_tahap === 'tahap_1_wali_kelas' && $user->isWaliKelas())
            || ($kasus->status_tahap === 'tahap_2_bk' && $user->isGuruBk())
            || ($kasus->status_tahap === 'tahap_3_wakasis' && $user->isWakaKesiswaan())
            || ($kasus->status_tahap === 'tahap_4_kepsek' && $user->isKepalaSekolah())
            || ($kasus->status_tahap === 'selesai_pembinaan' && ($user->isAdmin() || $user->isWakaKesiswaan()));
        @endphp

        {{-- KANAN ATAS: AKSI DOSSIER SESUAI WEWENANG --}}
        <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
          @if($canProcessStage)
            <button type="button" class="btn btn-sm btn-outline" style="font-weight:700; font-size:12px; height:36px; padding:0 14px; display:inline-flex; align-items:center;" onclick="openModalTindakLanjutDossier()">
              Tindak Lanjut / Eskalasi
            </button>
          @else
            <span class="badge" style="background:var(--bg-3); border:1px solid var(--border-2); color:var(--text-2); font-size:11px; font-weight:700; padding:6px 12px; height:36px; display:inline-flex; align-items:center;">
              Wewenang: {{ $kasus->status_tahap === 'tahap_2_bk' ? 'Guru BK' : ($kasus->status_tahap === 'tahap_3_wakasis' ? 'Waka Kesiswaan' : ($kasus->status_tahap === 'tahap_4_kepsek' ? 'Kepala Sekolah' : 'Selesai')) }}
            </span>
          @endif

          @if($user->isAdmin() || $user->isKepalaSekolah() || $user->isWakaKesiswaan())
            <a href="{{ route('admin.disiplin.resume.cetak', $kasus->id) }}" target="_blank" class="btn btn-sm btn-outline" style="font-weight:700; font-size:12px; height:36px; padding:0 14px; display:inline-flex; align-items:center; text-decoration:none;">
              Resume Kepsek A4
            </a>
          @elseif($user->isWaliKelas() || $user->isGuruBk())
            <a href="{{ route('surat.cetak', ['siswa_id' => $siswa->id, 'kategori' => 'panggilan_ortu']) }}" target="_blank" class="btn btn-sm btn-outline" style="font-weight:700; font-size:12px; height:36px; padding:0 14px; display:inline-flex; align-items:center; text-decoration:none;">
              Surat Panggilan Ortu (A4)
            </a>
          @endif
        </div>
      </div>

      {{-- BAWAH: KONTAK WALI MURID & TOOLBAR AKSI CEPAT --}}
      <div class="dossier-hero-bottom">
        <div style="font-size:12px; color:var(--text-2); display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <span>Wali Murid: <strong style="color:var(--text); font-weight:700;">{{ $siswa->nama_ortu ?: 'Bapak/Ibu Orang Tua' }}</strong></span>
          @if($cleanHpOrtu)
            <a href="https://wa.me/{{ $cleanHpOrtu }}" target="_blank" class="badge" style="background:var(--bg-3); color:var(--text); border:1px solid var(--border-2); font-size:11px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:4px; padding:3px 9px;" title="Chat WhatsApp Wali Murid">
              <i class="bi bi-whatsapp"></i> {{ $siswa->nomor_hp_ortu }}
            </a>
          @endif
        </div>

        <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
          <button type="button" class="btn btn-sm btn-gold" style="font-weight:800; font-size:12px; height:36px; padding:0 14px; display:inline-flex; align-items:center;" onclick="openModalLog()">
            + Catat Log
          </button>
          <button type="button" class="btn btn-sm btn-outline" style="font-weight:700; font-size:12px; height:36px; padding:0 12px; display:inline-flex; align-items:center;" onclick="openModalPelanggaranSiswa()">
            + Catat Pelanggaran
          </button>
          <button type="button" class="btn btn-sm btn-outline" style="font-weight:700; font-size:12px; height:36px; padding:0 12px; display:inline-flex; align-items:center;" onclick="openModalRewardSiswa()">
            + Beri Self-Reward
          </button>
          <button type="button" class="btn btn-sm btn-outline" style="font-size:12px; font-weight:700; height:36px; padding:0 12px; display:inline-flex; align-items:center;" onclick="openModalUpload()">
            + Upload Bukti
          </button>
        </div>
      </div>
    </div>

    {{-- EXECUTIVE DECISION BANNER (KHUSUS TAHAP 4: KEPALA SEKOLAH) --}}
    @if($kasus->status_tahap === 'tahap_4_kepsek')
      <div class="panel" style="background:var(--bg-2); border:1.5px solid #CA8A04; border-radius:var(--r-md); padding:16px 20px; margin-bottom:18px; box-shadow:var(--shadow-sm);">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
          <div>
            <div style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:#CA8A04; letter-spacing:0.5px;">
              Otoritas Eksekutif Tingkat Tertinggi
            </div>
            <h3 style="font-size:16px; font-weight:900; color:var(--text); margin:2px 0 0 0;">
              Meja Keputusan Final Kepala Sekolah (Tahap 4)
            </h3>
            <p style="font-size:12px; color:var(--text-2); margin:3px 0 0 0;">
              Kasus telah melalui pembinaan Wali Kelas, Guru BK, dan Waka Kesiswaan. Silakan tetapkan SK resmi atau selesaikan pembinaan.
            </p>
          </div>

          <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            @if($user->isKepalaSekolah() || $user->isAdmin())
              <button type="button" class="btn btn-sm btn-gold" style="background:#000000; color:#FFFFFF; border:none; font-weight:800; font-size:12px; height:36px; padding:0 14px; display:inline-flex; align-items:center; box-shadow:0 2px 8px rgba(0,0,0,0.2);" onclick="openModalTindakLanjutDossier()">
                Tetapkan Keputusan Final
              </button>
            @endif
            <a href="{{ route('admin.disiplin.sk.cetak', $kasus->id) }}" target="_blank" class="btn btn-sm btn-outline" style="font-weight:800; font-size:12px; height:36px; padding:0 12px; display:inline-flex; align-items:center; text-decoration:none;">
              Cetak SK Kepsek (A4)
            </a>
            <a href="{{ route('admin.disiplin.resume.cetak', $kasus->id) }}" target="_blank" class="btn btn-sm btn-outline" style="font-weight:700; font-size:12px; height:36px; padding:0 12px; display:inline-flex; align-items:center; text-decoration:none;">
              Resume Yuridis
            </a>
          </div>
        </div>
      </div>
    @endif

    {{-- MAIN DOSSIER GRID --}}
    <div class="dossier-grid">
      
      {{-- LEFT COLUMN: TIMELINE & EVIDENCE VAULT --}}
      <div>
        
        {{-- SECTION 1: TIMELINE KRONOLOGIS INTERAKSI --}}
        <div class="panel-section">
          <div class="panel-section-header">
            <span class="panel-section-title">
              Timeline Kronologis Pembinaan
            </span>
            <button type="button" class="btn btn-sm btn-outline" style="font-size:11px; padding:3px 8px; font-weight:700;" onclick="openModalLog()">
              + Tambah Peristiwa
            </button>
          </div>

          <div class="timeline-container">
            @forelse($kasus->logs as $log)
              <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-card">
                  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px; flex-wrap:wrap; gap:6px;">
                    <div>
                      <strong style="color:var(--text); font-size:13.5px;">{{ $log->judul_kegiatan }}</strong>
                      <span class="badge" style="background:var(--bg-2); border:1px solid var(--border-2); font-size:11px; margin-left:6px; font-weight:700; padding:2px 7px;">
                        {{ str_replace('_', ' ', strtoupper($log->tahap)) }}
                      </span>
                    </div>
                    <div style="font-size:12px; font-family:var(--font-mono); color:var(--text-3); font-weight:600;">
                      {{ \Carbon\Carbon::parse($log->tanggal_kegiatan)->translatedFormat('d F Y') }}
                    </div>
                  </div>

                  <p style="font-size:13px; color:var(--text-2); margin:0 0 8px 0; line-height:1.6;">
                    {{ $log->uraian_tindakan }}
                  </p>

                  {{-- BERKAS MELEKAT PADA TAHAP INI --}}
                  <div style="margin-top:10px; margin-bottom:8px; padding:8px 10px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm);">
                    <div style="font-size:10.5px; font-weight:700; color:var(--text-3); text-transform:uppercase; letter-spacing:0.4px; margin-bottom:6px;">
                      Berkas Melekat Pada Tahap Ini:
                    </div>
                    <div style="display:flex; gap:6px; flex-wrap:wrap;">
                      @if($log->tahap === 'tahap_1_wali_kelas')
                        <a href="{{ route('surat.cetak', ['siswa_id' => $siswa->id, 'kategori' => 'panggilan_ortu']) }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; height:28px; padding:0 8px; font-weight:700; text-decoration:none;">
                          Surat Panggilan Ortu (A4)
                        </a>
                        <a href="{{ route('surat.cetak', ['siswa_id' => $siswa->id, 'kategori' => 'berita_acara']) }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; height:28px; padding:0 8px; font-weight:700; text-decoration:none;">
                          Berita Acara Perwalian (A4)
                        </a>
                      @elseif($log->tahap === 'tahap_2_bk')
                        <a href="{{ route('surat.cetak', ['siswa_id' => $siswa->id, 'kategori' => 'berita_acara']) }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; height:28px; padding:0 8px; font-weight:700; text-decoration:none;">
                          Berita Acara Musyawarah (A4)
                        </a>
                        <a href="{{ route('surat.cetak', ['siswa_id' => $siswa->id, 'kategori' => 'pembinaan']) }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; height:28px; padding:0 8px; font-weight:700; text-decoration:none;">
                          Surat Peringatan Siswa (SP)
                        </a>
                        <a href="{{ route('surat.cetak', ['siswa_id' => $siswa->id, 'kategori' => 'panggilan_ortu']) }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; height:28px; padding:0 8px; font-weight:700; text-decoration:none;">
                          Surat Panggilan Ortu (A4)
                        </a>
                      @elseif($log->tahap === 'tahap_3_wakasis')
                        <a href="{{ route('surat.cetak', ['siswa_id' => $siswa->id, 'kategori' => 'pembinaan']) }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; height:28px; padding:0 8px; font-weight:700; text-decoration:none;">
                          Surat Peringatan Siswa (SP)
                        </a>
                        <a href="{{ route('admin.disiplin.resume.cetak', $kasus->id) }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; height:28px; padding:0 8px; font-weight:700; text-decoration:none;">
                          Resume Yuridis (A4)
                        </a>
                      @elseif($log->tahap === 'tahap_4_kepsek')
                        <a href="{{ route('admin.disiplin.sk.cetak', $kasus->id) }}" target="_blank" class="btn btn-sm btn-gold" style="font-size:11px; height:28px; padding:0 10px; font-weight:800; text-decoration:none; background:#000000; color:#FFFFFF; border:none;">
                          SK Kepala Sekolah (A4)
                        </a>
                        <a href="{{ route('admin.disiplin.resume.cetak', $kasus->id) }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; height:28px; padding:0 8px; font-weight:700; text-decoration:none;">
                          Resume Rekam Jejak Kepsek (A4)
                        </a>
                      @elseif($log->tahap === 'selesai_pembinaan')
                        <a href="{{ route('admin.disiplin.resume.cetak', $kasus->id) }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; height:28px; padding:0 8px; font-weight:700; text-decoration:none;">
                          Resume Rekam Jejak (A4)
                        </a>
                        @if(Route::has('admin.siswa.surat-bebas-masalah'))
                          <a href="{{ route('admin.siswa.surat-bebas-masalah', $siswa->id) }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; height:28px; padding:0 8px; font-weight:700; text-decoration:none;">
                            Surat Bebas Masalah (A4)
                          </a>
                        @endif
                      @else
                        <a href="{{ route('admin.disiplin.resume.cetak', $kasus->id) }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; height:28px; padding:0 8px; font-weight:700; text-decoration:none;">
                          Resume Berkas (A4)
                        </a>
                      @endif
                    </div>

                    @php
                      $dokumenTahap = $kasus->dokumens->where('tahap', $log->tahap);
                    @endphp
                    @if($dokumenTahap->isNotEmpty())
                      <div style="margin-top:6px; padding-top:6px; border-top:1px dashed var(--border-2);">
                        <div style="font-size:10px; font-weight:700; color:var(--text-3); text-transform:uppercase; margin-bottom:4px;">
                          Bukti Fisik Terunggah:
                        </div>
                        <div style="display:flex; gap:6px; flex-wrap:wrap;">
                          @foreach($dokumenTahap as $dokTahap)
                            <a href="{{ $dokTahap->file_url }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:10.5px; height:24px; padding:0 6px; text-decoration:none; font-weight:600;" title="{{ $dokTahap->judul_dokumen }}">
                              {{ Str::limit($dokTahap->judul_dokumen, 25) }}
                            </a>
                          @endforeach
                        </div>
                      </div>
                    @endif
                  </div>

                  <div style="display:flex; justify-content:space-between; align-items:center; font-size:12px; color:var(--text-3); border-top:1px solid var(--border); padding-top:6px; flex-wrap:wrap; gap:6px;">
                    <div>
                      Dicatat oleh: <strong style="color:var(--text);">{{ $log->petugas_nama }}</strong> ({{ $log->petugas_role }})
                    </div>
                    
                    <div style="display:flex; align-items:center; gap:6px;">
                      @if($log->poin_perubahan != 0)
                        <span class="badge" style="background:var(--bg-2); border:1px solid var(--border-2); color:var(--text); font-weight:700; font-family:var(--font-mono); font-size:11px; padding:2px 7px;">
                          {{ $log->poin_perubahan > 0 ? '+' . $log->poin_perubahan : $log->poin_perubahan }} Poin
                        </span>
                      @endif

                      @php
                        $canEditLog = $user->isAdmin()
                          || ($log->tahap === 'tahap_1_wali_kelas' && $user->isWaliKelas() && $kasus->status_tahap === 'tahap_1_wali_kelas')
                          || ($log->tahap === 'tahap_2_bk' && $user->isGuruBk() && $kasus->status_tahap === 'tahap_2_bk')
                          || ($log->tahap === 'tahap_3_wakasis' && $user->isWakaKesiswaan())
                          || ($log->tahap === 'tahap_4_kepsek' && $user->isKepalaSekolah());
                      @endphp

                      @if($canEditLog)
                        <button type="button" class="btn btn-sm btn-outline" style="font-size:10.5px; height:24px; padding:0 8px; font-weight:700;" onclick="openModalEditLog({{ $log->id }}, '{{ addslashes($log->judul_kegiatan) }}', '{{ addslashes($log->uraian_tindakan) }}', '{{ \Carbon\Carbon::parse($log->tanggal_kegiatan)->format('Y-m-d') }}')">
                          Edit
                        </button>

                        <form action="{{ route('admin.disiplin.log.destroy', ['id' => $kasus->id, 'logId' => $log->id]) }}" method="POST" onsubmit="return confirm('Hapus peristiwa ini dari timeline?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline" style="font-size:10.5px; height:24px; padding:0 8px; font-weight:700;">
                            Hapus
                          </button>
                        </form>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            @empty
              <div style="text-align:center; padding:32px 16px; color:var(--text-3);">
                <div style="font-weight:700; font-size:13px; color:var(--text-2);">Belum ada riwayat interaksi yang dicatat</div>
                <p style="font-size:11.5px; margin-top:2px;">Gunakan tombol "+ Tambah Peristiwa" di atas untuk mencatat panggilan telepon, home visit, atau konseling.</p>
              </div>
            @endforelse
          </div>
        </div>

        {{-- SECTION 2: BRANKAS BUKTI DIGITAL (DIGITAL EVIDENCE VAULT) --}}
        <div class="panel-section">
          <div class="panel-section-header">
            <span class="panel-section-title">
              Brankas Bukti Digital (Surat Pernyataan &amp; Foto)
            </span>
            <button type="button" class="btn btn-sm btn-outline" style="font-size:11px; padding:3px 8px; font-weight:700;" onclick="openModalUpload()">
              + Unggah Berkas
            </button>
          </div>

          <div class="vault-grid">
            @forelse($kasus->dokumens as $dok)
              <div class="vault-card">
                <div>
                  <div style="margin-bottom:6px; display:flex; gap:4px; align-items:center; flex-wrap:wrap;">
                    {!! $dok->kategori_badge !!}
                    <span class="badge" style="background:var(--bg-2); border:1px solid var(--border-2); color:var(--text-2); font-size:10px; font-weight:700;">{{ $dok->tahap_label }}</span>
                  </div>
                  <strong style="color:var(--text); font-size:12px; display:block; margin-bottom:3px; line-height:1.3;">{{ $dok->judul_dokumen }}</strong>
                  <div style="font-size:10px; color:var(--text-3); font-family:var(--font-mono);">
                    {{ $dok->created_at->translatedFormat('d M Y - H:i') }}
                  </div>
                </div>

                <div style="margin-top:10px; display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:8px;">
                  <a href="{{ $dok->file_url }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:10.5px; padding:2px 8px; font-weight:700;">
                    Buka
                  </a>
                  <form action="{{ route('admin.disiplin.dokumen.destroy', ['id' => $kasus->id, 'dokumenId' => $dok->id]) }}" method="POST" onsubmit="return confirm('Hapus berkas bukti ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline" style="padding:2px 6px; font-size:10.5px;" title="Hapus Dokumen">
                      Hapus
                    </button>
                  </form>
                </div>
              </div>
            @empty
              <div style="grid-column:1/-1; text-align:center; padding:28px 16px; color:var(--text-3);">
                <div style="font-weight:700; font-size:13px; color:var(--text-2);">Brankas bukti digital masih kosong</div>
                <p style="font-size:11.5px; margin-top:2px;">Unggah scan surat pernyataan bermaterai, foto pertemuan di BK, atau foto home visit.</p>
              </div>
            @endforelse
          </div>
        </div>

        {{-- SECTION 3: RIWAYAT SELF-REWARD & POIN PEMULIHAN --}}
        <div class="panel-section">
          <div class="panel-section-header">
            <span class="panel-section-title">
              Riwayat Self-Reward &amp; Poin Pemulihan
            </span>
            <button type="button" class="btn btn-sm btn-outline" style="font-weight:700; font-size:11px; padding:3px 8px;" onclick="openModalRewardSiswa()">
              + Beri Reward
            </button>
          </div>

          <div style="display:flex; flex-direction:column; gap:8px;">
            @forelse($kasus->rewards as $rew)
              <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:10px 12px; display:flex; justify-content:space-between; align-items:center;">
                <div>
                  <div style="display:flex; align-items:center; gap:6px;">
                    <span class="badge" style="background:var(--bg-2); border:1px solid var(--border-2); color:var(--text); font-weight:700; font-family:var(--font-mono); font-size:11.5px; padding:2px 7px;">
                      -{{ $rew->poin_dikurangi }} Poin
                    </span>
                    <strong style="color:var(--text); font-size:13px;">{{ $rew->nama_tindakan }}</strong>
                    @if($rew->katalogReward)
                      <span class="badge" style="background:var(--bg-2); color:var(--text-3); font-size:10.5px; text-transform:uppercase; padding:2px 6px;">
                        {{ $rew->katalogReward->kategori }}
                      </span>
                    @endif
                  </div>
                  <div style="font-size:12.5px; color:var(--text-2); margin-top:4px; line-height:1.5;">
                    {{ $rew->catatan ?: 'Reward dan pemulihan poin kedisiplinan siswa.' }}
                  </div>
                  <div style="font-size:11px; color:var(--text-3); margin-top:3px;">
                    {{ $rew->dicatat_oleh }} · <span style="font-family:var(--font-mono);">{{ \Carbon\Carbon::parse($rew->tanggal)->translatedFormat('d F Y') }}</span>
                  </div>
                </div>

                <form action="{{ route('admin.disiplin.reward.destroy', ['id' => $kasus->id, 'rewardId' => $rew->id]) }}" method="POST" onsubmit="return confirm('Batalkan reward ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline" style="padding:2px 6px; font-size:10.5px;" title="Batalkan Reward">
                    Hapus
                  </button>
                </form>
              </div>
            @empty
              <div style="text-align:center; padding:24px 16px; color:var(--text-3);">
                <div style="font-size:12px; color:var(--text-3);">Belum ada catatan reward atau aksi pemulihan poin untuk siswa ini.</div>
              </div>
            @endforelse
          </div>
        </div>

        {{-- SECTION 4: RIWAYAT PELANGGARAN MANUAL / TAMBAHAN --}}
        <div class="panel-section" style="margin-bottom:0;">
          <div class="panel-section-header">
            <span class="panel-section-title">
              Riwayat Pelanggaran Manual / Tambahan
            </span>
            <button type="button" class="btn btn-sm btn-outline" style="font-weight:700; font-size:11px; padding:3px 8px;" onclick="openModalPelanggaranSiswa()">
              + Catat Pelanggaran
            </button>
          </div>

          <div style="display:flex; flex-direction:column; gap:8px;">
            @forelse($kasus->pelanggarans as $pel)
              <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:10px 12px; display:flex; justify-content:space-between; align-items:center;">
                <div>
                  <div style="display:flex; align-items:center; gap:6px;">
                    <span class="badge" style="background:var(--bg-2); border:1px solid var(--border-2); color:var(--text); font-weight:700; font-family:var(--font-mono); font-size:11.5px; padding:2px 7px;">
                      +{{ $pel->poin_ditambah }} Poin
                    </span>
                    <strong style="color:var(--text); font-size:13px;">{{ $pel->nama_pelanggaran }}</strong>
                    @if($pel->katalogPelanggaran)
                      <span class="badge" style="background:var(--bg-2); color:var(--text-3); font-size:10.5px; text-transform:uppercase; padding:2px 6px;">
                        {{ $pel->katalogPelanggaran->kategori }}
                      </span>
                    @endif
                  </div>
                  <div style="font-size:12.5px; color:var(--text-2); margin-top:4px; line-height:1.5;">
                    {{ $pel->catatan ?: 'Pelanggaran tata tertib / kedisiplinan sekolah.' }}
                  </div>
                  <div style="font-size:11px; color:var(--text-3); margin-top:3px;">
                    {{ $pel->dicatat_oleh }} · <span style="font-family:var(--font-mono);">{{ \Carbon\Carbon::parse($pel->tanggal)->translatedFormat('d F Y') }}</span>
                  </div>
                </div>

                <form action="{{ route('admin.disiplin.pelanggaran.destroy', ['id' => $kasus->id, 'pelanggaranId' => $pel->id]) }}" method="POST" onsubmit="return confirm('Hapus catatan pelanggaran ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline" style="padding:2px 6px; font-size:10.5px;" title="Hapus Catatan Pelanggaran">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            @empty
              <div style="text-align:center; padding:24px 16px; color:var(--text-3);">
                <i class="bi bi-shield-check" style="font-size:28px; opacity:0.35; display:block; margin-bottom:6px;"></i>
                <div style="font-size:12px; color:var(--text-3);">Belum ada pelanggaran manual tambahan di luar presensi harian.</div>
              </div>
            @endforelse
          </div>
        </div>

      </div>

      {{-- RIGHT COLUMN: SKOR POIN, REKAP PRESENSI & KONTAK ORTU --}}
      <div>
        
        {{-- CARD 1: SKOR & AKUMULASI POIN --}}
        <div class="panel-section">
          <div class="panel-section-header">
            <span class="panel-section-title">
              <i class="bi bi-speedometer2"></i> Skor &amp; Kredit Disiplin
            </span>
          </div>

          <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:16px 14px; text-align:center; margin-bottom:14px;">
            <div style="font-size:10.5px; font-weight:800; color:var(--text-3); text-transform:uppercase; letter-spacing:0.5px;">Akumulasi Poin Bersih</div>
            <div style="font-size:42px; font-weight:900; font-family:var(--font-mono); line-height:1.1; margin:4px 0; color:var(--text);">
              {{ $kasus->poin_bersih }}
            </div>
            <div style="margin-top:4px;">
              <span class="badge" style="background:var(--bg-2); border:1px solid var(--border-2); color:var(--text); font-weight:800; font-size:11px; padding:3px 8px;">
                @if($kasus->poin_bersih >= $pengaturanDisiplin->ambang_tahap_4_kepsek) Tahap 4 – Kepala Sekolah
                @elseif($kasus->poin_bersih >= $pengaturanDisiplin->ambang_tahap_3_wakasis) Tahap 3 – Waka Kesiswaan
                @elseif($kasus->poin_bersih >= $pengaturanDisiplin->ambang_tahap_2_bk) Tahap 2 – Guru BK
                @elseif($kasus->poin_bersih >= $pengaturanDisiplin->ambang_tahap_1_wali) Tahap 1 – Wali Kelas
                @else Bersih / Tertib
                @endif
              </span>
            </div>
            <div style="font-size:11px; color:var(--text-3); margin-top:8px; border-top:1px solid var(--border); padding-top:6px;">
              Pelanggaran: <strong style="color:var(--text); font-family:var(--font-mono);">{{ $kasus->total_poin_pelanggaran }}</strong> &nbsp;|&nbsp; Pemulihan: <strong style="color:var(--text); font-family:var(--font-mono);">-{{ $kasus->total_poin_pemulihan }}</strong>
            </div>
          </div>

          {{-- Breakdown kontribusi poin per jenis --}}
          @php
            $tolPiket = (int) ($pengaturanDisiplin->toleransi_terlambat_piket ?? 2);
            $bLate = (int) ($pengaturanDisiplin->bobot_terlambat ?? 3);
            $bAlpha = (int) ($pengaturanDisiplin->bobot_alpha ?? 10);
            $bBolos = (int) ($pengaturanDisiplin->bobot_bolos ?? 15);
            $hitungLate = max(0, $kasus->total_terlambat - $tolPiket);
            $poinLate = $hitungLate * $bLate;
            $poinAlpha = $kasus->total_alpha * $bAlpha;
            $poinBolos = $kasus->total_bolos * $bBolos;
            $totalManualPelanggaran = $kasus->pelanggarans->sum('poin_ditambah');
          @endphp
          <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:12px 14px; margin-bottom:14px;">
            <div style="font-weight:800; color:var(--text-3); font-size:10px; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">Rincian Poin Pelanggaran</div>
            
            <div class="metric-row">
              <span style="color:var(--text-2);"><i class="bi bi-x-circle" style="margin-right:4px;"></i> Alpha ({{ $kasus->total_alpha }}x × {{ $bAlpha }}):</span>
              <strong style="color:var(--text); font-family:var(--font-mono); font-weight:800;">{{ $poinAlpha }} poin</strong>
            </div>
            <div class="metric-row">
              <span style="color:var(--text-2);"><i class="bi bi-door-open" style="margin-right:4px;"></i> Bolos ({{ $kasus->total_bolos }}x × {{ $bBolos }}):</span>
              <strong style="color:var(--text); font-family:var(--font-mono); font-weight:800;">{{ $poinBolos }} poin</strong>
            </div>
            <div class="metric-row">
              <span style="color:var(--text-2);"><i class="bi bi-clock-history" style="margin-right:4px;"></i> Terlambat ({{ $hitungLate }}x × {{ $bLate }}):</span>
              <strong style="color:var(--text); font-family:var(--font-mono); font-weight:800;">{{ $poinLate }} poin</strong>
            </div>
            @if($totalManualPelanggaran > 0)
              <div class="metric-row">
                <span style="color:var(--text-2);"><i class="bi bi-exclamation-triangle" style="margin-right:4px;"></i> Pelanggaran Manual ({{ $kasus->pelanggarans->count() }}x):</span>
                <strong style="color:var(--text); font-family:var(--font-mono); font-weight:800;">+{{ $totalManualPelanggaran }} poin</strong>
              </div>
            @endif
            @if($kasus->total_poin_pemulihan > 0)
              <div class="metric-row" style="border-top:1px solid var(--border-2); padding-top:6px; margin-top:4px;">
                <span style="color:var(--text); font-weight:700;"><i class="bi bi-gift" style="margin-right:4px;"></i> Total Self-Reward:</span>
                <strong style="color:var(--text); font-family:var(--font-mono); font-weight:800;">-{{ $kasus->total_poin_pemulihan }} poin</strong>
              </div>
            @endif
          </div>

          <div style="display:flex; flex-direction:column; gap:6px; font-size:12px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <span style="color:var(--text-2);"><i class="bi bi-calendar-x" style="margin-right:4px;"></i> Total Alpha:</span>
              <strong style="color:var(--text); font-weight:800; font-family:var(--font-mono);">{{ $kasus->total_alpha }} Hari</strong>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <span style="color:var(--text-2);"><i class="bi bi-slash-circle" style="margin-right:4px;"></i> Total Bolos:</span>
              <strong style="color:var(--text); font-weight:800; font-family:var(--font-mono);">{{ $kasus->total_bolos }} Kali</strong>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <span style="color:var(--text-2);"><i class="bi bi-alarm" style="margin-right:4px;"></i> Total Terlambat:</span>
              <strong style="color:var(--text); font-weight:800; font-family:var(--font-mono);">{{ $kasus->total_terlambat }} Kali</strong>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:6px; margin-top:2px;">
              <span style="color:var(--text); font-weight:700;">Kehadiran Efektif:</span>
              <strong style="color:var(--text); font-size:13.5px; font-weight:900; font-family:var(--font-mono);">{{ $persenKehadiran }}%</strong>
            </div>
          </div>
        </div>

        {{-- CARD 2: KONTAK ORANG TUA --}}
        <div class="panel-section">
          <div class="panel-section-header">
            <span class="panel-section-title">
              <i class="bi bi-telephone"></i> Komunikasi Orang Tua
            </span>
          </div>

          <div style="font-size:12px; margin-bottom:12px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:10px 12px;">
            <div style="color:var(--text-3); font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.4px;">Nama Orang Tua / Wali:</div>
            <strong style="color:var(--text); font-size:13.5px; display:block; margin-top:2px;">{{ $siswa->nama_ortu ?: 'Bapak/Ibu Orang Tua' }}</strong>
            <div style="color:var(--text-2); font-size:11.5px; margin-top:3px; font-weight:600;">
              No. HP/WA: <span style="font-family:var(--font-mono); color:var(--text); font-weight:800;">{{ $siswa->nomor_hp_ortu ?: 'Belum terdata' }}</span>
            </div>
          </div>

          @if($siswa->nomor_hp_ortu)
            @php
              $cleanHp = preg_replace('/[^0-9]/', '', $siswa->nomor_hp_ortu);
              if (str_starts_with($cleanHp, '0')) {
                $cleanHp = '62' . substr($cleanHp, 1);
              }
            @endphp
            <a href="https://wa.me/{{ $cleanHp }}?text=Assalamu'alaikum%20Bapak/Ibu%20Wali%20dari%20{{ urlencode($siswa->nama) }},%20kami%20dari%20pihak%20sekolah%20SMKN%201%20Air%20Naningan%20ingin%20berkoordinasi%20mengenai%20kehadiran%20putra/putri%20Bapak/Ibu." target="_blank" class="btn btn-outline" style="width:100%; justify-content:center; font-weight:700; font-size:12px; height:36px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
              <i class="bi bi-whatsapp" style="font-size:14px;"></i> Hubungi WhatsApp Ortu
            </a>
          @endif
        </div>

        {{-- CARD 3: FORMAT SURAT RESMI 1-KLIK (ROLE-BASED VISIBILITY) --}}
        <div class="panel-section">
          <div class="panel-section-header">
            <span class="panel-section-title">
              Berkas Cetak Resmi
            </span>
          </div>

          <div style="display:flex; flex-direction:column; gap:6px;">
            {{-- 1. Surat Panggilan Ortu: Wali Kelas, Guru BK, Wakasis, Admin --}}
            @if($user->isAdmin() || $user->isWaliKelas() || $user->isGuruBk() || $user->isWakaKesiswaan())
              <a href="{{ route('surat.cetak', ['siswa_id' => $siswa->id, 'kategori' => 'panggilan_ortu']) }}" target="_blank" class="btn btn-sm btn-outline" style="justify-content:flex-start; text-align:left; font-size:11.5px; font-weight:700; height:34px; padding:0 10px; text-decoration:none;" title="Cetak Surat Panggilan Orang Tua">
                Surat Panggilan Ortu (A4)
              </a>
            @endif

            {{-- 2. Berita Acara Musyawarah: Wali Kelas, Guru BK, Wakasis, Kepala Sekolah, Admin --}}
            @if($user->isAdmin() || $user->isGuruBk() || $user->isWaliKelas() || $user->isWakaKesiswaan() || $user->isKepalaSekolah())
              <a href="{{ route('surat.cetak', ['siswa_id' => $siswa->id, 'kategori' => 'berita_acara']) }}" target="_blank" class="btn btn-sm btn-outline" style="justify-content:flex-start; text-align:left; font-size:11.5px; font-weight:700; height:34px; padding:0 10px; text-decoration:none;" title="Cetak Berita Acara Musyawarah">
                Berita Acara Musyawarah (A4)
              </a>
            @endif

            {{-- 3. Surat Peringatan Siswa SP: Guru BK, Waka Kesiswaan, Admin --}}
            @if($user->isAdmin() || $user->isGuruBk() || $user->isWakaKesiswaan())
              <a href="{{ route('surat.cetak', ['siswa_id' => $siswa->id, 'kategori' => 'pembinaan']) }}" target="_blank" class="btn btn-sm btn-outline" style="justify-content:flex-start; text-align:left; font-size:11.5px; font-weight:700; height:34px; padding:0 10px; text-decoration:none;" title="Cetak Surat Peringatan Siswa">
                Surat Peringatan Siswa (SP)
              </a>
            @endif

            {{-- 4. SK Kepala Sekolah: Kepala Sekolah, Admin, atau Wakasis saat kasus di tahap kesiswaan/kepsek --}}
            @if($user->isAdmin() || $user->isKepalaSekolah() || ($user->isWakaKesiswaan() && in_array($kasus->status_tahap, ['tahap_3_wakasis', 'tahap_4_kepsek', 'selesai_pembinaan'])))
              <a href="{{ route('admin.disiplin.sk.cetak', $kasus->id) }}" target="_blank" class="btn btn-sm btn-gold" style="justify-content:flex-start; text-align:left; font-size:11.5px; font-weight:800; height:34px; padding:0 10px; text-decoration:none; background:#000000; color:#FFFFFF; border:none;" title="Cetak Surat Keputusan (SK) Kepala Sekolah">
                SK Kepala Sekolah (A4)
              </a>
            @endif

            {{-- 5. Resume Rekam Jejak Kepsek: Kepala Sekolah, Waka Kesiswaan, Guru BK, Admin --}}
            @if($user->isAdmin() || $user->isKepalaSekolah() || $user->isWakaKesiswaan() || $user->isGuruBk())
              <a href="{{ route('admin.disiplin.resume.cetak', $kasus->id) }}" target="_blank" class="btn btn-sm btn-outline" style="justify-content:flex-start; text-align:left; font-size:11.5px; font-weight:800; height:34px; padding:0 10px; text-decoration:none;" title="Cetak Resume Rekam Jejak Kepsek">
                Resume Rekam Jejak Kepsek (A4)
              </a>
            @endif
          </div>
        </div>

        {{-- CARD 4: RIWAYAT NOTIFIKASI WHATSAPP KASUS --}}
        <div class="panel-section" style="margin-bottom:0;">
          <div class="panel-section-header">
            <span class="panel-section-title">
              <i class="bi bi-whatsapp"></i> Log Notifikasi WhatsApp
            </span>
            <span class="badge" style="background:var(--bg-3); border:1px solid var(--border-2); font-size:9.5px; font-weight:700;">{{ count($notifikasiList ?? []) }} Pesan</span>
          </div>

          <div style="display:flex; flex-direction:column; gap:8px;">
            @forelse($notifikasiList ?? [] as $ntf)
              <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:8px 10px; font-size:11px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:3px;">
                  <strong style="color:var(--text); font-size:11px;">
                    {{ str_replace('_', ' ', ucwords($ntf->kategori)) }}
                  </strong>
                  @if($ntf->status === 'terkirim')
                    <span class="badge" style="background:var(--bg-2); border:1px solid var(--border-2); color:var(--text); font-size:9.5px; font-weight:800;">
                      <i class="bi bi-check2-all"></i> Terkirim
                    </span>
                  @elseif($ntf->status === 'pending')
                    <span class="badge" style="background:var(--bg-2); border:1px solid var(--border-2); color:var(--text-2); font-size:9.5px; font-weight:700;">
                      <i class="bi bi-hourglass-split"></i> Pending
                    </span>
                  @elseif($ntf->status === 'dibatalkan')
                    <span class="badge" style="background:var(--bg-2); border:1px solid var(--border-2); color:var(--text-3); font-size:9.5px;">
                      Dibatalkan
                    </span>
                  @else
                    <span class="badge" style="background:var(--bg-2); border:1px solid var(--border-2); color:var(--text); font-size:9.5px; font-weight:800;">
                      <i class="bi bi-x-circle"></i> Gagal
                    </span>
                  @endif
                </div>

                <div style="color:var(--text-3); font-family:var(--font-mono); font-size:10.5px; margin-bottom:3px;">
                  <i class="bi bi-telephone-outbound"></i> {{ $ntf->no_tujuan ?: '(Tanpa No HP)' }}
                </div>

                <div style="color:var(--text-2); font-size:10.5px; line-height:1.35;">
                  {{ Str::limit($ntf->pesan, 70) }}
                </div>

                <div style="font-size:9.5px; color:var(--text-3); margin-top:3px; text-align:right; font-family:var(--font-mono);">
                  {{ $ntf->created_at->format('d/m/Y H:i') }}
                </div>
              </div>
            @empty
              <div style="text-align:center; padding:12px; color:var(--text-3); font-size:11px;">
                Belum ada pengiriman notifikasi WhatsApp untuk kasus ini.
              </div>
            @endforelse
          </div>
        </div>

      </div>

    </div>
  </main>
</div>

{{-- MODAL TAMBAH LOG INTERAKSI --}}
<div class="modal-overlay" id="modalLog">
  <div class="modal-card" style="max-width:520px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:10px;">
      <h3 style="font-size:16px; font-weight:800; color:var(--text); margin:0; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-chat-left-text-fill"></i> Catat Log Interaksi Pembinaan
      </h3>
      <button type="button" class="btn btn-sm btn-outline" style="padding:2px 6px;" onclick="closeModalLog()"><i class="bi bi-x-lg"></i></button>
    </div>

    <form action="{{ route('admin.disiplin.log.store', $kasus->id) }}" method="POST">
      @csrf
      <div style="margin-bottom:12px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Judul Kegiatan / Bentuk Interaksi <span style="color:var(--red);">*</span></label>
        <input type="text" name="judul_kegiatan" class="input-field" placeholder="Misal: Panggilan Telepon Ortu / Home Visit / Konseling BK" required style="width:100%;" />
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Tahap Pelaksana <span style="color:var(--red);">*</span></label>
          <select name="tahap" class="input-field" style="width:100%;" required>
            @if($user->isAdmin() || $user->isWaliKelas())
              <option value="tahap_1_wali_kelas" {{ $kasus->status_tahap === 'tahap_1_wali_kelas' ? 'selected' : '' }}>Tahap 1: Wali Kelas</option>
            @endif
            @if($user->isAdmin() || $user->isGuruBk())
              <option value="tahap_2_bk" {{ $kasus->status_tahap === 'tahap_2_bk' ? 'selected' : '' }}>Tahap 2: Guru BK</option>
            @endif
            @if($user->isAdmin() || $user->isWakaKesiswaan())
              <option value="tahap_3_wakasis" {{ $kasus->status_tahap === 'tahap_3_wakasis' ? 'selected' : '' }}>Tahap 3: Wakasis</option>
            @endif
            @if($user->isAdmin() || $user->isKepalaSekolah())
              <option value="tahap_4_kepsek" {{ $kasus->status_tahap === 'tahap_4_kepsek' ? 'selected' : '' }}>Tahap 4: Kepsek</option>
            @endif
            <option value="home_visit">Home Visit (Kunjungan Rumah)</option>
            <option value="pemulihan_sikap">Pemulihan Sikap / Prestasi</option>
          </select>
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Tanggal Kegiatan <span style="color:var(--red);">*</span></label>
          <input type="date" name="tanggal_kegiatan" class="input-field" value="{{ \Carbon\Carbon::today()->toDateString() }}" required style="width:100%; font-family:var(--font-mono);" />
        </div>
      </div>

      <div style="margin-bottom:12px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Uraian Hasil Pembinaan / Pembicaraan <span style="color:var(--red);">*</span></label>
        <textarea name="uraian_tindakan" class="input-field" rows="4" placeholder="Tuliskan detail pembicaraan dengan orang tua, alasan siswa, atau komitmen yang disepakati..." required style="width:100%;"></textarea>
      </div>

      <div style="margin-bottom:18px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Penyesuaian Poin Disiplin</label>
        <input type="number" name="poin_perubahan" class="input-field" placeholder="0 (Gunakan tanda minus untuk poin pemulihan, misal: -10)" style="width:100%; font-family:var(--font-mono);" />
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModalLog()">Batal</button>
        <button type="submit" class="btn btn-gold">Simpan ke Timeline</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL UPLOAD BUKTI DOKUMEN --}}
<div class="modal-overlay" id="modalUpload">
  <div class="modal-card" style="max-width:500px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:10px;">
      <h3 style="font-size:16px; font-weight:800; color:var(--text); margin:0; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-cloud-arrow-up-fill"></i> Unggah Bukti Fisik Digital
      </h3>
      <button type="button" class="btn btn-sm btn-outline" style="padding:2px 6px;" onclick="closeModalUpload()"><i class="bi bi-x-lg"></i></button>
    </div>

    <form action="{{ route('admin.disiplin.dokumen.upload', $kasus->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div style="margin-bottom:12px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Nama / Judul Dokumen <span style="color:var(--red);">*</span></label>
        <input type="text" name="judul_dokumen" class="input-field" placeholder="Misal: Scan Surat Pernyataan SP 2 Bermaterai" required style="width:100%;" />
      </div>

      <div style="margin-bottom:12px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Lampirkan Pada Tahap Pembinaan <span style="color:var(--red);">*</span></label>
        <select name="tahap" class="input-field" style="width:100%;" required>
          @if($user->isAdmin() || $user->isWaliKelas())
            <option value="tahap_1_wali_kelas" {{ $kasus->status_tahap === 'tahap_1_wali_kelas' ? 'selected' : '' }}>Tahap 1: Pembinaan Wali Kelas</option>
          @endif
          @if($user->isAdmin() || $user->isGuruBk())
            <option value="tahap_2_bk" {{ $kasus->status_tahap === 'tahap_2_bk' ? 'selected' : '' }}>Tahap 2: Konseling &amp; Musyawarah Guru BK</option>
          @endif
          @if($user->isAdmin() || $user->isWakaKesiswaan())
            <option value="tahap_3_wakasis" {{ $kasus->status_tahap === 'tahap_3_wakasis' ? 'selected' : '' }}>Tahap 3: Sidang Pleno Kesiswaan</option>
          @endif
          @if($user->isAdmin() || $user->isKepalaSekolah())
            <option value="tahap_4_kepsek" {{ $kasus->status_tahap === 'tahap_4_kepsek' ? 'selected' : '' }}>Tahap 4: Keputusan Final Kepala Sekolah</option>
          @endif
          <option value="selesai_pembinaan" {{ $kasus->status_tahap === 'selesai_pembinaan' ? 'selected' : '' }}>Tahap Selesai Pembinaan</option>
        </select>
      </div>

      <div style="margin-bottom:12px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Kategori Berkas <span style="color:var(--red);">*</span></label>
        <select name="kategori" class="input-field" style="width:100%;" required>
          <option value="surat_pernyataan">Surat Pernyataan / Perjanjian SP</option>
          <option value="foto_dokumentasi">Foto Pertemuan / Home Visit</option>
          <option value="berita_acara">Berita Acara Pembinaan</option>
          <option value="surat_dokter">Surat Keterangan Dokter</option>
          <option value="lainnya">Berkas Lampiran Lainnya</option>
        </select>
      </div>

      <div style="margin-bottom:18px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:flex; justify-content:space-between; margin-bottom:4px;">
          <span>Pilih File (JPG, PNG, PDF - Max 5MB) <span style="color:var(--red);">*</span></span>
          <span style="color:var(--text); font-size:10.5px; font-weight:700;"><i class="bi bi-crop"></i> Auto-Crop Gambar</span>
        </label>
        <input type="file" name="file" id="inputDokumenDisiplin" class="input-field" accept=".jpg,.jpeg,.png,.webp,.pdf" onchange="initPhotoCrop(this, null, 'free', 'Sesuaikan & Potong Bukti Dokumen')" required style="width:100%;" />
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModalUpload()">Batal</button>
        <button type="submit" class="btn btn-gold">Unggah ke Brankas</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL TINDAK LANJUT / ESKALASI --}}
<div class="modal-overlay" id="modalEskalasiDossier">
  <div class="modal-card" style="max-width:560px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:10px;">
      <h3 style="font-size:16px; font-weight:800; color:var(--text); margin:0;">
        Tindak Lanjut &amp; Eskalasi Kasus
      </h3>
      <button type="button" class="btn btn-sm btn-outline" style="padding:2px 6px;" onclick="closeModalTindakLanjutDossier()"><i class="bi bi-x-lg"></i></button>
    </div>

    <form action="{{ route('admin.disiplin.tindak-lanjut', $kasus->id) }}" method="POST">
      @csrf
      <div style="margin-bottom:14px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Pilih Tahap Baru <span style="color:var(--red);">*</span></label>
        <select name="status_tahap_baru" id="selectStatusTahapBaru" class="input-field" style="width:100%; font-weight:700;" onchange="handleTahapChange(this.value)" required>
          @if($user->isAdmin() || $user->isWaliKelas())
            <option value="tahap_1_wali_kelas" {{ $kasus->status_tahap === 'tahap_1_wali_kelas' ? 'selected' : '' }}>Tahap 1: Pembinaan Wali Kelas</option>
            <option value="tahap_2_bk" {{ $kasus->status_tahap === 'tahap_2_bk' ? 'selected' : '' }}>Eskalasi ke Tahap 2: Konseling &amp; Panggilan Guru BK</option>
          @endif

          @if($user->isAdmin() || $user->isGuruBk())
            @if(!$user->isWaliKelas())
              <option value="tahap_2_bk" {{ $kasus->status_tahap === 'tahap_2_bk' ? 'selected' : '' }}>Tahap 2: Konseling &amp; Panggilan Guru BK</option>
            @endif
            <option value="tahap_3_wakasis" {{ $kasus->status_tahap === 'tahap_3_wakasis' ? 'selected' : '' }}>Eskalasi ke Tahap 3: Sidang &amp; Sanksi Waka Kesiswaan</option>
          @endif

          @if($user->isAdmin() || $user->isWakaKesiswaan())
            @if(!$user->isGuruBk() && !$user->isWaliKelas())
              <option value="tahap_3_wakasis" {{ $kasus->status_tahap === 'tahap_3_wakasis' ? 'selected' : '' }}>Tahap 3: Sidang &amp; Sanksi Waka Kesiswaan</option>
            @endif
            <option value="tahap_4_kepsek" {{ $kasus->status_tahap === 'tahap_4_kepsek' ? 'selected' : '' }}>Eskalasi ke Tahap 4: Keputusan Final Kepala Sekolah</option>
          @endif

          @if($user->isAdmin() || $user->isKepalaSekolah())
            <option value="tahap_4_kepsek" {{ $kasus->status_tahap === 'tahap_4_kepsek' ? 'selected' : '' }}>Tahap 4: Keputusan Final Kepala Sekolah</option>
          @endif

          <option value="selesai_pembinaan">Selesaikan Pembinaan</option>
        </select>
      </div>

      {{-- PRESET KEPUTUSAN KEPALA SEKOLAH --}}
      <div id="boxPresetKepsek" style="margin-bottom:12px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:10px 12px; display:{{ $kasus->status_tahap === 'tahap_4_kepsek' ? 'block' : 'none' }};">
        <div style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:#CA8A04; margin-bottom:6px;">
          Template Keputusan Kepala Sekolah:
        </div>
        <div style="display:flex; flex-wrap:wrap; gap:6px;">
          <button type="button" class="btn btn-sm btn-outline" style="font-size:10.5px; padding:3px 8px;" onclick="applyPresetKepsek('sp3')">
            SP-3 Terakhir
          </button>
          <button type="button" class="btn btn-sm btn-outline" style="font-size:10.5px; padding:3px 8px;" onclick="applyPresetKepsek('skorsing3')">
            Skorsing 3 Hari
          </button>
          <button type="button" class="btn btn-sm btn-outline" style="font-size:10.5px; padding:3px 8px;" onclick="applyPresetKepsek('skorsing7')">
            Skorsing 7 Hari
          </button>
          <button type="button" class="btn btn-sm btn-outline" style="font-size:10.5px; padding:3px 8px;" onclick="applyPresetKepsek('mutasi')">
            Rekomendasi Mutasi
          </button>
          <button type="button" class="btn btn-sm btn-outline" style="font-size:10.5px; padding:3px 8px;" onclick="applyPresetKepsek('amnesti')">
            Amnesti Pemulihan
          </button>
        </div>
      </div>

      <div style="margin-bottom:14px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Catatan Tindakan / Keputusan <span style="color:var(--red);">*</span></label>
        <textarea name="catatan_tindakan" id="textareaCatatanTindakLanjut" class="input-field" rows="3" placeholder="Catat alasan eskalasi atau substansi sanksi yang ditetapkan..." required style="width:100%;">{{ $kasus->status_tahap === 'tahap_4_kepsek' ? $kasus->keputusan_kepsek : '' }}</textarea>
      </div>

      <div style="margin-bottom:18px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Sanksi Tambahan / Ketetapan SK (Opsional)</label>
        <input type="text" name="sanksi_tambahan" id="inputSanksiTambahan" class="input-field" value="{{ $kasus->sanksi_wakasis }}" placeholder="Misal: Penerbitan SP 3 / Skorsing 3 hari / Perjanjian Terakhir" style="width:100%;" />
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModalTindakLanjutDossier()">Batal</button>
        <button type="submit" class="btn btn-gold">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL BERI SELF-REWARD & KURANGI POIN --}}
<div class="modal-overlay" id="modalRewardSiswa">
  <div class="modal-card" style="max-width:540px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:10px;">
      <h3 style="font-size:16px; font-weight:800; color:var(--text); margin:0; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-gift-fill"></i> Berikan Self-Reward / Pengurangan Poin
      </h3>
      <button type="button" class="btn btn-sm btn-outline" style="padding:2px 6px;" onclick="closeModalRewardSiswa()"><i class="bi bi-x-lg"></i></button>
    </div>

    <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:10px 14px; margin-bottom:14px; font-size:12px;">
      Siswa: <strong style="color:var(--text); font-weight:800;">{{ $siswa->nama }}</strong> · Poin Bersih Saat Ini: <strong style="color:var(--text); font-family:var(--font-mono);">{{ $kasus->poin_bersih }} Poin</strong>
    </div>

    <form action="{{ route('admin.disiplin.reward.store', $kasus->id) }}" method="POST">
      @csrf
      <div style="margin-bottom:14px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Pilih dari Katalog Master Reward</label>
        <select id="selectKatalogReward" name="katalog_reward_id" class="input-field" style="width:100%;" onchange="autoFillReward(this)">
          <option value="">-- Tindakan Kustom / Manual --</option>
          @foreach($katalogRewards as $kr)
            <option value="{{ $kr->id }}" data-nama="{{ $kr->nama_reward }}" data-poin="{{ $kr->poin_deduksi }}" data-desc="{{ $kr->deskripsi }}">
              {{ $kr->nama_reward }} (-{{ $kr->poin_deduksi }} Poin) [{{ strtoupper($kr->kategori) }}]
            </option>
          @endforeach
        </select>
      </div>

      <div style="margin-bottom:14px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Nama Tindakan / Aksi Positif <span style="color:var(--red);">*</span></label>
        <input type="text" id="inputRewardNama" name="nama_tindakan" class="input-field" placeholder="Misal: Menyelesaikan Tugas Karakter / Juara Lomba" required style="width:100%;" />
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Poin Pengurangan <span style="color:var(--red);">*</span></label>
          <input type="number" id="inputRewardPoin" name="poin_dikurangi" class="input-field" value="5" min="1" max="200" required style="width:100%; font-family:var(--font-mono); font-weight:800;" />
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Tanggal Perolehan <span style="color:var(--red);">*</span></label>
          <input type="date" name="tanggal" class="input-field" value="{{ date('Y-m-d') }}" required style="width:100%; font-family:var(--font-mono);" />
        </div>
      </div>

      <div style="margin-bottom:18px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Catatan Tambahan / Hasil Pembinaan (Opsional)</label>
        <textarea id="inputRewardCatatan" name="catatan" class="input-field" rows="2" placeholder="Tuliskan apresiasi atau keterangan pelaksanaan reward..." style="width:100%;"></textarea>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModalRewardSiswa()">Batal</button>
        <button type="submit" class="btn btn-gold">
          <i class="bi bi-gift-fill" style="margin-right:4px;"></i> Simpan Reward &amp; Kurangi Poin
        </button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL CATAT PELANGGARAN MANUAL & TAMBAHAN --}}
<div class="modal-overlay" id="modalPelanggaranSiswa">
  <div class="modal-card" style="max-width:540px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:10px;">
      <h3 style="font-size:16px; font-weight:800; color:var(--text); margin:0; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-exclamation-triangle-fill"></i> Catat Pelanggaran Kedisiplinan / Poin
      </h3>
      <button type="button" class="btn btn-sm btn-outline" style="padding:2px 6px;" onclick="closeModalPelanggaranSiswa()"><i class="bi bi-x-lg"></i></button>
    </div>

    <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:10px 14px; margin-bottom:14px; font-size:12px;">
      Siswa: <strong style="color:var(--text); font-weight:800;">{{ $siswa->nama }}</strong> · Poin Bersih Saat Ini: <strong style="color:var(--text); font-family:var(--font-mono);">{{ $kasus->poin_bersih }} Poin</strong>
    </div>

    <form action="{{ route('admin.disiplin.pelanggaran.store', $kasus->id) }}" method="POST">
      @csrf
      <div style="margin-bottom:14px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Pilih dari Katalog Master Pelanggaran</label>
        <select id="selectKatalogPelanggaran" name="katalog_pelanggaran_id" class="input-field" style="width:100%;" onchange="autoFillPelanggaran(this)">
          <option value="">-- Tindakan Kustom / Manual --</option>
          @foreach($katalogPelanggarans as $kp)
            <option value="{{ $kp->id }}" data-nama="{{ $kp->nama_pelanggaran }}" data-poin="{{ $kp->poin_pelanggaran }}" data-desc="{{ $kp->deskripsi }}">
              {{ $kp->nama_pelanggaran }} (+{{ $kp->poin_pelanggaran }} Poin) [{{ strtoupper($kp->kategori) }}]
            </option>
          @endforeach
        </select>
      </div>

      <div style="margin-bottom:14px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Nama Pelanggaran / Tindakan <span style="color:var(--red);">*</span></label>
        <input type="text" id="inputPelanggaranNama" name="nama_pelanggaran" class="input-field" placeholder="Misal: Merokok / Atribut Tidak Lengkap / Membawa HP" required style="width:100%;" />
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Bobot Poin Ditambah <span style="color:var(--red);">*</span></label>
          <input type="number" id="inputPelanggaranPoin" name="poin_ditambah" class="input-field" value="10" min="1" max="200" required style="width:100%; font-family:var(--font-mono); font-weight:800;" />
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Tanggal Pelanggaran <span style="color:var(--red);">*</span></label>
          <input type="date" name="tanggal" class="input-field" value="{{ date('Y-m-d') }}" required style="width:100%; font-family:var(--font-mono);" />
        </div>
      </div>

      <div style="margin-bottom:18px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Uraian / Kronologi Singkat (Opsional)</label>
        <textarea id="inputPelanggaranCatatan" name="catatan" class="input-field" rows="2" placeholder="Tuliskan keterangan detail kejadian atau barang bukti..." style="width:100%;"></textarea>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModalPelanggaranSiswa()">Batal</button>
        <button type="submit" class="btn btn-gold">
          <i class="bi bi-exclamation-triangle-fill" style="margin-right:4px;"></i> Simpan Pelanggaran &amp; Tambah Poin
        </button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL EDIT LOG PERISTIWA --}}
<div class="modal-overlay" id="modalEditLog">
  <div class="modal-card" style="max-width:520px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:10px;">
      <h3 style="font-size:16px; font-weight:800; color:var(--text); margin:0;">
        Edit Peristiwa Timeline Pembinaan
      </h3>
      <button type="button" class="btn btn-sm btn-outline" style="padding:2px 6px;" onclick="closeModalEditLog()">Tutup</button>
    </div>

    <form id="formEditLog" action="" method="POST">
      @csrf
      @method('PUT')
      <div style="margin-bottom:12px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Judul / Nama Peristiwa <span style="color:var(--red);">*</span></label>
        <input type="text" id="editLogJudul" name="judul_kegiatan" class="input-field" required style="width:100%;" />
      </div>

      <div style="margin-bottom:12px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Tanggal Peristiwa <span style="color:var(--red);">*</span></label>
        <input type="date" id="editLogTanggal" name="tanggal_kegiatan" class="input-field" required style="width:100%; font-family:var(--font-mono);" />
      </div>

      <div style="margin-bottom:18px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Uraian Tindakan / Catatan Peristiwa <span style="color:var(--red);">*</span></label>
        <textarea id="editLogUraian" name="uraian_tindakan" class="input-field" rows="4" required style="width:100%;"></textarea>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModalEditLog()">Batal</button>
        <button type="submit" class="btn btn-gold">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

@include('partials.crop_modal')

<script>
  function openModalLog() { document.getElementById('modalLog').classList.add('active'); }
  function closeModalLog() { document.getElementById('modalLog').classList.remove('active'); }

  function openModalEditLog(id, judul, uraian, tanggal) {
    const form = document.getElementById('formEditLog');
    form.action = `/admin/disiplin/{{ $kasus->id }}/log/${id}`;
    document.getElementById('editLogJudul').value = judul;
    document.getElementById('editLogUraian').value = uraian;
    document.getElementById('editLogTanggal').value = tanggal;
    document.getElementById('modalEditLog').classList.add('active');
  }
  function closeModalEditLog() {
    document.getElementById('modalEditLog').classList.remove('active');
  }

  function openModalUpload() { document.getElementById('modalUpload').classList.add('active'); }
  function closeModalUpload() { document.getElementById('modalUpload').classList.remove('active'); }

  function openModalTindakLanjutDossier() { document.getElementById('modalEskalasiDossier').classList.add('active'); }
  function closeModalTindakLanjutDossier() { document.getElementById('modalEskalasiDossier').classList.remove('active'); }

  function openModalRewardSiswa() { document.getElementById('modalRewardSiswa').classList.add('active'); }
  function closeModalRewardSiswa() { document.getElementById('modalRewardSiswa').classList.remove('active'); }

  function openModalPelanggaranSiswa() { document.getElementById('modalPelanggaranSiswa').classList.add('active'); }
  function closeModalPelanggaranSiswa() { document.getElementById('modalPelanggaranSiswa').classList.remove('active'); }

  function handleTahapChange(val) {
    const box = document.getElementById('boxPresetKepsek');
    if (box) {
      box.style.display = (val === 'tahap_4_kepsek') ? 'block' : 'none';
    }
  }

  function applyPresetKepsek(type) {
    const textarea = document.getElementById('textareaCatatanTindakLanjut');
    const inputSanksi = document.getElementById('inputSanksiTambahan');

    if (type === 'sp3') {
      textarea.value = "Berdasarkan evaluasi menyeluruh bersama Waka Kesiswaan dan Guru BK, Kepala Sekolah menetapkan Peringatan Terakhir (SP-3) kepada siswa serta penandatanganan Pakta Integritas Bermaterai di hadapan orang tua.";
      inputSanksi.value = "Penerbitan Surat Peringatan Terakhir (SP-3) & Pakta Integritas Bermaterai";
    } else if (type === 'skorsing3') {
      textarea.value = "Kepala Sekolah menetapkan sanksi Skorsing Pembinaan Mandiri di Rumah selama 3 (tiga) hari efektif dengan kewajiban mengerjakan modul pembinaan karakter dan didampingi orang tua.";
      inputSanksi.value = "Skorsing Akademik Pembinaan Mandiri di Rumah (3 Hari Efektif)";
    } else if (type === 'skorsing7') {
      textarea.value = "Kepala Sekolah menetapkan sanksi Skorsing Pembinaan Khusus di Rumah selama 7 (tujuh) hari efektif dengan evaluasi berkala bersama orang tua dan Guru BK.";
      inputSanksi.value = "Skorsing Akademik Pembinaan Khusus di Rumah (7 Hari Efektif)";
    } else if (type === 'mutasi') {
      textarea.value = "Setelah mempertimbangkan bobot pelanggaran kritis dan ketidakmampuan mematuhi komitmen sekolah, Kepala Sekolah merekomendasikan pengembalian pembinaan peserta didik kepada orang tua / mutasi ke sekolah lain.";
      inputSanksi.value = "Rekomendasi Pemindahan / Pengembalian Siswa ke Orang Tua";
    } else if (type === 'amnesti') {
      textarea.value = "Kepala Sekolah memberikan Amnesti Disiplin Khusus atas iktikad baik dan komitmen pemulihan sikap yang ditunjukkan oleh siswa dan orang tua dalam masa pembinaan.";
      inputSanksi.value = "Amnesti Disiplin Pimpinan Sekolah & Pemulihan Status";
    }
  }

  function autoFillReward(select) {
    const selected = select.options[select.selectedIndex];
    if (selected && selected.value) {
      document.getElementById('inputRewardNama').value = selected.getAttribute('data-nama') || '';
      document.getElementById('inputRewardPoin').value = selected.getAttribute('data-poin') || 5;
      document.getElementById('inputRewardCatatan').value = selected.getAttribute('data-desc') || '';
    }
  }

  function autoFillPelanggaran(select) {
    const selected = select.options[select.selectedIndex];
    if (selected && selected.value) {
      document.getElementById('inputPelanggaranNama').value = selected.getAttribute('data-nama') || '';
      document.getElementById('inputPelanggaranPoin').value = selected.getAttribute('data-poin') || 10;
      document.getElementById('inputPelanggaranCatatan').value = selected.getAttribute('data-desc') || '';
    }
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeModalLog();
      closeModalUpload();
      closeModalTindakLanjutDossier();
      closeModalRewardSiswa();
      closeModalPelanggaranSiswa();
    }
  });
</script>

</body>
</html>
