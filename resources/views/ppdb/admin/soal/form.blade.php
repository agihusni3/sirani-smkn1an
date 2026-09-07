@php
  $isEdit = isset($soal);
  $tipe = old('tipe_soal', $isEdit ? $soal->tipe_soal : (request('type', 'pg')));
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $isEdit ? 'Edit Soal' : 'Tambah Soal' }} CBT PPDB — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/admin-ppdb.css') }}?v={{ filemtime(public_path('css/admin-ppdb.css')) }}">
</head>
<body>
<div class="app-container">
  @include('partials.sidebar_ppdb')

  <main class="main-content">
    <div style="max-width: 900px; margin: 0 auto; padding: 24px 20px;">

      {{-- BREADCRUMB --}}
      <div style="font-size: 13px; color: #64748b; margin-bottom: 12px;">
        <a href="{{ route('admin.ppdb.seleksi', ['tab' => 'pengaturan']) }}" style="color: #2563eb; text-decoration: none;">PPDB Seleksi</a>
        <span style="margin: 0 6px;">/</span>
        <a href="{{ route('admin.ppdb.soal.index', $setting->id) }}" style="color: #2563eb; text-decoration: none;">Bank Soal CBT</a>
        <span style="margin: 0 6px;">/</span>
        <span style="color: #0f172a; font-weight: 600;">{{ $isEdit ? 'Edit Soal #' . $soal->nomor_urut : 'Tambah Soal Baru' }}</span>
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
          <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 4px;">
            {{ $isEdit ? 'Edit Soal CBT #' . $soal->nomor_urut : 'Tambah Soal CBT Baru' }}
          </h1>
          <div style="font-size: 13px; color: #64748b;">
            {{ $setting->judul_ujian }}
          </div>
        </div>

        <a href="{{ route('admin.ppdb.soal.index', $setting->id) }}" style="padding: 8px 16px; background: #ffffff; border: 1px solid #cbd5e1; color: #334155; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none;">
          &larr; Kembali ke Daftar Soal
        </a>
      </div>

      @if($errors->any())
        <div style="padding: 14px 18px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; color: #991b1b; font-size: 13px; margin-bottom: 20px;">
          <div style="font-weight: 700; margin-bottom: 4px;">Terdapat kesalahan pada input:</div>
          <ul style="margin: 0; padding-left: 18px;">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <form action="{{ $isEdit ? route('admin.ppdb.soal.update', $soal->id) : route('admin.ppdb.soal.store', $setting->id) }}" method="POST">
          @csrf
          @if($isEdit)
            @method('PUT')
          @endif

          {{-- TIPE SOAL & NOMOR URUT --}}
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
              <label style="display: block; font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                Tipe Soal:
              </label>
              <select name="tipe_soal" id="tipe_soal" onchange="toggleTipeSoal()" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-weight: 600;" {{ $isEdit ? 'disabled' : '' }}>
                <option value="pg" {{ $tipe === 'pg' ? 'selected' : '' }}>Pilihan Ganda (PG) - Koreksi Otomatis</option>
                <option value="esai" {{ $tipe === 'esai' ? 'selected' : '' }}>Esai / Uraian - Koreksi Manual</option>
              </select>
              @if($isEdit)
                <input type="hidden" name="tipe_soal" value="{{ $soal->tipe_soal }}">
              @endif
            </div>

            <div>
              <label style="display: block; font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                Nomor Urut Soal:
              </label>
              <input type="number" name="nomor_urut" value="{{ old('nomor_urut', $isEdit ? $soal->nomor_urut : ($nextNomor ?? 1)) }}" min="1" max="100" required style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-weight: 700;">
              <span style="font-size: 11px; color: #64748b;">Standar: 1-30 untuk PG, 31-35 untuk Esai</span>
            </div>
          </div>

          {{-- PERTANYAAN SOAL --}}
          <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
              Isi Pertanyaan / Soal: <span style="color: #dc2626;">*</span>
            </label>
            <textarea name="pertanyaan" rows="5" required placeholder="Ketik pertanyaan atau soal lengkap di sini..." style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; line-height: 1.5; font-family: inherit;">{{ old('pertanyaan', $isEdit ? $soal->pertanyaan : '') }}</textarea>
          </div>

          {{-- SECTION OPSI PG --}}
          <div id="section_pg" style="{{ $tipe === 'esai' ? 'display: none;' : 'display: block;' }}">
            <div style="font-size: 14px; font-weight: 800; color: #0f172a; margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1px solid #e2e8f0;">
              Pilihan Opsi Jawaban (A s/d E) &amp; Kunci Jawaban
            </div>

            @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
              @php 
                $field = 'opsi_' . strtolower($opt); 
                $currVal = old($field, $isEdit ? $soal->$field : '');
                $currKunci = old('kunci_jawaban', $isEdit ? $soal->kunci_jawaban : 'A');
              @endphp
              <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 12px;">
                <div style="width: 38px; height: 38px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #334155; flex-shrink: 0;">
                  {{ $opt }}
                </div>
                <input type="text" name="{{ $field }}" value="{{ $currVal }}" placeholder="Opsi jawaban {{ $opt }}..." style="flex: 1; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                <label style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 700; cursor: pointer; color: #1e293b; background: #f8fafc; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                  <input type="radio" name="kunci_jawaban" value="{{ $opt }}" {{ $currKunci === $opt ? 'checked' : '' }}>
                  Kunci
                </label>
              </div>
            @endforeach
          </div>

          {{-- TOMBOL SIMPAN --}}
          <div style="margin-top: 28px; padding-top: 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px;">
            <a href="{{ route('admin.ppdb.soal.index', $setting->id) }}" style="padding: 10px 18px; background: #f1f5f9; border: 1px solid #cbd5e1; color: #475569; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none;">
              Batal
            </a>
            <button type="submit" style="padding: 10px 24px; background: #2563eb; color: #ffffff; border: none; border-radius: 8px; font-size: 13px; font-weight: 800; cursor: pointer; box-shadow: 0 2px 4px rgba(37,99,235,0.2);">
              {{ $isEdit ? 'Simpan Perubahan Soal' : 'Simpan & Tambah ke Bank Soal' }}
            </button>
          </div>

        </form>
      </div>

    </div>
  </main>
</div>

<script>
  function toggleTipeSoal() {
    const tipe = document.getElementById('tipe_soal').value;
    const secPg = document.getElementById('section_pg');
    if (tipe === 'esai') {
      secPg.style.display = 'none';
    } else {
      secPg.style.display = 'block';
    }
  }
</script>
</body>
</html>
