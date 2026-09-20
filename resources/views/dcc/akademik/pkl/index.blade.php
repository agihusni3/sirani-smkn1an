@extends('dcc.akademik.layout')

@section('title', 'Praktik Kerja Lapangan (PKL)')
@section('breadcrumb', 'PKL & Mitra DU/DI')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Praktik Kerja Lapangan (PKL) &amp; Kemitraan DU/DI</h1>
    <div class="akademik-page-desc">
      Pengelolaan kemitraan dunia usaha, penempatan siswa magang, dan asesmen nilai PKL sesuai standar vokasi SMK.
    </div>
  </div>

  <div style="display:flex; gap:10px;">
    <button type="button" class="ak-btn ak-btn-secondary" data-bs-toggle="modal" data-bs-target="#modalTambahTempat">
      <i class="bi bi-buildings"></i>
      <span>Tambah Mitra DU/DI</span>
    </button>
    <button type="button" class="ak-btn ak-btn-primary" data-bs-toggle="modal" data-bs-target="#modalPenempatanSiswa">
      <i class="bi bi-person-plus"></i>
      <span>Tempatkan Siswa PKL</span>
    </button>
  </div>
</div>

{{-- Top Cards --}}
<div class="akademik-kpi-grid">
  <div class="akademik-kpi-card">
    <div>
      <div class="akademik-kpi-val">{{ $tempats->count() }}</div>
      <div class="akademik-kpi-label">Mitra Industri (DU/DI)</div>
    </div>
    <div class="akademik-kpi-icon icon-violet">
      <i class="bi bi-buildings"></i>
    </div>
  </div>

  <div class="akademik-kpi-card">
    <div>
      <div class="akademik-kpi-val" style="color:#059669;">
        {{ $siswaPkls->where('status', 'aktif')->count() }}
      </div>
      <div class="akademik-kpi-label">Siswa Sedang PKL Aktif</div>
    </div>
    <div class="akademik-kpi-icon icon-emerald">
      <i class="bi bi-person-workspace"></i>
    </div>
  </div>

  <div class="akademik-kpi-card">
    <div>
      <div class="akademik-kpi-val" style="color:#d97706;">
        {{ $siswaPkls->where('status', 'belum_berangkat')->count() }}
      </div>
      <div class="akademik-kpi-label">Menunggu Keberangkatan</div>
    </div>
    <div class="akademik-kpi-icon icon-amber">
      <i class="bi bi-hourglass-split"></i>
    </div>
  </div>

  <div class="akademik-kpi-card">
    <div>
      <div class="akademik-kpi-val" style="color:#4f46e5;">
        {{ $siswaPkls->where('status', 'selesai')->count() }}
      </div>
      <div class="akademik-kpi-label">Selesai Magang</div>
    </div>
    <div class="akademik-kpi-icon icon-indigo">
      <i class="bi bi-check2-circle"></i>
    </div>
  </div>
</div>

{{-- Section Penempatan Siswa PKL --}}
<div class="akademik-card">
  <div class="akademik-card-header">
    <h3 class="akademik-card-title">
      <i class="bi bi-people text-primary"></i>
      <span>Daftar Siswa &amp; Penempatan PKL</span>
    </h3>
  </div>
  <div class="akademik-card-body" style="padding:0;">
    @if($siswaPkls->isEmpty())
      <div style="padding:48px 20px; text-align:center; color:#64748b;">
        <i class="bi bi-briefcase" style="font-size:40px; opacity:0.35; display:block; margin-bottom:10px;"></i>
        Belum ada penempatan siswa PKL. Klik tombol "Tempatkan Siswa PKL" untuk memulai.
      </div>
    @else
      <div class="akademik-table-wrap">
        <table class="akademik-table">
          <thead>
            <tr>
              <th style="width:40px;">No</th>
              <th>Nama Siswa &amp; Kelas</th>
              <th>Perusahaan / Mitra DU/DI</th>
              <th>Guru Pembimbing</th>
              <th>Periode PKL</th>
              <th>Status</th>
              <th>Nilai PKL</th>
              <th style="width:90px; text-align:center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($siswaPkls as $idx => $sp)
              <tr>
                <td>{{ $siswaPkls->firstItem() + $idx }}</td>
                <td>
                  <div style="font-weight:700; color:var(--ak-dark);">{{ $sp->siswa?->nama_lengkap ?? '-' }}</div>
                  <div style="font-size:11px; color:#64748b;">{{ $sp->siswa?->rombel?->nama_rombel ?? '-' }}</div>
                </td>
                <td>
                  <div style="font-weight:700; color:var(--ak-primary);">{{ $sp->pklTempat?->nama_dudi ?? '-' }}</div>
                  <div style="font-size:11px; color:#64748b;">{{ $sp->pklTempat?->kota ?? 'Lampung' }} · Pembimbing DU/DI: {{ $sp->pklTempat?->nama_pembimbing_dudi ?? '-' }}</div>
                </td>
                <td>
                  <div style="font-weight:600;">{{ $sp->guruPembimbing?->nama ?? '-' }}</div>
                </td>
                <td style="font-size:12px;">
                  {{ $sp->tanggal_mulai ? \Carbon\Carbon::parse($sp->tanggal_mulai)->isoFormat('D MMM') : '-' }} s/d 
                  {{ $sp->tanggal_selesai ? \Carbon\Carbon::parse($sp->tanggal_selesai)->isoFormat('D MMM Y') : '-' }}
                </td>
                <td>
                  @if($sp->status == 'aktif')
                    <span class="ak-badge ak-badge-success">Aktif Magang</span>
                  @elseif($sp->status == 'selesai')
                    <span class="ak-badge ak-badge-primary">Selesai</span>
                  @else
                    <span class="ak-badge ak-badge-warning">Persiapan</span>
                  @endif
                </td>
                <td>
                  @if($sp->nilai_pkl)
                    <span style="font-weight:800; color:var(--ak-primary);">{{ $sp->nilai_pkl }}</span>
                    <span class="ak-badge ak-badge-success">{{ $sp->predikat_pkl }}</span>
                  @else
                    <span style="color:#94a3b8; font-size:12px;">Belum dinilai</span>
                  @endif
                </td>
                <td style="text-align:center;">
                  <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" data-bs-toggle="modal" data-bs-target="#modalNilaiPkl{{ $sp->id }}" title="Input Nilai">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                </td>
              </tr>

              {{-- Modal Nilai PKL --}}
              <div class="modal fade" id="modalNilaiPkl{{ $sp->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content" style="border-radius:14px;">
                    <form action="{{ route('akademik.pkl.nilai', $sp->id) }}" method="POST">
                      @csrf
                      @method('PUT')
                      <div class="modal-header">
                        <h5 class="modal-title" style="font-weight:800; font-size:16px;">Penilaian PKL Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <div style="margin-bottom:12px;">
                          <div style="font-weight:700;">{{ $sp->siswa?->nama_lengkap }}</div>
                          <div style="font-size:12px; color:#64748b;">{{ $sp->pklTempat?->nama_dudi }}</div>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                          <div>
                            <label class="ak-form-label">Nilai Angka (0-100)</label>
                            <input type="number" step="0.1" name="nilai_pkl" class="ak-input" value="{{ $sp->nilai_pkl }}" min="0" max="100" required>
                          </div>
                          <div>
                            <label class="ak-form-label">Status PKL</label>
                            <select name="status" class="ak-select" required>
                              <option value="belum_berangkat" {{ $sp->status == 'belum_berangkat' ? 'selected' : '' }}>Belum Berangkat</option>
                              <option value="aktif" {{ $sp->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                              <option value="selesai" {{ $sp->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                          </div>
                        </div>
                        <div style="margin-bottom:12px;">
                          <label class="ak-form-label">Catatan Evaluasi / Rekomendasi Industri</label>
                          <textarea name="catatan" class="ak-textarea" rows="3">{{ $sp->catatan }}</textarea>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="ak-btn ak-btn-primary">Simpan Nilai PKL</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

            @endforeach
          </tbody>
        </table>
      </div>
      <div style="padding:16px 20px;">
        {{ $siswaPkls->links() }}
      </div>
    @endif
  </div>
</div>

{{-- Modal Tambah Tempat DU/DI --}}
<div class="modal fade" id="modalTambahTempat" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:14px;">
      <form action="{{ route('akademik.pkl.tempat.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" style="font-weight:800; font-size:16px;">Tambah Mitra DU/DI Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Nama Perusahaan / Instansi</label>
            <input type="text" name="nama_dudi" class="ak-input" placeholder="Contoh: PT Telkom Akses, Bengkel Auto 2000" required>
          </div>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
            <div>
              <label class="ak-form-label">Bidang Usaha</label>
              <input type="text" name="bidang_usaha" class="ak-input" placeholder="IT, Otomotif, Pangan...">
            </div>
            <div>
              <label class="ak-form-label">Kota / Wilayah</label>
              <input type="text" name="kota" class="ak-input" placeholder="Tanggamus, Pringsewu...">
            </div>
          </div>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
            <div>
              <label class="ak-form-label">Pembimbing DU/DI</label>
              <input type="text" name="nama_pembimbing_dudi" class="ak-input" placeholder="Nama kontak...">
            </div>
            <div>
              <label class="ak-form-label">No. Telepon / WhatsApp</label>
              <input type="text" name="kontak_dudi" class="ak-input" placeholder="08xxxxxxxxxx">
            </div>
          </div>
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Alamat Lengkap Perusahaan</label>
            <textarea name="alamat" class="ak-textarea" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="ak-btn ak-btn-primary">Simpan Mitra Industri</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal Penempatan Siswa --}}
<div class="modal fade" id="modalPenempatanSiswa" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:14px;">
      <form action="{{ route('akademik.pkl.siswa.store') }}" method="POST">
        @csrf
        <input type="hidden" name="tahun_ajaran_id" value="{{ $ta?->id ?? 1 }}">
        <div class="modal-header">
          <h5 class="modal-title" style="font-weight:800; font-size:16px;">Penempatan Siswa PKL</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Pilih Siswa (Kelas XI / XII)</label>
            <select name="siswa_id" class="ak-select" required>
              <option value="">-- Pilih Siswa --</option>
              @foreach($siswas as $s)
                <option value="{{ $s->id }}">{{ $s->nama_lengkap }} ({{ $s->rombel?->nama_rombel }})</option>
              @endforeach
            </select>
          </div>
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Mitra Industri (DU/DI)</label>
            <select name="pkl_tempat_id" class="ak-select" required>
              <option value="">-- Pilih Mitra DU/DI --</option>
              @foreach($tempats as $t)
                <option value="{{ $t->id }}">{{ $t->nama_dudi }} ({{ $t->kota ?? 'Lampung' }})</option>
              @endforeach
            </select>
          </div>
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Guru Pembimbing Sekolah</label>
            <select name="guru_pembimbing_id" class="ak-select" required>
              <option value="">-- Pilih Guru Pembimbing --</option>
              @foreach($gurus as $g)
                <option value="{{ $g->id }}">{{ $g->nama }}</option>
              @endforeach
            </select>
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
            <label class="ak-form-label">Status Keberangkatan</label>
            <select name="status" class="ak-select" required>
              <option value="belum_berangkat">Belum Berangkat (Persiapan)</option>
              <option value="aktif" selected>Aktif Magang</option>
              <option value="selesai">Selesai</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="ak-btn ak-btn-primary">Tempatkan Siswa</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
