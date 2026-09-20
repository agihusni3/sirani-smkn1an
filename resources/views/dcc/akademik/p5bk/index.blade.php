@extends('dcc.akademik.layout')

@section('title', 'Projek Penguatan P5BK')
@section('breadcrumb', 'Projek P5BK')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Projek Penguatan Profil Pelajar Pancasila &amp; Budaya Kerja (P5BK)</h1>
    <div class="akademik-page-desc">
      Kurikulum Merdeka SMK: Pengelolaan tema projek, tim fasilitator, dan penilaian 6 dimensi karakter pelajar Pancasila.
    </div>
  </div>

  <button type="button" class="ak-btn ak-btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahProyek">
    <i class="bi bi-plus-circle"></i>
    <span>Buat Projek P5BK Baru</span>
  </button>
</div>

{{-- Data Table --}}
<div class="akademik-card">
  <div class="akademik-card-body" style="padding:0;">
    @if($proyeks->isEmpty())
      <div style="padding:48px 20px; text-align:center; color:#64748b;">
        <i class="bi bi-award" style="font-size:40px; opacity:0.35; display:block; margin-bottom:10px;"></i>
        Belum ada projek P5BK yang dibuat. Klik tombol "Buat Projek P5BK Baru" di atas.
      </div>
    @else
      <div class="akademik-table-wrap">
        <table class="akademik-table">
          <thead>
            <tr>
              <th style="width:40px;">No</th>
              <th>Nama Projek P5BK</th>
              <th>Tema Utama</th>
              <th>Rombel / Kelas</th>
              <th>Semester</th>
              <th>Siswa Dinilai</th>
              <th style="width:140px; text-align:center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($proyeks as $idx => $p)
              <tr>
                <td>{{ $proyeks->firstItem() + $idx }}</td>
                <td>
                  <div style="font-weight:700; color:var(--ak-dark);">{{ $p->nama_proyek }}</div>
                  <div style="font-size:12px; color:#64748b;">{{ Str::limit($p->deskripsi, 60) }}</div>
                </td>
                <td>
                  <span class="ak-badge ak-badge-primary">{{ $p->tema }}</span>
                </td>
                <td>
                  <span class="ak-badge ak-badge-secondary">{{ $p->rombel?->nama_rombel }}</span>
                </td>
                <td>
                  <span class="ak-badge ak-badge-secondary">Sem {{ $p->semester }}</span>
                </td>
                <td>
                  <span class="ak-badge ak-badge-success">{{ $p->nilais_count }} Siswa</span>
                </td>
                <td style="text-align:center;">
                  <div style="display:flex; justify-content:center; gap:6px;">
                    <a href="{{ route('akademik.p5bk.penilaian', $p->id) }}" class="ak-btn ak-btn-primary ak-btn-sm" title="Form Penilaian 6 Dimensi">
                      <i class="bi bi-star"></i>
                      <span>Nilai</span>
                    </a>
                    <form action="{{ route('akademik.p5bk.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus projek P5BK ini?')" style="margin:0;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="ak-btn ak-btn-secondary ak-btn-sm text-danger" title="Hapus">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div style="padding:16px 20px;">
        {{ $proyeks->links() }}
      </div>
    @endif
  </div>
</div>

{{-- Modal Buat Projek P5BK --}}
<div class="modal fade" id="modalTambahProyek" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:14px;">
      <form action="{{ route('akademik.p5bk.store') }}" method="POST">
        @csrf
        <input type="hidden" name="tahun_ajaran_id" value="{{ $ta?->id ?? 1 }}">
        <div class="modal-header">
          <h5 class="modal-title" style="font-weight:800; font-size:16px;">Buat Projek P5BK Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Nama Projek</label>
            <input type="text" name="nama_proyek" class="ak-input" placeholder="Contoh: Pembuatan Website E-Commerce Produk Siswa" required>
          </div>
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Tema P5BK</label>
            <select name="tema" class="ak-select" required>
              @foreach($temas as $tema)
                <option value="{{ $tema }}">{{ $tema }}</option>
              @endforeach
            </select>
          </div>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
            <div>
              <label class="ak-form-label">Rombel / Kelas</label>
              <select name="rombel_id" class="ak-select" required>
                <option value="">-- Pilih Rombel --</option>
                @foreach($rombels as $r)
                  <option value="{{ $r->id }}">{{ $r->nama_rombel }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="ak-form-label">Semester</label>
              <select name="semester" class="ak-select" required>
                <option value="1">Semester 1 (Ganjil)</option>
                <option value="2">Semester 2 (Genap)</option>
              </select>
            </div>
          </div>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
            <div>
              <label class="ak-form-label">Tanggal Mulai</label>
              <input type="date" name="tanggal_mulai" class="ak-input">
            </div>
            <div>
              <label class="ak-form-label">Tanggal Selesai</label>
              <input type="date" name="tanggal_selesai" class="ak-input">
            </div>
          </div>
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Deskripsi Projek &amp; Tujuan Capaian</label>
            <textarea name="deskripsi" class="ak-textarea" rows="3" placeholder="Tujuan yang diharapkan dari pelaksanaan projek ini..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="ak-btn ak-btn-primary">Simpan Projek P5BK</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
