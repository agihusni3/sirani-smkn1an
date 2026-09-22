@extends('dcc.akademik.layout')

@section('title', 'Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) — Perangkat Pembelajaran')
@section('breadcrumb')
  <a href="{{ route('akademik.perangkat.kktp') }}">Perangkat Pembelajaran</a>
  <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
  <span>Kriteria Ketercapaian (KKTP)</span>
@endsection

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">
      <i class="bi bi-speedometer2 text-primary me-2"></i>
      5. Kriteria Ketercapaian Tujuan Pembelajaran (KKTP)
    </h1>
    <div class="akademik-page-desc">
      Penetapan kriteria ketuntasan tujuan pembelajaran dan tindak lanjut remedial.
    </div>
  </div>

  @if($activePerangkat && ($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka))
    <button type="button" class="ak-btn ak-btn-primary" onclick="toggleFormKktp()">
      <i class="bi bi-plus-lg me-1"></i>
      <span id="btnTeksTambahKktp">{{ $kktpItems->isEmpty() ? 'Tutup Formulir' : 'Atur Kriteria KKTP' }}</span>
    </button>
  @endif
</div>

@include('dcc.akademik.perangkat.partials.selector')

@if($activePerangkat)
  {{-- Ringkasan Interval Nilai Resmi (Compact Strip) --}}
  <div class="akademik-card" style="margin-bottom: 16px; padding: 10px 18px; background: #f8fafc; border: 1px solid #e2e8f0;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
      <div style="font-size: 12.5px; font-weight: 800; color: var(--ak-dark); display: flex; align-items: center; gap: 6px;">
        <i class="bi bi-sliders text-primary"></i> Standar Interval Nilai:
      </div>
      <div style="display: flex; gap: 8px; flex-wrap: wrap; font-size: 11.5px;">
        <span style="background: #fee2e2; color: #991b1b; padding: 3px 10px; border-radius: 6px; font-weight: 700; border: 1px solid #fca5a5;">
          0 – 60: Perlu Bimbingan
        </span>
        <span style="background: #fef3c7; color: #92400e; padding: 3px 10px; border-radius: 6px; font-weight: 700; border: 1px solid #fde68a;">
          61 – 74: Cukup (Remedial)
        </span>
        <span style="background: #ecfdf5; color: #065f46; padding: 3px 10px; border-radius: 6px; font-weight: 700; border: 1px solid #a7f3d0;">
          75 – 85: Tuntas Mandiri
        </span>
        <span style="background: #eff6ff; color: #1e40af; padding: 3px 10px; border-radius: 6px; font-weight: 700; border: 1px solid #bfdbfe;">
          86 – 100: Mahir (Pengayaan)
        </span>
      </div>
    </div>
  </div>

  {{-- FORM INLINE ATUR KKTP (NON-POPUP) --}}
  @if($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka)
    <div class="akademik-card" id="formCardTambahKktp" style="border: 2px solid #3b82f6; border-radius: 14px; margin-bottom: 22px; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.08); {{ $kktpItems->isEmpty() ? '' : 'display: none;' }}">
      <div class="akademik-card-header" style="background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%); border-bottom: 1px solid #dbeafe; display: flex; justify-content: space-between; align-items: center; padding: 14px 20px;">
        <h3 class="akademik-card-title" style="font-weight: 800; font-size: 15px; color: #1e3a8a; margin: 0;">
          <i class="bi bi-speedometer text-primary me-2"></i>
          <span>Formulir Kriteria Ketercapaian (KKTP) Baru</span>
        </h3>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleFormKktp(false)" title="Tutup Formulir" style="border-radius: 8px; padding: 4px 12px; font-size: 12px; font-weight: 700;">
          <i class="bi bi-x-lg me-1"></i> Tutup
        </button>
      </div>

      <div class="akademik-card-body" style="padding: 20px;">
        <form action="{{ route('akademik.perangkat.kktp.store', $activePerangkat->id) }}" method="POST">
          @csrf
          <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
              <label class="ak-form-label">Tautkan ke Butir TP</label>
              <select name="atp_item_id" class="ak-select">
                <option value="">-- Kriteria Seluruh TP (Umum Mata Pelajaran) --</option>
                @foreach($atpItems as $atp)
                  <option value="{{ $atp->id }}">[{{ $atp->kode_tp }}] {{ Str::limit($atp->tujuan_pembelajaran, 65) }}</option>
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

          <div style="margin-bottom: 16px;">
            <label class="ak-form-label">Deskripsi Kriteria Peserta Didik Dinyatakan Tuntas <span class="text-danger">*</span></label>
            <textarea name="keterangan_tuntas" rows="3" class="ak-textarea" placeholder="Peserta didik mencapai nilai minimal 75 dan mampu menyelesaikan seluruh jobsheet praktik..." required></textarea>
          </div>

          <div style="margin-bottom: 20px;">
            <label class="ak-form-label">Tindak Lanjut Bagi yang Belum Tuntas (Remedial)</label>
            <textarea name="keterangan_remedial" rows="2" class="ak-textarea" placeholder="Bimbingan perorangan, penugasan alternatif, dan pengujian ulang aspek yang belum tuntas..."></textarea>
          </div>

          <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 16px;">
            <button type="button" class="ak-btn ak-btn-secondary" onclick="toggleFormKktp(false)">
              <i class="bi bi-x"></i> Batal / Sembunyikan
            </button>
            <button type="submit" class="ak-btn ak-btn-primary" style="padding: 9px 22px;">
              <i class="bi bi-check2-circle me-1"></i> Simpan Kriteria KKTP
            </button>
          </div>
        </form>
      </div>
    </div>
  @endif

  {{-- Tabel Rincian KKTP per TP --}}
  <div class="akademik-card">
    <div class="akademik-card-header" style="background: #f8fafc; padding: 14px 20px;">
      <h3 class="akademik-card-title" style="font-size: 15px; margin: 0;">
        <i class="bi bi-check2-square text-primary me-2"></i>
        <span>Daftar Kriteria Ketuntasan Tujuan Pembelajaran</span>
        <span class="ak-badge ak-badge-primary" style="font-size: 11px; margin-left: 6px;">{{ $kktpItems->count() }} Kriteria</span>
      </h3>
    </div>

    <div class="akademik-card-body" style="padding: 0;">
      <div class="akademik-table-wrap">
        <table class="akademik-table">
          <thead>
            <tr>
              <th style="width: 40px; text-align: center;">No</th>
              <th style="width: 120px;">Tautan TP</th>
              <th style="width: 130px;">Pendekatan</th>
              <th>Kriteria Ketuntasan</th>
              <th style="width: 280px;">Tindak Lanjut Remedial</th>
              @if($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka)
                <th style="width: 60px; text-align: center;">Aksi</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @forelse($kktpItems as $idx => $kktp)
              <tr>
                <td style="text-align: center; font-weight: 900; color: #64748b;">
                  {{ $idx + 1 }}
                </td>
                <td>
                  @if($kktp->atpItem)
                    <span class="ak-badge ak-badge-primary" style="font-weight: 800; font-size: 11px;">
                      {{ $kktp->atpItem->kode_tp }}
                    </span>
                  @else
                    <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 11px;">Umum Mapel</span>
                  @endif
                </td>
                <td>
                  <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-size: 11px; text-transform: uppercase;">
                    {{ str_replace('_', ' ', $kktp->pendekatan) }}
                  </span>
                </td>
                <td style="font-size: 12.5px; line-height: 1.55; color: var(--ak-dark); font-weight: 600;">
                  {{ $kktp->keterangan_tuntas }}
                </td>
                <td style="font-size: 12px; color: #64748b; line-height: 1.45;">
                  {{ $kktp->keterangan_remedial ?: '—' }}
                </td>
                @if($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka)
                  <td style="text-align: center;">
                    <form action="{{ route('akademik.perangkat.kktp.destroy', ['id' => $activePerangkat->id, 'kktpId' => $kktp->id]) }}" method="POST" onsubmit="return confirm('Hapus kriteria KKTP ini?')" style="margin: 0;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Hapus Kriteria">
                        <i class="bi bi-trash" style="font-size: 15px;"></i>
                      </button>
                    </form>
                  </td>
                @endif
              </tr>
            @empty
              <tr>
                <td colspan="{{ ($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka) ? 6 : 5 }}" style="text-align: center; padding: 36px; color: #64748b;">
                  <i class="bi bi-speedometer" style="font-size: 32px; color: #cbd5e1; display: block; margin-bottom: 8px;"></i>
                  Belum ada kriteria KKTP spesifik yang ditambahkan. Gunakan standar interval di atas atau buat kriteria baru.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

@else
  <div class="akademik-card" style="padding: 40px 20px; text-align: center; color: #64748b;">
    <i class="bi bi-speedometer2" style="font-size: 36px; color: #cbd5e1; display: block; margin-bottom: 12px;"></i>
    <div style="font-weight: 700; font-size: 15px; color: var(--ak-dark); margin-bottom: 4px;">Belum Ada Folder Perangkat Ajar</div>
    <div style="font-size: 12.5px;">Silakan buat folder perangkat ajar terlebih dahulu menggunakan menu di atas.</div>
  </div>
@endif

<script>
  function toggleFormKktp(show) {
    const formEl = document.getElementById('formCardTambahKktp');
    if (!formEl) return;
    if (show === undefined) {
      show = (formEl.style.display === 'none' || formEl.style.display === '');
    }
    formEl.style.display = show ? 'block' : 'none';

    const btnTeks = document.getElementById('btnTeksTambahKktp');
    if (btnTeks) {
      btnTeks.innerText = show ? 'Tutup Formulir' : 'Atur Kriteria KKTP';
    }

    if (show) {
      formEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }
</script>
@endsection
