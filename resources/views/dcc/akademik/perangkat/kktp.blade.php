@extends('dcc.akademik.layout')

@section('title', 'Kriteria Ketuntasan (KKTP) — Perangkat Pembelajaran')
@section('breadcrumb')
  <a href="{{ route('akademik.perangkat.kktp') }}">Perangkat Pembelajaran</a>
  <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
  <span>Kriteria Ketuntasan (KKTP)</span>
@endsection

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">
      <i class="bi bi-speedometer2 text-primary me-2"></i>
      5. Kriteria Ketercapaian Tujuan Pembelajaran (KKTP)
    </h1>
    <div class="akademik-page-desc">
      Penetapan interval nilai ketuntasan, rubrik pencapaian kompetensi, dan tindak lanjut intervensi remedial atau pengayaan peserta didik.
    </div>
  </div>

  @if($activePerangkat && ($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka))
    <button type="button" class="ak-btn ak-btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKktp">
      <i class="bi bi-plus-lg"></i>
      <span>+ Atur Kriteria KKTP</span>
    </button>
  @endif
</div>

@include('dcc.akademik.perangkat.partials.selector')

@if($activePerangkat)
  {{-- Standar Interval Nilai Resmi SMKN 1 Air Naningan --}}
  <div class="akademik-card" style="margin-bottom:20px; padding:18px 22px;">
    <h2 style="font-size:15px; font-weight:800; color:var(--ak-dark); margin:0 0 10px;">
      <i class="bi bi-sliders text-primary me-1"></i> Standar Interval Nilai Ketuntasan Kurikulum Merdeka
    </h2>
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:12px;">
      <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:10px; padding:12px 14px;">
        <div style="font-size:11px; font-weight:800; color:#991b1b; text-transform:uppercase;">0 – 60 (Belum Tuntas)</div>
        <div style="font-size:13px; font-weight:800; color:#991b1b; margin-top:2px;">Perlu Bimbingan Khusus</div>
        <div style="font-size:11.5px; color:#7f1d1d; margin-top:4px;">Remedial menyeluruh pada aspek dasar tujuan pembelajaran.</div>
      </div>
      <div style="background:#fef3c7; border:1px solid #fde68a; border-radius:10px; padding:12px 14px;">
        <div style="font-size:11px; font-weight:800; color:#92400e; text-transform:uppercase;">61 – 74 (Cukup / Menuju Tuntas)</div>
        <div style="font-size:13px; font-weight:800; color:#92400e; margin-top:2px;">Remedial Bagian Tertentu</div>
        <div style="font-size:11.5px; color:#78350f; margin-top:4px;">Mengulang materi atau jobsheet yang belum dikuasai siswa.</div>
      </div>
      <div style="background:#ecfdf5; border:1px solid #a7f3d0; border-radius:10px; padding:12px 14px;">
        <div style="font-size:11px; font-weight:800; color:#065f46; text-transform:uppercase;">75 – 85 (Tuntas Mandiri)</div>
        <div style="font-size:13px; font-weight:800; color:#065f46; margin-top:2px;">Mencapai Ketuntasan</div>
        <div style="font-size:11.5px; color:#064e3b; margin-top:4px;">Melanjutkan ke tujuan pembelajaran (TP) berikutnya.</div>
      </div>
      <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:12px 14px;">
        <div style="font-size:11px; font-weight:800; color:#1e40af; text-transform:uppercase;">86 – 100 (Sangat Mahir)</div>
        <div style="font-size:13px; font-weight:800; color:#1e40af; margin-top:2px;">Tuntas &amp; Pengayaan</div>
        <div style="font-size:11.5px; color:#1e3a8a; margin-top:4px;">Diberikan tantangan proyek mandiri / tutor sebaya praktik.</div>
      </div>
    </div>
  </div>

  {{-- Tabel Rincian KKTP per TP --}}
  <div class="akademik-card">
    <div class="akademik-card-header" style="background:#f8fafc; display:flex; justify-content:space-between; align-items:center;">
      <h3 class="akademik-card-title" style="font-size:15px;">
        <i class="bi bi-check2-square text-primary me-2"></i>
        Daftar Kriteria Ketuntasan Tujuan Pembelajaran
      </h3>
      @if($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka)
        <button type="button" class="ak-btn ak-btn-primary ak-btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKktp">
          <i class="bi bi-plus-lg me-1"></i> Atur KKTP Baru
        </button>
      @endif
    </div>

    <div class="akademik-card-body" style="padding:0;">
      <div class="akademik-table-wrap">
        <table class="akademik-table">
          <thead>
            <tr>
              <th style="width:40px; text-align:center;">No</th>
              <th style="width:90px;">Kode TP</th>
              <th>Tujuan Pembelajaran Terkait</th>
              <th style="width:140px;">Pendekatan KKTP</th>
              <th>Deskripsi Kriteria Ketercapaian (Tuntas)</th>
              <th>Tindak Lanjut Remedial</th>
            </tr>
          </thead>
          <tbody>
            @forelse($kktpItems as $idx => $kt)
              <tr>
                <td style="text-align:center; font-weight:800;">{{ $loop->iteration }}</td>
                <td>
                  <span class="ak-badge ak-badge-primary" style="font-size:11px; font-weight:800;">
                    {{ $kt->atpItem?->kode_tp ?? 'Umum' }}
                  </span>
                </td>
                <td>
                  <div style="font-weight:700; color:var(--ak-dark); font-size:12.5px;">
                    {{ $kt->atpItem?->tujuan_pembelajaran ?? 'Kriteria Penilaian Umum Mata Pelajaran' }}
                  </div>
                  <div style="font-size:11px; color:#64748b;">
                    Materi: {{ $kt->atpItem?->materi_pokok ?? '-' }}
                  </div>
                </td>
                <td>
                  <span class="ak-badge ak-badge-info" style="font-size:11px; text-transform:capitalize;">
                    {{ str_replace('_', ' ', $kt->pendekatan) }}
                  </span>
                </td>
                <td style="font-size:12.5px; line-height:1.5; color:#166534; background:#f0fdf4;">
                  <i class="bi bi-check-circle-fill me-1 text-success"></i>
                  {{ $kt->keterangan_tuntas ?: 'Peserta didik mencapai nilai minimal 75 dan menguasai jobsheet praktik secara mandiri.' }}
                </td>
                <td style="font-size:12.5px; line-height:1.5; color:#991b1b; background:#fff1f2;">
                  <i class="bi bi-exclamation-triangle-fill me-1 text-danger"></i>
                  {{ $kt->keterangan_remedial ?: 'Diberikan bimbingan khusus pada sub-materi yang belum tuntas dan pengujian ulang jobsheet.' }}
                </td>
              </tr>
            @empty
              @if($atpItems->isNotEmpty())
                @foreach($atpItems as $atp)
                  <tr>
                    <td style="text-align:center; font-weight:800;">{{ $loop->iteration }}</td>
                    <td>
                      <span class="ak-badge ak-badge-primary" style="font-size:11px; font-weight:800;">{{ $atp->kode_tp }}</span>
                    </td>
                    <td>
                      <div style="font-weight:700; color:var(--ak-dark); font-size:12.5px;">{{ $atp->tujuan_pembelajaran }}</div>
                      <div style="font-size:11px; color:#64748b;">Materi: {{ $atp->materi_pokok }}</div>
                    </td>
                    <td>
                      <span class="ak-badge ak-badge-secondary" style="font-size:11px;">Interval Nilai</span>
                    </td>
                    <td style="font-size:12px; color:#065f46;">
                      Nilai &ge; 75: Mampu mendemonstrasikan target kompetensi materi {{ $atp->materi_pokok }} sesuai SOP.
                    </td>
                    <td style="font-size:12px; color:#991b1b;">
                      Nilai &lt; 75: Pendampingan tutor sebaya &amp; latihan soal/praktik terarah pada butir {{ $atp->kode_tp }}.
                    </td>
                  </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="6" style="text-align:center; padding:36px; color:#64748b;">
                    Belum ada butir KKTP atau Tujuan Pembelajaran (TP).
                  </td>
                </tr>
              @endif
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Modal Tambah KKTP --}}
  @if($activePerangkat && ($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka))
  <div class="modal fade" id="modalTambahKktp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius:14px;">
        <form action="{{ route('akademik.perangkat.kktp.store', $activePerangkat->id) }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" style="font-weight:800; font-size:16px;">
              <i class="bi bi-plus-circle text-primary me-1"></i> Atur Kriteria Ketercapaian (KKTP)
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" style="padding:20px;">
            <div style="display:grid; grid-template-columns: 2fr 1fr; gap:12px; margin-bottom:14px;">
              <div>
                <label class="ak-form-label">Tautkan ke Butir TP</label>
                <select name="atp_item_id" class="ak-select">
                  <option value="">-- Kriteria Seluruh TP (Umum) --</option>
                  @foreach($atpItems as $atp)
                    <option value="{{ $atp->id }}">[{{ $atp->kode_tp }}] {{ Str::limit($atp->tujuan_pembelajaran, 55) }}</option>
                  @endforeach
                </select>
              </div>
              <div>
                <label class="ak-form-label">Pendekatan Penilaian <span class="text-danger">*</span></label>
                <select name="pendekatan" class="ak-select" required>
                  <option value="interval_nilai" selected>Interval Nilai (0 - 100)</option>
                  <option value="rubrik">Rubrik Skala Kinerja</option>
                  <option value="deskripsi">Deskripsi Kriteria</option>
                </select>
              </div>
            </div>

            <div style="margin-bottom:14px;">
              <label class="ak-form-label">Deskripsi Kriteria Peserta Didik Tuntas <span class="text-danger">*</span></label>
              <textarea name="keterangan_tuntas" rows="3" class="ak-textarea" placeholder="Peserta didik mencapai nilai minimal 75 dan mampu menyelesaikan seluruh jobsheet praktik..." required></textarea>
            </div>

            <div>
              <label class="ak-form-label">Tindak Lanjut Bagi yang Belum Tuntas (Remedial)</label>
              <textarea name="keterangan_remedial" rows="2" class="ak-textarea" placeholder="Bimbingan perorangan, penugasan alternatif, dan pengujian ulang aspek yang belum tuntas..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="ak-btn ak-btn-primary">Simpan Kriteria KKTP</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif

@else
  <div class="akademik-card" style="padding:40px 20px; text-align:center; color:#64748b;">
    <i class="bi bi-speedometer2" style="font-size:36px; color:#cbd5e1; display:block; margin-bottom:12px;"></i>
    <div style="font-weight:700; font-size:15px; color:var(--ak-dark); margin-bottom:4px;">Belum Ada Folder Perangkat Ajar</div>
    <div style="font-size:12.5px;">Silakan buat folder perangkat ajar terlebih dahulu menggunakan tombol di atas.</div>
  </div>
@endif

@endsection
