@extends('dcc.akademik.layout')

@section('title', 'Asesmen Penilaian Berbasis Online')
@section('breadcrumb', 'Asesmen Online')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Asesmen Penilaian Berbasis Online</h1>
    <div class="akademik-page-desc">
      Evaluasi pembelajaran berbasis komputer (CBT): Kuis, Ulangan Harian, Sumatif Tengah Semester (STS), dan Sumatif Akhir Semester (SAS).
    </div>
  </div>

  <div style="display:flex; gap:8px;">
    <a href="{{ route('akademik.bank_soal.index') }}" class="ak-btn ak-btn-secondary">
      <i class="bi bi-archive me-1"></i>
      <span>Bank Soal</span>
    </a>
    <a href="{{ route('akademik.asesmen.create') }}" class="ak-btn ak-btn-primary">
      <i class="bi bi-plus-circle"></i>
      <span>Buat Paket Asesmen Baru</span>
    </a>
  </div>
</div>

{{-- Filter Card --}}
<div class="akademik-card" style="margin-bottom:20px;">
  <div class="akademik-card-body" style="padding:14px 20px;">
    <form action="{{ route('akademik.asesmen.index') }}" method="GET" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
      
      <div style="width:200px;">
        <select name="mapel_id" class="ak-select" onchange="this.form.submit()">
          <option value="">Semua Mata Pelajaran</option>
          @foreach($filterMapels as $m)
            <option value="{{ $m->id }}" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>
              {{ $m->nama_mapel }}
            </option>
          @endforeach
        </select>
      </div>

      <div style="width:180px;">
        <select name="rombel_id" class="ak-select" onchange="this.form.submit()">
          <option value="">Semua Rombel</option>
          @foreach($filterRombels as $r)
            <option value="{{ $r->id }}" {{ request('rombel_id') == $r->id ? 'selected' : '' }}>
              {{ $r->nama_rombel }}
            </option>
          @endforeach
        </select>
      </div>

      <div style="width:180px;">
        <select name="jenis" class="ak-select" onchange="this.form.submit()">
          <option value="">Semua Jenis Asesmen</option>
          <option value="kuis" {{ request('jenis') == 'kuis' ? 'selected' : '' }}>Kuis Harian</option>
          <option value="ulangan_harian" {{ request('jenis') == 'ulangan_harian' ? 'selected' : '' }}>Ulangan Harian (UH)</option>
          <option value="pts" {{ request('jenis') == 'pts' ? 'selected' : '' }}>STS (Sumatif Tengah Semester)</option>
          <option value="pas" {{ request('jenis') == 'pas' ? 'selected' : '' }}>SAS (Sumatif Akhir Semester)</option>
          <option value="tugas" {{ request('jenis') == 'tugas' ? 'selected' : '' }}>Tugas Daring</option>
        </select>
      </div>

      <button type="submit" class="ak-btn ak-btn-secondary" title="Terapkan Filter">
        <i class="bi bi-filter"></i>
      </button>

      @if(request()->anyFilled(['mapel_id', 'rombel_id', 'jenis']))
        <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary" title="Reset Filter">
          <i class="bi bi-x-circle"></i>
        </a>
      @endif
    </form>
  </div>
</div>

{{-- Data Table --}}
<div class="akademik-card">
  <div class="akademik-card-body" style="padding:0;">
    @if($asesmens->isEmpty())
      <div style="padding:48px 20px; text-align:center; color:#64748b;">
        <i class="bi bi-laptop" style="font-size:40px; opacity:0.35; display:block; margin-bottom:10px;"></i>
        Belum ada asesmen online yang dibuat. Klik tombol "Buat Asesmen Online Baru" di atas.
      </div>
    @else
      <style>
        .table-asesmen-minimal {
          width: 100%;
          border-collapse: collapse;
          font-size: 13px;
          text-align: left;
        }
        .table-asesmen-minimal th {
          padding: 14px 18px;
          font-size: 11px;
          font-weight: 700;
          text-transform: uppercase;
          letter-spacing: 0.05em;
          color: #000000;
          background: #fafafa;
          border-bottom: 1px solid #e5e7eb;
        }
        .table-asesmen-minimal td {
          padding: 16px 18px;
          vertical-align: middle;
          border-bottom: 1px solid #f3f4f6;
          color: #000000;
          background: transparent;
        }
        .asesmen-row {
          transition: background-color 0.15s ease;
        }
        .asesmen-row:hover {
          background-color: #f9fafb !important;
        }
        .btn-horev {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          padding: 5px 12px;
          font-size: 12px;
          font-weight: 600;
          color: #000000;
          background: transparent;
          border: 1px solid #000000;
          border-radius: 6px;
          text-decoration: none;
          cursor: pointer;
          transition: all 0.15s ease;
          white-space: nowrap;
          line-height: 1.2;
        }
        .btn-horev:hover {
          background: #000000;
          color: #ffffff !important;
        }
        .btn-horev-danger {
          color: #000000;
          border-color: #000000;
        }
        .btn-horev-danger:hover {
          background: #000000;
          color: #ffffff !important;
          border-color: #000000;
        }
      </style>

      <div class="akademik-table-wrap" style="overflow-x:auto;">
        <table class="table-asesmen-minimal">
          <thead>
            <tr>
              <th style="width:40px;">No</th>
              <th>Judul Asesmen</th>
              <th>Mata Pelajaran &amp; Rombel</th>
              <th>Jenis &amp; Durasi</th>
              <th>Butir Soal</th>
              <th>Peserta Selesai</th>
              <th>Status Ujian</th>
              <th style="text-align:right; width:340px;">Aksi &amp; Ujian</th>
            </tr>
          </thead>
          <tbody>
            @foreach($asesmens as $idx => $a)
              <tr class="asesmen-row">
                <td style="color:#000000; font-weight:600;">{{ $asesmens->firstItem() + $idx }}</td>
                <td>
                  <div style="font-weight:700; color:#000000; font-size:14px; line-height:1.3;">
                    {{ $a->judul }}
                  </div>
                </td>
                <td>
                  <div style="font-weight:600; color:#000000; font-size:13px;">
                    {{ $a->distribusi?->mataPelajaran?->nama_mapel ?? '-' }}
                  </div>
                  <div style="color:#000000; font-size:12px; margin-top:2px;">
                    @php $targetRombels = $a->getTargetRombels(); @endphp
                    @if($targetRombels->count() > 1)
                      {{ $targetRombels->count() }} Rombel: {{ $a->rombel_names }}
                    @else
                      {{ $targetRombels->first()?->nama_rombel ?? ($a->distribusi?->rombel?->nama_rombel ?? '-') }}
                    @endif
                  </div>
                </td>
                <td>
                  <div style="font-weight:600; color:#000000; text-transform:uppercase; font-size:13px;">
                    {{ str_replace('_', ' ', $a->jenis) }}
                  </div>
                  <div style="color:#000000; font-size:12px; margin-top:2px;">
                    {{ $a->durasi_menit }} Menit
                  </div>
                </td>
                <td>
                  <a href="{{ route('akademik.asesmen.soal', $a->id) }}" style="color:#000000; font-weight:600; font-size:13px; text-decoration:none;" title="Kelola Butir Soal">
                    {{ $a->soals_count }} Soal
                  </a>
                </td>
                <td>
                  <a href="{{ route('akademik.asesmen.hasil', $a->id) }}" style="color:#000000; font-weight:600; font-size:13px; text-decoration:none;" title="Lihat Rekap Nilai">
                    {{ $a->hasils_count }} Siswa
                  </a>
                </td>
                <td>
                  <form action="{{ route('akademik.asesmen.toggle', $a->id) }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" style="background:none; border:none; padding:0; color:#000000; font-size:13px; font-weight:600; cursor:pointer; text-decoration:underline;" title="Klik untuk mengubah status aktif">
                      {{ $a->is_active ? 'Aktif' : 'Nonaktif' }}
                    </button>
                  </form>
                </td>
                <td style="text-align:right;">
                  <div style="display:flex; justify-content:flex-end; align-items:center; gap:6px; flex-wrap:wrap;">
                    <button type="button" class="btn-horev" data-bs-toggle="modal" data-bs-target="#modalEditAsesmen{{ $a->id }}" title="Edit Asesmen">
                      Edit
                    </button>
                    <a href="{{ route('akademik.asesmen.soal', $a->id) }}" class="btn-horev" title="Kelola Butir Soal">
                      Soal
                    </a>
                    <a href="{{ route('akademik.asesmen.penugasan', $a->id) }}" class="btn-horev" title="Atur Sesi &amp; Penugasan Rombel">
                      Penugasan
                    </a>
                    <a href="{{ route('akademik.asesmen.hasil', $a->id) }}" class="btn-horev" title="Pantau Hasil &amp; Nilai Siswa">
                      Hasil
                    </a>
                    <a href="{{ route('akademik.asesmen.kerjakan', $a->id) }}" class="btn-horev" title="Simulasi Ujian CBT">
                      Ujian
                    </a>
                    <form action="{{ route('akademik.asesmen.destroy', $a->id) }}" method="POST" style="margin:0; display:inline;" onsubmit="return confirm('Hapus paket asesmen {{ addslashes($a->judul) }} beserta seluruh butir soal?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn-horev btn-horev-danger" title="Hapus Asesmen">
                        Hapus
                      </button>
                    </form>
                  </div>

                  {{-- Modal Edit Asesmen --}}
                  <div class="modal fade" id="modalEditAsesmen{{ $a->id }}" tabindex="-1" aria-hidden="true" style="text-align:left;">
                    <div class="modal-dialog">
                      <div class="modal-content" style="border-radius:12px; border:1px solid #000000;">
                        <form action="{{ route('akademik.asesmen.update', $a->id) }}" method="POST">
                          @csrf
                          @method('PUT')
                          <div class="modal-header" style="border-bottom:1px solid #e5e7eb; padding:14px 20px;">
                            <h5 class="modal-title" style="font-weight:700; font-size:15px; color:#000000;">Edit Paket Asesmen</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body" style="padding:20px;">
                            <div style="margin-bottom:14px;">
                              <label class="ak-form-label" style="color:#000000; font-weight:600; font-size:12px; margin-bottom:6px;">Judul Asesmen</label>
                              <input type="text" name="judul" class="ak-input" value="{{ $a->judul }}" required style="color:#000000; border-color:#d1d5db;">
                            </div>
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
                              <div>
                                <label class="ak-form-label" style="color:#000000; font-weight:600; font-size:12px; margin-bottom:6px;">Jenis Asesmen</label>
                                <select name="jenis" class="ak-select" required style="color:#000000; border-color:#d1d5db;">
                                  <option value="kuis" {{ $a->jenis === 'kuis' ? 'selected' : '' }}>Kuis Harian</option>
                                  <option value="ulangan_harian" {{ $a->jenis === 'ulangan_harian' ? 'selected' : '' }}>Ulangan Harian (UH)</option>
                                  <option value="pts" {{ $a->jenis === 'pts' ? 'selected' : '' }}>STS (Sumatif Tengah Semester)</option>
                                  <option value="pas" {{ $a->jenis === 'pas' ? 'selected' : '' }}>SAS (Sumatif Akhir Semester)</option>
                                  <option value="tugas" {{ $a->jenis === 'tugas' ? 'selected' : '' }}>Tugas Daring</option>
                                </select>
                              </div>
                              <div>
                                <label class="ak-form-label" style="color:#000000; font-weight:600; font-size:12px; margin-bottom:6px;">Durasi (Menit)</label>
                                <input type="number" name="durasi_menit" class="ak-input" value="{{ $a->durasi_menit }}" min="1" max="300" required style="color:#000000; border-color:#d1d5db;">
                              </div>
                            </div>
                            <div>
                              <label class="ak-form-label" style="color:#000000; font-weight:600; font-size:12px; margin-bottom:6px;">Nilai KKM (Passing Grade)</label>
                              <input type="number" name="passing_grade" class="ak-input" value="{{ $a->passing_grade }}" min="0" max="100" required style="color:#000000; border-color:#d1d5db;">
                            </div>
                          </div>
                          <div class="modal-footer" style="border-top:1px solid #e5e7eb; padding:12px 20px;">
                            <button type="button" class="btn-horev" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn-horev" style="background:#000000; color:#ffffff;">Simpan Perubahan</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div style="padding:16px 20px;">
        {{ $asesmens->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
