@extends('dcc.akademik.layout')

@section('title', 'Mata Pelajaran & Capaian Pembelajaran')
@section('breadcrumb', 'Mata Pelajaran')

@section('content')
@include('dcc.akademik.partials.workflow_stepper', ['currentStep' => 1])

<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Mata Pelajaran &amp; Capaian Pembelajaran</h1>
    <div class="akademik-page-desc">
      Struktur kurikulum merdeka: Umum, Kejuruan, P5BK, dan PKL per Fase E (Kelas X) &amp; Fase F (Kelas XI - XII).
    </div>
  </div>

  <button type="button" class="ak-btn ak-btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahMapel">
    <i class="bi bi-plus-circle"></i>
    <span>Tambah Mata Pelajaran</span>
  </button>
</div>

{{-- Panduan Kurikulum Merdeka --}}
<div class="akademik-card" style="margin-bottom:16px; background:linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border:1px solid #a7f3d0; border-radius:12px; padding:14px 18px;">
  <div style="display:flex; align-items:flex-start; gap:12px;">
    <div style="width:32px; height:32px; border-radius:8px; background:#10b981; color:#fff; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0;">
      <i class="bi bi-lightbulb-fill"></i>
    </div>
    <div style="font-size:12px; color:#065f46; line-height:1.5;">
      <b>Panduan Struktur Kurikulum &amp; Penugasan Guru:</b>
      <ul style="margin:4px 0 0 0; padding-left:18px;">
        <li><b>Beda Jenjang Beda Beban JP/CP:</b> Jika suatu mapel memiliki beban JP atau Fase CP yang berbeda antar jenjang (contoh: <i>Bahasa Indonesia Kelas X = 4 JP, Kelas XI = 3 JP, Kelas XII = 2 JP</i>), daftarkan sebagai <b>baris terpisah per tingkat kelas</b> agar alokasi jam dan capaian rapor Fase E/F akurat.</li>
        <li><b>Beda Guru Tiap Kelas / Rombel:</b> Jika dalam 1 angkatan diajar oleh guru yang berbeda (contoh: <i>Kelas X TSM diajar Bu Eka Setiani, sedangkan Kelas X RPL diajar Bu Reni Ulfasari</i>), pembagian guru dilakukan per rombel pada <b><a href="{{ route('akademik.jadwal.index', ['tab' => 'distribusi']) }}" style="color:#047857; font-weight:800; text-decoration:underline;">Langkah 2: SK Pembagian Tugas (Matriks)</a></b>.</li>
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
            <tr>
              <th style="width:40px;">No</th>
              <th>Kode</th>
              <th>Nama Mata Pelajaran</th>
              <th>Jenis</th>
              <th>Ruangan / Lab</th>
              <th>Tingkat Kelas</th>
              <th>Guru Pengampu</th>
              <th>Beban JP</th>
              <th>Jurusan</th>
              <th>Capaian Pembelajaran (CP)</th>
              <th style="width:90px; text-align:center;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($mapels as $idx => $m)
              <tr>
                <td>{{ $mapels->firstItem() + $idx }}</td>
                <td><code style="font-weight:700; color:var(--ak-primary);">{{ $m->kode_mapel }}</code></td>
                <td style="font-weight:700; color:var(--ak-dark);">{{ $m->nama_mapel }}</td>
                <td>
                  @if($m->jenis == 'umum')
                    <span class="ak-badge ak-badge-secondary">Umum</span>
                  @elseif($m->jenis == 'kejuruan')
                    <span class="ak-badge ak-badge-primary">Kejuruan</span>
                  @elseif($m->jenis == 'pilihan')
                    <span class="ak-badge ak-badge-info" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;">Pilihan</span>
                  @elseif($m->jenis == 'p5bk')
                    <span class="ak-badge ak-badge-warning">P5BK</span>
                  @else
                    <span class="ak-badge ak-badge-success">PKL</span>
                  @endif
                </td>
                <td>
                  @if($m->resource_key)
                    <span class="ak-badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:11px; font-weight:700;">
                      {{ $m->resource_label }}
                    </span>
                  @else
                    <span style="font-size:11.5px; color:#94a3b8;">Kelas Biasa</span>
                  @endif
                </td>
                <td>
                  <div style="display:flex; flex-wrap:wrap; gap:4px;">
                    @foreach($m->tingkat_array as $t)
                      <span class="ak-badge ak-badge-secondary" style="font-size:11px; font-weight:700;">Kelas {{ $t }}</span>
                    @endforeach
                  </div>
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
                      <span style="font-size:11px; color:#94a3b8; font-style:italic;">Belum di-plot</span>
                      <a href="{{ route('akademik.jadwal.index', ['tab' => 'distribusi']) }}" style="font-size:10px; color:var(--ak-primary); text-decoration:none; font-weight:700;">
                        + Plot Guru di Langkah 2 &rarr;
                      </a>
                    </div>
                  @else
                    <div style="display:flex; flex-direction:column; gap:6px;">
                      @foreach($groupedGurus as $guruId => $distList)
                        @php
                          $guru = $distList->first()->guru;
                          $uniqueRombels = $distList->pluck('rombel.nama_rombel')->filter()->unique()->values();
                        @endphp
                        <div style="font-size:11.5px; line-height:1.25;">
                          <div style="font-weight:700; color:var(--ak-dark); display:flex; align-items:center; gap:4px;">
                            <i class="bi bi-person-check text-success" style="font-size:12px;"></i>
                            <span>{{ $guru?->nama ?? '-' }}</span>
                          </div>
                          @if($uniqueRombels->isNotEmpty())
                            <div style="display:flex; flex-wrap:wrap; gap:3px; margin-top:2px; margin-left:16px;">
                              @foreach($uniqueRombels as $rName)
                                <span class="ak-badge" style="background:#f1f5f9; color:#475569; font-size:9.5px; font-weight:700; padding:1px 5px; border:1px solid #e2e8f0;">
                                  {{ $rName }}
                                </span>
                              @endforeach
                            </div>
                          @endif
                        </div>
                      @endforeach
                    </div>
                  @endif
                </td>
                <td style="font-weight:700;">{{ $m->jumlah_jam_per_minggu }} JP</td>
                <td>{{ $m->jurusan?->nama_jurusan ?? 'Semua Jurusan' }}</td>
                <td style="max-width:280px; font-size:12px; color:#475569;">
                  @if($m->deskripsi_cp)
                    <div style="display:flex; flex-direction:column; gap:4px;">
                      <span style="font-size:10px; font-weight:800; color:#0369a1; background:#e0f2fe; padding:1px 6px; border-radius:4px; width:fit-content;">
                        {{ $m->fase_label }}
                      </span>
                      <span style="line-height:1.4; color:#334155;" title="{{ $m->deskripsi_cp }}">
                        {{ Str::limit($m->deskripsi_cp, 90) }}
                      </span>
                    </div>
                  @else
                    <span style="color:#94a3b8; font-style:italic;">Belum ada ringkasan CP</span>
                  @endif
                </td>
                <td style="text-align:center;">
                  <div style="display:flex; justify-content:center; gap:6px;">
                    <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditMapel{{ $m->id }}" title="Ubah">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <form action="{{ route('akademik.matpel.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Hapus mata pelajaran ini?')" style="margin:0;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="ak-btn ak-btn-secondary ak-btn-sm text-danger" title="Hapus">
                        <i class="bi bi-trash"></i>
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
                        <div style="margin-bottom:12px;">
                          <label class="ak-form-label">Ringkasan Capaian Pembelajaran (CP)</label>
                          <textarea name="deskripsi_cp" class="ak-textarea" rows="3">{{ $m->deskripsi_cp }}</textarea>
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

{{-- Panduan Lanjut ke Langkah 2 --}}
<div style="background:linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border:1.5px solid #bfdbfe; border-radius:12px; padding:16px 20px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
  <div>
    <div style="font-weight:800; font-size:14px; color:#1e40af; display:flex; align-items:center; gap:6px;">
      <i class="bi bi-arrow-right-circle-fill"></i> Langkah 1 Selesai: Struktur Mata Pelajaran &amp; CP Telah Siap!
    </div>
    <div style="font-size:12px; color:#1d4ed8; margin-top:2px;">
      Langkah selanjutnya adalah menugaskan guru pengampu per rombel dan memverifikasi pemenuhan beban 24–40 JP di SK Pembagian Tugas.
    </div>
  </div>
  <a href="{{ route('akademik.jadwal.index', ['tab' => 'distribusi']) }}" class="ak-btn ak-btn-primary" style="font-size:12.5px; font-weight:700;">
    <span>Lanjut ke Langkah 2: SK Pembagian Tugas</span>
    <i class="bi bi-arrow-right ms-1"></i>
  </a>
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
          <div style="margin-bottom:12px;">
            <label class="ak-form-label">Ringkasan Capaian Pembelajaran (CP)</label>
            <textarea name="deskripsi_cp" class="ak-textarea" rows="3" placeholder="Tuliskan kompetensi akhir / elemen CP dari Kemdikbudristek..."></textarea>
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
