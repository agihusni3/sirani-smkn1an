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
    <button type="button" class="ak-btn ak-btn-primary" onclick="toggleFormAtp()">
      <i class="bi bi-pencil-square me-1"></i>
      <span id="btnTeksTambahTp">{{ $atpItems->isEmpty() ? 'Tutup Formulir TP' : 'Tambah Butir TP' }}</span>
    </button>
  @endif
</div>

@include('dcc.akademik.perangkat.partials.selector')

@if($activePerangkat)

  {{-- FORM INLINE TAMBAH BUTIR ATP (NON-POPUP) --}}
  @if($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka)
    <div class="akademik-card" id="formCardTambahAtp" style="border: 2px solid #3b82f6; border-radius: 14px; margin-bottom: 22px; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.08); {{ $atpItems->isEmpty() ? '' : 'display: none;' }}">
      <div class="akademik-card-header" style="background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%); border-bottom: 1px solid #dbeafe; display: flex; justify-content: space-between; align-items: center; padding: 14px 20px;">
        <h3 class="akademik-card-title" style="font-weight: 800; font-size: 15px; color: #1e3a8a; margin: 0;">
          <i class="bi bi-plus-circle-fill text-primary me-2"></i>
          <span>Formulir Tambah Butir Alur Tujuan Pembelajaran (ATP)</span>
        </h3>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleFormAtp(false)" title="Tutup Formulir" style="border-radius: 8px; padding: 4px 12px; font-size: 12px; font-weight: 700;">
          <i class="bi bi-x-lg me-1"></i> Tutup
        </button>
      </div>

      <div class="akademik-card-body" style="padding: 20px;">
        <form action="{{ route('akademik.perangkat.atp.store', $activePerangkat->id) }}" method="POST">
          @csrf

          <div style="display: grid; grid-template-columns: 140px 180px 140px 1fr; gap: 14px; margin-bottom: 16px;">
            <div>
              <label class="ak-form-label">Urutan Alur <span class="text-danger">*</span></label>
              <input type="number" name="urutan" id="inputUrutan" class="ak-input" value="{{ $atpItems->count() + 1 }}" min="1" required>
            </div>
            <div>
              <label class="ak-form-label">Kode TP <span class="text-danger">*</span></label>
              <input type="text" name="kode_tp" id="inputKodeTp" class="ak-input" value="TP 1.{{ $atpItems->count() + 1 }}" placeholder="Misal: TP 1.1" required>
            </div>
            <div>
              <label class="ak-form-label">Alokasi JP <span class="text-danger">*</span></label>
              <div style="position: relative;">
                <input type="number" name="alokasi_jp" class="ak-input" value="4" min="1" max="40" required>
                <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 11px; color: #94a3b8; font-weight: 700;">JP</span>
              </div>
            </div>
            <div>
              <label class="ak-form-label">Elemen Capaian Pembelajaran (CP)</label>
              <input type="text" name="elemen_cp" class="ak-input" placeholder="Misal: Proses Bisnis, Pemrograman Dasar, dll">
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
              <label class="ak-form-label">Rumusan Tujuan Pembelajaran (TP) <span class="text-danger">*</span></label>
              <textarea name="tujuan_pembelajaran" rows="3" class="ak-textarea" placeholder="Peserta didik mampu memahami dan mendemonstrasikan..." required></textarea>
            </div>
            <div>
              <label class="ak-form-label">Materi Pokok / Lingkup Pembelajaran <span class="text-danger">*</span></label>
              <textarea name="materi_pokok" rows="3" class="ak-textarea" placeholder="Topik dan materi inti yang dipelajari..." required></textarea>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
            <div>
              <label class="ak-form-label">Dimensi Profil Pelajar Pancasila</label>
              <input type="text" name="profil_pancasila" class="ak-input" value="Mandiri, Bernalar Kritis, Gotong Royong" placeholder="Pilih dimensi profil">
            </div>
            <div>
              <label class="ak-form-label">Rencana Asesmen Awal / Formatif</label>
              <input type="text" name="asesmen_rencana" class="ak-input" placeholder="Misal: Tes Lisan, Lembar Observasi Praktik">
            </div>
          </div>

          <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 16px;">
            <button type="button" class="ak-btn ak-btn-secondary" onclick="toggleFormAtp(false)">
              <i class="bi bi-x"></i> Batal / Sembunyikan
            </button>
            <button type="submit" class="ak-btn ak-btn-primary" style="padding: 9px 22px;">
              <i class="bi bi-check2-circle me-1"></i> Simpan Butir TP
            </button>
          </div>
        </form>
      </div>
    </div>
  @endif

  {{-- Tabel Alur Tujuan Pembelajaran (ATP) --}}
  <div class="akademik-card" style="border-top:4px solid var(--ak-primary);">
    <div class="akademik-card-header" style="background:#f8fafc; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; padding:12px 20px;">
      <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <h3 class="akademik-card-title" style="font-size:15px; margin:0;">
          <i class="bi bi-table text-primary me-2"></i>
          Matriks Alur Tujuan Pembelajaran (ATP)
        </h3>
        <span class="ak-badge ak-badge-primary" style="font-size:11.5px; font-weight:800;">
          Fase {{ $activePerangkat->fase }}
        </span>
      </div>

      <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
        <div style="display:flex; align-items:center; gap:10px; font-size:12.5px;">
          <span style="color:#64748b;">
            Beban ATP: <strong style="color:var(--ak-primary); font-size:14px; font-weight:800;">{{ $totalJp }} JP</strong>
          </span>
          <span style="color:#cbd5e1;">&bull;</span>
          <span style="color:#64748b;">
            Jumlah: <strong style="color:#059669; font-size:14px; font-weight:800;">{{ $atpItems->count() }} Tujuan</strong>
          </span>
        </div>
      </div>
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
                      <button type="button" class="ak-btn ak-btn-primary ak-btn-sm" onclick="toggleFormAtp(true)">
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

@else
  <div class="akademik-card" style="padding:40px 20px; text-align:center; color:#64748b;">
    <i class="bi bi-folder-x" style="font-size:36px; color:#cbd5e1; display:block; margin-bottom:12px;"></i>
    <div style="font-weight:700; font-size:15px; color:var(--ak-dark); margin-bottom:4px;">Belum Ada Folder Perangkat Ajar</div>
    <div style="font-size:12.5px;">Silakan buat folder perangkat ajar terlebih dahulu menggunakan tombol di atas.</div>
  </div>
@endif

<script>
  function toggleFormAtp(show) {
    const formEl = document.getElementById('formCardTambahAtp');
    if (!formEl) return;
    if (show === undefined) {
      show = (formEl.style.display === 'none' || formEl.style.display === '');
    }
    formEl.style.display = show ? 'block' : 'none';

    const btnTeks = document.getElementById('btnTeksTambahTp');
    if (btnTeks) {
      btnTeks.innerText = show ? 'Tutup Formulir TP' : 'Tambah Butir TP';
    }

    if (show) {
      formEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
      const targetInput = document.getElementById('inputKodeTp');
      if (targetInput) setTimeout(() => targetInput.focus(), 300);
    }
  }
</script>
@endsection
