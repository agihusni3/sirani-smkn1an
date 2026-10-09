<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SITUAN — Administrasi Rapat &amp; Notula Dinas</title>
  
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
              <a href="{{ route('situan.index') }}" style="display:inline-flex; align-items:center; gap:4px;"><i class="bi bi-buildings" style="color:#0284c7; font-size:12px;"></i> SITUAN</a>
              <span class="sep">/</span>
              <span>Tata Persuratan</span>
              <span class="sep">/</span>
              <span style="color:#0284c7; font-weight:800;">Administrasi Rapat &amp; Notula</span>
            </div>
            <h1 class="situan-page-title" style="margin-top:2px; font-size:20px;">Administrasi Rapat &amp; Notula Dinas</h1>
          </div>
        </div>
        
        <div class="situan-topbar-actions" style="display:flex; align-items:center; gap:8px;">
          <button type="button" class="btn btn-sm btn-primary" onclick="toggleModalRapat(true)" style="height:36px; padding:0 16px; font-size:12.5px; font-weight:800; display:inline-flex; align-items:center; gap:6px; border-radius:8px; background:#0284c7; color:#fff; border:none; cursor:pointer; box-shadow:0 2px 8px rgba(2,132,199,0.25);">
            <i class="bi bi-plus-circle-fill"></i>
            <span>+ Buat Agenda Rapat</span>
          </button>
          @include('partials.header_actions')
        </div>
      </div>

      {{-- Flash Message --}}
      @if(session('success'))
        <div class="alert-success" style="margin-bottom:14px; background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:10px 16px; border-radius:8px; font-size:13px; font-weight:700; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-check-circle-fill"></i>
          <span>{{ session('success') }}</span>
        </div>
      @endif

      {{-- KPI Cards --}}
      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(210px, 1fr)); gap:14px; margin-bottom:18px;">
        <div class="kpi-mini-card" style="background:#ffffff; border:1px solid var(--border); border-radius:10px; padding:14px 16px; display:flex; align-items:center; gap:14px; box-shadow:var(--shadow-sm);">
          <div style="width:42px; height:42px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:20px;">
            <i class="bi bi-calendar-check-fill"></i>
          </div>
          <div>
            <div style="font-size:20px; font-weight:900; color:var(--text); line-height:1.1;">{{ $totalRapat }}</div>
            <div style="font-size:11.5px; color:var(--text-3); font-weight:600; margin-top:2px;">Total Agenda Rapat</div>
          </div>
        </div>

        <div class="kpi-mini-card" style="background:#ffffff; border:1px solid var(--border); border-radius:10px; padding:14px 16px; display:flex; align-items:center; gap:14px; box-shadow:var(--shadow-sm);">
          <div style="width:42px; height:42px; border-radius:10px; background:#f0fdf4; color:#16a34a; display:flex; align-items:center; justify-content:center; font-size:20px;">
            <i class="bi bi-calendar-event-fill"></i>
          </div>
          <div>
            <div style="font-size:20px; font-weight:900; color:#15803d; line-height:1.1;">{{ $rapatBulanIni }}</div>
            <div style="font-size:11.5px; color:var(--text-3); font-weight:600; margin-top:2px;">Rapat Bulan Ini</div>
          </div>
        </div>

        <div class="kpi-mini-card" style="background:#ffffff; border:1px solid var(--border); border-radius:10px; padding:14px 16px; display:flex; align-items:center; gap:14px; box-shadow:var(--shadow-sm);">
          <div style="width:42px; height:42px; border-radius:10px; background:#fef3c7; color:#d97706; display:flex; align-items:center; justify-content:center; font-size:20px;">
            <i class="bi bi-clock-history"></i>
          </div>
          <div>
            <div style="font-size:20px; font-weight:900; color:#b45309; line-height:1.1;">{{ $rapatDijadwalkan }}</div>
            <div style="font-size:11.5px; color:var(--text-3); font-weight:600; margin-top:2px;">Rapat Terjadwal</div>
          </div>
        </div>

        <div class="kpi-mini-card" style="background:#ffffff; border:1px solid var(--border); border-radius:10px; padding:14px 16px; display:flex; align-items:center; gap:14px; box-shadow:var(--shadow-sm);">
          <div style="width:42px; height:42px; border-radius:10px; background:#f5f3ff; color:#7c3aed; display:flex; align-items:center; justify-content:center; font-size:20px;">
            <i class="bi bi-file-earmark-text-fill"></i>
          </div>
          <div>
            <div style="font-size:20px; font-weight:900; color:#6d28d9; line-height:1.1;">{{ $rapatSelesai }}</div>
            <div style="font-size:11.5px; color:var(--text-3); font-weight:600; margin-top:2px;">Notula Tuntas Disimpan</div>
          </div>
        </div>
      </div>

      {{-- Filter & Search Bar --}}
      <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); border-radius:10px; padding:14px 18px; margin-bottom:16px; box-shadow:var(--shadow-sm);">
        <form method="GET" action="{{ route('situan.rapat.index') }}" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin:0;">
          <div style="display:flex; align-items:center; gap:8px; flex:1; min-width:280px; flex-wrap:wrap;">
            <div style="position:relative; flex:1; min-width:200px;">
              <i class="bi bi-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-3); font-size:13px;"></i>
              <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul rapat, agenda, atau pimpinan..." style="width:100%; height:36px; padding-left:32px; padding-right:10px; border-radius:6px; border:1px solid var(--border-2); font-size:12.5px; background:var(--bg-3); color:var(--text);" />
            </div>

            <select name="tipe" onchange="this.form.submit()" style="height:36px; padding:0 10px; border-radius:6px; border:1px solid var(--border-2); font-size:12.5px; background:var(--bg-3); color:var(--text);">
              <option value="semua">Semua Tipe Rapat</option>
              <option value="dinas_guru" {{ request('tipe') === 'dinas_guru' ? 'selected' : '' }}>Rapat Dinas Guru &amp; Tendik</option>
              <option value="pleno_kelulusan" {{ request('tipe') === 'pleno_kelulusan' ? 'selected' : '' }}>Pleno Kelulusan Siswa</option>
              <option value="pleno_kenaikan" {{ request('tipe') === 'pleno_kenaikan' ? 'selected' : '' }}>Pleno Kenaikan Kelas</option>
              <option value="kurikulum_kosp" {{ request('tipe') === 'kurikulum_kosp' ? 'selected' : '' }}>Kurikulum &amp; KOSP</option>
              <option value="komite_ortu" {{ request('tipe') === 'komite_ortu' ? 'selected' : '' }}>Komite &amp; Orang Tua</option>
              <option value="kepanitiaan" {{ request('tipe') === 'kepanitiaan' ? 'selected' : '' }}>Koordinasi Kepanitiaan</option>
              <option value="evaluasi_bulanan" {{ request('tipe') === 'evaluasi_bulanan' ? 'selected' : '' }}>Evaluasi Bulanan</option>
            </select>

            <select name="status" onchange="this.form.submit()" style="height:36px; padding:0 10px; border-radius:6px; border:1px solid var(--border-2); font-size:12.5px; background:var(--bg-3); color:var(--text);">
              <option value="semua">Semua Status</option>
              <option value="dijadwalkan" {{ request('status') === 'dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
              <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
              <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
          </div>

          <div style="display:flex; align-items:center; gap:8px;">
            <button type="submit" class="btn btn-sm btn-outline" style="height:36px; padding:0 14px; font-size:12px; font-weight:700; border-radius:6px;">
              <i class="bi bi-funnel"></i> Terapkan Filter
            </button>
            @if(request()->hasAny(['q', 'tipe', 'status', 'bulan']))
              <a href="{{ route('situan.rapat.index') }}" class="btn btn-sm btn-outline" style="height:36px; padding:0 12px; font-size:12px; border-radius:6px; color:#ef4444;" title="Reset Filter">
                <i class="bi bi-x-circle"></i> Reset
              </a>
            @endif
          </div>
        </form>
      </div>

      {{-- Table Card --}}
      <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); border-radius:10px; overflow:hidden; box-shadow:var(--shadow-sm); margin-bottom:20px;">
        <div style="overflow-x:auto;">
          <table style="width:100%; border-collapse:collapse; text-align:left; font-size:12.5px;">
            <thead>
              <tr style="background:var(--bg-3); border-bottom:1.5px solid var(--border); color:var(--text); font-weight:800; font-size:11.5px; text-transform:uppercase; letter-spacing:0.3px;">
                <th style="padding:12px 14px; width:45px; text-align:center;">No</th>
                <th style="padding:12px 14px;">Waktu &amp; Ruangan</th>
                <th style="padding:12px 14px;">Agenda &amp; Judul Rapat</th>
                <th style="padding:12px 14px;">Pimpinan &amp; Notulis</th>
                <th style="padding:12px 14px; text-align:center;">Status</th>
                <th style="padding:12px 14px; text-align:right;">Paket Administrasi &amp; Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($rapats as $index => $r)
                <tr style="border-bottom:1px solid var(--border); transition:background .15s;" onmouseover="this.style.background='var(--bg-3)'" onmouseout="this.style.background='transparent'">
                  <td style="padding:12px 14px; text-align:center; font-family:var(--font-mono); font-weight:700; color:var(--text-3);">
                    {{ $rapats->firstItem() + $index }}
                  </td>
                  
                  {{-- Waktu & Ruangan --}}
                  <td style="padding:12px 14px; white-space:nowrap;">
                    <div style="font-weight:800; color:var(--text);">
                      <i class="bi bi-calendar3" style="color:#0284c7; margin-right:4px;"></i>
                      {{ $r->tanggal_rapat->locale('id')->isoFormat('D MMMM Y') }}
                    </div>
                    <div style="font-size:11.5px; color:var(--text-3); font-family:var(--font-mono); margin-top:2px;">
                      <i class="bi bi-clock"></i> {{ substr($r->jam_mulai, 0, 5) }} s/d {{ $r->jam_selesai ? substr($r->jam_selesai, 0, 5) : 'Selesai' }} WIB
                    </div>
                    <div style="font-size:11px; color:#64748b; margin-top:1px;">
                      <i class="bi bi-geo-alt-fill text-danger"></i> {{ $r->tempat }}
                    </div>
                  </td>

                  {{-- Judul & Agenda --}}
                  <td style="padding:12px 14px; min-width:260px;">
                    <div style="margin-bottom:3px;">
                      <span class="badge" style="background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; font-size:10px; font-weight:800; padding:2px 7px; border-radius:4px;">
                        {{ $r->tipe_label }}
                      </span>
                      @if($r->nomor_surat)
                        <span style="font-size:11px; font-family:var(--font-mono); color:var(--text-3); margin-left:6px;">
                          No: {{ $r->nomor_surat }}
                        </span>
                      @endif
                    </div>
                    <div style="font-size:13.5px; font-weight:800; color:var(--text);">
                      <a href="{{ route('situan.rapat.show', $r->id) }}" style="color:inherit; text-decoration:none;" onmouseover="this.style.color='#0284c7'" onmouseout="this.style.color='inherit'">
                        {{ $r->judul_rapat }}
                      </a>
                    </div>
                    @if($r->agenda)
                      <div style="font-size:11.5px; color:var(--text-3); margin-top:2px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                        {{ $r->agenda }}
                      </div>
                    @endif
                  </td>

                  {{-- Pimpinan & Notulis --}}
                  <td style="padding:12px 14px; white-space:nowrap;">
                    <div style="font-size:12px;">
                      <span style="font-size:10.5px; font-weight:700; color:var(--text-3); text-transform:uppercase;">Pimpinan:</span><br>
                      <strong style="color:var(--text);">{{ $r->pimpinan_nama ?: ($r->pimpinan->nama ?? '-') }}</strong>
                    </div>
                    <div style="font-size:11.5px; color:var(--text-3); margin-top:3px;">
                      <span style="font-size:10.5px; font-weight:700; color:var(--text-3); text-transform:uppercase;">Notulis:</span>
                      <span>{{ $r->notulis_nama ?: ($r->notulis->nama ?? 'Belum Ditunjuk') }}</span>
                    </div>
                  </td>

                  {{-- Status --}}
                  <td style="padding:12px 14px; text-align:center;">
                    {!! $r->status_badge !!}
                  </td>

                  {{-- Aksi --}}
                  <td style="padding:12px 14px; text-align:right; white-space:nowrap;">
                    <div style="display:inline-flex; align-items:center; gap:5px;">
                      {{-- Tombol Utama: Buka Detail & Notula --}}
                      <a href="{{ route('situan.rapat.show', $r->id) }}" class="btn btn-sm btn-outline" style="height:30px; padding:0 10px; font-size:11.5px; font-weight:700; border-radius:6px; display:inline-flex; align-items:center; gap:4px;" title="Buka Detail &amp; Input Notula Rapat">
                        <i class="bi bi-pencil-square text-primary"></i> Notula
                      </a>

                      {{-- Tombol Cetak 1-Klik Paket Lengkap --}}
                      <a href="{{ route('situan.rapat.cetak-paket', $r->id) }}" target="_blank" class="btn btn-sm" style="height:30px; padding:0 10px; font-size:11.5px; font-weight:800; border-radius:6px; background:#16a34a; color:#fff; text-decoration:none; display:inline-flex; align-items:center; gap:4px; box-shadow:0 1px 4px rgba(22,163,74,0.25);" title="Cetak 1-Klik Paket Lengkap (Undangan + Hadir + Notula + Berita Acara)">
                        <i class="bi bi-printer-fill"></i> Paket A4
                      </a>

                      {{-- Dropdown Cetak Spesifik --}}
                      <div class="dropdown-cetak-wrapper" style="position:relative; display:inline-block;">
                        <button type="button" class="btn btn-sm btn-outline" onclick="toggleDropdownCetak({{ $r->id }})" style="height:30px; padding:0 8px; font-size:11.5px; border-radius:6px;" title="Pilih Lembar Dokumen Spesifik">
                          <i class="bi bi-chevron-down"></i>
                        </button>
                        <div id="dropdownCetak{{ $r->id }}" style="display:none; position:absolute; right:0; top:34px; background:#ffffff; border:1px solid var(--border); border-radius:8px; box-shadow:0 10px 25px rgba(0,0,0,0.15); width:180px; z-index:99; padding:5px 0; text-align:left;">
                          <a href="{{ route('situan.rapat.cetak-undangan', $r->id) }}" target="_blank" style="display:flex; align-items:center; gap:8px; padding:7px 12px; font-size:12px; color:var(--text); text-decoration:none;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                            <i class="bi bi-envelope-paper text-primary"></i> 1. Undangan Rapat
                          </a>
                          <a href="{{ route('situan.rapat.cetak-daftar-hadir', $r->id) }}" target="_blank" style="display:flex; align-items:center; gap:8px; padding:7px 12px; font-size:12px; color:var(--text); text-decoration:none;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                            <i class="bi bi-card-checklist text-success"></i> 2. Daftar Hadir
                          </a>
                          <a href="{{ route('situan.rapat.cetak-notula', $r->id) }}" target="_blank" style="display:flex; align-items:center; gap:8px; padding:7px 12px; font-size:12px; color:var(--text); text-decoration:none;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                            <i class="bi bi-journal-text text-warning"></i> 3. Notula Rapat
                          </a>
                          <a href="{{ route('situan.rapat.cetak-berita-acara', $r->id) }}" target="_blank" style="display:flex; align-items:center; gap:8px; padding:7px 12px; font-size:12px; color:var(--text); text-decoration:none;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                            <i class="bi bi-file-earmark-ruled text-danger"></i> 4. Berita Acara
                          </a>
                        </div>
                      </div>

                      {{-- Hapus --}}
                      <form action="{{ route('situan.rapat.destroy', $r->id) }}" method="POST" style="margin:0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus agenda rapat ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline" style="height:30px; padding:0 8px; border-radius:6px; color:#ef4444;" title="Hapus Agenda Rapat">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" style="text-align:center; padding:40px 20px; color:var(--text-3);">
                    <div style="font-size:36px; color:#cbd5e1; margin-bottom:8px;"><i class="bi bi-calendar-x"></i></div>
                    <div style="font-weight:700; font-size:14px; color:var(--text);">Belum ada agenda rapat yang tercatat</div>
                    <div style="font-size:12px; margin-top:2px;">Klik tombol <strong>+ Buat Agenda Rapat</strong> di atas untuk membuat surat undangan &amp; lembar presensi rapat baru.</div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Pagination --}}
        @if($rapats->hasPages())
          <div style="padding:12px 18px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
            <div style="font-size:12px; color:var(--text-3);">
              Menampilkan {{ $rapats->firstItem() }} s/d {{ $rapats->lastItem() }} dari {{ $rapats->total() }} agenda rapat
            </div>
            <div>
              {{ $rapats->links() }}
            </div>
          </div>
        @endif
      </div>

    </div>
  </main>
</div>

{{-- MODAL BUAT AGENDA RAPAT BARU --}}
<div id="modalBuatRapat" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:9999; backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:16px;">
  <div style="background:#ffffff; border-radius:12px; width:100%; max-width:680px; max-height:90vh; overflow-y:auto; box-shadow:0 25px 60px rgba(0,0,0,0.3); border:1px solid var(--border);">
    <div style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; background:#f8fafc; border-top-left-radius:12px; border-top-right-radius:12px;">
      <div>
        <h3 style="margin:0; font-size:16px; font-weight:800; color:#0f172a;">Buat Agenda Rapat &amp; Paket Administrasi Baru</h3>
        <div style="font-size:11.5px; color:#64748b; margin-top:2px;">Otomatis membuat Undangan Resmi, Daftar Hadir (Presensi), dan Lembar Notula</div>
      </div>
      <button type="button" onclick="toggleModalRapat(false)" style="background:transparent; border:none; font-size:18px; color:#64748b; cursor:pointer;"><i class="bi bi-x-lg"></i></button>
    </div>

    <form action="{{ route('situan.rapat.store') }}" method="POST" style="padding:20px;">
      @csrf

      <div style="margin-bottom:14px;">
        <label style="display:block; font-size:12px; font-weight:800; color:#334155; margin-bottom:4px;">Judul / Perihal Rapat <span style="color:#ef4444;">*</span></label>
        <input type="text" name="judul_rapat" required placeholder="Contoh: Rapat Dinas Awal Tahun Ajaran 2026/2027 &amp; Pembagian Tugas" style="width:100%; height:38px; border-radius:6px; border:1px solid #cbd5e1; padding:0 10px; font-size:13px;" />
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
        <div>
          <label style="display:block; font-size:12px; font-weight:800; color:#334155; margin-bottom:4px;">Tipe / Klasifikasi Rapat <span style="color:#ef4444;">*</span></label>
          <select name="tipe_rapat" required style="width:100%; height:38px; border-radius:6px; border:1px solid #cbd5e1; padding:0 10px; font-size:13px;">
            <option value="dinas_guru">Rapat Dinas Guru &amp; Tendik</option>
            <option value="pleno_kelulusan">Rapat Pleno Kelulusan Siswa</option>
            <option value="pleno_kenaikan">Rapat Pleno Kenaikan Kelas</option>
            <option value="kurikulum_kosp">Rapat Kurikulum &amp; KOSP</option>
            <option value="komite_ortu">Rapat Komite &amp; Wali Murid</option>
            <option value="kepanitiaan">Rapat Koordinasi Kepanitiaan</option>
            <option value="evaluasi_bulanan">Rapat Evaluasi Bulanan</option>
            <option value="lainnya">Rapat Khusus Lainnya</option>
          </select>
        </div>

        <div>
          <label style="display:block; font-size:12px; font-weight:800; color:#334155; margin-bottom:4px;">Nomor Agenda Surat Undangan</label>
          <input type="text" name="nomor_surat" placeholder="Kosongkan untuk auto-generate nomor dinas" style="width:100%; height:38px; border-radius:6px; border:1px solid #cbd5e1; padding:0 10px; font-size:13px;" />
        </div>
      </div>

      <div style="display:grid; grid-template-columns:1.5fr 1fr 1fr; gap:12px; margin-bottom:14px;">
        <div>
          <label style="display:block; font-size:12px; font-weight:800; color:#334155; margin-bottom:4px;">Tanggal Pelaksanaan <span style="color:#ef4444;">*</span></label>
          <input type="date" name="tanggal_rapat" value="{{ date('Y-m-d') }}" required style="width:100%; height:38px; border-radius:6px; border:1px solid #cbd5e1; padding:0 10px; font-size:13px;" />
        </div>

        <div>
          <label style="display:block; font-size:12px; font-weight:800; color:#334155; margin-bottom:4px;">Jam Mulai <span style="color:#ef4444;">*</span></label>
          <input type="time" name="jam_mulai" value="08:30" required style="width:100%; height:38px; border-radius:6px; border:1px solid #cbd5e1; padding:0 10px; font-size:13px;" />
        </div>

        <div>
          <label style="display:block; font-size:12px; font-weight:800; color:#334155; margin-bottom:4px;">Jam Selesai</label>
          <input type="text" name="jam_selesai" value="Selesai" placeholder="Contoh: 12:00 atau Selesai" style="width:100%; height:38px; border-radius:6px; border:1px solid #cbd5e1; padding:0 10px; font-size:13px;" />
        </div>
      </div>

      <div style="margin-bottom:14px;">
        <label style="display:block; font-size:12px; font-weight:800; color:#334155; margin-bottom:4px;">Tempat / Ruangan Pelaksanaan <span style="color:#ef4444;">*</span></label>
        <input type="text" name="tempat" value="Ruang Guru SMKN 1 Air Naningan" required style="width:100%; height:38px; border-radius:6px; border:1px solid #cbd5e1; padding:0 10px; font-size:13px;" />
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
        <div>
          <label style="display:block; font-size:12px; font-weight:800; color:#334155; margin-bottom:4px;">Pimpinan Rapat</label>
          <select name="pimpinan_rapat_id" style="width:100%; height:38px; border-radius:6px; border:1px solid #cbd5e1; padding:0 10px; font-size:13px;">
            <option value="">-- Pilih Pimpinan Rapat (Kepsek / Waka) --</option>
            @foreach($gurus as $g)
              <option value="{{ $g->id }}" {{ (stripos($g->jabatan ?? '', 'kepala sekolah') !== false) ? 'selected' : '' }}>
                {{ $g->nama }} ({{ $g->jabatan ?: 'Guru' }})
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label style="display:block; font-size:12px; font-weight:800; color:#334155; margin-bottom:4px;">Notulis Rapat (Pencatat Notula)</label>
          <select name="notulis_id" style="width:100%; height:38px; border-radius:6px; border:1px solid #cbd5e1; padding:0 10px; font-size:13px;">
            <option value="">-- Pilih Notulis / Staf TU --</option>
            @foreach($gurus as $g)
              <option value="{{ $g->id }}">{{ $g->nama }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div style="margin-bottom:14px;">
        <label style="display:block; font-size:12px; font-weight:800; color:#334155; margin-bottom:4px;">Sasaran Peserta Rapat <span style="color:#ef4444;">*</span></label>
        <select name="peserta_tipe" id="selectPesertaTipe" onchange="togglePesertaSelection(this.value)" style="width:100%; height:38px; border-radius:6px; border:1px solid #cbd5e1; padding:0 10px; font-size:13px;">
          <option value="semua_gtk">Seluruh Dewan Guru &amp; Tenaga Kependidikan (GTK)</option>
          <option value="guru">Hanya Dewan Guru</option>
          <option value="tendik">Hanya Tenaga Kependidikan / Tata Usaha</option>
          <option value="komite">Pengurus Komite Sekolah &amp; Wali Murid</option>
          <option value="terpilih">Pilih Personel Guru Tertentu</option>
        </select>
      </div>

      {{-- Box Seleksi Peserta Terpilih --}}
      <div id="boxPesertaTerpilih" style="display:none; margin-bottom:14px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px;">
        <div style="font-size:11.5px; font-weight:800; color:#475569; margin-bottom:6px;">Centang Guru / Peserta Rapat yang Diundang:</div>
        <div style="max-height:160px; overflow-y:auto; display:grid; grid-template-columns:1fr 1fr; gap:6px;">
          @foreach($gurus as $g)
            <label style="display:flex; align-items:center; gap:6px; font-size:11.5px; color:#1e293b; cursor:pointer;">
              <input type="checkbox" name="peserta_ids[]" value="{{ $g->id }}" style="accent-color:#0284c7;" />
              <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $g->nama }}</span>
            </label>
          @endforeach
        </div>
      </div>

      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:12px; font-weight:800; color:#334155; margin-bottom:4px;">Agenda / Poin Utama Pembahasan</label>
        <textarea name="agenda" rows="3" placeholder="Tuliskan poin-poin agenda rapat..." style="width:100%; border-radius:6px; border:1px solid #cbd5e1; padding:8px 10px; font-size:13px; resize:vertical;"></textarea>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--border); padding-top:14px;">
        <button type="button" class="btn btn-outline" onclick="toggleModalRapat(false)" style="height:38px; padding:0 16px; border-radius:6px; font-size:12.5px;">Batal</button>
        <button type="submit" class="btn" style="height:38px; padding:0 20px; border-radius:6px; font-size:12.5px; font-weight:800; background:#0284c7; color:#fff; border:none; cursor:pointer;">
          <i class="bi bi-check2-circle"></i> Buat Agenda &amp; Dokumen Rapat
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  function toggleModalRapat(show) {
    const modal = document.getElementById('modalBuatRapat');
    if (modal) {
      modal.style.display = show ? 'flex' : 'none';
      document.body.style.overflow = show ? 'hidden' : '';
    }
  }

  function togglePesertaSelection(val) {
    const box = document.getElementById('boxPesertaTerpilih');
    if (box) {
      box.style.display = (val === 'terpilih') ? 'block' : 'none';
    }
  }

  function toggleDropdownCetak(id) {
    const el = document.getElementById('dropdownCetak' + id);
    if (!el) return;
    const isShowing = el.style.display === 'block';
    
    // Close other dropdowns
    document.querySelectorAll('[id^="dropdownCetak"]').forEach(d => d.style.display = 'none');
    el.style.display = isShowing ? 'none' : 'block';
  }

  // Close dropdowns on outside click
  document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown-cetak-wrapper')) {
      document.querySelectorAll('[id^="dropdownCetak"]').forEach(d => d.style.display = 'none');
    }
  });
</script>

</body>
</html>
