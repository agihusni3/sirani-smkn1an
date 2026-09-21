@extends('dcc.akademik.layout')

@section('title', 'Tujuan & Alur Pembelajaran (TP & ATP) — Perangkat Pembelajaran')
@section('breadcrumb')
  <a href="{{ route('akademik.perangkat.atp') }}">Perangkat Pembelajaran</a>
  <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
  <span>Tujuan &amp; Alur Pembelajaran (TP &amp; ATP)</span>
@endsection

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">
      <i class="bi bi-list-ol text-primary me-2"></i>
      2. Tujuan Pembelajaran (TP) &amp; Alur Tujuan Pembelajaran (ATP)
    </h1>
    <div class="akademik-page-desc">
      Perumusan Tujuan Pembelajaran (TP), materi esensial, Profil Pelajar Pancasila, dan alokasi JP per alur tahapan KBM.
    </div>
  </div>

  @if($activePerangkat && ($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka))
    <button type="button" class="ak-btn ak-btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahAtp">
      <i class="bi bi-plus-lg"></i>
      <span>+ Tambah Butir TP / ATP</span>
    </button>
  @endif
</div>

@include('dcc.akademik.perangkat.partials.selector')

@if($activePerangkat)
  {{-- Header Info Mapel & Total JP --}}
  <div class="akademik-card" style="margin-bottom:20px; padding:16px 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
      <div>
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
          <span class="ak-badge ak-badge-primary" style="font-size:12px; font-weight:800;">
            Kelas {{ $activePerangkat->tingkat }} · Fase {{ $activePerangkat->fase }}
          </span>
          <span class="ak-badge ak-badge-secondary" style="font-size:12px;">
            Semester {{ $activePerangkat->semester == 1 ? '1 (Ganjil)' : '2 (Genap)' }}
          </span>
          <span class="ak-badge ak-badge-info" style="font-size:12px;">
            {{ $activePerangkat->mataPelajaran?->nama_mapel }}
          </span>
        </div>
        <div style="font-size:13px; color:#64748b;">
          Pendidik: <b>{{ $activePerangkat->guru?->nama }}</b> · NIP: {{ $activePerangkat->guru?->nip ?? '-' }}
        </div>
      </div>

      <div style="display:flex; gap:12px; align-items:center;">
        <div style="text-align:right;">
          <div style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase;">Akumulasi Beban ATP</div>
          <div style="font-size:22px; font-weight:900; color:var(--ak-primary);">
            {{ $totalJp }} <span style="font-size:13px; font-weight:700; color:#64748b;">JP Terjadwal</span>
          </div>
        </div>
        <div style="border-left:1px solid #e2e8f0; height:36px;"></div>
        <div style="text-align:right;">
          <div style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase;">Jumlah Butir TP</div>
          <div style="font-size:22px; font-weight:900; color:#059669;">
            {{ $atpItems->count() }} <span style="font-size:13px; font-weight:700; color:#64748b;">Tujuan</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Tabel Alur Tujuan Pembelajaran (ATP) --}}
  <div class="akademik-card">
    <div class="akademik-card-header" style="background:#f8fafc; display:flex; justify-content:space-between; align-items:center;">
      <h3 class="akademik-card-title" style="font-size:15px;">
        <i class="bi bi-table text-primary me-2"></i>
        Matriks Rincian Alur Tujuan Pembelajaran (ATP)
      </h3>
      @if($atpItems->isNotEmpty() && ($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka))
        <button type="button" class="ak-btn ak-btn-primary ak-btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahAtp" style="font-size:12px;">
          <i class="bi bi-plus-lg me-1"></i> Tambah TP
        </button>
      @endif
    </div>

    <div class="akademik-card-body" style="padding:0;">
      <div class="akademik-table-wrap">
        <table class="akademik-table">
          <thead>
            <tr>
              <th style="width:40px; text-align:center;">Alur</th>
              <th style="width:90px;">Kode TP</th>
              <th style="width:160px;">Elemen CP</th>
              <th>Tujuan Pembelajaran (TP)</th>
              <th style="width:200px;">Materi Pokok / Lingkup</th>
              <th style="width:70px; text-align:center;">JP</th>
              <th style="width:160px;">Dimensi Profil Pancasila</th>
              @if($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka)
                <th style="width:70px; text-align:center;">Aksi</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @forelse($atpItems as $idx => $atp)
              <tr>
                <td style="text-align:center; font-weight:900; color:#64748b;">
                  {{ $atp->urutan }}
                </td>
                <td>
                  <span class="ak-badge ak-badge-primary" style="font-weight:800; font-size:11.5px;">
                    {{ $atp->kode_tp }}
                  </span>
                </td>
                <td style="font-size:12.5px; font-weight:600; color:#334155;">
                  {{ $atp->elemen_cp ?: '—' }}
                </td>
                <td style="font-size:12.5px; line-height:1.55; color:var(--ak-dark); font-weight:600;">
                  {{ $atp->tujuan_pembelajaran }}
                  @if($atp->asesmen_rencana)
                    <div style="font-size:11px; color:#0284c7; margin-top:3px; font-weight:500;">
                      <i class="bi bi-check-circle me-1"></i> Asesmen: {{ $atp->asesmen_rencana }}
                    </div>
                  @endif
                </td>
                <td style="font-size:12px; color:#475569;">
                  {{ $atp->materi_pokok }}
                </td>
                <td style="text-align:center; font-weight:900; color:var(--ak-primary); font-size:13px;">
                  {{ $atp->alokasi_jp }} JP
                </td>
                <td style="font-size:11.5px; color:#475569;">
                  @if($atp->profil_pancasila)
                    <span class="badge" style="background:#f1f5f9; color:#334155; font-weight:600; text-wrap:balance; border:1px solid #e2e8f0;">
                      {{ $atp->profil_pancasila }}
                    </span>
                  @else
                    —
                  @endif
                </td>
                @if($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka)
                  <td style="text-align:center;">
                    <form action="{{ route('akademik.perangkat.atp.destroy', ['id' => $activePerangkat->id, 'atpId' => $atp->id]) }}" method="POST" onsubmit="return confirm('Hapus butir TP {{ $atp->kode_tp }} ini?')" style="margin:0;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Hapus Butir TP">
                        <i class="bi bi-trash" style="font-size:15px;"></i>
                      </button>
                    </form>
                  </td>
                @endif
              </tr>
            @empty
              <tr>
                <td colspan="{{ ($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka) ? 8 : 7 }}" style="text-align:center; padding:36px; color:#64748b;">
                  <i class="bi bi-card-checklist" style="font-size:32px; color:#cbd5e1; display:block; margin-bottom:8px;"></i>
                  Belum ada butir Tujuan Pembelajaran (TP) yang dimasukkan untuk mata pelajaran ini.
                  @if($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka)
                    <div style="margin-top:10px;">
                      <button type="button" class="ak-btn ak-btn-primary ak-btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahAtp">
                        <i class="bi bi-plus-lg me-1"></i> Mulai Rumuskan TP Pertama
                      </button>
                    </div>
                  @endif
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Modal Tambah Butir ATP --}}
  @if($activePerangkat && ($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka))
  <div class="modal fade" id="modalTambahAtp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius:14px;">
        <form action="{{ route('akademik.perangkat.atp.store', $activePerangkat->id) }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" style="font-weight:800; font-size:16px;">
              <i class="bi bi-plus-circle text-primary me-1"></i> Tambah Butir Alur Tujuan Pembelajaran (ATP)
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" style="padding:20px;">
            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:12px; margin-bottom:14px;">
              <div>
                <label class="ak-form-label">Urutan Alur <span class="text-danger">*</span></label>
                <input type="number" name="urutan" class="ak-input" value="{{ $atpItems->count() + 1 }}" min="1" required>
              </div>
              <div>
                <label class="ak-form-label">Kode TP <span class="text-danger">*</span></label>
                <input type="text" name="kode_tp" class="ak-input" value="TP 1.{{ $atpItems->count() + 1 }}" placeholder="Misal: TP 1.1" required>
              </div>
              <div>
                <label class="ak-form-label">Alokasi JP <span class="text-danger">*</span></label>
                <input type="number" name="alokasi_jp" class="ak-input" value="4" min="1" max="40" required>
              </div>
            </div>

            <div style="margin-bottom:14px;">
              <label class="ak-form-label">Elemen Capaian Pembelajaran (CP)</label>
              <input type="text" name="elemen_cp" class="ak-input" placeholder="Misal: Proses Bisnis, Pemrograman Dasar, dll">
            </div>

            <div style="margin-bottom:14px;">
              <label class="ak-form-label">Rumusan Tujuan Pembelajaran (TP) <span class="text-danger">*</span></label>
              <textarea name="tujuan_pembelajaran" rows="3" class="ak-textarea" placeholder="Peserta didik mampu memahami dan mendemonstrasikan..." required></textarea>
            </div>

            <div style="margin-bottom:14px;">
              <label class="ak-form-label">Materi Pokok / Lingkup Pembelajaran <span class="text-danger">*</span></label>
              <textarea name="materi_pokok" rows="2" class="ak-textarea" placeholder="Topik dan materi inti yang dipelajari..." required></textarea>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
              <div>
                <label class="ak-form-label">Dimensi Profil Pelajar Pancasila</label>
                <input type="text" name="profil_pancasila" class="ak-input" value="Mandiri, Bernalar Kritis, Gotong Royong" placeholder="Pilih dimensi profil">
              </div>
              <div>
                <label class="ak-form-label">Rencana Asesmen Awal / Formatif</label>
                <input type="text" name="asesmen_rencana" class="ak-input" placeholder="Misal: Tes Lisan, Lembar Observasi Praktik">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="ak-btn ak-btn-primary">Simpan Butir TP</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif

@else
  <div class="akademik-card" style="padding:40px 20px; text-align:center; color:#64748b;">
    <i class="bi bi-folder-x" style="font-size:36px; color:#cbd5e1; display:block; margin-bottom:12px;"></i>
    <div style="font-weight:700; font-size:15px; color:var(--ak-dark); margin-bottom:4px;">Belum Ada Folder Perangkat Ajar</div>
    <div style="font-size:12.5px;">Silakan buat folder perangkat ajar terlebih dahulu menggunakan tombol di atas.</div>
  </div>
@endif

@endsection
