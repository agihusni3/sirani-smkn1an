@extends('dcc.akademik.layout')

@section('title', 'Langkah 1: Tentukan Nama Paket Soal')
@section('breadcrumb', 'Buat Paket Asesmen')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Buat Paket Asesmen Baru</h1>
    <div class="akademik-page-desc">
      Langkah 1 dari 3: Tentukan identitas paket soal, mata pelajaran, dan indikator Kurikulum Merdeka.
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

<form action="{{ route('akademik.asesmen.store') }}" method="POST">
  @csrf

  <div style="max-width:880px; margin:0 auto;">
    <div class="akademik-card">
      <div class="akademik-card-header" style="background:#ffffff; border-bottom:1px solid #f1f5f9;">
        <h3 class="akademik-card-title">
          <i class="bi bi-folder-plus text-primary"></i>
          <span>Identitas &amp; Capaian Paket Soal</span>
        </h3>
        <span class="ak-badge ak-badge-primary">Tahap 1: Definisi Paket</span>
      </div>

      <div class="akademik-card-body" style="padding:24px 28px;">
        
        {{-- Mata Pelajaran & Rombel --}}
        <div style="margin-bottom:20px;">
          <label class="ak-form-label" style="font-size:13px; font-weight:800; color:#0f172a;">
            Mata Pelajaran &amp; Rombel Rujukan <span class="text-danger">*</span>
          </label>
          <select name="distribusi_id" id="selectDistribusi" class="ak-select" required style="font-size:14px; padding:10px 14px;">
            <option value="">-- Pilih Mata Pelajaran &amp; Rombel --</option>
            @foreach($distribusis as $d)
              <option value="{{ $d->id }}" {{ old('distribusi_id') == $d->id ? 'selected' : '' }}>
                {{ $d->rombel?->nama_rombel }} — {{ $d->mataPelajaran?->nama_mapel }} (Guru: {{ $d->guru?->nama }})
              </option>
            @endforeach
          </select>
          <div style="font-size:12px; color:#64748b; margin-top:4px;">
            Paket soal ini akan ditautkan ke kurikulum mata pelajaran di atas. Penugasan ke siswa/rombel akan diatur pada Langkah 3.
          </div>
        </div>

        {{-- Judul & Jenis --}}
        <div style="display:grid; grid-template-columns: 2fr 1fr; gap:16px; margin-bottom:20px;">
          <div>
            <label class="ak-form-label" style="font-size:13px; font-weight:800; color:#0f172a;">
              Nama / Judul Paket Soal <span class="text-danger">*</span>
            </label>
            <input type="text" name="judul" id="inputJudul" class="ak-input" 
                   placeholder="Contoh: Ulangan Harian 1 - Pemrograman Web Dasar" 
                   value="{{ old('judul') }}" required style="font-size:14px; padding:10px 14px;">
          </div>
          <div>
            <label class="ak-form-label" style="font-size:13px; font-weight:800; color:#0f172a;">
              Jenis Evaluasi <span class="text-danger">*</span>
            </label>
            <select name="jenis" id="inputJenis" class="ak-select" required style="font-size:14px; padding:10px 14px;">
              <option value="ulangan_harian" selected>Ulangan Harian (UH)</option>
              <option value="kuis">Kuis / Latihan Harian</option>
              <option value="pts">PTS (Sumatif Tengah Semester)</option>
              <option value="pas">PAS (Sumatif Akhir Semester)</option>
              <option value="tugas">Tugas Daring Mandiri</option>
            </select>
          </div>
        </div>

        {{-- TP Rujukan Kurikulum Merdeka --}}
        <div style="margin-bottom:20px;">
          <label class="ak-form-label" style="font-size:13px; font-weight:800; color:#0f172a;">
            <span>Tujuan Pembelajaran (TP) / Capaian Rujukan</span>
            <span class="ak-badge ak-badge-primary" style="font-size:10px; margin-left:6px;">Kurikulum Merdeka</span>
          </label>
          <textarea name="tujuan_pembelajaran" class="ak-textarea" rows="2" 
                    placeholder="Contoh: 10.1 Memahami sintaks dasar PHP dan kontrol percabangan logika dalam pemecahan algoritma.">{{ old('tujuan_pembelajaran') }}</textarea>
          <div style="font-size:11.5px; color:#64748b; margin-top:4px;">
            Menghubungkan paket butir soal dengan capaian kompetensi siswa pada Buku Nilai &amp; Leger KBM.
          </div>
        </div>

        {{-- Petunjuk & Tata Tertib --}}
        <div style="margin-bottom:24px;">
          <label class="ak-form-label" style="font-size:13px; font-weight:800; color:#0f172a;">
            Petunjuk Umum Pengerjaan (Opsional)
          </label>
          <textarea name="deskripsi" id="inputDeskripsi" class="ak-textarea" rows="3" 
                    placeholder="Pilihlah salah satu opsi jawaban yang paling tepat. Dilarang membuka buku atau berpindah jendela aplikasi selama tes berlangsung...">{{ old('deskripsi') }}</textarea>
        </div>

        {{-- Tombol Lanjut ke Tahap 2 --}}
        <div style="border-top:1px solid #e2e8f0; padding-top:20px; display:flex; justify-content:space-between; align-items:center;">
          <div style="font-size:12.5px; color:#64748b;">
            <i class="bi bi-info-circle me-1"></i> Setelah menyimpan paket, Anda akan langsung diarahkan untuk <strong>membuat butir soal &amp; validasi mutu (Langkah 2)</strong>.
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
