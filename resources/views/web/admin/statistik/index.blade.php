<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Grafik &amp; Statistik Pengunjung Website — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <style>
    :root {
      --stat-text: #000000;
      --stat-text-sub: #000000;
      --stat-bg: #ffffff;
      --stat-card: #ffffff;
      --stat-border: #c8dfdb;
      --palette-deep: #3368a0;
      --palette-ocean: #66a3bf;
      --palette-mint: #c8dfdb;
      --palette-cream: #f2efe7;
    }

    body {
      color: var(--stat-text);
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .stat-hero-header {
      background: #f2efe7;
      border: 1px solid #c8dfdb;
      border-radius: 16px;
      padding: 24px 28px;
      margin-bottom: 22px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
      box-shadow: 0 4px 16px rgba(51, 104, 160, 0.05);
    }

    .stat-title {
      font-size: 22px;
      font-weight: 900;
      color: #000000;
      margin: 0 0 6px;
      letter-spacing: -0.02em;
    }

    .stat-subtitle {
      font-size: 13px;
      color: #000000;
      font-weight: 600;
      opacity: 0.85;
      margin: 0;
    }

    .stat-period-filter {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #ffffff;
      border: 1px solid #c8dfdb;
      padding: 4px;
      border-radius: 10px;
    }

    .stat-period-btn {
      padding: 6px 14px;
      font-size: 11.5px;
      font-weight: 800;
      color: #000000;
      text-decoration: none;
      border-radius: 7px;
      transition: all 0.15s ease;
    }

    .stat-period-btn.active {
      background: #3368a0;
      color: #ffffff;
      box-shadow: 0 2px 6px rgba(51, 104, 160, 0.25);
    }

    .stat-period-btn:not(.active):hover {
      background: #c8dfdb;
      color: #000000;
    }

    /* Grid 4 Kartu KPI */
    .stat-kpi-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
      margin-bottom: 22px;
    }

    .stat-kpi-card {
      background: var(--stat-card);
      border: 1px solid #c8dfdb;
      border-radius: 14px;
      padding: 18px 20px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-kpi-card:hover {
      transform: translateY(-2px);
      border-color: #3368a0;
      box-shadow: 0 8px 20px rgba(51, 104, 160, 0.08);
    }

    .stat-kpi-label {
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: #000000;
      margin-bottom: 4px;
    }

    .stat-kpi-val {
      font-size: 26px;
      font-weight: 900;
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
      color: #000000;
      line-height: 1.15;
      margin-bottom: 4px;
    }

    .stat-kpi-sub {
      font-size: 11.5px;
      font-weight: 600;
      color: #000000;
      opacity: 0.8;
    }

    /* Visual Charts Section */
    .stat-charts-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 20px;
      margin-bottom: 22px;
    }

    .stat-chart-box {
      background: var(--stat-card);
      border: 1px solid #c8dfdb;
      border-radius: 16px;
      padding: 22px 24px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .stat-chart-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 18px;
    }

    .stat-chart-title {
      font-size: 15px;
      font-weight: 900;
      color: #000000;
      margin: 0;
    }

    .stat-chart-badge {
      font-size: 10.5px;
      font-weight: 800;
      padding: 3px 9px;
      border-radius: 6px;
      background: #f2efe7;
      border: 1px solid #c8dfdb;
      color: #000000;
    }

    /* Top Pages Table */
    .stat-table-box {
      background: var(--stat-card);
      border: 1px solid #c8dfdb;
      border-radius: 16px;
      padding: 22px 24px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
      margin-bottom: 30px;
    }

    .stat-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 12px;
    }

    .stat-table th {
      text-align: left;
      font-size: 11px;
      font-weight: 800;
      color: #000000;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      padding: 10px 14px;
      border-bottom: 2px solid #c8dfdb;
      background: #f2efe7;
    }

    .stat-table td {
      padding: 12px 14px;
      font-size: 12.5px;
      color: #000000;
      font-weight: 600;
      border-bottom: 1px solid #c8dfdb;
    }

    .stat-table tr:hover td {
      background: #f2efe7;
    }

    .rank-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 22px;
      height: 22px;
      border-radius: 6px;
      background: #3368a0;
      color: #ffffff;
      font-size: 11px;
      font-weight: 800;
    }

    @media (max-width: 1100px) {
      .stat-kpi-grid { grid-template-columns: repeat(2, 1fr); }
      .stat-charts-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 640px) {
      .stat-kpi-grid { grid-template-columns: 1fr; }
      .stat-hero-header { padding: 18px 16px; }
      .stat-title { font-size: 19px; }
    }
  </style>
</head>
<body>
<div class="app-container">
  @include('partials.sidebar_web')
  
  <main class="main-content">
    
    {{-- Top Hero Header --}}
    <div class="stat-hero-header">
      <div>
        <div style="display:inline-flex; align-items:center; gap:6px; background:#c8dfdb; border:1px solid #66a3bf; border-radius:6px; padding:3px 8px; font-size:10.5px; font-weight:800; color:#3368a0; margin-bottom:8px;">
          <span>Hak Akses: Administrator Eksklusif</span>
        </div>
        <h1 class="stat-title">Grafik &amp; Analisis Pengunjung Website</h1>
        <p class="stat-subtitle">Pantau lalu lintas kunjungan masyarakat, calon peserta didik, dan siswa ke portal web resmi SMKN 1 Air Naningan.</p>
      </div>

      {{-- Period Filter Selector --}}
      <div class="stat-period-filter">
        <a href="{{ route('admin.statistik.web', ['days' => 7]) }}" class="stat-period-btn {{ $days == 7 ? 'active' : '' }}">7 Hari</a>
        <a href="{{ route('admin.statistik.web', ['days' => 14]) }}" class="stat-period-btn {{ $days == 14 ? 'active' : '' }}">14 Hari</a>
        <a href="{{ route('admin.statistik.web', ['days' => 30]) }}" class="stat-period-btn {{ $days == 30 ? 'active' : '' }}">30 Hari</a>
        <a href="{{ route('admin.statistik.web', ['days' => 90]) }}" class="stat-period-btn {{ $days == 90 ? 'active' : '' }}">90 Hari</a>
      </div>
    </div>

    {{-- 4 Summary Cards --}}
    <div class="stat-kpi-grid">
      <div class="stat-kpi-card">
        <div class="stat-kpi-label">Kunjungan Hari Ini</div>
        <div class="stat-kpi-val">{{ number_format($summary['today_views']) }}</div>
        <div class="stat-kpi-sub">{{ number_format($summary['today_unique']) }} Pengunjung Unik</div>
      </div>

      <div class="stat-kpi-card">
        <div class="stat-kpi-label">Pengunjung Unik Hari Ini</div>
        <div class="stat-kpi-val">{{ number_format($summary['today_unique']) }}</div>
        <div class="stat-kpi-sub">Berdasarkan Sesi IP Valid</div>
      </div>

      <div class="stat-kpi-card">
        <div class="stat-kpi-label">Kunjungan Bulan Ini</div>
        <div class="stat-kpi-val">{{ number_format($summary['month_views']) }}</div>
        <div class="stat-kpi-sub">{{ number_format($summary['month_unique']) }} Pengunjung Unik</div>
      </div>

      <div class="stat-kpi-card">
        <div class="stat-kpi-label">Total Sepanjang Waktu</div>
        <div class="stat-kpi-val">{{ number_format($summary['total_views']) }}</div>
        <div class="stat-kpi-sub">{{ number_format($summary['total_unique']) }} Akumulasi Unik</div>
      </div>
    </div>

    {{-- Charts Section --}}
    <div class="stat-charts-grid">
      
      {{-- Line Chart: Tren Pengunjung --}}
      <div class="stat-chart-box">
        <div class="stat-chart-head">
          <div>
            <h2 class="stat-chart-title">Tren Kunjungan Harian</h2>
            <span style="font-size:11.5px; font-weight:600; color:#000000; opacity:0.8;">Rentang {{ $days }} Hari Terakhir</span>
          </div>
          <span class="stat-chart-badge">Total vs Unik</span>
        </div>
        <div style="position:relative; height:280px; width:100%;">
          <canvas id="visitorTrendChart"></canvas>
        </div>
      </div>

      {{-- Donut Chart: Komposisi Perangkat --}}
      <div class="stat-chart-box">
        <div class="stat-chart-head">
          <div>
            <h2 class="stat-chart-title">Perangkat Pengunjung</h2>
            <span style="font-size:11.5px; font-weight:600; color:#000000; opacity:0.8;">HP vs Komputer (Desktop)</span>
          </div>
          <span class="stat-chart-badge">{{ $devices['total'] }} Sesi</span>
        </div>
        <div style="position:relative; height:210px; width:100%; display:flex; align-items:center; justify-content:center;">
          <canvas id="deviceDonutChart"></canvas>
        </div>
        <div style="display:flex; justify-content:space-around; margin-top:16px; border-top:1px solid #c8dfdb; padding-top:12px;">
          <div style="text-align:center;">
            <div style="font-size:10px; font-weight:800; color:#000000; text-transform:uppercase;">Mobile (HP)</div>
            <div style="font-size:16px; font-weight:900; color:#000000;">{{ $devices['mobile_pct'] }}%</div>
            <div style="font-size:10.5px; color:#000000; font-weight:600; opacity:0.7;">{{ $devices['mobile'] }} kali</div>
          </div>
          <div style="text-align:center;">
            <div style="font-size:10px; font-weight:800; color:#000000; text-transform:uppercase;">Desktop</div>
            <div style="font-size:16px; font-weight:900; color:#000000;">{{ $devices['desktop_pct'] }}%</div>
            <div style="font-size:10.5px; color:#000000; font-weight:600; opacity:0.7;">{{ $devices['desktop'] }} kali</div>
          </div>
          @if($devices['tablet'] > 0)
          <div style="text-align:center;">
            <div style="font-size:10px; font-weight:800; color:#000000; text-transform:uppercase;">Tablet</div>
            <div style="font-size:16px; font-weight:900; color:#000000;">{{ $devices['tablet_pct'] }}%</div>
            <div style="font-size:10.5px; color:#000000; font-weight:600; opacity:0.7;">{{ $devices['tablet'] }} kali</div>
          </div>
          @endif
        </div>
      </div>

    </div>

    {{-- Top Pages Table --}}
    <div class="stat-table-box">
      <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
        <div>
          <h2 style="font-size:15px; font-weight:900; color:#000000; margin:0 0 2px;">Halaman Paling Sering Dikunjungi</h2>
          <span style="font-size:11.5px; color:#000000; font-weight:600; opacity:0.8;">10 Halaman &amp; Konten Terpopuler dalam {{ $days }} Hari Terakhir</span>
        </div>
        <a href="{{ route('admin.portal') }}" class="btn" style="font-size:11.5px; font-weight:800; background:#3368a0; color:#ffffff; border:none; padding:7px 16px; border-radius:8px; text-decoration:none; box-shadow:0 2px 6px rgba(51,104,160,0.25);">
          Kembali ke DCC
        </a>
      </div>

      <div style="overflow-x:auto;">
        <table class="stat-table">
          <thead>
            <tr>
              <th style="width:50px;">#</th>
              <th>Alamat Halaman (URL Path)</th>
              <th style="width:160px; text-align:right;">Total Tayangan</th>
              <th style="width:160px; text-align:right;">Pengunjung Unik</th>
            </tr>
          </thead>
          <tbody>
            @forelse($topPages as $idx => $p)
              <tr>
                <td><span class="rank-badge">{{ $idx + 1 }}</span></td>
                <td>
                  <a href="{{ $p['url'] }}" target="_blank" style="color:#000000; font-weight:800; text-decoration:none;" title="Buka tautan di tab baru">
                    {{ $p['url'] == '/' ? '/ (Beranda Utama)' : $p['url'] }}
                  </a>
                </td>
                <td style="text-align:right; font-family:ui-monospace, SFMono-Regular, monospace; font-weight:900;">
                  {{ number_format($p['views']) }} kali
                </td>
                <td style="text-align:right; font-family:ui-monospace, SFMono-Regular, monospace; font-weight:800;">
                  {{ number_format($p['uniques']) }} unik
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" style="text-align:center; padding:30px; color:#000000; font-weight:700;">
                  Belum ada data kunjungan publik yang tercatat. Kunjungan akan otomatis terdata saat pengunjung membuka halaman website sekolah.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<script>
  // Inisialisasi Chart.js
  document.addEventListener('DOMContentLoaded', function () {
    // 1. Line Chart Tren Kunjungan
    const ctxTrend = document.getElementById('visitorTrendChart');
    if (ctxTrend) {
      const labels = @json($trend['labels']);
      const views = @json($trend['views']);
      const uniques = @json($trend['uniques']);

      new Chart(ctxTrend, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [
            {
              label: 'Total Tayangan (Views)',
              data: views,
              borderColor: '#3368a0',
              backgroundColor: 'rgba(51, 104, 160, 0.12)',
              fill: true,
              tension: 0.35,
              borderWidth: 2.5,
              pointRadius: 3,
              pointBackgroundColor: '#3368a0',
              pointHoverRadius: 6,
            },
            {
              label: 'Pengunjung Unik (Visitors)',
              data: uniques,
              borderColor: '#66a3bf',
              backgroundColor: 'transparent',
              borderDash: [4, 4],
              tension: 0.35,
              borderWidth: 2,
              pointRadius: 3,
              pointBackgroundColor: '#66a3bf',
              pointHoverRadius: 5,
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'top',
              labels: {
                color: '#000000',
                font: {
                  weight: 'bold',
                  family: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
                }
              }
            },
            tooltip: {
              backgroundColor: '#3368a0',
              titleColor: '#ffffff',
              bodyColor: '#ffffff',
              padding: 10,
              displayColors: false
            }
          },
          scales: {
            x: {
              ticks: {
                color: '#000000',
                font: { weight: '700', size: 10.5 }
              },
              grid: {
                color: 'rgba(200, 223, 219, 0.35)'
              }
            },
            y: {
              beginAtZero: true,
              ticks: {
                precision: 0,
                color: '#000000',
                font: { weight: '800', size: 10.5 }
              },
              grid: {
                color: 'rgba(200, 223, 219, 0.35)'
              }
            }
          }
        }
      });
    }

    // 2. Donut Chart Perangkat
    const ctxDonut = document.getElementById('deviceDonutChart');
    if (ctxDonut) {
      const mobCount = {{ $devices['mobile'] }};
      const dskCount = {{ $devices['desktop'] }};
      const tabCount = {{ $devices['tablet'] }};

      new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
          labels: ['Mobile (HP)', 'Desktop', 'Tablet'],
          datasets: [{
            data: [mobCount, dskCount, tabCount],
            backgroundColor: ['#3368a0', '#66a3bf', '#c8dfdb'],
            borderWidth: 2,
            borderColor: '#ffffff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              backgroundColor: '#3368a0',
              titleColor: '#ffffff',
              bodyColor: '#ffffff',
              padding: 10
            }
          },
          cutout: '70%'
        }
      });
    }
  });
</script>

</body>
</html>
