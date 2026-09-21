@extends('dcc.akademik.layout')

@section('title', 'Dasbor Akademik & KBM')
@section('breadcrumb', 'Dasbor')

@section('content')

{{-- ========================================================================== --}}
{{-- 1. HERO WELCOME BANNER (SAMBUTAN WAKA KURIKULUM)                           --}}
{{-- ========================================================================== --}}
<div class="akademik-hero">
  <div class="akademik-hero-top">
    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
      <span class="akademik-hero-badge">
        <i class="bi bi-person-check-fill"></i> Tim Kurikulum &amp; Manajemen KBM
      </span>
      <span class="akademik-hero-badge">
        <i class="bi bi-calendar-event"></i> Tahun Ajaran {{ $ta?->nama ?? $ta?->tahun_ajaran ?? '2026/2027' }} · Ganjil
      </span>
      <span class="akademik-hero-badge" style="background:rgba(255,255,255,0.22);">
        <i class="bi bi-clock-history"></i> {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
      </span>
    </div>

    <div>
      @if($kalender)
        @if($kalender->is_locked)
          <span class="akademik-hero-badge" style="background:#10b981; color:#fff;">
            <i class="bi bi-shield-check me-1"></i> Kaldik Resmi Ditetapkan
          </span>
        @else
          <span class="akademik-hero-badge" style="background:#f59e0b; color:#fff;">
            <i class="bi bi-pencil-square me-1"></i> Draf Kalender Akademik
          </span>
        @endif
      @endif
    </div>
  </div>

  <h1 class="akademik-hero-title">
    Selamat Datang, {{ auth()->user()?->name ?? 'Waka Kurikulum' }}
  </h1>
  <p class="akademik-hero-desc">
    Pusat kendali dan monitoring operasional kurikulum SMKN 1 Air Naningan. Pantau penetapan kalender pendidikan, distribusi beban mengajar guru, matriks roster mingguan, dan keterisian jurnal KBM harian secara terpadu.
  </p>

  <div class="akademik-hero-actions">
    <a href="{{ route('akademik.kalender.index') }}" class="akademik-hero-btn akademik-hero-btn-primary">
      <i class="bi bi-calendar-range"></i>
      <span>Kalender Pendidikan (Kaldik)</span>
    </a>
    <a href="{{ route('akademik.jadwal.index', ['tab' => 'distribusi']) }}" class="akademik-hero-btn akademik-hero-btn-ghost">
      <i class="bi bi-file-earmark-person"></i>
      <span>SK &amp; Beban Mengajar</span>
    </a>
    <a href="{{ route('akademik.jadwal.index', ['tab' => 'jadwal']) }}" class="akademik-hero-btn akademik-hero-btn-ghost">
      <i class="bi bi-clock-history"></i>
      <span>Roster Jadwal Kelas</span>
    </a>
    <a href="{{ route('akademik.perangkat.supervisi-meja') }}" class="akademik-hero-btn akademik-hero-btn-ghost">
      <i class="bi bi-patch-check"></i>
      <span>Supervisi Perangkat</span>
    </a>
  </div>
</div>

{{-- ========================================================================== --}}
{{-- 2. AGENDA KALDIK TERDEKAT / SEDANG BERJALAN (INFORMASI PENTING WAKAKUR)    --}}
{{-- ========================================================================== --}}
@if($agendaKaldikTerdekat)
  <div style="background:#ffffff; border:1px solid #e2e8f0; border-left:4px solid #6366f1; border-radius:12px; padding:14px 18px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; box-shadow:0 2px 4px rgba(0,0,0,0.03);">
    <div style="display:flex; align-items:center; gap:12px; min-width:0;">
      <div style="width:40px; height:40px; border-radius:10px; background:#eff2fe; color:#4f46e5; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">
        <i class="bi bi-calendar-event-fill"></i>
      </div>
      <div style="min-width:0;">
        <div style="font-size:11px; font-weight:800; text-transform:uppercase; color:#6366f1; letter-spacing:0.04em;">
          Agenda Kurikulum Berjalan / Terdekat
        </div>
        <div style="font-size:14px; font-weight:800; color:var(--ak-dark); margin-top:2px;">
          {{ $agendaKaldikTerdekat->keterangan }}
          <span class="badge" style="background:{{ $agendaKaldikTerdekat->warna ?: '#f59e0b' }}; color:#ffffff; font-size:10px; font-weight:800; padding:2px 6px; margin-left:6px;">
            {{ strtoupper($agendaKaldikTerdekat->kategori) }}
          </span>
        </div>
        <div style="font-size:12px; color:#64748b; margin-top:1px;">
          <i class="bi bi-clock me-1"></i>
          {{ $agendaKaldikTerdekat->formatRentangTanggal() ?: ('Pekan ' . $agendaKaldikTerdekat->minggu_ke . ' (' . $agendaKaldikTerdekat->bulan . ')') }}
          · {{ $agendaKaldikTerdekat->jenis === 'efektif' ? 'KBM Tetap Berjalan' : 'Memotong Jam Tatap Muka KBM' }}
        </div>
      </div>
    </div>

    <div>
      <a href="{{ route('akademik.kalender.index') }}" class="ak-btn ak-btn-secondary" style="font-size:12px; padding:6px 14px; font-weight:700;">
        Lihat Seluruh Kaldik <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>
  </div>
@endif

{{-- ========================================================================== --}}
{{-- 3. INDIKATOR KUNCI KURIKULUM & KBM (KPI STRATEGIS WAKAKUR)                 --}}
{{-- ========================================================================== --}}
<div class="akademik-kpi-grid">
  {{-- 1. Rincian Pekan Efektif (Kaldik) --}}
  <a href="{{ route('akademik.kalender.index') }}" class="akademik-kpi-card" title="Kelola Kalender Pendidikan & Rincian Pekan Efektif">
    <div>
      <div class="akademik-kpi-val" style="color:#4f46e5;">
        {{ $kalender?->pekan_efektif ?? 18 }} 
        <span style="font-size:13px; font-weight:700; color:#64748b;">/ {{ $kalender?->total_pekan ?? 25 }} Pekan</span>
      </div>
      <div class="akademik-kpi-label">Pekan Efektif KBM (RPE)</div>
    </div>
    <div class="akademik-kpi-icon icon-indigo">
      <i class="bi bi-calendar-range"></i>
    </div>
  </a>

  {{-- 2. Mata Pelajaran Kurikulum --}}
  <a href="{{ route('akademik.matpel.index') }}" class="akademik-kpi-card" title="Kelola Mata Pelajaran Kurikulum">
    <div>
      <div class="akademik-kpi-val">
        {{ $totalMapel }} 
        <span style="font-size:13px; font-weight:700; color:#64748b;">Mapel</span>
      </div>
      <div class="akademik-kpi-label">Mata Pelajaran Aktif ({{ $totalDistribusi }} Sesi)</div>
    </div>
    <div class="akademik-kpi-icon icon-violet">
      <i class="bi bi-book"></i>
    </div>
  </a>

  {{-- 3. Guru Pengajar Ber-SK --}}
  <a href="{{ route('akademik.jadwal.index', ['tab' => 'distribusi']) }}" class="akademik-kpi-card" title="Distribusi Beban Mengajar Guru">
    <div>
      <div class="akademik-kpi-val" style="color:#059669;">
        {{ $totalGuruMengajar }} 
        <span style="font-size:13px; font-weight:700; color:#64748b;">/ {{ $totalGuru }} Guru</span>
      </div>
      <div class="akademik-kpi-label">Guru Terplot Beban Ajar</div>
    </div>
    <div class="akademik-kpi-icon icon-emerald">
      <i class="bi bi-file-earmark-person"></i>
    </div>
  </a>

  {{-- 4. Keterisian Jurnal KBM Hari Ini --}}
  <a href="{{ route('akademik.jurnal.index') }}" class="akademik-kpi-card" title="Monitoring Keterisian Jurnal Guru Hari Ini">
    <div>
      <div class="akademik-kpi-val" style="color:#0284c7;">
        {{ $jurnalHariIni }} 
        <span style="font-size:13px; font-weight:700; color:#64748b;">/ {{ $totalJadwalHariIni }} Sesi</span>
      </div>
      <div class="akademik-kpi-label">Jurnal KBM Terisi Hari Ini</div>
    </div>
    <div class="akademik-kpi-icon icon-cyan">
      <i class="bi bi-journal-check"></i>
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
