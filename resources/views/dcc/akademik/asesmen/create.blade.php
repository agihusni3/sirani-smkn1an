@extends('dcc.akademik.layout')

@section('title', 'Buat Asesmen Online Baru')
@section('breadcrumb', 'Buat Asesmen')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Buat Asesmen Penilaian Online Baru</h1>
    <div class="akademik-page-desc">
      Konfigurasikan judul, durasi pengerjaan, KKM kelulusan, dan jadwal pelaksanaan asesmen berbasis CBT.
    </div>
  </div>

  <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary">
    <i class="bi bi-arrow-left"></i>
    <span>Kembali</span>
  </a>
</div>

<div class="akademik-card" style="max-width:800px;">
  <div class="akademik-card-header">
    <h3 class="akademik-card-title">
      <i class="bi bi-laptop text-primary"></i>
      <span>Formulir Pengaturan Asesmen</span>
    </h3>
  </div>
  <div class="akademik-card-body">
    <form action="{{ route('akademik.asesmen.store') }}" method="POST">
      @csrf

      <div style="margin-bottom:16px;">
        <label class="ak-form-label">Mata Pelajaran &amp; Rombel Sasaran <span class="text-danger">*</span></label>
        <select name="distribusi_id" class="ak-select" required>
          <option value="">-- Pilih Mata Pelajaran &amp; Rombel --</option>
          @foreach($distribusis as $d)
            <option value="{{ $d->id }}">
              {{ $d->rombel?->nama_rombel }} — {{ $d->mataPelajaran?->nama_mapel }} ({{ $d->guru?->nama }})
            </option>
          @endforeach
        </select>
      </div>

      <div style="margin-bottom:16px;">
        <label class="ak-form-label">Judul Asesmen / Ujian <span class="text-danger">*</span></label>
        <input type="text" name="judul" class="ak-input" placeholder="Contoh: Kuis 1 Dasar Pemrograman, STS Ganjil 2025/2026" required>
      </div>

      <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:14px; margin-bottom:16px;">
        <div>
          <label class="ak-form-label">Jenis Evaluasi</label>
          <select name="jenis" class="ak-select" required>
            <option value="kuis">Kuis Harian</option>
            <option value="ulangan_harian" selected>Ulangan Harian (UH)</option>
            <option value="pts">PTS (Tengah Semester)</option>
            <option value="pas">PAS (Akhir Semester)</option>
            <option value="tugas">Tugas Daring</option>
          </select>
        </div>
        <div>
          <label class="ak-form-label">Durasi Pengerjaan (Menit)</label>
          <input type="number" name="durasi_menit" class="ak-input" value="60" min="5" max="300" required>
        </div>
        <div>
          <label class="ak-form-label">Standar Kelulusan (KKM)</label>
          <input type="number" name="passing_grade" class="ak-input" value="70" min="0" max="100" required>
        </div>
      </div>

      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:14px; margin-bottom:16px;">
        <div>
          <label class="ak-form-label">Waktu Dibuka (Opsional)</label>
          <input type="datetime-local" name="dibuka_pada" class="ak-input">
        </div>
        <div>
          <label class="ak-form-label">Waktu Ditutup (Opsional)</label>
          <input type="datetime-local" name="ditutup_pada" class="ak-input">
        </div>
      </div>

      <div style="margin-bottom:16px;">
        <label class="ak-form-label">Petunjuk Pengerjaan / Deskripsi</label>
        <textarea name="deskripsi" class="ak-textarea" rows="3" placeholder="Pilihlah salah satu jawaban yang paling tepat. Waktu pengerjaan akan otomatis berjalan..."></textarea>
      </div>

      <div style="background:#f8fafc; padding:14px 18px; border-radius:10px; border:1px solid #e2e8f0; margin-bottom:20px;">
        <div style="font-weight:700; font-size:13px; margin-bottom:10px; color:var(--ak-dark);">Pengaturan Tambahan</div>
        <div style="display:flex; flex-direction:column; gap:8px;">
          <label style="display:flex; align-items:center; gap:8px; font-size:13px; cursor:pointer;">
            <input type="checkbox" name="acak_soal" value="1" checked>
            <span>Acak urutan butir soal untuk setiap siswa</span>
          </label>
          <label style="display:flex; align-items:center; gap:8px; font-size:13px; cursor:pointer;">
            <input type="checkbox" name="tampilkan_nilai" value="1" checked>
            <span>Tampilkan skor perolehan nilai langsung setelah siswa menyelesaikan ujian</span>
          </label>
          <label style="display:flex; align-items:center; gap:8px; font-size:13px; cursor:pointer;">
            <input type="checkbox" name="is_active" value="1" checked>
            <span>Aktifkan asesmen ini sekarang (siap dikerjakan)</span>
          </label>
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px;">
        <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary">Batal</a>
        <button type="submit" class="ak-btn ak-btn-primary">
          <i class="bi bi-arrow-right"></i>
          <span>Lanjut ke Input Butir Soal</span>
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
