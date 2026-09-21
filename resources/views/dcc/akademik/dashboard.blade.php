@extends('dcc.akademik.layout')

@section('title', 'Dasbor Akademik & KBM')
@section('breadcrumb', 'Dasbor')

@section('content')

{{-- ========================================================================== --}}
{{-- 1. HERO WELCOME BANNER                                                     --}}
{{-- ========================================================================== --}}
<div class="akademik-hero">
  <div class="akademik-hero-top">
    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
      <span class="akademik-hero-badge">
        <i class="bi bi-mortarboard-fill"></i> Kurikulum Merdeka SMK
      </span>
      <span class="akademik-hero-badge">
        <i class="bi bi-calendar-event"></i> Tahun Ajaran {{ $ta?->tahun_ajaran ?? '2026/2027' }} · Ganjil
      </span>
      <span class="akademik-hero-badge" style="background:rgba(255,255,255,0.22);">
        <i class="bi bi-clock-history"></i> {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
      </span>
    </div>

    <div style="display:flex; align-items:center; gap:8px;">
      <span style="font-size:12px; font-weight:700; color:rgba(255,255,255,0.9); display:flex; align-items:center; gap:6px;">
        <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#34d399; box-shadow:0 0 10px #34d399;"></span>
        Ekosistem DCC Aktif
      </span>
    </div>
  </div>

  <h1 class="akademik-hero-title">
    Pusat Kendali Akademik &amp; KBM Terpadu
  </h1>
  <p class="akademik-hero-desc">
    Sistem Manajemen Pembelajaran SMKN 1 Air Naningan yang mengintegrasikan Roster Jadwal Wakakur, Jurnal KBM Harian Guru, Asesmen Ujian Daring (CBT), Buku Nilai &amp; Leger, PKL Industri, serta Penilaian Karakter P5BK.
  </p>

  <div class="akademik-hero-actions">
    <a href="{{ route('akademik.jurnal.create') }}" class="akademik-hero-btn akademik-hero-btn-primary">
      <i class="bi bi-plus-circle-fill"></i>
      <span>Isi Jurnal KBM Hari Ini</span>
    </a>
    <a href="{{ route('akademik.jadwal.index') }}" class="akademik-hero-btn akademik-hero-btn-ghost">
      <i class="bi bi-calendar3-week"></i>
      <span>Roster Jadwal Wakakur</span>
    </a>
    <a href="{{ route('akademik.asesmen.index') }}" class="akademik-hero-btn akademik-hero-btn-ghost">
      <i class="bi bi-laptop"></i>
      <span>Asesmen Online (CBT)</span>
    </a>
    <a href="{{ route('akademik.nilai.leger') }}" class="akademik-hero-btn akademik-hero-btn-ghost">
      <i class="bi bi-table"></i>
      <span>Leger Nilai Kelas</span>
    </a>
  </div>
</div>

{{-- ========================================================================== --}}
{{-- 2. KPI STATS GRID (6 PILAR UTAMA)                                          --}}
{{-- ========================================================================== --}}
<div class="akademik-kpi-grid">
  {{-- 1. Mapel Aktif --}}
  <a href="{{ route('akademik.matpel.index') }}" class="akademik-kpi-card" title="Kelola Mata Pelajaran">
    <div>
      <div class="akademik-kpi-val">{{ $totalMapel }}</div>
      <div class="akademik-kpi-label">Mata Pelajaran Aktif</div>
    </div>
    <div class="akademik-kpi-icon icon-violet">
      <i class="bi bi-journal-bookmark-fill"></i>
    </div>
  </a>

  {{-- 2. Alokasi Mengajar --}}
  <a href="{{ route('akademik.jadwal.index', ['tab' => 'distribusi']) }}" class="akademik-kpi-card" title="Distribusi Beban Mengajar (JJM)">
    <div>
      <div class="akademik-kpi-val">{{ $totalDistribusi }}</div>
      <div class="akademik-kpi-label">Alokasi Beban Jam (JJM)</div>
    </div>
    <div class="akademik-kpi-icon icon-indigo">
      <i class="bi bi-calendar3-week"></i>
    </div>
  </a>

  {{-- 3. Jurnal KBM Hari Ini --}}
  <a href="{{ route('akademik.jurnal.index') }}" class="akademik-kpi-card" title="Jurnal KBM Harian">
    <div>
      <div class="akademik-kpi-val" style="color:#059669;">{{ $jurnalHariIni }}</div>
      <div class="akademik-kpi-label">Jurnal Terisi Hari Ini</div>
    </div>
    <div class="akademik-kpi-icon icon-emerald">
      <i class="bi bi-check2-circle"></i>
    </div>
  </a>

  {{-- 4. Asesmen Online CBT --}}
  <a href="{{ route('akademik.asesmen.index') }}" class="akademik-kpi-card" title="Asesmen Penilaian Online">
    <div>
      <div class="akademik-kpi-val" style="color:#2563eb;">{{ $asesmenAktif }}</div>
      <div class="akademik-kpi-label">Asesmen Online Aktif</div>
    </div>
    <div class="akademik-kpi-icon icon-blue">
      <i class="bi bi-laptop"></i>
    </div>
  </a>

  {{-- 5. Nilai & Leger Siswa --}}
  <a href="{{ route('akademik.nilai.index') }}" class="akademik-kpi-card" title="Input & Rekap Nilai Siswa">
    <div>
      <div class="akademik-kpi-val" style="color:#0891b2;">{{ $totalNilai }}</div>
      <div class="akademik-kpi-label">Nilai Siswa Terinput</div>
    </div>
    <div class="akademik-kpi-icon icon-cyan">
      <i class="bi bi-award-fill"></i>
    </div>
  </a>

  {{-- 6. Siswa PKL DU/DI --}}
  <a href="{{ route('akademik.pkl.index') }}" class="akademik-kpi-card" title="Praktik Kerja Lapangan (PKL)">
    <div>
      <div class="akademik-kpi-val" style="color:#d97706;">{{ $siswaPklAktif }}</div>
      <div class="akademik-kpi-label">Siswa Magang / PKL</div>
    </div>
    <div class="akademik-kpi-icon icon-amber">
      <i class="bi bi-buildings-fill"></i>
    </div>
  </a>
</div>

{{-- ========================================================================== --}}
{{-- 3. PUSAT AKSES CEPAT MODUL (6 PILAR AKADEMIK)                              --}}
{{-- ========================================================================== --}}
<div style="margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
  <h2 style="font-size:16px; font-weight:800; color:var(--ak-dark); margin:0; display:flex; align-items:center; gap:8px;">
    <i class="bi bi-grid-fill text-primary"></i>
    Pilar Layanan Akademik &amp; KBM SMK
  </h2>
  <span style="font-size:12px; color:#64748b;">Akses langsung fitur operasional harian</span>
</div>

<div class="akademik-modules-grid">
  {{-- Modul 1: Jadwal & Roster Pelajaran --}}
  <a href="{{ route('akademik.jadwal.index') }}" class="akademik-module-card">
    <div class="akademik-module-icon icon-violet">
      <i class="bi bi-calendar-week-fill"></i>
    </div>
    <div style="flex:1;">
      <div class="akademik-module-title">
        <span>Jadwal Pelajaran (Roster)</span>
        <i class="bi bi-arrow-right-short" style="font-size:18px; color:var(--ak-primary);"></i>
      </div>
      <p class="akademik-module-desc">
        Matriks jadwal mingguan kelas X, XI, XII, formulasi cepat blok jam, live anti-bentrok, &amp; cetak resmi Wakakur.
      </p>
    </div>
  </a>

  {{-- Modul 2: Jurnal KBM Harian & Presensi --}}
  <a href="{{ route('akademik.jurnal.index') }}" class="akademik-module-card">
    <div class="akademik-module-icon icon-emerald">
      <i class="bi bi-journal-text"></i>
    </div>
    <div style="flex:1;">
      <div class="akademik-module-title">
        <span>Jurnal KBM &amp; Presensi</span>
        <i class="bi bi-arrow-right-short" style="font-size:18px; color:#059669;"></i>
      </div>
      <p class="akademik-module-desc">
        Pencatatan materi sesi KBM tatap muka, kehadiran siswa per jam pelajaran (H/I/S/A), dan refleksi guru.
      </p>
    </div>
  </a>

  {{-- Modul 3: Asesmen Penilaian Online (CBT) --}}
  <a href="{{ route('akademik.asesmen.index') }}" class="akademik-module-card">
    <div class="akademik-module-icon icon-indigo">
      <i class="bi bi-laptop"></i>
    </div>
    <div style="flex:1;">
      <div class="akademik-module-title">
        <span>Asesmen Online (CBT)</span>
        <span class="ak-badge ak-badge-primary" style="font-size:10px; padding:2px 6px;">CBT</span>
      </div>
      <p class="akademik-module-desc">
        Ujian daring real-time, bank soal pilihan ganda, hitung nilai otomatis, dan 1-klik transfer nilai ke leger.
      </p>
    </div>
  </a>

  {{-- Modul 4: Buku Nilai & Leger Kelas --}}
  <a href="{{ route('akademik.nilai.index') }}" class="akademik-module-card">
    <div class="akademik-module-icon icon-cyan">
      <i class="bi bi-table"></i>
    </div>
    <div style="flex:1;">
      <div class="akademik-module-title">
        <span>Buku Nilai &amp; Leger Kelas</span>
        <i class="bi bi-arrow-right-short" style="font-size:18px; color:#0891b2;"></i>
      </div>
      <p class="akademik-module-desc">
        Rekap nilai formatif 1..3, sumatif STS/SAS, otomatisasi predikat capaian kompetensi, &amp; cetak leger kelas.
      </p>
    </div>
  </a>

  {{-- Modul 5: Praktik Kerja Lapangan (PKL) --}}
  <a href="{{ route('akademik.pkl.index') }}" class="akademik-module-card">
    <div class="akademik-module-icon icon-amber">
      <i class="bi bi-buildings-fill"></i>
    </div>
    <div style="flex:1;">
      <div class="akademik-module-title">
        <span>Praktik Kerja Lapangan</span>
        <i class="bi bi-arrow-right-short" style="font-size:18px; color:#d97706;"></i>
      </div>
      <p class="akademik-module-desc">
        Manajemen mitra industri (DU/DI), penempatan siswa magang tingkat XII, dan penilaian pembimbing lapangan.
      </p>
    </div>
  </a>

  {{-- Modul 6: Projek Penguatan Karakter P5BK --}}
  <a href="{{ route('akademik.p5bk.index') }}" class="akademik-module-card">
    <div class="akademik-module-icon icon-rose">
      <i class="bi bi-stars"></i>
    </div>
    <div style="flex:1;">
      <div class="akademik-module-title">
        <span>Projek Karakter P5BK</span>
        <i class="bi bi-arrow-right-short" style="font-size:18px; color:#e11d48;"></i>
      </div>
      <p class="akademik-module-desc">
        Penilaian 6 Dimensi Profil Pelajar Pancasila &amp; etos budaya kerja industri khas vokasi SMK Negeri 1 Air Naningan.
      </p>
    </div>
  </a>
</div>

{{-- ========================================================================== --}}
{{-- 4. DYNAMIC SECTION: JADWAL KBM HARI INI & AKTIVITAS TERKINI                --}}
{{-- ========================================================================== --}}
<div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:20px; align-items:start;">

  {{-- Kolom Kiri: Jadwal KBM Hari Ini (Live Roster) --}}
  <div class="akademik-card">
    <div class="akademik-card-header" style="flex-wrap:wrap; gap:10px;">
      <div>
        <h3 class="akademik-card-title">
          <i class="bi bi-calendar-check-fill text-primary"></i>
          <span>Jadwal KBM Hari Ini ({{ $hariAktif }})</span>
        </h3>
        <div style="font-size:11.5px; color:#64748b; margin-top:2px;">
          Jadwal resmi dari formulasi roster mingguan Wakakur
        </div>
      </div>

      {{-- Filter Hari Cepat --}}
      <div style="display:flex; gap:4px; flex-wrap:wrap;">
        @foreach($daftarHariKbm as $h)
          <a href="{{ route('akademik.dashboard', ['hari' => $h]) }}" 
             class="ak-btn {{ $hariAktif === $h ? 'ak-btn-primary' : 'ak-btn-secondary' }}" 
             style="font-size:11px; padding:4px 9px; font-weight:700;">
            {{ substr($h, 0, 3) }}
          </a>
        @endforeach
      </div>
    </div>

    {{-- Banner Guru Piket Hari Ini --}}
    @if($piketHariIni)
      <div style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:12px 18px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; font-size:12px;">
        <div style="display:flex; align-items:center; gap:8px;">
          <span class="ak-badge ak-badge-primary" style="font-size:11px;">
            <i class="bi bi-shield-check me-1"></i> Waka Piket:
          </span>
          <b style="color:var(--ak-dark);">{{ $piketHariIni->wakaPiket?->nama ?? 'Belum Ditentukan' }}</b>
        </div>
        <div style="color:#64748b; font-size:11.5px;">
          <b>Guru Piket:</b> 
          {{ $piketHariIni->guru_list->isNotEmpty() ? $piketHariIni->guru_list->pluck('nama')->implode(', ') : 'Belum Ditentukan' }}
        </div>
      </div>
    @endif

    <div class="akademik-card-body" style="padding:0;">
      @if($jadwalHariIni->isEmpty())
        <div style="padding:40px 20px; text-align:center; color:#64748b; font-size:13px;">
          <i class="bi bi-calendar-x" style="font-size:40px; color:#94a3b8; display:block; margin-bottom:8px;"></i>
          Tidak ada jadwal pelajaran yang tercatat untuk hari {{ $hariAktif }}.
        </div>
      @else
        <div class="akademik-table-wrap">
          <table class="akademik-table">
            <thead>
              <tr>
                <th style="width:70px;">Jam Ke</th>
                <th>Kelas / Rombel</th>
                <th>Mata Pelajaran</th>
                <th>Guru Pengampu</th>
                <th style="width:100px; text-align:center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($jadwalHariIni as $jdw)
                <tr>
                  <td>
                    <span class="ak-badge ak-badge-primary" style="font-weight:800; font-size:11px;">
                      Jam Ke-{{ $jdw->jam_ke }}
                    </span>
                  </td>
                  <td>
                    <span class="ak-badge ak-badge-secondary" style="font-weight:700;">
                      {{ $jdw->rombel?->nama_rombel ?? '-' }}
                    </span>
                  </td>
                  <td>
                    <div style="font-weight:700; color:var(--ak-primary); font-size:12.5px;">
                      {{ $jdw->mataPelajaran?->nama_mapel ?? '-' }}
                    </div>
                    <div style="font-size:11px; color:#64748b;">
                      Kode: {{ $jdw->singkatan_mapel ?? $jdw->mataPelajaran?->singkatan_mapel ?? '-' }}
                    </div>
                  </td>
                  <td>
                    <div style="font-weight:700; color:var(--ak-dark); font-size:12.5px;">
                      {{ $jdw->guru?->nama ?? '-' }}
                    </div>
                    @if($jdw->kode_guru)
                      <span class="ak-badge ak-badge-secondary" style="font-size:10px;">Kode: {{ $jdw->kode_guru }}</span>
                    @endif
                  </td>
                  <td style="text-align:center;">
                    <a href="{{ route('akademik.jurnal.create') }}" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:11px; padding:4px 8px;" title="Isi Jurnal Untuk Jadwal Ini">
                      <i class="bi bi-pencil-square me-1"></i> Jurnal
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div style="padding:12px 18px; background:#fafafa; border-top:1px solid #e2e8f0; text-align:right;">
          <a href="{{ route('akademik.jadwal.index', ['tab' => 'roster', 'hari' => $hariAktif]) }}" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:12px;">
            Lihat Matriks Lengkap Seluruh Jam (0 s/d 12) &rarr;
          </a>
        </div>
      @endif
    </div>
  </div>

  {{-- Kolom Kanan: Feed Jurnal & Asesmen Online --}}
  <div style="display:flex; flex-direction:column; gap:20px;">
    
    {{-- Card 1: Jurnal KBM Harian Terbaru --}}
    <div class="akademik-card" style="margin-bottom:0;">
      <div class="akademik-card-header">
        <h3 class="akademik-card-title">
          <i class="bi bi-activity text-success"></i>
          <span>Jurnal KBM Terisi</span>
        </h3>
        <a href="{{ route('akademik.jurnal.index') }}" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:11.5px;">Lihat Semua</a>
      </div>
      <div class="akademik-card-body" style="padding:0;">
        @if($jurnalTerbaru->isEmpty())
          <div style="padding:32px 18px; text-align:center; color:#64748b; font-size:12.5px;">
            <i class="bi bi-journal-check" style="font-size:32px; color:#cbd5e1; display:block; margin-bottom:6px;"></i>
            Belum ada jurnal KBM yang tercatat hari ini.
            <div style="margin-top:10px;">
              <a href="{{ route('akademik.jurnal.create') }}" class="ak-btn ak-btn-primary ak-btn-sm" style="font-size:11.5px;">
                + Mulai Isi Jurnal
              </a>
            </div>
          </div>
        @else
          <div style="display:flex; flex-direction:column;">
            @foreach($jurnalTerbaru as $jrn)
              <div style="padding:12px 16px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; gap:10px;">
                <div>
                  <div style="font-size:12.5px; font-weight:700; color:var(--ak-dark);">
                    {{ $jrn->distribusi?->guru?->nama ?? '-' }}
                  </div>
                  <div style="font-size:11.5px; color:var(--ak-primary); font-weight:600;">
                    {{ $jrn->distribusi?->mataPelajaran?->nama_mapel ?? '-' }} · <span style="color:#64748b;">{{ $jrn->distribusi?->rombel?->nama_rombel }}</span>
                  </div>
                  <div style="font-size:11px; color:#94a3b8; margin-top:2px;">
                    {{ \Carbon\Carbon::parse($jrn->tanggal)->isoFormat('D MMM Y') }} · Pertemuan Ke-{{ $jrn->pertemuan_ke }}
                  </div>
                </div>
                <a href="{{ route('akademik.jurnal.show', $jrn->id) }}" class="ak-btn ak-btn-secondary ak-btn-sm" title="Lihat Detail">
                  <i class="bi bi-eye"></i>
                </a>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    {{-- Card 2: Asesmen Penilaian Online Berjalan --}}
    <div class="akademik-card" style="margin-bottom:0;">
      <div class="akademik-card-header">
        <h3 class="akademik-card-title">
          <i class="bi bi-laptop text-primary"></i>
          <span>Asesmen Ujian Daring</span>
        </h3>
        <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:11.5px;">Kelola</a>
      </div>
      <div class="akademik-card-body" style="padding:0;">
        @if($asesmenMendatang->isEmpty())
          <div style="padding:32px 18px; text-align:center; color:#64748b; font-size:12.5px;">
            <i class="bi bi-laptop" style="font-size:32px; color:#cbd5e1; display:block; margin-bottom:6px;"></i>
            Tidak ada asesmen online aktif saat ini.
            <div style="margin-top:10px;">
              <a href="{{ route('akademik.asesmen.create') }}" class="ak-btn ak-btn-primary ak-btn-sm" style="font-size:11.5px;">
                + Buat Asesmen Baru
              </a>
            </div>
          </div>
        @else
          <div style="display:flex; flex-direction:column;">
            @foreach($asesmenMendatang as $asm)
              <div style="padding:12px 16px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; gap:10px;">
                <div>
                  <div style="font-size:12.5px; font-weight:700; color:var(--ak-dark);">
                    {{ $asm->judul }}
                  </div>
                  <div style="font-size:11.5px; color:#64748b;">
                    {{ $asm->distribusi?->rombel?->nama_rombel }} · {{ $asm->distribusi?->mataPelajaran?->nama_mapel }}
                  </div>
                  <div style="margin-top:3px; display:flex; gap:6px;">
                    <span class="ak-badge ak-badge-warning" style="font-size:10.5px;">
                      <i class="bi bi-clock me-1"></i>{{ $asm->durasi_menit }} Menit
                    </span>
                  </div>
                </div>
                <div style="display:flex; gap:4px;">
                  <a href="{{ route('akademik.asesmen.hasil', $asm->id) }}" class="ak-btn ak-btn-secondary ak-btn-sm" title="Pantau Hasil">
                    <i class="bi bi-bar-chart"></i>
                  </a>
                  <a href="{{ route('akademik.asesmen.kerjakan', $asm->id) }}" class="ak-btn ak-btn-primary ak-btn-sm" title="Buka Simulasi">
                    <i class="bi bi-play-circle"></i>
                  </a>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

  </div>

</div>

@endsection
