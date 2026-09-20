@extends('dcc.akademik.layout')

@section('title', 'Isi Jurnal KBM Harian')
@section('breadcrumb', 'Isi Jurnal KBM')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Input Jurnal KBM &amp; Presensi Siswa</h1>
    <div class="akademik-page-desc">
      Catat ringkasan topik materi yang diajarkan dan verifikasi kehadiran siswa di kelas.
    </div>
  </div>

  <a href="{{ route('akademik.jurnal.index') }}" class="ak-btn ak-btn-secondary">
    <i class="bi bi-arrow-left"></i>
    <span>Kembali</span>
  </a>
</div>

{{-- Step 1: Pilih Kelas & Mapel --}}
<div class="akademik-card" style="margin-bottom:20px;">
  <div class="akademik-card-header">
    <h3 class="akademik-card-title">
      <i class="bi bi-1-circle text-primary"></i>
      <span>Pilih Jadwal &amp; Rombel Mengajar</span>
    </h3>
  </div>
  <div class="akademik-card-body">
    <form action="{{ route('akademik.jurnal.create') }}" method="GET" id="formPilihDistribusi">
      <div style="max-width:600px;">
        <label class="ak-form-label">Jadwal Mapel &amp; Rombel</label>
        <select name="distribusi_id" class="ak-select" onchange="this.form.submit()" required>
          <option value="">-- Pilih Mata Pelajaran &amp; Rombel --</option>
          @foreach($distribusis as $d)
            <option value="{{ $d->id }}" {{ (request('distribusi_id') == $d->id || ($selectedDistribusi && $selectedDistribusi->id == $d->id)) ? 'selected' : '' }}>
              {{ $d->rombel?->nama_rombel }} — {{ $d->mataPelajaran?->nama_mapel }} ({{ $d->guru?->nama }})
            </option>
          @endforeach
        </select>
        <span style="font-size:12px; color:#64748b; margin-top:4px; display:block;">
          Pilih salah satu jadwal mengajar untuk memuat daftar siswa rombel bersangkutan.
        </span>
      </div>
    </form>
  </div>
</div>

@if($selectedDistribusi)
  <form action="{{ route('akademik.jurnal.store') }}" method="POST">
    @csrf
    <input type="hidden" name="distribusi_id" value="{{ $selectedDistribusi->id }}">

    {{-- Step 2: Detail Sesi KBM --}}
    <div class="akademik-card" style="margin-bottom:20px;">
      <div class="akademik-card-header">
        <h3 class="akademik-card-title">
          <i class="bi bi-2-circle text-primary"></i>
          <span>Materi &amp; Pelaksanaan Pembelajaran</span>
        </h3>
        <span class="ak-badge ak-badge-primary">{{ $selectedDistribusi->rombel?->nama_rombel }} · {{ $selectedDistribusi->mataPelajaran?->nama_mapel }}</span>
      </div>
      <div class="akademik-card-body">
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:16px;">
          <div>
            <label class="ak-form-label">Tanggal Pelaksanaan</label>
            <input type="date" name="tanggal" class="ak-input" value="{{ date('Y-m-d') }}" required>
          </div>
          <div>
            <label class="ak-form-label">Pertemuan Ke-</label>
            <input type="number" name="pertemuan_ke" class="ak-input" value="{{ $pertemuanKe }}" min="1" required>
          </div>
          <div>
            <label class="ak-form-label">Metode Pembelajaran (Opsional)</label>
            <input type="text" name="metode_pembelajaran" class="ak-input" placeholder="Ceramah, Praktik, Diskusi...">
          </div>
        </div>

        <div style="margin-bottom:16px;">
          <label class="ak-form-label">Materi Pokok / Pembahasan <span class="text-danger">*</span></label>
          <textarea name="materi_ajar" class="ak-textarea" rows="3" placeholder="Tuliskan pokok materi / modul ajar / kompetensi yang diajarkan pada sesi ini..." required></textarea>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
          <div>
            <label class="ak-form-label">Catatan Guru / Kejadian di Kelas</label>
            <textarea name="catatan_guru" class="ak-textarea" rows="2" placeholder="Catatan keaktifan siswa, kendala perangkat..."></textarea>
          </div>
          <div>
            <label class="ak-form-label">Refleksi / Tindak Lanjut Guru</label>
            <textarea name="refleksi" class="ak-textarea" rows="2" placeholder="Tindak lanjut untuk pertemuan berikutnya..."></textarea>
          </div>
        </div>
      </div>
    </div>

    {{-- Step 3: Presensi Siswa Sesi Ini --}}
    <div class="akademik-card">
      <div class="akademik-card-header">
        <h3 class="akademik-card-title">
          <i class="bi bi-3-circle text-primary"></i>
          <span>Presensi Kehadiran Siswa ({{ $siswas->count() }} Siswa)</span>
        </h3>
        <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" onclick="setAllStatus('hadir')">
          <i class="bi bi-check-all"></i>
          <span>Set Semua Hadir</span>
        </button>
      </div>
      <div class="akademik-card-body" style="padding:0;">
        @if($siswas->isEmpty())
          <div style="padding:30px; text-align:center; color:#64748b;">
            Belum ada siswa aktif terdaftar di rombel ini.
          </div>
        @else
          <div class="akademik-table-wrap">
            <table class="akademik-table">
              <thead>
                <tr>
                  <th style="width:40px;">No</th>
                  <th>NISN</th>
                  <th>Nama Lengkap Siswa</th>
                  <th style="width:340px; text-align:center;">Status Kehadiran</th>
                  <th>Keterangan</th>
                </tr>
              </thead>
              <tbody>
                @foreach($siswas as $idx => $s)
                  <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td><code>{{ $s->nisn ?? '-' }}</code></td>
                    <td style="font-weight:700; color:var(--ak-dark);">{{ $s->nama_lengkap }}</td>
                    <td style="text-align:center;">
                      <div style="display:inline-flex; gap:14px; align-items:center;">
                        <label style="cursor:pointer; display:flex; align-items:center; gap:4px; font-weight:600; font-size:12px; color:#059669;">
                          <input type="radio" name="kehadiran[{{ $s->id }}]" value="hadir" checked class="radio-status-hadir">
                          Hadir
                        </label>
                        <label style="cursor:pointer; display:flex; align-items:center; gap:4px; font-weight:600; font-size:12px; color:#2563eb;">
                          <input type="radio" name="kehadiran[{{ $s->id }}]" value="izin">
                          Izin
                        </label>
                        <label style="cursor:pointer; display:flex; align-items:center; gap:4px; font-weight:600; font-size:12px; color:#d97706;">
                          <input type="radio" name="kehadiran[{{ $s->id }}]" value="sakit">
                          Sakit
                        </label>
                        <label style="cursor:pointer; display:flex; align-items:center; gap:4px; font-weight:600; font-size:12px; color:#dc2626;">
                          <input type="radio" name="kehadiran[{{ $s->id }}]" value="alfa">
                          Alfa
                        </label>
                      </div>
                    </td>
                    <td>
                      <input type="text" name="keterangan[{{ $s->id }}]" class="ak-input" style="padding:4px 8px; font-size:12px;" placeholder="Ket. khusus (opsional)">
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div style="padding:20px; border-top:1px solid var(--ak-slate-200); display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('akademik.jurnal.index') }}" class="ak-btn ak-btn-secondary">Batal</a>
            <button type="submit" class="ak-btn ak-btn-primary">
              <i class="bi bi-save"></i>
              <span>Simpan Jurnal &amp; Presensi KBM</span>
            </button>
          </div>
        @endif
      </div>
    </div>
  </form>
@endif

@push('scripts')
<script>
  function setAllStatus(val) {
    document.querySelectorAll('.radio-status-' + val).forEach(el => el.checked = true);
  }
</script>
@endpush
@endsection
