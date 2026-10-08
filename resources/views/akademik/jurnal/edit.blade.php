@extends('akademik.layout')

@section('title', 'Edit Jurnal KBM Pertemuan ' . $jurnal->pertemuan_ke)
@section('breadcrumb', 'Edit Jurnal KBM')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Edit Jurnal KBM &amp; Presensi Siswa</h1>
    <div class="akademik-page-desc">
      Perbarui ringkasan materi, catatan pelaksanaan KBM, dan koreksi status kehadiran siswa.
    </div>
  </div>

  <div style="display:flex; gap:8px;">
    <a href="{{ route('akademik.jurnal.show', $jurnal->id) }}" class="ak-btn ak-btn-secondary">
      <i class="bi bi-eye"></i>
      <span>Lihat Detail</span>
    </a>
    <a href="{{ route('akademik.jurnal.index') }}" class="ak-btn ak-btn-secondary">
      <i class="bi bi-arrow-left"></i>
      <span>Kembali</span>
    </a>
  </div>
</div>

<form action="{{ route('akademik.jurnal.update', $jurnal->id) }}" method="POST">
  @csrf
  @method('PUT')

  {{-- Step 1: Informasi Kelas & Mapel --}}
  <div class="akademik-card" style="margin-bottom:20px;">
    <div class="akademik-card-header">
      <h3 class="akademik-card-title">
        <i class="bi bi-info-circle text-primary"></i>
        <span>Informasi Mata Pelajaran &amp; Rombel</span>
      </h3>
      <span class="ak-badge ak-badge-primary">
        {{ $jurnal->distribusi?->rombel?->nama_rombel }} · {{ $jurnal->distribusi?->mataPelajaran?->nama_mapel }}
      </span>
    </div>
    <div class="akademik-card-body">
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
        <div>
          <label class="ak-form-label">Guru Pengampu</label>
          <input type="text" class="ak-input" value="{{ $jurnal->distribusi?->guru?->nama ?? '-' }}" disabled style="background:#f8fafc;">
        </div>
        <div>
          <label class="ak-form-label">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
          <input type="date" name="tanggal" class="ak-input" value="{{ old('tanggal', $jurnal->tanggal?->format('Y-m-d') ?? $jurnal->tanggal) }}" required>
        </div>
        <div>
          <label class="ak-form-label">Pertemuan Ke- <span class="text-danger">*</span></label>
          <input type="number" name="pertemuan_ke" class="ak-input" value="{{ old('pertemuan_ke', $jurnal->pertemuan_ke) }}" min="1" required>
        </div>
        <div>
          <label class="ak-form-label">Metode Pembelajaran</label>
          <input type="text" name="metode_pembelajaran" class="ak-input" value="{{ old('metode_pembelajaran', $jurnal->metode_pembelajaran) }}" placeholder="Ceramah, Praktik, Diskusi...">
        </div>
      </div>

      <div style="margin-top:16px;">
        <label class="ak-form-label">Materi Pokok / Pembahasan <span class="text-danger">*</span></label>
        <textarea name="materi_ajar" id="input_materi_ajar" class="ak-textarea" rows="3" placeholder="Tuliskan pokok materi / modul ajar / kompetensi yang diajarkan..." required>{{ old('materi_ajar', $jurnal->materi_ajar) }}</textarea>
      </div>

      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-top:16px;">
        <div>
          <label class="ak-form-label">Catatan Guru / Kejadian di Kelas</label>
          <textarea name="catatan_guru" class="ak-textarea" rows="2" placeholder="Catatan keaktifan siswa, kendala perangkat...">{{ old('catatan_guru', $jurnal->catatan_guru) }}</textarea>
        </div>
        <div>
          <label class="ak-form-label">Refleksi / Tindak Lanjut Guru</label>
          <textarea name="refleksi" class="ak-textarea" rows="2" placeholder="Tindak lanjut untuk pertemuan berikutnya...">{{ old('refleksi', $jurnal->refleksi) }}</textarea>
        </div>
      </div>
    </div>
  </div>

  {{-- Step 2: Presensi Siswa Sesi Ini --}}
  <div class="akademik-card">
    <div class="akademik-card-header">
      <h3 class="akademik-card-title">
        <i class="bi bi-people text-primary"></i>
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
                @php
                  $kItem = $kehadiranMap->get($s->id);
                  $currentStatus = $kItem?->status ?? 'hadir';
                  $currentKet = $kItem?->keterangan ?? '';
                @endphp
                <tr>
                  <td>{{ $idx + 1 }}</td>
                  <td><code>{{ $s->nisn ?? '-' }}</code></td>
                  <td style="font-weight:700; color:var(--ak-dark);">{{ $s->nama_lengkap }}</td>
                  <td style="text-align:center;">
                    <div style="display:inline-flex; gap:14px; align-items:center;">
                      <label style="cursor:pointer; display:flex; align-items:center; gap:4px; font-weight:600; font-size:12px; color:#059669;">
                        <input type="radio" name="kehadiran[{{ $s->id }}]" value="hadir" {{ $currentStatus === 'hadir' ? 'checked' : '' }} class="radio-status-hadir">
                        Hadir
                      </label>
                      <label style="cursor:pointer; display:flex; align-items:center; gap:4px; font-weight:600; font-size:12px; color:#2563eb;">
                        <input type="radio" name="kehadiran[{{ $s->id }}]" value="izin" {{ $currentStatus === 'izin' ? 'checked' : '' }}>
                        Izin
                      </label>
                      <label style="cursor:pointer; display:flex; align-items:center; gap:4px; font-weight:600; font-size:12px; color:#d97706;">
                        <input type="radio" name="kehadiran[{{ $s->id }}]" value="sakit" {{ $currentStatus === 'sakit' ? 'checked' : '' }}>
                        Sakit
                      </label>
                      <label style="cursor:pointer; display:flex; align-items:center; gap:4px; font-weight:600; font-size:12px; color:#dc2626;">
                        <input type="radio" name="kehadiran[{{ $s->id }}]" value="alfa" {{ $currentStatus === 'alfa' ? 'checked' : '' }}>
                        Alfa
                      </label>
                    </div>
                  </td>
                  <td>
                    <input type="text" name="keterangan[{{ $s->id }}]" class="ak-input" value="{{ $currentKet }}" style="padding:4px 8px; font-size:12px;" placeholder="Ket. khusus (opsional)">
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div style="padding:20px; border-top:1px solid var(--ak-slate-200); display:flex; justify-content:flex-end; gap:10px;">
          <a href="{{ route('akademik.jurnal.show', $jurnal->id) }}" class="ak-btn ak-btn-secondary">Batal</a>
          <button type="submit" class="ak-btn ak-btn-primary">
            <i class="bi bi-save"></i>
            <span>Simpan Perubahan Jurnal</span>
          </button>
        </div>
      @endif
    </div>
  </div>
</form>

<script>
  function setAllStatus(status) {
    document.querySelectorAll('.radio-status-' + status).forEach(radio => {
      radio.checked = true;
    });
  }
</script>
@endsection
