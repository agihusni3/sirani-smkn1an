<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Siklus Akademik &amp; Transisi Siswa — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <style>
    /* ─── Custom Page Styling & Typography ─── */
    .page-hero {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: var(--r-lg);
      padding: 22px 24px;
      margin-bottom: 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
      box-shadow: var(--shadow-sm);
    }
    .page-hero-title {
      font-size: 22px;
      font-weight: 900;
      color: var(--text);
      letter-spacing: -0.02em;
      display: flex;
      align-items: center;
      gap: 10px;
      line-height: 1.2;
    }
    .page-hero-sub {
      font-size: 13px;
      color: var(--text-2);
      margin-top: 4px;
      font-weight: 500;
    }

    /* ─── Segmented Tab Switcher ─── */
    .segmented-control {
      display: inline-flex;
      background: var(--bg-3);
      padding: 4px;
      border-radius: var(--r-md);
      border: 1px solid var(--border-2);
      gap: 4px;
    }
    .segmented-btn {
      background: transparent;
      border: none;
      color: var(--text-2);
      padding: 8px 18px;
      border-radius: var(--r-sm);
      font-family: var(--font);
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all .2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .segmented-btn:hover {
      color: var(--text);
      background: rgba(255, 255, 255, 0.05);
    }
    .segmented-btn.active {
      background: linear-gradient(135deg, var(--gold), var(--gold-2));
      color: #0F172A;
      font-weight: 900;
      box-shadow: 0 2px 10px var(--gold-glow);
    }

    /* ─── Step Cards & Containers ─── */
    .step-card {
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      border-radius: var(--r-md);
      padding: 18px;
      transition: border-color .2s ease, box-shadow .2s ease;
    }
    .step-card:focus-within, .step-card:hover {
      border-color: rgba(202, 138, 4, 0.35);
    }
    .step-badge {
      width: 24px;
      height: 24px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--gold), var(--gold-2));
      color: #0F172A;
      font-size: 12px;
      font-weight: 900;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-right: 8px;
      flex-shrink: 0;
      box-shadow: 0 2px 6px rgba(202, 138, 4, 0.3);
    }
    .step-label {
      font-size: 13px;
      font-weight: 800;
      color: var(--text);
      display: flex;
      align-items: center;
      margin-bottom: 10px;
      letter-spacing: -0.01em;
    }

    /* ─── Tahun Ajaran Card ─── */
    .ta-active-box {
      background: linear-gradient(135deg, rgba(34,197,94,0.12) 0%, rgba(34,197,94,0.04) 100%);
      border: 1.5px solid rgba(34,197,94,0.3);
      padding: 16px 20px;
      border-radius: var(--r-md);
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 16px;
    }
    .ta-chip {
      background: var(--bg-2);
      border: 1.5px solid var(--border-2);
      border-radius: var(--r-sm);
      padding: 7px 14px;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: all .2s ease;
    }
    .ta-chip.is-active {
      border-color: rgba(34,197,94,0.5);
      background: rgba(34,197,94,0.08);
    }
    .ta-chip:hover {
      border-color: var(--gold);
    }

    /* ─── Typography Helpers ─── */
    .font-mono { font-family: var(--font-mono); }
    .label-uppercase {
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--text-3);
      font-family: var(--font-mono);
      margin-bottom: 8px;
    }
    .histori-pill {
      font-family: var(--font-mono);
      font-size: 11px;
      font-weight: 700;
      padding: 3px 9px;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }
  </style>
</head>
<body>
<div class="app-container">
  @include('partials.sidebar')
  <main class="main-content">
    
    {{-- PAGE HEADER HERO --}}
    <header class="page-hero">
      <div>
        <div class="page-hero-title">
          <i class="bi bi-arrow-repeat" style="color:var(--gold); font-size:24px;"></i>
          Siklus Akademik &amp; Transisi Siswa
        </div>
        <div class="page-hero-sub">
          Kelola kenaikan kelas massal, status kelulusan, penugasan PKL, dan riwayat mutasi akademik siswa secara aman.
        </div>
      </div>
      @include('partials.header_actions')
    </header>

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
      <div class="alert alert-success" style="margin-bottom:20px;">
        <i class="bi bi-check-circle-fill" style="margin-right:8px; font-size:16px;"></i>
        <span style="font-weight:700;">{{ session('success') }}</span>
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-error" style="margin-bottom:20px;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:8px; font-size:16px;"></i>
        <span style="font-weight:700;">{{ session('error') }}</span>
      </div>
    @endif

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- SECTION 1: PENGATURAN PERIODE TAHUN AJARAN -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="panel" style="margin-bottom:24px; padding:22px;">
      <div style="margin-bottom:18px; display:flex; justify-content:space-between; align-items:center;">
        <div style="font-size:15px; font-weight:900; color:var(--text); display:flex; align-items:center; gap:10px;">
          <i class="bi bi-calendar-range-fill" style="color:var(--gold); font-size:18px;"></i>
          <span>Pengaturan Periode Tahun Ajaran</span>
        </div>
      </div>

      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:20px;">
        <!-- Kolom Kiri: Status & Daftar Tahun Ajaran -->
        <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-md); padding:18px; display:flex; flex-direction:column; justify-content:space-between;">
          <div>
            <div class="label-uppercase">
              Tahun Ajaran Aktif Berjalan
            </div>
            @php $activeTa = $tahunAjarans->firstWhere('is_active', true); @endphp
            @if($activeTa)
              <div class="ta-active-box">
                <i class="bi bi-check-circle-fill" style="font-size:26px; color:var(--green); flex-shrink:0;"></i>
                <div>
                  <div class="font-mono" style="font-size:20px; font-weight:900; color:var(--text); letter-spacing:-0.02em;">
                    {{ $activeTa->nama }}
                  </div>
                  <div style="font-size:12px; color:var(--green); font-weight:700; margin-top:2px;">
                    ✓ Digunakan untuk seluruh absensi harian dan rekap aktif
                  </div>
                </div>
              </div>
            @else
              <div style="background:rgba(234,179,8,0.12); border:1.5px solid rgba(234,179,8,0.3); color:var(--gold); padding:14px; border-radius:var(--r-md); font-size:12.5px; font-weight:700; margin-bottom:16px;">
                ⚠️ Belum ada tahun ajaran yang diset aktif. Silakan pilih atau buat di bawah.
              </div>
            @endif

            <div class="label-uppercase" style="margin-top:6px;">
              Daftar Periode Lainnya:
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:8px;">
              @foreach($tahunAjarans as $ta)
                <div class="ta-chip {{ $ta->is_active ? 'is-active' : '' }}">
                  <strong class="font-mono" style="font-size:13px; color:var(--text); font-weight:800;">{{ $ta->nama }}</strong>
                  @if($ta->is_active)
                    <span style="background:var(--green); color:#fff; font-size:9.5px; font-weight:900; padding:2px 7px; border-radius:4px; letter-spacing:0.04em;">AKTIF</span>
                  @else
                    <form action="/tahun-ajaran/{{ $ta->id }}/aktifkan" method="POST" style="margin:0; display:inline;">
                      @csrf
                      <button type="submit" class="btn btn-outline" style="padding:3px 9px; font-size:11px; height:auto; border-color:var(--border-2); color:var(--text-2); font-weight:700;" title="Jadikan Tahun Ajaran Aktif">
                        Aktifkan
                      </button>
                    </form>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        </div>

        <!-- Kolom Kanan: Buat Tahun Ajaran Baru -->
        <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-md); padding:18px;">
          <div class="label-uppercase">
            + Tambah Periode Baru
          </div>
          <form action="/tahun-ajaran" method="POST">
            @csrf
            <div style="margin-bottom:14px;">
              <label style="font-size:12.5px; font-weight:800; color:var(--text-2); display:block; margin-bottom:6px;">Nama Tahun Ajaran</label>
              <input type="text" name="nama" required placeholder="Contoh: 2027/2028" class="input-field font-mono" style="width:100%; height:42px; font-size:13.5px; font-weight:700;" />
            </div>
            <div style="margin-bottom:18px;">
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:12.5px; font-weight:700; color:var(--text);">
                <input type="checkbox" name="is_active" value="1" style="width:18px; height:18px; cursor:pointer; accent-color:var(--gold);" /> 
                Jadikan langsung sebagai tahun ajaran aktif
              </label>
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%; height:42px; font-weight:900; font-size:13px; display:inline-flex; align-items:center; justify-content:center; gap:8px;">
              <i class="bi bi-plus-circle-fill"></i> Simpan Periode Baru
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- SECTION 2: PANEL AKSI TRANSISI AKADEMIK -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="panel" style="margin-bottom:24px; padding:22px;">
      <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:14px; margin-bottom:20px;">
        <div style="font-size:15px; font-weight:900; color:var(--text); display:flex; align-items:center; gap:10px;">
          <i class="bi bi-sliders" style="color:var(--gold); font-size:18px;"></i>
          <span>Aksi Transisi Akademik Siswa</span>
        </div>

        {{-- Tab Switcher --}}
        <div class="segmented-control">
          <button type="button" id="tabBtnMassal" class="segmented-btn active" onclick="switchTransisiTab('massal')">
            <i class="bi bi-collection-fill"></i> Aksi Massal per Kelas
          </button>
          <button type="button" id="tabBtnIndividu" class="segmented-btn" onclick="switchTransisiTab('individu')">
            <i class="bi bi-person-fill"></i> Aksi Perorangan
          </button>
        </div>
      </div>

      {{-- FORM 1: AKSI MASSAL PER KELAS / ROMBEL --}}
      <div id="panelFormMassal">
        <form action="/siklus-siswa/transisi-massal" method="POST" onsubmit="return confirmTransisiMassal()">
          @csrf
          
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:16px; margin-bottom:16px;">
            <!-- Langkah 1: Pilih Rombel Asal -->
            <div class="step-card">
              <label class="step-label">
                <span class="step-badge">1</span> Rombel / Kelas Asal <span style="color:var(--red); margin-left:4px;">*</span>
              </label>
              <select name="rombel_asal_id" id="massal_rombel_asal" required class="input-field" style="width:100%; height:44px; font-size:13px; font-weight:700;">
                <option value="">-- Pilih Rombel Asal --</option>
                @foreach($rombels as $r)
                  <option value="{{ $r->id }}">
                    {{ $r->nama_rombel }} (Tingkat {{ $r->tingkat }}) — [{{ $r->siswa_rombels_count ?? 0 }} Siswa]
                  </option>
                @endforeach
              </select>
              <div style="font-size:11.5px; color:var(--text-3); margin-top:6px; font-weight:500;">
                Seluruh siswa berstatus aktif di rombel ini akan diproses.
              </div>
            </div>

            <!-- Langkah 2: Jenis Tindakan -->
            <div class="step-card">
              <label class="step-label">
                <span class="step-badge">2</span> Jenis Tindakan Massal <span style="color:var(--red); margin-left:4px;">*</span>
              </label>
              <select name="aksi_massal" id="massal_aksi" required class="input-field" style="width:100%; height:44px; font-size:13px; font-weight:800; color:var(--gold);" onchange="toggleMassalFields()">
                <option value="naik_kelas">Naik Kelas Massal (Pindah ke Rombel Baru)</option>
                <option value="lulus">Kelulusan Massal (Nonaktifkan Akun Siswa)</option>
                <option value="tinggal_kelas">Tinggal Kelas Massal (Tahun Ajaran Baru)</option>
                <option value="mulai_pkl">Tugaskan PKL Massal (Bebas Evaluasi Alpha)</option>
                <option value="selesai_pkl">Selesai PKL Massal (Kembali Aktif di Sekolah)</option>
              </select>
              <div id="massal_aksi_desc" style="font-size:11.5px; color:var(--gold); margin-top:6px; font-weight:700;">
                Memindahkan rombel anggota ke tingkat/kelas lanjutan.
              </div>
            </div>
          </div>

          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:16px; margin-bottom:20px;">
            <!-- Langkah 3: Rombel Tujuan -->
            <div class="step-card" id="massal_group_tujuan">
              <label id="massal_label_tujuan" class="step-label">
                <span class="step-badge">3</span> Rombel / Kelas Tujuan
              </label>
              <select name="rombel_tujuan_id" id="massal_rombel_tujuan" class="input-field" style="width:100%; height:44px; font-size:13px; font-weight:700;">
                <option value="">-- Pilih Rombel Tujuan --</option>
                @foreach($rombels as $r)
                  <option value="{{ $r->id }}">{{ $r->nama_rombel }} (Tingkat {{ $r->tingkat }})</option>
                @endforeach
              </select>
              <div style="font-size:11.5px; color:var(--text-3); margin-top:6px; font-weight:500;">
                Rombel baru tempat siswa didaftarkan.
              </div>
            </div>

            <!-- Langkah 4: Tahun Ajaran Baru -->
            <div class="step-card" id="massal_group_ta">
              <label id="massal_label_ta" class="step-label">
                <span class="step-badge">4</span> Tahun Ajaran Target
              </label>
              <select name="tahun_ajaran_baru_id" id="massal_ta_baru" class="input-field font-mono" style="width:100%; height:44px; font-size:13px; font-weight:700;">
                @foreach($tahunAjarans as $ta)
                  <option value="{{ $ta->id }}" @if($ta->is_active) selected @endif>{{ $ta->nama }} @if($ta->is_active)(Aktif)@endif</option>
                @endforeach
              </select>
              <div style="font-size:11.5px; color:var(--text-3); margin-top:6px; font-weight:500;">
                Tahun ajaran baru yang akan terasosiasi.
              </div>
            </div>
          </div>

          <div style="display:flex; justify-content:flex-end;">
            <button type="submit" class="btn btn-gold" style="height:44px; padding:0 30px; display:inline-flex; align-items:center; gap:10px; font-size:13.5px; font-weight:900; letter-spacing:0.01em;">
              <i class="bi bi-lightning-charge-fill" style="font-size:16px;"></i> Eksekusi Transisi Massal
            </button>
          </div>
        </form>
      </div>

      {{-- FORM 2: AKSI PERORANGAN SISWA --}}
      <div id="panelFormIndividu" style="display:none;">
        <form action="/siklus-siswa/transisi" method="POST" id="formTransisi">
          @csrf
          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:16px; margin-bottom:16px;">
            <div class="step-card">
              <label class="step-label">
                <span class="step-badge">1</span> Pilih Siswa <span style="color:var(--red); margin-left:4px;">*</span>
              </label>
              <select name="siswa_id" id="select_siswa" required class="input-field" style="width:100%; height:44px; font-size:13px; font-weight:700;">
                <option value="">-- Cari / Pilih Siswa --</option>
                @foreach($allSiswas as $s)
                  @php $srAktif = $s->siswaRombels->firstWhere('status_keanggotaan', 'aktif'); @endphp
                  <option value="{{ $s->id }}" data-status="{{ $s->status }}">
                    {{ $s->nama }} ({{ $s->nis }}) — [{{ $srAktif->rombel->nama_rombel ?? 'Tanpa Rombel' }}] ({{ strtoupper($s->status) }})
                  </option>
                @endforeach
              </select>
            </div>

            <div class="step-card">
              <label class="step-label">
                <span class="step-badge">2</span> Jenis Transisi <span style="color:var(--red); margin-left:4px;">*</span>
              </label>
              <select name="jenis" id="select_jenis" required class="input-field" style="width:100%; height:44px; font-size:13px; font-weight:800; color:var(--gold);" onchange="toggleTransisiFields()">
                <option value="naik_kelas">Naik Kelas (Ke Rombel Baru)</option>
                <option value="tinggal_kelas">Tinggal Kelas (Tahun Ajaran Baru)</option>
                <option value="mulai_pkl">Mulai PKL (Bebas Absensi Sekolah)</option>
                <option value="selesai_pkl">Selesai PKL (Kembali Aktif di Sekolah)</option>
                <option value="lulus">Lulus (Nonaktifkan Akun Siswa)</option>
                <option value="pindah">Pindah Sekolah (Nonaktifkan Siswa)</option>
                <option value="keluar">Keluar / Dikeluarkan (Nonaktifkan Siswa)</option>
              </select>
            </div>
          </div>

          <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:16px; margin-bottom:20px;">
            <div class="step-card" id="group_rombel_tujuan">
              <label id="label_rombel_tujuan" class="step-label">
                <span class="step-badge">3</span> Rombel Tujuan
              </label>
              <select name="rombel_tujuan_id" id="select_rombel_tujuan" class="input-field" style="width:100%; height:44px; font-size:13px; font-weight:700;">
                <option value="">-- Pilih Rombel Tujuan --</option>
                @foreach($rombels as $r)<option value="{{ $r->id }}">{{ $r->nama_rombel }} (Tingkat {{ $r->tingkat }})</option>@endforeach
              </select>
            </div>

            <div class="step-card" id="group_ta_baru">
              <label id="label_ta_baru" class="step-label">
                <span class="step-badge">4</span> Tahun Ajaran Baru
              </label>
              <select name="tahun_ajaran_baru_id" id="select_ta_baru" class="input-field font-mono" style="width:100%; height:44px; font-size:13px; font-weight:700;">
                @foreach($tahunAjarans as $ta)<option value="{{ $ta->id }}" @if($ta->is_active) selected @endif>{{ $ta->nama }} @if($ta->is_active)(Aktif)@endif</option>@endforeach
              </select>
            </div>
          </div>

          <div style="display:flex; justify-content:flex-end;">
            <button type="submit" class="btn btn-gold" style="height:44px; padding:0 30px; display:inline-flex; align-items:center; gap:10px; font-size:13.5px; font-weight:900;">
              <i class="bi bi-check2-circle" style="font-size:18px;"></i> Simpan Transisi Siswa
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- SECTION 3: TOOLBAR PENCARIAN & FILTER -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="panel" style="padding:16px 20px; margin-bottom:20px;">
      <form method="GET" action="{{ route('siklus-siswa.index') }}" style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
        
        {{-- Search Box --}}
        <div style="flex:1.5; min-width:240px; position:relative;">
          <input type="text" name="q" value="{{ $search }}" placeholder="🔍 Cari nama siswa, NIS, NISN..." class="input-field" style="width:100%; height:42px; font-size:13px;" />
        </div>

        {{-- Filter Rombel --}}
        <div style="min-width:170px;">
          <select name="rombel_id" class="input-field" style="width:100%; height:42px; font-size:13px; font-weight:700;" onchange="this.form.submit()">
            <option value="">Semua Kelas / Rombel</option>
            @foreach($rombels as $r)
              <option value="{{ $r->id }}" {{ $rombelId == $r->id ? 'selected' : '' }}>{{ $r->nama_rombel }}</option>
            @endforeach
          </select>
        </div>

        {{-- Filter Status Siswa --}}
        <div style="min-width:150px;">
          <select name="status" class="input-field" style="width:100%; height:42px; font-size:13px; font-weight:700;" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="aktif" {{ $status === 'aktif' ? 'selected' : '' }}>🟢 Aktif</option>
            <option value="pkl" {{ $status === 'pkl' ? 'selected' : '' }}>🏭 Sedang PKL</option>
            <option value="lulus" {{ $status === 'lulus' ? 'selected' : '' }}>🎓 Lulus</option>
            <option value="pindah" {{ $status === 'pindah' ? 'selected' : '' }}>📦 Pindah</option>
            <option value="keluar" {{ $status === 'keluar' ? 'selected' : '' }}>❌ Keluar</option>
          </select>
        </div>

        {{-- Menu Sortir --}}
        <div style="min-width:150px;">
          <select name="sort" class="input-field" style="width:100%; height:42px; font-size:13px; font-weight:800;" onchange="this.form.submit()">
            <option value="nama_asc" {{ $sort === 'nama_asc' ? 'selected' : '' }}>↕️ Nama (A - Z)</option>
            <option value="nama_desc" {{ $sort === 'nama_desc' ? 'selected' : '' }}>↕️ Nama (Z - A)</option>
            <option value="nis_asc" {{ $sort === 'nis_asc' ? 'selected' : '' }}>↕️ NIS (Terkecil)</option>
            <option value="nis_desc" {{ $sort === 'nis_desc' ? 'selected' : '' }}>↕️ NIS (Terbesar)</option>
            <option value="terbaru" {{ $sort === 'terbaru' ? 'selected' : '' }}>↕️ Data Terbaru</option>
          </select>
        </div>

        <button type="submit" class="btn btn-outline" style="height:42px; font-weight:800; padding:0 18px;">
          <i class="bi bi-funnel-fill"></i> Filter
        </button>

        @if($search || $rombelId || $status || ($sort && $sort !== 'nama_asc'))
          <a href="{{ route('siklus-siswa.index') }}" class="btn btn-outline" style="height:42px; color:var(--red); border-color:rgba(239,68,68,0.4); font-weight:800;" title="Reset Filter">
            <i class="bi bi-x-circle"></i> Reset
          </a>
        @endif
      </form>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <!-- SECTION 4: TABEL HISTORI KEANGGOTAAN ROMBEL & STATUS SISWA -->
    <!-- ═══════════════════════════════════════════════════════════════════ -->
    <div class="panel" style="padding:0; overflow:hidden;">
      <div class="panel-title" style="padding:18px 22px; margin:0; border-bottom:1px solid var(--border-2); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:10px;">
          <i class="bi bi-people-fill" style="color:var(--gold); font-size:18px;"></i>
          <span style="font-size:15px; font-weight:900; color:var(--text);">Histori Keanggotaan Rombel &amp; Status Siswa</span>
          <span style="background:var(--bg-3); border:1px solid var(--border-2); color:var(--text-2); font-size:11.5px; font-weight:700; padding:3px 10px; border-radius:12px;" class="font-mono">
            {{ $siswas->firstItem() ?? 0 }} - {{ $siswas->lastItem() ?? 0 }} dari {{ $siswas->total() }} Siswa
          </span>
        </div>
        <div style="font-size:12.5px; color:var(--text-3); font-weight:600;">
          Halaman {{ $siswas->currentPage() }} dari {{ $siswas->lastPage() }}
        </div>
      </div>

      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th style="width:40px; text-align:center;">No</th>
              <th style="width:48px; text-align:center;">Foto</th>
              <th style="width:100px; text-align:center;">NIS</th>
              <th style="text-align:left;">Nama Lengkap Siswa</th>
              <th style="width:100px; text-align:center;">Status</th>
              <th style="width:120px; text-align:center;">Rombel Aktif</th>
              <th style="width:110px; text-align:center;">Tahun Ajaran</th>
              <th style="min-width:260px; text-align:left;">Histori Siklus Perjalanan Kelas</th>
              <th style="width:90px; text-align:center;" class="no-print">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($siswas as $idx => $s)
              @php
                $sr = $s->siswaRombels->firstWhere('status_keanggotaan', 'aktif');
              @endphp
              <tr>
                <td style="text-align:center; font-weight:700; color:var(--text-3); font-size:12px;">
                  {{ $siswas->firstItem() + $idx }}
                </td>
                <td style="text-align:center; vertical-align:middle;">
                  <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}" style="width:36px; height:36px; border-radius:50%; object-fit:cover; border:2px solid rgba(202,138,4,0.3);" />
                </td>
                <td style="text-align:center; font-family:var(--font-mono); font-weight:800; color:var(--gold); font-size:12.5px;">
                  {{ $s->nis }}
                </td>
                <td style="text-align:left;">
                  <strong style="color:var(--text); font-size:14px; font-weight:800;">{{ $s->nama }}</strong>
                  @if($s->nisn)
                    <div style="font-size:11px; color:var(--text-3); font-family:var(--font-mono); margin-top:2px;">NISN: {{ $s->nisn }}</div>
                  @endif
                </td>
                <td style="text-align:center;">
                  @if($s->status === 'aktif')
                    <span class="badge" style="background:rgba(34,197,94,0.12); color:#16A34A; font-weight:800; font-size:10.5px;">AKTIF</span>
                  @elseif($s->status === 'pkl')
                    <span class="badge" style="background:rgba(14,165,233,0.12); color:#0284C7; font-weight:800; font-size:10.5px;">PKL</span>
                  @elseif($s->status === 'lulus')
                    <span class="badge" style="background:rgba(59,130,246,0.12); color:#2563EB; font-weight:800; font-size:10.5px;">LULUS</span>
                  @elseif($s->status === 'pindah')
                    <span class="badge" style="background:rgba(234,179,8,0.15); color:#CA8A04; font-weight:800; font-size:10.5px;">PINDAH</span>
                  @else
                    <span class="badge" style="background:rgba(239,68,68,0.12); color:#DC2626; font-weight:800; font-size:10.5px;">{{ strtoupper($s->status) }}</span>
                  @endif
                </td>
                <td style="text-align:center; font-weight:800; color:var(--text); font-size:13px;">
                  {{ $sr->rombel->nama_rombel ?? '-' }}
                </td>
                <td style="text-align:center; font-family:var(--font-mono); font-size:12px; font-weight:700; color:var(--text-2);">
                  {{ $sr->tahunAjaran->nama ?? '-' }}
                </td>
                <td style="text-align:left;">
                  <div style="display:flex; flex-wrap:wrap; gap:6px;">
                    @forelse($s->siswaRombels as $hist)
                      @php
                        $histStyle = match($hist->status_keanggotaan) {
                          'aktif' => 'background:rgba(234,179,8,0.15); color:#CA8A04; border:1px solid rgba(234,179,8,0.3); font-weight:800;',
                          'naik' => 'background:rgba(34,197,94,0.12); color:#16A34A; border:1px solid rgba(34,197,94,0.25); font-weight:700;',
                          'tinggal' => 'background:rgba(239,68,68,0.12); color:#DC2626; border:1px solid rgba(239,68,68,0.25); font-weight:700;',
                          'lulus' => 'background:rgba(59,130,246,0.12); color:#2563EB; border:1px solid rgba(59,130,246,0.25); font-weight:700;',
                          default => 'background:var(--bg-3); color:var(--text-3); border:1px solid var(--border-2);',
                        };
                      @endphp
                      <span class="histori-pill" style="{{ $histStyle }}">
                        {{ $hist->rombel->nama_rombel ?? 'Rombel' }} ({{ ucfirst($hist->status_keanggotaan) }})
                      </span>
                    @empty
                      <span style="color:var(--text-3); font-size:12px;">-</span>
                    @endforelse
                  </div>
                </td>
                <td style="text-align:center;" class="no-print">
                  <div style="display:flex; gap:6px; justify-content:center;">
                    <button type="button" class="btn btn-sm btn-outline" onclick="pilihSiswaTransisi({{ $s->id }})" style="font-size:11px; padding:4px 9px; font-weight:800; color:var(--gold); border-color:rgba(202,138,4,0.4);" title="Proses Transisi Siswa Ini">
                      <i class="bi bi-arrow-left-right"></i>
                    </button>
                    @if($s->nis)
                      <a href="/presensi-siswa/{{ $s->nis }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; padding:4px 9px; font-weight:800; color:var(--navy); border-color:rgba(59,130,246,0.4);" title="Portal Rekap Siswa">
                        <i class="bi bi-person-lines-fill"></i>
                      </a>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" style="text-align:center; padding:40px; color:var(--text-3);">
                  <i class="bi bi-inbox" style="font-size:32px; display:block; margin-bottom:10px; opacity:.5;"></i>
                  <span style="font-size:13.5px; font-weight:600;">Tidak ada data siswa ditemukan untuk kriteria pencarian ini.</span>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($siswas->hasPages())
        <div style="padding:16px 22px; border-top:1px solid var(--border-2); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
          <div style="font-size:12.5px; color:var(--text-3); font-weight:600;">
            Menampilkan {{ $siswas->firstItem() }} s/d {{ $siswas->lastItem() }} dari {{ $siswas->total() }} data
          </div>
          <div>
            {{ $siswas->links() }}
          </div>
        </div>
      @endif
    </div>
  </main>
</div>

<script>
  function switchTransisiTab(mode) {
    const btnMassal = document.getElementById('tabBtnMassal');
    const btnIndividu = document.getElementById('tabBtnIndividu');
    const panelMassal = document.getElementById('panelFormMassal');
    const panelIndividu = document.getElementById('panelFormIndividu');

    if (mode === 'massal') {
      btnMassal.classList.add('active');
      btnIndividu.classList.remove('active');
      panelMassal.style.display = 'block';
      panelIndividu.style.display = 'none';
    } else {
      btnIndividu.classList.add('active');
      btnMassal.classList.remove('active');
      panelIndividu.style.display = 'block';
      panelMassal.style.display = 'none';
    }
  }

  function toggleMassalFields() {
    const aksi = document.getElementById('massal_aksi').value;
    const selectTujuan = document.getElementById('massal_rombel_tujuan');
    const selectTa = document.getElementById('massal_ta_baru');
    const labelTujuan = document.getElementById('massal_label_tujuan');
    const labelTa = document.getElementById('massal_label_ta');
    const desc = document.getElementById('massal_aksi_desc');

    if (aksi === 'naik_kelas') {
      desc.textContent = 'Memindahkan rombel anggota ke tingkat/kelas lanjutan.';
    } else if (aksi === 'lulus') {
      desc.textContent = 'Menetapkan kelulusan dan otomatis menonaktifkan status siswa.';
    } else if (aksi === 'tinggal_kelas') {
      desc.textContent = 'Mempertahankan tingkat kelas siswa di tahun ajaran baru.';
    } else if (aksi === 'mulai_pkl') {
      desc.textContent = 'Menugaskan PKL (bebas dari evaluasi alpha otomatis gerbang sekolah).';
    } else if (aksi === 'selesai_pkl') {
      desc.textContent = 'Menyelesaikan penugasan PKL (kembali aktif absensi di sekolah).';
    }

    if (['lulus', 'mulai_pkl', 'selesai_pkl'].includes(aksi)) {
      selectTujuan.disabled = true;
      selectTa.disabled = true;
      selectTujuan.style.opacity = '0.4';
      selectTa.style.opacity = '0.4';
      labelTujuan.innerHTML = '<span class="step-badge">3</span> Rombel Tujuan <span style="color:var(--text-3); font-weight:400; font-size:11px;">(Tidak diperlukan)</span>';
      labelTa.innerHTML = '<span class="step-badge">4</span> Tahun Ajaran <span style="color:var(--text-3); font-weight:400; font-size:11px;">(Tidak diperlukan)</span>';
    } else {
      selectTujuan.disabled = false;
      selectTa.disabled = false;
      selectTujuan.style.opacity = '1';
      selectTa.style.opacity = '1';
      labelTujuan.innerHTML = '<span class="step-badge">3</span> Rombel Tujuan';
      labelTa.innerHTML = '<span class="step-badge">4</span> Tahun Ajaran Target';
    }
  }

  function confirmTransisiMassal() {
    const rombelSelect = document.getElementById('massal_rombel_asal');
    const rombelText = rombelSelect.options[rombelSelect.selectedIndex]?.text || '';
    const aksi = document.getElementById('massal_aksi').value;

    let aksiName = 'Naik Kelas';
    if (aksi === 'lulus') aksiName = 'Kelulusan Massal (Nonaktifkan Siswa)';
    if (aksi === 'tinggal_kelas') aksiName = 'Tinggal Kelas Massal';
    if (aksi === 'mulai_pkl') aksiName = 'Penugasan PKL Massal (Bebas Absensi)';
    if (aksi === 'selesai_pkl') aksiName = 'Selesai PKL Massal (Kembali Aktif)';

    return confirm(`Apakah Anda yakin ingin memproses ${aksiName} secara MASSAL untuk seluruh siswa di [${rombelText}]?`);
  }

  function toggleTransisiFields() {
    const jenis = document.getElementById('select_jenis').value;
    const rombelTujuan = document.getElementById('select_rombel_tujuan');
    const taBaru = document.getElementById('select_ta_baru');
    const labelRombel = document.getElementById('label_rombel_tujuan');
    const labelTa = document.getElementById('label_ta_baru');

    const isNoTarget = ['lulus', 'pindah', 'keluar', 'mulai_pkl', 'selesai_pkl'].includes(jenis);

    if (isNoTarget) {
      rombelTujuan.disabled = true;
      taBaru.disabled = true;
      rombelTujuan.style.opacity = '0.4';
      taBaru.style.opacity = '0.4';
      labelRombel.innerHTML = '<span class="step-badge">3</span> Rombel Tujuan <span style="color:var(--text-3); font-weight:400; font-size:11px;">(Tidak diperlukan)</span>';
      labelTa.innerHTML = '<span class="step-badge">4</span> Tahun Ajaran <span style="color:var(--text-3); font-weight:400; font-size:11px;">(Tidak diperlukan)</span>';
    } else {
      rombelTujuan.disabled = false;
      taBaru.disabled = false;
      rombelTujuan.style.opacity = '1';
      taBaru.style.opacity = '1';
      labelRombel.innerHTML = '<span class="step-badge">3</span> Rombel Tujuan';
      labelTa.innerHTML = '<span class="step-badge">4</span> Tahun Ajaran Baru';
    }
  }

  function pilihSiswaTransisi(siswaId) {
    switchTransisiTab('individu');
    const select = document.getElementById('select_siswa');
    if (select) {
      select.value = siswaId;
      select.scrollIntoView({ behavior: 'smooth', block: 'center' });
      select.focus();
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    toggleTransisiFields();
    toggleMassalFields();
  });
</script>
</body>
</html>
