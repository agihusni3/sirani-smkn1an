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
      4. Modul Ajar (RPP Merdeka), LKPD &amp; Jobsheet Praktik
    </h1>
    <div class="akademik-page-desc">
      Penyusunan modul ajar kurikulum merdeka berbasis Teaching Factory / PjBL, lembar kerja siswa (LKPD), jobsheet bengkel/lab, dan media ajar.
    </div>
  </div>

  @if($activePerangkat && ($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka))
    <button type="button" class="ak-btn ak-btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahModul">
      <i class="bi bi-plus-lg me-1"></i>
      <span>Tambah Modul Ajar</span>
    </button>
  @endif
</div>

@include('dcc.akademik.perangkat.partials.selector')

@if($activePerangkat)
  {{-- Header Daftar Modul --}}
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
    <div style="font-size:15px; font-weight:800; color:var(--ak-dark); display:flex; align-items:center; gap:8px;">
      <i class="bi bi-collection text-primary"></i>
      Daftar Modul Ajar &amp; LKPD
      <span class="badge" style="background:#e0f2fe; color:#0369a1; font-size:12px; font-weight:700;">
        {{ $modulAjars->count() }} Modul
      </span>
    </div>
  </div>

  {{-- Daftar Modul Ajar Grid --}}
  <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap:18px; margin-bottom:24px;">
    @forelse($modulAjars as $ma)
      <div class="akademik-card" style="display:flex; flex-direction:column; justify-content:space-between; border-top:4px solid var(--ak-primary);">
        <div class="akademik-card-header" style="background:#f8fafc; padding:12px 18px;">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
            <div>
              <span class="badge" style="background:#e0e7ff; color:#3730a3; font-weight:800; font-size:11px;">
                Pertemuan {{ $ma->pertemuan_ke_mulai }} s/d {{ $ma->pertemuan_ke_selesai }} ({{ $ma->alokasi_jp }} JP)
              </span>
              <h3 style="font-size:15px; font-weight:800; color:var(--ak-dark); margin:6px 0 0; line-height:1.35;">
                {{ $ma->judul_modul }}
              </h3>
            </div>
            @if($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka)
              <form action="{{ route('akademik.perangkat.modul.destroy', ['id' => $activePerangkat->id, 'modulId' => $ma->id]) }}" method="POST" onsubmit="return confirm('Hapus modul ajar {{ $ma->judul_modul }}?')" style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Hapus Modul">
                  <i class="bi bi-trash" style="font-size:15px;"></i>
                </button>
              </form>
            @endif
          </div>
        </div>

        <div class="akademik-card-body" style="padding:16px 18px; font-size:12.5px; flex:1;">
          @if($ma->atpItem)
            <div style="margin-bottom:10px; padding:6px 10px; background:#eff6ff; border-radius:6px; border-left:3px solid #3b82f6;">
              <span style="font-weight:800; color:#1d4ed8;">{{ $ma->atpItem->kode_tp }}:</span>
              <span style="color:#1e3a8a;">{{ Str::limit($ma->atpItem->tujuan_pembelajaran, 90) }}</span>
            </div>
          @endif

          <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:10px;">
            @if($ma->model_pembelajaran)
              <span class="ak-badge ak-badge-secondary" style="font-size:11px;">
                <i class="bi bi-cpu me-1"></i> {{ $ma->model_pembelajaran }}
              </span>
            @endif
            @if($ma->metode_pembelajaran)
              <span class="ak-badge ak-badge-info" style="font-size:11px;">
                <i class="bi bi-tools me-1"></i> {{ $ma->metode_pembelajaran }}
              </span>
            @endif
          </div>

          @if($ma->pemahaman_bermakna)
            <div style="margin-bottom:8px;">
              <strong style="color:var(--ak-dark); font-size:12px;">Pemahaman Bermakna:</strong>
              <div style="color:#475569; font-size:12px; margin-top:2px;">{{ Str::limit($ma->pemahaman_bermakna, 120) }}</div>
            </div>
          @endif

          @if($ma->pertanyaan_pemantik)
            <div style="margin-bottom:10px;">
              <strong style="color:var(--ak-dark); font-size:12px;">Pertanyaan Pemantik:</strong>
              <div style="color:#475569; font-size:12px; font-style:italic; margin-top:2px;">"{{ Str::limit($ma->pertanyaan_pemantik, 120) }}"</div>
            </div>
          @endif

          {{-- Lampiran Dokumen & Media --}}
          <div style="border-top:1px solid #e2e8f0; padding-top:12px; margin-top:12px; display:flex; flex-direction:column; gap:6px;">
            @if($ma->file_modul_pdf)
              <a href="{{ Storage::url($ma->file_modul_pdf) }}" target="_blank" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:11.5px; text-decoration:none;">
                <i class="bi bi-file-earmark-pdf text-danger me-1"></i> Buka Dokumen Modul Ajar (PDF)
              </a>
            @endif

            @if($ma->file_lkpd_pdf)
              <a href="{{ Storage::url($ma->file_lkpd_pdf) }}" target="_blank" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:11.5px; text-decoration:none;">
                <i class="bi bi-file-earmark-text text-primary me-1"></i> Buka Lembar Kerja (LKPD)
              </a>
            @endif

            @if($ma->file_jobsheet_praktik)
              <a href="{{ Storage::url($ma->file_jobsheet_praktik) }}" target="_blank" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:11.5px; text-decoration:none;">
                <i class="bi bi-wrench text-warning me-1"></i> Jobsheet Praktik Bengkel/Lab
              </a>
            @endif

            @if($ma->link_media_pembelajaran)
              <a href="{{ $ma->link_media_pembelajaran }}" target="_blank" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:11.5px; text-decoration:none; color:#0284c7;">
                <i class="bi bi-link-45deg me-1"></i> Tautan Media Belajar Interaktif
              </a>
            @endif

            @if(!$ma->file_modul_pdf && !$ma->file_lkpd_pdf && !$ma->link_media_pembelajaran)
              <span style="color:#94a3b8; font-style:italic; font-size:11.5px;">Belum ada file lampiran PDF / link media.</span>
            @endif
          </div>
        </div>
      </div>
    @empty
      <div class="akademik-card" style="grid-column: 1 / -1; padding:40px 20px; text-align:center; color:#64748b;">
        <i class="bi bi-journal-plus" style="font-size:36px; color:#cbd5e1; display:block; margin-bottom:10px;"></i>
        <div style="font-weight:700; font-size:15px; color:var(--ak-dark); margin-bottom:4px;">Belum Ada Modul Ajar / LKPD</div>
        <div style="font-size:12.5px; max-width:500px; margin:0 auto 14px;">
          Setiap modul ajar mewakili 1 hingga beberapa pertemuan KBM yang memuat skenario pembelajaran, LKPD, dan jobsheet praktik kejuruan.
        </div>
        @if($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka)
          <button type="button" class="ak-btn ak-btn-primary ak-btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahModul">
            <i class="bi bi-plus-lg me-1"></i> Susun Modul Pertama Sekarang
          </button>
        @endif
      </div>
    @endforelse
  </div>

  {{-- Modal Tambah Modul Ajar --}}
  @if($activePerangkat && ($activePerangkat->guru_id == auth()->user()->guru_id || $isAdminOrWaka))
  <div class="modal fade" id="modalTambahModul" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius:14px;">
        <form action="{{ route('akademik.perangkat.modul.store', $activePerangkat->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" style="font-weight:800; font-size:16px;">
              <i class="bi bi-plus-circle text-primary me-1"></i> Tambah Modul Ajar / LKPD Baru
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" style="padding:20px;">
            <div style="margin-bottom:14px;">
              <label class="ak-form-label">Judul Modul Ajar / Topik KBM <span class="text-danger">*</span></label>
              <input type="text" name="judul_modul" class="ak-input" placeholder="Contoh: Modul 1 - Perakitan Sistem Transmisi Sepeda Motor" required>
            </div>

            <div style="display:grid; grid-template-columns: 2fr 1fr 1fr; gap:12px; margin-bottom:14px;">
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
                <div style="display:flex; align-items:center; gap:6px;">
                  <input type="number" name="pertemuan_ke_mulai" class="ak-input" value="1" min="1" style="text-align:center;">
                  <span>s/d</span>
                  <input type="number" name="pertemuan_ke_selesai" class="ak-input" value="2" min="1" style="text-align:center;">
                </div>
              </div>
              <div>
                <label class="ak-form-label">Alokasi JP</label>
                <input type="number" name="alokasi_jp" class="ak-input" value="4" min="1" max="40" style="text-align:center;">
              </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:14px;">
              <div>
                <label class="ak-form-label">Model Pembelajaran</label>
                <input type="text" name="model_pembelajaran" class="ak-input" value="Project-Based Learning (PjBL)" placeholder="Misal: PjBL, Problem-Based Learning, TEFA">
              </div>
              <div>
                <label class="ak-form-label">Metode Pembelajaran</label>
                <input type="text" name="metode_pembelajaran" class="ak-input" value="Praktik Bengkel / Demonstrasi" placeholder="Misal: Praktik Lab, Diskusi Kelompok">
              </div>
            </div>

            <div style="margin-bottom:14px;">
              <label class="ak-form-label">Pemahaman Bermakna (Deep Understanding)</label>
              <textarea name="pemahaman_bermakna" rows="2" class="ak-textarea" placeholder="Intisari konsep yang akan diingat siswa seumur hidup..."></textarea>
            </div>

            <div style="margin-bottom:14px;">
              <label class="ak-form-label">Pertanyaan Pemantik (Essential Question)</label>
              <textarea name="pertanyaan_pemantik" rows="2" class="ak-textarea" placeholder="Pertanyaan pembuka yang memicu rasa ingin tahu siswa..."></textarea>
            </div>

            <div style="border-top:1px solid #e2e8f0; padding-top:14px; margin-top:14px;">
              <div style="font-weight:800; font-size:13px; color:var(--ak-dark); margin-bottom:10px;">
                <i class="bi bi-paperclip me-1 text-primary"></i> Unggah Berkas Dokumen (PDF, Maks 20 MB)
              </div>
              <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:12px;">
                <div>
                  <label class="ak-form-label">File Modul Ajar (PDF)</label>
                  <input type="file" name="file_modul_pdf" class="ak-input" accept=".pdf">
                </div>
                <div>
                  <label class="ak-form-label">File Lembar Kerja Siswa (LKPD PDF)</label>
                  <input type="file" name="file_lkpd_pdf" class="ak-input" accept=".pdf">
                </div>
              </div>

              <div>
                <label class="ak-form-label">Tautan / Link Media Pembelajaran (YouTube / Google Drive / Slide)</label>
                <input type="url" name="link_media_pembelajaran" class="ak-input" placeholder="https://youtube.com/watch?v=... atau https://drive.google.com/...">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="ak-btn ak-btn-primary">Simpan Modul Ajar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif

@else
  <div class="akademik-card" style="padding:40px 20px; text-align:center; color:#64748b;">
    <i class="bi bi-journal-x" style="font-size:36px; color:#cbd5e1; display:block; margin-bottom:12px;"></i>
    <div style="font-weight:700; font-size:15px; color:var(--ak-dark); margin-bottom:4px;">Belum Ada Folder Perangkat Ajar</div>
    <div style="font-size:12.5px;">Silakan buat folder perangkat ajar terlebih dahulu menggunakan tombol di atas.</div>
  </div>
@endif

@endsection
