<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Peringkat &amp; Apresiasi Presisi Waktu — SIRANI SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/peringkat.css') }}?v={{ filemtime(public_path('css/peringkat.css')) }}">
</head>
<body>
<div class="app-container">
  @include('partials.sidebar')
  <main class="main-content">
    {{-- ULTRA COMPACT SLIM HEADER BAR --}}
    <div class="panel no-print" style="background:var(--bg-2); border:1px solid var(--border); padding:10px 16px; margin-bottom:12px; border-radius:var(--r-md); box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <h1 style="margin:0; font-size:16px; font-weight:900; color:var(--text); display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-trophy-fill" style="color:#000000; font-size:16px;"></i> Peringkat Kehadiran
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:11.5px; color:var(--text-3);">
            Leaderboard keteladanan presisi waktu
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
          {{-- Category Switcher --}}
          <div style="display:inline-flex; background:var(--bg-3); border:1.5px solid var(--border-2); border-radius:8px; padding:2px; gap:2px;">
            <button type="button" class="btn btn-sm" onclick="document.getElementById('inputKategori').value='siswa'; document.getElementById('formPeringkat').submit();" style="height:28px; padding:0 12px; font-size:11.5px; font-weight:800; border-radius:6px; border:none; {{ $kategori === 'siswa' ? 'background:#000000; color:#FFFFFF;' : 'background:transparent; color:var(--text-2);' }}">
              <i class="bi bi-people-fill"></i> Siswa
            </button>
            <button type="button" class="btn btn-sm" onclick="document.getElementById('inputKategori').value='guru'; document.getElementById('formPeringkat').submit();" style="height:28px; padding:0 12px; font-size:11.5px; font-weight:800; border-radius:6px; border:none; {{ $kategori === 'guru' ? 'background:#000000; color:#FFFFFF;' : 'background:transparent; color:var(--text-2);' }}">
              <i class="bi bi-person-badge-fill"></i> Guru
            </button>
          </div>

          {{-- Export CSV --}}
          <a href="{{ route('peringkat.export-csv', request()->all()) }}" class="btn btn-sm btn-outline" style="height:32px; padding:0 10px; font-size:11.5px; font-weight:800; color:#000000; border:1px solid var(--border-2); background:var(--bg-2); display:inline-flex; align-items:center; gap:4px; border-radius:6px; text-decoration:none;">
            <i class="bi bi-file-earmark-arrow-down-fill"></i> Export CSV
          </a>

          @include('partials.header_actions')
        </div>
      </div>
    </div>

    <!-- ══ Filter Parameters Card ══ -->
    <div class="panel" style="margin-bottom:14px; padding:10px 14px;">
      <form method="GET" action="{{ route('peringkat.index') }}" id="formPeringkat">
        <input type="hidden" name="kategori" id="inputKategori" value="{{ $kategori }}" />
        
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:8px; align-items:flex-end;">
          {{-- Periode Tipe --}}
          <div class="form-group" style="margin-bottom:0;">
            <label style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">Periode</label>
            <select name="periode" id="selectPeriode" class="input-field" style="width:100%; height:32px; font-size:11.5px; font-weight:700; padding:0 8px;" onchange="document.getElementById('formPeringkat').submit();">
              <option value="semester" {{ $periode === 'semester' ? 'selected' : '' }}>Semester</option>
              <option value="bulan" {{ $periode === 'bulan' ? 'selected' : '' }}>Bulanan</option>
              <option value="kustom" {{ $periode === 'kustom' ? 'selected' : '' }}>Kustom</option>
            </select>
          </div>

          @if($periode === 'semester')
            <div class="form-group" style="margin-bottom:0;">
              <label style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">Semester</label>
              <select name="semester" class="input-field" style="width:100%; height:32px; font-size:11.5px; padding:0 8px;" onchange="document.getElementById('formPeringkat').submit();">
                <option value="ganjil" {{ $semesterTipe === 'ganjil' ? 'selected' : '' }}>Ganjil (Jul - Des)</option>
                <option value="genap" {{ $semesterTipe === 'genap' ? 'selected' : '' }}>Genap (Jan - Jun)</option>
              </select>
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">Tahun Ajaran</label>
              <select name="tahun_ajaran_id" class="input-field" style="width:100%; height:32px; font-size:11.5px; padding:0 8px;" onchange="document.getElementById('formPeringkat').submit();">
                @foreach($semuaTa as $ta)
                  <option value="{{ $ta->id }}" {{ $taPilihan && $taPilihan->id == $ta->id ? 'selected' : '' }}>{{ $ta->nama }}</option>
                @endforeach
              </select>
            </div>
          @elseif($periode === 'bulan')
            <div class="form-group" style="margin-bottom:0;">
              <label style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">Pilih Bulan</label>
              <input type="month" name="bulan" value="{{ $bulan }}" class="input-field" style="width:100%; height:32px; font-size:11.5px; padding:0 8px;" onchange="document.getElementById('formPeringkat').submit();" />
            </div>
          @else
            <div class="form-group" style="margin-bottom:0;">
              <label style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">Tgl Mulai</label>
              <input type="date" name="tanggal_mulai" value="{{ $startDate }}" class="input-field" style="width:100%; height:32px; font-size:11.5px; padding:0 8px;" />
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">Tgl Selesai</label>
              <input type="date" name="tanggal_selesai" value="{{ $effectiveEndDate }}" class="input-field" style="width:100%; height:32px; font-size:11.5px; padding:0 8px;" />
            </div>
          @endif

          @if($kategori === 'siswa')
            <div class="form-group" style="margin-bottom:0;">
              <label style="font-size:10.5px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:4px; display:block;">Kelas / Rombel</label>
              <select name="rombel_id" class="input-field" style="width:100%; height:32px; font-size:11.5px; padding:0 8px;" onchange="document.getElementById('formPeringkat').submit();" {{ $isWaliKelas ? 'disabled' : '' }}>
                <option value="">Semua Rombel</option>
                @foreach($rombels as $r)
                  <option value="{{ $r->id }}" {{ $rombelId == $r->id ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
                @endforeach
              </select>
            </div>
          @endif

          <div>
            <button type="submit" class="btn btn-sm btn-gold" style="height:32px; width:100%; font-weight:800; font-size:11.5px; border-radius:6px;">
              <i class="bi bi-funnel-fill"></i> Terapkan
            </button>
          </div>
        </div>
      </form>
    </div>

    <!-- ══ PODIUM TELADAN DISIPLIN ══ -->
    @php
      $top1 = $top1 ?? $leaderboard->firstWhere('rank', 1);
      $top2 = $top2 ?? $leaderboard->firstWhere('rank', 2);
      $top3 = $top3 ?? $leaderboard->firstWhere('rank', 3);
      $totalRanked = $totalRanked ?? $leaderboard->total();
    @endphp

    @if($top1)
      <section class="podium-section">
        <div style="margin-bottom:16px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <h3 style="font-size:16px; font-weight:900; color:var(--text); margin:0;">
              🏆 Podium Teladan Disiplin — {{ $periodeLabel }}
            </h3>
            <div style="font-size:12px; color:var(--text-3); margin-top:2px;">
              Apresiasi presisi waktu kedatangan terpagi, total durasi jam belajar/mengajar, dan ketepatan kehadiran.
            </div>
          </div>
        </div>

        @if($totalRanked >= 3)
          {{-- ── 3 PODIUM OLYMPIC (PERAK - EMAS - PERUNGGU) ── --}}
          <div class="podium-grid-3">
            {{-- JUARA 2 (PERAK) --}}
            <div class="podium-card rank-2">
              <div>
                <div class="trophy-avatar">🥈</div>
                <div>
                  <span class="badge-rank-title badge-rank-2">🥈 Peringkat #2</span>
                </div>
                <h4 class="podium-user-name">{{ $top2['nama'] }}</h4>
                <div class="podium-user-meta">{{ $top2['ident'] }} · {{ $top2['sub'] }}</div>

                <div class="podium-stats-box">
                  <div class="podium-stat-score">
                    <span class="podium-pct-val">{{ $top2['persen_kehadiran'] }}%</span>
                    <span style="font-size:11px; color:var(--text-2); font-weight:700;">{{ $top2['predikat'] }} ({{ $top2['hadir_tepat'] }}x Tepat)</span>
                  </div>
                  <div class="stat-row-item">
                    <span class="stat-row-label"><i class="bi bi-clock-history" style="color:#94A3B8;"></i> Rata-rata Masuk:</span>
                    <span class="stat-row-val">{{ $top2['avg_masuk_str'] }}</span>
                  </div>
                  <div class="stat-row-item">
                    <span class="stat-row-label"><i class="bi bi-lightning-charge-fill" style="color:#000000;"></i> Rekor Terpagi:</span>
                    <span class="stat-row-val">{{ $top2['terpagi_str'] }}</span>
                  </div>
                  <div class="stat-row-item">
                    <span class="stat-row-label"><i class="bi bi-hourglass-split" style="color:#000000;"></i> Total Durasi:</span>
                    <span class="stat-row-val">{{ $top2['total_durasi_str'] }}</span>
                  </div>
                </div>
              </div>

              <a href="{{ $kategori === 'siswa' ? route('peringkat.piagam-siswa', ['id' => $top2['id'], 'rank' => 2, 'predikat' => $top2['predikat'], 'periode' => $periodeLabel, 'persen' => $top2['persen_kehadiran'], 'avg_masuk' => $top2['avg_masuk_str'], 'durasi' => $top2['total_durasi_str']]) : route('peringkat.piagam-guru', ['id' => $top2['id'], 'rank' => 2, 'predikat' => $top2['predikat'], 'periode' => $periodeLabel, 'persen' => $top2['persen_kehadiran'], 'avg_masuk' => $top2['avg_masuk_str'], 'durasi' => $top2['total_durasi_str']]) }}" target="_blank" class="btn btn-sm btn-outline-mono" style="width:100%; font-size:12px; font-weight:800; padding:8px 0; text-decoration:none;">
                <i class="bi bi-award-fill" style="color:#000000;"></i> Cetak Piagam
              </a>
            </div>

            {{-- JUARA 1 (EMAS / UTAMA) --}}
            <div class="podium-card rank-1">
              <div>
                <div class="trophy-avatar">🥇</div>
                <div>
                  <span class="badge-rank-title badge-rank-1">👑 JUARA 1 TELADAN UTAMA</span>
                </div>
                <h4 class="podium-user-name">{{ $top1['nama'] }}</h4>
                <div class="podium-user-meta" style="color:#000000; font-weight:800;">{{ $top1['ident'] }} · {{ $top1['sub'] }}</div>

                <div class="podium-stats-box">
                  <div class="podium-stat-score">
                    <span class="podium-pct-val">{{ $top1['persen_kehadiran'] }}%</span>
                    <span style="font-size:11.5px; color:var(--text-2); font-weight:800;">{{ $top1['predikat'] }} ({{ $top1['hadir_tepat'] }}x Tepat)</span>
                  </div>
                  <div class="stat-row-item">
                    <span class="stat-row-label"><i class="bi bi-clock-history" style="color:#000000;"></i> Rata-rata Masuk:</span>
                    <span class="stat-row-val">{{ $top1['avg_masuk_str'] }}</span>
                  </div>
                  <div class="stat-row-item">
                    <span class="stat-row-label"><i class="bi bi-lightning-charge-fill" style="color:#000000;"></i> Rekor Terpagi:</span>
                    <span class="stat-row-val">{{ $top1['terpagi_str'] }}</span>
                  </div>
                  <div class="stat-row-item">
                    <span class="stat-row-label"><i class="bi bi-hourglass-split" style="color:#000000;"></i> Total Durasi:</span>
                    <span class="stat-row-val">{{ $top1['total_durasi_str'] }}</span>
                  </div>
                </div>
              </div>

              <a href="{{ $kategori === 'siswa' ? route('peringkat.piagam-siswa', ['id' => $top1['id'], 'rank' => 1, 'predikat' => $top1['predikat'], 'periode' => $periodeLabel, 'persen' => $top1['persen_kehadiran'], 'avg_masuk' => $top1['avg_masuk_str'], 'durasi' => $top1['total_durasi_str']]) : route('peringkat.piagam-guru', ['id' => $top1['id'], 'rank' => 1, 'predikat' => $top1['predikat'], 'periode' => $periodeLabel, 'persen' => $top1['persen_kehadiran'], 'avg_masuk' => $top1['avg_masuk_str'], 'durasi' => $top1['total_durasi_str']]) }}" target="_blank" class="btn btn-gold" style="width:100%; font-size:13px; font-weight:900; padding:10px 0; text-decoration:none;">
                <i class="bi bi-award-fill"></i> Cetak Piagam Penghargaan
              </a>
            </div>

            {{-- JUARA 3 (PERUNGGU) --}}
            <div class="podium-card rank-3">
              <div>
                <div class="trophy-avatar">🥉</div>
                <div>
                  <span class="badge-rank-title badge-rank-3">🥉 Peringkat #3</span>
                </div>
                <h4 class="podium-user-name">{{ $top3['nama'] }}</h4>
                <div class="podium-user-meta">{{ $top3['ident'] }} · {{ $top3['sub'] }}</div>

                <div class="podium-stats-box">
                  <div class="podium-stat-score">
                    <span class="podium-pct-val">{{ $top3['persen_kehadiran'] }}%</span>
                    <span style="font-size:11px; color:var(--text-2); font-weight:700;">{{ $top3['predikat'] }} ({{ $top3['hadir_tepat'] }}x Tepat)</span>
                  </div>
                  <div class="stat-row-item">
                    <span class="stat-row-label"><i class="bi bi-clock-history" style="color:#000000;"></i> Rata-rata Masuk:</span>
                    <span class="stat-row-val">{{ $top3['avg_masuk_str'] }}</span>
                  </div>
                  <div class="stat-row-item">
                    <span class="stat-row-label"><i class="bi bi-lightning-charge-fill" style="color:#000000;"></i> Rekor Terpagi:</span>
                    <span class="stat-row-val">{{ $top3['terpagi_str'] }}</span>
                  </div>
                  <div class="stat-row-item">
                    <span class="stat-row-label"><i class="bi bi-hourglass-split" style="color:#000000;"></i> Total Durasi:</span>
                    <span class="stat-row-val">{{ $top3['total_durasi_str'] }}</span>
                  </div>
                </div>
              </div>

              <a href="{{ $kategori === 'siswa' ? route('peringkat.piagam-siswa', ['id' => $top3['id'], 'rank' => 3, 'predikat' => $top3['predikat'], 'periode' => $periodeLabel, 'persen' => $top3['persen_kehadiran'], 'avg_masuk' => $top3['avg_masuk_str'], 'durasi' => $top3['total_durasi_str']]) : route('peringkat.piagam-guru', ['id' => $top3['id'], 'rank' => 3, 'predikat' => $top3['predikat'], 'periode' => $periodeLabel, 'persen' => $top3['persen_kehadiran'], 'avg_masuk' => $top3['avg_masuk_str'], 'durasi' => $top3['total_durasi_str']]) }}" target="_blank" class="btn btn-sm btn-outline-mono" style="width:100%; font-size:12px; font-weight:800; padding:8px 0; text-decoration:none;">
                <i class="bi bi-award-fill" style="color:#000000;"></i> Cetak Piagam
              </a>
            </div>
          </div>

        @elseif($totalRanked == 2)
          {{-- ── 2 PODIUM (EMAS & PERAK) ── --}}
          <div class="podium-grid-2">
            {{-- JUARA 1 (EMAS / UTAMA) --}}
            <div class="podium-card rank-1">
              <div>
                <div class="trophy-avatar">🥇</div>
                <div>
                  <span class="badge-rank-title badge-rank-1">👑 JUARA 1 TELADAN UTAMA</span>
                </div>
                <h4 class="podium-user-name">{{ $top1['nama'] }}</h4>
                <div class="podium-user-meta" style="color:#000000; font-weight:800;">{{ $top1['ident'] }} · {{ $top1['sub'] }}</div>

                <div class="podium-stats-box">
                  <div class="podium-stat-score">
                    <span class="podium-pct-val">{{ $top1['persen_kehadiran'] }}%</span>
                    <span style="font-size:11.5px; color:var(--text-2); font-weight:800;">{{ $top1['predikat'] }} ({{ $top1['hadir_tepat'] }}x Tepat)</span>
                  </div>
                  <div class="stat-row-item">
                    <span class="stat-row-label"><i class="bi bi-clock-history" style="color:#000000;"></i> Rata-rata Masuk:</span>
                    <span class="stat-row-val">{{ $top1['avg_masuk_str'] }}</span>
                  </div>
                  <div class="stat-row-item">
                    <span class="stat-row-label"><i class="bi bi-lightning-charge-fill" style="color:#000000;"></i> Rekor Terpagi:</span>
                    <span class="stat-row-val">{{ $top1['terpagi_str'] }}</span>
                  </div>
                  <div class="stat-row-item">
                    <span class="stat-row-label"><i class="bi bi-hourglass-split" style="color:#000000;"></i> Total Durasi:</span>
                    <span class="stat-row-val">{{ $top1['total_durasi_str'] }}</span>
                  </div>
                </div>
              </div>

              <a href="{{ $kategori === 'siswa' ? route('peringkat.piagam-siswa', ['id' => $top1['id'], 'rank' => 1, 'predikat' => $top1['predikat'], 'periode' => $periodeLabel, 'persen' => $top1['persen_kehadiran'], 'avg_masuk' => $top1['avg_masuk_str'], 'durasi' => $top1['total_durasi_str']]) : route('peringkat.piagam-guru', ['id' => $top1['id'], 'rank' => 1, 'predikat' => $top1['predikat'], 'periode' => $periodeLabel, 'persen' => $top1['persen_kehadiran'], 'avg_masuk' => $top1['avg_masuk_str'], 'durasi' => $top1['total_durasi_str']]) }}" target="_blank" class="btn btn-gold" style="width:100%; font-size:13px; font-weight:900; padding:10px 0; text-decoration:none;">
                <i class="bi bi-award-fill"></i> Cetak Piagam Penghargaan
              </a>
            </div>

            {{-- JUARA 2 (PERAK) --}}
            <div class="podium-card rank-2">
              <div>
                <div class="trophy-avatar">🥈</div>
                <div>
                  <span class="badge-rank-title badge-rank-2">🥈 Peringkat #2</span>
                </div>
                <h4 class="podium-user-name">{{ $top2['nama'] }}</h4>
                <div class="podium-user-meta">{{ $top2['ident'] }} · {{ $top2['sub'] }}</div>

                <div class="podium-stats-box">
                  <div class="podium-stat-score">
                    <span class="podium-pct-val">{{ $top2['persen_kehadiran'] }}%</span>
                    <span style="font-size:11px; color:var(--text-2); font-weight:700;">{{ $top2['predikat'] }} ({{ $top2['hadir_tepat'] }}x Tepat)</span>
                  </div>
                  <div class="stat-row-item">
                    <span class="stat-row-label"><i class="bi bi-clock-history" style="color:#000000;"></i> Rata-rata Masuk:</span>
                    <span class="stat-row-val">{{ $top2['avg_masuk_str'] }}</span>
                  </div>
                  <div class="stat-row-item">
                    <span class="stat-row-label"><i class="bi bi-lightning-charge-fill" style="color:#000000;"></i> Rekor Terpagi:</span>
                    <span class="stat-row-val">{{ $top2['terpagi_str'] }}</span>
                  </div>
                  <div class="stat-row-item">
                    <span class="stat-row-label"><i class="bi bi-hourglass-split" style="color:#000000;"></i> Total Durasi:</span>
                    <span class="stat-row-val">{{ $top2['total_durasi_str'] }}</span>
                  </div>
                </div>
              </div>

              <a href="{{ $kategori === 'siswa' ? route('peringkat.piagam-siswa', ['id' => $top2['id'], 'rank' => 2, 'predikat' => $top2['predikat'], 'periode' => $periodeLabel, 'persen' => $top2['persen_kehadiran'], 'avg_masuk' => $top2['avg_masuk_str'], 'durasi' => $top2['total_durasi_str']]) : route('peringkat.piagam-guru', ['id' => $top2['id'], 'rank' => 2, 'predikat' => $top2['predikat'], 'periode' => $periodeLabel, 'persen' => $top2['persen_kehadiran'], 'avg_masuk' => $top2['avg_masuk_str'], 'durasi' => $top2['total_durasi_str']]) }}" target="_blank" class="btn btn-sm btn-outline-mono" style="width:100%; font-size:12px; font-weight:800; padding:8px 0; text-decoration:none;">
                <i class="bi bi-award-fill" style="color:#000000;"></i> Cetak Piagam
              </a>
            </div>
          </div>

        @else
          {{-- ── 1 PODIUM SOLO (CENTRAL CHAMPION HERO) ── --}}
          <div class="podium-solo">
            <div class="podium-card rank-1">
              <div>
                <div class="trophy-avatar">🥇</div>
                <div>
                  <span class="badge-rank-title badge-rank-1">👑 JUARA 1 TELADAN UTAMA</span>
                </div>
                <h4 class="podium-user-name">{{ $top1['nama'] }}</h4>
                <div class="podium-user-meta" style="color:#000000; font-weight:800;">{{ $top1['ident'] }} · {{ $top1['sub'] }}</div>

                <div class="podium-stats-box">
                  <div class="podium-stat-score">
                    <span class="podium-pct-val">{{ $top1['persen_kehadiran'] }}%</span>
                    <span style="font-size:11.5px; color:var(--text-2); font-weight:800;">{{ $top1['predikat'] }} ({{ $top1['hadir_tepat'] }}x Tepat)</span>
                  </div>
                  <div class="stat-row-item">
                    <span class="stat-row-label"><i class="bi bi-clock-history" style="color:#000000;"></i> Rata-rata Masuk:</span>
                    <span class="stat-row-val">{{ $top1['avg_masuk_str'] }}</span>
                  </div>
                  <div class="stat-row-item">
                    <span class="stat-row-label"><i class="bi bi-lightning-charge-fill" style="color:#000000;"></i> Rekor Terpagi:</span>
                    <span class="stat-row-val">{{ $top1['terpagi_str'] }}</span>
                  </div>
                  <div class="stat-row-item">
                    <span class="stat-row-label"><i class="bi bi-hourglass-split" style="color:#000000;"></i> Total Durasi:</span>
                    <span class="stat-row-val">{{ $top1['total_durasi_str'] }}</span>
                  </div>
                </div>
              </div>

              <a href="{{ $kategori === 'siswa' ? route('peringkat.piagam-siswa', ['id' => $top1['id'], 'rank' => 1, 'predikat' => $top1['predikat'], 'periode' => $periodeLabel, 'persen' => $top1['persen_kehadiran'], 'avg_masuk' => $top1['avg_masuk_str'], 'durasi' => $top1['total_durasi_str']]) : route('peringkat.piagam-guru', ['id' => $top1['id'], 'rank' => 1, 'predikat' => $top1['predikat'], 'periode' => $periodeLabel, 'persen' => $top1['persen_kehadiran'], 'avg_masuk' => $top1['avg_masuk_str'], 'durasi' => $top1['total_durasi_str']]) }}" target="_blank" class="btn btn-gold" style="width:100%; font-size:13px; font-weight:900; padding:10px 0; text-decoration:none;">
                <i class="bi bi-award-fill"></i> Cetak Piagam Penghargaan
              </a>
            </div>
          </div>
        @endif
      </section>
    @endif

    <!-- ══ TABEL LEADERBOARD LENGKAP ══ -->
    <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
      <div class="panel-title" style="padding:14px 18px; margin:0; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:8px; font-weight:800; font-size:15px; color:var(--text);">
          <i class="bi bi-trophy-fill" style="color:#000000;"></i>
          <span>Tabel Peringkat Presisi Waktu</span>
        </div>
        <div style="width:260px; position:relative;">
          <input type="text" id="filterPeringkatTable" placeholder="Cari nama, NIS/NIP..." class="input-field" style="width:100%; height:36px; font-size:12px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px;" oninput="searchPeringkatTable(this.value)" />
        </div>
      </div>

      <div class="table-responsive" style="overflow-x:auto;">
        <table class="data-table" id="tableLeaderboard">
          <thead>
            <tr>
              <th style="width:65px; text-align:center; color:#000000; font-weight:900;">Rank</th>
              <th style="width:110px; text-align:center; color:#000000; font-weight:900;">{{ $kategori === 'siswa' ? 'NIS' : 'NIP' }}</th>
              <th style="text-align:left; color:#000000; font-weight:900;">Nama Lengkap</th>
              <th style="text-align:left; color:#000000; font-weight:900;">{{ $kategori === 'siswa' ? 'Rombel / Kelas' : 'Jabatan' }}</th>
              <th style="text-align:center; color:#000000; font-weight:900;" title="Rata-rata Waktu Kedatangan (Jam:Menit:Detik)">Rata2 Masuk</th>
              <th style="text-align:center; color:#000000; font-weight:900;" title="Rekor Kedatangan Terpagi (Jam:Menit:Detik)">Terpagi</th>
              <th style="text-align:center; color:#000000; font-weight:900;" title="Akumulasi Keterlambatan">Akumulasi Telat</th>
              <th style="text-align:center; color:#000000; font-weight:900;" title="Total Akumulasi Durasi Berada di Sekolah">Total Durasi</th>
              <th style="text-align:center; color:#000000; font-weight:900;">Kehadiran %</th>
              <th style="text-align:center; color:#000000; font-weight:900;">Predikat</th>
              <th style="width:90px; text-align:center; color:#000000; font-weight:900;">Piagam</th>
            </tr>
          </thead>
          <tbody>
            @forelse($leaderboard as $item)
              <tr class="leaderboard-row" data-search="{{ strtolower($item['nama'] . ' ' . $item['ident'] . ' ' . $item['sub']) }}">
                <td style="text-align:center; font-weight:900; color:#000000;">
                  @if($item['rank'] === 1)
                    <span class="badge-gold-rank">🥇 #1</span>
                  @elseif($item['rank'] === 2)
                    <span class="badge-silver-rank">🥈 #2</span>
                  @elseif($item['rank'] === 3)
                    <span class="badge-bronze-rank">🥉 #3</span>
                  @else
                    <span class="badge" style="background:var(--bg-3); color:#000000; font-weight:900; font-size:11px; padding:2px 8px; border-radius:8px;">#{{ $item['rank'] }}</span>
                  @endif
                </td>
                <td style="text-align:center; font-family:var(--font-mono); font-weight:800; color:#000000; font-size:12.5px;">
                  {{ $item['ident'] }}
                </td>
                <td style="text-align:left;">
                  <strong style="color:#000000; font-size:13.5px; font-weight:900;">{{ $item['nama'] }}</strong>
                </td>
                <td style="text-align:left; font-size:12.5px; color:#000000; font-weight:800;">
                  {{ $item['sub'] }}
                </td>
                <td style="text-align:center; font-family:var(--font-mono); font-weight:900; color:#000000; font-size:12.5px;">
                  {{ $item['avg_masuk_str'] }}
                </td>
                <td style="text-align:center; font-family:var(--font-mono); font-weight:900; color:#000000; font-size:12.5px;">
                  {{ $item['terpagi_str'] }}
                </td>
                <td style="text-align:center; font-family:var(--font-mono); font-size:12px; color:#000000; font-weight:800;">
                  {{ $item['total_terlambat_str'] }}
                </td>
                <td style="text-align:center; font-family:var(--font-mono); font-size:12px; color:#000000; font-weight:800;">
                  {{ $item['total_durasi_str'] }}
                </td>
                <td style="text-align:center;">
                  <div style="font-family:var(--font-mono); font-weight:900; font-size:13.5px; color:#000000;">
                    {{ $item['persen_kehadiran'] }}%
                  </div>
                </td>
                <td style="text-align:center;">
                  @php
                    $pBadge = 'background:transparent; color:#000000; border:none;';
                  @endphp
                  <span class="badge" style="{{ $pBadge }} font-weight:900; font-size:10.5px;">
                    {{ $item['predikat'] }}
                  </span>
                </td>
                <td style="text-align:center;">
                  <a href="{{ $kategori === 'siswa' ? route('peringkat.piagam-siswa', ['id' => $item['id'], 'rank' => $item['rank'], 'predikat' => $item['predikat'], 'periode' => $periodeLabel, 'persen' => $item['persen_kehadiran'], 'avg_masuk' => $item['avg_masuk_str'], 'durasi' => $item['total_durasi_str']]) : route('peringkat.piagam-guru', ['id' => $item['id'], 'rank' => $item['rank'], 'predikat' => $item['predikat'], 'periode' => $periodeLabel, 'persen' => $item['persen_kehadiran'], 'avg_masuk' => $item['avg_masuk_str'], 'durasi' => $item['total_durasi_str']]) }}" target="_blank" class="btn btn-sm btn-outline-mono" style="font-size:11px; padding:4px 10px; font-weight:800; text-decoration:none;" title="Cetak Piagam Penghargaan">
                    <i class="bi bi-award" style="color:#000000;"></i> Piagam
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" style="text-align:center; padding:36px; color:var(--text-3);">
                  <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px; opacity:0.4;"></i>
                  Belum ada data kehadiran untuk periode yang dipilih.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($leaderboard instanceof \Illuminate\Pagination\LengthAwarePaginator && $leaderboard->hasPages())
        <div style="padding:14px 18px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; background:var(--bg-2);">
          <div style="font-size:12.5px; color:var(--text-2); font-weight:600;">
            Menampilkan <strong style="color:#000000;">{{ $leaderboard->firstItem() }}</strong> – <strong style="color:#000000;">{{ $leaderboard->lastItem() }}</strong> dari <strong style="color:var(--text);">{{ $leaderboard->total() }}</strong> peringkat {{ $kategori }}
          </div>
          <div>
            {{ $leaderboard->withQueryString()->links() }}
          </div>
        </div>
      @endif
    </div>
  </main>
</div>

<script>
  function searchPeringkatTable(q) {
    const term = (q || '').toLowerCase().trim();
    const rows = document.querySelectorAll('.leaderboard-row');
    rows.forEach(r => {
      const text = r.dataset.search || '';
      r.style.display = text.includes(term) ? '' : 'none';
    });
  }
</script>
</body>
</html>
