@extends('dcc.akademik.layout')

@section('title', 'Kelola Butir Soal Asesmen')
@section('breadcrumb', 'Butir Soal Asesmen')

@section('content')
{{-- KaTeX & Google Fonts Amiri (Bahasa Arab & Rumus Matematika) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"></script>

<style>
  /* Base & Modern Minimalist Layout */
  .soal-header-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 20px;
  }
  .soal-header-title {
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.4px;
    margin-bottom: 4px;
  }
  .soal-header-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    font-size: 12.5px;
    color: #64748b;
  }
  .soal-meta-chip {
    display: inline-flex;
    align-items: center;
    padding: 3px 9px;
    background: #f1f5f9;
    border-radius: 6px;
    font-weight: 600;
    color: #334155;
    font-size: 12px;
  }



  /* Main 2-Column Grid Layout */
  .soal-layout-grid {
    display: grid;
    grid-template-columns: 1.15fr 0.85fr;
    gap: 20px;
    align-items: start;
  }
  @media (max-width: 1024px) {
    .soal-layout-grid {
      grid-template-columns: 1fr;
    }
  }

  /* Modern Card Design */
  .panel-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 2px 4px -1px rgba(0,0,0,0.02);
    overflow: hidden;
  }
  .panel-card-head {
    padding: 14px 18px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
  }
  .panel-card-title {
    font-size: 15px;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
  }
  .panel-card-body {
    padding: 20px;
  }

  /* Unified Minimalist Editor Box */
  .editor-shell {
    border: 1.5px solid #cbd5e1;
    border-radius: 12px;
    background: #ffffff;
    overflow: hidden;
    transition: all 0.2s ease;
  }
  .editor-shell:focus-within {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
  }
  .editor-toolbar {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: 7px 10px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
  }
  .tool-btn {
    background: transparent;
    border: 1px solid transparent;
    border-radius: 6px;
    padding: 5px 8px;
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    line-height: 1;
    transition: all 0.15s ease;
  }
  .tool-btn:hover {
    background: #ffffff;
    border-color: #cbd5e1;
    color: #0f172a;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
  }
  .tool-btn.active {
    background: #2563eb;
    color: #ffffff;
    border-color: #2563eb;
  }
  .tool-sep {
    width: 1px;
    height: 16px;
    background: #cbd5e1;
    margin: 0 3px;
  }
  .tool-select {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 11.5px;
    font-weight: 600;
    color: #334155;
    outline: none;
    cursor: pointer;
  }

  /* Drawer Palettes */
  .editor-drawer {
    display: none;
    padding: 8px 12px;
    background: #f1f5f9;
    border-bottom: 1px solid #e2e8f0;
    flex-wrap: wrap;
    gap: 4px;
  }
  .drawer-chip-btn {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 3px 8px;
    font-size: 11.5px;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .drawer-chip-btn:hover {
    border-color: #3b82f6;
    background: #eff6ff;
    color: #1d4ed8;
  }

  /* Question Textarea */
  .editor-textarea {
    width: 100%;
    border: none;
    outline: none;
    padding: 14px 16px;
    font-size: 14px;
    line-height: 1.6;
    color: #0f172a;
    font-family: inherit;
    resize: vertical;
    min-height: 100px;
  }

  /* Live Preview Strip */
  .live-preview-box {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: 10px 14px;
    font-size: 13.5px;
  }
  .live-preview-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
    font-size: 10.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
  }

  /* Options / Pilihan Jawaban Rows */
  .option-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    transition: all 0.2s ease;
    cursor: pointer;
  }
  .option-row:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
  }
  .option-badge-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #475569;
    font-weight: 900;
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: all 0.2s ease;
  }
  .option-input {
    flex: 1;
    border: none;
    outline: none;
    font-size: 13.5px;
    font-weight: 600;
    color: #0f172a;
    background: transparent;
    padding: 4px 6px;
  }
  .option-key-btn {
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #64748b;
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
  }

  /* Active Correct Key State */
  .option-row.is-kunci {
    border-color: #10b981 !important;
    background: #f0fdf4 !important;
    box-shadow: 0 1px 4px rgba(16, 185, 129, 0.1);
  }
  .option-row.is-kunci .option-badge-btn {
    background: #10b981 !important;
    border-color: #10b981 !important;
    color: #ffffff !important;
  }
  .option-row.is-kunci .option-key-btn {
    background: #10b981 !important;
    border-color: #10b981 !important;
    color: #ffffff !important;
    font-weight: 800;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
  }

  /* Saved Question Item in Right Panel */
  .saved-soal-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 16px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    transition: all 0.15s ease;
  }
  .saved-soal-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 8px -2px rgba(0,0,0,0.04);
  }
</style>

{{-- Page Header --}}
<div class="soal-header-bar">
  <div>
    <h1 class="soal-header-title">{{ $asesmen->judul }}</h1>
    <div class="soal-header-meta">
      <span class="soal-meta-chip"><i class="bi bi-book me-1"></i> {{ $asesmen->distribusi?->mataPelajaran?->nama_mapel ?? 'Mata Pelajaran' }}</span>
      <span class="soal-meta-chip"><i class="bi bi-clock me-1"></i> {{ $asesmen->durasi_menit }} Menit</span>
      <span class="soal-meta-chip"><i class="bi bi-bullseye me-1"></i> KKM: {{ $asesmen->passing_grade ?? 75 }}</span>
      <span class="soal-meta-chip" style="color:#2563eb; background:#eff6ff;">
        <i class="bi bi-card-checklist me-1"></i> Soal: {{ $auditKelayakan['total_soal'] }}@if($asesmen->target_jumlah_soal)/{{ $asesmen->target_jumlah_soal }} Target@endif
      </span>
      @if($asesmen->rombel_names)
        <span class="soal-meta-chip"><i class="bi bi-people me-1"></i> {{ $asesmen->rombel_names }}</span>
      @endif
    </div>
  </div>

  <div style="display:flex; gap:8px; align-items:center;">
    <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary" style="font-size:12.5px; padding:7px 13px;">
      <i class="bi bi-arrow-left"></i>
      <span>Kembali</span>
    </a>
    <a href="{{ route('akademik.asesmen.kerjakan', $asesmen->id) }}" class="ak-btn ak-btn-secondary" target="_blank" style="font-size:12.5px; padding:7px 13px;" title="Simulasi Ujian CBT Siswa">
      <i class="bi bi-phone"></i>
      <span>Simulasi CBT</span>
    </a>
    @if($auditKelayakan['is_valid'])
      <a href="{{ route('akademik.asesmen.penugasan', $asesmen->id) }}" class="ak-btn ak-btn-primary" style="font-size:12.5px; padding:7px 15px; font-weight:800;">
        <span>Lanjut ke Penugasan</span>
        <i class="bi bi-arrow-right ms-1"></i>
      </a>
    @else
      <span class="ak-btn ak-btn-secondary" style="font-size:12.5px; padding:7px 13px; opacity:0.65; cursor:not-allowed;" title="Tambahkan minimal 1 butir soal untuk membuka sesi penugasan">
        <span>Lanjut ke Penugasan</span>
        <i class="bi bi-lock-fill ms-1"></i>
      </span>
    @endif
  </div>
</div>

{{-- Step Indicator --}}
@include('dcc.akademik.asesmen.partials.wizard_steps', ['step' => 2])

{{-- Main Grid --}}
<div class="soal-layout-grid">

  {{-- Left Column: Form Editor Soal --}}
  <div class="panel-card">
    <div class="panel-card-head">
      <h3 class="panel-card-title">
        <i class="bi bi-plus-circle text-primary"></i>
        <span>Tambah Butir Pertanyaan No. {{ $nomorBerikutnya }}</span>
      </h3>
      <button type="button" onclick="bukaModalBankSoal()" class="ak-btn ak-btn-secondary" style="padding:5px 12px; font-size:12px; font-weight:700;">
        <i class="bi bi-box-arrow-in-down-right text-primary me-1"></i>
        <span>Panggil dari Bank Soal ({{ $bankSoals->count() }})</span>
      </button>
    </div>

    <div class="panel-card-body">
      <form action="{{ route('akademik.asesmen.soal.store', $asesmen->id) }}" method="POST" id="formTambahSoal" enctype="multipart/form-data" onsubmit="return validasiFormSoal(event)">
        @csrf
        <input type="hidden" name="tipe" value="pilihan_ganda">
        <input type="hidden" name="kunci_jawaban" id="inputKunciJawaban" value="{{ old('kunci_jawaban', 'A') }}">

        {{-- Section 1: Pertanyaan Editor Shell --}}
        <div style="margin-bottom:18px;">
          <label class="ak-form-label" style="font-size:13px; font-weight:800; color:#0f172a; margin-bottom:8px; display:block;">
            Teks Pertanyaan / Soal <span class="text-danger">*</span>
          </label>

          {{-- Editor Container Shell --}}
          <div class="editor-shell">
            {{-- 1. Area Textarea Penulisan Soal di Atas --}}
            <textarea name="pertanyaan" id="inputPertanyaan" class="editor-textarea" rows="4" 
                      placeholder="Tuliskan butir soal di sini..." 
                      required oninput="updateLivePreview()">{{ old('pertanyaan') }}</textarea>

            {{-- 2. Box Preview Gambar Terpilih --}}
            <div id="boxPreviewGambar" style="display:none; padding:10px 14px; background:#f8fafc; border-top:1px solid #e2e8f0;">
              <div style="display:flex; align-items:center; justify-content:space-between; max-width:320px; background:#ffffff; border:1px solid #cbd5e1; border-radius:8px; padding:6px 10px;">
                <div style="display:flex; align-items:center; gap:8px; overflow:hidden;">
                  <img id="imgPreviewTarget" src="" style="width:40px; height:40px; border-radius:6px; object-fit:cover; border:1px solid #e2e8f0;">
                  <div style="font-size:12px; font-weight:700; color:#334155; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px;" id="infoNamaGambar"></div>
                </div>
                <button type="button" onclick="hapusUploadGambar()" style="background:#fee2e2; color:#dc2626; border:1px solid #fca5a5; border-radius:6px; padding:4px 8px; font-size:11px; font-weight:700; cursor:pointer;">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>

            {{-- 3. Toolbar Pengaturan Huruf, Format, Gambar & Mode di Bawah Textarea --}}
            <div class="editor-toolbar">
              {{-- Text Format --}}
              <button type="button" class="tool-btn" onclick="wrapText('<b>', '</b>')" title="Tebal (Bold)"><b>B</b></button>
              <button type="button" class="tool-btn" onclick="wrapText('<i>', '</i>')" title="Miring (Italic)"><i>I</i></button>
              <button type="button" class="tool-btn" onclick="wrapText('<u>', '</u>')" title="Garis Bawah (Underline)"><u>U</u></button>
              <button type="button" class="tool-btn" onclick="wrapText('<s>', '</s>')" title="Coret (Strikethrough)"><s>S</s></button>
              <button type="button" class="tool-btn" onclick="wrapText('<sup>', '</sup>')" title="Pangkat (x²)">x²</button>
              <button type="button" class="tool-btn" onclick="wrapText('<sub>', '</sub>')" title="Indeks (x₂)">x₂</button>

              <span class="tool-sep"></span>

              {{-- Font Size --}}
              <select id="selectFontSize" onchange="setFontSize(this.value)" class="tool-select" title="Ukuran Font">
                <option value="14px">Font: Normal</option>
                <option value="16px">Font: Sedang</option>
                <option value="18px">Font: Besar</option>
              </select>

              <span class="tool-sep"></span>

              {{-- Alignment --}}
              <button type="button" class="tool-btn" onclick="setTextAlign('left')" title="Rata Kiri"><i class="bi bi-text-left"></i></button>
              <button type="button" class="tool-btn" onclick="setTextAlign('center')" title="Rata Tengah"><i class="bi bi-text-center"></i></button>
              <button type="button" class="tool-btn" onclick="setTextAlign('right')" title="Rata Kanan"><i class="bi bi-text-right"></i></button>

              <span class="tool-sep"></span>

              {{-- Insert Image Trigger --}}
              <button type="button" class="tool-btn" style="color:#2563eb;" onclick="document.getElementById('fileGambarSoal').click()" title="Sisipkan Gambar">
                <i class="bi bi-image"></i>
                <span>+ Gambar</span>
              </button>

              {{-- Arabic Harakat Trigger --}}
              <button type="button" class="tool-btn" onclick="toggleDrawer('harakatDrawer')" title="Sisipkan Harakat Arab" style="font-family:'Amiri', serif;">
                <span>َ ِ ُ Harakat</span>
              </button>

              <span class="tool-sep"></span>

              {{-- Arab & Rumus Buttons --}}
              <button type="button" class="tool-btn" id="btnToggleArab" onclick="toggleModeArab()" title="Mode Penulisan Teks Arab">
                <span style="font-family:'Amiri', serif; font-size:13.5px;">ع</span> Arab (RTL)
              </button>
              <button type="button" class="tool-btn" id="btnToggleMath" onclick="toggleDrawer('mathDrawer')" title="Simbol & Rumus Matematika">
                <span style="font-weight:900;">∑</span> Rumus (LaTeX)
              </button>

              {{-- Native file input hidden strictly without overriding --}}
              <input type="file" name="gambar" id="fileGambarSoal" accept="image/*" onchange="previewUploadGambar(this)" 
                     style="display:none !important; visibility:hidden !important; position:absolute !important; width:0 !important; height:0 !important; opacity:0 !important; pointer-events:none !important;">
            </div>

            {{-- 4. Drawer 1: Harakat Arab --}}
            <div id="harakatDrawer" class="editor-drawer">
              <span style="font-size:11px; font-weight:700; color:#64748b; width:100%; margin-bottom:2px;">Klik untuk menyisipkan harakat:</span>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('َ')">َ Fathah</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('ِ')">ِ Kasrah</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('ُ')">ُ Dhammah</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('ْ')">ْ Sukun</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('ّ')">ّ Tasydid</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('ً')">ً Tanwin</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('﷽ ')">﷽</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('؟')">؟</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('،')">،</button>
            </div>

            {{-- 5. Drawer 2: Rumus KaTeX --}}
            <div id="mathDrawer" class="editor-drawer" style="background:#eff6ff; border-color:#bfdbfe;">
              <span style="font-size:11px; font-weight:700; color:#1e40af; width:100%; margin-bottom:2px;">Klik untuk menyisipkan simbol/formula:</span>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('$\\frac{a}{b}$')">a/b (Pecahan)</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('$\\sqrt{x}$')">√x (Akar)</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('$x^{2}$')">x²</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('$x_{1}$')">x₁</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('×')">×</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('÷')">÷</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('±')">±</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('≤')">≤</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('≥')">≥</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('≠')">≠</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('°')">°</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('π')">π</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('$\\sum$')">∑</button>
              <button type="button" class="drawer-chip-btn" onclick="insertChar('$\\int$')">∫</button>
            </div>

            {{-- 6. Live Render Preview (Pratinjau hanya tampil jika ada teks) --}}
            <div class="live-preview-box" id="livePreviewBox" style="display:none;">
              <div class="live-preview-head">
                <span><i class="bi bi-eye me-1"></i> Pratinjau Tampilan Siswa</span>
                <span id="previewTagArab" style="display:none; color:#059669; background:#ecfdf5; padding:1px 6px; border-radius:4px;">Mode Arab</span>
              </div>
              <div id="livePreviewContainer" style="color:#0f172a; line-height:1.6; min-height:22px;"></div>
            </div>
          </div>
        </div>

        {{-- Section 2: Pilihan Jawaban A, B, C, D, E --}}
        <div style="margin-bottom:18px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <label class="ak-form-label" style="font-size:13px; font-weight:800; color:#0f172a; margin:0;">
              Pilihan Jawaban &amp; Kunci Benar <span class="text-danger">*</span>
            </label>
            <div style="font-size:11.5px; color:#059669; font-weight:700;">
              <i class="bi bi-check-circle-fill me-1"></i> Klik opsi untuk menandai kunci
            </div>
          </div>

          <div style="display:flex; flex-direction:column; gap:8px;">
            {{-- OPSI A --}}
            <div class="option-row" id="card_opsi_A" onclick="setKunciJawaban('A')">
              <button type="button" class="option-badge-btn" id="badge_abjad_A" onclick="setKunciJawaban('A'); event.stopPropagation();">A</button>
              <input type="text" name="opsi_a" id="opsi_A" class="option-input" placeholder="Pilihan A (Wajib)" value="{{ old('opsi_a') }}" required oninput="cekKesesuaianKunci()" onclick="event.stopPropagation()">
              <button type="button" class="option-key-btn" id="btn_kunci_A" onclick="setKunciJawaban('A'); event.stopPropagation();">
                <i class="bi bi-circle"></i> <span>Kunci</span>
              </button>
            </div>

            {{-- OPSI B --}}
            <div class="option-row" id="card_opsi_B" onclick="setKunciJawaban('B')">
              <button type="button" class="option-badge-btn" id="badge_abjad_B" onclick="setKunciJawaban('B'); event.stopPropagation();">B</button>
              <input type="text" name="opsi_b" id="opsi_B" class="option-input" placeholder="Pilihan B (Wajib)" value="{{ old('opsi_b') }}" required oninput="cekKesesuaianKunci()" onclick="event.stopPropagation()">
              <button type="button" class="option-key-btn" id="btn_kunci_B" onclick="setKunciJawaban('B'); event.stopPropagation();">
                <i class="bi bi-circle"></i> <span>Kunci</span>
              </button>
            </div>

            {{-- OPSI C --}}
            <div class="option-row" id="card_opsi_C" onclick="setKunciJawaban('C')">
              <button type="button" class="option-badge-btn" id="badge_abjad_C" onclick="setKunciJawaban('C'); event.stopPropagation();">C</button>
              <input type="text" name="opsi_c" id="opsi_C" class="option-input" placeholder="Pilihan C (Opsional)" value="{{ old('opsi_c') }}" oninput="cekKesesuaianKunci()" onclick="event.stopPropagation()">
              <button type="button" class="option-key-btn" id="btn_kunci_C" onclick="setKunciJawaban('C'); event.stopPropagation();">
                <i class="bi bi-circle"></i> <span>Kunci</span>
              </button>
            </div>

            {{-- OPSI D --}}
            <div class="option-row" id="card_opsi_D" onclick="setKunciJawaban('D')">
              <button type="button" class="option-badge-btn" id="badge_abjad_D" onclick="setKunciJawaban('D'); event.stopPropagation();">D</button>
              <input type="text" name="opsi_d" id="opsi_D" class="option-input" placeholder="Pilihan D (Opsional)" value="{{ old('opsi_d') }}" oninput="cekKesesuaianKunci()" onclick="event.stopPropagation()">
              <button type="button" class="option-key-btn" id="btn_kunci_D" onclick="setKunciJawaban('D'); event.stopPropagation();">
                <i class="bi bi-circle"></i> <span>Kunci</span>
              </button>
            </div>

            {{-- OPSI E --}}
            <div class="option-row" id="card_opsi_E" onclick="setKunciJawaban('E')">
              <button type="button" class="option-badge-btn" id="badge_abjad_E" onclick="setKunciJawaban('E'); event.stopPropagation();">E</button>
              <input type="text" name="opsi_e" id="opsi_E" class="option-input" placeholder="Pilihan E (Opsional)" value="{{ old('opsi_e') }}" oninput="cekKesesuaianKunci()" onclick="event.stopPropagation()">
              <button type="button" class="option-key-btn" id="btn_kunci_E" onclick="setKunciJawaban('E'); event.stopPropagation();">
                <i class="bi bi-circle"></i> <span>Kunci</span>
              </button>
            </div>
          </div>

          <div id="kunciWarning" style="display:none; color:#dc2626; font-size:12px; font-weight:700; margin-top:8px;">
            ⚠️ Peringatan: Opsi yang Anda jadikan kunci jawaban saat ini masih kosong!
          </div>
        </div>

        {{-- Section 3: Bobot & Pembahasan --}}
        <div style="display:grid; grid-template-columns: 1fr 2.5fr; gap:12px; margin-bottom:18px;">
          <div>
            <label class="ak-form-label" style="font-size:12.5px; font-weight:800;">Bobot Nilai</label>
            <input type="number" name="bobot" class="ak-input" value="{{ old('bobot', 1) }}" min="1" required style="font-size:14px; font-weight:800; text-align:center;">
          </div>
          <div>
            <label class="ak-form-label" style="font-size:12.5px; font-weight:800;">Pembahasan (Opsional)</label>
            <input type="text" name="pembahasan" class="ak-input" placeholder="Penjelasan jawaban untuk siswa..." value="{{ old('pembahasan') }}">
          </div>
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="ak-btn ak-btn-primary" style="width:100%; padding:11px; font-size:14px; font-weight:800; justify-content:center; border-radius:10px;">
          <i class="bi bi-check2-circle me-1"></i>
          <span>Simpan Butir Soal No. {{ $nomorBerikutnya }}</span>
        </button>
      </form>
    </div>
  </div>

  {{-- Right Column: Daftar Butir Soal Asesmen Ini --}}
  <div class="panel-card">
    <div class="panel-card-head">
      <h3 class="panel-card-title">
        <i class="bi bi-collection text-primary"></i>
        <span>Daftar Soal Asesmen Ini ({{ $asesmen->soals->count() }})</span>
      </h3>
      <span class="ak-badge ak-badge-success" style="font-size:11px;">Total: {{ $totalBobot }} Poin</span>
    </div>

    <div class="panel-card-body" style="padding:16px; max-height:820px; overflow-y:auto;" id="savedQuestionsContainer">
      @if($asesmen->soals->isEmpty())
        <div style="padding:44px 20px; text-align:center; color:#64748b;">
          <div style="width:54px; height:54px; border-radius:50%; background:#f1f5f9; color:#94a3b8; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; font-size:24px;">
            <i class="bi bi-file-earmark-plus"></i>
          </div>
          <h5 style="font-size:15px; font-weight:800; color:#1e293b; margin-bottom:4px;">Belum Ada Butir Soal</h5>
          <p style="font-size:12.5px; color:#64748b; max-width:320px; margin:0 auto 14px; line-height:1.5;">
            Tulis pertanyaan di sebelah kiri atau panggil butir soal yang sudah tersimpan di Bank Soal.
          </p>
          @if($bankSoals->isNotEmpty())
            <button type="button" onclick="bukaModalBankSoal()" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-weight:700;">
              <i class="bi bi-box-arrow-in-down-right text-primary me-1"></i> Panggil dari Bank Soal ({{ $bankSoals->count() }})
            </button>
          @endif
        </div>
      @else
        <div style="display:flex; flex-direction:column; gap:12px;">
          @foreach($asesmen->soals as $idx => $soal)
            <div class="saved-soal-card">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                <span class="ak-badge ak-badge-primary" style="font-size:11px;">
                  Soal #{{ $idx + 1 }} · {{ $soal->bobot }} Poin
                </span>
                <form action="{{ route('akademik.asesmen.soal.destroy', [$asesmen->id, $soal->id]) }}" method="POST" onsubmit="return confirm('Hapus butir soal no {{ $idx + 1 }}?')" style="margin:0;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="ak-btn ak-btn-secondary ak-btn-sm text-danger" title="Hapus Soal" style="padding:3px 8px; font-size:11px;">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>

              {{-- Teks Soal --}}
              <div class="render-math-target" style="font-size:13.5px; font-weight:700; color:#0f172a; margin-bottom:8px; line-height:1.5;">
                {!! $soal->pertanyaan !!}
              </div>

              @if($soal->gambar_url)
                <div style="margin-bottom:8px; max-width:240px;">
                  <img src="{{ $soal->gambar_url }}" alt="Gambar Soal" style="width:100%; border-radius:6px; border:1px solid #cbd5e1;">
                </div>
              @endif

              {{-- Pilihan Opsi & Kunci --}}
              <div style="display:flex; flex-direction:column; gap:4px; font-size:12px;">
                @foreach(['A' => $soal->opsi_a, 'B' => $soal->opsi_b, 'C' => $soal->opsi_c, 'D' => $soal->opsi_d, 'E' => $soal->opsi_e] as $abjad => $teksOpsi)
                  @if(!empty($teksOpsi))
                    @php $isKunci = ($soal->kunci_jawaban == $abjad); @endphp
                    <div class="render-math-target" style="display:flex; align-items:center; gap:6px; padding:3px 8px; border-radius:6px; {{ $isKunci ? 'font-weight:800; color:#065f46; background:#ecfdf5; border:1px solid #a7f3d0;' : 'color:#475569;' }}">
                      <strong style="width:16px;">{{ $abjad }}.</strong>
                      <span style="flex:1;">{{ $teksOpsi }}</span>
                      @if($isKunci)
                        <i class="bi bi-check-circle-fill text-success" title="Kunci Jawaban"></i>
                      @endif
                    </div>
                  @endif
                @endforeach
              </div>

              @if($soal->pembahasan)
                <div style="margin-top:8px; padding:6px 10px; background:#f8fafc; border-radius:6px; font-size:11.5px; color:#64748b;">
                  <strong style="color:#334155;">Pembahasan:</strong> {{ $soal->pembahasan }}
                </div>
              @endif
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>

</div>

<script>
  let isArabMode = false;

  // 1. SELEKSI KUNCI JAWABAN LANGSUNG (TAMPILAN HIJAU EMERALD)
  function setKunciJawaban(kunci) {
    document.getElementById('inputKunciJawaban').value = kunci;

    ['A', 'B', 'C', 'D', 'E'].forEach(letter => {
      const card = document.getElementById('card_opsi_' + letter);
      const btn = document.getElementById('btn_kunci_' + letter);

      if (letter === kunci) {
        card.classList.add('is-kunci');
        btn.innerHTML = '<i class="bi bi-check-circle-fill"></i><span>Kunci Benar</span>';
      } else {
        card.classList.remove('is-kunci');
        btn.innerHTML = '<i class="bi bi-circle"></i><span>Kunci</span>';
      }
    });

    cekKesesuaianKunci();
  }

  function cekKesesuaianKunci() {
    const kunci = document.getElementById('inputKunciJawaban').value;
    const inputOpsi = document.getElementById('opsi_' + kunci);
    const warningEl = document.getElementById('kunciWarning');

    if (inputOpsi && inputOpsi.value.trim() === '') {
      if (warningEl) {
        warningEl.style.display = 'block';
        warningEl.innerText = '⚠️ Peringatan: Pilihan Jawaban ' + kunci + ' (kunci) masih kosong!';
      }
      return false;
    } else {
      if (warningEl) {
        warningEl.style.display = 'none';
      }
      return true;
    }
  }

  // 2. TEXT FORMATTING
  function wrapText(openTag, closeTag) {
    const textarea = document.getElementById('inputPertanyaan');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selected = textarea.value.substring(start, end);

    const replacement = openTag + (selected || 'teks') + closeTag;
    textarea.setRangeText(replacement, start, end, 'end');
    textarea.focus();
    updateLivePreview();
  }

  function insertChar(char) {
    const textarea = document.getElementById('inputPertanyaan');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    textarea.setRangeText(char, start, end, 'end');
    textarea.focus();
    updateLivePreview();
  }

  function setFontSize(size) {
    const textarea = document.getElementById('inputPertanyaan');
    textarea.style.fontSize = size;
  }

  function setTextAlign(align) {
    const textarea = document.getElementById('inputPertanyaan');
    textarea.style.textAlign = align;
    const preview = document.getElementById('livePreviewContainer');
    if (preview) preview.style.textAlign = align;
  }

  // 3. MODE BAHASA ARAB (RTL)
  function toggleModeArab() {
    isArabMode = !isArabMode;
    const textarea = document.getElementById('inputPertanyaan');
    const btn = document.getElementById('btnToggleArab');
    const tagArab = document.getElementById('previewTagArab');
    const preview = document.getElementById('livePreviewContainer');

    if (isArabMode) {
      textarea.setAttribute('dir', 'rtl');
      textarea.style.fontFamily = "'Amiri', 'Traditional Arabic', serif";
      textarea.style.fontSize = '18px';
      textarea.style.textAlign = 'right';
      btn.classList.add('active');
      if (tagArab) tagArab.style.display = 'inline-block';
      if (preview) {
        preview.setAttribute('dir', 'rtl');
        preview.style.fontFamily = "'Amiri', 'Traditional Arabic', serif";
        preview.style.fontSize = '18px';
        preview.style.textAlign = 'right';
      }
    } else {
      textarea.removeAttribute('dir');
      textarea.style.fontFamily = 'inherit';
      textarea.style.fontSize = '14px';
      textarea.style.textAlign = 'left';
      btn.classList.remove('active');
      if (tagArab) tagArab.style.display = 'none';
      if (preview) {
        preview.removeAttribute('dir');
        preview.style.fontFamily = 'inherit';
        preview.style.fontSize = '14px';
        preview.style.textAlign = 'left';
      }
    }
    updateLivePreview();
  }

  function toggleDrawer(id) {
    const drawer = document.getElementById(id);
    if (drawer) {
      drawer.style.display = drawer.style.display === 'none' ? 'flex' : 'none';
    }
  }

  // 4. PREVIEW UNGGAH GAMBAR
  function previewUploadGambar(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('imgPreviewTarget').src = e.target.result;
        document.getElementById('boxPreviewGambar').style.display = 'block';
        document.getElementById('infoNamaGambar').innerText = input.files[0].name;
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  function hapusUploadGambar() {
    const fileInput = document.getElementById('fileGambarSoal');
    fileInput.value = '';
    document.getElementById('imgPreviewTarget').src = '';
    document.getElementById('boxPreviewGambar').style.display = 'none';
  }

  // 5. LIVE KATEX & FORMATTING PREVIEW
  function updateLivePreview() {
    const text = document.getElementById('inputPertanyaan').value;
    const box = document.getElementById('livePreviewBox');
    const container = document.getElementById('livePreviewContainer');
    if (!text.trim()) {
      if (box) box.style.display = 'none';
      if (container) container.innerHTML = '';
      return;
    }

    if (box) box.style.display = 'block';
    let formatted = text.replace(/\n/g, '<br>');
    container.innerHTML = formatted;

    if (typeof renderMathInElement === 'function') {
      renderMathInElement(container, {
        delimiters: [
          {left: '$$', right: '$$', display: true},
          {left: '$', right: '$', display: false},
          {left: '\\(', right: '\\)', display: false},
          {left: '\\[', right: '\\]', display: true}
        ],
        throwOnError: false
      });
    }
  }

  function validasiFormSoal(e) {
    const isKunciValid = cekKesesuaianKunci();
    const kunci = document.getElementById('inputKunciJawaban').value;
    if (!isKunciValid) {
      alert("Peringatan: Pilihan Jawaban " + kunci + " dijadikan kunci tetapi masih kosong.");
      const inputOpsi = document.getElementById('opsi_' + kunci);
      if (inputOpsi) inputOpsi.focus();
      return false;
    }

    const opsiA = document.getElementById('opsi_A').value.trim();
    const opsiB = document.getElementById('opsi_B').value.trim();
    if (opsiA && opsiB && opsiA.toLowerCase() === opsiB.toLowerCase()) {
      alert("Pilihan Jawaban A dan B tidak boleh sama persis!");
      return false;
    }

    return true;
  }

  document.addEventListener('DOMContentLoaded', function() {
    const initialKunci = document.getElementById('inputKunciJawaban').value || 'A';
    setKunciJawaban(initialKunci);
    updateLivePreview();

    setTimeout(function() {
      const savedBox = document.getElementById('savedQuestionsContainer');
      if (savedBox && typeof renderMathInElement === 'function') {
        renderMathInElement(savedBox, {
          delimiters: [
            {left: '$$', right: '$$', display: true},
            {left: '$', right: '$', display: false},
            {left: '\\(', right: '\\)', display: false},
            {left: '\\[', right: '\\]', display: true}
          ],
          throwOnError: false
        });
      }
    }, 400);
  });

  // 6. MODAL & FITUR PANGGIL DARI BANK SOAL
  function bukaModalBankSoal() {
    const modal = document.getElementById('modalBankSoal');
    if (modal) {
      modal.style.display = 'flex';
      hitungTerpilihBankSoal();
      if (typeof renderMathInElement === 'function') {
        renderMathInElement(document.getElementById('listBankSoalContainer'), {
          delimiters: [
            {left: '$$', right: '$$', display: true},
            {left: '$', right: '$', display: false},
            {left: '\\(', right: '\\)', display: false},
            {left: '\\[', right: '\\]', display: true}
          ],
          throwOnError: false
        });
      }
    }
  }

  function tutupModalBankSoal() {
    const modal = document.getElementById('modalBankSoal');
    if (modal) modal.style.display = 'none';
  }

  function hitungTerpilihBankSoal() {
    const cbs = document.querySelectorAll('.bank-item-cb:checked');
    const label = document.getElementById('labelJumlahTerpilih');
    const btn = document.getElementById('btnSubmitImportBank');
    if (label) label.innerText = cbs.length;
    if (btn) btn.disabled = (cbs.length === 0);
  }

  function pilihSemuaBankSoal(status) {
    const cbs = document.querySelectorAll('.bank-item-cb:not(:disabled)');
    cbs.forEach(cb => {
      const parent = cb.closest('.bank-soal-item');
      if (!parent || parent.style.display !== 'none') {
        cb.checked = status;
      }
    });
    hitungTerpilihBankSoal();
  }

  function filterBankSoal() {
    const query = (document.getElementById('cariBankSoalInput').value || '').toLowerCase();
    const items = document.querySelectorAll('.bank-soal-item');
    items.forEach(item => {
      const text = item.getAttribute('data-pertanyaan') || '';
      if (text.includes(query)) {
        item.style.display = '';
      } else {
        item.style.display = 'none';
      }
    });
  }
</script>

{{-- Modal Panggil dari Bank Soal --}}
<div id="modalBankSoal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); z-index:99999; align-items:center; justify-content:center; padding:16px;">
  <div style="background:#ffffff; border-radius:14px; max-width:780px; width:100%; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2); overflow:hidden;">
    
    {{-- Modal Header --}}
    <div style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; background:#f8fafc;">
      <div>
        <h4 style="font-size:16px; font-weight:800; color:#0f172a; margin:0; display:flex; align-items:center; gap:8px;">
          <i class="bi bi-archive-fill text-primary"></i>
          <span>Bank Soal: {{ $asesmen->distribusi?->mataPelajaran?->nama_mapel ?? 'Mata Pelajaran' }}</span>
        </h4>
        <div style="font-size:12px; color:#64748b; margin-top:3px;">
          Pilih butir soal yang tersimpan untuk digunakan pada paket asesmen ini.
        </div>
      </div>
      <button type="button" onclick="tutupModalBankSoal()" style="background:none; border:none; font-size:20px; color:#64748b; cursor:pointer; padding:4px;">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    {{-- Filter & Search Toolbar --}}
    <div style="padding:12px 20px; border-bottom:1px solid #f1f5f9; background:#ffffff; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
      <div style="position:relative; flex:1; min-width:240px;">
        <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:13px;"></i>
        <input type="text" id="cariBankSoalInput" oninput="filterBankSoal()" placeholder="Ketik kata kunci untuk mencari butir soal..." class="ak-input" style="padding-left:34px; font-size:12.5px; height:36px;">
      </div>
      <div style="display:flex; align-items:center; gap:8px;">
        <button type="button" onclick="pilihSemuaBankSoal(true)" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:11.5px;">Pilih Semua</button>
        <button type="button" onclick="pilihSemuaBankSoal(false)" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:11.5px;">Batal Pilih</button>
      </div>
    </div>

    {{-- Form Import --}}
    <form action="{{ route('akademik.asesmen.soal.import_bank', $asesmen->id) }}" method="POST" id="formImportBankSoal" style="display:flex; flex-direction:column; flex:1; overflow:hidden; margin:0;">
      @csrf

      {{-- Question List Container --}}
      <div style="padding:16px 20px; overflow-y:auto; flex:1; max-height:480px; display:flex; flex-direction:column; gap:12px;" id="listBankSoalContainer">
        @if($bankSoals->isEmpty())
          <div style="padding:40px 20px; text-align:center; color:#64748b;">
            <i class="bi bi-folder-x" style="font-size:36px; color:#cbd5e1; display:block; margin-bottom:10px;"></i>
            <h5 style="font-size:14.5px; font-weight:700; color:#334155; margin-bottom:4px;">Bank Soal Masih Kosong</h5>
            <p style="font-size:12px; color:#64748b; margin:0;">
              Belum ada butir soal tersimpan untuk mata pelajaran ini. Setiap butir soal yang dibuat akan otomatis tersimpan di sini.
            </p>
          </div>
        @else
          @foreach($bankSoals as $bs)
            @php
              $sudahAda = in_array(trim(strip_tags($bs->pertanyaan)), $existingPertanyaans);
            @endphp
            <div class="bank-soal-item" data-pertanyaan="{{ strtolower(strip_tags($bs->pertanyaan)) }}" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; transition:all 0.15s ease; {{ $sudahAda ? 'opacity:0.65; background:#f8fafc;' : '' }}">
              <div style="display:flex; align-items:flex-start; gap:12px;">
                <div style="margin-top:3px;">
                  @if($sudahAda)
                    <input type="checkbox" disabled checked style="width:17px; height:17px; cursor:not-allowed;">
                  @else
                    <input type="checkbox" name="bank_soal_ids[]" value="{{ $bs->id }}" class="bank-item-cb" onchange="hitungTerpilihBankSoal()" style="width:17px; height:17px; cursor:pointer;">
                  @endif
                </div>
                <div style="flex:1;">
                  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                    <div style="display:flex; align-items:center; gap:6px;">
                      <span class="ak-badge ak-badge-primary" style="font-size:10.5px;">{{ strtoupper(str_replace('_', ' ', $bs->tipe)) }}</span>
                      <span class="ak-badge ak-badge-secondary" style="font-size:10.5px;">Bobot: {{ $bs->bobot }} Poin</span>
                      @if($bs->guru)
                        <span style="font-size:11px; color:#64748b;">· {{ $bs->guru->nama_guru }}</span>
                      @endif
                    </div>
                    @if($sudahAda)
                      <span class="ak-badge ak-badge-success" style="font-size:10.5px;">
                        <i class="bi bi-check-circle-fill me-1"></i> Sudah Ada di Asesmen
                      </span>
                    @endif
                  </div>

                  {{-- Teks Pertanyaan --}}
                  <div class="render-math-target" style="font-size:13px; font-weight:700; color:#0f172a; margin-bottom:8px; line-height:1.5;">
                    {!! $bs->pertanyaan !!}
                  </div>

                  @if($bs->gambar_url)
                    <div style="margin-bottom:8px; max-width:180px;">
                      <img src="{{ $bs->gambar_url }}" alt="Gambar" style="width:100%; border-radius:6px; border:1px solid #cbd5e1;">
                    </div>
                  @endif

                  {{-- Preview Opsi Jawaban --}}
                  <div style="display:flex; flex-direction:column; gap:3px; font-size:11.5px;">
                    @foreach(['A' => $bs->opsi_a, 'B' => $bs->opsi_b, 'C' => $bs->opsi_c, 'D' => $bs->opsi_d, 'E' => $bs->opsi_e] as $abjad => $teksOpsi)
                      @if(!empty($teksOpsi))
                        @php $isKunci = ($bs->kunci_jawaban == $abjad); @endphp
                        <div class="render-math-target" style="display:flex; align-items:center; gap:6px; padding:2px 6px; border-radius:4px; {{ $isKunci ? 'font-weight:700; color:#065f46; background:#ecfdf5;' : 'color:#475569;' }}">
                          <span style="width:14px;">{{ $abjad }}.</span>
                          <span>{{ $teksOpsi }}</span>
                          @if($isKunci)
                            <i class="bi bi-check-circle-fill text-success" title="Kunci Jawaban"></i>
                          @endif
                        </div>
                      @endif
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        @endif
      </div>

      {{-- Modal Footer --}}
      <div style="padding:14px 20px; border-top:1px solid #e2e8f0; background:#f8fafc; display:flex; justify-content:space-between; align-items:center;">
        <div style="font-size:12.5px; font-weight:700; color:#334155;">
          <span id="labelJumlahTerpilih">0</span> butir soal dipilih
        </div>
        <div style="display:flex; gap:8px;">
          <button type="button" onclick="tutupModalBankSoal()" class="ak-btn ak-btn-secondary" style="font-size:12.5px; padding:7px 14px;">Batal</button>
          <button type="submit" id="btnSubmitImportBank" class="ak-btn ak-btn-primary" disabled style="font-size:12.5px; padding:7px 16px; font-weight:800;">
            <i class="bi bi-box-arrow-in-down-right me-1"></i>
            <span>Panggil &amp; Masukkan ke Asesmen</span>
          </button>
        </div>
      </div>
    </form>

  </div>
</div>
@endsection
