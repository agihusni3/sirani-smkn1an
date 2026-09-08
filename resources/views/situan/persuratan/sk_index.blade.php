@extends('layouts.app')

@section('title', 'Buku Register SK Kepala Sekolah — SITUAN SMKN 1 AN')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">

  {{-- Page Header --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
      <div class="d-flex align-items-center gap-2 mb-1">
        <span class="badge bg-indigo-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill" style="font-size:11px; font-weight:700;">
          <i class="bi bi-file-earmark-lock-fill me-1"></i> Keputusan Kepala Sekolah
        </span>
        <span class="text-muted" style="font-size:12px;">Tahun {{ $thisYear }}</span>
      </div>
      <h1 class="h3 mb-0 fw-bold" style="color:var(--text); letter-spacing:-0.02em;">Buku Register SK Kepala Sekolah</h1>
      <p class="text-muted mb-0 small">Buku pendaftaran keputusan resmi kepala sekolah (pembagian tugas mengajar, kepanitiaan ujian, bendahara BOS, ekstra, dll).</p>
    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('situan.index') }}" class="btn btn-outline-secondary btn-sm" style="font-weight:600;">
        <i class="bi bi-arrow-left me-1"></i> Dasbor SITUAN
      </a>
      <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambahSk" style="font-weight:700;">
        <i class="bi bi-plus-circle-fill me-1"></i> Registrasi SK Baru
      </button>
    </div>
  </div>

  {{-- Top Stat Banner --}}
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:var(--surface); border:1px solid var(--border)!important;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted small fw-bold text-uppercase">Total SK Terdaftar ({{ $thisYear }})</div>
            <div class="h3 mb-0 fw-bold text-primary">{{ $totalSk }}</div>
          </div>
          <div class="rounded-3 p-3 bg-primary-subtle text-primary">
            <i class="bi bi-file-earmark-check fs-4"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:var(--surface); border:1px solid var(--border)!important;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted small fw-bold text-uppercase">Nomor Urut Berikutnya</div>
            <div class="h3 mb-0 fw-bold font-monospace text-success">#{{ str_pad((string)$nextUrut, 3, '0', STR_PAD_LEFT) }}</div>
          </div>
          <div class="rounded-3 p-3 bg-success-subtle text-success">
            <i class="bi bi-hash fs-4"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-3 p-3" style="background:var(--surface); border:1px solid var(--border)!important;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted small fw-bold text-uppercase">Format Baku SK</div>
            <div class="small fw-bold font-monospace text-dark mt-1">421.3/SK.XXX/SMKN1AN/[Bln]/{{ $thisYear }}</div>
          </div>
          <div class="rounded-3 p-3 bg-warning-subtle text-warning">
            <i class="bi bi-shield-check fs-4"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Filter & Search --}}
  <div class="card border-0 shadow-sm rounded-3 mb-4" style="background:var(--surface); border:1px solid var(--border)!important;">
    <div class="card-body p-3">
      <form method="GET" action="{{ route('situan.buku-sk.index') }}" class="row g-2">
        <div class="col-md-10">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-start-0" placeholder="Cari nomor SK, perihal tentang SK, atau kategori penetapan..." />
          </div>
        </div>
        <div class="col-md-2 d-flex gap-1">
          <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">Cari SK</button>
          @if(request()->filled('q'))
            <a href="{{ route('situan.buku-sk.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>
          @endif
        </div>
      </form>
    </div>
  </div>

  {{-- SK List Table --}}
  <div class="card border-0 shadow-sm rounded-3" style="background:var(--surface); border:1px solid var(--border)!important;">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="font-size:13px;">
        <thead class="table-light" style="border-bottom:1.5px solid var(--border);">
          <tr>
            <th class="py-3 px-3 text-center" style="width:75px;">No. Urut</th>
            <th class="py-3 px-3" style="width:230px;">Nomor SK Resmi</th>
            <th class="py-3 px-3" style="width:130px;">Tgl Penetapan</th>
            <th class="py-3 px-3" style="width:180px;">Kategori</th>
            <th class="py-3 px-3">Tentang / Ketetapan SK</th>
            <th class="py-3 px-3 text-center" style="width:120px;">Arsip Digital</th>
          </tr>
        </thead>
        <tbody>
          @forelse($bukuSks as $sk)
            <tr>
              <td class="text-center px-3">
                <span class="badge bg-secondary-subtle text-secondary px-2 py-1 fw-bold font-monospace" style="font-size:12px;">
                  #{{ str_pad((string)$sk->nomor_urut_sk, 3, '0', STR_PAD_LEFT) }}
                </span>
              </td>
              <td class="px-3">
                <span class="fw-bold font-monospace text-primary" style="font-size:12.5px;">{{ $sk->nomor_sk_lengkap }}</span>
              </td>
              <td class="px-3 text-muted">
                <i class="bi bi-calendar3 me-1 text-secondary"></i>{{ \Carbon\Carbon::parse($sk->tanggal_ditetapkan)->translatedFormat('d M Y') }}
              </td>
              <td class="px-3">
                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 rounded-pill" style="font-size:11.5px;">
                  {{ $sk->kategori_sk }}
                </span>
              </td>
              <td class="px-3">
                <div class="fw-semibold text-dark">{{ $sk->tentang_sk }}</div>
                @if($sk->distribusi_ptks_count > 0)
                  <div class="mt-1">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill" style="font-size:11px;" title="SK ini otomatis terarsip di lemari berkas guru penerima">
                      <i class="bi bi-link-45deg me-1"></i> Terdistribusi ke {{ $sk->distribusi_ptks_count }} Guru
                    </span>
                  </div>
                @endif
              </td>
              <td class="text-center px-3">
                @if($sk->file_dokumen)
                  <a href="{{ asset('storage/' . $sk->file_dokumen) }}" target="_blank" class="btn btn-sm btn-outline-primary" style="font-size:11.5px; padding:3px 8px;">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Buka PDF
                  </a>
                @else
                  <span class="text-muted" style="font-size:11px;">Belum Unggah</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                <p class="mb-0 fw-semibold">Belum ada SK Kepala Sekolah yang didaftarkan.</p>
                <small>Gunakan tombol "Registrasi SK Baru" untuk mencatat nomor SK resmi.</small>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($bukuSks->hasPages())
      <div class="p-3 border-top" style="border-color:var(--border)!important;">
        {{ $bukuSks->links() }}
      </div>
    @endif
  </div>

</div>

{{-- Modal Registrasi SK Baru --}}
<div class="modal fade" id="modalTambahSk" tabindex="-1" aria-labelledby="modalTambahSkLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow">
      <form action="{{ route('situan.buku-sk.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header border-bottom py-3 px-4">
          <h5 class="modal-title fw-bold" id="modalTambahSkLabel">
            <i class="bi bi-plus-circle-fill text-primary me-2"></i>Registrasi SK Kepala Sekolah
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="alert alert-info py-2 px-3 small mb-3">
            <i class="bi bi-info-circle-fill me-1"></i> Nomor SK resmi otomatis menggunakan nomor urut <strong>#{{ str_pad((string)$nextUrut, 3, '0', STR_PAD_LEFT) }}</strong> format baku SMKN 1 Air Naningan.
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold">Tentang / Perihal SK <span class="text-danger">*</span></label>
            <textarea name="tentang_sk" rows="2" class="form-control form-control-sm" placeholder="Contoh: Pembagian Tugas Guru dalam Proses Belajar Mengajar dan Bimbingan Konseling Semester Ganjil TA 2026/2027" required></textarea>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold">Tanggal Ditetapkan <span class="text-danger">*</span></label>
              <input type="date" name="tanggal_ditetapkan" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required />
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold">Kategori SK <span class="text-danger">*</span></label>
              <select name="kategori_sk" class="form-select form-select-sm" required>
                <option value="Pembagian Tugas PBM">Pembagian Tugas PBM &amp; Guru</option>
                <option value="Kepanitiaan Ujian / Asesmen">Kepanitiaan Ujian / Asesmen</option>
                <option value="Pengelolaan BOS / Keuangan">Pengelolaan BOS / Keuangan</option>
                <option value="Kepanitiaan PPDB">Kepanitiaan PPDB</option>
                <option value="Ekstrakurikuler & Pembina">Ekstrakurikuler &amp; Pembina</option>
                <option value="Kelulusan Siswa">Kelulusan Siswa</option>
                <option value="Tata Tertib & Disiplin">Tata Tertib &amp; Disiplin</option>
                <option value="Lainnya">Ketetapan Lainnya</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold">Unggah Dokumen PDF SK Asli Bertandatangan</label>
            <input type="file" name="file_dokumen" accept=".pdf" class="form-control form-control-sm" />
            <div class="form-text" style="font-size:11px;">Maksimal 15 MB format PDF bertandatangan/stempel basah.</div>
          </div>

          {{-- Opsi Distribusi Otomatis ke E-Kabinet Guru (Smart One-to-Many) --}}
          <div class="card bg-light border-0 rounded-3 p-3 mb-2">
            <label class="form-label small fw-bold text-dark d-flex align-items-center gap-1 mb-1">
              <i class="bi bi-share-fill text-primary"></i> Distribusi Otomatis ke E-Kabinet Guru (One-to-Many)
            </label>
            <div class="text-muted mb-2" style="font-size:11.5px;">
              SK ini akan otomatis ditautkan ke lemari berkas digital guru yang bersangkutan tanpa perlu mengunggah ulang satu per satu.
            </div>

            <div class="form-check mb-1">
              <input class="form-check-input" type="radio" name="distribusi_target" id="distribusiSemua" value="semua_guru" checked onchange="togglePilihGuru(false)">
              <label class="form-check-label small fw-bold text-dark" for="distribusiSemua">
                Distribusikan ke Seluruh Guru Aktif ({{ $gurus->count() }} Guru)
                <span class="text-muted fw-normal d-block" style="font-size:11px;">Sangat cocok untuk SK Pembagian Tugas Mengajar (PBM), Panitia Ujian, &amp; SK Sekolah umum.</span>
              </label>
            </div>

            <div class="form-check mb-1">
              <input class="form-check-input" type="radio" name="distribusi_target" id="distribusiPilih" value="pilih_guru" onchange="togglePilihGuru(true)">
              <label class="form-check-label small fw-bold text-dark" for="distribusiPilih">
                Pilih Guru Penerima Tertentu
                <span class="text-muted fw-normal d-block" style="font-size:11px;">Khusus SK Wali Kelas, Kaprog, Pembina Ekstra, atau panitia terpilih.</span>
              </label>
            </div>

            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="distribusi_target" id="distribusiTidak" value="tidak_distribusi" onchange="togglePilihGuru(false)">
              <label class="form-check-label small text-muted" for="distribusiTidak">
                Hanya Arsip Sekolah (Tidak didistribusikan ke lemari guru)
              </label>
            </div>

            {{-- Box Checkbox Daftar Guru --}}
            <div id="boxPilihGuru" style="display:none; max-height:160px; overflow-y:auto; background:#fff; border:1px solid #cbd5e1; border-radius:6px; padding:8px 12px;">
              <div class="small fw-bold text-muted mb-2 pb-1 border-bottom d-flex justify-content-between">
                <span>Centang Guru yang Diberikan SK:</span>
                <span class="badge bg-light text-dark border">Pilih di bawah</span>
              </div>
              <div class="row g-2">
                @foreach($gurus as $g)
                  <div class="col-md-6 col-12">
                    <div class="form-check py-1">
                      <input class="form-check-input" type="checkbox" name="guru_ids[]" value="{{ $g->id }}" id="guruCheck{{ $g->id }}">
                      <label class="form-check-label small" for="guruCheck{{ $g->id }}">
                        <strong class="text-dark">{{ $g->nama }}</strong>
                        <span class="text-muted d-block" style="font-size:10.5px;">{{ $g->jabatan ?: 'Guru Mata Pelajaran' }}</span>
                      </label>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer border-top px-4 py-2">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-sm btn-primary fw-bold">
            <i class="bi bi-save me-1"></i> Simpan &amp; Terbitkan Nomor SK
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function togglePilihGuru(show) {
    const box = document.getElementById('boxPilihGuru');
    if (box) box.style.display = show ? 'block' : 'none';
  }
</script>
@endsection
