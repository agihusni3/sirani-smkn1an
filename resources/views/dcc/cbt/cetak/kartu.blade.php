<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cetak Kartu Peserta Ujian CBT — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/dcc-cbt.css') }}?v={{ filemtime(public_path('css/dcc-cbt.css')) }}">

  <style>
    @media print {
      body {
        background: #ffffff !important;
      }
      .cbt-navbar-top, .cbt-subbar, .no-print {
        display: none !important;
      }
      .cbt-container {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
      }
      .kartu-grid {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 12px !important;
      }
      .kartu-box {
        page-break-inside: avoid !important;
        border: 1.5px solid #000000 !important;
        box-shadow: none !important;
      }
    }

    .kartu-box {
      border: 1.5px solid #cbd5e1;
      border-radius: 10px;
      padding: 16px;
      background: #ffffff;
      box-sizing: border-box;
      position: relative;
    }
  </style>
</head>
<body class="cbt-app-body">

  @include('dcc.cbt.partials.cbt_header')

  <main class="cbt-container">

    <div class="no-print" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
      <div>
        <h1 style="margin:0 0 4px; font-size:22px; font-weight:900; color:#0f172a;">
          <i class="bi bi-person-badge-fill text-primary me-2"></i> Cetak Kartu Peserta Ujian CBT
        </h1>
        <p style="margin:0; font-size:13.5px; color:#64748b;">
          Cetak kartu login ujian siswa per rombel lengkap dengan NISN dan identitas sekolah.
        </p>
      </div>

      <div style="display:flex; gap:12px; align-items:center;">
        <form method="GET" action="{{ route('admin.cbt.cetak.kartu') }}" style="display:flex; gap:8px;">
          <select name="rombel_id" class="form-select" onchange="this.form.submit()" style="font-size:13px; border-radius:8px; width:220px;">
            @foreach($rombels as $r)
              <option value="{{ $r->id }}" {{ $selectedRombelId == $r->id ? 'selected' : '' }}>
                {{ $r->nama_rombel }}
              </option>
            @endforeach
          </select>
        </form>

        <button type="button" class="btn btn-primary" onclick="window.print()" style="font-weight:700; font-size:13px; border-radius:8px; padding:8px 18px;">
          <i class="bi bi-printer-fill me-1"></i> Cetak Semua Kartu
        </button>
      </div>
    </div>

    @if(empty($siswas) || count($siswas) === 0)
      <div class="no-print" style="background:#ffffff; border:1px dashed #cbd5e1; border-radius:12px; padding:48px 20px; text-align:center; color:#94a3b8;">
        <i class="bi bi-people" style="font-size:40px; display:block; margin-bottom:10px; color:#cbd5e1;"></i>
        Tidak ada siswa pada rombel yang dipilih.
      </div>
    @else
      <div class="kartu-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(360px, 1fr)); gap:16px;">
        @foreach($siswas as $s)
          <div class="kartu-box">
            <!-- Kop Kartu -->
            <div style="display:flex; align-items:center; gap:10px; border-bottom:1.5px solid #0f172a; padding-bottom:8px; margin-bottom:10px;">
              <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width:36px; height:36px; object-fit:contain;" onerror="this.src='{{ asset('apple-touch-icon.png') }}'" />
              <div style="flex-grow:1; text-align:center;">
                <div style="font-size:11px; font-weight:800; color:#0f172a; text-transform:uppercase; line-height:1.2;">
                  KARTU PESERTA ASESMEN / UJIAN CBT
                </div>
                <div style="font-size:12px; font-weight:900; color:#0072bc; line-height:1.2;">
                  SMK NEGERI 1 AIR NANINGAN
                </div>
                <div style="font-size:9.5px; color:#64748b;">
                  Tanggamus - Lampung | Portal: smkn1airnaningan.sch.id
                </div>
              </div>
            </div>

            <!-- Identitas Siswa -->
            <div style="display:flex; gap:12px; align-items:flex-start;">
              <div style="width:64px; height:80px; border:1px solid #cbd5e1; border-radius:6px; background:#f8fafc; display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
                @if($s->foto)
                  <img src="{{ asset($s->foto) }}" alt="Foto" style="width:100%; height:100%; object-fit:cover;" />
                @else
                  <span style="font-size:10px; color:#94a3b8; font-weight:700;">2x3</span>
                @endif
              </div>

              <div style="flex-grow:1; font-size:12px; line-height:1.6; color:#1e293b;">
                <div style="display:grid; grid-template-columns:85px 1fr;">
                  <span style="color:#64748b;">Nama Siswa</span>
                  <span style="font-weight:800; color:#0f172a;">: {{ Str::limit($s->nama_siswa, 24) }}</span>
                </div>
                <div style="display:grid; grid-template-columns:85px 1fr;">
                  <span style="color:#64748b;">NISN (User)</span>
                  <span style="font-weight:900; font-family:monospace; color:#0072bc;">: {{ $s->nisn }}</span>
                </div>
                <div style="display:grid; grid-template-columns:85px 1fr;">
                  <span style="color:#64748b;">Kelas/Rombel</span>
                  <span style="font-weight:700;">: {{ $s->rombel->nama_rombel ?? '-' }}</span>
                </div>
                <div style="display:grid; grid-template-columns:85px 1fr;">
                  <span style="color:#64748b;">Password</span>
                  <span style="font-family:monospace; font-weight:700;">: {{ $s->password_cbt ?: '(Sesuai NISN)' }}</span>
                </div>
              </div>
            </div>

            <div style="border-top:1px dashed #cbd5e1; margin-top:10px; padding-top:6px; font-size:10px; color:#64748b; display:flex; justify-content:space-between; align-items:center;">
              <span>Simpan kartu ini selama masa ujian.</span>
              <span style="font-weight:700; color:#0f172a;">Panitia Asesmen</span>
            </div>
          </div>
        @endforeach
      </div>
    @endif

  </main>

</body>
</html>
