@extends('dcc.akademik.layout')

@section('title', 'Mata Pelajaran')
@section('breadcrumb', 'Mata Pelajaran')

@section('content')

<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Mata Pelajaran</h1>
    <div class="akademik-page-desc">
      Master data mata pelajaran, kelompok kurikulum, tingkat kelas, beban JP, dan alokasi ruang KBM SMKN 1 Air Naningan.
    </div>
  </div>

  <button type="button" class="ak-btn ak-btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahMapel">
    <i class="bi bi-plus-circle"></i>
    <span>Tambah Mata Pelajaran</span>
  </button>
</div>

{{-- Panduan Struktur Kurikulum --}}
<div class="akademik-card" style="margin-bottom:16px; background:linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border:1px solid #a7f3d0; border-radius:12px; padding:14px 18px;">
  <div style="display:flex; align-items:flex-start; gap:12px;">
    <div style="width:32px; height:32px; border-radius:8px; background:#10b981; color:#fff; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
      <i class="bi bi-lightbulb-fill"></i>
    </div>
    <div style="font-size:12px; color:#065f46; line-height:1.5;">
      <b>Panduan Struktur Kurikulum &amp; Penugasan Guru:</b>
      <ul style="margin:4px 0 0 0; padding-left:18px;">
        <li><b>Beda Jenjang Beda Beban JP:</b> Jika suatu mapel memiliki beban JP yang berbeda antar jenjang (contoh: <i>Bahasa Indonesia Kelas X = 4 JP, Kelas XI = 3 JP, Kelas XII = 2 JP</i>), daftarkan sebagai <b>baris terpisah per tingkat kelas</b> agar alokasi jadwal KBM akurat.</li>
        <li><b>Beda Guru Tiap Kelas / Rombel:</b> Jika dalam 1 angkatan diajar oleh guru yang berbeda, pembagian guru dilakukan per rombel pada <b><a href="{{ route('akademik.jadwal.index', ['tab' => 'distribusi']) }}" style="color:#047857; font-weight:800; text-decoration:underline;">Langkah 2: SK Pembagian Tugas (Matriks)</a></b>.</li>
        <li><b>Capaian Pembelajaran (CP) &amp; Modul Ajar:</b> Rumusan CP, TP/ATP, dan Modul Ajar dikelola langsung oleh masing-masing guru pengampu pada menu <b>Perangkat Pembelajaran</b>.</li>
      </ul>
    </div>
  </div>
</div>

{{-- Filter Toolbar --}}
<div class="akademik-card" style="margin-bottom:16px;">
  <div class="akademik-card-body" style="padding:14px 20px;">
    <form action="{{ route('akademik.matpel.index') }}" method="GET" style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
      <div style="flex:1; min-width:200px;">
        <input type="text" name="search" class="ak-input" placeholder="Cari kode atau nama mapel..." value="{{ request('search') }}">
      </div>

      <div style="width:140px;">
        <select name="jenis" class="ak-select" onchange="this.form.submit()">
          <option value="">Semua Jenis</option>
          <option value="umum" {{ request('jenis') == 'umum' ? 'selected' : '' }}>Umum</option>
          <option value="kejuruan" {{ request('jenis') == 'kejuruan' ? 'selected' : '' }}>Kejuruan</option>
          <option value="pilihan" {{ request('jenis') == 'pilihan' ? 'selected' : '' }}>Mapel Pilihan</option>
          <option value="p5bk" {{ request('jenis') == 'p5bk' ? 'selected' : '' }}>P5BK</option>
          <option value="pkl" {{ request('jenis') == 'pkl' ? 'selected' : '' }}>PKL</option>
        </select>
      </div>

      <div style="width:140px;">
        <select name="tingkat" class="ak-select" onchange="this.form.submit()">
          <option value="">Semua Tingkat</option>
          <option value="X" {{ request('tingkat') == 'X' ? 'selected' : '' }}>Kelas X</option>
          <option value="XI" {{ request('tingkat') == 'XI' ? 'selected' : '' }}>Kelas XI</option>
          <option value="XII" {{ request('tingkat') == 'XII' ? 'selected' : '' }}>Kelas XII</option>
        </select>
      </div>

      <div style="width:160px;">
        <select name="jurusan_id" class="ak-select" onchange="this.form.submit()">
          <option value="">Semua Jurusan</option>
          @foreach($jurusans as $j)
            <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
          @endforeach
        </select>
      </div>

      <button type="submit" class="ak-btn ak-btn-secondary">
        <i class="bi bi-search"></i>
      </button>
      @if(request()->anyFilled(['search', 'jenis', 'tingkat', 'jurusan_id']))
        <a href="{{ route('akademik.matpel.index') }}" class="ak-btn ak-btn-secondary" title="Reset Filter">
          <i class="bi bi-x-circle"></i>
        </a>
      @endif
    </form>
  </div>
</div>

{{-- Data Table --}}
<div class="akademik-card">
  <div class="akademik-card-body" style="padding:0;">
    @if($mapels->isEmpty())
      <div style="padding:48px 20px; text-align:center; color:var(--ak-slate-600);">
        <i class="bi bi-journal-x" style="font-size:40px; opacity:0.35; display:block; margin-bottom:10px;"></i>
        Belum ada data mata pelajaran sesuai kriteria pencarian.
      </div>
    @else
      <div class="akademik-table-wrap">
        <table class="akademik-table">
          <thead>
            <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0; font-size:11.5px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">
              <th style="width:44px; text-align:center; padding:12px 14px;">No</th>
              <th style="width:65px; text-align:center; padding:12px 14px;">Kode</th>
              <th style="padding:12px 14px;">Nama Mata Pelajaran</th>
              <th style="padding:12px 14px;">Jenis</th>
              <th style="padding:12px 14px;">Ruangan / Lab</th>
              <th style="padding:12px 14px;">Tingkat Kelas</th>
              <th style="padding:12px 14px;">Guru Pengampu</th>
              <th style="padding:12px 14px;">Beban JP</th>
              <th style="padding:12px 14px;">Jurusan</th>
              <th style="width:90px; text-align:center; padding:12px 14px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($mapels as $idx => $m)
              <tr style="border-bottom:1px solid #f1f5f9;">
                <td style="text-align:center; color:#64748b; font-weight:600; font-size:12.5px;">{{ $mapels->firstItem() + $idx }}</td>
                <td style="text-align:center;">
                  <span style="font-weight:800; font-size:13px; color:#6366f1; font-family:var(--font-mono, monospace);">{{ $m->kode_mapel }}</span>
                </td>
                <td style="font-weight:700; font-size:13px; color:#0f172a;">{{ $m->nama_mapel }}</td>
                <td>
                  @php
                    $jenisLabel = match($m->jenis) {
                      'kejuruan' => 'Kejuruan',
                      'pilihan'  => 'Pilihan',
                      'p5bk'     => 'P5BK',
                      'pkl'      => 'PKL',
                      default    => 'Umum',
                    };
                    $jenisColor = match($m->jenis) {
                      'kejuruan' => '#6d28d9',
                      'pilihan'  => '#0284c7',
                      'p5bk'     => '#b45309',
                      'pkl'      => '#15803d',
                      default    => '#475569',
                    };
                  @endphp
                  <span style="font-weight:600; font-size:12.5px; color:{{ $jenisColor }};">{{ $jenisLabel }}</span>
                </td>
                <td>
                  @if($m->resource_key)
                    <span style="font-weight:600; font-size:12.5px; color:#2563eb; display:inline-flex; align-items:center; gap:5px;">
                      <i class="bi bi-display" style="font-size:13px;"></i> {{ $m->resource_label }}
                    </span>
                  @else
                    <span style="color:#64748b; font-size:12.5px;">Kelas Biasa</span>
                  @endif
                </td>
                <td>
                  <span style="font-weight:600; font-size:12.5px; color:#334155;">
                    @if(!empty($m->tingkat_array))
                      Kelas {{ implode(', ', $m->tingkat_array) }}
                    @else
                      -
                    @endif
                  </span>
                </td>
                <td>
                  @php
                    $dists = $m->distribusiMengajars;
                    if ($selectedTaId = request('tahun_ajaran_id', $ta?->id)) {
                        $dists = $dists->where('tahun_ajaran_id', $selectedTaId);
                    }
                    $groupedGurus = $dists->groupBy('guru_id');
                  @endphp

                  @if($groupedGurus->isEmpty())
                    <div style="display:flex; flex-direction:column; gap:2px;">
                      <span style="font-size:11.5px; color:#94a3b8; font-style:italic;">Belum di-plot</span>
                      <a href="{{ route('akademik.jadwal.index', ['tab' => 'distribusi']) }}" style="font-size:11px; color:#4f46e5; text-decoration:none; font-weight:600; display:inline-flex; align-items:center; gap:2px;">
                        + Plot Guru di Langkah 2 <i class="bi bi-arrow-right-short"></i>
                      </a>
                    </div>
                  @else
                    <div style="display:flex; flex-direction:column; gap:4px;">
                      @foreach($groupedGurus as $guruId => $distList)
                        @php
                          $guru = $distList->first()->guru;
                          $uniqueRombels = $distList->pluck('rombel.nama_rombel')->filter()->unique()->values();
                        @endphp
                        <div style="font-size:12px; line-height:1.35;">
                          <div style="font-weight:700; color:#1e293b; display:flex; align-items:center; gap:5px;">
                            <i class="bi bi-person-check text-success" style="font-size:12.5px;"></i>
                            <span>{{ $guru?->nama ?? '-' }}</span>
                          </div>
                          @if($uniqueRombels->isNotEmpty())
                            <div style="font-size:11px; color:#64748b; margin-left:17px;">
                              {{ $uniqueRombels->join(', ') }}
                            </div>
                          @endif
                        </div>
                      @endforeach
                    </div>
                  @endif
                </td>
                <td style="font-weight:700; font-size:12.5px; color:#0f172a; white-space:nowrap;">{{ $m->jumlah_jam_per_minggu }} JP</td>
                <td style="color:#475569; font-size:12.5px;">{{ $m->jurusan?->nama_jurusan ?? 'Semua Jurusan' }}</td>
                <td style="text-align:center;">
                  <div style="display:flex; justify-content:center; gap:6px;">
                    <button type="button" class="btn btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#modalEditMapel{{ $m->id }}" title="Ubah" style="width:30px; height:30px; padding:0; border-radius:6px; border:1px solid #e2e8f0; background:#f8fafc; color:#475569; display:inline-flex; align-items:center; justify-content:center; transition:all 0.15s;">
                      <i class="bi bi-pencil" style="font-size:12px;"></i>
                    </button>
                    <form action="{{ route('akademik.matpel.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Hapus mata pelajaran ini?')" style="margin:0;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-icon" title="Hapus" style="width:30px; height:30px; padding:0; border-radius:6px; border:1px solid #fecdd3; background:#fff1f2; color:#e11d48; display:inline-flex; align-items:center; justify-content:center; transition:all 0.15s;">
                        <i class="bi bi-trash" style="font-size:12px;"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>

              {{-- Modal Edit Mapel --}}
              <div class="modal fade" id="modalEditMapel{{ $m->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content" style="border-radius:14px;">
                    <form action="{{ route('akademik.matpel.update', $m->id) }}" method="POST">
                      @csrf
                      @method('PUT')
                      <input type="hidden" name="is_active" value="1">
                      <div class="modal-header">
                        <h5 class="modal-title" style="font-weight:800; font-size:16px;">Ubah Mata Pelajaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <div style="margin-bottom:12px;">
                          <label class="ak-form-label">Kode Mapel</label>
                          <input type="text" name="kode_mapel" class="ak-input" value="{{ $m->kode_mapel }}" required>
                        </div>
                        <div style="margin-bottom:12px;">
                          <label class="ak-form-label">Nama Mata Pelajaran</label>
                          <input type="text" name="nama_mapel" class="ak-input" value="{{ $m->nama_mapel }}" required>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                          <div>
                            <label class="ak-form-label">Jenis</label>
                            <select name="jenis" class="ak-select" required>
                              <option value="umum" {{ $m->jenis == 'umum' ? 'selected' : '' }}>Umum</option>
                              <option value="kejuruan" {{ $m->jenis == 'kejuruan' ? 'selected' : '' }}>Kejuruan</option>
                              <option value="pilihan" {{ $m->jenis == 'pilihan' ? 'selected' : '' }}>Mapel Pilihan</option>
                              <option value="p5bk" {{ $m->jenis == 'p5bk' ? 'selected' : '' }}>P5BK</option>
                              <option value="pkl" {{ $m->jenis == 'pkl' ? 'selected' : '' }}>PKL</option>
                            </select>
                          </div>
                          <div>
                            <label class="ak-form-label" style="margin-bottom:6px;">Tingkat Kelas (X, XI, XII)</label>
                            <div style="display:flex; gap:12px; align-items:center; height:38px;">
                              <label style="display:flex; align-items:center; gap:5px; font-size:12px; font-weight:600; cursor:pointer; margin:0;">
                                <input type="checkbox" name="tingkats[]" value="X" {{ in_array('X', $m->tingkat_array) ? 'checked' : '' }} style="cursor:pointer; width:15px; height:15px;">
                                <span>Kelas X</span>
                              </label>
                              <label style="display:flex; align-items:center; gap:5px; font-size:12px; font-weight:600; cursor:pointer; margin:0;">
                                <input type="checkbox" name="tingkats[]" value="XI" {{ in_array('XI', $m->tingkat_array) ? 'checked' : '' }} style="cursor:pointer; width:15px; height:15px;">
                                <span>Kelas XI</span>
                              </label>
                              <label style="display:flex; align-items:center; gap:5px; font-size:12px; font-weight:600; cursor:pointer; margin:0;">
                                <input type="checkbox" name="tingkats[]" value="XII" {{ in_array('XII', $m->tingkat_array) ? 'checked' : '' }} style="cursor:pointer; width:15px; height:15px;">
                                <span>Kelas XII</span>
                              </label>
                            </div>
                          </div>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                          <div>
                            <label class="ak-form-label">Beban Jam / Minggu (JP)</label>
                            <input type="number" name="jumlah_jam_per_minggu" class="ak-input" value="{{ $m->jumlah_jam_per_minggu }}" min="1" max="20" required>
                          </div>
                          <div>
                            <label class="ak-form-label">Konsentrasi Jurusan</label>
                            <select name="jurusan_id" class="ak-select">
                              <option value="">Semua Jurusan</option>
                              @foreach($jurusans as $j)
                                <option value="{{ $j->id }}" {{ $m->jurusan_id == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <div style="margin-bottom:12px;">
                          <label class="ak-form-label">Kebutuhan Ruangan / Lab Khusus (Proteksi Bentrok)</label>
                          <select name="resource_key" class="ak-select">
                            <option value="">Kelas Reguler (Tidak Butuh Lab Khusus)</option>
                            @foreach(\App\Models\AkademikMataPelajaran::RESOURCES as $k => $lbl)
                              <option value="{{ $k }}" {{ $m->resource_key == $k ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                          </select>
                          <div style="font-size:11px; color:#64748b; margin-top:4px;">
                            💡 Jika disetel ke salah satu lab/bengkel, sistem otomatis menolak &amp; mencegah bentrok pemakaian di jam yang sama.
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="ak-btn ak-btn-primary">Simpan Perubahan</button>
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
        {{ $mapels->links() }}
      </div>
    @endif
  </div>
</div>

{{-- Modal Tambah Mapel --}}
<div class="modal fade" id="modalTambahMapel" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border-radius:14px;">
      <form action="{{ route('akademik.matpel.store') }}" method="POST">
        @csrf
        <input type="hidden" name="tahun_ajaran_id" value="{{ $ta?->id ?? 1 }}">
        <div class="modal-header">
          <h5 class="modal-title" style="font-weight:800; font-size:16px;">Tambah Mata Pelajaran Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Kode Mata Pelajaran</label>
            <input type="text" name="kode_mapel" class="ak-input" placeholder="Contoh: RPL-01, MAT-X" required>
          </div>
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Nama Mata Pelajaran</label>
            <input type="text" name="nama_mapel" class="ak-input" placeholder="Contoh: Pemrograman Web &amp; Bergerak" required>
          </div>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
            <div>
              <label class="ak-form-label">Jenis Mapel</label>
              <select name="jenis" class="ak-select" required>
                <option value="umum">Umum</option>
                <option value="kejuruan" selected>Kejuruan</option>
                <option value="pilihan">Mapel Pilihan</option>
                <option value="p5bk">P5BK</option>
                <option value="pkl">PKL</option>
              </select>
            </div>
            <div>
              <label class="ak-form-label" style="margin-bottom:6px;">Tingkat Kelas (Bisa X, XI, XII)</label>
              <div style="display:flex; gap:12px; align-items:center; height:38px;">
                <label style="display:flex; align-items:center; gap:5px; font-size:12px; font-weight:600; cursor:pointer; margin:0;">
                  <input type="checkbox" name="tingkats[]" value="X" checked style="cursor:pointer; width:15px; height:15px;">
                  <span>Kelas X</span>
                </label>
                <label style="display:flex; align-items:center; gap:5px; font-size:12px; font-weight:600; cursor:pointer; margin:0;">
                  <input type="checkbox" name="tingkats[]" value="XI" checked style="cursor:pointer; width:15px; height:15px;">
                  <span>Kelas XI</span>
                </label>
                <label style="display:flex; align-items:center; gap:5px; font-size:12px; font-weight:600; cursor:pointer; margin:0;">
                  <input type="checkbox" name="tingkats[]" value="XII" checked style="cursor:pointer; width:15px; height:15px;">
                  <span>Kelas XII</span>
                </label>
              </div>
            </div>
          </div>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
            <div>
              <label class="ak-form-label">Beban Jam / Minggu (JP)</label>
              <input type="number" name="jumlah_jam_per_minggu" class="ak-input" value="4" min="1" max="20" required>
            </div>
            <div>
              <label class="ak-form-label">Jurusan Khusus</label>
              <select name="jurusan_id" class="ak-select">
                <option value="">Semua Jurusan</option>
                @foreach($jurusans as $j)
                  <option value="{{ $j->id }}">{{ $j->nama_jurusan }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Kebutuhan Ruangan / Lab Khusus (Proteksi Bentrok)</label>
            <select name="resource_key" class="ak-select">
              <option value="">Kelas Reguler (Tidak Butuh Lab Khusus)</option>
              @foreach(\App\Models\AkademikMataPelajaran::RESOURCES as $k => $lbl)
                <option value="{{ $k }}">{{ $lbl }}</option>
              @endforeach
            </select>
            <div style="font-size:11px; color:#64748b; margin-top:4px;">
              💡 Jika disetel ke salah satu lab/bengkel, sistem otomatis menolak &amp; mencegah bentrok pemakaian di jam yang sama.
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="ak-btn ak-btn-primary">Simpan Mata Pelajaran</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
