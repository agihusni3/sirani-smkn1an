<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pusat Pengumuman &amp; Broadcast — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <style>
    .ann-stat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 10px;
      margin-bottom: 12px;
    }
    .ann-stat-card {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: var(--r-sm);
      padding: 10px 14px;
      display: flex;
      align-items: center;
      gap: 12px;
      transition: all .15s ease;
      box-shadow: var(--shadow-sm);
    }
    .ann-stat-card:hover {
      border-color: #000000;
    }
    .ann-stat-icon {
      width: 32px;
      height: 32px;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 15px;
      flex-shrink: 0;
    }
    .ann-stat-val {
      font-size: 20px;
      font-weight: 900;
      font-family: var(--font-mono);
      line-height: 1.1;
      color: #000000;
    }
    .ann-stat-lbl {
      font-size: 11.5px;
      color: var(--text-3);
      font-weight: 600;
      margin-top: 2px;
    }
    @media (max-width: 768px) {
      .ann-stat-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 8px !important;
      }
      .ann-stat-card {
        padding: 8px 10px !important;
        gap: 10px !important;
      }
      .ann-stat-val {
        font-size: 18px !important;
      }
      .ann-stat-icon {
        width: 32px !important;
        height: 32px !important;
        font-size: 15px !important;
      }
    }

    /* Modern Hover Tooltip */
    [data-tooltip] {
      position: relative;
    }
    [data-tooltip]::before {
      content: attr(data-tooltip);
      position: absolute;
      bottom: calc(100% + 8px);
      left: 50%;
      transform: translateX(-50%) translateY(4px);
      background: #0f172a;
      color: #f8fafc;
      font-size: 11px;
      font-weight: 700;
      padding: 5px 9px;
      border-radius: 6px;
      white-space: nowrap;
      pointer-events: none;
      opacity: 0;
      visibility: hidden;
      transition: opacity .15s ease, transform .15s ease;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.45);
      border: 1px solid rgba(255,255,255,0.15);
      z-index: 1000;
    }
    [data-tooltip]::after {
      content: '';
      position: absolute;
      bottom: calc(100% + 2px);
      left: 50%;
      transform: translateX(-50%) translateY(4px);
      border: 4px solid transparent;
      border-top-color: #0f172a;
      pointer-events: none;
      opacity: 0;
      visibility: hidden;
      transition: opacity .15s ease, transform .15s ease;
      z-index: 1000;
    }
    [data-tooltip]:hover::before,
    [data-tooltip]:hover::after {
      opacity: 1;
      visibility: visible;
      transform: translateX(-50%) translateY(0);
    }
  </style>
</head>
<body>
<div class="app-container">
  @include('partials.sidebar')
  <main class="main-content">
    
    {{-- HEADER --}}
    @php
      $currentUser = auth()->user();
      $canManagePengumuman = $currentUser && ($currentUser->isAdmin() || $currentUser->isWakasis() || $currentUser->isWakaKurikulum());
    @endphp

    {{-- ULTRA COMPACT SLIM HEADER BAR --}}
    <div class="panel no-print" style="background:var(--bg-2); border:1px solid var(--border); padding:10px 16px; margin-bottom:12px; border-radius:var(--r-md); box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <h1 style="margin:0; font-size:16px; font-weight:900; color:var(--text); display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-megaphone-fill" style="color:#000000; font-size:16px;"></i> Pengumuman &amp; Broadcast Sekolah
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:11.5px; color:var(--text-3);">
            Informasi edaran &amp; broadcast WhatsApp massal
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
          @if($canManagePengumuman)
            <button type="button" id="btnTogglePengumuman" onclick="toggleFormPengumuman()" class="btn btn-sm btn-gold" style="height:32px; padding:0 12px; font-size:11.5px; font-weight:800; display:inline-flex; align-items:center; gap:5px; border-radius:6px;">
              <i class="bi bi-plus-circle-fill"></i> <span id="textTogglePengumuman">Buat Pengumuman</span>
            </button>
          @endif
          @include('partials.header_actions')
        </div>
      </div>
    </div>

    @if(session('success'))<div class="alert-success" style="margin-bottom:16px;"><i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error" style="margin-bottom:16px;"><i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}</div>@endif
    @if(isset($errors) && $errors->any())<div class="alert-error" style="margin-bottom:16px;">@foreach($errors->all() as $err)<div><i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>{{ $err }}</div>@endforeach</div>@endif

    {{-- SUB-NAVIGASI MODUL WHATSAPP: NOTIFIKASI OTOMATIS vs BROADCAST PENGUMUMAN --}}
    <div style="display:flex; gap:6px; margin-bottom:12px; border-bottom:1px solid var(--border); padding-bottom:8px; overflow-x:auto; flex-wrap:nowrap; -webkit-overflow-scrolling:touch; scrollbar-width:none;">
      <a href="/notifikasi" class="btn btn-sm btn-outline" style="font-weight:700; font-size:11.5px; border-radius:16px; padding:4px 14px; text-decoration:none; display:inline-flex; align-items:center; gap:5px; white-space:nowrap; flex-shrink:0;">
        <i class="bi bi-chat-text-fill"></i> Notifikasi Presensi &amp; Disiplin
      </a>
      <a href="/pengumuman" class="btn btn-sm btn-gold" style="font-weight:800; font-size:11.5px; border-radius:16px; padding:4px 14px; text-decoration:none; display:inline-flex; align-items:center; gap:5px; white-space:nowrap; flex-shrink:0;">
        <i class="bi bi-megaphone-fill"></i> Broadcast &amp; Pengumuman Sekolah
      </a>
    </div>

    {{-- KPI STATS --}}
    <div class="ann-stat-grid">
      <div class="ann-stat-card">
        <div class="ann-stat-icon" style="background:rgba(0,0,0,0.06); color:var(--text);">
          <i class="bi bi-megaphone-fill"></i>
        </div>
        <div>
          <div class="ann-stat-val">{{ $statTotal }}</div>
          <div class="ann-stat-lbl">Total Pengumuman</div>
        </div>
      </div>

      <div class="ann-stat-card">
        <div class="ann-stat-icon" style="background:rgba(0,0,0,0.06); color:var(--text);">
          <i class="bi bi-broadcast-pin"></i>
        </div>
        <div>
          <div class="ann-stat-val">{{ $statAktif }}</div>
          <div class="ann-stat-lbl">Pengumuman Aktif Hari Ini</div>
        </div>
      </div>

      <div class="ann-stat-card">
        <div class="ann-stat-icon" style="background:rgba(0,0,0,0.06); color:var(--text);">
          <i class="bi bi-whatsapp"></i>
        </div>
        <div>
          <div class="ann-stat-val">{{ number_format($statWaTerkirim) }}</div>
          <div class="ann-stat-lbl">Pesan WA Terkirim</div>
        </div>
      </div>

      <div class="ann-stat-card">
        <div class="ann-stat-icon" style="background:rgba(0,0,0,0.06); color:var(--text);">
          <i class="bi bi-window-sidebar"></i>
        </div>
        <div>
          <div class="ann-stat-val">{{ $statPortal }}</div>
          <div class="ann-stat-lbl">Tayang di Portal Siswa</div>
        </div>
      </div>
    </div>

    @if($canManagePengumuman)
    <!-- FORM BUAT PENGUMUMAN BARU (COLLAPSIBLE) -->
    <div class="panel" id="panelPengumuman" style="{{ (isset($errors) && $errors->any()) ? 'display:block;' : 'display:none;' }} margin-bottom: 20px; border-color: var(--border); background: var(--bg-2);">
      <div class="panel-title" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; padding-bottom:12px; border-bottom:1px solid var(--border);">
        <div style="display:flex; align-items:center; gap:8px;">
          <div class="stat-icon" style="width:36px; height:36px; border-radius:8px; background:rgba(0,0,0,0.06); color:#000000; display:flex; align-items:center; justify-content:center; font-size:18px;">
            <i class="bi bi-send-plus-fill"></i>
          </div>
          <div>
            <span style="font-weight:800; font-size:15px; color:var(--text);">Formulir Penerbitan Pengumuman &amp; Broadcast</span>
            <div style="font-size:12px; color:var(--text-3);">Kirim pesan resmi instan atau tayangkan banner pengumuman di portal dan gerbang.</div>
          </div>
        </div>
        <button type="button" onclick="toggleFormPengumuman(false)" class="btn btn-outline" style="height:32px; width:32px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:6px; color:var(--text-3);" title="Tutup Form">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <form action="{{ route('pengumuman.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        {{-- Template Pesan Siap Pakai --}}
        <div style="background: var(--bg-2); border: 1px solid var(--border); border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;">
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div>
              <strong style="font-size:12.5px; color:var(--text);">Template Pesan Siap Pakai:</strong>
              <div style="font-size:11px; color:var(--text-3);">Pilih template resmi di bawah untuk mengisi formulir secara otomatis.</div>
            </div>
            <select id="selectTemplatePesan" onchange="applyTemplate(this.value)" class="input-field" style="max-width:320px; font-weight:700; height:34px; font-size:12px;">
              <option value="">-- Pilih Template Pesan Resmi --</option>
              <option value="upacara">Upacara Bendera &amp; Seragam Sekolah</option>
              <option value="ujian">Asesmen / Ujian Semester (KBM)</option>
              <option value="libur_rapat">Belajar Mandiri di Rumah (Rapat Guru)</option>
              <option value="disiplin">Himbauan Kedisiplinan &amp; Tata Tertib</option>
              <option value="rapor">Undangan Pertemuan Wali Murid &amp; Rapor</option>
              <option value="pkl">Pembekalan &amp; Persiapan PKL Industri</option>
              <option value="praktik_bengkel">Perlengkapan Praktik Bengkel (Khusus Siswa)</option>
              <option value="alumni">Pengambilan Ijazah &amp; Cap 3 Jari (Alumni)</option>
            </select>
          </div>
          
          {{-- Tombol Pill Template --}}
          <div style="display:flex; flex-wrap:wrap; gap:6px; margin-top:10px;">
            <button type="button" onclick="applyTemplate('upacara')" class="btn btn-sm btn-outline" style="font-size:11px; padding:4px 10px; border-radius:14px; background:var(--bg-card);">Upacara &amp; Seragam</button>
            <button type="button" onclick="applyTemplate('ujian')" class="btn btn-sm btn-outline" style="font-size:11px; padding:4px 10px; border-radius:14px; background:var(--bg-card);">Ujian Semester</button>
            <button type="button" onclick="applyTemplate('libur_rapat')" class="btn btn-sm btn-outline" style="font-size:11px; padding:4px 10px; border-radius:14px; background:var(--bg-card);">Belajar Mandiri</button>
            <button type="button" onclick="applyTemplate('disiplin')" class="btn btn-sm btn-outline" style="font-size:11px; padding:4px 10px; border-radius:14px; background:var(--bg-card);">Kedisiplinan</button>
            <button type="button" onclick="applyTemplate('rapor')" class="btn btn-sm btn-outline" style="font-size:11px; padding:4px 10px; border-radius:14px; background:var(--bg-card);">Ambil Rapor</button>
            <button type="button" onclick="applyTemplate('pkl')" class="btn btn-sm btn-outline" style="font-size:11px; padding:4px 10px; border-radius:14px; background:var(--bg-card);">PKL Industri</button>
            <button type="button" onclick="applyTemplate('praktik_bengkel')" class="btn btn-sm btn-outline" style="font-size:11px; padding:4px 10px; border-radius:14px; background:var(--bg-card);">Praktik Bengkel</button>
            <button type="button" onclick="applyTemplate('alumni')" class="btn btn-sm btn-outline" style="font-size:11px; padding:4px 10px; border-radius:14px; background:var(--bg-card);">Alumni Ijazah</button>
          </div>
        </div>
        
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:14px; margin-bottom:14px;">
          
          {{-- Judul Pengumuman --}}
          <div style="grid-column: 1 / -1;">
            <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Judul / Topik Pengumuman <span style="color:var(--red);">*</span></label>
            <input type="text" name="judul" id="inputJudulPengumuman" required placeholder="Contoh: Pemberitahuan Kegiatan Upacara Hari Pahlawan & Jadwal KBM" class="input-field" style="width:100%; font-weight:700;" value="{{ old('judul') }}" />
          </div>

          {{-- Kategori --}}
          <div>
            <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Kategori Pengumuman <span style="color:var(--red);">*</span></label>
            <select name="kategori" id="selectKategoriPengumuman" class="input-field" style="width:100%; font-weight:600;" required>
              <option value="umum" {{ old('kategori') === 'umum' ? 'selected' : '' }}>Pengumuman Umum</option>
              <option value="kedisiplinan" {{ old('kategori') === 'kedisiplinan' ? 'selected' : '' }}>Kedisiplinan &amp; Tata Tertib</option>
              <option value="kegiatan" {{ old('kategori') === 'kegiatan' ? 'selected' : '' }}>Kegiatan Sekolah / Upacara</option>
              <option value="akademik" {{ old('kategori') === 'akademik' ? 'selected' : '' }}>Akademik, Ujian &amp; KBM</option>
              <option value="darurat" {{ old('kategori') === 'darurat' ? 'selected' : '' }}>Penting / Libur Mendadak</option>
            </select>
          </div>

          {{-- Target Penerima --}}
          <div>
            <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Target Penerima Pesan <span style="color:var(--red);">*</span></label>
            <select name="target_tipe" id="targetTipeSelect" class="input-field" style="width:100%; font-weight:700;" onchange="handleTargetChange(this.value)" required>
              <option value="semua">Seluruh Siswa &amp; Orang Tua</option>
              <option value="tingkat">Berdasarkan Tingkat Kelas (X / XI / XII)</option>
              <option value="rombel">Khusus Rombel Tertentu</option>
              @if(!$isWaliOnly)
                <option value="jurusan">Khusus Program Keahlian / Jurusan</option>
                <option value="alumni">Khusus Direktori Alumni / Lulusan</option>
              @endif
            </select>
          </div>

          {{-- Dynamic Target ID --}}
          <div id="targetDetailBox" style="display:none;">
            <label class="form-label" id="targetDetailLabel" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Pilih Detail Target</label>
            
            <select name="target_id" id="targetIdSelect" class="input-field" style="width:100%;">
              {{-- Options injected by JavaScript --}}
            </select>
          </div>

          {{-- Tanggal Mulai --}}
          <div>
            <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Tanggal Mulai Tayang</label>
            <input type="date" name="tanggal_mulai" class="input-field" style="width:100%;" value="{{ date('Y-m-d') }}" />
          </div>

          {{-- Tanggal Selesai (Opsional) --}}
          <div>
            <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Tanggal Berakhir (Opsional)</label>
            <input type="date" name="tanggal_selesai" class="input-field" style="width:100%;" placeholder="Kosongkan jika berlaku terus" />
          </div>
        </div>

        {{-- Isi Pesan --}}
        <div style="margin-bottom:14px;">
          <label class="form-label" style="font-weight:700; font-size:12px; display:flex; justify-content:space-between; margin-bottom:4px;">
            <span>Isi Pesan Pengumuman <span style="color:var(--red);">*</span></span>
            <span style="color:var(--text-3); font-size:11px; font-weight:400;">Gunakan *teks tebal* untuk penekanan di WhatsApp</span>
          </label>
          <textarea name="isi_pesan" id="textareaIsiPesan" rows="6" required class="input-field" style="width:100%; font-family:var(--font); line-height:1.4;" placeholder="Tuliskan isi pengumuman secara rinci dan jelas... Contoh: Diberitahukan kepada seluruh siswa bahwa pada hari Senin tanggal 28 Agustus 2026 wajib mengenakan pakaian seragam Pramuka lengkap beserta atribut topi dan dasi...">{{ old('isi_pesan') }}</textarea>
        </div>

        {{-- Upload Poster / Banner Gambar (Opsional) --}}
        <div style="margin-bottom:14px; background:var(--bg-3); border:1px dashed var(--border-2); border-radius:8px; padding:12px 16px;">
          <label class="form-label" style="font-weight:700; font-size:12px; display:flex; justify-content:space-between; margin-bottom:6px;">
            <span>Upload Poster / Banner Gambar (Opsional)</span>
            <span style="color:var(--text-3); font-size:11px; font-weight:400;">Maks. 3 MB (JPG, PNG, WEBP)</span>
          </label>
          <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
            <div id="banner_preview_box" style="width:120px; height:68px; border-radius:6px; border:1px solid var(--border); background:var(--bg-2); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
              <span style="font-size:10.5px; color:var(--text-3); text-align:center;">Pratinjau Banner</span>
            </div>
            <div style="flex:1; min-width:220px;">
              <input type="file" name="banner_gambar" id="inputBannerGambar" accept="image/*" class="input-field" style="width:100%; height:38px; padding:4px 8px; font-size:12px;" onchange="previewBannerImage(this)" />
              <div style="font-size:11px; color:var(--text-3); margin-top:4px;">
                Gambar ini akan ditampilkan sebagai poster visual di <strong>Portal Siswa &amp; Wali Murid</strong>.
              </div>
            </div>
          </div>
        </div>

        {{-- Pilihan Saluran Publikasi --}}
        <div style="background:var(--bg-2); border:1px solid var(--border); border-radius:8px; padding:14px 16px; margin-bottom:16px;">
          <div style="font-size:12px; font-weight:800; text-transform:uppercase; color:var(--text-2); margin-bottom:10px;">
            Saluran Pengiriman &amp; Publikasi
          </div>
          
          <div style="display:flex; flex-direction:column; gap:12px;">
            {{-- Checkbox WA & Opsi Penerima --}}
            <div style="border-bottom:1px solid var(--border); padding-bottom:10px;">
              <label style="display:inline-flex; align-items:center; gap:8px; font-size:13px; font-weight:800; cursor:pointer; color:var(--text);">
                <input type="checkbox" name="kirim_wa" id="checkboxKirimWa" value="1" checked onchange="document.getElementById('waTargetOptions').style.display = this.checked ? 'block' : 'none';" style="width:16px; height:16px; accent-color:var(--text);" />
                <span>Kirim Broadcast WhatsApp Massal</span>
              </label>

              {{-- Pilihan Penerima WA --}}
              <div id="waTargetOptions" style="margin-left:24px; margin-top:8px; background:var(--bg-3); padding:10px 14px; border-radius:6px; border:1px solid var(--border);">
                <div style="font-size:11px; font-weight:700; color:var(--text-3); margin-bottom:6px;">PILIH TUJUAN NOMOR WHATSAPP:</div>
                <div style="display:flex; flex-wrap:wrap; gap:16px;">
                  <label style="display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:700; cursor:pointer; color:var(--text);">
                    <input type="radio" name="target_penerima_wa" value="ortu" checked style="accent-color:var(--text);" />
                    <span>Orang Tua / Wali Saja</span>
                  </label>
                  <label style="display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:700; cursor:pointer; color:var(--text);">
                    <input type="radio" name="target_penerima_wa" value="siswa" style="accent-color:var(--text);" />
                    <span>Siswa Pribadi Saja (Tanpa melibatkan Ortu)</span>
                  </label>
                  <label style="display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:700; cursor:pointer; color:var(--text);">
                    <input type="radio" name="target_penerima_wa" value="keduanya" style="accent-color:var(--text);" />
                    <span>Kirim ke Keduanya (Ortu &amp; Siswa)</span>
                  </label>
                </div>
              </div>
            </div>

            <div style="display:flex; flex-wrap:wrap; gap:16px;">
              <label style="display:inline-flex; align-items:center; gap:8px; font-size:12.5px; font-weight:700; cursor:pointer; color:var(--text);">
                <input type="checkbox" name="tampil_portal" value="1" checked style="width:16px; height:16px; accent-color:#000000;" />
                <span>Tampilkan Banner di Portal Siswa &amp; Wali Murid</span>
              </label>
              <label style="display:inline-flex; align-items:center; gap:8px; font-size:12.5px; font-weight:700; cursor:pointer; color:var(--text);">
                <input type="checkbox" name="tampil_kios" value="1" checked style="width:16px; height:16px; accent-color:#000000;" />
                <span>Tampilkan Running Text di Layar Kios Gerbang</span>
              </label>
            </div>
          </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:8px; border-top:1px solid var(--border); padding-top:14px;">
          <button type="button" onclick="toggleFormPengumuman(false)" class="btn btn-outline">Batal</button>
          <button type="submit" class="btn btn-gold" style="font-weight:800; padding:0 20px;">
            Terbitkan &amp; Kirim Pengumuman
          </button>
        </div>
      </form>
    </div>
    @endif

    {{-- TABEL PENGUMUMAN AKTIF & RIWAYAT --}}
    <div class="panel" style="padding:0; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); box-shadow:var(--shadow-sm); background:var(--bg-2); margin-bottom:24px;">
      {{-- Header & Toolbar Terpadu --}}
      <div style="padding:8px 12px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
        <div style="font-weight:800; font-size:13.5px; color:var(--text); display:flex; align-items:center; gap:6px;">
          <i class="bi bi-collection-fill" style="color:#000000;"></i>
          <span>Daftar Pengumuman Terbit</span>
          <span style="background:var(--bg-3); border:1px solid var(--border-2); color:var(--text-2); font-size:10.5px; font-weight:700; padding:1px 6px; border-radius:4px;" class="font-mono">
            {{ $pengumumans->total() }}
          </span>
        </div>

        {{-- Filter Kategori --}}
        <form method="GET" action="{{ route('pengumuman.index') }}" style="display:flex; gap:6px; align-items:center; flex-wrap:wrap; flex:1; justify-content:flex-end; max-width:480px;">
          <div style="width:130px; flex-shrink:0;">
            <select name="kategori" class="input-field" style="width:100%; height:32px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 6px;" onchange="this.form.submit()">
              <option value="">Semua Kategori</option>
              <option value="umum" {{ request('kategori') === 'umum' ? 'selected' : '' }}>Umum</option>
              <option value="kegiatan" {{ request('kategori') === 'kegiatan' ? 'selected' : '' }}>Kegiatan</option>
              <option value="kedisiplinan" {{ request('kategori') === 'kedisiplinan' ? 'selected' : '' }}>Kedisiplinan</option>
              <option value="akademik" {{ request('kategori') === 'akademik' ? 'selected' : '' }}>Akademik</option>
              <option value="libur" {{ request('kategori') === 'libur' ? 'selected' : '' }}>Hari Libur</option>
            </select>
          </div>

          <div style="width:125px; flex-shrink:0;">
            <select name="status" class="input-field" style="width:100%; height:32px; font-size:11.5px; background:var(--bg-2); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 6px;" onchange="this.form.submit()">
              <option value="">Semua Status</option>
              <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Tayang Aktif</option>
              <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
          </div>

          @if(request('kategori') || request('status'))
            <a href="{{ route('pengumuman.index') }}" class="btn btn-sm btn-outline" style="height:32px; padding:0 8px; font-size:11px; font-weight:800; color:var(--red); border-color:rgba(239,68,68,0.4); border-radius:var(--r-sm); flex-shrink:0;" title="Reset Filter">
              Reset
            </a>
          @endif
        </form>
      </div>

      <div class="table-responsive">
        <table class="data-table" style="margin:0;">
          <thead>
            <tr>
              <th style="width:40px; text-align:center;">#</th>
              <th>Topik &amp; Isi Pengumuman</th>
              <th>Kategori</th>
              <th>Target Penerima</th>
              <th>Saluran Publikasi</th>
              <th>Masa Tayang</th>
              <th>Status WA</th>
              <th style="text-align:center;">Tayang</th>
              @if($canManagePengumuman)
                <th style="width:90px; text-align:center;">Aksi</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @forelse($pengumumans as $idx => $p)
              @php
                $badge = $p->kategori_badge;
              @endphp
              <tr>
                <td style="text-align:center; font-weight:700; color:var(--text-3); font-size:12px;">
                  {{ $pengumumans->firstItem() + $idx }}
                </td>
                <td>
                  <div style="display:flex; gap:10px; align-items:flex-start;">
                    @if($p->banner_url)
                      <a href="{{ $p->banner_url }}" target="_blank" title="Klik untuk lihat gambar poster penuh" style="flex-shrink:0;">
                        <img src="{{ $p->banner_url }}" alt="Poster" style="width:48px; height:48px; border-radius:6px; object-fit:cover; border:1.5px solid rgba(0,0,0,0.15); box-shadow:0 2px 6px rgba(0,0,0,0.15);" />
                      </a>
                    @endif
                    <div style="flex:1;">
                      <strong style="color:var(--text); font-size:13.5px; display:block;">{{ $p->judul }}</strong>
                      <div style="font-size:12px; color:var(--text-3); margin-top:2px; max-width:380px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $p->isi_pesan }}
                      </div>
                      <div style="font-size:11px; color:var(--text-3); margin-top:3px;">
                        Dibuat oleh: <strong>{{ $p->user->name ?? 'Administrator' }}</strong> &bull; {{ $p->created_at->diffForHumans() }}
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <span style="color:var(--text); font-weight:800; font-size:11px; text-transform:uppercase;">
                    {{ $badge['label'] }}
                  </span>
                </td>
                <td>
                  <span style="color:var(--text); font-weight:700; font-size:11px;">
                    {{ $p->target_nama ?: 'Semua Siswa' }}
                  </span>
                </td>
                <td>
                  <div style="display:flex; gap:4px; flex-wrap:wrap;">
                    @if($p->kirim_wa)
                      <span style="color:var(--text); font-size:11px; font-weight:800;">
                        WA
                      </span>
                    @endif
                    @if($p->tampil_portal)
                      <span style="color:var(--text); font-size:11px; font-weight:800;">
                        Portal
                      </span>
                    @endif
                    @if($p->tampil_kios)
                      <span style="color:var(--text); font-size:11px; font-weight:800;">
                        Kios
                      </span>
                    @endif
                  </div>
                </td>
                <td style="font-size:11.5px; color:var(--text-2);">
                  @if($p->tanggal_mulai)
                    <div>{{ $p->tanggal_mulai->format('d/m/Y') }}</div>
                  @endif
                  @if($p->tanggal_selesai)
                    <div style="color:var(--text-3); font-size:10.5px;">s.d {{ $p->tanggal_selesai->format('d/m/Y') }}</div>
                  @else
                    <div style="color:var(--text); font-size:10px; font-weight:700;">Berlaku Terus</div>
                  @endif
                </td>
                <td>
                  @if($p->kirim_wa)
                    <span class="badge" style="background:var(--bg-3); color:var(--text); border:1px solid var(--border-2); font-weight:800; font-size:10.5px;">
                      {{ $p->total_terkirim }} / {{ $p->total_target }} WA
                    </span>
                  @else
                    <span style="color:var(--text-3); font-size:11px;">-</span>
                  @endif
                </td>
                <td style="text-align:center;">
                  @if($canManagePengumuman)
                    <form action="{{ route('pengumuman.toggle', $p->id) }}" method="POST" style="display:inline;">
                      @csrf
                      <button type="submit" class="table-status-pill {{ $p->is_active ? 'aktif' : 'netral' }}" style="cursor:pointer;" title="Klik untuk {{ $p->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                        <i class="bi {{ $p->is_active ? 'bi-broadcast' : 'bi-dash-circle' }}"></i> {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                      </button>
                    </form>
                  @else
                    <span class="table-status-pill {{ $p->is_active ? 'aktif' : 'netral' }}">
                      <i class="bi {{ $p->is_active ? 'bi-broadcast' : 'bi-dash-circle' }}"></i> {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  @endif
                </td>
                @if($canManagePengumuman)
                  <td style="text-align:center;">
                    <form action="{{ route('pengumuman.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')" style="display:inline; margin:0;">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn-icon btn-icon-danger" data-tooltip="Hapus Pengumuman" title="Hapus Pengumuman">
                        <i class="bi bi-trash3-fill"></i>
                      </button>
                    </form>
                  </td>
                @endif
              </tr>
            @empty
              <tr>
                <td colspan="9" style="text-align:center; padding:36px; color:var(--text-3);">
                  <i class="bi bi-megaphone" style="font-size:32px; opacity:0.4;"></i>
                  <div style="font-weight:700; margin-top:8px; font-size:14px; color:var(--text);">Belum ada pengumuman yang diterbitkan</div>
                  <p style="font-size:12px; margin-top:4px;">Klik tombol "+ Buat Pengumuman Baru" di atas untuk mengirim broadcast.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($pengumumans->hasPages())
        <div style="padding:14px 18px; border-top:1px solid var(--border); display:flex; justify-content:center;">
          {{ $pengumumans->links() }}
        </div>
      @endif
    </div>
  </main>
</div>

<script>
  const rombelsData = @json($rombels);
  const jurusansData = @json($jurusans);

  function toggleFormPengumuman(forceState) {
    const panel = document.getElementById('panelPengumuman');
    const text = document.getElementById('textTogglePengumuman');
    if (forceState !== undefined) {
      panel.style.display = forceState ? 'block' : 'none';
    } else {
      panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    }
    if (text) {
      text.innerText = panel.style.display === 'block' ? '✕ Tutup Form' : '+ Buat Pengumuman Baru';
    }
  }

  function handleTargetChange(val) {
    const box = document.getElementById('targetDetailBox');
    const label = document.getElementById('targetDetailLabel');
    const select = document.getElementById('targetIdSelect');

    select.innerHTML = '';

    if (val === 'tingkat') {
      box.style.display = 'block';
      label.innerText = 'Pilih Tingkat Kelas:';
      select.innerHTML = `
        <option value="10">Kelas X (Sepuluh)</option>
        <option value="11">Kelas XI (Sebelas)</option>
        <option value="12">Kelas XII (Duabelas)</option>
      `;
    } else if (val === 'rombel') {
      box.style.display = 'block';
      label.innerText = 'Pilih Rombel Kelas:';
      rombelsData.forEach(r => {
        const opt = document.createElement('option');
        opt.value = r.id;
        opt.innerText = r.nama_rombel;
        select.appendChild(opt);
      });
    } else if (val === 'jurusan') {
      box.style.display = 'block';
      label.innerText = 'Pilih Program Keahlian:';
      jurusansData.forEach(j => {
        const opt = document.createElement('option');
        opt.value = j.id;
        opt.innerText = j.nama_jurusan;
        select.appendChild(opt);
      });
    } else {
      box.style.display = 'none';
    }
  }

  const templatePesan = {
    upacara: {
      judul: "Pemberitahuan Pelaksanaan Upacara Bendera & Ketentuan Seragam",
      kategori: "kegiatan",
      target_penerima_wa: "keduanya",
      target_tipe: "semua",
      isi_pesan: "Diberitahukan kepada seluruh siswa/i SMK Negeri 1 Air Naningan bahwa pada hari Senin, [Tanggal Upacara], akan dilaksanakan Upacara Bendera rutin.\n\nKetentuan Pelaksanaan:\n1. Hadir di sekolah paling lambat pukul 06.45 WIB untuk presensi Face ID di Smart Gate gerbang.\n2. Mengenakan seragam OSIS Putih Abu-abu lengkap (Dasi, Topi Sekolah, Sabuk Hitam, Kaos Kaki Putih, dan Sepatu Hitam).\n3. Rambut dan kuku wajib rapi sesuai ketentuan tata tertib sekolah.\n\nMohon kerja sama Bapak/Ibu Wali Murid untuk memantau keberangkatan ananda tepat waktu. Terima kasih."
    },
    ujian: {
      judul: "Pemberitahuan Pelaksanaan Asesmen / Penilaian Akhir Semester",
      kategori: "akademik",
      target_penerima_wa: "keduanya",
      target_tipe: "semua",
      isi_pesan: "Diberitahukan kepada seluruh siswa/i dan Bapak/Ibu Wali Murid bahwa Asesmen / Penilaian Sumatif Akhir Semester akan dilaksanakan mulai hari [Hari/Tanggal] s.d [Tanggal Selesai].\n\nKetentuan Ujian:\n1. Siswa wajib hadir tepat waktu pukul 07.15 WIB.\n2. Membawa Kartu Peserta Ujian untuk verifikasi presensi di sekolah.\n3. Membawa alat tulis sendiri dan smartphone/gadget dengan baterai terisi penuh & kuota internet jika ujian berbasis CBT.\n4. Menjaga ketertiban dan sportivitas selama ujian berlangsung.\n\nMari bersama-sama membimbing ananda agar belajar dengan maksimal di rumah. Terima kasih."
    },
    libur_rapat: {
      judul: "Pemberitahuan Kegiatan Belajar Mandiri di Rumah (Rapat Dinas Guru)",
      kategori: "darurat",
      target_penerima_wa: "keduanya",
      target_tipe: "semua",
      isi_pesan: "Sehubungan dengan adanya agenda Rapat Dinas Dewan Guru dan Tenaga Kependidikan SMK Negeri 1 Air Naningan pada hari [Hari/Tanggal], dengan ini kami sampaikan:\n\n1. Siswa/i melaksanakan Kegiatan Belajar Mengajar (KBM) Mandiri di rumah masing-masing.\n2. Guru mata pelajaran akan memberikan materi / penugasan daring melalui platform pembelajaran yang telah disepakati.\n3. Siswa/i kembali masuk sekolah seperti biasa pada hari [Hari/Tanggal Masuk].\n\nDimohon Bapak/Ibu Wali Murid mengawasi kegiatan belajar dan aktivitas ananda selama di rumah. Terima kasih."
    },
    disiplin: {
      judul: "Himbauan Kedisiplinan Kehadiran & Larangan Pelanggaran Tata Tertib",
      kategori: "kedisiplinan",
      target_penerima_wa: "keduanya",
      target_tipe: "semua",
      isi_pesan: "Dalam rangka meningkatkan mutu kedisiplinan dan pembentukan karakter peserta didik di SMKN 1 Air Naningan, kami menghimbau:\n\n1. Jam masuk sekolah adalah pukul 07.00 WIB. Siswa yang hadir setelah pukul 07.15 WIB dinyatakan terlambat dan tercatat pada sistem SIRANI.\n2. Dilarang membawa barang-barang terlarang (rokok/vape, senjata tajam, dll).\n3. Siswa yang akumulasi ketidakhadiran tanpa keterangan (Alpha) mencapai batas toleransi akan langsung diterbitkan Surat Panggilan Orang Tua resmi.\n\nDemikian himbauan ini kami sampaikan demi kebaikan dan masa depan putra/putri kita bersama. Terima kasih."
    },
    rapor: {
      judul: "Undangan Pertemuan Wali Murid & Pembagian Laporan Hasil Belajar (Rapor)",
      kategori: "akademik",
      target_penerima_wa: "ortu",
      target_tipe: "semua",
      isi_pesan: "Kepada Yth. Bapak/Ibu Orang Tua / Wali Murid SMKN 1 Air Naningan,\n\nKami mengundang Bapak/Ibu untuk hadir pada pertemuan penerimaan Laporan Hasil Belajar (Rapor) Semester [Ganjil/Genap] yang akan diselenggarakan pada:\n\n📅 Hari/Tanggal: [Hari/Tanggal]\n⏰ Waktu: Pukul 08.00 WIB s.d Selesai\n📍 Tempat: Ruang Kelas Masing-masing Rombel (Wali Kelas)\n\nCatatan Penting:\n- Kehadiran Orang Tua / Wali Murid TIDAK DAPAT DIWAKILKAN oleh siswa.\n- Membawa alat tulis dan kartu identitas jika diperlukan.\n\nKehadiran Bapak/Ibu sangat berarti bagi perkembangan pendidikan ananda. Terima kasih."
    },
    pkl: {
      judul: "Informasi Pembekalan & Persiapan Praktik Kerja Lapangan (PKL) Industri",
      kategori: "kegiatan",
      target_penerima_wa: "keduanya",
      target_tipe: "tingkat",
      target_id: "11",
      isi_pesan: "Diberitahukan kepada seluruh siswa/i Kelas XI dan Bapak/Ibu Wali Murid mengenai persiapan Praktik Kerja Lapangan (PKL) Industri:\n\n1. Pembekalan PKL Wajib dilaksanakan pada hari [Hari/Tanggal] bertempat di Aula Sekolah.\n2. Seluruh siswa wajib mengenakan pakaian seragam identitas kejuruan (Wearpack) rapi.\n3. Membawa buku jurnal PKL dan kelengkapan administrasi yang telah dibagikan.\n4. Penugasan ke Dunia Usaha / Dunia Industri (DUDI) akan dimulai terhitung tanggal [Tanggal Mulai PKL].\n\nMohon dukungan dan doa restu Bapak/Ibu orang tua agar ananda dapat menjalankan praktik industri dengan sukses."
    },
    praktik_bengkel: {
      judul: "Instruksi Khusus: Kelengkapan APD & Baju Wearpack Praktik Bengkel",
      kategori: "akademik",
      target_penerima_wa: "siswa",
      target_tipe: "rombel",
      isi_pesan: "Diberitahukan kepada seluruh peserta didik rombel terkait bahwa pada pertemuan praktikum bengkel/laboratorium besok:\n\n1. Wajib mengenakan Seragam Wearpack / Katelpak resmi kejuruan lengkap sejak dari rumah atau berganti sebelum jam praktik dimulai.\n2. Wajib mengenakan Sepatu Safety / Sepatu Tertutup (Dilarang bersandal atau sepatu robek).\n3. Membawa kelengkapan APD masing-masing (kacamata kerja, sarung tangan kain, dll).\n4. Dilarang menyalakan mesin/peralatan tanpa instruksi dari Guru Pembimbing / Toolman Bengkel.\n\nSiswa yang tidak mematuhi standar K3 tidak diperkenankan mengikuti kegiatan praktikum."
    },
    alumni: {
      judul: "Informasi Pengambilan Ijazah & Cap 3 Jari Alumni SMKN 1 Air Naningan",
      kategori: "umum",
      target_penerima_wa: "siswa",
      target_tipe: "alumni",
      isi_pesan: "Diberitahukan kepada seluruh Alumni SMK Negeri 1 Air Naningan Tahun Kelulusan [Tahun Lulus]:\n\nJadwal pelaksanaan Cap 3 Jari & Pengambilan Ijazah / Dokumen Kelulusan akan dilayani pada:\n📅 Hari: Senin s.d Jumat\n⏰ Waktu: Pukul 08.30 - 14.00 WIB\n📍 Tempat: Bagian Tata Usaha (TU) SMKN 1 Air Naningan\n\nSyarat Pengambilan:\n1. Telah menyelesaikan Bebas Masalah Perpustakaan & Laboratorium/Bengkel.\n2. Berpakaian rapi dan bersepatu (baju kemeja berkerah, tidak diperkenankan memakai kaos oblong/celana sobek).\n3. Pengambilan ijazah wajib dilakukan sendiri oleh yang bersangkutan (tidak dapat diwakilkan).\n\nTerima kasih."
    }
  };

  function applyTemplate(key) {
    if (!key || !templatePesan[key]) return;
    
    // Buka panel form jika sedang tertutup
    toggleFormPengumuman(true);

    const t = templatePesan[key];
    
    // Set judul
    document.getElementById('inputJudulPengumuman').value = t.judul;
    
    // Set kategori
    document.getElementById('selectKategoriPengumuman').value = t.kategori;
    
    // Set isi pesan
    document.getElementById('textareaIsiPesan').value = t.isi_pesan;
    
    // Set target tipe
    if (t.target_tipe) {
      const targetSelect = document.getElementById('targetTipeSelect');
      if (targetSelect) {
        targetSelect.value = t.target_tipe;
        handleTargetChange(t.target_tipe);
        
        if (t.target_id) {
          setTimeout(() => {
            const detailSelect = document.getElementById('targetIdSelect');
            if (detailSelect) detailSelect.value = t.target_id;
          }, 60);
        }
      }
    }
    
    // Set target penerima WA
    if (t.target_penerima_wa) {
      const radio = document.querySelector(`input[name="target_penerima_wa"][value="${t.target_penerima_wa}"]`);
      if (radio) radio.checked = true;
    }

    // Sync select template dropdown
    const sel = document.getElementById('selectTemplatePesan');
    if (sel) sel.value = key;

    // Scroll smoothly to form
    document.getElementById('panelPengumuman').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function previewBannerImage(input) {
    const box = document.getElementById('banner_preview_box');
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        box.innerHTML = `<img src="${e.target.result}" alt="Preview" style="width:100%; height:100%; object-fit:cover;" />`;
      };
      reader.readAsDataURL(input.files[0]);
    } else {
      box.innerHTML = `<span style="font-size:10.5px; color:var(--text-3); text-align:center;">Pratinjau Banner</span>`;
    }
  }
</script>

</body>
</html>
