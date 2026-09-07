@extends('layouts.app')

@php
  $isEdit = isset($soal);
  $tipe = old('tipe_soal', $isEdit ? $soal->tipe_soal : (request('type', 'pg')));
@endphp

@section('title', ($isEdit ? 'Edit Soal' : 'Tambah Soal') . ' CBT PPDB')

@section('content')
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
          <select name="tipe_soal" id="tipe_soal" class="form-control" onchange="toggleTipeSoal()" required style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-weight: 600;">
            <option value="pg" {{ $tipe === 'pg' ? 'selected' : '' }}>Pilihan Ganda (PG) — Koreksi Otomatis</option>
            <option value="esai" {{ $tipe === 'esai' ? 'selected' : '' }}>Esai / Uraian — Koreksi Manual</option>
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
            Nomor Urut Soal:
          </label>
          <input type="number" name="nomor_urut" id="nomor_urut" class="form-control" required min="1"
                 value="{{ old('nomor_urut', $isEdit ? $soal->nomor_urut : ($tipe === 'pg' ? ($nextPg ?? 1) : ($nextEsai ?? 1))) }}"
                 style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-weight: 700;">
          <div style="font-size: 11.5px; color: #64748b; margin-top: 4px;">Nomor urut referensi bank soal (saat siswa mengerjakan, urutan soal akan teracak otomatis)</div>
        </div>
      </div>

      {{-- TEKS PERTANYAAN --}}
      <div style="margin-bottom: 24px;">
        <label style="display: block; font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
          Teks Pertanyaan / Soal:
        </label>
        <textarea name="pertanyaan" class="form-control" rows="5" required placeholder="Tuliskan pertanyaan soal secara lengkap di sini..."
                  style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; line-height: 1.5;">{{ old('pertanyaan', $isEdit ? $soal->pertanyaan : '') }}</textarea>
      </div>

      {{-- BAGIAN PILIHAN GANDA (OPSI A-E & KUNCI) --}}
      <div id="section_pg" style="{{ $tipe === 'esai' ? 'display: none;' : '' }} margin-bottom: 24px; padding: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;">
        <div style="font-size: 14px; font-weight: 800; color: #0f172a; margin-bottom: 4px;">
          Pilihan Opsi & Kunci Jawaban
        </div>
        <div style="font-size: 12px; color: #64748b; margin-bottom: 16px;">
          Isi opsi jawaban A sampai D (opsi E bersifat opsional) dan pilih satu kunci jawaban yang benar.
        </div>

        @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
          @php
            $optLower = strtolower($opt);
            $val = old('opsi_' . $optLower, $isEdit ? $soal->{'opsi_' . $optLower} : '');
            $isKunci = old('kunci_jawaban', $isEdit ? $soal->kunci_jawaban : 'A') === $opt;
          @endphp
          <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 6px; width: 65px; flex-shrink: 0;">
              <input type="radio" name="kunci_jawaban" id="kunci_{{ $opt }}" value="{{ $opt }}" {{ $isKunci ? 'checked' : '' }} style="width: 16px; height: 16px; cursor: pointer; accent-color: #059669;">
              <label for="kunci_{{ $opt }}" style="font-weight: 800; font-size: 13px; color: #1e293b; cursor: pointer;">
                {{ $opt }}.
              </label>
            </div>

            <div style="flex: 1;">
              <input type="text" name="opsi_{{ $optLower }}" id="opsi_{{ $optLower }}" class="form-control"
                     value="{{ $val }}" placeholder="Teks pilihan jawaban {{ $opt }}{{ $opt === 'E' ? ' (opsional)' : '' }}"
                     style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
            </div>
          </div>
        @endforeach

        <div style="margin-top: 10px; font-size: 11.5px; color: #059669; font-weight: 600;">
          &bull; Bulatan radio di sebelah huruf merupakan penentu Kunci Jawaban yang benar.
        </div>
      </div>

      {{-- BOBOT NILAI (OPSIONAL) --}}
      <div style="margin-bottom: 24px; max-width: 250px;">
        <label style="display: block; font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
          Bobot Poin Soal (Opsional):
        </label>
        <input type="number" step="0.5" name="bobot_nilai" class="form-control"
               value="{{ old('bobot_nilai', $isEdit ? $soal->bobot_nilai : 0) }}"
               style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
        <div style="font-size: 11.5px; color: #64748b; margin-top: 4px;">Biarkan 0 untuk menggunakan pembobotan standar sistem.</div>
      </div>

      {{-- SUBMIT BUTTONS --}}
      <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #e2e8f0; padding-top: 18px;">
        <a href="{{ route('admin.ppdb.soal.index', $setting->id) }}" style="padding: 10px 20px; background: #ffffff; border: 1px solid #cbd5e1; color: #334155; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none;">
          Batal
        </a>
        <button type="submit" style="padding: 10px 24px; background: #2563eb; color: #ffffff; border: none; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;">
          {{ $isEdit ? 'Simpan Perubahan Soal' : 'Simpan & Tambah ke Bank Soal' }}
        </button>
      </div>

    </form>
  </div>

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
@endsection
