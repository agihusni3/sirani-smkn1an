<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Buku Kasus &amp; Disiplin Siswa — SIRANI SMKN 1 AN</title>
  @include('partials.styles')
  <style>
    .kpi-disiplin-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
      gap: 12px;
      margin-bottom: 20px;
    }
    .kpi-disiplin-card {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-sm);
      padding: 12px 14px;
      text-decoration: none;
      color: inherit;
      display: block;
      transition: all .2s ease;
    }
    .kpi-disiplin-card:hover {
      border-color: var(--gold);
      transform: translateY(-2px);
    }
    .kpi-disiplin-card.active {
      border-color: #000000;
      background: #000000 !important;
      color: #FFFFFF !important;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
    }
    .kpi-disiplin-card.active .tahap-pill-counter,
    .kpi-disiplin-card.active .kpi-disiplin-num,
    .kpi-disiplin-card.active div {
      color: #FFFFFF !important;
    }
    .kpi-disiplin-card.active i {
      color: #FFFFFF !important;
    }
    .kpi-disiplin-num {
      font-size: 24px;
      font-weight: 900;
      font-family: var(--font-mono);
      line-height: 1.1;
      margin: 4px 0;
    }
    .tahap-pill-counter {
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: .02em;
    }

    .timeline-mini {
      font-size: 11px;
      color: var(--text-2);
      line-height: 1.4;
      background: var(--bg-3);
      padding: 6px 10px;
      border-radius: var(--r-sm);
      border-left: 3px solid var(--gold);
      margin-top: 4px;
    }

    /* ─── Table Enhancements ─── */
    .data-table.disiplin-table th {
      padding: 12px 14px;
      font-size: 11px;
      letter-spacing: 0.05em;
    }
    .data-table.disiplin-table td {
      vertical-align: middle;
      padding: 12px 14px;
    }
    .btn-dossier-compact {
      width: 100%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 11.5px;
      font-weight: 800;
      color: var(--gold);
      background: var(--gold-dim);
      border: 1px solid rgba(202, 138, 4, 0.35);
      text-decoration: none;
      transition: all 0.2s ease;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      margin-bottom: 5px;
    }
    .btn-dossier-compact:hover {
      background: var(--gold);
      color: #000;
      border-color: var(--gold);
      transform: translateY(-1px);
      box-shadow: 0 4px 10px var(--gold-glow);
    }
    .action-toolbar-row {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      background: var(--bg-3);
      padding: 4px 6px;
      border-radius: 8px;
      border: 1px solid var(--border);
    }
    .btn-tool-item {
      width: 30px;
      height: 30px;
      border-radius: 6px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 1px solid transparent;
      background: var(--bg-2);
      color: var(--text-2);
      font-size: 13px;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.18s ease;
      padding: 0;
      position: relative;
    }
    .btn-tool-item:hover {
      transform: translateY(-2px);
      color: var(--text);
      border-color: var(--border-2);
      box-shadow: 0 3px 8px rgba(0,0,0,0.12);
    }
    .btn-tool-item.tool-edit:hover { background: rgba(202,138,4,0.15); color: var(--gold); border-color: var(--gold); }
    .btn-tool-item.tool-resume:hover { background: rgba(220,38,38,0.15); color: #DC2626; border-color: #DC2626; }
    .btn-tool-item.tool-check:hover { background: rgba(22,163,74,0.15); color: #16A34A; border-color: #16A34A; }
    .btn-tool-item.tool-delete:hover { background: rgba(220,38,38,0.15); color: #DC2626; border-color: #DC2626; }

    /* Floating Tooltip */
    .btn-tool-item::after {
      content: attr(data-tooltip);
      position: absolute;
      bottom: calc(100% + 7px);
      left: 50%;
      transform: translateX(-50%) translateY(4px);
      background: #0F172A;
      color: #FFFFFF;
      font-size: 10.5px;
      font-weight: 700;
      white-space: nowrap;
      padding: 4px 8px;
      border-radius: 6px;
      pointer-events: none;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.18s ease, transform 0.18s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.18s;
      box-shadow: 0 4px 14px rgba(0,0,0,0.35);
      z-index: 1000;
    }
    .btn-tool-item::before {
      content: '';
      position: absolute;
      bottom: calc(100% + 2px);
      left: 50%;
      transform: translateX(-50%) translateY(4px);
      border-width: 5px;
      border-style: solid;
      border-color: #0F172A transparent transparent transparent;
      pointer-events: none;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.18s ease, transform 0.18s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.18s;
      z-index: 1000;
    }
    .btn-tool-item:hover::after,
    .btn-tool-item:hover::before {
      opacity: 1;
      visibility: visible;
      transform: translateX(-50%) translateY(0);
    }

    /* ─── Tahap Badge Structured Styling ─── */
    .tahap-badge-wrap {
      display: inline-flex;
      flex-direction: column;
      padding: 6px 10px;
      border-radius: 8px;
      border: 1px solid var(--border-2);
      background: var(--bg-2);
      width: 100%;
      max-width: 185px;
      line-height: 1.3;
      text-align: left;
      box-shadow: var(--shadow-sm);
    }
    .tahap-badge-wrap .tahap-title {
      font-size: 11.5px;
      font-weight: 800;
      display: flex;
      align-items: center;
      gap: 5px;
      white-space: nowrap;
      color: var(--text);
    }
    .tahap-badge-wrap .tahap-sub {
      font-size: 10px;
      font-family: var(--font-mono);
      color: var(--text-2);
      font-weight: 600;
      margin-top: 2px;
    }
    .tahap-badge-wrap.tahap-1 { border-left: 3.5px solid #D97706; }
    .tahap-badge-wrap.tahap-2 { border-left: 3.5px solid #2563EB; }
    .tahap-badge-wrap.tahap-3 { border-left: 3.5px solid #EA580C; }
    .tahap-badge-wrap.tahap-4 { border-left: 3.5px solid #DC2626; }
    .tahap-badge-wrap.tahap-selesai { border-left: 3.5px solid #16A34A; }

    .catatan-card-mini {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: 8px;
      padding: 8px 12px;
      font-size: 12px;
      line-height: 1.45;
      display: flex;
      flex-direction: column;
      gap: 4px;
      box-shadow: var(--shadow-sm);
    }
    .catatan-card-mini .catatan-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 6px;
      font-size: 10.5px;
      font-weight: 700;
      color: var(--text-2);
      border-bottom: 1px solid var(--border);
      padding-bottom: 4px;
      margin-bottom: 2px;
    }
  </style>
</head>
<body>

<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    <header class="header" style="margin-bottom:20px;">
      <div class="header-title">
        <h1 style="margin:0; font-size:22px;">Buku Kasus &amp; Penegakan Disiplin Siswa</h1>
        <p style="margin-top:2px; font-size:13px; color:var(--text-3);">
          Alur Pembinaan Berjenjang: Wali Kelas &rarr; Guru BK &rarr; Waka Kesiswaan &rarr; Kepala Sekolah
        </p>
      </div>

      <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        @if($user->isAdmin() || $user->isWakaKesiswaan())
          <button type="button" class="btn btn-outline-mono" onclick="openModalPengaturanDisiplin()">
            <i class="bi bi-sliders2"></i> Aturan Poin &amp; Reward
          </button>
        @endif

        @include('partials.header_actions')
      </div>
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

    {{-- KPI COUNTER CARDS --}}
    <div class="kpi-disiplin-grid">
      <a href="{{ route('admin.disiplin.index') }}" class="kpi-disiplin-card {{ empty($tahapFilter) ? 'active' : '' }}">
        <div class="tahap-pill-counter" style="color:var(--text); font-weight:800;"><i class="bi bi-collection-fill"></i> Semua Kasus</div>
        <div class="kpi-disiplin-num" style="color:var(--text);">{{ $totalKasus }}</div>
        <div style="font-size:10.5px; color:var(--text-2);">Total kasus aktif</div>
      </a>

      <a href="{{ route('admin.disiplin.index', ['tahap' => 'tahap_1_wali_kelas']) }}" class="kpi-disiplin-card {{ $tahapFilter === 'tahap_1_wali_kelas' ? 'active' : '' }}">
        <div class="tahap-pill-counter" style="color:var(--text); font-weight:800;"><i class="bi bi-person-fill"></i> Tahap 1: Wali</div>
        <div class="kpi-disiplin-num" style="color:var(--text);">{{ $statTahap1 }}</div>
        <div style="font-size:10.5px; color:var(--text-2);">Pembinaan awal</div>
      </a>

      <a href="{{ route('admin.disiplin.index', ['tahap' => 'tahap_2_bk']) }}" class="kpi-disiplin-card {{ $tahapFilter === 'tahap_2_bk' ? 'active' : '' }}">
        <div class="tahap-pill-counter" style="color:var(--text); font-weight:800;"><i class="bi bi-heart-pulse-fill"></i> Tahap 2: BK</div>
        <div class="kpi-disiplin-num" style="color:var(--text);">{{ $statTahap2 }}</div>
        <div style="font-size:10.5px; color:var(--text-2);">Panggilan &amp; konseling</div>
      </a>

      <a href="{{ route('admin.disiplin.index', ['tahap' => 'tahap_3_wakasis']) }}" class="kpi-disiplin-card {{ $tahapFilter === 'tahap_3_wakasis' ? 'active' : '' }}">
        <div class="tahap-pill-counter" style="color:var(--text); font-weight:800;"><i class="bi bi-shield-shaded"></i> Tahap 3: Wakasis</div>
        <div class="kpi-disiplin-num" style="color:var(--text);">{{ $statTahap3 }}</div>
        <div style="font-size:10.5px; color:var(--text-2);">Sidang &amp; sanksi SP 3</div>
      </a>

      <a href="{{ route('admin.disiplin.index', ['tahap' => 'tahap_4_kepsek']) }}" class="kpi-disiplin-card {{ $tahapFilter === 'tahap_4_kepsek' ? 'active' : '' }}">
        <div class="tahap-pill-counter" style="color:var(--text); font-weight:800;"><i class="bi bi-award-fill"></i> Tahap 4: Kepsek</div>
        <div class="kpi-disiplin-num" style="color:var(--text);">{{ $statTahap4 }}</div>
        <div style="font-size:10.5px; color:var(--text-2);">Pengesahan keputusan</div>
      </a>

      <a href="{{ route('admin.disiplin.index', ['tahap' => 'selesai_pembinaan']) }}" class="kpi-disiplin-card {{ $tahapFilter === 'selesai_pembinaan' ? 'active' : '' }}">
        <div class="tahap-pill-counter" style="color:var(--text); font-weight:800;"><i class="bi bi-check-circle-fill"></i> Selesai</div>
        <div class="kpi-disiplin-num" style="color:var(--text);">{{ $statSelesai }}</div>
        <div style="font-size:10.5px; color:var(--text-2);">Disiplin pulih</div>
      </a>
    </div>

    {{-- KONTEN UTAMA: KANBAN ATAU TABEL TERPADU --}}
    <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
      {{-- Header & Toolbar Terpadu --}}
      <div style="padding:14px 18px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="font-weight:800; font-size:15px; color:var(--text); display:flex; align-items:center; gap:8px;">
          <i class="bi bi-journals" style="color:#000000;"></i>
          <span>Daftar Kasus &amp; Pembinaan Siswa</span>
        </div>
        <form action="{{ route('admin.disiplin.index') }}" method="GET" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; flex:1; justify-content:flex-end; max-width:650px;">
          <input type="hidden" name="tahap" value="{{ $tahapFilter }}" />
          
          <div style="flex:1.5; min-width:180px;">
            <input type="text" name="search" class="input-field" placeholder="Cari nama siswa / NIS..." value="{{ $search }}" style="width:100%; height:36px; font-size:12px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px;" />
          </div>

          <div style="min-width:160px;">
            <select name="rombel_id" class="input-field" style="width:100%; height:36px; font-size:12px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm);" onchange="this.form.submit()">
              <option value="">Semua Rombel</option>
              @foreach($rombels as $r)
                <option value="{{ $r->id }}" {{ $rombelId == $r->id ? 'selected' : '' }}>
                  {{ $r->nama_rombel }}
                </option>
              @endforeach
            </select>
          </div>

          <button type="submit" class="btn btn-outline" style="height:36px; padding:0 12px; font-size:12px; font-weight:700;">
            <i class="bi bi-search"></i> Cari
          </button>

          @if($search || $rombelId || $tahapFilter)
            <a href="{{ route('admin.disiplin.index') }}" class="btn btn-outline" style="height:36px; padding:0 10px; font-size:12px; color:var(--red); border-color:rgba(239,68,68,0.4);" title="Reset Filter">
              Reset
            </a>
          @endif
        </form>
      </div>

      {{-- TABEL DAFTAR KASUS --}}
      <div class="panel" style="padding:0; overflow:hidden;">
        <div class="table-responsive">
          <table class="data-table disiplin-table">
            <thead>
              <tr>
                <th style="width:38px; text-align:center;">No</th>
                <th style="width:220px;">Siswa &amp; Rombel</th>
                <th style="width:140px;">Poin &amp; Pelanggaran</th>
                <th style="width:160px;">Tahap Penanganan</th>
                <th>Catatan Terkini</th>
                <th style="width:150px; text-align:center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($kasusList as $idx => $kasus)
                @php
                  $siswa = $kasus->siswa;
                  $rombelAktif = $siswa?->siswaRombels?->where('status_keanggotaan', 'aktif')->first()?->rombel;
                  
                  $tahapMeta = [
                    'tahap_1_wali_kelas' => ['label' => 'Tahap 1 · Wali Kelas', 'color' => '#D97706', 'bg' => 'rgba(245,158,11,0.08)', 'border' => '#D97706'],
                    'tahap_2_bk'         => ['label' => 'Tahap 2 · Guru BK',     'color' => '#2563EB', 'bg' => 'rgba(37,99,235,0.08)',  'border' => '#2563EB'],
                    'tahap_3_wakasis'    => ['label' => 'Tahap 3 · Wakasis',     'color' => '#EA580C', 'bg' => 'rgba(234,88,12,0.08)',  'border' => '#EA580C'],
                    'tahap_4_kepsek'     => ['label' => 'Tahap 4 · Kepala Sekolah', 'color' => '#DC2626', 'bg' => 'rgba(220,38,38,0.08)', 'border' => '#DC2626'],
                    'selesai_pembinaan'  => ['label' => 'Selesai Pembinaan',     'color' => '#16A34A', 'bg' => 'rgba(22,163,74,0.08)',  'border' => '#16A34A'],
                  ];
                  $tMeta = $tahapMeta[$kasus->status_tahap] ?? ['label' => $kasus->status_tahap, 'color' => 'var(--text)', 'bg' => 'var(--bg-3)', 'border' => 'var(--border)'];

                  $catatan = match($kasus->status_tahap) {
                    'tahap_1_wali_kelas' => $kasus->catatan_wali_kelas ?: 'Belum ada catatan pembinaan awal.',
                    'tahap_2_bk'         => $kasus->hasil_musyawarah_bk ? 'Hasil BA: ' . $kasus->hasil_musyawarah_bk : ($kasus->catatan_bk ?: 'Menunggu panggilan ortu / konseling.'),
                    'tahap_3_wakasis'    => $kasus->sanksi_wakasis ? 'Sanksi: ' . $kasus->sanksi_wakasis : ($kasus->catatan_wakasis ?: 'Proses sidang kesiswaan.'),
                    'tahap_4_kepsek'     => $kasus->keputusan_kepsek ?: 'Menunggu pengesahan keputusan akhir.',
                    default              => 'Pembinaan kedisiplinan telah selesai.',
                  };
                @endphp
                <tr>
                  <td style="text-align:center; font-family:var(--font-mono); color:var(--text-3); font-weight:700;">{{ ($kasusList instanceof \Illuminate\Pagination\LengthAwarePaginator ? $kasusList->firstItem() : 1) + $idx }}</td>
                  <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                      <div style="width:34px; height:34px; border-radius:50%; background:var(--bg-3); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:12.5px; color:var(--text); flex-shrink:0;">
                        @if($siswa && $siswa->foto)
                          <img src="{{ $siswa->foto_url }}" alt="" style="width:100%; height:100%; border-radius:50%; object-fit:cover;" />
                        @else
                          {{ substr($siswa->nama ?? 'S', 0, 1) }}
                        @endif
                      </div>
                      <div style="min-width:0;">
                        <strong style="color:var(--text); font-size:13px; display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $siswa->nama ?? '-' }}</strong>
                        <div style="font-size:11px; color:var(--text-3); font-family:var(--font-mono); margin-top:2px;">
                          {{ $siswa->nis ?? '-' }} · <span style="color:var(--text); font-weight:700;">{{ $rombelAktif->nama_rombel ?? '-' }}</span>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div style="font-size:13px; font-weight:900; font-family:var(--font-mono); color:var(--text);">
                      {{ $kasus->poin_bersih }} Poin
                    </div>
                    <div style="font-size:11px; color:var(--text-3); margin-top:2px; display:flex; gap:6px; flex-wrap:wrap;">
                      @if($kasus->total_alpha > 0)
                        <span>Alpha: <strong style="color:var(--text);">{{ $kasus->total_alpha }}x</strong></span>
                      @endif
                      @if($kasus->total_bolos > 0)
                        <span>Bolos: <strong style="color:var(--text);">{{ $kasus->total_bolos }}x</strong></span>
                      @endif
                      @if($kasus->total_terlambat > 0)
                        <span>Telat: <strong style="color:var(--text);">{{ $kasus->total_terlambat }}x</strong></span>
                      @endif
                    </div>
                  </td>
                  <td>
                    <div style="display:inline-flex; align-items:center; padding:4px 8px; border-radius:6px; background:{{ $tMeta['bg'] }}; border-left:3px solid {{ $tMeta['border'] }};">
                      <span style="font-size:11.5px; font-weight:800; color:var(--text); white-space:nowrap;">
                        {{ $tMeta['label'] }}
                      </span>
                    </div>
                  </td>
                  <td>
                    <div style="font-size:12px; color:var(--text); line-height:1.4;">
                      {{ $catatan }}
                    </div>
                    <div style="font-size:10.5px; color:var(--text-3); font-family:var(--font-mono); margin-top:3px;">
                      {{ $kasus->updated_at->format('d/m/Y H:i') }}
                    </div>
                  </td>
                  <td style="text-align:center;">
                    <div style="display:flex; align-items:center; justify-content:center; gap:5px;">
                      <a href="{{ route('admin.disiplin.show', $kasus->id) }}" class="btn btn-sm btn-outline-mono" style="height:30px; padding:0 10px; font-size:11px; font-weight:800;" title="Buka Dossier Lengkap">
                        Dossier
                      </a>

                      <button type="button" class="btn-tool-item tool-edit" data-tooltip="Tindak Lanjut" onclick="openModalTindakLanjut({{ $kasus->id }}, '{{ addslashes($siswa->nama ?? '') }}', '{{ $kasus->status_tahap }}')" style="width:30px; height:30px;">
                        <i class="bi bi-pencil-square"></i>
                      </button>

                      <a href="{{ route('admin.disiplin.resume.cetak', $kasus->id) }}" target="_blank" class="btn-tool-item tool-resume" data-tooltip="Cetak Resume" style="width:30px; height:30px;">
                        <i class="bi bi-printer"></i>
                      </a>

                      @if($kasus->status_tahap !== 'selesai_pembinaan')
                        <form action="{{ route('admin.disiplin.selesaikan', $kasus->id) }}" method="POST" style="margin:0; display:inline;" onsubmit="return confirm('Tandai masa pembinaan {{ $siswa->nama }} telah SELESAI?')">
                          @csrf
                          <button type="submit" class="btn-tool-item tool-check" data-tooltip="Selesaikan" style="width:30px; height:30px;">
                            <i class="bi bi-check-lg"></i>
                          </button>
                        </form>
                      @endif

                      @if($user->isAdmin())
                        <form action="{{ route('admin.disiplin.destroy', $kasus->id) }}" method="POST" style="margin:0; display:inline;" onsubmit="return confirm('Hapus catatan kasus siswa {{ $siswa->nama }}?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn-tool-item tool-delete" data-tooltip="Hapus" style="width:30px; height:30px;">
                            <i class="bi bi-trash3"></i>
                          </button>
                        </form>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" style="text-align:center; padding:40px; color:var(--text-3);">
                    <div style="font-size:38px; margin-bottom:8px;">🛡️</div>
                    <div style="font-weight:700; font-size:14.5px; color:var(--text);">Tidak ada catatan kasus disiplin aktif</div>
                    <p style="font-size:12px; margin-top:4px;">Semua siswa dalam kondisi tertib atau belum ada kasus yang dilaporkan.</p>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($kasusList instanceof \Illuminate\Pagination\LengthAwarePaginator && $kasusList->hasPages())
          <div style="padding:16px 20px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; background:var(--bg-2);">
            <div style="font-size:12.5px; color:var(--text-2); font-weight:600;">
              Menampilkan <strong style="color:#000000;">{{ $kasusList->firstItem() }}</strong> – <strong style="color:#000000;">{{ $kasusList->lastItem() }}</strong> dari <strong style="color:#000000;">{{ $kasusList->total() }}</strong> kasus disiplin
            </div>
            <div>
              {{ $kasusList->withQueryString()->links() }}
            </div>
          </div>
        @endif
      </div>

  </main>
</div>

{{-- MODAL TINDAK LANJUT / ESKALASI TAHAP --}}
<div class="modal-overlay" id="modalTindakLanjut">
  <div class="modal-card" style="max-width:540px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h3 style="font-size:17px; font-weight:900; color:var(--text); margin:0;">
        <i class="bi bi-pencil-square" style="color:var(--gold);"></i> Tindak Lanjut Pembinaan Siswa
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModalTindakLanjut()"><i class="bi bi-x-lg"></i></button>
    </div>

    <div style="background:var(--bg-3); border-radius:var(--r-sm); padding:10px 14px; margin-bottom:14px; font-size:12.5px;">
      Siswa: <strong style="color:var(--gold);" id="tlSiswaNama">-</strong>
    </div>

    <form id="formTindakLanjut" method="POST" action="">
      @csrf
      <div style="margin-bottom:14px;">
        <label class="form-label" style="font-weight:700; font-size:12.5px; display:block; margin-bottom:4px;">Eskalasi / Status Tahap Baru <span style="color:var(--red);">*</span></label>
        <select name="status_tahap_baru" id="tlTahapBaru" class="input-field" style="width:100%;" required>
          <option value="tahap_1_wali_kelas">Tahap 1: Pembinaan Wali Kelas</option>
          <option value="tahap_2_bk">Tahap 2: Bimbingan Konseling (Guru BK)</option>
          @if($user->isAdmin() || $user->isGuruBk() || $user->isWakaKesiswaan() || $user->isKepalaSekolah())
            <option value="tahap_3_wakasis">Tahap 3: Sidang Kesiswaan (Waka Kesiswaan)</option>
          @endif
          @if($user->isAdmin() || $user->isWakaKesiswaan() || $user->isKepalaSekolah())
            <option value="tahap_4_kepsek">Tahap 4: Keputusan Kepala Sekolah</option>
          @endif
          <option value="selesai_pembinaan">✅ Selesaikan Masa Pembinaan (Disiplin Pulih)</option>
        </select>
      </div>

      <div style="margin-bottom:14px;">
        <label class="form-label" style="font-weight:700; font-size:12.5px; display:block; margin-bottom:4px;">Catatan Hasil Pembinaan / Musyawarah <span style="color:var(--red);">*</span></label>
        <textarea name="catatan_tindakan" class="input-field" rows="3" placeholder="Tuliskan hasil musyawarah bersama ortu, arahan pembinaan, atau pertimbangan sanksi..." required style="width:100%;"></textarea>
      </div>

      <div style="margin-bottom:18px;">
        <label class="form-label" style="font-weight:700; font-size:12.5px; display:block; margin-bottom:4px;">Sanksi Khusus / Kesepakatan (Opsional)</label>
        <input type="text" name="sanksi_tambahan" class="input-field" placeholder="Misal: Penandatanganan SP 2 / Skorsing 3 hari / Tugas Kebersihan" style="width:100%;" />
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModalTindakLanjut()">Batal</button>
        <button type="submit" class="btn btn-gold">Simpan Tindak Lanjut</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL PENGATURAN BOBOT POIN & KATALOG REWARD FLEKSIBEL --}}
@if($user->isAdmin() || $user->isWakaKesiswaan())
<div class="modal-overlay" id="modalPengaturanDisiplin">
  <div class="modal-card" style="max-width:860px; padding:24px 28px; max-height:90vh; overflow-y:auto; border-radius:var(--r-xl); box-shadow:var(--shadow-lg);">
    
    {{-- MODAL HEADER --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--border); padding-bottom:14px;">
      <div>
        <div style="display:inline-flex; align-items:center; gap:6px; background:var(--gold-dim); border:1px solid var(--gold); padding:2px 10px; border-radius:20px; font-size:10.5px; font-weight:800; color:var(--gold); margin-bottom:6px;">
          <i class="bi bi-shield-lock-fill"></i> PUSAT KEBIJAKAN KESISWAAN
        </div>
        <h3 style="font-size:19px; font-weight:900; color:var(--text); margin:0; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-sliders2" style="color:var(--gold);"></i> Aturan Poin, Pelanggaran &amp; Self-Reward
        </h3>
        <p style="margin:3px 0 0; font-size:12.5px; color:var(--text-3);">
          Konfigurasi otomatis bobot presensi, ambang batas 4 jenjang pembinaan, master katalog, dan pemulihan poin.
        </p>
      </div>
      <button type="button" class="btn btn-outline" style="padding:6px 12px; border-radius:var(--r-sm);" onclick="closeModalPengaturanDisiplin()"><i class="bi bi-x-lg"></i></button>
    </div>

    {{-- TAB NAVIGATION --}}
    <div style="display:flex; gap:8px; border-bottom:1px solid var(--border); margin-bottom:20px; flex-wrap:wrap;">
      <button type="button" class="btn btn-sm btn-outline tab-btn-modal active" id="btnTabPoin" onclick="switchModalTab('tabPoin')" style="font-weight:800; font-size:12px; padding:8px 14px;">
        <i class="bi bi-diagram-3-fill"></i> 1. Bobot &amp; Ambang Eskalasi
      </button>
      <button type="button" class="btn btn-sm btn-outline tab-btn-modal" id="btnTabPelanggaran" onclick="switchModalTab('tabPelanggaran')" style="font-weight:800; font-size:12px; padding:8px 14px;">
        <i class="bi bi-exclamation-octagon-fill"></i> 2. Master Pelanggaran
      </button>
      <button type="button" class="btn btn-sm btn-outline tab-btn-modal" id="btnTabKatalog" onclick="switchModalTab('tabKatalog')" style="font-weight:800; font-size:12px; padding:8px 14px;">
        <i class="bi bi-gift-fill"></i> 3. Master Self-Reward
      </button>
      <button type="button" class="btn btn-sm btn-outline tab-btn-modal" id="btnTabSimulasi" onclick="switchModalTab('tabSimulasi')" style="font-weight:800; font-size:12px; padding:8px 14px;">
        <i class="bi bi-calculator-fill"></i> 4. Live Simulator &amp; Sync
      </button>
    </div>

    {{-- TAB 1: FORM PENGATURAN BOBOT & AMBANG BATAS ESKALASI --}}
    <div id="paneTabPoin" class="modal-tab-pane active">
      <form action="{{ route('admin.disiplin.pengaturan-poin') }}" method="POST">
        @csrf

        {{-- 4 JENJANG PEMBINAAN (ESCALATION TIER LADDER) --}}
        <div style="background:var(--bg-3); border-radius:var(--r-md); padding:16px 18px; margin-bottom:18px; border:1px solid var(--border);">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h4 style="font-size:13px; font-weight:800; color:var(--text); margin:0; text-transform:uppercase; letter-spacing:0.5px; display:flex; align-items:center; gap:6px;">
              <i class="bi bi-ladder" style="color:var(--gold);"></i> 4 Jenjang Ambang Eskalasi Pembinaan (Poin Minimal)
            </h4>
            <span style="font-size:11px; color:var(--text-3); font-weight:600;">Otomatis naik tahap saat poin tercapai</span>
          </div>

          <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(170px, 1fr)); gap:12px;">
            {{-- Tahap 1 --}}
            <div style="background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:12px;">
              <div style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text); margin-bottom:6px; display:flex; align-items:center; justify-content:space-between;">
                <span>Wali Kelas</span>
                <span class="badge" style="background:var(--bg-3); border:1px solid var(--border); color:var(--text-3); font-size:9.5px; font-weight:800;">Tahap 1</span>
              </div>
              <label class="form-label" style="font-size:11px; font-weight:700; color:var(--text-3); display:block; margin-bottom:4px;">Minimal Poin</label>
              <input type="number" name="ambang_tahap_1_wali" class="input-field" value="{{ $pengaturanDisiplin->ambang_tahap_1_wali }}" min="1" max="200" required style="width:100%; font-family:var(--font-mono); font-weight:800; font-size:15px; height:38px; color:var(--text);" />
              <div style="font-size:10.5px; color:var(--text-3); margin-top:4px;">Teguran &amp; Panggilan Ortu</div>
            </div>

            {{-- Tahap 2 --}}
            <div style="background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:12px;">
              <div style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text); margin-bottom:6px; display:flex; align-items:center; justify-content:space-between;">
                <span>Guru BK</span>
                <span class="badge" style="background:var(--bg-3); border:1px solid var(--border); color:var(--text-3); font-size:9.5px; font-weight:800;">Tahap 2</span>
              </div>
              <label class="form-label" style="font-size:11px; font-weight:700; color:var(--text-3); display:block; margin-bottom:4px;">Minimal Poin</label>
              <input type="number" name="ambang_tahap_2_bk" class="input-field" value="{{ $pengaturanDisiplin->ambang_tahap_2_bk }}" min="1" max="300" required style="width:100%; font-family:var(--font-mono); font-weight:800; font-size:15px; height:38px; color:var(--text);" />
              <div style="font-size:10.5px; color:var(--text-3); margin-top:4px;">Konseling &amp; Surat Peringatan 1</div>
            </div>

            {{-- Tahap 3 --}}
            <div style="background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:12px;">
              <div style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text); margin-bottom:6px; display:flex; align-items:center; justify-content:space-between;">
                <span>Wakasis</span>
                <span class="badge" style="background:var(--bg-3); border:1px solid var(--border); color:var(--text-3); font-size:9.5px; font-weight:800;">Tahap 3</span>
              </div>
              <label class="form-label" style="font-size:11px; font-weight:700; color:var(--text-3); display:block; margin-bottom:4px;">Minimal Poin</label>
              <input type="number" name="ambang_tahap_3_wakasis" class="input-field" value="{{ $pengaturanDisiplin->ambang_tahap_3_wakasis }}" min="1" max="400" required style="width:100%; font-family:var(--font-mono); font-weight:800; font-size:15px; height:38px; color:var(--text);" />
              <div style="font-size:10.5px; color:var(--text-3); margin-top:4px;">Sidang Kasus &amp; SP 2 / Skorsing</div>
            </div>

            {{-- Tahap 4 --}}
            <div style="background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:12px;">
              <div style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text); margin-bottom:6px; display:flex; align-items:center; justify-content:space-between;">
                <span>Kepsek</span>
                <span class="badge" style="background:var(--bg-3); border:1px solid var(--border); color:var(--text-3); font-size:9.5px; font-weight:800;">Tahap 4</span>
              </div>
              <label class="form-label" style="font-size:11px; font-weight:700; color:var(--text-3); display:block; margin-bottom:4px;">Minimal Poin</label>
              <input type="number" name="ambang_tahap_4_kepsek" class="input-field" value="{{ $pengaturanDisiplin->ambang_tahap_4_kepsek }}" min="1" max="500" required style="width:100%; font-family:var(--font-mono); font-weight:800; font-size:15px; height:38px; color:var(--text);" />
              <div style="font-size:10.5px; color:var(--text-3); margin-top:4px;">Sidang Pleno &amp; SP 3 / DO</div>
            </div>
          </div>
        </div>

        {{-- BOBOT PRESENSI OTOMATIS --}}
        <div style="background:var(--bg-3); border-radius:var(--r-md); padding:16px 18px; margin-bottom:18px; border:1px solid var(--border);">
          <h4 style="font-size:13px; font-weight:800; color:var(--text); margin:0 0 12px; text-transform:uppercase; letter-spacing:0.5px; display:flex; align-items:center; gap:6px;">
            <i class="bi bi-fingerprint" style="color:var(--gold);"></i> Bobot Pelanggaran Presensi Harian (Otomatis)
          </h4>
          <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:12px;">
            <div>
              <label class="form-label" style="font-size:11.5px; font-weight:700;">Terlambat (Poin/x)</label>
              <input type="number" name="bobot_terlambat" class="input-field" value="{{ $pengaturanDisiplin->bobot_terlambat }}" min="0" max="50" required style="width:100%; font-family:var(--font-mono); font-weight:800; height:38px;" />
            </div>
            <div>
              <label class="form-label" style="font-size:11.5px; font-weight:700;">Alpha (Poin/Hari)</label>
              <input type="number" name="bobot_alpha" class="input-field" value="{{ $pengaturanDisiplin->bobot_alpha }}" min="0" max="100" required style="width:100%; font-family:var(--font-mono); font-weight:800; height:38px;" />
            </div>
            <div>
              <label class="form-label" style="font-size:11.5px; font-weight:700;">Bolos (Poin/x)</label>
              <input type="number" name="bobot_bolos" class="input-field" value="{{ $pengaturanDisiplin->bobot_bolos }}" min="0" max="100" required style="width:100%; font-family:var(--font-mono); font-weight:800; height:38px;" />
            </div>
            <div>
              <label class="form-label" style="font-size:11.5px; font-weight:700;" title="Batas toleransi keterlambatan di pos piket tanpa dicatat ke buku kasus">Toleransi Pos Piket (x)</label>
              <input type="number" name="toleransi_terlambat_piket" class="input-field" value="{{ $pengaturanDisiplin->toleransi_terlambat_piket }}" min="0" max="20" required style="width:100%; font-family:var(--font-mono); font-weight:800; height:38px;" />
            </div>
          </div>
          <div style="font-size:11px; color:var(--text-3); margin-top:8px;">
            * Siswa yang telat di bawah batas toleransi pos piket diselesaikan di gerbang tanpa dimasukkan ke berkas kasus.
          </div>
        </div>

        {{-- SELF-REWARD OTOMATIS --}}
        <div style="background:var(--bg-3); border-radius:var(--r-md); padding:16px 18px; margin-bottom:20px; border:1px solid var(--border);">
          <h4 style="font-size:13px; font-weight:800; color:var(--text); margin:0 0 12px; text-transform:uppercase; letter-spacing:0.5px; display:flex; align-items:center; gap:6px;">
            <i class="bi bi-stars" style="color:var(--gold);"></i> Self-Reward Kehadiran Konsisten (Streak Pemulihan)
          </h4>
          <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:12px;">
            <div>
              <label class="form-label" style="font-size:11.5px; font-weight:700;">Streak Tepat Waktu (Hari Berturut-turut)</label>
              <input type="number" name="reward_streak_hari" class="input-field" value="{{ $pengaturanDisiplin->reward_streak_hari }}" min="1" max="60" required style="width:100%; font-family:var(--font-mono); font-weight:800; height:38px;" />
            </div>
            <div>
              <label class="form-label" style="font-size:11.5px; font-weight:700;">Deduksi Poin Pemulihan Kasus</label>
              <input type="number" name="reward_streak_poin" class="input-field" value="{{ $pengaturanDisiplin->reward_streak_poin }}" min="1" max="50" required style="width:100%; font-family:var(--font-mono); font-weight:800; height:38px;" />
            </div>
          </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; border-top:1px solid var(--border); padding-top:16px;">
          <label style="display:flex; align-items:center; gap:8px; font-size:12.5px; color:var(--text); cursor:pointer;">
            <input type="checkbox" name="recalculate_now" value="1" checked style="width:16px; height:16px; cursor:pointer;" />
            <strong>Hitung ulang seluruh poin dan jenjang kasus siswa sekarang</strong>
          </label>
          <div style="display:flex; gap:8px;">
            <button type="button" class="btn btn-outline" onclick="closeModalPengaturanDisiplin()">Batal</button>
            <button type="submit" class="btn btn-gold" style="font-weight:800; padding:0 20px; height:38px;">
              <i class="bi bi-check-lg"></i> Simpan Kebijakan
            </button>
          </div>
        </div>
      </form>
    </div>

    {{-- TAB 2: MASTER KATALOG PELANGGARAN --}}
    <div id="paneTabPelanggaran" class="modal-tab-pane" style="display:none;">
      <div id="alertKatalogPelanggaran" style="display:none; margin-bottom:12px; padding:10px 14px; border-radius:8px; font-size:12px; font-weight:700; background:rgba(34,197,94,0.15); color:#16A34A; border:1px solid rgba(34,197,94,0.3);"></div>

      <div style="margin-bottom:12px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <span style="font-size:13px; color:var(--text); font-weight:800;">Daftar Master Jenis Pelanggaran Terdaftar:</span>
      </div>

      {{-- List of Violations --}}
      <div id="listKatalogPelanggaran" style="display:flex; flex-direction:column; gap:8px; max-height:260px; overflow-y:auto; margin-bottom:18px; padding-right:4px;">
        @forelse($katalogPelanggarans as $kp)
          <div id="itemPelanggaran-{{ $kp->id }}" style="background:var(--bg-3); border:1px solid var(--border); border-radius:var(--r-sm); padding:10px 14px; display:flex; justify-content:space-between; align-items:center; transition:all 0.2s ease;">
            <div>
              <div style="display:flex; align-items:center; gap:8px;">
                <strong style="color:var(--text); font-size:13px;">{{ $kp->nama_pelanggaran }}</strong>
                <span class="badge" style="background:rgba(239,68,68,0.15); color:#DC2626; font-weight:800; font-size:11px;">
                  +{{ $kp->poin_pelanggaran }} Poin
                </span>
                <span class="badge" style="background:var(--bg-2); color:var(--text-3); font-size:10px; text-transform:uppercase;">
                  {{ $kp->kategori }}
                </span>
              </div>
              <div style="font-size:11px; color:var(--text-3); margin-top:2px;">{{ $kp->deskripsi ?: 'Tidak ada deskripsi' }}</div>
            </div>
            <button type="button" onclick="handleAjaxDeletePelanggaran('{{ route('admin.disiplin.katalog-pelanggaran.destroy', $kp->id) }}', {{ $kp->id }}, '{{ addslashes($kp->nama_pelanggaran) }}')" class="btn btn-sm btn-outline" style="color:var(--red); border-color:rgba(239,68,68,0.3); padding:4px 8px; font-size:11px;" title="Hapus Pelanggaran">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        @empty
          <div id="emptyKatalogPelanggaran" style="text-align:center; padding:24px; color:var(--text-3); font-size:12px;">
            Belum ada katalog pelanggaran kustom.
          </div>
        @endforelse
      </div>

      {{-- Form Tambah Pelanggaran Baru --}}
      <div style="background:var(--bg-2); border:1px dashed var(--border); border-radius:var(--r-md); padding:16px;">
        <h4 style="font-size:13px; font-weight:800; color:#DC2626; margin:0 0 12px; display:flex; align-items:center; gap:6px;">
          <i class="bi bi-plus-circle-fill"></i> Tambah Master Jenis Pelanggaran Baru
        </h4>
        <form id="formAddKatalogPelanggaran" onsubmit="handleAjaxAddPelanggaran(event)">
          @csrf
          <div style="display:grid; grid-template-columns:2fr 1fr 1fr; gap:10px; margin-bottom:10px;">
            <div>
              <label class="form-label" style="font-size:11px; font-weight:700;">Nama Pelanggaran <span style="color:var(--red);">*</span></label>
              <input type="text" id="addPelNama" name="nama_pelanggaran" class="input-field" placeholder="Misal: Merokok di Lingkungan Sekolah" required style="width:100%; font-size:12px; height:36px;" />
            </div>
            <div>
              <label class="form-label" style="font-size:11px; font-weight:700;">Kategori</label>
              <select id="addPelKategori" name="kategori" class="input-field" style="width:100%; font-size:12px; height:36px;">
                <option value="presensi">Presensi / Kehadiran</option>
                <option value="tata_tertib">Tata Tertib / Seragam</option>
                <option value="sikap">Sikap / Perilaku</option>
                <option value="berat">Pelanggaran Berat</option>
                <option value="custom">Lainnya</option>
              </select>
            </div>
            <div>
              <label class="form-label" style="font-size:11px; font-weight:700;">Bobot Poin <span style="color:var(--red);">*</span></label>
              <input type="number" id="addPelPoin" name="poin_pelanggaran" class="input-field" value="10" min="1" max="200" required style="width:100%; font-family:var(--font-mono); font-weight:800; font-size:12px; height:36px;" />
            </div>
          </div>
          <div style="margin-bottom:12px;">
            <input type="text" id="addPelDesc" name="deskripsi" class="input-field" placeholder="Keterangan singkat / ketentuan sanksi..." style="width:100%; font-size:12px; height:36px;" />
          </div>
          <div style="display:flex; justify-content:flex-end;">
            <button type="submit" id="btnSubmitAddPel" class="btn btn-sm btn-gold" style="font-weight:800; background:#DC2626; border-color:#DC2626; height:34px; padding:0 16px;">
              <i class="bi bi-plus-lg"></i> Simpan ke Katalog Master
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- TAB 3: KATALOG MASTER SELF-REWARD --}}
    <div id="paneTabKatalog" class="modal-tab-pane" style="display:none;">
      <div id="alertKatalogReward" style="display:none; margin-bottom:12px; padding:10px 14px; border-radius:8px; font-size:12px; font-weight:700; background:rgba(34,197,94,0.15); color:#16A34A; border:1px solid rgba(34,197,94,0.3);"></div>

      <div style="margin-bottom:12px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <span style="font-size:13px; color:var(--text); font-weight:800;">Daftar Master Aksi Reward &amp; Pemulihan Poin:</span>
      </div>

      {{-- List of Rewards --}}
      <div id="listKatalogReward" style="display:flex; flex-direction:column; gap:8px; max-height:260px; overflow-y:auto; margin-bottom:18px; padding-right:4px;">
        @forelse($katalogRewards as $kr)
          <div id="itemReward-{{ $kr->id }}" style="background:var(--bg-3); border:1px solid var(--border); border-radius:var(--r-sm); padding:10px 14px; display:flex; justify-content:space-between; align-items:center; transition:all 0.2s ease;">
            <div>
              <div style="display:flex; align-items:center; gap:8px;">
                <strong style="color:var(--text); font-size:13px;">{{ $kr->nama_reward }}</strong>
                <span class="badge" style="background:rgba(34,197,94,0.15); color:#16A34A; font-weight:800; font-size:11px;">
                  -{{ $kr->poin_deduksi }} Poin
                </span>
                <span class="badge" style="background:var(--bg-2); color:var(--text-3); font-size:10px; text-transform:uppercase;">
                  {{ $kr->kategori }}
                </span>
              </div>
              <div style="font-size:11px; color:var(--text-3); margin-top:2px;">{{ $kr->deskripsi ?: 'Tidak ada deskripsi' }}</div>
            </div>
            <button type="button" onclick="handleAjaxDeleteReward('{{ route('admin.disiplin.katalog-reward.destroy', $kr->id) }}', {{ $kr->id }}, '{{ addslashes($kr->nama_reward) }}')" class="btn btn-sm btn-outline" style="color:var(--red); border-color:rgba(239,68,68,0.3); padding:4px 8px; font-size:11px;" title="Hapus Reward">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        @empty
          <div id="emptyKatalogReward" style="text-align:center; padding:24px; color:var(--text-3); font-size:12px;">
            Belum ada katalog reward kustom.
          </div>
        @endforelse
      </div>

      {{-- Form Tambah Reward Baru --}}
      <div style="background:var(--bg-2); border:1px dashed var(--border); border-radius:var(--r-md); padding:16px;">
        <h4 style="font-size:13px; font-weight:800; color:var(--gold); margin:0 0 12px; display:flex; align-items:center; gap:6px;">
          <i class="bi bi-plus-circle-fill"></i> Tambah Master Tindakan Reward Baru
        </h4>
        <form id="formAddKatalogReward" onsubmit="handleAjaxAddReward(event)">
          @csrf
          <div style="display:grid; grid-template-columns:2fr 1fr 1fr; gap:10px; margin-bottom:10px;">
            <div>
              <label class="form-label" style="font-size:11px; font-weight:700;">Nama Aksi / Prestasi <span style="color:var(--red);">*</span></label>
              <input type="text" id="addRewNama" name="nama_reward" class="input-field" placeholder="Misal: Juara LKS / Hafalan Surat Pendek" required style="width:100%; font-size:12px; height:36px;" />
            </div>
            <div>
              <label class="form-label" style="font-size:11px; font-weight:700;">Kategori</label>
              <select id="addRewKategori" name="kategori" class="input-field" style="width:100%; font-size:12px; height:36px;">
                <option value="karakter">Karakter / Ibadah</option>
                <option value="kebersihan">Kebersihan / Bakti Sosial</option>
                <option value="prestasi">Prestasi / Kejuaraan</option>
                <option value="konseling">Konseling BK</option>
                <option value="kehadiran">Kehadiran</option>
                <option value="custom">Lainnya</option>
              </select>
            </div>
            <div>
              <label class="form-label" style="font-size:11px; font-weight:700;">Poin Deduksi <span style="color:var(--red);">*</span></label>
              <input type="number" id="addRewPoin" name="poin_deduksi" class="input-field" value="5" min="1" max="100" required style="width:100%; font-family:var(--font-mono); font-weight:800; font-size:12px; height:36px;" />
            </div>
          </div>
          <div style="margin-bottom:12px;">
            <input type="text" id="addRewDesc" name="deskripsi" class="input-field" placeholder="Keterangan / kriteria pencapaian reward..." style="width:100%; font-size:12px; height:36px;" />
          </div>
          <div style="display:flex; justify-content:flex-end;">
            <button type="submit" id="btnSubmitAddRew" class="btn btn-sm btn-gold" style="font-weight:800; height:34px; padding:0 16px;">
              <i class="bi bi-plus-lg"></i> Simpan ke Master Reward
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- TAB 4: SIMULATOR POIN & BATCH SYNC --}}
    <div id="paneTabSimulasi" class="modal-tab-pane" style="display:none;">
      <div style="background:var(--bg-3); border-radius:var(--r-md); padding:18px; margin-bottom:16px; border:1px solid var(--border);">
        <h4 style="font-size:13px; font-weight:800; color:var(--gold); margin:0 0 12px; text-transform:uppercase; letter-spacing:0.5px;">
          <i class="bi bi-calculator-fill"></i> Simulator Perhitungan Poin Interaktif
        </h4>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(130px, 1fr)); gap:10px; margin-bottom:14px;">
          <div>
            <label style="font-size:11px; font-weight:700;">Simulasi Telat (x)</label>
            <input type="number" id="simTelat" class="input-field" value="3" min="0" style="width:100%; font-family:var(--font-mono); height:36px;" oninput="runDisiplinSimulation()" />
          </div>
          <div>
            <label style="font-size:11px; font-weight:700;">Simulasi Alpha (Hari)</label>
            <input type="number" id="simAlpha" class="input-field" value="1" min="0" style="width:100%; font-family:var(--font-mono); height:36px;" oninput="runDisiplinSimulation()" />
          </div>
          <div>
            <label style="font-size:11px; font-weight:700;">Simulasi Bolos (x)</label>
            <input type="number" id="simBolos" class="input-field" value="0" min="0" style="width:100%; font-family:var(--font-mono); height:36px;" oninput="runDisiplinSimulation()" />
          </div>
          <div>
            <label style="font-size:11px; font-weight:700;">Reward Diraih (Poin)</label>
            <input type="number" id="simReward" class="input-field" value="0" min="0" style="width:100%; font-family:var(--font-mono); height:36px;" oninput="runDisiplinSimulation()" />
          </div>
        </div>

        <div style="background:var(--bg-2); border-radius:var(--r-sm); padding:14px 18px; border:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
          <div>
            <div style="font-size:11px; color:var(--text-3);">Hasil Perhitungan Bersih:</div>
            <div id="simHasilPoin" style="font-size:22px; font-weight:900; font-family:var(--font-mono); color:var(--gold);">13 Poin</div>
          </div>
          <div>
            <div style="font-size:11px; color:var(--text-3); text-align:right;">Rekomendasi Jenjang Pembinaan:</div>
            <div id="simHasilTahap" style="font-size:13.5px; font-weight:800; color:#CA8A04;">Tahap 1: Wali Kelas</div>
          </div>
        </div>
      </div>

      <div style="background:var(--bg-3); border-radius:var(--r-md); padding:18px; border:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div style="flex:1; min-width:200px;">
          <strong style="color:var(--text); font-size:13.5px;">Hitung Ulang Seluruh Kasus Siswa (Batch Recalculate)</strong>
          <p style="margin:2px 0 0; font-size:12px; color:var(--text-3);">
            Perbarui seluruh data akumulasi pelanggaran dan jenjang pembinaan seluruh siswa berdasarkan skema kebijakan terbaru.
          </p>
        </div>
        <form action="{{ route('admin.disiplin.recalculate') }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-outline" style="border-color:var(--gold); color:var(--gold); font-weight:800; font-size:12.5px; height:38px; padding:0 16px;" onclick="return confirm('Hitung ulang seluruh poin dan tahap kedisiplinan siswa sekarang?')">
            <i class="bi bi-arrow-repeat"></i> Hitung Ulang Semua Data
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif

<script>
  function openModalPengaturanDisiplin() {
    const el = document.getElementById('modalPengaturanDisiplin');
    if (el) el.classList.add('active');
  }
  function closeModalPengaturanDisiplin() {
    const el = document.getElementById('modalPengaturanDisiplin');
    if (el) el.classList.remove('active');
  }

  function switchModalTab(tabId) {
    document.querySelectorAll('.tab-btn-modal').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.modal-tab-pane').forEach(p => p.style.display = 'none');

    if (tabId === 'tabPoin') {
      document.getElementById('btnTabPoin').classList.add('active');
      document.getElementById('paneTabPoin').style.display = 'block';
    } else if (tabId === 'tabPelanggaran') {
      document.getElementById('btnTabPelanggaran').classList.add('active');
      document.getElementById('paneTabPelanggaran').style.display = 'block';
    } else if (tabId === 'tabKatalog') {
      document.getElementById('btnTabKatalog').classList.add('active');
      document.getElementById('paneTabKatalog').style.display = 'block';
    } else if (tabId === 'tabSimulasi') {
      document.getElementById('btnTabSimulasi').classList.add('active');
      document.getElementById('paneTabSimulasi').style.display = 'block';
      runDisiplinSimulation();
    }
  }

  function showModalAlert(elementId, message, isError = false) {
    const el = document.getElementById(elementId);
    if (!el) return;
    el.innerText = message;
    el.style.display = 'block';
    el.style.background = isError ? 'rgba(239,68,68,0.15)' : 'rgba(34,197,94,0.15)';
    el.style.color = isError ? '#DC2626' : '#16A34A';
    el.style.borderColor = isError ? 'rgba(239,68,68,0.3)' : 'rgba(34,197,94,0.3)';
    setTimeout(() => {
      el.style.display = 'none';
    }, 4000);
  }

  // AJAX Add Pelanggaran
  async function handleAjaxAddPelanggaran(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitAddPel');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

    const nama = document.getElementById('addPelNama').value;
    const kategori = document.getElementById('addPelKategori').value;
    const poin = document.getElementById('addPelPoin').value;
    const desc = document.getElementById('addPelDesc').value;

    try {
      const res = await fetch("{{ route('admin.disiplin.katalog-pelanggaran.store') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          nama_pelanggaran: nama,
          kategori: kategori,
          poin_pelanggaran: poin,
          deskripsi: desc
        })
      });

      const data = await res.json();
      if (res.ok && data.success) {
        showModalAlert('alertKatalogPelanggaran', data.message || 'Jenis pelanggaran baru berhasil ditambahkan!');
        
        // Append item to list without closing modal
        const list = document.getElementById('listKatalogPelanggaran');
        const emptyEl = document.getElementById('emptyKatalogPelanggaran');
        if (emptyEl) emptyEl.remove();

        const newItem = document.createElement('div');
        newItem.id = 'itemPelanggaran-' + data.item.id;
        newItem.style = 'background:var(--bg-3); border:1px solid var(--border); border-radius:8px; padding:10px 14px; display:flex; justify-content:space-between; align-items:center; animation:fadeIn 0.3s ease;';
        newItem.innerHTML = `
          <div>
            <div style="display:flex; align-items:center; gap:8px;">
              <strong style="color:var(--text); font-size:13px;">${data.item.nama_pelanggaran}</strong>
              <span class="badge" style="background:rgba(239,68,68,0.15); color:#DC2626; font-weight:800; font-size:11px;">
                +${data.item.poin_pelanggaran} Poin
              </span>
              <span class="badge" style="background:var(--bg-2); color:var(--text-3); font-size:10px; text-transform:uppercase;">
                ${data.item.kategori}
              </span>
            </div>
            <div style="font-size:11px; color:var(--text-3); margin-top:2px;">${data.item.deskripsi || 'Tidak ada deskripsi'}</div>
          </div>
          <button type="button" onclick="handleAjaxDeletePelanggaran('/disiplin/katalog-pelanggaran/${data.item.id}', ${data.item.id}, '${data.item.nama_pelanggaran.replace(/'/g, "\\'")}')" class="btn btn-sm btn-outline" style="color:var(--red); border-color:rgba(239,68,68,0.3); padding:3px 8px; font-size:11px;" title="Hapus Pelanggaran">
            <i class="bi bi-trash"></i>
          </button>
        `;
        list.prepend(newItem);

        // Reset form inputs
        document.getElementById('addPelNama').value = '';
        document.getElementById('addPelDesc').value = '';
        document.getElementById('addPelPoin').value = '10';
      } else {
        showModalAlert('alertKatalogPelanggaran', data.message || 'Gagal menambahkan pelanggaran.', true);
      }
    } catch (err) {
      showModalAlert('alertKatalogPelanggaran', 'Terjadi kesalahan jaringan.', true);
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-plus-lg"></i> Simpan ke Master Pelanggaran';
    }
  }

  // AJAX Delete Pelanggaran
  async function handleAjaxDeletePelanggaran(url, id, name) {
    if (!confirm(`Hapus jenis pelanggaran "${name}" dari katalog master?`)) return;

    const itemEl = document.getElementById('itemPelanggaran-' + id);
    if (itemEl) itemEl.style.opacity = '0.5';

    try {
      const res = await fetch(url, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      });
      const data = await res.json();
      if (res.ok && data.success) {
        showModalAlert('alertKatalogPelanggaran', data.message || 'Item berhasil dihapus.');
        if (itemEl) {
          itemEl.style.transform = 'scale(0.95)';
          itemEl.style.opacity = '0';
          setTimeout(() => itemEl.remove(), 200);
        }
      } else {
        if (itemEl) itemEl.style.opacity = '1';
        showModalAlert('alertKatalogPelanggaran', data.message || 'Gagal menghapus item.', true);
      }
    } catch (err) {
      if (itemEl) itemEl.style.opacity = '1';
      showModalAlert('alertKatalogPelanggaran', 'Terjadi kesalahan saat menghapus.', true);
    }
  }

  // AJAX Add Reward
  async function handleAjaxAddReward(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitAddRew');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

    const nama = document.getElementById('addRewNama').value;
    const kategori = document.getElementById('addRewKategori').value;
    const poin = document.getElementById('addRewPoin').value;
    const desc = document.getElementById('addRewDesc').value;

    try {
      const res = await fetch("{{ route('admin.disiplin.katalog-reward.store') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          nama_reward: nama,
          kategori: kategori,
          poin_deduksi: poin,
          deskripsi: desc
        })
      });

      const data = await res.json();
      if (res.ok && data.success) {
        showModalAlert('alertKatalogReward', data.message || 'Jenis reward baru berhasil ditambahkan!');
        
        // Append item to list without closing modal
        const list = document.getElementById('listKatalogReward');
        const emptyEl = document.getElementById('emptyKatalogReward');
        if (emptyEl) emptyEl.remove();

        const newItem = document.createElement('div');
        newItem.id = 'itemReward-' + data.item.id;
        newItem.style = 'background:var(--bg-3); border:1px solid var(--border); border-radius:8px; padding:10px 14px; display:flex; justify-content:space-between; align-items:center; animation:fadeIn 0.3s ease;';
        newItem.innerHTML = `
          <div>
            <div style="display:flex; align-items:center; gap:8px;">
              <strong style="color:var(--text); font-size:13px;">${data.item.nama_reward}</strong>
              <span class="badge" style="background:rgba(34,197,94,0.15); color:#16A34A; font-weight:800; font-size:11px;">
                -${data.item.poin_deduksi} Poin
              </span>
              <span class="badge" style="background:var(--bg-2); color:var(--text-3); font-size:10px; text-transform:uppercase;">
                ${data.item.kategori}
              </span>
            </div>
            <div style="font-size:11px; color:var(--text-3); margin-top:2px;">${data.item.deskripsi || 'Tidak ada deskripsi'}</div>
          </div>
          <button type="button" onclick="handleAjaxDeleteReward('/disiplin/katalog-reward/${data.item.id}', ${data.item.id}, '${data.item.nama_reward.replace(/'/g, "\\'")}')" class="btn btn-sm btn-outline" style="color:var(--red); border-color:rgba(239,68,68,0.3); padding:3px 8px; font-size:11px;" title="Hapus Reward">
            <i class="bi bi-trash"></i>
          </button>
        `;
        list.prepend(newItem);

        // Reset form inputs
        document.getElementById('addRewNama').value = '';
        document.getElementById('addRewDesc').value = '';
        document.getElementById('addRewPoin').value = '5';
      } else {
        showModalAlert('alertKatalogReward', data.message || 'Gagal menambahkan reward.', true);
      }
    } catch (err) {
      showModalAlert('alertKatalogReward', 'Terjadi kesalahan jaringan.', true);
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-plus-lg"></i> Simpan ke Katalog Master';
    }
  }

  // AJAX Delete Reward
  async function handleAjaxDeleteReward(url, id, name) {
    if (!confirm(`Hapus jenis reward "${name}" dari katalog master?`)) return;

    const itemEl = document.getElementById('itemReward-' + id);
    if (itemEl) itemEl.style.opacity = '0.5';

    try {
      const res = await fetch(url, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      });
      const data = await res.json();
      if (res.ok && data.success) {
        showModalAlert('alertKatalogReward', data.message || 'Item berhasil dihapus.');
        if (itemEl) {
          itemEl.style.transform = 'scale(0.95)';
          itemEl.style.opacity = '0';
          setTimeout(() => itemEl.remove(), 200);
        }
      } else {
        if (itemEl) itemEl.style.opacity = '1';
        showModalAlert('alertKatalogReward', data.message || 'Gagal menghapus item.', true);
      }
    } catch (err) {
      if (itemEl) itemEl.style.opacity = '1';
      showModalAlert('alertKatalogReward', 'Terjadi kesalahan saat menghapus.', true);
    }
  }

  function runDisiplinSimulation() {
    const bTerlambat = {{ $pengaturanDisiplin->bobot_terlambat ?? 3 }};
    const bAlpha = {{ $pengaturanDisiplin->bobot_alpha ?? 10 }};
    const bBolos = {{ $pengaturanDisiplin->bobot_bolos ?? 15 }};
    const tolPiket = {{ $pengaturanDisiplin->toleransi_terlambat_piket ?? 2 }};
    const a1 = {{ $pengaturanDisiplin->ambang_tahap_1_wali ?? 10 }};
    const a2 = {{ $pengaturanDisiplin->ambang_tahap_2_bk ?? 30 }};
    const a3 = {{ $pengaturanDisiplin->ambang_tahap_3_wakasis ?? 50 }};
    const a4 = {{ $pengaturanDisiplin->ambang_tahap_4_kepsek ?? 75 }};

    const sTelat = parseInt(document.getElementById('simTelat')?.value || 0);
    const sAlpha = parseInt(document.getElementById('simAlpha')?.value || 0);
    const sBolos = parseInt(document.getElementById('simBolos')?.value || 0);
    const sReward = parseInt(document.getElementById('simReward')?.value || 0);

    const hitungTelat = Math.max(0, sTelat - tolPiket);
    const poinKotor = (hitungTelat * bTerlambat) + (sAlpha * bAlpha) + (sBolos * bBolos);
    const poinBersih = Math.max(0, poinKotor - sReward);

    const elHasil = document.getElementById('simHasilPoin');
    const elTahap = document.getElementById('simHasilTahap');

    if (elHasil) elHasil.innerText = poinBersih + ' Poin (Kotor: ' + poinKotor + ')';

    if (elTahap) {
      if (poinBersih >= a4) {
        elTahap.innerText = 'Tahap 4: Kepala Sekolah (SP 3 / Pleno)';
        elTahap.style.color = '#DC2626';
      } else if (poinBersih >= a3) {
        elTahap.innerText = 'Tahap 3: Waka Kesiswaan (SP 2 / Sidang)';
        elTahap.style.color = '#EA580C';
      } else if (poinBersih >= a2) {
        elTahap.innerText = 'Tahap 2: Guru BK (SP 1 / Panggilan Ortu)';
        elTahap.style.color = '#2563EB';
      } else if (poinBersih >= a1) {
        elTahap.innerText = 'Tahap 1: Wali Kelas (Pembinaan Internal)';
        elTahap.style.color = '#CA8A04';
      } else {
        elTahap.innerText = 'Selesai Pembinaan / Tertib (Bebas Masalah)';
        elTahap.style.color = '#16A34A';
      }
    }
  }

  function openModalTindakLanjut(kasusId, siswaNama, currentTahap) {
    document.getElementById('tlSiswaNama').innerText = siswaNama;
    document.getElementById('tlTahapBaru').value = currentTahap;
    document.getElementById('formTindakLanjut').action = '/disiplin/' + kasusId + '/tindak-lanjut';
    document.getElementById('modalTindakLanjut').classList.add('active');
  }
  function closeModalTindakLanjut() {
    document.getElementById('modalTindakLanjut').classList.remove('active');
  }

  document.addEventListener('DOMContentLoaded', () => {
    @if(session('open_tab'))
      openModalPengaturanDisiplin();
      switchModalTab("{{ session('open_tab') }}");
    @endif
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeModalPengaturanDisiplin();
      closeModalTindakLanjut();
    }
  });
</script>

</body>
</html>
