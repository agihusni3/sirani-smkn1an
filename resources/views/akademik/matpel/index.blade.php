@extends('akademik.layout')

@section('title', 'Mata Pelajaran & Struktur Kurikulum')
@section('breadcrumb', 'Mata Pelajaran')

@section('content')

{{-- ========================================================================== --}}
{{-- 1. HEADER HALAMAN & AKSI CEPAT                                             --}}
{{-- ========================================================================== --}}
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Mata Pelajaran &amp; Struktur Kurikulum</h1>
    <div class="akademik-page-desc">
      Master struktur mata pelajaran, alokasi JP per minggu, tingkat kelas/fase, serta kebutuhan laboratorium KBM SMKN 1 Air Naningan.
    </div>
  </div>

  <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
    <a href="{{ route('akademik.jadwal.index', ['tab' => 'distribusi']) }}" class="ak-btn ak-btn-secondary" title="Lanjut ke pembagian tugas guru pengampu">
      <i class="bi bi-diagram-3"></i>
      <span>Langkah 2: SK Mengajar</span>
    </a>

    <button type="button" class="ak-btn ak-btn-primary" onclick="openTambahMapel()">
      <i class="bi bi-plus-circle"></i>
      <span>Tambah Mata Pelajaran</span>
    </button>
  </div>
</div>

{{-- ========================================================================== --}}
{{-- 2. STATISTIK RINGKAS KURIKULUM (KPI CARDS)                                 --}}
{{-- ========================================================================== --}}
<div class="akademik-kpi-grid" style="margin-bottom:20px;">
  <div class="akademik-kpi-card">
    <div>
      <div class="akademik-kpi-val" style="color:var(--ak-indigo, #4f46e5);">
        {{ $stats['total_mapel'] }}
        <span style="font-size:13px; font-weight:700; color:#64748b;">Mapel</span>
      </div>
      <div class="akademik-kpi-label">Total Mata Pelajaran (TA {{ $selectedTa?->tahun ?? '-' }})</div>
    </div>
    <div class="akademik-kpi-icon icon-indigo">
      <i class="bi bi-book-half"></i>
    </div>
  </div>

  <div class="akademik-kpi-card">
    <div>
      <div class="akademik-kpi-val" style="color:#059669;">
        {{ $stats['total_jp'] }}
        <span style="font-size:13px; font-weight:700; color:#64748b;">JP / Pekan</span>
      </div>
      <div class="akademik-kpi-label">Total Beban Tatap Muka</div>
    </div>
    <div class="akademik-kpi-icon" style="background:#ecfdf5; color:#059669;">
      <i class="bi bi-clock-history"></i>
    </div>
  </div>

  <div class="akademik-kpi-card">
    <div>
      <div class="akademik-kpi-val" style="color:#7c3aed;">
        {{ $stats['total_kejuruan'] }}
        <span style="font-size:13px; font-weight:700; color:#64748b;">Mapel</span>
      </div>
      <div class="akademik-kpi-label">Kejuruan &amp; Mapel Pilihan</div>
    </div>
    <div class="akademik-kpi-icon" style="background:#f5f3ff; color:#7c3aed;">
      <i class="bi bi-tools"></i>
    </div>
  </div>

  <div class="akademik-kpi-card">
    <div>
      <div class="akademik-kpi-val" style="color:#0284c7;">
        {{ $stats['total_lab'] }}
        <span style="font-size:13px; font-weight:700; color:#64748b;">Mapel</span>
      </div>
      <div class="akademik-kpi-label">Kebutuhan Lab / Bengkel Khusus</div>
    </div>
    <div class="akademik-kpi-icon" style="background:#f0f9ff; color:#0284c7;">
      <i class="bi bi-display"></i>
    </div>
  </div>
</div>

{{-- ========================================================================== --}}
{{-- 3. FILTER & PENCARIAN TERPADU                                              --}}
{{-- ========================================================================== --}}
<div class="akademik-card" style="margin-bottom:16px;">
  <div class="akademik-card-body" style="padding:14px 18px;">
    <form action="{{ route('akademik.matpel.index') }}" method="GET" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
      
      {{-- Filter Tahun Ajaran --}}
      <div style="min-width:180px;">
        <select name="tahun_ajaran_id" class="ak-select" onchange="this.form.submit()" style="font-weight:700;">
          @foreach($tahunAjarans as $t)
            <option value="{{ $t->id }}" {{ $selectedTaId == $t->id ? 'selected' : '' }}>
              TA {{ $t->tahun }} {{ $t->semester ? '('.ucfirst($t->semester).')' : '' }} {{ $t->is_active ? '★ Aktif' : '' }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- Search Bar --}}
      <div style="flex:1; min-width:220px;">
        <input type="text" name="search" class="ak-input" placeholder="Cari kode atau nama mata pelajaran..." value="{{ request('search') }}">
      </div>

      {{-- Filter Kelompok Jenis --}}
      <div style="width:140px;">
        <select name="jenis" class="ak-select" onchange="this.form.submit()">
          <option value="">Semua Kelompok</option>
          <option value="umum" {{ request('jenis') == 'umum' ? 'selected' : '' }}>Umum</option>
          <option value="kejuruan" {{ request('jenis') == 'kejuruan' ? 'selected' : '' }}>Kejuruan</option>
          <option value="pilihan" {{ request('jenis') == 'pilihan' ? 'selected' : '' }}>Pilihan</option>
          <option value="p5bk" {{ request('jenis') == 'p5bk' ? 'selected' : '' }}>P5BK</option>
          <option value="pkl" {{ request('jenis') == 'pkl' ? 'selected' : '' }}>PKL</option>
        </select>
      </div>

      {{-- Filter Tingkat --}}
      <div style="width:130px;">
        <select name="tingkat" class="ak-select" onchange="this.form.submit()">
          <option value="">Semua Tingkat</option>
          <option value="X" {{ request('tingkat') == 'X' ? 'selected' : '' }}>Kelas X</option>
          <option value="XI" {{ request('tingkat') == 'XI' ? 'selected' : '' }}>Kelas XI</option>
          <option value="XII" {{ request('tingkat') == 'XII' ? 'selected' : '' }}>Kelas XII</option>
        </select>
      </div>

      {{-- Filter Jurusan --}}
      <div style="width:160px;">
        <select name="jurusan_id" class="ak-select" onchange="this.form.submit()">
          <option value="">Semua Jurusan</option>
          @foreach($jurusans as $j)
            <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
          @endforeach
        </select>
      </div>

      <button type="submit" class="ak-btn ak-btn-secondary" title="Terapkan Pencarian">
        <i class="bi bi-search"></i>
      </button>

      @if(request()->anyFilled(['search', 'jenis', 'tingkat', 'jurusan_id']) || (request('tahun_ajaran_id') && request('tahun_ajaran_id') != $ta?->id))
        <a href="{{ route('akademik.matpel.index', ['tahun_ajaran_id' => $ta?->id]) }}" class="ak-btn ak-btn-secondary" title="Reset Filter" style="color:#ef4444;">
          <i class="bi bi-x-circle"></i>
          <span>Reset</span>
        </a>
      @endif
    </form>
  </div>
</div>

{{-- ========================================================================== --}}
{{-- 4. TABEL MASTER MATA PELAJARAN                                             --}}
{{-- ========================================================================== --}}
<div class="akademik-card">
  <div class="akademik-card-body" style="padding:0;">
    @if($mapels->isEmpty())
      <div style="padding:60px 20px; text-align:center; color:var(--ak-slate-600);">
        <i class="bi bi-journal-x" style="font-size:44px; opacity:0.35; display:block; margin-bottom:12px;"></i>
        <div style="font-weight:700; font-size:15px; color:#1e293b;">Tidak ada data mata pelajaran</div>
        <div style="font-size:13px; color:#64748b; margin-top:4px;">Silakan sesuaikan kriteria pencarian atau tambahkan mata pelajaran baru.</div>
      </div>
    @else
      <div class="akademik-table-wrap">
        <table class="akademik-table">
          <thead>
            <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0; font-size:11.5px; text-transform:uppercase; letter-spacing:0.5px; color:#64748b;">
              <th style="width:46px; text-align:center; padding:12px 14px;">No</th>
              <th style="width:90px; text-align:center; padding:12px 14px;">Kode</th>
              <th style="padding:12px 14px;">Mata Pelajaran</th>
              <th style="padding:12px 14px;">Kelompok</th>
              <th style="padding:12px 14px; text-align:center;">Tingkat &amp; Fase</th>
              <th style="padding:12px 14px; text-align:center;">Beban JP</th>
              <th style="padding:12px 14px;">Konsentrasi Jurusan</th>
              <th style="padding:12px 14px;">Ruang / Lab</th>
              <th style="width:80px; text-align:center; padding:12px 14px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($mapels as $idx => $m)
              @php
                $jenisColor = match($m->jenis) {
                  'kejuruan' => '#6d28d9',
                  'pilihan'  => '#0284c7',
                  'p5bk'     => '#b45309',
                  'pkl'      => '#15803d',
                  default    => '#475569',
                };
                $jenisBg = match($m->jenis) {
                  'kejuruan' => '#f5f3ff',
                  'pilihan'  => '#f0f9ff',
                  'p5bk'     => '#fef3c7',
                  'pkl'      => '#ecfdf5',
                  default    => '#f1f5f9',
                };
              @endphp
              <tr style="border-bottom:1px solid #f1f5f9;">
                {{-- No --}}
                <td style="text-align:center; color:#64748b; font-weight:600; font-size:12.5px;">
                  {{ $mapels->firstItem() + $idx }}
                </td>

                {{-- Kode Mapel --}}
                <td style="text-align:center;">
                  <span style="font-weight:800; font-size:12px; color:#4338ca; font-family:var(--font-mono, monospace); background:#eef2ff; border:1px solid #c7d2fe; padding:3px 8px; border-radius:6px; letter-spacing:0.5px;">
                    {{ $m->kode_mapel }}
                  </span>
                </td>

                {{-- Nama Mata Pelajaran --}}
                <td>
                  <div style="font-weight:700; font-size:13.5px; color:#0f172a;">
                    {{ $m->nama_mapel }}
                  </div>
                  @if($m->deskripsi_cp)
                    <div style="font-size:11px; color:#64748b; margin-top:2px; max-width:420px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                      {{ $m->deskripsi_cp }}
                    </div>
                  @endif
                </td>

                {{-- Kelompok --}}
                <td>
                  <span style="font-weight:700; font-size:11.5px; color:{{ $jenisColor }}; background:{{ $jenisBg }}; padding:3px 9px; border-radius:6px; display:inline-block;">
                    {{ $m->jenis_label }}
                  </span>
                </td>

                {{-- Tingkat & Fase --}}
                <td style="text-align:center;">
                  <div style="display:flex; justify-content:center; gap:4px; align-items:center; flex-wrap:wrap;">
                    @foreach($m->tingkat_array as $tk)
                      <span style="font-weight:700; font-size:11px; color:#334155; background:#e2e8f0; padding:2px 7px; border-radius:4px;">
                        Kelas {{ $tk }}
                      </span>
                    @endforeach
                  </div>
                  <div style="font-size:10.5px; color:#64748b; font-weight:600; margin-top:3px;">
                    {{ $m->fase_label }}
                  </div>
                </td>

                {{-- Beban Jam (JP) --}}
                <td style="text-align:center; font-weight:800; font-size:13px; color:#0f172a; white-space:nowrap;">
                  <span style="background:#f8fafc; border:1px solid #e2e8f0; padding:3px 10px; border-radius:6px;">
                    {{ $m->jumlah_jam_per_minggu }} JP
                  </span>
                </td>

                {{-- Konsentrasi Jurusan --}}
                <td>
                  @if($m->jurusan)
                    <span style="font-weight:600; font-size:12.5px; color:#1e293b;">
                      {{ $m->jurusan->nama_jurusan }}
                    </span>
                  @else
                    <span style="font-size:12px; color:#64748b;">
                      Semua Jurusan
                    </span>
                  @endif
                </td>

                {{-- Ruang / Lab --}}
                <td>
                  @if($m->resource_key)
                    <span style="font-weight:700; font-size:11.5px; color:#2563eb; background:#eff6ff; border:1px solid #bfdbfe; padding:3px 8px; border-radius:6px; display:inline-flex; align-items:center; gap:5px;">
                      <i class="bi bi-display"></i> {{ $m->resource_label }}
                    </span>
                  @else
                    <span style="color:#94a3b8; font-size:12px;">Kelas Reguler</span>
                  @endif
                </td>

                {{-- Aksi --}}
                <td style="text-align:center;">
                  <div style="display:flex; justify-content:center; gap:6px;">
                    <button type="button" class="btn btn-sm" 
                            title="Ubah Mata Pelajaran"
                            style="width:30px; height:30px; padding:0; border-radius:6px; border:1px solid #cbd5e1; background:#ffffff; color:#334155; display:inline-flex; align-items:center; justify-content:center; transition:all 0.15s;"
                            onclick="openEditMapel({{ json_encode([
                              'id' => $m->id,
                              'kode_mapel' => $m->kode_mapel,
                              'nama_mapel' => $m->nama_mapel,
                              'jenis' => $m->jenis,
                              'tingkat' => $m->tingkat,
                              'jumlah_jam_per_minggu' => $m->jumlah_jam_per_minggu,
                              'jurusan_id' => $m->jurusan_id,
                              'resource_key' => $m->resource_key,
                              'deskripsi_cp' => $m->deskripsi_cp,
                              'update_url' => route('akademik.matpel.update', $m->id)
                            ]) }})">
                      <i class="bi bi-pencil" style="font-size:12px;"></i>
                    </button>

                    <form action="{{ route('akademik.matpel.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Hapus mata pelajaran {{ $m->nama_mapel }}?')" style="margin:0;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm" title="Hapus" style="width:30px; height:30px; padding:0; border-radius:6px; border:1px solid #fecdd3; background:#fff1f2; color:#e11d48; display:inline-flex; align-items:center; justify-content:center; transition:all 0.15s;">
                        <i class="bi bi-trash" style="font-size:12px;"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      <div style="padding:14px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; border-top:1px solid #f1f5f9;">
        <div style="font-size:12.5px; color:#64748b;">
          Menampilkan {{ $mapels->firstItem() ?? 0 }} - {{ $mapels->lastItem() ?? 0 }} dari total {{ $mapels->total() }} mata pelajaran
        </div>
        <div>
          {{ $mapels->links() }}
        </div>
      </div>
    @endif
  </div>
</div>

{{-- ========================================================================== --}}
{{-- 5. MODAL FORM TERPADU (TAMBAH / EDIT MATA PELAJARAN)                       --}}
{{-- ========================================================================== --}}
<div class="modal fade" id="modalFormMapel" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:14px; border:none; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);">
      <form id="formMapel" method="POST" action="{{ route('akademik.matpel.store') }}">
        @csrf
        <input type="hidden" name="_method" id="formMapelMethod" value="POST">
        <input type="hidden" name="tahun_ajaran_id" value="{{ $selectedTaId }}">

        <div class="modal-header" style="border-bottom:1px solid #f1f5f9; padding:16px 20px;">
          <div style="display:flex; align-items:center; gap:10px;">
            <div id="modalIconWrap" style="width:34px; height:34px; border-radius:8px; background:#eff2fe; color:#4f46e5; display:flex; align-items:center; justify-content:center; font-size:16px;">
              <i class="bi bi-book"></i>
            </div>
            <div>
              <h5 class="modal-title" id="modalFormTitle" style="font-weight:800; font-size:16px; margin:0; color:#0f172a;">
                Tambah Mata Pelajaran
              </h5>
              <div style="font-size:11.5px; color:#64748b; margin-top:2px;">
                Tahun Ajaran: <span style="font-weight:700; color:#334155;">{{ $selectedTa?->tahun ?? '-' }}</span>
              </div>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body" style="padding:20px;">
          {{-- Baris 1: Kode & Nama --}}
          <div style="display:grid; grid-template-columns:120px 1fr; gap:12px; margin-bottom:14px;">
            <div>
              <label class="ak-form-label" style="font-weight:700;">Kode <span style="color:#ef4444;">*</span></label>
              <input type="text" name="kode_mapel" id="inputKodeMapel" class="ak-input" placeholder="RPL-01" required style="font-family:var(--font-mono, monospace); font-weight:700; text-transform:uppercase;">
            </div>
            <div>
              <label class="ak-form-label" style="font-weight:700;">Nama Mata Pelajaran <span style="color:#ef4444;">*</span></label>
              <input type="text" name="nama_mapel" id="inputNamaMapel" class="ak-input" placeholder="Contoh: Pemrograman Web & Perangkat Bergerak" required>
            </div>
          </div>

          {{-- Baris 2: Kelompok & Beban JP --}}
          <div style="display:grid; grid-template-columns:1fr 140px; gap:12px; margin-bottom:14px;">
            <div>
              <label class="ak-form-label" style="font-weight:700;">Kelompok Kurikulum <span style="color:#ef4444;">*</span></label>
              <select name="jenis" id="inputJenisMapel" class="ak-select" required onchange="handleJenisChange(this.value)">
                <option value="umum">Umum</option>
                <option value="kejuruan" selected>Kejuruan</option>
                <option value="pilihan">Mapel Pilihan</option>
                <option value="p5bk">P5BK</option>
                <option value="pkl">PKL</option>
              </select>
            </div>
            <div>
              <label class="ak-form-label" style="font-weight:700;">Beban Jam (JP) <span style="color:#ef4444;">*</span></label>
              <div style="display:flex; align-items:center; gap:6px;">
                <input type="number" name="jumlah_jam_per_minggu" id="inputJpMapel" class="ak-input" value="4" min="1" max="20" required style="text-align:center; font-weight:700;">
                <span style="font-size:12px; font-weight:700; color:#64748b;">/ mg</span>
              </div>
            </div>
          </div>

          {{-- Baris 3: Tingkat Kelas --}}
          <div style="margin-bottom:14px;">
            <label class="ak-form-label" style="font-weight:700; margin-bottom:6px;">Tingkat Kelas</label>
            <div style="display:flex; gap:14px; align-items:center; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:8px 14px;">
              <label style="display:flex; align-items:center; gap:6px; font-size:12.5px; font-weight:600; cursor:pointer; margin:0;">
                <input type="checkbox" name="tingkats[]" value="X" id="chkTkX" checked style="cursor:pointer; width:16px; height:16px;">
                <span>Kelas X (Fase E)</span>
              </label>
              <label style="display:flex; align-items:center; gap:6px; font-size:12.5px; font-weight:600; cursor:pointer; margin:0;">
                <input type="checkbox" name="tingkats[]" value="XI" id="chkTkXI" checked style="cursor:pointer; width:16px; height:16px;">
                <span>Kelas XI (Fase F)</span>
              </label>
              <label style="display:flex; align-items:center; gap:6px; font-size:12.5px; font-weight:600; cursor:pointer; margin:0;">
                <input type="checkbox" name="tingkats[]" value="XII" id="chkTkXII" checked style="cursor:pointer; width:16px; height:16px;">
                <span>Kelas XII (Fase F)</span>
              </label>
            </div>
          </div>

          {{-- Baris 4: Jurusan Khusus --}}
          <div style="margin-bottom:14px;" id="wrapJurusan">
            <label class="ak-form-label" style="font-weight:700;">Konsentrasi Keahlian / Jurusan</label>
            <select name="jurusan_id" id="inputJurusanId" class="ak-select">
              <option value="">Semua Jurusan</option>
              @foreach($jurusans as $j)
                <option value="{{ $j->id }}">{{ $j->nama_jurusan }}</option>
              @endforeach
            </select>
          </div>

          {{-- Baris 5: Ruangan / Lab Khusus --}}
          <div style="margin-bottom:14px;">
            <label class="ak-form-label" style="font-weight:700;">Kebutuhan Ruangan / Lab Khusus (Proteksi Bentrok)</label>
            <select name="resource_key" id="inputResourceKey" class="ak-select">
              <option value="">Kelas Reguler (Tidak Butuh Lab Khusus)</option>
              @foreach(\App\Models\AkademikMataPelajaran::RESOURCES as $k => $lbl)
                <option value="{{ $k }}">{{ $lbl }}</option>
              @endforeach
            </select>
            <div style="font-size:11px; color:#64748b; margin-top:4px;">
              💡 Sistem otomatis memvalidasi jadwal agar tidak terjadi bentrok ruangan antar kelas di jam yang sama.
            </div>
          </div>

          {{-- Baris 6: Deskripsi Ringkas / CP --}}
          <div>
            <label class="ak-form-label" style="font-weight:700;">Catatan / Ringkasan Materi (Opsional)</label>
            <textarea name="deskripsi_cp" id="inputDeskripsiCp" class="ak-input" rows="2" placeholder="Catatan lingkup materi atau referensi kurikulum..."></textarea>
          </div>
        </div>

        <div class="modal-footer" style="border-top:1px solid #f1f5f9; padding:14px 20px;">
          <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="ak-btn ak-btn-primary" id="btnSubmitForm">
            <i class="bi bi-check2-circle"></i>
            <span id="btnSubmitText">Simpan Mata Pelajaran</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  let modalInstance = null;

  document.addEventListener('DOMContentLoaded', function() {
    const el = document.getElementById('modalFormMapel');
    if (el) {
      modalInstance = new bootstrap.Modal(el);
    }
  });

  function openTambahMapel() {
    const form = document.getElementById('formMapel');
    form.action = "{{ route('akademik.matpel.store') }}";
    document.getElementById('formMapelMethod').value = "POST";
    document.getElementById('modalFormTitle').innerText = "Tambah Mata Pelajaran";
    document.getElementById('btnSubmitText').innerText = "Simpan Mata Pelajaran";

    // Reset input fields
    document.getElementById('inputKodeMapel').value = "";
    document.getElementById('inputNamaMapel').value = "";
    document.getElementById('inputJenisMapel').value = "kejuruan";
    document.getElementById('inputJpMapel').value = "4";
    document.getElementById('chkTkX').checked = true;
    document.getElementById('chkTkXI').checked = true;
    document.getElementById('chkTkXII').checked = true;
    document.getElementById('inputJurusanId').value = "";
    document.getElementById('inputResourceKey').value = "";
    document.getElementById('inputDeskripsiCp').value = "";

    handleJenisChange("kejuruan");

    if (modalInstance) modalInstance.show();
  }

  function openEditMapel(data) {
    const form = document.getElementById('formMapel');
    form.action = data.update_url;
    document.getElementById('formMapelMethod').value = "PUT";
    document.getElementById('modalFormTitle').innerText = "Ubah Mata Pelajaran";
    document.getElementById('btnSubmitText').innerText = "Simpan Perubahan";

    // Fill form fields
    document.getElementById('inputKodeMapel').value = data.kode_mapel || "";
    document.getElementById('inputNamaMapel').value = data.nama_mapel || "";
    document.getElementById('inputJenisMapel').value = data.jenis || "umum";
    document.getElementById('inputJpMapel').value = data.jumlah_jam_per_minggu || 4;

    const tkList = (data.tingkat || "").split(',').map(s => s.trim());
    document.getElementById('chkTkX').checked = tkList.includes('X');
    document.getElementById('chkTkXI').checked = tkList.includes('XI');
    document.getElementById('chkTkXII').checked = tkList.includes('XII');

    document.getElementById('inputJurusanId').value = data.jurusan_id || "";
    document.getElementById('inputResourceKey').value = data.resource_key || "";
    document.getElementById('inputDeskripsiCp').value = data.deskripsi_cp || "";

    handleJenisChange(data.jenis);

    if (modalInstance) modalInstance.show();
  }

  function handleJenisChange(jenis) {
    const wrapJurusan = document.getElementById('wrapJurusan');
    const inputJurusan = document.getElementById('inputJurusanId');
    if (!wrapJurusan) return;

    if (jenis === 'umum' || jenis === 'p5bk') {
      inputJurusan.value = "";
      wrapJurusan.style.opacity = '0.5';
      inputJurusan.disabled = true;
    } else {
      wrapJurusan.style.opacity = '1';
      inputJurusan.disabled = false;
    }
  }
</script>
@endpush
