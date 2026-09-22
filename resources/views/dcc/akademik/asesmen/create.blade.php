@extends('dcc.akademik.layout')

@section('title', 'Langkah 1: Tentukan Nama Paket Soal & KKM')
@section('breadcrumb', 'Buat Paket Asesmen')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Buat Paket Asesmen Baru</h1>
    <div class="akademik-page-desc">
      Langkah 1 dari 3: Tentukan identitas paket soal, mata pelajaran, target jumlah soal, dan KKM.
    </div>
  </div>

  <div>
    <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary">
      <i class="bi bi-arrow-left"></i>
      <span>Batal &amp; Kembali</span>
    </a>
  </div>
</div>

{{-- Step Indicator --}}
@include('dcc.akademik.asesmen.partials.wizard_steps', ['step' => 1])

<form action="{{ route('akademik.asesmen.store') }}" method="POST" id="formBuatPaket">
  @csrf

  <div style="max-width:820px; margin:0 auto;">
    <div class="akademik-card">
      <div class="akademik-card-header" style="background:#ffffff; border-bottom:1px solid #f1f5f9;">
        <h3 class="akademik-card-title">
          <i class="bi bi-folder-plus text-primary"></i>
          <span>Identitas &amp; Ketentuan Paket Soal</span>
        </h3>
        <span class="ak-badge ak-badge-primary">Tahap 1: Definisi Paket</span>
      </div>

      <div class="akademik-card-body" style="padding:26px 30px;">
        
        {{-- Mata Pelajaran --}}
        <div style="margin-bottom:22px;">
          <label class="ak-form-label" style="font-size:13.5px; font-weight:800; color:#0f172a;">
            Mata Pelajaran yang Diampu <span class="text-danger">*</span>
          </label>
          <select name="mata_pelajaran_id" id="selectMapel" class="ak-select" required style="font-size:14px; padding:11px 14px;">
            <option value="">-- Pilih Mata Pelajaran --</option>
            @foreach($mapels as $m)
              <option value="{{ $m->id }}" {{ (old('mata_pelajaran_id') == $m->id || $mapels->count() === 1) ? 'selected' : '' }}>
                {{ $m->nama_mapel }}
              </option>
            @endforeach
          </select>
          <div style="font-size:12px; color:#64748b; margin-top:5px;">
            Pilih mata pelajaran yang Anda ampu untuk paket asesmen ini.
          </div>
        </div>

        {{-- Nama / Judul Paket Soal --}}
        <div style="margin-bottom:22px;">
          <label class="ak-form-label" style="font-size:13.5px; font-weight:800; color:#0f172a;">
            Nama / Judul Paket Soal <span class="text-danger">*</span>
          </label>
          <input type="text" name="judul" id="inputJudul" class="ak-input" 
                 placeholder="Contoh: Ulangan Harian 1 - Pemrograman Web Dasar" 
                 value="{{ old('judul') }}" required style="font-size:14px; padding:11px 14px;">
          <div style="font-size:12px; color:#64748b; margin-top:5px;">
            Gunakan judul yang spesifik dan mudah dikenali oleh guru maupun siswa.
          </div>
        </div>

        {{-- Grid: Jenis Evaluasi, Rencana Jumlah Soal, dan KKM --}}
        <div style="display:grid; grid-template-columns: 1.4fr 1fr 1fr; gap:16px; margin-bottom:26px;">
          
          {{-- Jenis Evaluasi --}}
          <div>
            <label class="ak-form-label" style="font-size:13px; font-weight:800; color:#0f172a;">
              Jenis Evaluasi <span class="text-danger">*</span>
            </label>
            <select name="jenis" id="inputJenis" class="ak-select" required style="font-size:14px; padding:10px 14px;">
              <option value="ulangan_harian" {{ old('jenis') == 'ulangan_harian' ? 'selected' : '' }}>Ulangan Harian (UH)</option>
              <option value="kuis" {{ old('jenis') == 'kuis' ? 'selected' : '' }}>Kuis / Latihan Harian</option>
              <option value="pts" {{ old('jenis') == 'pts' ? 'selected' : '' }}>PTS (Sumatif Tengah Semester)</option>
              <option value="pas" {{ old('jenis') == 'pas' ? 'selected' : '' }}>PAS (Sumatif Akhir Semester)</option>
              <option value="tugas" {{ old('jenis') == 'tugas' ? 'selected' : '' }}>Tugas Daring Mandiri</option>
            </select>
            <div style="font-size:11.5px; color:#64748b; margin-top:4px;">
              Kategori penilaian akademik.
            </div>
          </div>

          {{-- Target / Rencana Jumlah Soal --}}
          <div>
            <label class="ak-form-label" style="font-size:13px; font-weight:800; color:#0f172a;">
              Target Jumlah Soal <span class="text-danger">*</span>
            </label>
            <div style="position:relative;">
              <input type="number" name="target_jumlah_soal" id="inputJumlahSoal" class="ak-input" 
                     min="1" max="200" value="{{ old('target_jumlah_soal', 10) }}" required 
                     style="font-size:14px; padding:10px 48px 10px 14px; font-weight:700;">
              <span style="position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:12px; color:#64748b; font-weight:600; pointer-events:none;">
                Butir
              </span>
            </div>
            <div style="font-size:11.5px; color:#64748b; margin-top:4px;">
              Rencana butir soal disusun.
            </div>
          </div>

          {{-- Standar Nilai Minimal / KKM --}}
          <div>
            <label class="ak-form-label" style="font-size:13px; font-weight:800; color:#0f172a;">
              KKM / Standar Minimal <span class="text-danger">*</span>
            </label>
            <div style="position:relative;">
              <input type="number" name="passing_grade" id="inputPassingGrade" class="ak-input" 
                     min="0" max="100" value="{{ old('passing_grade', 75) }}" required 
                     style="font-size:14px; padding:10px 44px 10px 14px; font-weight:700; color:#2563eb;">
              <span style="position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:12px; color:#64748b; font-weight:600; pointer-events:none;">
                / 100
              </span>
            </div>
            <div style="font-size:11.5px; color:#64748b; margin-top:4px;">
              Batas nilai ketuntasan (KKTP).
            </div>
          </div>

        </div>

        {{-- Notice Banner Penugasan Dipisah --}}
        <div style="padding:14px 18px; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:10px; margin-bottom:24px; display:flex; align-items:flex-start; gap:12px;">
          <i class="bi bi-info-circle-fill text-primary" style="font-size:18px; margin-top:2px;"></i>
          <div style="font-size:12.5px; color:#475569; line-height:1.5;">
            <strong>Catatan Alur:</strong> Pemilihan kelas/rombel sasaran peserta ujian, pembagian token, jadwal buka-tutup, dan proteksi anti-curang dilakukan secara terpusat pada <strong>Langkah 3 (Sesi Penugasan)</strong> setelah Anda selesai menyusun butir-butir soal.
          </div>
        </div>

        {{-- Tombol Lanjut ke Tahap 2 --}}
        <div style="border-top:1px solid #e2e8f0; padding-top:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
          <div style="font-size:12.5px; color:#64748b;">
            <i class="bi bi-check2-circle text-success me-1"></i> Data paket akan disimpan dan dilanjutkan ke <strong>Penyusunan Butir Soal (Langkah 2)</strong>.
          </div>
          <button type="submit" class="ak-btn ak-btn-primary" style="padding:12px 24px; font-size:14px; font-weight:800;">
            <span>Lanjut: Buat Butir Soal &amp; Validasi</span>
            <i class="bi bi-arrow-right ms-1"></i>
          </button>
        </div>

      </div>
    </div>
  </div>

</form>
@endsection
