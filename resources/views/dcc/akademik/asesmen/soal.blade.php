@extends('dcc.akademik.layout')

@section('title', 'Kelola Butir Soal Asesmen')
@section('breadcrumb', 'Butir Soal Asesmen')

@section('content')
{{-- KaTeX & Google Fonts Amiri (Bahasa Arab & Rumus Matematika) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"></script>

<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">{{ $asesmen->judul }}</h1>
    <div class="akademik-page-desc">
      {{ $asesmen->distribusi?->mataPelajaran?->nama_mapel }} · {{ $asesmen->rombel_names }} · Durasi: {{ $asesmen->durasi_menit }} Menit · KKM: {{ $asesmen->passing_grade }}
    </div>
  </div>

  <div style="display:flex; gap:10px; align-items:center;">
    <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary">
      <i class="bi bi-arrow-left"></i>
      <span>Kembali</span>
    </a>
    <a href="{{ route('akademik.asesmen.kerjakan', $asesmen->id) }}" class="ak-btn ak-btn-secondary" target="_blank" title="Simulasi Ujian CBT">
      <i class="bi bi-phone"></i>
      <span>Simulasi CBT</span>
    </a>
    @if($auditKelayakan['is_valid'])
      <a href="{{ route('akademik.asesmen.penugasan', $asesmen->id) }}" class="ak-btn ak-btn-primary" style="font-weight:800;">
        <span>Lanjut ke Sesi Penugasan</span>
        <i class="bi bi-arrow-right ms-1"></i>
      </a>
    @else
      <span class="ak-btn ak-btn-secondary" style="opacity:0.6; cursor:not-allowed;" title="Selesaikan minimal 1 butir soal yang valid untuk membuka sesi penugasan">
        <span>Lanjut ke Sesi Penugasan</span>
        <i class="bi bi-lock-fill ms-1"></i>
      </span>
    @endif
  </div>
</div>

{{-- Step Indicator: Step 2 Aktif --}}
@include('dcc.akademik.asesmen.partials.wizard_steps', ['step' => 2])

{{-- Panel Audit Kelayakan & Validasi Mutu Soal --}}
<div class="akademik-card" style="margin-bottom:20px; border-left:4px solid {{ $auditKelayakan['is_valid'] ? '#10b981' : '#f59e0b' }};">
  <div class="akademik-card-body" style="padding:16px 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:42px; height:42px; border-radius:10px; background:{{ $auditKelayakan['is_valid'] ? '#ecfdf5' : '#fffbeb' }}; color:{{ $auditKelayakan['is_valid'] ? '#059669' : '#d97706' }}; display:flex; align-items:center; justify-content:center; font-size:22px;">
          <i class="bi {{ $auditKelayakan['is_valid'] ? 'bi-shield-check' : 'bi-shield-exclamation' }}"></i>
        </div>
        <div>
          <div style="display:flex; align-items:center; gap:8px;">
            <strong style="font-size:14.5px; color:#0f172a;">Audit Kelayakan &amp; Validasi Butir Soal</strong>
            @if($asesmen->status_validasi === 'siap_diujikan')
              <span class="ak-badge ak-badge-success"><i class="bi bi-check-circle-fill me-1"></i> Terverifikasi &amp; Siap Diujikan</span>
            @elseif($asesmen->status_validasi === 'perlu_revisi')
              <span class="ak-badge ak-badge-danger"><i class="bi bi-exclamation-octagon-fill me-1"></i> Perlu Revisi</span>
            @else
              <span class="ak-badge ak-badge-warning"><i class="bi bi-pencil-square me-1"></i> Draft Penyusunan</span>
            @endif
          </div>
          <div style="font-size:12px; color:#64748b; margin-top:2px;">
            @if($auditKelayakan['is_valid'])
              Semua <strong>{{ $auditKelayakan['total_soal'] }} butir soal</strong> memenuhi standar teknis integritas CBT (Kunci valid &amp; opsi lengkap).
            @else
              Ditemukan <strong>{{ count($auditKelayakan['errors']) }} kendala</strong> yang perlu diselesaikan sebelum ujian dapat diaktifkan.
            @endif
          </div>
        </div>
      </div>

      {{-- KPI Mini Bar --}}
      <div style="display:flex; gap:16px; align-items:center;">
        <div style="text-align:right;">
          <div style="font-size:11px; color:#64748b; text-transform:uppercase; font-weight:700;">Progress Soal</div>
          <div style="font-size:18px; font-weight:900; color:#0f172a;">
            {{ $auditKelayakan['total_soal'] }}
            @if($asesmen->target_jumlah_soal)
              <span style="font-size:13px; color:#64748b; font-weight:700;">/ {{ $asesmen->target_jumlah_soal }}</span>
            @endif
            <span style="font-size:12px; font-weight:600; color:#64748b;">Butir</span>
          </div>
        </div>
        <div style="height:32px; width:1px; background:#e2e8f0;"></div>
        <div style="text-align:right;">
          <div style="font-size:11px; color:#64748b; text-transform:uppercase; font-weight:700;">KKM</div>
          <div style="font-size:18px; font-weight:900; color:#059669;">{{ $asesmen->passing_grade ?? 75 }}</div>
        </div>
        <div style="height:32px; width:1px; background:#e2e8f0;"></div>
        <div style="text-align:right;">
          <div style="font-size:11px; color:#64748b; text-transform:uppercase; font-weight:700;">Total Bobot</div>
          <div style="font-size:18px; font-weight:900; color:#2563eb;">{{ $auditKelayakan['total_bobot'] }} Poin</div>
        </div>
      </div>
    </div>

    {{-- Error Checklist (Jika ada) --}}
    @if(!empty($auditKelayakan['errors']))
      <div style="margin-top:14px; padding:10px 14px; background:#fef2f2; border:1px solid #fecaca; border-radius:8px; font-size:12px; color:#991b1b;">
        <div style="font-weight:800; margin-bottom:4px;"><i class="bi bi-x-circle me-1"></i> Wajib Diperbaiki Sebelum Sesi Diaktifkan:</div>
        <ul style="margin:0; padding-left:18px;">
          @foreach($auditKelayakan['errors'] as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- Warning Checklist (Saran Mutu) --}}
    @if(!empty($auditKelayakan['warnings']))
      <div style="margin-top:10px; padding:10px 14px; background:#fffbeb; border:1px solid #fde68a; border-radius:8px; font-size:12px; color:#92400e;">
        <div style="font-weight:800; margin-bottom:4px;"><i class="bi bi-exclamation-triangle me-1"></i> Catatan Mutu Butir Soal:</div>
        <ul style="margin:0; padding-left:18px;">
          @foreach($auditKelayakan['warnings'] as $warn)
            <li>{{ $warn }}</li>
          @endforeach
        </ul>
      </div>
    @endif
  </div>
</div>

<div style="display:grid; grid-template-columns: 1.15fr 0.85fr; gap:20px; align-items:start;">

  {{-- Left: Form Editor Soal --}}
  <div class="akademik-card">
    <div class="akademik-card-header" style="background:#ffffff; border-bottom:1px solid #f1f5f9;">
      <h3 class="akademik-card-title">
        <i class="bi bi-plus-circle text-primary"></i>
        <span>Tambah Butir Pertanyaan No. {{ $nomorBerikutnya }}</span>
      </h3>
      <span class="ak-badge ak-badge-primary">Pilihan Ganda</span>
    </div>
    <div class="akademik-card-body" style="padding:20px 24px;">
      <form action="{{ route('akademik.asesmen.soal.store', $asesmen->id) }}" method="POST" id="formTambahSoal" enctype="multipart/form-data" onsubmit="return validasiFormSoal(event)">
        @csrf
        <input type="hidden" name="tipe" value="pilihan_ganda">
        <input type="hidden" name="kunci_jawaban" id="inputKunciJawaban" value="{{ old('kunci_jawaban', 'A') }}">

        {{-- Toolbar Pengaturan Format, Font, Posisi, Gambar, Bahasa Arab, dan Matematika --}}
        <div style="margin-bottom:16px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <label class="ak-form-label" style="font-size:13px; font-weight:800; color:#0f172a; margin:0;">
              Teks Pertanyaan / Soal <span class="text-danger">*</span>
            </label>
            <div style="display:flex; gap:6px;">
              <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" id="btnToggleArab" onclick="toggleModeArab()" style="font-size:11.5px; padding:3px 10px; font-weight:800;">
                <span style="font-family:'Amiri', serif; font-size:14px; margin-right:4px;">ع</span> Mode Arab (RTL)
              </button>
              <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" id="btnToggleMath" onclick="toggleDrawer('mathDrawer')" style="font-size:11.5px; padding:3px 10px; font-weight:800;">
                <span style="font-weight:900; margin-right:4px;">∑</span> Rumus Matematika
              </button>
            </div>
          </div>

          {{-- Main Formatting Toolbar --}}
          <div style="display:flex; flex-wrap:wrap; gap:4px; padding:6px 10px; background:#f1f5f9; border:1px solid #cbd5e1; border-radius:8px 8px 0 0; border-bottom:none; align-items:center;">
            {{-- Text Style --}}
            <button type="button" class="toolbar-btn" onclick="wrapText('<b>', '</b>')" title="Tebal (Bold)"><b>B</b></button>
            <button type="button" class="toolbar-btn" onclick="wrapText('<i>', '</i>')" title="Miring (Italic)"><i>I</i></button>
            <button type="button" class="toolbar-btn" onclick="wrapText('<u>', '</u>')" title="Garis Bawah (Underline)"><u>U</u></button>
            <button type="button" class="toolbar-btn" onclick="wrapText('<s>', '</s>')" title="Coret (Strikethrough)"><s>S</s></button>
            <button type="button" class="toolbar-btn" onclick="wrapText('<sup>', '</sup>')" title="Pangkat / Superscript (x²)">x²</button>
            <button type="button" class="toolbar-btn" onclick="wrapText('<sub>', '</sub>')" title="Indeks / Subscript (x₂)">x₂</button>

            <span style="width:1px; height:18px; background:#cbd5e1; margin:0 4px;"></span>

            {{-- Font Size --}}
            <select id="selectFontSize" onchange="setFontSize(this.value)" class="toolbar-select" title="Ukuran Font">
              <option value="14px">Font: Normal</option>
              <option value="16px">Font: Sedang</option>
              <option value="18px">Font: Besar</option>
              <option value="20px">Font: Ekstra Besar</option>
            </select>

            <span style="width:1px; height:18px; background:#cbd5e1; margin:0 4px;"></span>

            {{-- Posisi / Alignment --}}
            <button type="button" class="toolbar-btn" onclick="setTextAlign('left')" title="Rata Kiri"><i class="bi bi-text-left"></i></button>
            <button type="button" class="toolbar-btn" onclick="setTextAlign('center')" title="Rata Tengah"><i class="bi bi-text-center"></i></button>
            <button type="button" class="toolbar-btn" onclick="setTextAlign('right')" title="Rata Kanan"><i class="bi bi-text-right"></i></button>
            <button type="button" class="toolbar-btn" onclick="setTextAlign('justify')" title="Rata Kiri-Kanan (Justify)"><i class="bi bi-justify"></i></button>

            <span style="width:1px; height:18px; background:#cbd5e1; margin:0 4px;"></span>

            {{-- Image Upload Trigger --}}
            <button type="button" class="toolbar-btn" style="color:#2563eb; font-weight:700; display:flex; align-items:center; gap:4px; padding:2px 8px;" onclick="document.getElementById('fileGambarSoal').click()" title="Unggah Gambar Soal">
              <i class="bi bi-image"></i>
              <span>+ Gambar</span>
            </button>
            <input type="file" name="gambar" id="fileGambarSoal" accept="image/*" style="display:none;" onchange="previewUploadGambar(this)">

            {{-- Harakat Arab Drawer Trigger --}}
            <button type="button" class="toolbar-btn" onclick="toggleDrawer('harakatDrawer')" title="Buka Keyboard Harakat Arab" style="font-family:'Amiri', serif; font-size:13px; font-weight:700;">
              <span>َ ِ ُ Harakat</span>
            </button>
          </div>

          {{-- Drawer 1: Keyboard Harakat Arab --}}
          <div id="harakatDrawer" style="display:none; padding:8px 10px; background:#f8fafc; border:1px solid #cbd5e1; border-top:none; border-bottom:1px dashed #cbd5e1; flex-wrap:wrap; gap:4px;">
            <div style="width:100%; font-size:11px; font-weight:800; color:#475569; margin-bottom:4px;">
              <i class="bi bi-keyboard me-1"></i> Klik untuk menyisipkan harakat pada teks Arab:
            </div>
            <button type="button" class="harakat-btn" onclick="insertChar('َ')" title="Fathah">َ Fathah</button>
            <button type="button" class="harakat-btn" onclick="insertChar('ِ')" title="Kasrah">ِ Kasrah</button>
            <button type="button" class="harakat-btn" onclick="insertChar('ُ')" title="Dhammah">ُ Dhammah</button>
            <button type="button" class="harakat-btn" onclick="insertChar('ْ')" title="Sukun">ْ Sukun</button>
            <button type="button" class="harakat-btn" onclick="insertChar('ّ')" title="Tasydid">ّ Tasydid</button>
            <button type="button" class="harakat-btn" onclick="insertChar('ً')" title="Tanwin Fathah">ً Fathatain</button>
            <button type="button" class="harakat-btn" onclick="insertChar('ٍ')" title="Tanwin Kasrah">ٍ Kasratain</button>
            <button type="button" class="harakat-btn" onclick="insertChar('ٌ')" title="Tanwin Dhammah">ٌ Dhammatain</button>
            <button type="button" class="harakat-btn" onclick="insertChar('ٰ')" title="Alif Khanjariah">ٰ Alif Kecil</button>
            <button type="button" class="harakat-btn" onclick="insertChar('﷽ ')" title="Bismillah">﷽</button>
            <button type="button" class="harakat-btn" onclick="insertChar('؟')" title="Tanda Tanya Arab">؟</button>
            <button type="button" class="harakat-btn" onclick="insertChar('،')" title="Koma Arab">،</button>
          </div>

          {{-- Drawer 2: Palet Rumus & Simbol Matematika --}}
          <div id="mathDrawer" style="display:none; padding:8px 10px; background:#eff6ff; border:1px solid #93c5fd; border-top:none; border-bottom:1px dashed #93c5fd; flex-wrap:wrap; gap:4px;">
            <div style="width:100%; font-size:11px; font-weight:800; color:#1e40af; margin-bottom:4px;">
              <i class="bi bi-calculator me-1"></i> Klik untuk menyisipkan simbol atau rumus matematika (Format LaTeX / KaTeX):
            </div>
            <button type="button" class="math-btn" onclick="insertChar('×')">×</button>
            <button type="button" class="math-btn" onclick="insertChar('÷')">÷</button>
            <button type="button" class="math-btn" onclick="insertChar('±')">±</button>
            <button type="button" class="math-btn" onclick="insertChar('≤')">≤</button>
            <button type="button" class="math-btn" onclick="insertChar('≥')">≥</button>
            <button type="button" class="math-btn" onclick="insertChar('≠')">≠</button>
            <button type="button" class="math-btn" onclick="insertChar('≈')">≈</button>
            <button type="button" class="math-btn" onclick="insertChar('∞')">∞</button>
            <button type="button" class="math-btn" onclick="insertChar('°')">°</button>
            <button type="button" class="math-btn" onclick="insertChar('π')">π</button>
            <button type="button" class="math-btn" onclick="insertChar('$\\sqrt{x}$')">√x (Akar)</button>
            <button type="button" class="math-btn" onclick="insertChar('$\\frac{a}{b}$')">a/b (Pecahan)</button>
            <button type="button" class="math-btn" onclick="insertChar('$x^{2}$')">x² (Pangkat)</button>
            <button type="button" class="math-btn" onclick="insertChar('$x_{1}$')">x₁ (Indeks)</button>
            <button type="button" class="math-btn" onclick="insertChar('$\\alpha$')">α</button>
            <button type="button" class="math-btn" onclick="insertChar('$\\beta$')">β</button>
            <button type="button" class="math-btn" onclick="insertChar('$\\theta$')">θ</button>
            <button type="button" class="math-btn" onclick="insertChar('$\\Delta$')">Δ</button>
            <button type="button" class="math-btn" onclick="insertChar('$\\sum$')">∑</button>
            <button type="button" class="math-btn" onclick="insertChar('$\\int$')">∫</button>
          </div>

          {{-- Area Textarea Soal --}}
          <textarea name="pertanyaan" id="inputPertanyaan" class="ak-textarea" rows="4" 
                    placeholder="Tuliskan butir soal di sini... (Dapat menyisipkan rumus seperti $\frac{1}{2}$ atau teks Arab)" 
                    required style="border-radius:0 0 8px 8px; font-size:14px; line-height:1.6;" 
                    oninput="updateLivePreview()">{{ old('pertanyaan') }}</textarea>

          {{-- Preview Gambar yang Diunggah --}}
          <div id="boxPreviewGambar" style="display:none; margin-top:10px; position:relative; max-width:320px; background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; padding:6px;">
            <img id="imgPreviewTarget" src="" style="width:100%; border-radius:6px; max-height:220px; object-fit:contain; background:#ffffff;">
            <button type="button" onclick="hapusUploadGambar()" style="position:absolute; top:12px; right:12px; background:#ef4444; color:#fff; border:none; border-radius:6px; padding:4px 8px; font-size:11px; font-weight:700; cursor:pointer; box-shadow:0 2px 4px rgba(0,0,0,0.2);">
              <i class="bi bi-trash"></i> Hapus
            </button>
            <div style="font-size:11px; color:#64748b; margin-top:4px; text-align:center;" id="infoNamaGambar"></div>
          </div>

          {{-- Live Preview Box: Rendering KaTeX, Format & Arab --}}
          <div style="margin-top:10px; background:#f8fafc; border:1.5px dashed #cbd5e1; border-radius:8px; padding:10px 14px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
              <span style="font-size:11px; font-weight:800; color:#64748b; text-transform:uppercase;">
                <i class="bi bi-eye me-1"></i> Live Render Preview (Siswa di HP akan melihat seperti ini)
              </span>
              <span id="previewTagArab" style="display:none; font-size:10px; font-weight:800; color:#047857; background:#d1fae5; padding:2px 8px; border-radius:10px;">
                Arab RTL Aktif
              </span>
            </div>
            <div id="livePreviewContainer" style="font-size:14px; color:#0f172a; min-height:26px; line-height:1.6;">
              <span style="color:#94a3b8; font-style:italic;">Ketik soal di atas untuk melihat pratinjau rendering formula matematika dan huruf Arab...</span>
            </div>
          </div>
        </div>

        {{-- PILIHAN JAWABAN (A, B, C, D, E) DENGAN SELEKSI LANGSUNG HIJAU --}}
        <div style="margin-bottom:18px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <label class="ak-form-label" style="font-size:13px; font-weight:800; color:#0f172a; margin:0;">
              Pilihan Jawaban &amp; Kunci Benar <span class="text-danger">*</span>
            </label>
            <div style="font-size:11.5px; color:#15803d; font-weight:700;">
              <i class="bi bi-check-circle-fill me-1"></i> Klik opsi untuk langsung menjadikannya Kunci Jawaban (Ditandai Warna Hijau)
            </div>
          </div>

          <div style="display:flex; flex-direction:column; gap:10px;">

            {{-- OPSI A --}}
            <div class="card-opsi-jawaban" id="card_opsi_A" onclick="setKunciJawaban('A')">
              <div class="card-opsi-header">
                <div class="card-opsi-title">
                  <span class="badge-abjad" id="badge_abjad_A">A</span>
                  <span>Pilihan Jawaban A <span class="text-danger">*</span></span>
                </div>
                <button type="button" class="btn-pilih-kunci" id="btn_kunci_A" onclick="setKunciJawaban('A'); event.stopPropagation();">
                  <i class="bi bi-circle"></i>
                  <span>Jadikan Kunci</span>
                </button>
              </div>
              <input type="text" name="opsi_a" id="opsi_A" class="ak-input input-teks-opsi" placeholder="Tuliskan pilihan jawaban A..." value="{{ old('opsi_a') }}" required oninput="cekKesesuaianKunci()" onclick="event.stopPropagation()">
            </div>

            {{-- OPSI B --}}
            <div class="card-opsi-jawaban" id="card_opsi_B" onclick="setKunciJawaban('B')">
              <div class="card-opsi-header">
                <div class="card-opsi-title">
                  <span class="badge-abjad" id="badge_abjad_B">B</span>
                  <span>Pilihan Jawaban B <span class="text-danger">*</span></span>
                </div>
                <button type="button" class="btn-pilih-kunci" id="btn_kunci_B" onclick="setKunciJawaban('B'); event.stopPropagation();">
                  <i class="bi bi-circle"></i>
                  <span>Jadikan Kunci</span>
                </button>
              </div>
              <input type="text" name="opsi_b" id="opsi_B" class="ak-input input-teks-opsi" placeholder="Tuliskan pilihan jawaban B..." value="{{ old('opsi_b') }}" required oninput="cekKesesuaianKunci()" onclick="event.stopPropagation()">
            </div>

            {{-- OPSI C --}}
            <div class="card-opsi-jawaban" id="card_opsi_C" onclick="setKunciJawaban('C')">
              <div class="card-opsi-header">
                <div class="card-opsi-title">
                  <span class="badge-abjad" id="badge_abjad_C">C</span>
                  <span>Pilihan Jawaban C (Opsional)</span>
                </div>
                <button type="button" class="btn-pilih-kunci" id="btn_kunci_C" onclick="setKunciJawaban('C'); event.stopPropagation();">
                  <i class="bi bi-circle"></i>
                  <span>Jadikan Kunci</span>
                </button>
              </div>
              <input type="text" name="opsi_c" id="opsi_C" class="ak-input input-teks-opsi" placeholder="Tuliskan pilihan jawaban C..." value="{{ old('opsi_c') }}" oninput="cekKesesuaianKunci()" onclick="event.stopPropagation()">
            </div>

            {{-- OPSI D --}}
            <div class="card-opsi-jawaban" id="card_opsi_D" onclick="setKunciJawaban('D')">
              <div class="card-opsi-header">
                <div class="card-opsi-title">
                  <span class="badge-abjad" id="badge_abjad_D">D</span>
                  <span>Pilihan Jawaban D (Opsional)</span>
                </div>
                <button type="button" class="btn-pilih-kunci" id="btn_kunci_D" onclick="setKunciJawaban('D'); event.stopPropagation();">
                  <i class="bi bi-circle"></i>
                  <span>Jadikan Kunci</span>
                </button>
              </div>
              <input type="text" name="opsi_d" id="opsi_D" class="ak-input input-teks-opsi" placeholder="Tuliskan pilihan jawaban D..." value="{{ old('opsi_d') }}" oninput="cekKesesuaianKunci()" onclick="event.stopPropagation()">
            </div>

            {{-- OPSI E --}}
            <div class="card-opsi-jawaban" id="card_opsi_E" onclick="setKunciJawaban('E')">
              <div class="card-opsi-header">
                <div class="card-opsi-title">
                  <span class="badge-abjad" id="badge_abjad_E">E</span>
                  <span>Pilihan Jawaban E (Opsional)</span>
                </div>
                <button type="button" class="btn-pilih-kunci" id="btn_kunci_E" onclick="setKunciJawaban('E'); event.stopPropagation();">
                  <i class="bi bi-circle"></i>
                  <span>Jadikan Kunci</span>
                </button>
              </div>
              <input type="text" name="opsi_e" id="opsi_E" class="ak-input input-teks-opsi" placeholder="Tuliskan pilihan jawaban E..." value="{{ old('opsi_e') }}" oninput="cekKesesuaianKunci()" onclick="event.stopPropagation()">
            </div>

          </div>

          <div id="kunciWarning" style="display:none; color:#dc2626; font-size:12px; font-weight:800; margin-top:8px;">
            ⚠️ Peringatan: Opsi yang Anda jadikan kunci jawaban saat ini masih kosong!
          </div>
        </div>

        {{-- Bobot Nilai & Pembahasan --}}
        <div style="display:grid; grid-template-columns: 1fr 2fr; gap:14px; margin-bottom:18px;">
          <div>
            <label class="ak-form-label" style="font-size:13px; font-weight:800;">Bobot Nilai Soal</label>
            <input type="number" name="bobot" class="ak-input" value="{{ old('bobot', 1) }}" min="1" required style="font-size:14px; font-weight:800; text-align:center;">
          </div>
          <div>
            <label class="ak-form-label" style="font-size:13px; font-weight:800;">Pembahasan / Penjelasan (Opsional)</label>
            <input type="text" name="pembahasan" class="ak-input" placeholder="Tampilkan sebagai penjelasan setelah siswa menjawab..." value="{{ old('pembahasan') }}">
          </div>
        </div>

        <button type="submit" class="ak-btn ak-btn-primary" style="width:100%; padding:12px; font-size:14px; font-weight:800; justify-content:center;">
          <i class="bi bi-check2-circle me-1"></i>
          <span>Validasi &amp; Simpan Butir Soal No. {{ $nomorBerikutnya }}</span>
        </button>
      </form>
    </div>
  </div>

  {{-- Right: Daftar Butir Soal Tersimpan --}}
  <div class="akademik-card">
    <div class="akademik-card-header" style="background:#ffffff; border-bottom:1px solid #f1f5f9;">
      <h3 class="akademik-card-title">
        <i class="bi bi-card-checklist text-primary"></i>
        <span>Bank Soal Tersimpan ({{ $asesmen->soals->count() }} Soal)</span>
      </h3>
      <span class="ak-badge ak-badge-success">Total Bobot: {{ $totalBobot }}</span>
    </div>
    <div class="akademik-card-body" style="padding:16px 20px; max-height:780px; overflow-y:auto;" id="savedQuestionsContainer">
      @if($asesmen->soals->isEmpty())
        <div style="padding:40px 20px; text-align:center; color:#64748b;">
          <i class="bi bi-journal-plus" style="font-size:36px; opacity:0.35; display:block; margin-bottom:8px;"></i>
          Belum ada butir soal yang ditambahkan. Gunakan formulir di sebelah kiri untuk menambah soal.
        </div>
      @else
        <div style="display:flex; flex-direction:column; gap:16px;">
          @foreach($asesmen->soals as $idx => $soal)
            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
              <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                <span class="ak-badge ak-badge-primary">No. {{ $idx + 1 }} · Bobot: {{ $soal->bobot }}</span>
                <form action="{{ route('akademik.asesmen.soal.destroy', [$asesmen->id, $soal->id]) }}" method="POST" onsubmit="return confirm('Hapus butir soal no {{ $idx + 1 }}?')" style="margin:0;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="ak-btn ak-btn-secondary ak-btn-sm text-danger" title="Hapus Soal">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>

              {{-- Teks Soal (Render KaTeX / HTML / Arab) --}}
              <div class="render-math-target" style="font-size:13.5px; font-weight:700; color:var(--ak-dark); margin-bottom:10px; line-height:1.6;">
                {!! $soal->pertanyaan !!}
              </div>

              @if($soal->gambar_url)
                <div style="margin-bottom:10px; max-width:260px;">
                  <img src="{{ $soal->gambar_url }}" alt="Gambar Soal" style="width:100%; border-radius:6px; border:1px solid #cbd5e1;">
                </div>
              @endif

              <div style="display:flex; flex-direction:column; gap:4px; font-size:12.5px; color:#475569;">
                <div class="render-math-target" style="{{ $soal->kunci_jawaban == 'A' ? 'font-weight:800; color:#15803d; background:#f0fdf4; padding:3px 8px; border-radius:4px; border:1px solid #bbf7d0;' : '' }}">
                  <strong>A.</strong> {{ $soal->opsi_a }} {!! $soal->kunci_jawaban == 'A' ? ' <i class="bi bi-check-circle-fill text-success ms-1"></i> (Kunci)' : '' !!}
                </div>
                <div class="render-math-target" style="{{ $soal->kunci_jawaban == 'B' ? 'font-weight:800; color:#15803d; background:#f0fdf4; padding:3px 8px; border-radius:4px; border:1px solid #bbf7d0;' : '' }}">
                  <strong>B.</strong> {{ $soal->opsi_b }} {!! $soal->kunci_jawaban == 'B' ? ' <i class="bi bi-check-circle-fill text-success ms-1"></i> (Kunci)' : '' !!}
                </div>
                @if($soal->opsi_c)
                  <div class="render-math-target" style="{{ $soal->kunci_jawaban == 'C' ? 'font-weight:800; color:#15803d; background:#f0fdf4; padding:3px 8px; border-radius:4px; border:1px solid #bbf7d0;' : '' }}">
                    <strong>C.</strong> {{ $soal->opsi_c }} {!! $soal->kunci_jawaban == 'C' ? ' <i class="bi bi-check-circle-fill text-success ms-1"></i> (Kunci)' : '' !!}
                  </div>
                @endif
                @if($soal->opsi_d)
                  <div class="render-math-target" style="{{ $soal->kunci_jawaban == 'D' ? 'font-weight:800; color:#15803d; background:#f0fdf4; padding:3px 8px; border-radius:4px; border:1px solid #bbf7d0;' : '' }}">
                    <strong>D.</strong> {{ $soal->opsi_d }} {!! $soal->kunci_jawaban == 'D' ? ' <i class="bi bi-check-circle-fill text-success ms-1"></i> (Kunci)' : '' !!}
                  </div>
                @endif
                @if($soal->opsi_e)
                  <div class="render-math-target" style="{{ $soal->kunci_jawaban == 'E' ? 'font-weight:800; color:#15803d; background:#f0fdf4; padding:3px 8px; border-radius:4px; border:1px solid #bbf7d0;' : '' }}">
                    <strong>E.</strong> {{ $soal->opsi_e }} {!! $soal->kunci_jawaban == 'E' ? ' <i class="bi bi-check-circle-fill text-success ms-1"></i> (Kunci)' : '' !!}
                  </div>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>

</div>

<style>
  /* Toolbar Styles */
  .toolbar-btn {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 3px 8px;
    font-size: 12px;
    color: #334155;
    cursor: pointer;
    line-height: 1;
    transition: all 0.15s ease;
  }
  .toolbar-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
  }
  .toolbar-select {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 2px 6px;
    font-size: 11.5px;
    color: #334155;
    cursor: pointer;
  }
  .harakat-btn, .math-btn {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 3px 8px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .harakat-btn:hover { background: #e0f2fe; border-color: #38bdf8; }
  .math-btn:hover { background: #dbeafe; border-color: #60a5fa; }

  /* Option Card Styles */
  .card-opsi-jawaban {
    border: 1.5px solid #cbd5e1;
    background: #ffffff;
    border-radius: 10px;
    padding: 10px 14px;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .card-opsi-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
  }
  .card-opsi-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
  }
  .badge-abjad {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #475569;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 900;
    font-size: 12px;
    transition: all 0.2s ease;
  }
  .btn-pilih-kunci {
    border: none;
    border-radius: 20px;
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #f1f5f9;
    color: #64748b;
  }

  /* ACTIVE GREEN HIGHLIGHT */
  .card-opsi-jawaban.is-kunci {
    border: 2px solid #16a34a !important;
    background: #f0fdf4 !important;
    box-shadow: 0 2px 8px rgba(22, 163, 74, 0.12);
  }
  .card-opsi-jawaban.is-kunci .badge-abjad {
    background: #16a34a !important;
    color: #ffffff !important;
  }
  .card-opsi-jawaban.is-kunci .btn-pilih-kunci {
    background: #16a34a !important;
    color: #ffffff !important;
    font-weight: 800;
    box-shadow: 0 2px 6px rgba(22, 163, 74, 0.25);
  }
</style>

<script>
  let isArabMode = false;

  // 1. SELEKSI KUNCI JAWABAN LANGSUNG (TAMPILAN HIJAU)
  function setKunciJawaban(kunci) {
    document.getElementById('inputKunciJawaban').value = kunci;

    ['A', 'B', 'C', 'D', 'E'].forEach(letter => {
      const card = document.getElementById('card_opsi_' + letter);
      const btn = document.getElementById('btn_kunci_' + letter);

      if (letter === kunci) {
        card.classList.add('is-kunci');
        btn.innerHTML = '<i class="bi bi-check-circle-fill"></i><span>KUNCI JAWABAN BENAR</span>';
      } else {
        card.classList.remove('is-kunci');
        btn.innerHTML = '<i class="bi bi-circle"></i><span>Jadikan Kunci</span>';
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

  // 2. TEXT FORMATTING (Bold, Italic, Underline, Wrap)
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

  // 3. MODE BAHASA ARAB (RTL & Amiri Font)
  function toggleModeArab() {
    isArabMode = !isArabMode;
    const textarea = document.getElementById('inputPertanyaan');
    const btn = document.getElementById('btnToggleArab');
    const tagArab = document.getElementById('previewTagArab');
    const preview = document.getElementById('livePreviewContainer');

    if (isArabMode) {
      textarea.setAttribute('dir', 'rtl');
      textarea.style.fontFamily = "'Amiri', 'Traditional Arabic', serif";
      textarea.style.fontSize = '20px';
      textarea.style.textAlign = 'right';
      btn.classList.remove('ak-btn-secondary');
      btn.classList.add('ak-btn-primary');
      if (tagArab) tagArab.style.display = 'inline-block';
      if (preview) {
        preview.setAttribute('dir', 'rtl');
        preview.style.fontFamily = "'Amiri', 'Traditional Arabic', serif";
        preview.style.fontSize = '20px';
        preview.style.textAlign = 'right';
      }
    } else {
      textarea.removeAttribute('dir');
      textarea.style.fontFamily = 'inherit';
      textarea.style.fontSize = '14px';
      textarea.style.textAlign = 'left';
      btn.classList.remove('ak-btn-primary');
      btn.classList.add('ak-btn-secondary');
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
        document.getElementById('infoNamaGambar').innerText = input.files[0].name + ' (' + (input.files[0].size / 1024).toFixed(1) + ' KB)';
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
    const container = document.getElementById('livePreviewContainer');
    if (!text.trim()) {
      container.innerHTML = '<span style="color:#94a3b8; font-style:italic;">Ketik soal di atas untuk melihat pratinjau rendering formula matematika dan huruf Arab...</span>';
      return;
    }

    // Ganti newline dengan <br>
    let formatted = text.replace(/\n/g, '<br>');
    container.innerHTML = formatted;

    // Render KaTeX jika library tersedia
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
      alert("Peringatan Validasi: Anda memilih Kunci Jawaban '" + kunci + "', tetapi teks Pilihan Jawaban " + kunci + " masih kosong. Harap isi teks opsi tersebut sebelum menyimpan.");
      const inputOpsi = document.getElementById('opsi_' + kunci);
      if (inputOpsi) inputOpsi.focus();
      return false;
    }

    const opsiA = document.getElementById('opsi_A').value.trim();
    const opsiB = document.getElementById('opsi_B').value.trim();
    if (opsiA && opsiB && opsiA.toLowerCase() === opsiB.toLowerCase()) {
      alert("Peringatan Validasi: Pilihan Jawaban A dan B memiliki teks yang sama persis!");
      return false;
    }

    return true;
  }

  // Inisialisasi awal saat halaman dimuat
  document.addEventListener('DOMContentLoaded', function() {
    const initialKunci = document.getElementById('inputKunciJawaban').value || 'A';
    setKunciJawaban(initialKunci);
    updateLivePreview();

    // Render KaTeX pada daftar soal tersimpan
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
</script>
@endsection
