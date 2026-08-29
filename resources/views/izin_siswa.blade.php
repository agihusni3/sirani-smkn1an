<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Perizinan Siswa & Guru — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <style>
    /* ── Form Card & Grid Layout ── */
    .izin-form-card {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      padding: 22px 24px;
      margin-bottom: 24px;
      box-shadow: var(--shadow-sm);
    }
    .izin-form-row-1 {
      display: grid;
      grid-template-columns: 1.6fr 1fr 1fr;
      gap: 16px;
      margin-bottom: 16px;
    }
    .izin-form-row-2 {
      display: grid;
      grid-template-columns: 1.4fr 1.1fr 0.7fr;
      gap: 16px;
      align-items: flex-end;
    }
    @media (max-width: 900px) {
      .izin-form-row-1, .izin-form-row-2 {
        grid-template-columns: 1fr;
        gap: 14px;
      }
    }

    .form-field-label {
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: var(--text-2);
      margin-bottom: 6px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .form-field-hint {
      font-weight: 500;
      font-size: 10.5px;
      color: var(--text-3);
      text-transform: none;
      letter-spacing: normal;
    }

    /* ── Kategori Toggle ── */
    .btn-toggle-kat {
      border: none;
      background: transparent;
      color: var(--text-2);
      padding: 8px 18px;
      border-radius: 8px !important;
      font-size: 12.5px;
      font-weight: 800;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all .2s ease;
    }
    .btn-toggle-kat:hover {
      color: var(--text);
      background: rgba(255, 255, 255, 0.05);
    }
    .btn-toggle-kat.active {
      background: #000000 !important;
      color: #FFFFFF !important;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
      border-radius: 8px !important;
    }

    /* ── Searchable Picker (Combobox) ── */
    .person-picker-wrap {
      position: relative;
      width: 100%;
    }
    .person-picker-trigger {
      width: 100%;
      height: 42px;
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      border-radius: var(--r-sm);
      padding: 0 12px;
      display: flex;
      align-items: center;
      cursor: pointer;
      transition: all .2s ease;
      user-select: none;
    }
    .person-picker-trigger:hover, .person-picker-trigger.focused {
      border-color: var(--gold);
      box-shadow: 0 0 0 2px var(--gold-glow);
    }
    .btn-clear-person {
      background: transparent;
      border: none;
      color: var(--text-3);
      font-size: 15px;
      cursor: pointer;
      padding: 2px 4px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: color .15s;
    }
    .btn-clear-person:hover {
      color: var(--red);
    }
    .person-dropdown-panel {
      display: none;
      position: absolute;
      top: calc(100% + 6px);
      left: 0;
      right: 0;
      background: var(--bg-2);
      border: 1.5px solid var(--border-2);
      border-radius: var(--r-sm);
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
      z-index: 1050;
      overflow: hidden;
    }
    .person-dropdown-panel.open {
      display: block;
      animation: modalFadeIn .15s ease;
    }
    .person-list-container {
      max-height: 260px;
      overflow-y: auto;
    }
    .person-picker-item {
      padding: 9px 12px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--border);
      cursor: pointer;
      transition: background .15s;
      gap: 10px;
      text-align: left;
    }
    .person-picker-item:last-child {
      border-bottom: none;
    }
    .person-picker-item:hover {
      background: var(--gold-dim);
    }

    /* ── Tab Navigasi Riwayat ── */
    .izin-nav-tabs {
      display: flex;
      gap: 8px;
      border-bottom: 2px solid var(--border);
      margin-bottom: 18px;
      padding-bottom: 10px;
    }
    .izin-tab-btn {
      background: var(--bg-3);
      border: 1px solid var(--border);
      padding: 9px 18px;
      font-size: 13px;
      font-weight: 800;
      color: var(--text-2);
      cursor: pointer;
      border-radius: var(--r-sm);
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all .2s ease;
    }
    .izin-tab-btn:hover {
      color: var(--text);
      background: rgba(255, 255, 255, 0.05);
      border-color: var(--border-2);
    }
    .izin-tab-btn.active {
      color: #FFFFFF !important;
      background: #000000 !important;
      border-color: #000000 !important;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
    }
    .izin-tab-btn.active i {
      color: #FFFFFF !important;
    }
    .izin-tab-pane { display: none; }
    .izin-tab-pane.active { display: block; }

    /* ── Upload Area Styling ── */
    .upload-btn-wrap {
      position: relative;
      display: flex;
      align-items: center;
      gap: 6px;
      width: 100%;
    }
    .file-upload-label {
      width: 100%;
      height: 42px;
      background: var(--bg-3);
      border: 1.5px dashed var(--border-2);
      border-radius: var(--r-sm);
      padding: 0 12px;
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      margin-bottom: 0 !important;
      cursor: pointer;
      font-size: 12px;
      color: var(--text-2);
      transition: all .2s ease;
      overflow: hidden;
      white-space: nowrap;
      box-sizing: border-box;
    }
    .file-upload-label:hover {
      border-color: var(--gold);
      color: var(--gold);
      background: var(--gold-dim);
    }
    .file-upload-label.has-file {
      border-style: solid;
      border-color: var(--green);
      color: var(--text);
      background: var(--green-dim);
    }
  </style>
</head>
<body>
<div class="app-container">
  @include('partials.sidebar')
  <main class="main-content">
    <header class="header">
      <div class="header-title">
        <h1><i class="bi bi-file-earmark-check-fill" style="color:var(--gold); margin-right:8px;"></i>Perizinan Siswa &amp; Guru</h1>
        <p>Pencatatan izin Sakit, Izin Keperluan, Dispensasi, Dinas Luar, &amp; Pulang Awal yang terhubung langsung ke absensi harian.</p>
      </div>
      @include('partials.header_actions')
    </header>

    @if(session('success'))<div class="alert-success" style="margin-bottom:18px;"><i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error" style="margin-bottom:18px;"><i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}</div>@endif
    @if(isset($errors) && $errors->any())<div class="alert-error" style="margin-bottom:18px;">@foreach($errors->all() as $err)<div><i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>{{ $err }}</div>@endforeach</div>@endif

    {{-- TOOLBAR & TOMBOL TOGGLE CATAT PERIZINAN BARU --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; flex-wrap:wrap; gap:10px;">
      <div style="font-size:13px; font-weight:700; color:var(--text-2);">
        <i class="bi bi-file-earmark-medical" style="margin-right:4px;"></i> Pencatatan perizinan resmi siswa &amp; guru
      </div>
      <button type="button" id="btnToggleFormIzin" onclick="toggleFormIzin()" class="btn btn-gold" style="height:38px; padding:0 16px; font-size:12.5px; font-weight:800; display:inline-flex; align-items:center; gap:6px;">
        <i class="bi bi-plus-circle-fill" id="iconToggleIzin"></i>
        <span id="textToggleIzin">Catat Perizinan Baru</span>
      </button>
    </div>

    {{-- PANEL CATAT PERIZINAN BARU (HIDDEN DEFAULT / TOGGLE) --}}
    <div class="izin-form-card" id="panelFormIzin" style="display:none; margin-bottom:24px; animation:fadeIn 0.25s ease;">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:20px; padding-bottom:14px; border-bottom:1px solid var(--border);">
        <div style="display:flex; align-items:center; gap:8px;">
          <div style="width:32px; height:32px; border-radius:8px; background:var(--green-dim); color:var(--green); display:flex; align-items:center; justify-content:center; font-size:16px;">
            <i class="bi bi-plus-circle-fill"></i>
          </div>
          <div>
            <h3 style="font-size:15px; font-weight:800; color:var(--text); margin:0;">Catat Perizinan Baru</h3>
            <div style="font-size:11.5px; color:var(--text-3);">Pilih kategori siswa atau guru untuk mendata izin beserta bukti pendukung.</div>
          </div>
        </div>
        
        <div style="display:flex; align-items:center; gap:8px;">
          {{-- Kategori Toggle Segmented --}}
          <div style="display:flex; background:var(--bg-3); border:1px solid var(--border-2); border-radius:12px; padding:4px; gap:4px;">
            <button type="button" class="btn-toggle-kat active" id="btnKatSiswa" onclick="switchKategori('siswa')">
              <i class="bi bi-people-fill"></i> Peserta Didik (Siswa)
            </button>
            <button type="button" class="btn-toggle-kat" id="btnKatGuru" onclick="switchKategori('guru')">
              <i class="bi bi-person-badge-fill"></i> Guru &amp; Pegawai
            </button>
          </div>

          {{-- Tombol Tutup --}}
          <button type="button" onclick="toggleFormIzin(false)" class="btn btn-outline" style="height:32px; width:32px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:6px; color:var(--text-3);" title="Tutup Form">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>

      <form action="/izin-siswa" method="POST" enctype="multipart/form-data" id="formPerizinan">
        @csrf
        <input type="hidden" name="kategori" id="formKategori" value="siswa" />

        {{-- BARIS 1: IDENTITAS, TANGGAL & JENIS IZIN --}}
        <div class="izin-form-row-1">
          
          {{-- 1. PEMILIH ORANG (SEARCHABLE AUTOCOMPLETE) --}}
          <div class="form-group person-picker-wrap" style="margin-bottom:0;">
            <div class="form-field-label">
              <span id="labelPilihPerson">PILIH SISWA <span style="color:var(--red);">*</span></span>
              <span class="form-field-hint" id="hintPilihPerson">Cari Nama / NIS / Kelas</span>
            </div>
            <input type="hidden" name="siswa_id" id="inputSelectedSiswaId" required />
            <input type="hidden" name="guru_id" id="inputSelectedGuruId" disabled />
            
            <div id="personPickerTrigger" class="person-picker-trigger" onclick="togglePersonPickerDropdown()">
              <div id="personSelectedView" style="display:none; align-items:center; justify-content:space-between; width:100%;">
                <div style="display:flex; align-items:center; gap:8px; overflow:hidden;">
                  <img id="selectedPersonFoto" src="/img/user-default.png" alt="Foto" style="width:26px; height:26px; border-radius:50%; object-fit:cover; border:1.5px solid var(--gold); flex-shrink:0;" />
                  <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    <strong id="selectedPersonNama" style="color:var(--text); font-size:13px;"></strong>
                    <span id="selectedPersonMeta" style="font-size:11px; color:var(--gold); font-family:var(--font-mono); margin-left:6px; font-weight:700;"></span>
                  </div>
                </div>
                <button type="button" class="btn-clear-person" onclick="clearSelectedPerson(event)" title="Ganti Pilihan">
                  <i class="bi bi-x-circle-fill"></i>
                </button>
              </div>
              <div id="personPlaceholderView" style="display:flex; align-items:center; gap:8px; color:var(--text-3); font-size:12.5px;">
                <i class="bi bi-search" style="color:var(--gold);"></i>
                <span id="personPlaceholderText">Ketik nama, NIS, atau kelas...</span>
              </div>
            </div>

            <!-- Dropdown List Panel -->
            <div id="personDropdownPanel" class="person-dropdown-panel">
              <div style="padding:8px 10px; border-bottom:1px solid var(--border-2); background:var(--bg-3);">
                <div style="position:relative;">
                  <i class="bi bi-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--gold); font-size:12px;"></i>
                  <input type="text" id="personSearchBox" placeholder="Ketik untuk mencari..." oninput="filterPersonPickerList(this.value)" style="width:100%; padding-left:32px; height:34px; font-size:12px; border-radius:var(--r-sm);" autocomplete="off" />
                </div>
              </div>
              
              <div id="personListContainer" class="person-list-container">
                {{-- Siswa List --}}
                @foreach($siswas as $s)
                  @php
                    $rombelNama = ($s->siswaRombels && $s->siswaRombels->first() && $s->siswaRombels->first()->rombel) ? $s->siswaRombels->first()->rombel->nama_rombel : 'Tanpa Rombel';
                  @endphp
                  <div class="person-picker-item picker-item-siswa" 
                       data-id="{{ $s->id }}" 
                       data-nama="{{ strtolower($s->nama) }}" 
                       data-nis="{{ strtolower($s->nis) }}" 
                       data-rombel="{{ strtolower($rombelNama) }}" 
                       onclick="selectSiswaItem('{{ $s->id }}', '{{ addslashes($s->nama) }}', '{{ $s->nis }}', '{{ $rombelNama }}', '{{ $s->foto_url }}')">
                    <div style="display:flex; align-items:center; gap:8px;">
                      <div class="avatar-circle avatar-sm gold-border">
                        <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}" class="avatar-img" />
                      </div>
                      <div>
                        <div style="font-weight:700; font-size:12.5px; color:var(--text);">{{ $s->nama }}</div>
                        <div style="font-size:11px; color:var(--text-3); font-family:var(--font-mono);">NIS: {{ $s->nis }}</div>
                      </div>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                      <span class="badge" style="background:var(--gold-dim); color:var(--gold); border:1px solid rgba(202,138,4,0.2); font-size:10.5px; padding:1px 6px;">
                        {{ $rombelNama }}
                      </span>
                    </div>
                  </div>
                @endforeach

                {{-- Guru List --}}
                @foreach($gurus as $g)
                  <div class="person-picker-item picker-item-guru" 
                       style="display:none;"
                       data-id="{{ $g->id }}" 
                       data-nama="{{ strtolower($g->nama) }}" 
                       data-nip="{{ strtolower($g->nip ?? '') }}" 
                       data-jabatan="{{ strtolower($g->jabatan ?? '') }}" 
                       onclick="selectGuruItem('{{ $g->id }}', '{{ addslashes($g->nama) }}', '{{ $g->nip ?? '-' }}', '{{ $g->jabatan ?? 'Guru' }}', '{{ $g->foto_url ?? '/img/user-default.png' }}')">
                    <div style="display:flex; align-items:center; gap:8px;">
                      <div class="avatar-circle avatar-sm gold-border">
                        <img src="{{ $g->foto_url ?? '/img/user-default.png' }}" alt="{{ $g->nama }}" class="avatar-img" />
                      </div>
                      <div>
                        <div style="font-weight:700; font-size:12.5px; color:var(--text);">{{ $g->nama }}</div>
                        <div style="font-size:11px; color:var(--text-3); font-family:var(--font-mono);">NIP: {{ $g->nip ?? 'Non-NIP' }}</div>
                      </div>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                      <span class="badge" style="background:var(--green-dim); color:var(--green); border:1px solid rgba(22,163,74,0.2); font-size:10.5px; padding:1px 6px;">
                        {{ $g->jabatan ?? 'Guru' }}
                      </span>
                    </div>
                  </div>
                @endforeach

                <div id="pickerEmptyMsg" style="display:none; padding:16px; text-align:center; color:var(--text-3); font-size:12px;">
                  <i class="bi bi-search" style="margin-right:4px;"></i> Data tidak ditemukan
                </div>
              </div>
            </div>
          </div>

          {{-- 2. TANGGAL IZIN --}}
          <div class="form-group" style="margin-bottom:0;">
            <div class="form-field-label">
              <span>TANGGAL IZIN <span style="color:var(--red);">*</span></span>
            </div>
            <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}" style="width:100%; height:42px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px; font-weight:600;" />
          </div>

          {{-- 3. JENIS PERIZINAN --}}
          <div class="form-group" style="margin-bottom:0;">
            <div class="form-field-label">
              <span>JENIS PERIZINAN <span style="color:var(--red);">*</span></span>
            </div>
            <select name="jenis" id="selectJenisIzin" required style="width:100%; height:42px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px; font-weight:700;">
              <option value="sakit">Sakit</option>
              <option value="izin">Izin Keperluan</option>
              <option value="dispensasi">Dispensasi (Lomba / Tugas)</option>
              <option value="pulang_cepat">Pulang Cepat (Lebih Awal)</option>
            </select>
          </div>

        </div>

        {{-- BARIS 2: KETERANGAN, UPLOAD BUKTI & TOMBOL SIMPAN --}}
        <div class="izin-form-row-2">
          
          {{-- 4. KETERANGAN ALASAN --}}
          <div class="form-group" style="margin-bottom:0;">
            <div class="form-field-label">
              <span>KETERANGAN ALASAN</span>
              <span class="form-field-hint">Opsional</span>
            </div>
            <input type="text" name="keterangan" id="inputKeterangan" placeholder="Contoh: Demam tinggi / Surat dokter terlampir" style="width:100%; height:42px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:0 12px; color:var(--text); font-size:13px;" />
          </div>

          {{-- 5. UPLOAD DOKUMEN BUKTI PENDUKUNG --}}
          <div class="form-group" style="margin-bottom:0;">
            <div class="form-field-label">
              <span>BERKAS PENDUKUNG</span>
              <span class="form-field-hint">PDF / Foto Max 5MB</span>
            </div>
            <div class="upload-btn-wrap">
              <input type="file" name="file_pendukung" id="inputFilePendukung" accept=".pdf,.jpg,.jpeg,.png,.webp" onchange="onFileSelected(this)" style="display:none !important;" />
              <label for="inputFilePendukung" class="file-upload-label" id="fileUploadLabel">
                <div style="display:flex; align-items:center; gap:6px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; max-width:calc(100% - 70px);">
                  <i class="bi bi-paperclip" style="color:var(--gold); font-size:15px; flex-shrink:0;"></i>
                  <span id="fileUploadText" style="text-overflow:ellipsis; overflow:hidden; font-weight:600; font-size:12px;">Pilih Surat / Foto</span>
                </div>
                <span class="badge" style="background:var(--bg-2); border:1px solid var(--border-2); color:var(--text); font-size:10.5px; padding:3px 8px; flex-shrink:0; font-weight:800;">Browse</span>
              </label>
              <button type="button" id="btnClearFile" onclick="clearSelectedFile()" style="display:none; background:none; border:none; color:var(--red); cursor:pointer; padding:4px 6px; font-size:16px;" title="Batalkan File">
                <i class="bi bi-x-circle-fill"></i>
              </button>
            </div>
          </div>

          {{-- 6. TOMBOL SIMPAN --}}
          <div class="form-group" style="margin-bottom:0;">
            <div class="form-field-label" style="visibility:hidden; margin-bottom:6px;">
              <span>SIMPAN</span>
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%; height:42px; font-weight:800; font-size:13px; display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:var(--r-sm); white-space:nowrap; cursor:pointer;">
              <i class="bi bi-check2-circle" style="font-size:16px;"></i> Simpan Izin
            </button>
          </div>

        </div>
      </form>
    </div>

    {{-- RIWAYAT PERIZINAN MULTI-TAB --}}
    <div class="panel">
      <div class="izin-nav-tabs">
        <button class="izin-tab-btn {{ !request()->has('page_guru') ? 'active' : '' }}" id="tabBtnSiswa" onclick="switchRiwayatTab('tab-riwayat-siswa', this)">
          <i class="bi bi-people-fill"></i> Riwayat Izin Siswa ({{ $izins->total() }})
        </button>
        <button class="izin-tab-btn {{ request()->has('page_guru') ? 'active' : '' }}" id="tabBtnGuru" onclick="switchRiwayatTab('tab-riwayat-guru', this)">
          <i class="bi bi-person-badge-fill"></i> Riwayat Izin Guru ({{ $izinGurus->total() }})
        </button>
      </div>

      {{-- TAB 1: RIWAYAT IZIN SISWA --}}
      <div class="izin-tab-pane {{ !request()->has('page_guru') ? 'active' : '' }}" id="tab-riwayat-siswa" style="border:1px solid var(--border); border-radius:var(--r-md); overflow:hidden; background:var(--bg-2); box-shadow:var(--shadow-sm); margin-top:14px;">
        <div style="padding:12px 18px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
          <div style="font-weight:800; font-size:14px; color:var(--text); display:flex; align-items:center; gap:8px;">
            <i class="bi bi-people-fill" style="color:var(--gold);"></i>
            <span>Daftar Riwayat Perizinan Siswa</span>
          </div>
          <div style="position:relative; width:280px;">
            <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--gold); font-size:12px;"></i>
            <input type="text" placeholder="Cari nama, NIS, jenis..." oninput="filterTableIzin('tableIzinSiswa', this.value)" style="width:100%; padding-left:34px; height:36px; font-size:12px; border-radius:var(--r-sm); background:var(--bg-2); border:1px solid var(--border-2);" />
          </div>
        </div>

        <div class="table-responsive" style="overflow-x:auto;">
          <table class="data-table" id="tableIzinSiswa">
            <thead>
              <tr>
                <th style="width:42px; text-align:center;">No</th>
                <th>Tanggal</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Jenis Izin</th>
                <th style="text-align:center;">Status</th>
                <th>Keterangan</th>
                <th style="text-align:center;">Bukti Dokumen</th>
                <th>Disetujui Oleh</th>
                <th style="width:70px; text-align:center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($izins as $idx => $izin)
                <tr>
                  <td style="text-align:center; font-weight:700; color:var(--text-3); font-size:12px;">{{ ($izins instanceof \Illuminate\Pagination\LengthAwarePaginator ? $izins->firstItem() : 1) + $idx }}</td>
                  <td>
                    <div style="font-family:var(--font-mono); color:var(--text); font-weight:800; font-size:12.5px;">{{ $izin->tanggal }}</div>
                  </td>
                  <td style="font-family:var(--font-mono); font-size:12.5px; font-weight:700; color:var(--text-2);">{{ $izin->siswa->nis ?? '-' }}</td>
                  <td>
                    <strong style="color:var(--text); font-size:13px;">{{ $izin->siswa->nama ?? '-' }}</strong>
                  </td>
                  <td>
                    @php
                      $badgeStyle = match($izin->jenis) {
                        'sakit' => 'background:rgba(59,130,246,0.12); color:#2563EB;',
                        'izin' => 'background:rgba(245,158,11,0.12); color:#D97706;',
                        'dispensasi' => 'background:rgba(16,185,129,0.12); color:#059669;',
                        'pulang_cepat', 'pulang_awal' => 'background:rgba(168,85,247,0.12); color:#9333EA;',
                        default => 'background:var(--bg-3); color:var(--text-2);'
                      };
                    @endphp
                    <span class="badge" style="{{ $badgeStyle }} font-weight:800; font-size:11px; text-transform:uppercase;">
                      {{ str_replace('_', ' ', $izin->jenis) }}
                    </span>
                  </td>
                  <td style="text-align:center;">
                    <span class="badge" style="background:var(--green-dim); color:var(--green); font-weight:800; font-size:11px; text-transform:uppercase;">
                      {{ $izin->status }}
                    </span>
                  </td>
                  <td>
                    <span style="font-size:12px; color:var(--text-2);">{{ $izin->keterangan ?: '-' }}</span>
                  </td>
                  <td style="text-align:center;">
                    @if($izin->file_pendukung)
                      <a href="{{ asset('storage/' . $izin->file_pendukung) }}" target="_blank" class="btn btn-sm btn-outline" style="padding:3px 8px; font-size:11px; font-weight:700; display:inline-flex; align-items:center; gap:4px; border-color:rgba(202,138,4,0.3); color:var(--gold);" title="Buka berkas bukti">
                        <i class="bi bi-file-earmark-medical-fill"></i> Lihat Bukti
                      </a>
                    @else
                      <span style="color:var(--text-3); font-size:11px;">-</span>
                    @endif
                  </td>
                  <td>
                    <span style="font-size:12px; color:var(--text-2);">{{ $izin->disetujui_oleh ?: '-' }}</span>
                  </td>
                  <td style="text-align:center;">
                    <form action="{{ route('izin-siswa.destroy', $izin->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan perizinan siswa ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn-icon" style="color:var(--red);" title="Hapus Catatan Izin">
                        <i class="bi bi-trash-fill"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="10" style="text-align:center; padding:30px; color:var(--text-3);">
                    <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px; opacity:0.6;"></i>
                    Belum ada catatan perizinan siswa.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($izins instanceof \Illuminate\Pagination\LengthAwarePaginator && $izins->hasPages())
          <div style="padding:14px 18px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; background:var(--bg-2);">
            <div style="font-size:12.5px; color:var(--text-2); font-weight:600;">
              Menampilkan <strong style="color:var(--gold);">{{ $izins->firstItem() }}</strong> – <strong style="color:var(--gold);">{{ $izins->lastItem() }}</strong> dari <strong style="color:var(--text);">{{ $izins->total() }}</strong> izin siswa
            </div>
            <div>
              {{ $izins->appends(request()->except('page_siswa'))->links() }}
            </div>
          </div>
        @endif
      </div>

      {{-- TAB 2: RIWAYAT IZIN GURU --}}
      <div class="izin-tab-pane {{ request()->has('page_guru') ? 'active' : '' }}" id="tab-riwayat-guru" style="border:1px solid var(--border); border-radius:var(--r-md); overflow:hidden; background:var(--bg-2); box-shadow:var(--shadow-sm); margin-top:14px;">
        <div style="padding:12px 18px; border-bottom:1px solid var(--border); background:var(--surface); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
          <div style="font-weight:800; font-size:14px; color:var(--text); display:flex; align-items:center; gap:8px;">
            <i class="bi bi-person-badge-fill" style="color:var(--gold);"></i>
            <span>Daftar Riwayat Perizinan Guru &amp; Pegawai</span>
          </div>
          <div style="position:relative; width:280px;">
            <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--gold); font-size:12px;"></i>
            <input type="text" placeholder="Cari nama, NIP, jenis..." oninput="filterTableIzin('tableIzinGuru', this.value)" style="width:100%; padding-left:34px; height:36px; font-size:12px; border-radius:var(--r-sm); background:var(--bg-2); border:1px solid var(--border-2);" />
          </div>
        </div>

        <div class="table-responsive" style="overflow-x:auto;">
          <table class="data-table" id="tableIzinGuru">
            <thead>
              <tr>
                <th style="width:42px; text-align:center;">No</th>
                <th>Tanggal</th>
                <th>NIP</th>
                <th>Nama Guru / Pegawai</th>
                <th>Jenis Izin</th>
                <th style="text-align:center;">Status</th>
                <th>Keterangan</th>
                <th style="text-align:center;">Bukti Dokumen</th>
                <th>Disetujui Oleh</th>
                <th style="width:70px; text-align:center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($izinGurus as $idx => $izinG)
                <tr>
                  <td style="text-align:center; font-weight:700; color:var(--text-3); font-size:12px;">{{ ($izinGurus instanceof \Illuminate\Pagination\LengthAwarePaginator ? $izinGurus->firstItem() : 1) + $idx }}</td>
                  <td>
                    <div style="font-family:var(--font-mono); color:var(--text); font-weight:800; font-size:12.5px;">{{ $izinG->tanggal }}</div>
                  </td>
                  <td style="font-family:var(--font-mono); font-size:12.5px; font-weight:700; color:var(--text-2);">{{ $izinG->guru->nip ?? 'Non-NIP' }}</td>
                  <td>
                    <strong style="color:var(--text); font-size:13px;">{{ $izinG->guru->nama ?? '-' }}</strong>
                    <div style="font-size:11px; color:var(--text-3);">{{ $izinG->guru->jabatan ?? 'Guru' }}</div>
                  </td>
                  <td>
                    @php
                      $badgeStyleGuru = match($izinG->jenis) {
                        'sakit' => 'background:rgba(59,130,246,0.12); color:#2563EB;',
                        'izin' => 'background:rgba(245,158,11,0.12); color:#D97706;',
                        'dinas_luar' => 'background:rgba(16,185,129,0.12); color:#059669;',
                        'cuti' => 'background:rgba(6,182,212,0.12); color:#0891B2;',
                        'pulang_cepat' => 'background:rgba(168,85,247,0.12); color:#9333EA;',
                        default => 'background:var(--bg-3); color:var(--text-2);'
                      };
                    @endphp
                    <span class="badge" style="{{ $badgeStyleGuru }} font-weight:800; font-size:11px; text-transform:uppercase;">
                      {{ str_replace('_', ' ', $izinG->jenis) }}
                    </span>
                  </td>
                  <td style="text-align:center;">
                    <span class="badge" style="background:var(--green-dim); color:var(--green); font-weight:800; font-size:11px; text-transform:uppercase;">
                      {{ $izinG->status }}
                    </span>
                  </td>
                  <td>
                    <span style="font-size:12px; color:var(--text-2);">{{ $izinG->keterangan ?: '-' }}</span>
                  </td>
                  <td style="text-align:center;">
                    @if($izinG->file_pendukung)
                      <a href="{{ asset('storage/' . $izinG->file_pendukung) }}" target="_blank" class="btn btn-sm btn-outline" style="padding:3px 8px; font-size:11px; font-weight:700; display:inline-flex; align-items:center; gap:4px; border-color:rgba(202,138,4,0.3); color:var(--gold);" title="Buka berkas bukti">
                        <i class="bi bi-file-earmark-medical-fill"></i> Lihat Bukti
                      </a>
                    @else
                      <span style="color:var(--text-3); font-size:11px;">-</span>
                    @endif
                  </td>
                  <td>
                    <span style="font-size:12px; color:var(--text-2);">{{ $izinG->disetujui_oleh ?: '-' }}</span>
                  </td>
                  <td style="text-align:center;">
                    <form action="{{ route('izin-guru.destroy', $izinG->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan perizinan guru ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn-icon" style="color:var(--red);" title="Hapus Catatan Izin Guru">
                        <i class="bi bi-trash-fill"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="10" style="text-align:center; padding:30px; color:var(--text-3);">
                    <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:8px; opacity:0.6;"></i>
                    Belum ada catatan perizinan guru.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($izinGurus instanceof \Illuminate\Pagination\LengthAwarePaginator && $izinGurus->hasPages())
          <div style="padding:14px 18px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; background:var(--bg-2);">
            <div style="font-size:12.5px; color:var(--text-2); font-weight:600;">
              Menampilkan <strong style="color:var(--gold);">{{ $izinGurus->firstItem() }}</strong> – <strong style="color:var(--gold);">{{ $izinGurus->lastItem() }}</strong> dari <strong style="color:var(--text);">{{ $izinGurus->total() }}</strong> izin guru
            </div>
            <div>
              {{ $izinGurus->appends(request()->except('page_guru'))->links() }}
            </div>
          </div>
        @endif
      </div>

    </div>
  </main>
</div>

<script>
  let currentKategori = 'siswa';

  // ── Switch Kategori Form (Siswa vs Guru) ──
  function switchKategori(kat, syncRiwayat = true) {
    currentKategori = kat;
    document.getElementById('formKategori').value = kat;

    const btnSiswa = document.getElementById('btnKatSiswa');
    const btnGuru = document.getElementById('btnKatGuru');
    const inputSiswaId = document.getElementById('inputSelectedSiswaId');
    const inputGuruId = document.getElementById('inputSelectedGuruId');
    const labelPerson = document.getElementById('labelPilihPerson');
    const hintPerson = document.getElementById('hintPilihPerson');
    const placeholderText = document.getElementById('personPlaceholderText');
    const selectJenis = document.getElementById('selectJenisIzin');
    const inputKeterangan = document.getElementById('inputKeterangan');

    if (kat === 'siswa') {
      btnSiswa.classList.add('active');
      btnGuru.classList.remove('active');

      inputSiswaId.disabled = false;
      inputSiswaId.required = true;
      inputGuruId.disabled = true;
      inputGuruId.required = false;

      labelPerson.innerHTML = 'PILIH SISWA <span style="color:var(--red);">*</span>';
      hintPerson.textContent = 'Cari Nama / NIS / Kelas';
      placeholderText.textContent = 'Ketik nama, NIS, atau kelas...';
      inputKeterangan.placeholder = 'Contoh: Demam tinggi / Surat dokter terlampir';

      selectJenis.innerHTML = `
        <option value="sakit">Sakit</option>
        <option value="izin">Izin Keperluan</option>
        <option value="dispensasi">Dispensasi (Lomba / Tugas)</option>
        <option value="pulang_cepat">Pulang Cepat (Lebih Awal)</option>
      `;

      if (syncRiwayat) {
        const tabBtnSiswa = document.getElementById('tabBtnSiswa');
        if (tabBtnSiswa) switchRiwayatTab('tab-riwayat-siswa', tabBtnSiswa, false);
      }
    } else {
      btnGuru.classList.add('active');
      btnSiswa.classList.remove('active');

      inputGuruId.disabled = false;
      inputGuruId.required = true;
      inputSiswaId.disabled = true;
      inputSiswaId.required = false;

      labelPerson.innerHTML = 'PILIH GURU / PEGAWAI <span style="color:var(--red);">*</span>';
      hintPerson.textContent = 'Cari Nama / NIP / Jabatan';
      placeholderText.textContent = 'Ketik nama guru, NIP, atau jabatan...';
      inputKeterangan.placeholder = 'Contoh: Tugas Dinas Luar / Sakit / Urusan Keluarga';

      selectJenis.innerHTML = `
        <option value="sakit">Sakit</option>
        <option value="izin">Izin Keperluan Pribadi</option>
        <option value="dinas_luar">Dinas Luar (Tugas / Pelatihan)</option>
        <option value="cuti">Cuti Resmi (Tahunan / Melahirkan)</option>
        <option value="pulang_cepat">Pulang Cepat (Lebih Awal)</option>
      `;

      if (syncRiwayat) {
        const tabBtnGuru = document.getElementById('tabBtnGuru');
        if (tabBtnGuru) switchRiwayatTab('tab-riwayat-guru', tabBtnGuru, false);
      }
    }

    clearSelectedPerson();
  }

  // ── Searchable Picker Dropdown Functions ──
  function togglePersonPickerDropdown() {
    const panel = document.getElementById('personDropdownPanel');
    const trigger = document.getElementById('personPickerTrigger');
    const isOpen = panel.classList.contains('open');

    if (isOpen) {
      closePersonPickerDropdown();
    } else {
      panel.classList.add('open');
      trigger.classList.add('focused');
      const searchBox = document.getElementById('personSearchBox');
      searchBox.value = '';
      filterPersonPickerList('');
      setTimeout(() => searchBox.focus(), 50);
    }
  }

  function closePersonPickerDropdown() {
    document.getElementById('personDropdownPanel').classList.remove('open');
    document.getElementById('personPickerTrigger').classList.remove('focused');
  }

  function filterPersonPickerList(query) {
    const q = (query || '').toLowerCase().trim();
    const isSiswa = currentKategori === 'siswa';
    const targetClass = isSiswa ? '.picker-item-siswa' : '.picker-item-guru';
    const otherClass = isSiswa ? '.picker-item-guru' : '.picker-item-siswa';

    document.querySelectorAll(otherClass).forEach(el => el.style.display = 'none');

    let visibleCount = 0;
    document.querySelectorAll(targetClass).forEach(item => {
      let match = true;
      if (q) {
        if (isSiswa) {
          const nama = item.dataset.nama || '';
          const nis = item.dataset.nis || '';
          const rombel = item.dataset.rombel || '';
          match = nama.includes(q) || nis.includes(q) || rombel.includes(q);
        } else {
          const nama = item.dataset.nama || '';
          const nip = item.dataset.nip || '';
          const jabatan = item.dataset.jabatan || '';
          match = nama.includes(q) || nip.includes(q) || jabatan.includes(q);
        }
      }
      item.style.display = match ? 'flex' : 'none';
      if (match) visibleCount++;
    });

    const emptyMsg = document.getElementById('pickerEmptyMsg');
    if (emptyMsg) emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
  }

  function selectSiswaItem(id, nama, nis, rombel, fotoUrl) {
    document.getElementById('inputSelectedSiswaId').value = id;
    document.getElementById('selectedPersonNama').textContent = nama;
    document.getElementById('selectedPersonMeta').textContent = `NIS: ${nis} · ${rombel}`;
    document.getElementById('selectedPersonFoto').src = fotoUrl || '/img/user-default.png';

    document.getElementById('personPlaceholderView').style.display = 'none';
    document.getElementById('personSelectedView').style.display = 'flex';

    closePersonPickerDropdown();
  }

  function selectGuruItem(id, nama, nip, jabatan, fotoUrl) {
    document.getElementById('inputSelectedGuruId').value = id;
    document.getElementById('selectedPersonNama').textContent = nama;
    document.getElementById('selectedPersonMeta').textContent = `${nip !== '-' ? 'NIP: ' + nip : jabatan}`;
    document.getElementById('selectedPersonFoto').src = fotoUrl || '/img/user-default.png';

    document.getElementById('personPlaceholderView').style.display = 'none';
    document.getElementById('personSelectedView').style.display = 'flex';

    closePersonPickerDropdown();
  }

  function clearSelectedPerson(e) {
    if (e) e.stopPropagation();
    document.getElementById('inputSelectedSiswaId').value = '';
    document.getElementById('inputSelectedGuruId').value = '';
    document.getElementById('personSelectedView').style.display = 'none';
    document.getElementById('personPlaceholderView').style.display = 'flex';
  }

  // ── File Upload Handler ──
  function onFileSelected(input) {
    const label = document.getElementById('fileUploadLabel');
    const text = document.getElementById('fileUploadText');
    const clearBtn = document.getElementById('btnClearFile');

    if (input.files && input.files[0]) {
      const file = input.files[0];
      text.textContent = file.name;
      label.classList.add('has-file');
      clearBtn.style.display = 'block';

      // Jika file berupa gambar dan belum dicrop, tawarkan modal crop dengan rasio bebas
      if (file.type.startsWith('image/') && !file.name.includes('_cropped')) {
        initPhotoCrop(input, null, 'free', 'Sesuaikan & Potong Berkas Surat Izin');
      }
    } else {
      clearSelectedFile();
    }
  }

  function clearSelectedFile() {
    const input = document.getElementById('inputFilePendukung');
    const label = document.getElementById('fileUploadLabel');
    const text = document.getElementById('fileUploadText');
    const clearBtn = document.getElementById('btnClearFile');

    input.value = '';
    text.textContent = 'Pilih Surat / Foto Bukti';
    label.classList.remove('has-file');
    clearBtn.style.display = 'none';
  }

  // ── Riwayat Multi-Tab ──
  function switchRiwayatTab(tabId, btn, syncForm = true) {
    document.querySelectorAll('.izin-tab-pane').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.izin-tab-btn').forEach(el => el.classList.remove('active'));

    const target = document.getElementById(tabId);
    if (target) target.classList.add('active');
    if (btn) btn.classList.add('active');

    if (syncForm) {
      if (tabId === 'tab-riwayat-siswa' && currentKategori !== 'siswa') {
        switchKategori('siswa', false);
      } else if (tabId === 'tab-riwayat-guru' && currentKategori !== 'guru') {
        switchKategori('guru', false);
      }
    }
  }

  // ── Filter Tabel Riwayat ──
  function filterTableIzin(tableId, query) {
    const q = (query || '').toLowerCase().trim();
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
    rows.forEach(r => {
      const text = r.innerText.toLowerCase();
      r.style.display = text.includes(q) ? '' : 'none';
    });
  }

  // ── Toggle Form Pencatatan Izin ──
  function toggleFormIzin(forceState) {
    const panel = document.getElementById('panelFormIzin');
    const text = document.getElementById('textToggleIzin');
    const icon = document.getElementById('iconToggleIzin');
    const btn = document.getElementById('btnToggleFormIzin');
    if (!panel) return;

    const isVisible = (panel.style.display !== 'none' && panel.style.display !== '');
    const targetState = (forceState !== undefined) ? forceState : !isVisible;

    if (targetState) {
      panel.style.display = 'block';
      if (text) text.innerText = 'Tutup Form';
      if (icon) icon.className = 'bi bi-x-lg';
      if (btn) btn.classList.add('active');
      panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
      panel.style.display = 'none';
      if (text) text.innerText = 'Catat Perizinan Baru';
      if (icon) icon.className = 'bi bi-plus-circle-fill';
      if (btn) btn.classList.remove('active');
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    switchKategori('siswa');
  });
</script>

@include('partials.crop_modal')

</body>
</html>
