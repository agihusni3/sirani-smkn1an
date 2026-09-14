@php
  $isEdit = isset($soal);
  $tipe = old('tipe_soal', $isEdit ? $soal->tipe_soal : ($type ?? request('type', 'pg')));
  $cssVersion = file_exists(public_path('css/admin-ppdb.css')) ? filemtime(public_path('css/admin-ppdb.css')) : time();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $isEdit ? 'Edit Soal' : 'Tambah Soal' }} CBT PPDB — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/admin-ppdb.css') }}?v={{ $cssVersion }}">
</head>
<body>
<div class="app-container">
  @include('partials.sidebar_ppdb')

  <main class="main-content">
    <div class="cbt-container">

      {{-- BREADCRUMB --}}
      <nav class="cbt-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.ppdb.seleksi', ['tab' => 'pengaturan']) }}">PPDB Seleksi</a>
        <span class="cbt-breadcrumb-separator">/</span>
        <a href="{{ route('admin.ppdb.soal.index', $setting->id) }}">Bank Soal CBT</a>
        <span class="cbt-breadcrumb-separator">/</span>
        <span class="cbt-breadcrumb-current">{{ $isEdit ? 'Edit Soal #' . $soal->nomor_urut : 'Tambah Soal Baru' }}</span>
      </nav>

      {{-- HEADER --}}
      <div class="cbt-page-header">
        <div>
          <h1 class="cbt-page-title">
            {{ $isEdit ? 'Edit Soal CBT #' . $soal->nomor_urut : 'Tambah Soal CBT Baru' }}
          </h1>
          <div class="cbt-page-subtitle">
            {{ $setting->judul_ujian }} &bull; PPDB SMKN 1 Air Naningan
          </div>
        </div>

        <a href="{{ route('admin.ppdb.soal.index', $setting->id) }}" class="cbt-btn cbt-btn-secondary">
          &larr; Kembali ke Daftar Soal
        </a>
      </div>

      {{-- ERROR ALERT SUMMARY --}}
      @if($errors->any())
        <div class="cbt-alert cbt-alert-danger" role="alert">
          <div>
            <strong>Terdapat kesalahan pada formulir soal:</strong>
            <ul>
              @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif

      {{-- CARD FORM --}}
      <div class="cbt-card">
        <form action="{{ $isEdit ? route('admin.ppdb.soal.update', $soal->id) : route('admin.ppdb.soal.store', $setting->id) }}" method="POST" id="formSoal">
          @csrf
          @if($isEdit)
            @method('PUT')
          @endif

          {{-- TIPE SOAL & NOMOR URUT --}}
          <div class="cbt-form-grid">
            <div class="cbt-form-group">
              <label for="tipe_soal" class="cbt-label">
                Tipe Soal <span class="cbt-label-req">*</span>
              </label>
              <select name="tipe_soal" id="tipe_soal" class="cbt-control @error('tipe_soal') is-invalid @enderror" onchange="handleTipeChange()" {{ $isEdit ? 'disabled' : '' }}>
                <option value="pg" {{ $tipe === 'pg' ? 'selected' : '' }}>Pilihan Ganda (PG) — Koreksi Otomatis</option>
                <option value="esai" {{ $tipe === 'esai' ? 'selected' : '' }}>Esai / Uraian — Koreksi Manual</option>
              </select>
              @if($isEdit)
                <input type="hidden" name="tipe_soal" value="{{ $soal->tipe_soal }}">
              @endif
              @error('tipe_soal')
                <span class="cbt-error-feedback">{{ $message }}</span>
              @enderror
            </div>

            <div class="cbt-form-group">
              <label for="nomor_urut" class="cbt-label">
                Nomor Urut Soal <span class="cbt-label-req">*</span>
              </label>
              <input type="number" 
                     id="nomor_urut" 
                     name="nomor_urut" 
                     value="{{ old('nomor_urut', $isEdit ? $soal->nomor_urut : ($nextNomor ?? 1)) }}" 
                     min="1" 
                     max="500" 
                     required 
                     class="cbt-control @error('nomor_urut') is-invalid @enderror">
              <span class="cbt-hint">Standar rekomendasi: 1-{{ $setting->jumlah_soal_pg ?: 30 }} untuk PG, dilanjutkan untuk Esai</span>
              @error('nomor_urut')
                <span class="cbt-error-feedback">{{ $message }}</span>
              @enderror
            </div>
          </div>

          {{-- PERTANYAAN SOAL --}}
          <div class="cbt-form-group">
            <label for="pertanyaan" class="cbt-label">
              Isi Pertanyaan / Soal <span class="cbt-label-req">*</span>
            </label>
            <textarea name="pertanyaan" 
                      id="pertanyaan" 
                      rows="6" 
                      required 
                      placeholder="Ketik pertanyaan atau soal lengkap di sini..." 
                      class="cbt-control @error('pertanyaan') is-invalid @enderror">{{ old('pertanyaan', $isEdit ? $soal->pertanyaan : '') }}</textarea>
            @error('pertanyaan')
              <span class="cbt-error-feedback">{{ $message }}</span>
            @enderror
          </div>

          {{-- SECTION OPSI PG --}}
          <div id="section_pg" style="{{ $tipe === 'esai' ? 'display: none;' : 'display: block;' }}">
            <div class="cbt-section-title">
              <span>Pilihan Opsi Jawaban (A s/d E) &amp; Kunci Jawaban</span>
              <span class="cbt-hint" style="margin: 0;">Pilih radio "Kunci" pada opsi yang benar</span>
            </div>

            @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
              @php 
                $field = 'opsi_' . strtolower($opt); 
                $currVal = old($field, $isEdit ? $soal->$field : '');
                $currKunci = old('kunci_jawaban', $isEdit ? $soal->kunci_jawaban : 'A');
                $isKunci = ($currKunci === $opt);
              @endphp
              <div class="cbt-opsi-row">
                <div class="cbt-opsi-letter" title="Opsi {{ $opt }}">
                  {{ $opt }}
                </div>
                
                <input type="text" 
                       id="{{ $field }}"
                       name="{{ $field }}" 
                       value="{{ $currVal }}" 
                       placeholder="Opsi jawaban {{ $opt }}..." 
                       class="cbt-control cbt-opsi-input @error($field) is-invalid @enderror">

                <label class="cbt-kunci-toggle {{ $isKunci ? 'is-active' : '' }}" id="label_kunci_{{ $opt }}">
                  <input type="radio" 
                         name="kunci_jawaban" 
                         value="{{ $opt }}" 
                         {{ $isKunci ? 'checked' : '' }}
                         onchange="updateKunciHighlight('{{ $opt }}')">
                  Kunci
                </label>
              </div>
              @error($field)
                <span class="cbt-error-feedback" style="margin-bottom: 8px;">{{ $message }}</span>
              @enderror
            @endforeach

            @error('kunci_jawaban')
              <span class="cbt-error-feedback" style="margin-top: 6px;">{{ $message }}</span>
            @enderror
          </div>

          {{-- TOMBOL AKSI --}}
          <div class="cbt-form-actions">
            <a href="{{ route('admin.ppdb.soal.index', $setting->id) }}" class="cbt-btn cbt-btn-secondary">
              Batal
            </a>
            <button type="submit" class="cbt-btn cbt-btn-primary">
              <i class="bi bi-check2-circle"></i>
              {{ $isEdit ? 'Simpan Perubahan Soal' : 'Simpan ke Bank Soal' }}
            </button>
          </div>

        </form>
      </div>

    </div>
  </main>
</div>

<script>
  const isEditMode = {{ $isEdit ? 'true' : 'false' }};
  const nextPgVal = {{ $nextPg ?? 1 }};
  const nextEsaiVal = {{ $nextEsai ?? 31 }};

  function handleTipeChange() {
    const tipeEl = document.getElementById('tipe_soal');
    if (!tipeEl) return;

    const tipe = tipeEl.value;
    const secPg = document.getElementById('section_pg');
    const nomorInput = document.getElementById('nomor_urut');

    if (tipe === 'esai') {
      secPg.style.display = 'none';
      if (!isEditMode && (nomorInput.value === String(nextPgVal) || !nomorInput.value)) {
        nomorInput.value = nextEsaiVal;
      }
    } else {
      secPg.style.display = 'block';
      if (!isEditMode && (nomorInput.value === String(nextEsaiVal) || !nomorInput.value)) {
        nomorInput.value = nextPgVal;
      }
    }
  }

  function updateKunciHighlight(selectedOpt) {
    ['A', 'B', 'C', 'D', 'E'].forEach(function(opt) {
      const lbl = document.getElementById('label_kunci_' + opt);
      if (lbl) {
        if (opt === selectedOpt) {
          lbl.classList.add('is-active');
        } else {
          lbl.classList.remove('is-active');
        }
      }
    });
  }
</script>
</body>
</html>
