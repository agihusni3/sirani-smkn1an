@extends('dcc.akademik.layout')

@section('title', 'Modul Ajar & LKPD — Perangkat Pembelajaran')
@section('breadcrumb')
  <a href="{{ route('akademik.perangkat.modul') }}">Perangkat Pembelajaran</a>
  <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
  <span>Modul Ajar &amp; LKPD</span>
@endsection

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">
      <i class="bi bi-journal-richtext text-primary me-2"></i>
      4. Modul Ajar &amp; LKPD
    </h1>
    <div class="akademik-page-desc">
      Penyusunan modul ajar, lembar kerja siswa (LKPD), dan jobsheet praktik kejuruan.
    </div>
  </div>

  @if($activePerangkat && ($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka))
    <button type="button" class="ak-btn ak-btn-primary" onclick="toggleFormModul()">
      <i class="bi bi-plus-lg me-1"></i>
      <span id="btnTeksTambahModul">{{ $modulAjars->isEmpty() ? 'Tutup Formulir' : 'Tambah Modul Ajar' }}</span>
    </button>
  @endif
</div>

@include('dcc.akademik.perangkat.partials.selector')

@if($activePerangkat)

  {{-- FORM INLINE TAMBAH MODUL AJAR (NON-POPUP) --}}
  @if($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka)
    <div class="akademik-card" id="formCardTambahModul" style="border: 2px solid #3b82f6; border-radius: 14px; margin-bottom: 22px; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.08); {{ $modulAjars->isEmpty() ? '' : 'display: none;' }}">
      <div class="akademik-card-header" style="background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%); border-bottom: 1px solid #dbeafe; display: flex; justify-content: space-between; align-items: center; padding: 14px 20px;">
        <h3 class="akademik-card-title" style="font-weight: 800; font-size: 15px; color: #1e3a8a; margin: 0;">
          <i class="bi bi-journal-plus text-primary me-2"></i>
          <span>Formulir Modul Ajar &amp; LKPD Baru</span>
        </h3>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleFormModul(false)" title="Tutup Formulir" style="border-radius: 8px; padding: 4px 12px; font-size: 12px; font-weight: 700;">
          <i class="bi bi-x-lg me-1"></i> Tutup
        </button>
      </div>

      <div class="akademik-card-body" style="padding: 20px;">
        <form action="{{ route('akademik.perangkat.modul.store', $activePerangkat->id) }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div style="margin-bottom: 16px;">
            <label class="ak-form-label">Judul Modul Ajar / Topik KBM <span class="text-danger">*</span></label>
            <input type="text" name="judul_modul" id="inputJudulModul" class="ak-input" placeholder="Contoh: Modul 1 - Perakitan Sistem Transmisi Sepeda Motor" required>
          </div>

          <div style="display: grid; grid-template-columns: 2fr 1fr 120px; gap: 14px; margin-bottom: 16px;">
            <div>
              <label class="ak-form-label">Tautkan ke Butir TP (Opsional)</label>
              <select name="atp_item_id" class="ak-select">
                <option value="">-- Pilih TP Terkait --</option>
                @foreach($atpItems as $atp)
                  <option value="{{ $atp->id }}">[{{ $atp->kode_tp }}] {{ Str::limit($atp->tujuan_pembelajaran, 50) }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="ak-form-label">Pertemuan Ke-</label>
              <div style="display: flex; align-items: center; gap: 6px;">
                <input type="number" name="pertemuan_ke_mulai" class="ak-input" value="1" min="1" style="text-align: center;">
                <span style="color: #64748b; font-size: 12px;">s/d</span>
                <input type="number" name="pertemuan_ke_selesai" class="ak-input" value="2" min="1" style="text-align: center;">
              </div>
            </div>
            <div>
              <label class="ak-form-label">Alokasi JP</label>
              <div style="position: relative;">
                <input type="number" name="alokasi_jp" class="ak-input" value="4" min="1" max="40" style="text-align: center;">
                <span style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); font-size: 11px; color: #94a3b8; font-weight: 700;">JP</span>
              </div>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
              <label class="ak-form-label">Model Pembelajaran</label>
              <input type="text" name="model_pembelajaran" class="ak-input" value="Project-Based Learning (PjBL)" placeholder="Misal: PjBL, Problem-Based Learning, TEFA">
            </div>
            <div>
              <label class="ak-form-label">Metode Pembelajaran</label>
              <input type="text" name="metode_pembelajaran" class="ak-input" value="Praktik Bengkel / Demonstrasi" placeholder="Misal: Praktik Lab, Diskusi Kelompok">
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div>
              <label class="ak-form-label">Pemahaman Bermakna</label>
              <textarea name="pemahaman_bermakna" rows="2" class="ak-textarea" placeholder="Intisari konsep yang dikuasai siswa..."></textarea>
            </div>
            <div>
              <label class="ak-form-label">Pertanyaan Pemantik</label>
              <textarea name="pertanyaan_pemantik" rows="2" class="ak-textarea" placeholder="Pertanyaan pembuka yang memicu rasa ingin tahu siswa..."></textarea>
            </div>
          </div>

          <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;">
            <div style="font-weight: 800; font-size: 12.5px; color: var(--ak-dark); margin-bottom: 10px;">
              <i class="bi bi-paperclip me-1 text-primary"></i> Lampiran Berkas &amp; Tautan Media
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 12px;">
              <div>
                <label class="ak-form-label">Dokumen Modul Ajar (PDF)</label>
                <input type="file" name="file_modul_pdf" class="ak-input" accept=".pdf">
              </div>
              <div>
                <label class="ak-form-label">Lembar Kerja Siswa (LKPD PDF)</label>
                <input type="file" name="file_lkpd_pdf" class="ak-input" accept=".pdf">
              </div>
            </div>

            <div>
              <label class="ak-form-label">Tautan / Link Media Pembelajaran (YouTube / Google Drive)</label>
              <input type="url" name="link_media_pembelajaran" class="ak-input" placeholder="https://youtube.com/watch?v=... atau https://drive.google.com/...">
            </div>
          </div>

          <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 14px;">
            <button type="button" class="ak-btn ak-btn-secondary" onclick="toggleFormModul(false)">
              <i class="bi bi-x"></i> Batal / Sembunyikan
            </button>
            <button type="submit" class="ak-btn ak-btn-primary" style="padding: 9px 22px;">
              <i class="bi bi-check2-circle me-1"></i> Simpan Modul Ajar
            </button>
          </div>
        </form>
      </div>
    </div>
  @endif

  {{-- Daftar Modul Ajar Grid --}}
  @if($modulAjars->isNotEmpty())
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
      <div style="font-size: 14px; font-weight: 800; color: var(--ak-dark); display: flex; align-items: center; gap: 8px;">
        <i class="bi bi-collection text-primary"></i>
        <span>Daftar Modul Ajar &amp; LKPD</span>
        <span class="ak-badge ak-badge-primary" style="font-size: 11px;">{{ $modulAjars->count() }} Modul</span>
      </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 18px; margin-bottom: 24px;">
      @foreach($modulAjars as $ma)
        <div class="akademik-card" style="display: flex; flex-direction: column; justify-content: space-between; border-top: 4px solid var(--ak-primary);">
          <div class="akademik-card-header" style="background: #f8fafc; padding: 12px 18px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
              <div>
                <span class="badge" style="background: #e0e7ff; color: #3730a3; font-weight: 800; font-size: 11px;">
                  Pertemuan {{ $ma->pertemuan_ke_mulai }} s/d {{ $ma->pertemuan_ke_selesai }} ({{ $ma->alokasi_jp }} JP)
                </span>
                <h3 style="font-size: 15px; font-weight: 800; color: var(--ak-dark); margin: 6px 0 0; line-height: 1.35;">
                  {{ $ma->judul_modul }}
                </h3>
              </div>
              @if($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka)
                <form action="{{ route('akademik.perangkat.modul.destroy', ['id' => $activePerangkat->id, 'modulId' => $ma->id]) }}" method="POST" onsubmit="return confirm('Hapus modul ajar {{ $ma->judul_modul }}?')" style="margin: 0;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Hapus Modul">
                    <i class="bi bi-trash" style="font-size: 15px;"></i>
                  </button>
                </form>
              @endif
            </div>
          </div>

          <div class="akademik-card-body" style="padding: 16px 18px; font-size: 12.5px; flex: 1;">
            @if($ma->atpItem)
              <div style="margin-bottom: 10px; padding: 6px 10px; background: #eff6ff; border-radius: 6px; border-left: 3px solid #3b82f6;">
                <span style="font-weight: 800; color: #1d4ed8;">{{ $ma->atpItem->kode_tp }}:</span>
                <span style="color: #1e3a8a;">{{ Str::limit($ma->atpItem->tujuan_pembelajaran, 85) }}</span>
              </div>
            @endif

            <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 10px;">
              @if($ma->model_pembelajaran)
                <span class="ak-badge ak-badge-secondary" style="font-size: 11px;">
                  <i class="bi bi-cpu me-1"></i> {{ $ma->model_pembelajaran }}
                </span>
              @endif
              @if($ma->metode_pembelajaran)
                <span class="ak-badge ak-badge-info" style="font-size: 11px;">
                  <i class="bi bi-tools me-1"></i> {{ $ma->metode_pembelajaran }}
                </span>
              @endif
            </div>

            @if($ma->pemahaman_bermakna)
              <div style="margin-bottom: 8px;">
                <strong style="color: var(--ak-dark); font-size: 12px;">Pemahaman Bermakna:</strong>
                <div style="color: #475569; font-size: 12px; margin-top: 2px;">{{ Str::limit($ma->pemahaman_bermakna, 110) }}</div>
              </div>
            @endif

            @if($ma->pertanyaan_pemantik)
              <div style="margin-bottom: 10px;">
                <strong style="color: var(--ak-dark); font-size: 12px;">Pertanyaan Pemantik:</strong>
                <div style="color: #475569; font-size: 12px; font-style: italic; margin-top: 2px;">"{{ Str::limit($ma->pertanyaan_pemantik, 110) }}"</div>
              </div>
            @endif

            {{-- Lampiran Dokumen & Media --}}
            <div style="border-top: 1px solid #e2e8f0; padding-top: 12px; margin-top: 12px; display: flex; flex-direction: column; gap: 6px;">
              @if($ma->file_modul_pdf)
                <a href="{{ Storage::url($ma->file_modul_pdf) }}" target="_blank" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size: 11.5px; text-decoration: none;">
                  <i class="bi bi-file-earmark-pdf text-danger me-1"></i> Buka Dokumen Modul (PDF)
                </a>
              @endif

              @if($ma->file_lkpd_pdf)
                <a href="{{ Storage::url($ma->file_lkpd_pdf) }}" target="_blank" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size: 11.5px; text-decoration: none;">
                  <i class="bi bi-file-earmark-text text-primary me-1"></i> Buka Lembar Kerja (LKPD)
                </a>
              @endif

              @if($ma->link_media_pembelajaran)
                <a href="{{ $ma->link_media_pembelajaran }}" target="_blank" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size: 11.5px; text-decoration: none; color: #0284c7;">
                  <i class="bi bi-link-45deg me-1"></i> Tautan Media Pembelajaran
                </a>
              @endif

              @if(!$ma->file_modul_pdf && !$ma->file_lkpd_pdf && !$ma->link_media_pembelajaran)
                <span style="color: #94a3b8; font-style: italic; font-size: 11.5px;">Belum ada lampiran file PDF / tautan media.</span>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    {{-- Empty State Card (Bersih tanpa tombol duplikat) --}}
    <div class="akademik-card" style="padding: 48px 20px; text-align: center; color: #64748b;">
      <i class="bi bi-journal-plus" style="font-size: 40px; color: #cbd5e1; display: block; margin-bottom: 12px;"></i>
      <div style="font-weight: 800; font-size: 15px; color: var(--ak-dark); margin-bottom: 6px;">Belum Ada Modul Ajar / LKPD</div>
      <div style="font-size: 13px; max-width: 460px; margin: 0 auto; color: #64748b;">
        Gunakan formulir di atas untuk menyusun modul ajar dan mengunggah lembar kerja siswa (LKPD).
      </div>
    </div>
  @endif

@else
  <div class="akademik-card" style="padding: 40px 20px; text-align: center; color: #64748b;">
    <i class="bi bi-journal-x" style="font-size: 36px; color: #cbd5e1; display: block; margin-bottom: 12px;"></i>
    <div style="font-weight: 700; font-size: 15px; color: var(--ak-dark); margin-bottom: 4px;">Belum Ada Folder Perangkat Ajar</div>
    <div style="font-size: 12.5px;">Silakan pilih atau buat folder perangkat ajar terlebih dahulu menggunakan menu di atas.</div>
  </div>
@endif

<script>
  function toggleFormModul(show) {
    const formEl = document.getElementById('formCardTambahModul');
    if (!formEl) return;
    if (show === undefined) {
      show = (formEl.style.display === 'none' || formEl.style.display === '');
    }
    formEl.style.display = show ? 'block' : 'none';

    const btnTeks = document.getElementById('btnTeksTambahModul');
    if (btnTeks) {
      btnTeks.innerText = show ? 'Tutup Formulir' : 'Tambah Modul Ajar';
    }

    if (show) {
      formEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
      const targetInput = document.getElementById('inputJudulModul');
      if (targetInput) setTimeout(() => targetInput.focus(), 300);
    }
  }
</script>
@endsection
