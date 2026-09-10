<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Meja Wawancara Kejuruan — Modul PPDB 2026 SMKN 1 Air Naningan</title>
  
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/admin-ppdb.css') }}?v={{ file_exists(public_path('css/admin-ppdb.css')) ? filemtime(public_path('css/admin-ppdb.css')) : time() }}">

  <style>
    .wawancara-hero-bar {
      background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
      border-radius: 16px;
      padding: 24px;
      color: #ffffff;
      margin-bottom: 22px;
      box-shadow: 0 10px 25px rgba(30, 27, 75, 0.2);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
      border: 1px solid rgba(255, 255, 255, 0.12);
    }
    .wawancara-stat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 14px;
      margin-bottom: 22px;
    }
    .wawancara-stat-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 16px 20px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.03);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .btn-tab-w {
      padding: 8px 16px;
      font-size: 12.5px;
      font-weight: 800;
      border-radius: 8px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.15s;
    }
    .btn-tab-w.active {
      background: #2563eb;
      color: #ffffff !important;
      box-shadow: 0 2px 6px rgba(37,99,235,0.25);
    }
    .btn-tab-w:not(.active) {
      background: #f8fafc;
      color: #475569;
      border: 1px solid #cbd5e1;
    }
    .btn-tab-w:not(.active):hover {
      background: #f1f5f9;
      color: #0f172a;
    }
  </style>
</head>
<body>

<div class="app-container">
  {{-- 1. SIDEBAR RESMI MODUL PPDB --}}
  @include('partials.sidebar_ppdb')

  {{-- 2. KONTEN UTAMA MODUL PPDB --}}
  <main class="main-content">
    
    {{-- HERO HEADER MODUL PPDB --}}
    <div class="wawancara-hero-bar no-print">
      <div style="display:flex; align-items:center; gap:16px;">
        <div style="width:52px; height:52px; border-radius:14px; background:rgba(217,119,6,0.2); border:1.5px solid rgba(245,158,11,0.5); display:flex; align-items:center; justify-content:center;">
          <i class="bi bi-mic-fill" style="color:#fbbf24; font-size:24px;"></i>
        </div>
        <div>
          <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,0.12); padding:3px 10px; border-radius:20px; font-size:11px; font-weight:800; letter-spacing:0.04em; color:#fde68a; margin-bottom:4px;">
            <span style="width:6px; height:6px; border-radius:50%; background:#34d399;"></span>
            MODUL PPDB 2026 &bull; MEJA PENGUJI RESMI
          </div>
          <h1 style="margin:0 0 3px; font-size:20px; font-weight:900; letter-spacing:-0.02em; color:#ffffff;">
            Meja Penilaian Wawancara, Fisik &amp; Minat Kejuruan
          </h1>
          <div style="font-size:12.5px; color:#c7d2fe;">
            Penguji: <strong>{{ $user->name }}</strong>
            @if($user->nip || $user->guru?->nip)
              (NIP. {{ $user->nip ?: $user->guru?->nip }})
            @endif
          </div>
        </div>
      </div>

      <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('admin.ppdb.seleksi.tes_buta_warna') }}" target="_blank" class="btn btn-sm" style="background:rgba(255,255,255,0.12); color:#ffffff; font-weight:700; border-radius:8px; font-size:12px; padding:8px 14px; border:1px solid rgba(255,255,255,0.2); text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
          <i class="bi bi-eye text-warning"></i> Piringan Ishihara
        </a>
        <a href="{{ route('admin.ppdb.seleksi.cetak_wawancara') }}" target="_blank" class="btn btn-sm" style="background:#f59e0b; color:#000000; font-weight:800; border-radius:8px; font-size:12px; padding:8px 16px; border:none; text-decoration:none; display:inline-flex; align-items:center; gap:6px; box-shadow:0 4px 10px rgba(245,158,11,0.3);">
          <i class="bi bi-printer"></i> Format Rubrik A4
        </a>
      </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
      <div style="background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:12px 18px; margin-bottom:18px; border-radius:10px; font-size:13px; font-weight:700; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-check-circle-fill" style="font-size:16px;"></i> {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div style="background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:12px 18px; margin-bottom:18px; border-radius:10px; font-size:13px; font-weight:700; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-exclamation-triangle-fill" style="font-size:16px;"></i> {{ session('error') }}
      </div>
    @endif

    {{-- STATISTIK BEBAN PENGUJIAN --}}
    <div class="wawancara-stat-grid">
      <div class="wawancara-stat-card">
        <div>
          <div style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.05em;">Ditugaskan Kepada Saya</div>
          <div style="font-size:24px; font-weight:900; color:var(--text-1); margin-top:2px;">{{ $totalDitugaskan }} <span style="font-size:14px; font-weight:600; color:var(--text-3);">Siswa</span></div>
          <div style="font-size:11px; color:#2563eb; margin-top:4px;">Hasil plotting resmi panitia PPDB</div>
        </div>
        <div style="width:44px; height:44px; border-radius:12px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:20px;">
          <i class="bi bi-person-check-fill"></i>
        </div>
      </div>

      <div class="wawancara-stat-card">
        <div>
          <div style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.05em;">Sudah Selesai Diuji</div>
          <div style="font-size:24px; font-weight:900; color:#059669; margin-top:2px;">{{ $sudahDiuji }} <span style="font-size:14px; font-weight:600; color:var(--text-3);">Siswa</span></div>
          <div style="font-size:11px; color:#059669; margin-top:4px;">
            @if($totalDitugaskan > 0)
              {{ round(($sudahDiuji / $totalDitugaskan) * 100) }}% target pengujian tercapai
            @else
              0% selesai
            @endif
          </div>
        </div>
        <div style="width:44px; height:44px; border-radius:12px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; font-size:20px;">
          <i class="bi bi-check-circle-fill"></i>
        </div>
      </div>

      <div class="wawancara-stat-card">
        <div>
          <div style="font-size:11px; font-weight:800; text-transform:uppercase; color:var(--text-3); letter-spacing:0.05em;">Menunggu Wawancara</div>
          <div style="font-size:24px; font-weight:900; color:#d97706; margin-top:2px;">{{ $belumDiuji }} <span style="font-size:14px; font-weight:600; color:var(--text-3);">Siswa</span></div>
          <div style="font-size:11px; color:#d97706; margin-top:4px;">Calon siswa dalam antrean pengujian</div>
        </div>
        <div style="width:44px; height:44px; border-radius:12px; background:#fffbeb; color:#d97706; display:flex; align-items:center; justify-content:center; font-size:20px;">
          <i class="bi bi-hourglass-split"></i>
        </div>
      </div>
    </div>

    {{-- TOOLBAR & FILTER --}}
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:14px 18px; margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
      {{-- Tab Selector --}}
      <div style="display:flex; align-items:center; gap:8px;">
        <a href="{{ route('admin.ppdb.wawancara', ['tab' => 'saya', 'jurusan_id' => $jurusanId, 'cari' => $cari]) }}" class="btn-tab-w {{ $tab === 'saya' ? 'active' : '' }}">
          <i class="bi bi-person-badge"></i> Ditugaskan ke Saya ({{ $totalDitugaskan }})
        </a>
        <a href="{{ route('admin.ppdb.wawancara', ['tab' => 'semua', 'jurusan_id' => $jurusanId, 'cari' => $cari]) }}" class="btn-tab-w {{ $tab === 'semua' ? 'active' : '' }}">
          <i class="bi bi-people-fill"></i> Semua Calon Siswa
        </a>
      </div>

      {{-- Search & Major Filter --}}
      <form method="GET" action="{{ route('admin.ppdb.wawancara') }}" style="display:flex; align-items:center; gap:8px; margin:0; flex-wrap:wrap;">
        <input type="hidden" name="tab" value="{{ $tab }}">

        <select name="jurusan_id" class="form-control" style="font-size:12.5px; font-weight:700; width:auto; min-width:160px;" onchange="this.form.submit()">
          <option value="">-- Semua Jurusan --</option>
          @foreach($jurusans as $j)
            <option value="{{ $j->id }}" {{ $jurusanId == $j->id ? 'selected' : '' }}>
              {{ $j->kode_jurusan }} - {{ $j->nama_jurusan }}
            </option>
          @endforeach
        </select>

        <div style="position:relative;">
          <input type="text" name="cari" value="{{ $cari }}" class="form-control" placeholder="Cari nama / no. tes..." style="font-size:12.5px; padding-right:30px; width:180px;">
          <button type="submit" style="position:absolute; right:6px; top:50%; transform:translateY(-50%); border:none; background:transparent; color:#64748b; cursor:pointer;">
            <i class="bi bi-search"></i>
          </button>
        </div>

        @if($cari || $jurusanId)
          <a href="{{ route('admin.ppdb.wawancara', ['tab' => $tab]) }}" class="btn btn-sm" style="background:#f1f5f9; border:1px solid #cbd5e1; color:#475569; font-weight:700; padding:6px 10px; border-radius:6px; text-decoration:none;" title="Reset filter">
            <i class="bi bi-x-lg"></i>
          </a>
        @endif
      </form>
    </div>

    {{-- TABEL ANTREAN SISWA --}}
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
      <table class="table" style="width:100%; margin:0; border-collapse:collapse; font-size:12.5px;">
        <thead style="background:#f1f5f9; border-bottom:1px solid #cbd5e1; font-weight:800; color:var(--text-2);">
          <tr>
            <th style="padding:12px 16px; width:40px;">No</th>
            <th style="padding:12px 16px;">Peserta Calon Siswa</th>
            <th style="padding:12px 16px;">Pilihan Jurusan</th>
            <th style="padding:12px 16px; text-align:center;">Status</th>
            <th style="padding:12px 16px; text-align:center;">Skor Wawancara</th>
            <th style="padding:12px 16px;">Penguji / Waktu</th>
            <th style="padding:12px 16px; text-align:center; width:180px;">Aksi Penilaian</th>
          </tr>
        </thead>
        <tbody>
          @forelse($peserta as $index => $p)
            @php
              $pPayload = [
                'id' => $p->id,
                'nama_lengkap' => $p->nama_lengkap,
                'no_pendaftaran' => $p->no_pendaftaran,
                'nisn' => $p->nisn ?? '-',
                'asal_sekolah' => $p->asal_sekolah ?? '-',
                'jurusan1_nama' => $p->jurusan1->nama_jurusan ?? ($p->jurusanPilihan1->nama_jurusan ?? '-'),
                'jurusan1_kode' => $p->jurusan1->kode_jurusan ?? ($p->jurusanPilihan1->kode_jurusan ?? ''),
                'jurusan2_nama' => $p->jurusan2->nama_jurusan ?? ($p->jurusanPilihan2->nama_jurusan ?? '-'),
                'jurusan2_kode' => $p->jurusan2->kode_jurusan ?? ($p->jurusanPilihan2->kode_jurusan ?? ''),
                'nilai_wawancara_motivasi' => $p->nilai_wawancara_motivasi,
                'nilai_wawancara_karakter' => $p->nilai_wawancara_karakter,
                'nilai_wawancara_kejuruan' => $p->nilai_wawancara_kejuruan,
                'nilai_wawancara_ortu' => $p->nilai_wawancara_ortu,
                'nilai_wawancara_total' => $p->nilai_wawancara_total,
                'catatan_wawancara' => $p->catatan_wawancara,
                'pewawancara_nama' => $p->pewawancara->name ?? null,
                'diwawancara_pada' => $p->diwawancara_pada ? $p->diwawancara_pada->format('d/m/Y H:i') : null,
              ];
            @endphp
            <tr style="border-bottom:1px solid var(--border); {{ $p->nilai_wawancara_total !== null ? '' : 'background:rgba(248,250,252,0.6);' }}">
              <td style="padding:12px 16px; font-weight:700; color:var(--text-3);">
                {{ $peserta->firstItem() + $index }}
              </td>
              <td style="padding:12px 16px;">
                <strong style="color:var(--text-1); font-size:13.5px; display:block;">{{ $p->nama_lengkap }}</strong>
                <div style="display:flex; align-items:center; gap:8px; margin-top:2px;">
                  <span style="font-family:monospace; color:#2563eb; font-weight:700; font-size:11.5px;">{{ $p->no_pendaftaran }}</span>
                  <span style="color:#64748b; font-size:11px;">SMP: {{ $p->asal_sekolah ?? '-' }}</span>
                </div>
              </td>
              <td style="padding:12px 16px;">
                <span style="font-weight:700; color:var(--text-1); display:block;">{{ $pPayload['jurusan1_nama'] }}</span>
                @if(!empty($pPayload['jurusan2_nama']) && $pPayload['jurusan2_nama'] !== '-')
                  <div style="font-size:11px; color:#64748b;">Pil 2: {{ $pPayload['jurusan2_nama'] }}</div>
                @endif
              </td>
              <td style="padding:12px 16px; text-align:center;">
                @if($p->nilai_wawancara_total !== null)
                  <span style="background:#ecfdf5; color:#059669; font-weight:800; font-size:11px; padding:3px 8px; border-radius:6px; border:1px solid #a7f3d0; display:inline-flex; align-items:center; gap:4px;">
                    <i class="bi bi-check-circle-fill"></i> Sudah Diuji
                  </span>
                @else
                  <span style="background:#fffbeb; color:#b45309; font-weight:800; font-size:11px; padding:3px 8px; border-radius:6px; border:1px solid #fde68a; display:inline-flex; align-items:center; gap:4px;">
                    <i class="bi bi-hourglass-split"></i> Belum Wawancara
                  </span>
                @endif
              </td>
              <td style="padding:12px 16px; text-align:center; font-family:monospace; font-weight:900; font-size:15px; color:#059669;">
                {{ $p->nilai_wawancara_total !== null ? number_format($p->nilai_wawancara_total, 1) : '-' }}
              </td>
              <td style="padding:12px 16px;">
                @if($p->pewawancara)
                  <div style="font-weight:700; color:#0f172a; font-size:12px;">{{ $p->pewawancara->name }}</div>
                  @if($p->diwawancara_pada)
                    <div style="font-size:10.5px; color:#64748b;">Diuji: {{ $p->diwawancara_pada->format('d/m/Y H:i') }} WIB</div>
                  @else
                    <span style="background:#eff6ff; color:#1d4ed8; font-size:10px; font-weight:700; padding:1px 6px; border-radius:4px; border:1px solid #bfdbfe;">Ditunjuk (Pra-Tes)</span>
                  @endif
                @else
                  <span style="color:#94a3b8; font-size:11px;">Belum Ditunjuk</span>
                @endif
              </td>
              <td style="padding:12px 16px; text-align:center; white-space:nowrap;">
                <button type="button" class="btn btn-sm" onclick="bukaModalWawancara({{ json_encode($pPayload) }})" style="background:#0284c7; color:#ffffff; font-weight:800; font-size:11.5px; border-radius:6px; border:none; padding:6px 12px; cursor:pointer; display:inline-flex; align-items:center; gap:5px; box-shadow:0 2px 4px rgba(2,132,199,0.25);">
                  <i class="bi bi-mic-fill"></i> {{ $p->nilai_wawancara_total !== null ? 'Ubah Nilai' : 'Mulai Uji' }}
                </button>
                <a href="{{ route('admin.ppdb.wawancara.cetak', $p->id) }}" target="_blank" class="btn btn-sm" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; font-weight:700; font-size:11.5px; border-radius:6px; padding:5px 9px; text-decoration:none; display:inline-flex; align-items:center; gap:3px; margin-left:4px;" title="Cetak Lembar Format A4">
                  <i class="bi bi-printer"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="padding:36px; text-align:center; color:var(--text-3);">
                <i class="bi bi-inbox" style="font-size:32px; display:block; margin-bottom:8px; color:#cbd5e1;"></i>
                <div style="font-weight:700; font-size:13px; color:var(--text-2);">Tidak ada calon siswa di antrean ini.</div>
                <div style="font-size:11.5px; color:#94a3b8; margin-top:2px;">Silakan pilih tab <em>Semua Calon Siswa</em> atau hubungi panitia PPDB untuk plotting siswa ke meja Anda.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>

      @if($peserta->hasPages())
        <div style="padding:12px 18px; border-top:1px solid var(--border); background:#ffffff;">
          {{ $peserta->links() }}
        </div>
      @endif
    </div>

  </main>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL FORM PENILAIAN WAWANCARA TERPADU & BUTA WARNA --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modalWawancara" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.7); backdrop-filter:blur(5px); z-index:1050; align-items:center; justify-content:center; padding:16px;">
  <div style="background:#ffffff; border-radius:18px; max-width:860px; width:100%; max-height:92vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0,0,0,0.35);">
    
    {{-- MODAL HEADER --}}
    <div style="padding:18px 24px; background:#0f172a; color:#ffffff; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #334155;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:40px; height:40px; border-radius:10px; background:#2563eb; display:flex; align-items:center; justify-content:center; font-size:18px;">
          <i class="bi bi-person-lines-fill"></i>
        </div>
        <div>
          <h3 style="font-size:16px; font-weight:900; margin:0; line-height:1.2;" id="w_modal_nama">Nama Calon Siswa</h3>
          <div style="font-size:12px; color:#94a3b8; margin-top:2px;">
            <span id="w_modal_nopendaftar" style="font-family:monospace; color:#38bdf8; font-weight:700;">-</span> &bull; 
            Asal: <span id="w_modal_asalsekolah">-</span>
          </div>
        </div>
      </div>
      <button type="button" onclick="document.getElementById('modalWawancara').style.display='none'" style="border:none; background:transparent; font-size:24px; cursor:pointer; color:#94a3b8; line-height:1;">&times;</button>
    </div>

    {{-- INFO JURUSAN & SHORTCUT BUTA WARNA --}}
    <div style="padding:12px 24px; background:#f8fafc; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; font-size:12px;">
      <div>
        <span style="color:#64748b; font-weight:700;">Pilihan 1:</span> <strong id="w_modal_jurusan1" style="color:#0f172a;">-</strong>
        <span style="margin:0 8px; color:#cbd5e1;">|</span>
        <span style="color:#64748b; font-weight:700;">Pilihan 2:</span> <span id="w_modal_jurusan2" style="color:#475569;">-</span>
      </div>
      <div>
        <button type="button" onclick="bukaModalTesIshihara()" class="btn btn-sm" style="background:#ecfdf5; border:1px solid #a7f3d0; color:#059669; font-weight:800; padding:4px 10px; border-radius:6px; font-size:11.5px; cursor:pointer; display:inline-flex; align-items:center; gap:5px;">
          <i class="bi bi-eye-fill"></i> Uji Buta Warna Interaktif (Ishihara)
        </button>
      </div>
    </div>

    {{-- SCROLLABLE FORM BODY --}}
    <form id="formWawancara" method="POST" style="overflow-y:auto; padding:20px 24px; flex:1;">
      @csrf

      {{-- 1. DIMENSI MOTIVASI --}}
      <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
          <div style="font-weight:900; font-size:13.5px; color:#1e40af; display:flex; align-items:center; gap:6px;">
            <span style="width:22px; height:22px; border-radius:5px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:800;">1</span>
            Motivasi &amp; Orientasi Minat (Bobot 25%)
          </div>
          <div style="display:flex; align-items:center; gap:6px;">
            <input type="number" name="nilai_wawancara_motivasi" id="w_motivasi" min="0" max="100" step="1" required class="form-control" style="width:75px; font-weight:900; font-size:15px; text-align:center; color:#1e40af; font-family:monospace;" oninput="hitungTotalWawancara()">
            <span style="font-size:12px; font-weight:700; color:#64748b;">/ 100</span>
          </div>
        </div>
        <div style="font-size:11.5px; color:#475569; background:#f8fafc; padding:8px 12px; border-radius:6px; border-left:3px solid #3b82f6; margin-bottom:8px;">
          <strong>Pertanyaan Pemandu:</strong><br>
          @foreach($mw['motivasi']['pertanyaan'] ?? [] as $q)
            &bull; {{ $q }}<br>
          @endforeach
        </div>
        <div style="display:flex; gap:6px;">
          <button type="button" onclick="setNilaiW('w_motivasi', 95)" class="btn btn-sm btn-outline-secondary" style="font-size:11px; padding:2px 8px;">95 (Sangat Kuat)</button>
          <button type="button" onclick="setNilaiW('w_motivasi', 85)" class="btn btn-sm btn-outline-secondary" style="font-size:11px; padding:2px 8px;">85 (Kuat)</button>
          <button type="button" onclick="setNilaiW('w_motivasi', 75)" class="btn btn-sm btn-outline-secondary" style="font-size:11px; padding:2px 8px;">75 (Cukup)</button>
        </div>
      </div>

      {{-- 2. DIMENSI KARAKTER --}}
      <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
          <div style="font-weight:900; font-size:13.5px; color:#065f46; display:flex; align-items:center; gap:6px;">
            <span style="width:22px; height:22px; border-radius:5px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:800;">2</span>
            Karakter, Sikap, Integritas &amp; Disiplin (Bobot 25%)
          </div>
          <div style="display:flex; align-items:center; gap:6px;">
            <input type="number" name="nilai_wawancara_karakter" id="w_karakter" min="0" max="100" step="1" required class="form-control" style="width:75px; font-weight:900; font-size:15px; text-align:center; color:#065f46; font-family:monospace;" oninput="hitungTotalWawancara()">
            <span style="font-size:12px; font-weight:700; color:#64748b;">/ 100</span>
          </div>
        </div>
        <div style="font-size:11.5px; color:#475569; background:#f8fafc; padding:8px 12px; border-radius:6px; border-left:3px solid #10b981; margin-bottom:8px;">
          <strong>Pertanyaan Pemandu:</strong><br>
          @foreach($mw['karakter']['pertanyaan'] ?? [] as $q)
            &bull; {{ $q }}<br>
          @endforeach
        </div>
        <div style="display:flex; gap:6px;">
          <button type="button" onclick="setNilaiW('w_karakter', 95)" class="btn btn-sm btn-outline-secondary" style="font-size:11px; padding:2px 8px;">95 (Sangat Baik)</button>
          <button type="button" onclick="setNilaiW('w_karakter', 85)" class="btn btn-sm btn-outline-secondary" style="font-size:11px; padding:2px 8px;">85 (Baik)</button>
          <button type="button" onclick="setNilaiW('w_karakter', 75)" class="btn btn-sm btn-outline-secondary" style="font-size:11px; padding:2px 8px;">75 (Cukup)</button>
        </div>
      </div>

      {{-- 3. DIMENSI KESIAPAN FISIK & POTENSI KEJURUAN --}}
      <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
          <div style="font-weight:900; font-size:13.5px; color:#9a3412; display:flex; align-items:center; gap:6px;">
            <span style="width:22px; height:22px; border-radius:5px; background:#fff7ed; color:#ea580c; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:800;">3</span>
            Kesiapan Fisik &amp; Potensi Kejuruan (Bobot 30%)
          </div>
          <div style="display:flex; align-items:center; gap:6px;">
            <input type="number" name="nilai_wawancara_kejuruan" id="w_kejuruan" min="0" max="100" step="1" required class="form-control" style="width:75px; font-weight:900; font-size:15px; text-align:center; color:#9a3412; font-family:monospace;" oninput="hitungTotalWawancara()">
            <span style="font-size:12px; font-weight:700; color:#64748b;">/ 100</span>
          </div>
        </div>

        {{-- Dynamic Question per Major --}}
        <div id="w_panduan_kejuruan_rpl" class="panduan-kejuruan-box" style="display:none; font-size:11.5px; color:#475569; background:#eff6ff; padding:8px 12px; border-radius:6px; border-left:3px solid #2563eb; margin-bottom:8px;">
          <strong>Panduan Kejuruan RPL (Perangkat Lunak):</strong><br>
          @foreach($mw['kejuruan_rpl']['pertanyaan'] ?? [] as $q)
            &bull; {{ $q }}<br>
          @endforeach
        </div>

        <div id="w_panduan_kejuruan_tsm" class="panduan-kejuruan-box" style="display:none; font-size:11.5px; color:#475569; background:#fef2f2; padding:8px 12px; border-radius:6px; border-left:3px solid #dc2626; margin-bottom:8px;">
          <strong>Panduan Kejuruan TSM (Sepeda Motor / Otomotif):</strong><br>
          @foreach($mw['kejuruan_tsm']['pertanyaan'] ?? [] as $q)
            &bull; {{ $q }}<br>
          @endforeach
        </div>

        <div id="w_panduan_kejuruan_aphp" class="panduan-kejuruan-box" style="display:none; font-size:11.5px; color:#475569; background:#ecfdf5; padding:8px 12px; border-radius:6px; border-left:3px solid #059669; margin-bottom:8px;">
          <strong>Panduan Kejuruan APHP (Hasil Pertanian):</strong><br>
          @foreach($mw['kejuruan_aphp']['pertanyaan'] ?? [] as $q)
            &bull; {{ $q }}<br>
          @endforeach
        </div>

        <div id="w_panduan_kejuruan_default" class="panduan-kejuruan-box" style="font-size:11.5px; color:#475569; background:#f8fafc; padding:8px 12px; border-radius:6px; border-left:3px solid #f97316; margin-bottom:8px;">
          <strong>Panduan Kejuruan Umum:</strong><br>
          &bull; Pemahaman calon siswa tentang kejuruan yang dipilih.<br>
          &bull; Kesiapan fisik, ketelitian, dan bebas buta warna sesuai tuntutan kompetensi.
        </div>

        <div style="display:flex; gap:6px;">
          <button type="button" onclick="setNilaiW('w_kejuruan', 95)" class="btn btn-sm btn-outline-secondary" style="font-size:11px; padding:2px 8px;">95 (Sangat Sesuai)</button>
          <button type="button" onclick="setNilaiW('w_kejuruan', 85)" class="btn btn-sm btn-outline-secondary" style="font-size:11px; padding:2px 8px;">85 (Sesuai)</button>
          <button type="button" onclick="setNilaiW('w_kejuruan', 75)" class="btn btn-sm btn-outline-secondary" style="font-size:11px; padding:2px 8px;">75 (Cukup)</button>
        </div>
      </div>

      {{-- 4. DIMENSI DUKUNGAN ORANG TUA --}}
      <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
          <div style="font-weight:900; font-size:13.5px; color:#6b21a8; display:flex; align-items:center; gap:6px;">
            <span style="width:22px; height:22px; border-radius:5px; background:#faf5ff; color:#9333ea; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:800;">4</span>
            Dukungan &amp; Komitmen Orang Tua (Bobot 20%)
          </div>
          <div style="display:flex; align-items:center; gap:6px;">
            <input type="number" name="nilai_wawancara_ortu" id="w_ortu" min="0" max="100" step="1" required class="form-control" style="width:75px; font-weight:900; font-size:15px; text-align:center; color:#6b21a8; font-family:monospace;" oninput="hitungTotalWawancara()">
            <span style="font-size:12px; font-weight:700; color:#64748b;">/ 100</span>
          </div>
        </div>
        <div style="font-size:11.5px; color:#475569; background:#f8fafc; padding:8px 12px; border-radius:6px; border-left:3px solid #a855f7; margin-bottom:8px;">
          <strong>Pertanyaan Pemandu:</strong><br>
          @foreach($mw['ortu']['pertanyaan'] ?? [] as $q)
            &bull; {{ $q }}<br>
          @endforeach
        </div>
        <div style="display:flex; gap:6px;">
          <button type="button" onclick="setNilaiW('w_ortu', 95)" class="btn btn-sm btn-outline-secondary" style="font-size:11px; padding:2px 8px;">95 (Sangat Mendukung)</button>
          <button type="button" onclick="setNilaiW('w_ortu', 85)" class="btn btn-sm btn-outline-secondary" style="font-size:11px; padding:2px 8px;">85 (Mendukung Penuh)</button>
          <button type="button" onclick="setNilaiW('w_ortu', 75)" class="btn btn-sm btn-outline-secondary" style="font-size:11px; padding:2px 8px;">75 (Cukup)</button>
        </div>
      </div>

      {{-- 5. CATATAN & REKOMENDASI --}}
      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:12px; font-weight:700; color:#334155; margin-bottom:5px;">Catatan Observasi / Rekomendasi Khusus Penguji:</label>
        <textarea name="catatan_wawancara" id="w_catatan" class="form-control" rows="2" style="font-size:12px;" placeholder="Catatan sikap, hasil tes buta warna, komitmen, atau pertimbangan jurusan..."></textarea>
        <div style="display:flex; gap:6px; flex-wrap:wrap; margin-top:6px;">
          <button type="button" onclick="tambahCatatanCepat('Sikap sangat santun dan motivasi belajar tinggi. ')" class="btn btn-sm btn-outline-secondary" style="font-size:11px; padding:2px 8px;">+ Sikap Santun</button>
          <button type="button" onclick="tambahCatatanCepat('Bebas buta warna dan fisik prima. ')" class="btn btn-sm btn-outline-secondary" style="font-size:11px; padding:2px 8px;">+ Bebas Buta Warna</button>
          <button type="button" onclick="tambahCatatanCepat('Sangat direkomendasikan pada pilihan kejuruan 1. ')" class="btn btn-sm btn-outline-secondary" style="font-size:11px; padding:2px 8px;">+ Rekomendasi Pil 1</button>
        </div>
      </div>

      {{-- LIVE PREVIEW TOTAL --}}
      <div style="background:linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border:1px solid #bfdbfe; border-radius:12px; padding:14px 18px; display:flex; justify-content:space-between; align-items:center;">
        <div>
          <div style="font-size:11px; font-weight:800; text-transform:uppercase; color:#1e40af; letter-spacing:0.05em;">Total Skor Wawancara (Bobot 30% Seleksi):</div>
          <div style="font-size:11.5px; color:#475569; margin-top:2px;">Kalkulasi: 25% Motivasi + 25% Karakter + 30% Kejuruan + 20% Ortu</div>
        </div>
        <div style="text-align:right;">
          <div id="w_preview_predikat" style="font-size:11px; font-weight:800; padding:2px 10px; border-radius:12px; background:#dcfce7; color:#15803d; display:inline-block;">
            Sangat Direkomendasikan
          </div>
          <div id="w_preview_total" style="font-size:24px; font-weight:900; color:#1d4ed8; font-family:monospace; line-height:1.1; margin-top:2px;">
            80.00
          </div>
        </div>
      </div>

      {{-- MODAL ACTIONS --}}
      <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #e2e8f0; padding-top:16px; margin-top:18px;">
        <button type="button" onclick="document.getElementById('modalWawancara').style.display='none'" class="btn" style="background:#f1f5f9; color:#475569; font-weight:700; padding:8px 18px; border-radius:8px; font-size:13px; cursor:pointer;">
          Tutup
        </button>
        <button type="submit" class="btn" style="background:#0284c7; color:#ffffff; font-weight:800; padding:8px 22px; border-radius:8px; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 4px 12px rgba(2,132,199,0.3);">
          <i class="bi bi-check2-circle"></i> Simpan Nilai Wawancara
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODAL TES BUTA WARNA ISHIHARA INTERAKTIF (8 STANDAR SVG PLATES) --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="modalTesIshihara" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.8); backdrop-filter:blur(6px); z-index:1100; align-items:center; justify-content:center; padding:16px;">
  <div style="background:#ffffff; border-radius:16px; max-width:980px; width:100%; max-height:92vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 25px 50px -12px rgba(0,0,0,0.45);">
    
    {{-- HEADER --}}
    <div style="padding:16px 22px; background:#0f172a; color:#ffffff; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #334155;">
      <div>
        <h3 style="font-size:16px; font-weight:900; margin:0 0 2px; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-eye-fill text-emerald-400"></i> Uji Persepsi Buta Warna Ishihara (8 Piringan Standar)
        </h3>
        <div style="font-size:12px; color:#94a3b8;">
          Calon siswa melihat piringan pada layar dalam jarak ~75 cm. Pilih respon bacaan siswa pada setiap piringan.
        </div>
      </div>
      <button type="button" onclick="tutupModalTesIshihara()" style="border:none; background:transparent; font-size:24px; cursor:pointer; color:#94a3b8; line-height:1;">&times;</button>
    </div>

    {{-- QUICK ACTION & STATUS BAR --}}
    <div style="padding:12px 22px; background:#f8fafc; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
      <div style="display:flex; align-items:center; gap:8px;">
        <button type="button" onclick="setSemuaIshiharaNormal()" class="btn btn-sm" style="background:#ecfdf5; border:1.5px solid #10b981; color:#047857; font-weight:800; font-size:11.5px; padding:5px 12px; border-radius:6px; cursor:pointer;">
          <i class="bi bi-lightning-charge-fill text-amber-500"></i> Set Semua Normal (Bebas Buta Warna)
        </button>
        <button type="button" onclick="resetTesIshihara()" class="btn btn-sm" style="background:#ffffff; border:1px solid #cbd5e1; color:#475569; font-weight:700; font-size:11.5px; padding:5px 12px; border-radius:6px; cursor:pointer;">
          <i class="bi bi-arrow-counterclockwise"></i> Reset
        </button>
      </div>

      <div style="display:flex; align-items:center; gap:10px;">
        <div style="font-size:12px; color:#475569;">
          Skor: <strong id="live_ishihara_skor" style="font-size:14px; font-family:monospace; color:#059669;">8 / 8</strong>
        </div>
        <span id="live_ishihara_diagnosa" style="background:#ecfdf5; color:#059669; font-weight:900; font-size:12px; padding:4px 12px; border-radius:20px; border:1px solid #a7f3d0;">
          <i class="bi bi-check-circle-fill"></i> Bebas Buta Warna (Normal)
        </span>
      </div>
    </div>

    {{-- GRID 8 PIRINGAN --}}
    <div style="padding:16px 20px; overflow-y:auto; flex:1; background:#f1f5f9; display:grid; grid-template-columns:repeat(auto-fill, minmax(210px, 1fr)); gap:14px;">
      @php
        $platesData = [
          ['no' => 1, 'num' => '12', 'tipe' => 'Demonstrasi / Kontrol', 'norm' => '12', 'pars' => '12', 'tot' => '12', 'fg' => ['#e05238', '#eb6244', '#cf4128', '#d9472e'], 'bg' => ['#689f63', '#7ba977', '#8bb487', '#5f965a', '#54874f']],
          ['no' => 2, 'num' => '8', 'tipe' => 'Transformasi Merah-Hijau', 'norm' => '8', 'pars' => '3', 'tot' => 'Tidak Ada', 'fg' => ['#d9534f', '#c9302c', '#d43f3a', '#b92c28'], 'bg' => ['#5cb85c', '#4cae4c', '#6ec06e', '#7ec87e', '#419641']],
          ['no' => 3, 'num' => '6', 'tipe' => 'Transformasi Merah-Hijau', 'norm' => '6', 'pars' => '5', 'tot' => 'Tidak Ada', 'fg' => ['#d9534f', '#c9302c', '#e74c3c', '#c0392b'], 'bg' => ['#5cb85c', '#4cae4c', '#27ae60', '#2ecc71', '#419641']],
          ['no' => 4, 'num' => '29', 'tipe' => 'Transformasi Merah-Hijau', 'norm' => '29', 'pars' => '70', 'tot' => 'Tidak Ada', 'fg' => ['#d9534f', '#c0392b', '#e67e22', '#d35400'], 'bg' => ['#27ae60', '#2ecc71', '#5cb85c', '#4cae4c', '#16a085']],
          ['no' => 5, 'num' => '57', 'tipe' => 'Penyamaran Merah-Hijau', 'norm' => '57', 'pars' => '35', 'tot' => 'Tidak Ada', 'fg' => ['#e67e22', '#d35400', '#f39c12', '#e74c3c'], 'bg' => ['#27ae60', '#2ecc71', '#5cb85c', '#1abc9c', '#16a085']],
          ['no' => 6, 'num' => '5', 'tipe' => 'Transformasi Merah-Hijau', 'norm' => '5', 'pars' => '2', 'tot' => 'Tidak Ada', 'fg' => ['#27ae60', '#2ecc71', '#1abc9c', '#16a085'], 'bg' => ['#d9534f', '#e67e22', '#f39c12', '#c0392b', '#d35400']],
          ['no' => 7, 'num' => '3', 'tipe' => 'Transformasi Merah-Hijau', 'norm' => '3', 'pars' => '5', 'tot' => 'Tidak Ada', 'fg' => ['#27ae60', '#2ecc71', '#5cb85c', '#4cae4c'], 'bg' => ['#d9534f', '#c0392b', '#e74c3c', '#e67e22', '#d35400']],
          ['no' => 8, 'num' => '74', 'tipe' => 'Transformasi Merah-Hijau', 'norm' => '74', 'pars' => '21', 'tot' => 'Tidak Ada', 'fg' => ['#27ae60', '#2ecc71', '#16a085', '#5cb85c'], 'bg' => ['#d9534f', '#c0392b', '#d35400', '#e74c3c', '#f39c12']],
        ];
      @endphp

      @foreach($platesData as $p)
        <div class="plate-tester-card" id="plate-card-{{ $p['no'] }}" style="background:#ffffff; border:1.5px solid #e2e8f0; border-radius:12px; padding:12px; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,0.05); display:flex; flex-direction:column; align-items:center;">
          {{-- SVG PLATE --}}
          <div style="width:115px; height:115px; margin-bottom:8px; filter:drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
            <svg viewBox="0 0 200 200" width="115" height="115" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <clipPath id="circle-modal-{{ $p['no'] }}">
                  <circle cx="100" cy="100" r="94"/>
                </clipPath>
                <mask id="mask-modal-{{ $p['no'] }}">
                  <rect width="200" height="200" fill="black" />
                  <text x="100" y="{{ strlen($p['num']) > 1 ? '128' : '136' }}" font-family="Arial, Helvetica, sans-serif" font-weight="900" font-size="{{ strlen($p['num']) > 1 ? '86' : '108' }}" text-anchor="middle" fill="white" letter-spacing="-2">{{ $p['num'] }}</text>
                </mask>
              </defs>
              <circle cx="100" cy="100" r="98" fill="#f8fafc" stroke="#e2e8f0" stroke-width="2"/>
              <g clip-path="url(#circle-modal-{{ $p['no'] }})">
                @php
                  $bgCols = $p['bg'];
                  $fgCols = $p['fg'];
                  $step = 12;
                  $dots = [];
                  for ($x = 10; $x <= 190; $x += $step) {
                    for ($y = 10; $y <= 190; $y += $step) {
                      $dx = $x - 100;
                      $dy = $y - 100;
                      if (($dx*$dx + $dy*$dy) < (92 * 92)) {
                        $jitterX = $x + (($x * 7 + $y * 13 + $p['no']) % 7) - 3;
                        $jitterY = $y + (($x * 11 + $y * 5 + $p['no']) % 7) - 3;
                        $radius = 3.5 + (($x * 3 + $y * 7) % 4);
                        $cIdx = ($x + $y + $p['no']) % count($bgCols);
                        $dots[] = ['x' => $jitterX, 'y' => $jitterY, 'r' => $radius, 'c' => $bgCols[$cIdx]];
                      }
                    }
                  }
                @endphp
                @foreach($dots as $d)
                  <circle cx="{{ $d['x'] }}" cy="{{ $d['y'] }}" r="{{ $d['r'] }}" fill="{{ $d['c'] }}" opacity="0.95"/>
                @endforeach
                <g mask="url(#mask-modal-{{ $p['no'] }})">
                  <circle cx="100" cy="100" r="95" fill="{{ $fgCols[0] }}"/>
                  @foreach($dots as $d)
                    @php $fgIdx = ($d['x'] * 3 + $d['y'] * 5 + $p['no']) % count($fgCols); @endphp
                    <circle cx="{{ $d['x'] }}" cy="{{ $d['y'] }}" r="{{ $d['r'] + 0.5 }}" fill="{{ $fgCols[$fgIdx] }}"/>
                  @endforeach
                </g>
              </g>
              <circle cx="100" cy="100" r="94" fill="none" stroke="rgba(0,0,0,0.12)" stroke-width="1.5"/>
            </svg>
          </div>

          {{-- LABEL --}}
          <div style="font-size:11.5px; font-weight:800; color:#0f172a; margin-bottom:2px;">
            Piringan #{{ $p['no'] }}
          </div>
          <div style="font-size:10px; color:#64748b; margin-bottom:8px;">
            {{ $p['tipe'] }}
          </div>

          {{-- PILIHAN RESPON PESERTA --}}
          <div style="display:flex; flex-direction:column; gap:4px; width:100%;">
            <button type="button" id="btn-p{{ $p['no'] }}-norm" class="btn-ish-opt is-active" onclick="pilihIshiharaPlate({{ $p['no'] }}, 'norm', '{{ $p['norm'] }}')" style="border:1.5px solid #10b981; background:#ecfdf5; color:#065f46; font-size:11px; font-weight:800; padding:5px 8px; border-radius:6px; cursor:pointer; text-align:center;">
              ✓ Angka {{ $p['norm'] }} (Normal)
            </button>
            @if($p['no'] > 1)
              <button type="button" id="btn-p{{ $p['no'] }}-pars" class="btn-ish-opt" onclick="pilihIshiharaPlate({{ $p['no'] }}, 'pars', '{{ $p['pars'] }}')" style="border:1px solid #cbd5e1; background:#f8fafc; color:#334155; font-size:11px; font-weight:700; padding:5px 8px; border-radius:6px; cursor:pointer; text-align:center;">
                Angka {{ $p['pars'] }} (Parsial)
              </button>
            @endif
            <button type="button" id="btn-p{{ $p['no'] }}-salah" class="btn-ish-opt" onclick="pilihIshiharaPlate({{ $p['no'] }}, 'salah', 'Tidak Terbaca')" style="border:1px solid #cbd5e1; background:#f8fafc; color:#334155; font-size:11px; font-weight:700; padding:5px 8px; border-radius:6px; cursor:pointer; text-align:center;">
              ✗ Tidak Terbaca / Salah
            </button>
          </div>
        </div>
      @endforeach
    </div>

    {{-- FOOTER ACTION --}}
    <div style="padding:14px 22px; background:#ffffff; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
      <div style="font-size:12px; color:#64748b;">
        Standar: Normal (7-8 benar) &bull; Parsial (merah-hijau) &bull; Total (&le;1 benar).
      </div>

      <div style="display:flex; gap:10px;">
        <button type="button" onclick="tutupModalTesIshihara()" class="btn btn-sm" style="background:#f1f5f9; color:#475569; font-weight:700; padding:8px 18px; border-radius:8px; font-size:13px; cursor:pointer;">
          Tutup
        </button>
        <button type="button" onclick="terapkanHasilIshihara()" class="btn btn-sm" style="background:#059669; color:#ffffff; font-weight:800; padding:8px 22px; border-radius:8px; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 4px 12px rgba(5,150,105,0.3);">
          <i class="bi bi-check2-circle"></i> Terapkan ke Form Wawancara
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  let ishiharaState = { 1: 'norm', 2: 'norm', 3: 'norm', 4: 'norm', 5: 'norm', 6: 'norm', 7: 'norm', 8: 'norm' };

  function bukaModalWawancara(data) {
    document.getElementById('w_modal_nama').innerText = data.nama_lengkap;
    document.getElementById('w_modal_nopendaftar').innerText = data.no_pendaftaran;
    document.getElementById('w_modal_asalsekolah').innerText = data.asal_sekolah;
    document.getElementById('w_modal_jurusan1').innerText = data.jurusan1_nama;
    document.getElementById('w_modal_jurusan2').innerText = data.jurusan2_nama;

    document.getElementById('formWawancara').action = `/admin/ppdb/wawancara/nilai/${data.id}`;

    // Fill existing values or defaults
    document.getElementById('w_motivasi').value = data.nilai_wawancara_motivasi !== null ? data.nilai_wawancara_motivasi : 80;
    document.getElementById('w_karakter').value = data.nilai_wawancara_karakter !== null ? data.nilai_wawancara_karakter : 80;
    document.getElementById('w_kejuruan').value = data.nilai_wawancara_kejuruan !== null ? data.nilai_wawancara_kejuruan : 80;
    document.getElementById('w_ortu').value     = data.nilai_wawancara_ortu !== null ? data.nilai_wawancara_ortu : 80;
    document.getElementById('w_catatan').value  = data.catatan_wawancara || '';

    // Switch major questions
    document.querySelectorAll('.panduan-kejuruan-box').forEach(el => el.style.display = 'none');
    const kode1 = (data.jurusan1_kode || '').toUpperCase();
    const nama1 = (data.jurusan1_nama || '').toUpperCase();

    if (kode1.includes('RPL') || nama1.includes('REKAYASA') || nama1.includes('PERANGKAT')) {
      document.getElementById('w_panduan_kejuruan_rpl').style.display = 'block';
    } else if (kode1.includes('TSM') || nama1.includes('MOTOR') || nama1.includes('OTOMOTIF')) {
      document.getElementById('w_panduan_kejuruan_tsm').style.display = 'block';
    } else if (kode1.includes('APHP') || nama1.includes('PERTANIAN') || nama1.includes('PENGOLAHAN')) {
      document.getElementById('w_panduan_kejuruan_aphp').style.display = 'block';
    } else {
      document.getElementById('w_panduan_kejuruan_default').style.display = 'block';
    }

    hitungTotalWawancara();
    document.getElementById('modalWawancara').style.display = 'flex';
  }

  function setNilaiW(inputId, val) {
    document.getElementById(inputId).value = val;
    hitungTotalWawancara();
  }

  function tambahCatatanCepat(text) {
    const el = document.getElementById('w_catatan');
    if (el.value.indexOf(text.trim()) === -1) {
      el.value = (el.value ? el.value.trim() + ' ' : '') + text;
    }
  }

  function hitungTotalWawancara() {
    const m = parseFloat(document.getElementById('w_motivasi').value) || 0;
    const k = parseFloat(document.getElementById('w_karakter').value) || 0;
    const j = parseFloat(document.getElementById('w_kejuruan').value) || 0;
    const o = parseFloat(document.getElementById('w_ortu').value) || 0;

    const total = (m * 0.25) + (k * 0.25) + (j * 0.30) + (o * 0.20);
    document.getElementById('w_preview_total').innerText = total.toFixed(2);

    const predikatEl = document.getElementById('w_preview_predikat');
    if (total >= 85) {
      predikatEl.innerText = 'Sangat Direkomendasikan (A)';
      predikatEl.style.background = '#dcfce7';
      predikatEl.style.color = '#15803d';
    } else if (total >= 75) {
      predikatEl.innerText = 'Direkomendasikan (B)';
      predikatEl.style.background = '#eff6ff';
      predikatEl.style.color = '#1d4ed8';
    } else if (total >= 65) {
      predikatEl.innerText = 'Cukup / Dipertimbangkan (C)';
      predikatEl.style.background = '#fef9c3';
      predikatEl.style.color = '#854d0e';
    } else {
      predikatEl.innerText = 'Kurang Direkomendasikan (D)';
      predikatEl.style.background = '#fee2e2';
      predikatEl.style.color = '#b91c1c';
    }
  }

  function bukaModalTesIshihara() {
    document.getElementById('modalTesIshihara').style.display = 'flex';
    hitungHasilIshihara();
  }

  function tutupModalTesIshihara() {
    document.getElementById('modalTesIshihara').style.display = 'none';
  }

  function pilihIshiharaPlate(plateNo, tipe, val) {
    ishiharaState[plateNo] = tipe;
    
    const btnNorm = document.getElementById(`btn-p${plateNo}-norm`);
    const btnPars = document.getElementById(`btn-p${plateNo}-pars`);
    const btnSalah = document.getElementById(`btn-p${plateNo}-salah`);

    const resetStyle = (btn) => {
      if (!btn) return;
      btn.style.border = '1px solid #cbd5e1';
      btn.style.background = '#f8fafc';
      btn.style.color = '#334155';
      btn.style.fontWeight = '700';
    };

    resetStyle(btnNorm);
    resetStyle(btnPars);
    resetStyle(btnSalah);

    if (tipe === 'norm' && btnNorm) {
      btnNorm.style.border = '1.5px solid #10b981';
      btnNorm.style.background = '#ecfdf5';
      btnNorm.style.color = '#065f46';
      btnNorm.style.fontWeight = '800';
    } else if (tipe === 'pars' && btnPars) {
      btnPars.style.border = '1.5px solid #f59e0b';
      btnPars.style.background = '#fffbeb';
      btnPars.style.color = '#b45309';
      btnPars.style.fontWeight = '800';
    } else if (tipe === 'salah' && btnSalah) {
      btnSalah.style.border = '1.5px solid #ef4444';
      btnSalah.style.background = '#fef2f2';
      btnSalah.style.color = '#b91c1c';
      btnSalah.style.fontWeight = '800';
    }

    hitungHasilIshihara();
  }

  function setSemuaIshiharaNormal() {
    for (let i = 1; i <= 8; i++) {
      pilihIshiharaPlate(i, 'norm', '');
    }
  }

  function resetTesIshihara() {
    for (let i = 1; i <= 8; i++) {
      pilihIshiharaPlate(i, 'salah', '');
    }
  }

  function hitungHasilIshihara() {
    let benar = 0;
    let parsial = 0;
    let salah = 0;

    for (let i = 1; i <= 8; i++) {
      if (ishiharaState[i] === 'norm') benar++;
      else if (ishiharaState[i] === 'pars') parsial++;
      else salah++;
    }

    const skorEl = document.getElementById('live_ishihara_skor');
    const diagEl = document.getElementById('live_ishihara_diagnosa');

    if (skorEl) skorEl.innerText = `${benar} / 8`;

    if (!diagEl) return;

    if (benar >= 7) {
      diagEl.innerHTML = '<i class="bi bi-check-circle-fill"></i> Bebas Buta Warna (Normal)';
      diagEl.style.background = '#ecfdf5';
      diagEl.style.color = '#059669';
      diagEl.style.borderColor = '#a7f3d0';
    } else if (parsial > 0 || (benar >= 2 && benar <= 6)) {
      diagEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Terindikasi Buta Warna Parsial (Merah-Hijau)';
      diagEl.style.background = '#fffbeb';
      diagEl.style.color = '#b45309';
      diagEl.style.borderColor = '#fde68a';
    } else {
      diagEl.innerHTML = '<i class="bi bi-x-circle-fill"></i> Terindikasi Buta Warna Total';
      diagEl.style.background = '#fef2f2';
      diagEl.style.color = '#b91c1c';
      diagEl.style.borderColor = '#fca5a5';
    }
  }

  function terapkanHasilIshihara() {
    let benar = 0;
    let parsial = 0;
    for (let i = 1; i <= 8; i++) {
      if (ishiharaState[i] === 'norm') benar++;
      else if (ishiharaState[i] === 'pars') parsial++;
    }

    let kesimpulan = '';
    if (benar >= 7) {
      kesimpulan = `Lolos tes Ishihara: Bebas Buta Warna (${benar}/8 piringan terbaca normal). `;
    } else if (parsial > 0 || (benar >= 2 && benar <= 6)) {
      kesimpulan = `Perhatian: Terindikasi Buta Warna Parsial Merah-Hijau (${benar}/8 normal, ${parsial} parsial). `;
    } else {
      kesimpulan = `Perhatian: Terindikasi Buta Warna Total (${benar}/8 terbaca). `;
    }

    tambahCatatanCepat(kesimpulan);

    const kejuruanInput = document.getElementById('w_kejuruan');
    if (kejuruanInput && benar >= 7 && (kejuruanInput.value === '80' || !kejuruanInput.value)) {
      kejuruanInput.value = '85';
      hitungTotalWawancara();
    }

    tutupModalTesIshihara();
    alert('Hasil tes buta warna Ishihara berhasil disematkan ke formulir wawancara!');
  }
</script>
</body>
</html>
