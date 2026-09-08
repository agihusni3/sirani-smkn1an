@extends('layouts.app')

@section('title', 'Lemari Berkas Digital PTK — ' . $guru->nama)

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">

  {{-- Page Header --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
      <div class="d-flex align-items-center gap-2 mb-1">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill" style="font-size:11px; font-weight:700;">
          <i class="bi bi-folder-symlink-fill me-1"></i> E-Arsip PTK
        </span>
        <span class="text-muted" style="font-size:12px;">Lemari Berkas Digital Kepegawaian</span>
      </div>
      <h1 class="h3 mb-0 fw-bold" style="color:var(--text); letter-spacing:-0.02em;">Arsip Berkas Digital: {{ $guru->nama }}</h1>
      <p class="text-muted mb-0 small">Penyimpanan cloud dokumen kedinasan (SK, Ijazah, Serdik, KTP, KK, Sertifikat) untuk kemudahan usulan KGB, Pangkat, &amp; Dupak.</p>
    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('situan.radar-kgb.index') }}" class="btn btn-outline-secondary btn-sm" style="font-weight:600;">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Radar
      </a>
      <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalUploadArsip" style="font-weight:700;">
        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Unggah Berkas Baru
      </button>
    </div>
  </div>

  {{-- PTK Info Profile Strip --}}
  <div class="card border-0 shadow-sm rounded-3 mb-4 p-3" style="background:var(--surface); border:1px solid var(--border)!important;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width:50px; height:50px; font-size:20px; font-weight:700;">
          {{ strtoupper(substr($guru->nama, 0, 2)) }}
        </div>
        <div>
          <h5 class="fw-bold text-dark mb-0">{{ $guru->nama }}</h5>
          <div class="text-muted small">
            NIP: {{ $guru->nip ?: '-' }} &bull; NUPTK: {{ $guru->nuptk ?: '-' }} &bull; {{ $guru->jenis_ptk ?: 'Pendidik' }}
          </div>
        </div>
      </div>
      <div class="d-flex gap-2 flex-wrap">
        <span class="badge bg-light text-dark border px-3 py-2">Gol: <strong>{{ $guru->golongan_ruang ?: '-' }}</strong></span>
        <span class="badge bg-light text-dark border px-3 py-2">Status: <strong>{{ $guru->status_kepegawaian ?: '-' }}</strong></span>
        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2">Total Dokumen: <strong>{{ $arsips->count() }} Berkas</strong></span>
      </div>
    </div>
  </div>

  {{-- Arsip Grid / Table --}}
  <div class="card border-0 shadow-sm rounded-3" style="background:var(--surface); border:1px solid var(--border)!important;">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:12.5px;">
        <thead class="table-light" style="border-bottom:1.5px solid var(--border);">
          <tr>
            <th class="py-3 px-3" style="width:180px;">Kategori Berkas</th>
            <th class="py-3 px-3">Nama &amp; Keterangan Dokumen</th>
            <th class="py-3 px-3" style="width:200px;">Nomor Dokumen</th>
            <th class="py-3 px-3" style="width:130px;">Tgl Dokumen</th>
            <th class="py-3 px-3" style="width:120px;">Tgl Unggah</th>
            <th class="py-3 px-3 text-center" style="width:150px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($arsips as $arsip)
            @php
              $badgeCat = match($arsip->kategori_berkas) {
                'sk_pangkat_terakhir', 'sk_kgb_terakhir' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'sk_cpns', 'sk_pns', 'sk_pppk' => 'bg-primary-subtle text-primary border border-primary-subtle',
                'sk_penugasan_sekolah' => 'bg-info-subtle text-dark border border-info-subtle',
                'ijazah', 'transkrip', 'sertifikat_pendidik' => 'bg-success-subtle text-success border border-success-subtle',
                'ktp', 'kk', 'kartu_pegawai' => 'bg-info-subtle text-info border border-info-subtle',
                default => 'bg-secondary-subtle text-secondary',
              };
              $namaKategori = match($arsip->kategori_berkas) {
                'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
                'sk_kgb_terakhir'     => 'SK KGB Terakhir',
                'sk_cpns'             => 'SK CPNS',
                'sk_pns'              => 'SK PNS Definitif',
                'sk_pppk'             => 'SK PPPK',
                'sk_penugasan_sekolah'=> 'SK Kolektif Sekolah',
                'ijazah'              => 'Ijazah Pendidikan',
                'transkrip'           => 'Transkrip Nilai',
                'sertifikat_pendidik' => 'Sertifikat Pendidik (Serdik)',
                'kartu_pegawai'       => 'Karpeg / KPE',
                'ktp'                 => 'KTP Elektronik',
                'kk'                  => 'Kartu Keluarga',
                default               => 'Dokumen Lainnya',
              };
            @endphp
            <tr>
              <td class="px-3">
                <span class="badge {{ $badgeCat }} px-2 py-1 rounded-pill" style="font-size:11px;">
                  {{ $namaKategori }}
                </span>
              </td>
              <td class="px-3">
                <div class="fw-bold text-dark">{{ $arsip->nama_dokumen }}</div>
                @if($arsip->buku_sk_id)
                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0 rounded-pill mt-1" style="font-size:10px;">
                    <i class="bi bi-link-45deg"></i> Terdistribusi Otomatis dari Buku SK
                  </span>
                @endif
              </td>
              <td class="px-3">
                <div class="font-monospace text-secondary">{{ $arsip->nomor_dokumen ?: '-' }}</div>
              </td>
              <td class="px-3 text-muted">
                {{ $arsip->tanggal_dokumen ? \Carbon\Carbon::parse($arsip->tanggal_dokumen)->translatedFormat('d M Y') : '-' }}
              </td>
              <td class="px-3 text-muted">
                {{ \Carbon\Carbon::parse($arsip->created_at)->translatedFormat('d M Y') }}
              </td>
              <td class="text-center px-3">
                <div class="btn-group btn-group-sm">
                  <a href="{{ asset('storage/' . $arsip->file_path) }}" target="_blank" class="btn btn-primary" title="Buka Dokumen">
                    <i class="bi bi-eye-fill me-1"></i> Buka
                  </a>
                  <button type="button" class="btn btn-outline-danger" title="Hapus Dokumen" onclick="if(confirm('Hapus berkas digital ini?')) { document.getElementById('formHapusArsip{{ $arsip->id }}').submit(); }">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
                <form id="formHapusArsip{{ $arsip->id }}" action="{{ route('situan.arsip-ptk.destroy', $arsip->id) }}" method="POST" class="d-none">
                  @csrf
                  @method('DELETE')
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                <p class="mb-0 fw-semibold">Belum ada berkas digital yang diunggah untuk {{ $guru->nama }}.</p>
                <small>Klik "Unggah Berkas Baru" untuk mengarsipkan SK, Ijazah, KTP, atau dokumen lainnya.</small>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

{{-- Modal Upload Berkas Digital --}}
<div class="modal fade" id="modalUploadArsip" tabindex="-1" aria-labelledby="modalUploadArsipLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <form action="{{ route('situan.arsip-ptk.store', $guru->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header border-bottom py-3 px-4">
          <h5 class="modal-title fw-bold" id="modalUploadArsipLabel">
            <i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i>Unggah Arsip Digital PTK
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-bold">Kategori Berkas <span class="text-danger">*</span></label>
            <select name="kategori_berkas" class="form-select form-select-sm" required>
              <option value="sk_pangkat_terakhir">SK Kenaikan Pangkat Terakhir</option>
              <option value="sk_kgb_terakhir">SK Kenaikan Gaji Berkala (KGB) Terakhir</option>
              <option value="sk_cpns">SK Calon Pegawai Negeri Sipil (CPNS)</option>
              <option value="sk_pns">SK Pengangkatan PNS (100%)</option>
              <option value="sk_pppk">SK Pengangkatan PPPK</option>
              <option value="sertifikat_pendidik">Sertifikat Pendidik (Serdik)</option>
              <option value="ijazah">Ijazah Terakhir (S1 / S2)</option>
              <option value="transkrip">Transkrip Nilai Akademik</option>
              <option value="kartu_pegawai">Kartu Pegawai (Karpeg / KPE)</option>
              <option value="ktp">Kartu Tanda Penduduk (KTP)</option>
              <option value="kk">Kartu Keluarga (KK)</option>
              <option value="lainnya">Dokumen / Sertifikat Pelatihan Lainnya</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold">Nama / Keterangan Dokumen <span class="text-danger">*</span></label>
            <input type="text" name="nama_dokumen" class="form-control form-control-sm" placeholder="Contoh: SK Kenaikan Pangkat Golongan III/b" required />
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold">Nomor Dokumen / SK</label>
              <input type="text" name="nomor_dokumen" class="form-control form-control-sm" placeholder="Contoh: 821.2/123/V.01/2024" />
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold">Tanggal Dokumen</label>
              <input type="date" name="tanggal_dokumen" class="form-control form-control-sm" />
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold">Pilih File Dokumen <span class="text-danger">*</span></label>
            <input type="file" name="file_dokumen" accept=".pdf,.jpg,.jpeg,.png" class="form-control form-control-sm" required />
            <div class="form-text" style="font-size:11px;">Maksimal 10 MB (Format: PDF, JPG, PNG).</div>
          </div>
        </div>
        <div class="modal-footer border-top px-4 py-2">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-sm btn-primary fw-bold">
            <i class="bi bi-upload me-1"></i> Unggah &amp; Simpan ke Arsip
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
