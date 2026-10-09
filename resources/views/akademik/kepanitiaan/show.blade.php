@extends('layouts.app')

@section('title', 'Susunan Panitia - ' . $periode->nama_event)

@section('content')
<div class="container-fluid py-3">
  {{-- Breadcrumb & Top Bar --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-2 border-bottom">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('akademik.kepanitiaan.index') }}" class="btn btn-outline-secondary btn-sm p-2 rounded-circle" title="Kembali ke Daftar Event">
        <i class="bi bi-arrow-left fs-6"></i>
      </a>
      <div>
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-primary text-white" style="font-size:11px;">{{ $periode->jenis_label }}</span>
          {!! $periode->status_badge !!}
        </div>
        <h4 class="fw-bold mb-0 text-dark mt-1">{{ $periode->nama_event }}</h4>
        <div class="text-muted small">
          SK: <span class="font-monospace text-primary fw-bold">{{ $periode->sk_nomor ?? '-' }}</span> | 
          Pelaksanaan: <span class="fw-semibold text-dark">{{ $periode->tanggal_mulai->format('d/m/Y') }} s/d {{ $periode->tanggal_selesai->format('d/m/Y') }}</span>
        </div>
      </div>
    </div>

    <div class="d-flex align-items-center gap-2">
      {{-- Switch Status Event --}}
      <form action="{{ route('akademik.kepanitiaan.toggle-status', $periode->id) }}" method="POST" class="d-inline-flex gap-2 align-items-center">
        @csrf
        @method('PUT')
        <select name="status" class="form-select form-select-sm font-monospace fw-bold" onchange="this.form.submit()" style="width:160px;">
          <option value="draft" {{ $periode->status == 'draft' ? 'selected' : '' }}>⏳ Status: DRAFT</option>
          <option value="aktif" {{ $periode->status == 'aktif' ? 'selected' : '' }}>✅ Status: AKTIF</option>
          <option value="selesai" {{ $periode->status == 'selesai' ? 'selected' : '' }}>🏁 Status: SELESAI</option>
        </select>
      </form>

      <a href="{{ route('akademik.kepanitiaan.cetak-sk', $periode->id) }}" target="_blank" class="btn btn-success btn-sm px-3 shadow-sm d-flex align-items-center gap-2">
        <i class="bi bi-printer-fill"></i>
        <span>Cetak SK Kepanitiaan A4</span>
      </a>

      <a href="{{ route('panitia-asesmen.dashboard', ['periode_id' => $periode->id]) }}" class="btn btn-outline-primary btn-sm px-3 shadow-sm d-flex align-items-center gap-2">
        <i class="bi bi-laptop-fill"></i>
        <span>Masuk Ruang CBT Panitia</span>
      </a>
    </div>
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-check-circle-fill text-success fs-5"></i>
      <div>{{ session('success') }}</div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  <div class="row g-4">
    {{-- Kolom Kiri: Form Tambah Personil Panitia --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom">
          <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-person-plus-fill text-primary"></i>
            <span>Penetapan Personalia Panitia</span>
          </h6>
        </div>
        <div class="card-body p-3">
          <form action="{{ route('akademik.kepanitiaan.add-panitia', $periode->id) }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label small fw-bold text-dark">Pilih Guru / GTK <span class="text-danger">*</span></label>
              <select name="guru_id" class="form-select form-select-sm" required>
                <option value="">-- Pilih Guru / Tenaga Pendidik --</option>
                @foreach($gurus as $g)
                  <option value="{{ $g->id }}">{{ $g->nama }} ({{ $g->jabatan ?: 'Guru' }})</option>
                @endforeach
              </select>
              <div class="form-text text-muted" style="font-size:11px;">Hanya menampilkan GTK aktif yang belum masuk kepanitiaan ini.</div>
            </div>

            <div class="mb-3">
              <label class="form-label small fw-bold text-dark">Jabatan / Peran Kepanitiaan <span class="text-danger">*</span></label>
              <select name="peran" class="form-select form-select-sm" required>
                <option value="ketua">Ketua Pelaksana</option>
                <option value="sekretaris">Sekretaris</option>
                <option value="bendahara">Bendahara</option>
                <option value="proktor_utama">Proktor Utama (CBT Lab)</option>
                <option value="teknisi">Teknisi Jaringan & Lab Komputer</option>
                <option value="koordinator_soal">Koordinator Naskah & Kisi-Kisi</option>
                <option value="pengawas">Pengawas Ruang Ujian</option>
                <option value="pengarah">Pengarah Teknis</option>
                <option value="penanggung_jawab">Penanggung Jawab</option>
                <option value="anggota">Anggota Pelaksana</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label small fw-bold text-dark">Tugas Spesifik / Lokasi Penempatan</label>
              <input type="text" name="tugas_khusus" class="form-control form-control-sm" placeholder="Contoh: Proktor Lab Komputer 1 / Distribusi Soal">
            </div>

            <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
              <i class="bi bi-person-check-fill"></i>
              <span>Tetapkan ke Kepanitiaan</span>
            </button>
          </form>
        </div>
      </div>

      {{-- Card Petunjuk Alur Waka Kurikulum --}}
      <div class="card border-0 bg-light-subtle shadow-sm rounded-3 p-3">
        <h6 class="fw-bold text-primary small mb-2 d-flex align-items-center gap-1">
          <i class="bi bi-info-circle-fill"></i> Alur Kerja Waka Kurikulum
        </h6>
        <ul class="small text-muted mb-0 ps-3" style="font-size:12px; line-height:1.6;">
          <li>Tetapkan struktur kepanitiaan asesmen melalui form di atas.</li>
          <li>Setiap guru yang ditunjuk otomatis mendapatkan role <strong>"Panitia Asesmen"</strong> pada akunnya.</li>
          <li>Panitia yang bertugas akan mengelola operasional CBT, rilis token, pembagian ruang, serta rekap nilai murni.</li>
          <li>Waka Kurikulum dapat mencetak Dokumen SK Resmi melalui tombol di kanan atas.</li>
        </ul>
      </div>
    </div>

    {{-- Kolom Kanan: Tabel Personalia Kepanitiaan Terdaftar --}}
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
          <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-people-fill text-success"></i>
            <span>Struktur Personalia Terdaftar ({{ $panitias->count() }} Orang)</span>
          </h6>
          <span class="badge bg-light text-dark border">Standar SK Kepala Sekolah</span>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" style="font-size:13px;">
            <thead class="table-light text-muted fw-bold">
              <tr>
                <th class="ps-3 py-3" style="width:45px;">No</th>
                <th>Nama Personil &amp; NIP</th>
                <th>Jabatan Kepanitiaan</th>
                <th>Tugas Khusus / Penempatan</th>
                <th class="text-end pe-3" style="width:70px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($panitias as $idx => $p)
              <tr>
                <td class="ps-3 fw-bold text-muted">{{ $idx + 1 }}</td>
                <td>
                  <div class="fw-bold text-dark">{{ $p->guru->nama ?? 'Guru' }}</div>
                  <div class="text-muted small font-monospace">NIP: {{ $p->guru->nip ?? '-' }}</div>
                </td>
                <td>
                  <span class="badge rounded-pill px-2.5 py-1 fw-bold
                    @if(in_array($p->peran, ['penanggung_jawab', 'pengarah'])) bg-secondary
                    @elseif($p->peran == 'ketua') bg-primary
                    @elseif($p->peran == 'sekretaris' || $p->peran == 'bendahara') bg-info text-dark
                    @elseif(in_array($p->peran, ['proktor_utama', 'teknisi'])) bg-success
                    @else bg-light text-dark border @endif">
                    {{ $p->peran_label }}
                  </span>
                </td>
                <td>
                  <div class="text-dark small">{{ $p->tugas_khusus ?: '-' }}</div>
                </td>
                <td class="text-end pe-3">
                  <form action="{{ route('akademik.kepanitiaan.remove-panitia', [$periode->id, $p->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus petugas ini dari kepanitiaan?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm p-1 rounded" title="Hapus dari Panitia">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">
                  Belum ada personil yang ditetapkan. Silakan pilih guru pada formulir di sebelah kiri.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
