<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Presensi Manual (Lupa Kartu) — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <style>
    /* Segmented Control / Radio Button Tab Styling */
    .segmented-control {
      display: flex;
      background: var(--bg-3);
      padding: 4px;
      border-radius: var(--r-sm);
      border: 1px solid var(--border);
      margin-bottom: 20px;
    }
    .segmented-control label {
      flex: 1;
      text-align: center;
      padding: 8px;
      cursor: pointer;
      font-weight: 700;
      font-size: 13.5px;
      color: var(--text-2);
      border-radius: calc(var(--r-sm) - 4px);
      transition: all .2s;
    }
    .segmented-control input[type="radio"] {
      display: none;
    }
    .segmented-control input[type="radio"]:checked + label {
      background: #000000;
      color: #FFFFFF;
      box-shadow: var(--shadow-sm);
    }

    /* Combobox Styles */
    .picker-wrap {
      position: relative;
      width: 100%;
    }
    .picker-trigger {
      width: 100%;
      height: 44px;
      background: var(--bg);
      border: 1.5px solid var(--border-2);
      border-radius: var(--r-sm);
      padding: 0 12px;
      display: flex;
      align-items: center;
      cursor: pointer;
      transition: all .2s ease;
      user-select: none;
    }
    .picker-trigger:hover, .picker-trigger.focused {
      border-color: #000000;
      box-shadow: 0 0 0 2px rgba(0,0,0,0.15);
    }
    .btn-clear-selection {
      background: transparent;
      border: none;
      color: var(--text-3);
      font-size: 16px;
      cursor: pointer;
      padding: 2px 4px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: color .15s;
    }
    .btn-clear-selection:hover {
      color: var(--red);
    }
    .dropdown-panel {
      display: none;
      position: absolute;
      top: calc(100% + 6px);
      left: 0;
      right: 0;
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: var(--r-md);
      box-shadow: var(--shadow-lg);
      z-index: 1050;
      overflow: hidden;
    }
    .dropdown-panel.open {
      display: block;
      animation: modalFadeIn .15s ease;
    }
    .list-container {
      max-height: 280px;
      overflow-y: auto;
    }
    .picker-item {
      padding: 10px 14px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--border);
      cursor: pointer;
      transition: background .15s;
      gap: 12px;
    }
    .picker-item:hover {
      background: var(--surface);
    }
    
    /* Toggle switch for Sesi */
    .sesi-toggle-group {
      display: flex;
      gap: 12px;
    }
    .sesi-btn {
      flex: 1;
      padding: 10px 14px;
      border: 1.5px solid var(--border-2);
      border-radius: var(--r-sm);
      background: var(--bg);
      color: var(--text-2);
      font-weight: 700;
      font-size: 13px;
      cursor: pointer;
      text-align: center;
      transition: all .2s;
    }
    .sesi-btn.active-masuk {
      background: rgba(34,197,94,0.12);
      border-color: var(--green);
      color: var(--green);
    }
    .sesi-btn.active-pulang {
      background: rgba(59,130,246,0.12);
      border-color: var(--navy);
      color: var(--navy);
    }

    @keyframes modalFadeIn {
      from { opacity: 0; transform: translateY(-8px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
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
            <i class="bi bi-keyboard-fill" style="color:#000000; font-size:16px;"></i> Presensi Manual
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:11.5px; color:var(--text-3);">
            Pencatatan presensi darurat siswa &amp; guru
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
          @include('partials.header_actions')
        </div>
      </div>
    </div>

    @if(session('success'))<div class="alert-success" style="margin-bottom:18px;"><i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error" style="margin-bottom:18px;"><i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}</div>@endif
    @if(isset($errors) && $errors->any())<div class="alert-error" style="margin-bottom:18px;">@foreach($errors->all() as $err)<div><i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>{{ $err }}</div>@endforeach</div>@endif

    <div class="panel">
      <div class="panel-title">
        <span>Catat Presensi Manual</span>
      </div>
      <form action="/presensi-manual" method="POST" id="formPresensiManual">
        @csrf
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px 20px; margin-bottom: 20px;">
          
          <!-- 1. KATEGORI SEGMENTED CONTROL -->
          <div class="form-group">
            <label>Kategori Kehadiran <span style="color:var(--red);">*</span></label>
            <div class="segmented-control" style="height:42px; padding:3px; background:var(--bg-3); border:1.5px solid var(--border-2); border-radius:var(--r-sm); display:flex; gap:4px; box-sizing:border-box;">
              <input type="radio" name="kategori" id="katSiswa" value="siswa" checked onchange="switchKategori('siswa')">
              <label for="katSiswa" style="flex:1; text-align:center; padding:0 8px; line-height:34px; height:34px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.3px; cursor:pointer; margin-bottom:0; display:flex; align-items:center; justify-content:center;">Siswa</label>
              
              <input type="radio" name="kategori" id="katGuru" value="guru" onchange="switchKategori('guru')">
              <label for="katGuru" style="flex:1; text-align:center; padding:0 8px; line-height:34px; height:34px; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.3px; cursor:pointer; margin-bottom:0; display:flex; align-items:center; justify-content:center;">Guru & Pegawai</label>
            </div>
          </div>

          <!-- 2. PEMILIH (COMBOBOX) -->
          <div class="form-group picker-wrap" id="siswaPickerGroup">
            <label>Pilih Personel <span style="color:var(--red);">*</span></label>
            <input type="hidden" name="pemilik_id" id="selectedPemilikId" required />
            
            <div id="pickerTrigger" class="picker-trigger" style="height:42px; background:var(--bg-3);" onclick="togglePickerDropdown()">
              <div id="selectedView" style="display:none; align-items:center; justify-content:space-between; width:100%;">
                <div style="display:flex; align-items:center; gap:8px; overflow:hidden;">
                  <img id="selectedFoto" src="" alt="Foto" style="width:26px; height:26px; border-radius:50%; object-fit:cover; border:1.5px solid rgba(0,0,0,0.15); flex-shrink:0;" />
                  <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    <strong id="selectedNama" style="color:var(--text); font-size:13px;"></strong>
                    <span id="selectedMeta" style="font-size:11px; color:#000000; font-family:var(--font-mono); margin-left:6px; font-weight:700;"></span>
                  </div>
                </div>
                <button type="button" class="btn-clear-selection" onclick="clearSelection(event)">
                  <i class="bi bi-x-circle-fill"></i>
                </button>
              </div>
              <div id="placeholderView" style="display:flex; align-items:center; color:var(--text-3); font-size:13px;">
                <span id="pickerPlaceholderText">Pilih siswa atau cari nama...</span>
              </div>
            </div>

            <!-- Dropdown List Panel -->
            <div id="pickerDropdownPanel" class="dropdown-panel">
              <div style="padding:8px 10px; border-bottom:1px solid var(--border-2); background:var(--bg-3);">
                <div style="position:relative;">
                  <input type="text" id="searchBox" placeholder="Ketik nama, NIS, NIP, atau rombel..." oninput="filterPickerList(this.value)" style="width:100%; height:36px; font-size:12.5px; border-radius:var(--r-sm);" autocomplete="off" />
                </div>
              </div>
              
              <!-- Siswa Container -->
              <div id="siswaListContainer" class="list-container">
                @foreach($semuaSiswa as $s)
                  @php
                    $rombelNama = ($s->siswaRombels && $s->siswaRombels->first() && $s->siswaRombels->first()->rombel) ? $s->siswaRombels->first()->rombel->nama_rombel : 'Tanpa Rombel';
                  @endphp
                  <div class="picker-item" 
                       data-id="{{ $s->id }}" 
                       data-nama="{{ strtolower($s->nama) }}" 
                       data-nisn="{{ strtolower($s->nisn ?? '') }}" 
                       data-rombel="{{ strtolower($rombelNama) }}" 
                       onclick="selectPickerItem('{{ $s->id }}', '{{ addslashes($s->nama) }}', 'NISN: {{ $s->nisn ?: '-' }} · {{ $rombelNama }}', '{{ $s->foto_url }}')">
                    <div style="display:flex; align-items:center; gap:10px;">
                      <div class="avatar-circle avatar-sm gold-border">
                        <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}" class="avatar-img" />
                      </div>
                      <div>
                        <div style="font-weight:700; font-size:13px; color:var(--text);">{{ $s->nama }}</div>
                        <div style="font-size:11px; color:var(--text-3); font-family:var(--font-mono);">NISN: {{ $s->nisn ?: '-' }}</div>
                      </div>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                      <span class="btn btn-outline" style="padding:2px 8px; font-size:11px; pointer-events:none; font-weight:700; color:#000000; border-color:rgba(0,0,0,0.2);">
                        {{ $rombelNama }}
                      </span>
                    </div>
                  </div>
                @endforeach
              </div>

              <!-- Guru Container (Hidden initially) -->
              <div id="guruListContainer" class="list-container" style="display:none;">
                @foreach($semuaGuru as $g)
                  <div class="picker-item" 
                       data-id="{{ $g->id }}" 
                       data-nama="{{ strtolower($g->nama) }}" 
                       data-nip="{{ strtolower($g->nip) }}" 
                       data-jabatan="{{ strtolower($g->jabatan) }}" 
                       onclick="selectPickerItem('{{ $g->id }}', '{{ addslashes($g->nama) }}', 'NIP: {{ $g->nip ?? '-' }} · {{ $g->jabatan }}', '{{ $g->foto_url }}')">
                    <div style="display:flex; align-items:center; gap:10px;">
                      <div class="avatar-circle avatar-sm blue-border">
                        <img src="{{ $g->foto_url }}" alt="{{ $g->nama }}" class="avatar-img" />
                      </div>
                      <div>
                        <div style="font-weight:700; font-size:13px; color:var(--text);">{{ $g->nama }}</div>
                        <div style="font-size:11px; color:var(--text-3); font-family:var(--font-mono);">NIP: {{ $g->nip ?? '-' }}</div>
                      </div>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                      <span class="btn btn-outline" style="padding:2px 8px; font-size:11px; pointer-events:none; font-weight:700; color:var(--navy); border-color:rgba(59,130,246,0.3);">
                        {{ $g->jabatan }}
                      </span>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <!-- 3. SESI KEHADIRAN (MASUK / PULANG) -->
          <div class="form-group">
            <label>Sesi Presensi <span style="color:var(--red);">*</span></label>
            <input type="hidden" name="sesi" id="inputSesi" value="masuk" />
            <div class="sesi-toggle-group" style="height:42px; display:flex; gap:8px;">
              <button type="button" id="btnSesiMasuk" class="sesi-btn active-masuk" style="height:42px; display:inline-flex; align-items:center; justify-content:center;" onclick="setSesi('masuk')">
                Masuk
              </button>
              <button type="button" id="btnSesiPulang" class="sesi-btn" style="height:42px; display:inline-flex; align-items:center; justify-content:center;" onclick="setSesi('pulang')">
                Pulang
              </button>
            </div>
          </div>

          <!-- 4. KETERANGAN -->
          <div class="form-group">
            <label>Keterangan / Alasan <span style="color:var(--red);">*</span></label>
            <input type="text" name="keterangan" id="inputKeterangan" value="Terkendala Face ID / Verifikasi Manual" placeholder="Alasan presensi manual..." required style="width:100%; height:42px;" />
          </div>

        </div>

        <!-- Dedicated Bottom Action Row -->
        <div style="display:flex; justify-content:flex-end; border-top:1px solid var(--border-2); padding-top:16px;">
          <button type="submit" class="btn btn-gold" style="height:42px; padding:0 28px; font-size:13.5px; font-weight:800; display:inline-flex; align-items:center; gap:8px;">
            <i class="bi bi-save-fill"></i>Simpan Presensi
          </button>
        </div>
      </form>
    </div>
      </form>
    </div>

    <!-- 5. TABEL LOG HARI INI -->
    <div class="panel">
      <div class="panel-title" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:8px;">
          <span><i class="bi bi-journal-text" style="color:#000000; margin-right:4px;"></i>Log Presensi Manual Hari Ini</span>
          <span style="font-family:var(--font-mono);font-size:12px;color:#000000;font-weight:700;" id="logCountBadge">
            {{ $presensiManualHariIni->count() }} Entri
          </span>
        </div>
        <div style="position:relative; width:280px;">
          <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#000000; font-size:12px;"></i>
          <input type="text" placeholder="Cari nama, rombel, status..." oninput="filterLogPresensi(this.value)" style="width:100%; padding-left:34px; height:36px; font-size:12px; border-radius:var(--r-sm);" />
        </div>
      </div>
      <div class="table-responsive">
        <table class="data-table" id="tableLogPresensi">
          <thead>
            <tr>
              <th>Waktu</th>
              <th>Kategori</th>
              <th>Nama Lengkap</th>
              <th>Rombel / Jabatan</th>
              <th>Jam Masuk</th>
              <th>Jam Pulang</th>
              <th>Status</th>
              <th>Catatan</th>
            </tr>
          </thead>
          <tbody>
            @forelse($presensiManualHariIni as $log)
              @php
                $isSiswa = ($log->pemilik_type === 'siswa');
                $nama = $isSiswa ? ($log->siswa->nama ?? '-') : ($log->guru->nama ?? '-');
                $identitas = $isSiswa ? ($log->siswa->nisn ?? '-') : ($log->guru->nip ?? '-');
                $rombelJabatan = $isSiswa 
                  ? (($log->siswaRombel && $log->siswaRombel->rombel) ? $log->siswaRombel->rombel->nama_rombel : 'Tanpa Rombel')
                  : ($log->guru->jabatan ?? '-');
                $fotoUrl = $isSiswa ? ($log->siswa->foto_url ?? '') : ($log->guru->foto_url ?? '');
              @endphp
              <tr>
                <td style="font-family:var(--font-mono); color:var(--text-3); font-size:12px;">{{ $log->created_at->format('H:i:s') }}</td>
                <td>
                  <span style="font-size:11px; font-weight:700; color:var(--text); display:inline-flex; align-items:center; gap:4px;">
                    <i class="bi {{ $isSiswa ? 'bi-person' : 'bi-person-badge' }}" style="color:var(--text-3);"></i>
                    {{ $isSiswa ? 'Siswa' : 'Guru / Staf' }}
                  </span>
                </td>
                <td>
                  <div style="display:flex; align-items:center; gap:8px;">
                    <div class="avatar-circle avatar-sm {{ $isSiswa ? 'gold-border' : 'blue-border' }}">
                      <img src="{{ $fotoUrl }}" alt="Profile" class="avatar-img" />
                    </div>
                    <div>
                      <strong style="color:var(--text);">{{ $nama }}</strong>
                      <div style="font-size:10px; color:var(--text-3); font-family:var(--font-mono);">ID: {{ $identitas }}</div>
                    </div>
                  </div>
                </td>
                <td>{{ $rombelJabatan }}</td>
                <td style="font-family:var(--font-mono); font-weight:600;">{{ $log->jam_masuk ?? '-' }}</td>
                <td style="font-family:var(--font-mono); font-weight:600;">{{ $log->jam_pulang ?? '-' }}</td>
                <td>
                  @if($log->status === 'hadir')
                    <span class="table-status-pill hadir"><i class="bi bi-check-circle-fill"></i> Hadir Tepat</span>
                  @elseif($log->status === 'terlambat')
                    <span class="table-status-pill terlambat"><i class="bi bi-clock-fill"></i> Terlambat</span>
                  @elseif(in_array($log->status, ['izin', 'sakit', 'cuti', 'dispen']))
                    <span class="table-status-pill izin"><i class="bi bi-file-earmark-text-fill"></i> {{ ucfirst($log->status) }}</span>
                  @else
                    <span class="table-status-pill belum"><i class="bi bi-exclamation-circle-fill"></i> {{ ucfirst($log->status) }}</span>
                  @endif
                </td>
                <td style="font-size:12.5px; color:var(--text-2);">
                  <i class="bi bi-chat-left-text-fill" style="margin-right:4px; font-size:11px; color:var(--text-3);"></i>
                  Manual Lupa Kartu
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" style="text-align:center; color:var(--text-3); padding:28px;">
                  <i class="bi bi-info-circle-fill" style="font-size:18px; display:block; margin-bottom:8px; color:var(--text-3);"></i>
                  Belum ada catatan presensi manual untuk hari ini.
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
  let activeKategori = 'siswa';

  function switchKategori(kategori) {
    activeKategori = kategori;
    clearSelection();
    
    const searchBox = document.getElementById('searchBox');
    const pickerPlaceholder = document.getElementById('pickerPlaceholderText');
    const listSiswa = document.getElementById('siswaListContainer');
    const listGuru = document.getElementById('guruListContainer');

    searchBox.value = '';
    
    if (kategori === 'siswa') {
      pickerPlaceholder.textContent = 'Cari nama, NIS, kelas...';
      listSiswa.style.display = 'block';
      listGuru.style.display = 'none';
    } else {
      pickerPlaceholder.textContent = 'Cari nama, NIP, jabatan...';
      listSiswa.style.display = 'none';
      listGuru.style.display = 'block';
    }
  }

  function togglePickerDropdown() {
    const dropdown = document.getElementById('pickerDropdownPanel');
    const trigger = document.getElementById('pickerTrigger');
    
    const isOpen = dropdown.classList.contains('open');
    if (isOpen) {
      dropdown.classList.remove('open');
      trigger.classList.remove('focused');
    } else {
      dropdown.classList.add('open');
      trigger.classList.add('focused');
      document.getElementById('searchBox').focus();
    }
  }

  // Close dropdown on click outside
  document.addEventListener('click', function(e) {
    const picker = document.querySelector('.picker-wrap');
    if (picker && !picker.contains(e.target)) {
      document.getElementById('pickerDropdownPanel').classList.remove('open');
      document.getElementById('pickerTrigger').classList.remove('focused');
    }
  });

  function selectPickerItem(id, nama, meta, fotoUrl) {
    document.getElementById('selectedPemilikId').value = id;
    
    document.getElementById('selectedNama').textContent = nama;
    document.getElementById('selectedMeta').textContent = meta;
    document.getElementById('selectedFoto').src = fotoUrl;

    document.getElementById('placeholderView').style.display = 'none';
    document.getElementById('selectedView').style.display = 'flex';

    document.getElementById('pickerDropdownPanel').classList.remove('open');
    document.getElementById('pickerTrigger').classList.remove('focused');
  }

  function clearSelection(event) {
    if (event) event.stopPropagation();

    document.getElementById('selectedPemilikId').value = '';
    document.getElementById('placeholderView').style.display = 'flex';
    document.getElementById('selectedView').style.display = 'none';
  }

  function setSesi(sesi) {
    document.getElementById('inputSesi').value = sesi;
    
    const btnMasuk = document.getElementById('btnSesiMasuk');
    const btnPulang = document.getElementById('btnSesiPulang');

    if (sesi === 'masuk') {
      btnMasuk.className = 'sesi-btn active-masuk';
      btnPulang.className = 'sesi-btn';
    } else {
      btnMasuk.className = 'sesi-btn';
      btnPulang.className = 'sesi-btn active-pulang';
    }
  }

  function filterPickerList(val) {
    const query = val.toLowerCase().trim();
    const activeContainerId = activeKategori === 'siswa' ? 'siswaListContainer' : 'guruListContainer';
    const items = document.querySelectorAll(`#${activeContainerId} .picker-item`);

    items.forEach(item => {
      let match = false;
      if (activeKategori === 'siswa') {
        const nama = item.getAttribute('data-nama') || '';
        const nisn = item.getAttribute('data-nisn') || '';
        const rombel = item.getAttribute('data-rombel') || '';
        if (nama.includes(query) || nisn.includes(query) || rombel.includes(query)) {
          match = true;
        }
      } else {
        const nama = item.getAttribute('data-nama');
        const nip = item.getAttribute('data-nip');
        const jabatan = item.getAttribute('data-jabatan');
        if (nama.includes(query) || (nip && nip.includes(query)) || jabatan.includes(query)) {
          match = true;
        }
      }
      item.style.display = match ? 'flex' : 'none';
    });
  }

  function filterLogPresensi(val) {
    const query = val.toLowerCase().trim();
    const rows = document.querySelectorAll('#tableLogPresensi tbody tr');
    let visibleCount = 0;

    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      if (text.includes(query)) {
        row.style.display = '';
        visibleCount++;
      } else {
        row.style.display = 'none';
      }
    });

    const badge = document.getElementById('logCountBadge');
    if (badge) badge.textContent = `${visibleCount} Entri`;
  }
</script>
</body>
</html>
