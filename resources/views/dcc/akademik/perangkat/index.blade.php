@extends('dcc.akademik.layout')

@section('title', 'Perangkat Pembelajaran Kurikulum Merdeka')
@section('breadcrumb', 'Perangkat Pembelajaran')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Perangkat Pembelajaran &amp; Administrasi Guru</h1>
    <div class="akademik-page-desc">
      Pengelolaan Capaian Pembelajaran (CP BSKAP 032/2024), Alur Tujuan Pembelajaran (ATP), Prota, Promes, Modul Ajar, KKTP, dan Supervisi Pengesahan Resmi Kepala Sekolah.
    </div>
  </div>

  <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
    {{-- Filter Semester --}}
    <form method="GET" action="{{ route('akademik.perangkat.index') }}" style="margin:0; display:flex; gap:8px;">
      @if(request('guru_id')) <input type="hidden" name="guru_id" value="{{ request('guru_id') }}"> @endif
      @if(request('tingkat')) <input type="hidden" name="tingkat" value="{{ request('tingkat') }}"> @endif
      @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
      
      <select name="semester" class="ak-select" onchange="this.form.submit()" style="padding:6px 12px; font-weight:700; font-size:13px; width:auto;">
        <option value="1" {{ $semester == 1 ? 'selected' : '' }}>Semester Ganjil (1)</option>
        <option value="2" {{ $semester == 2 ? 'selected' : '' }}>Semester Genap (2)</option>
      </select>
    </form>

    <button type="button" class="ak-btn ak-btn-primary" data-bs-toggle="modal" data-bs-target="#modalBuatPerangkat">
      <i class="bi bi-plus-lg"></i>
      <span>Buat Perangkat Ajar</span>
    </button>
  </div>
</div>

{{-- Kartu Ringkasan Kepatuhan & Supervisi --}}
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:14px; margin-bottom:24px;">
  <div class="akademik-card" style="padding:16px;">
    <div style="font-size:12px; font-weight:700; color:var(--ak-slate-500); text-transform:uppercase; margin-bottom:6px;">Total Perangkat Ajar</div>
    <div style="font-size:24px; font-weight:900; color:var(--ak-dark);">{{ $stats['total'] }}</div>
    <div style="font-size:11.5px; color:var(--ak-slate-400); margin-top:2px;">Semester {{ $semester == 1 ? 'Ganjil' : 'Genap' }}</div>
  </div>

  <div class="akademik-card" style="padding:16px;">
    <div style="font-size:12px; font-weight:700; color:#059669; text-transform:uppercase; margin-bottom:6px;">Disahkan Kepala Sekolah</div>
    <div style="font-size:24px; font-weight:900; color:#059669;">{{ $stats['disahkan'] }}</div>
    <div style="font-size:11.5px; color:var(--ak-slate-400); margin-top:2px;">Sudah Ber-QR Code Sah</div>
  </div>

  <div class="akademik-card" style="padding:16px;">
    <div style="font-size:12px; font-weight:700; color:#d97706; text-transform:uppercase; margin-bottom:6px;">Menunggu Supervisi</div>
    <div style="font-size:24px; font-weight:900; color:#d97706;">{{ $stats['diajukan'] }}</div>
    <div style="font-size:11.5px; color:var(--ak-slate-400); margin-top:2px;">Perlu Telaah Wakakur/Kepsek</div>
  </div>

  <div class="akademik-card" style="padding:16px;">
    <div style="font-size:12px; font-weight:700; color:#dc2626; text-transform:uppercase; margin-bottom:6px;">Perlu Revisi</div>
    <div style="font-size:24px; font-weight:900; color:#dc2626;">{{ $stats['revisi'] }}</div>
    <div style="font-size:11.5px; color:var(--ak-slate-400); margin-top:2px;">Ada catatan perbaikan</div>
  </div>

  <div class="akademik-card" style="padding:16px;">
    <div style="font-size:12px; font-weight:700; color:var(--ak-slate-500); text-transform:uppercase; margin-bottom:6px;">Draft / Dalam Proses</div>
    <div style="font-size:24px; font-weight:900; color:var(--ak-slate-600);">{{ $stats['draft'] }}</div>
    <div style="font-size:11.5px; color:var(--ak-slate-400); margin-top:2px;">Sedang disusun guru</div>
  </div>
</div>

{{-- Quick Action untuk Guru: Sinkronisasi dari Jadwal SK Mengajar --}}
@if($isGuru && $myDistribusis->isNotEmpty())
<div class="akademik-card" style="margin-bottom:20px; background:#f8fafc; border-left:4px solid #0284c7;">
  <div class="akademik-card-body" style="padding:16px 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
      <div>
        <div style="font-size:14px; font-weight:800; color:var(--ak-dark); margin-bottom:2px;">
          <i class="bi bi-magic text-primary me-1"></i> Pembuatan Cepat dari Tugas Mengajar Anda
        </div>
        <div style="font-size:12px; color:var(--ak-slate-600);">
          Sistem mendeteksi alokasi mata pelajaran yang Anda ampu pada semester ini. Klik untuk langsung membuat folder perangkat ajar:
        </div>
      </div>
      <div style="display:flex; gap:8px; flex-wrap:wrap;">
        @foreach($myDistribusis->unique('mata_pelajaran_id') as $dist)
          @php
            $alreadyExists = $perangkats->first(fn($p) => $p->mata_pelajaran_id == $dist->mata_pelajaran_id && $p->tingkat == ($dist->rombel?->tingkat ?? 'X'));
          @endphp
          @if(!$alreadyExists)
            <form action="{{ route('akademik.perangkat.store') }}" method="POST" style="margin:0;">
              @csrf
              <input type="hidden" name="guru_id" value="{{ auth()->user()->guru_id }}">
              <input type="hidden" name="mata_pelajaran_id" value="{{ $dist->mata_pelajaran_id }}">
              <input type="hidden" name="distribusi_id" value="{{ $dist->id }}">
              <input type="hidden" name="tingkat" value="{{ $dist->rombel?->tingkat ?? 'X' }}">
              <input type="hidden" name="semester" value="{{ $semester }}">
              <button type="submit" class="ak-btn ak-btn-secondary" style="font-size:12px; padding:5px 12px; background:#ffffff;">
                <i class="bi bi-plus-circle text-primary"></i>
                <span>{{ $dist->mataPelajaran?->nama_mapel }} (Kls {{ $dist->rombel?->tingkat }})</span>
              </button>
            </form>
          @endif
        @endforeach
      </div>
    </div>
  </div>
</div>
@endif

{{-- Filter Toolbar --}}
<div class="akademik-card" style="margin-bottom:20px;">
  <div class="akademik-card-body" style="padding:14px 18px;">
    <form method="GET" action="{{ route('akademik.perangkat.index') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center; justify-content:space-between; margin:0;">
      <input type="hidden" name="semester" value="{{ $semester }}">
      
      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; flex:1;">
        <div style="position:relative; min-width:240px;">
          <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:13px;"></i>
          <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari mata pelajaran / guru..." class="ak-input" style="padding-left:34px; font-size:13px; height:38px;">
        </div>

        @if($isAdminOrWaka)
        <select name="guru_id" class="ak-select" style="max-width:200px; height:38px; font-size:13px;">
          <option value="">-- Semua Guru Pengampu --</option>
          @foreach($gurus as $g)
            <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
          @endforeach
        </select>
        @endif

        <select name="tingkat" class="ak-select" style="max-width:130px; height:38px; font-size:13px;">
          <option value="">-- Tingkat --</option>
          <option value="X" {{ request('tingkat') === 'X' ? 'selected' : '' }}>Kelas X (Fase E)</option>
          <option value="XI" {{ request('tingkat') === 'XI' ? 'selected' : '' }}>Kelas XI (Fase F)</option>
          <option value="XII" {{ request('tingkat') === 'XII' ? 'selected' : '' }}>Kelas XII (Fase F)</option>
        </select>

        <select name="status" class="ak-select" style="max-width:160px; height:38px; font-size:13px;">
          <option value="">-- Semua Status --</option>
          <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft Guru</option>
          <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>Menunggu Supervisi</option>
          <option value="perlu_revisi" {{ request('status') === 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
          <option value="disahkan" {{ request('status') === 'disahkan' ? 'selected' : '' }}>Disahkan Kepsek</option>
        </select>

        <button type="submit" class="ak-btn ak-btn-secondary" style="height:38px; padding:0 14px;">
          <span>Filter</span>
        </button>

        @if(request()->anyFilled(['q', 'guru_id', 'tingkat', 'status']))
          <a href="{{ route('akademik.perangkat.index', ['semester' => $semester]) }}" class="ak-btn ak-btn-secondary" style="height:38px; padding:0 12px; color:#ef4444;" title="Reset Filter">
            <i class="bi bi-x-circle"></i>
          </a>
        @endif
      </div>
    </form>
  </div>
</div>

{{-- Tabel Data Perangkat Pembelajaran --}}
<div class="akademik-card">
  <div class="akademik-card-header" style="border-bottom:1px solid var(--ak-slate-200); padding:16px 20px;">
    <h3 class="akademik-card-title">
      <i class="bi bi-folder2-open text-primary"></i>
      <span>Daftar Dokumen Perangkat Pembelajaran Kurikulum Merdeka</span>
    </h3>
    <span class="ak-badge ak-badge-secondary">{{ $perangkats->total() }} Dokumen Terdaftar</span>
  </div>

  <div class="table-responsive">
    <table class="table align-middle" style="margin-bottom:0; font-size:13px; color:#1e293b;">
      <thead style="background:#f8fafc; border-bottom:1.5px solid var(--ak-slate-200); color:#475569; font-weight:700;">
        <tr>
          <th style="width:40px; text-align:center;">#</th>
          <th>Mata Pelajaran &amp; Jenjang</th>
          <th>Guru Pengampu</th>
          <th style="width:200px;">Kelengkapan Komponen</th>
          <th style="width:170px; text-align:center;">Status Dokumen</th>
          <th style="width:170px; text-align:center;">Pengesahan</th>
          <th style="width:140px; text-align:center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($perangkats as $idx => $p)
          @php
            $kel = $p->kelengkapan;
            $badge = $p->status_badge;
          @endphp
          <tr style="border-bottom:1px solid #f1f5f9;">
            <td style="text-align:center; color:#94a3b8; font-weight:600;">{{ $perangkats->firstItem() + $idx }}</td>
            <td>
              <div style="font-weight:800; color:var(--ak-dark); font-size:13.5px;">
                <a href="{{ route('akademik.perangkat.show', $p->id) }}" style="color:inherit; text-decoration:none;" class="hover-underline">
                  {{ $p->mataPelajaran?->nama_mapel }}
                </a>
              </div>
              <div style="display:flex; gap:6px; align-items:center; margin-top:3px; flex-wrap:wrap;">
                <span class="badge" style="background:#e0e7ff; color:#3730a3; font-size:11px; font-weight:700;">
                  Kelas {{ $p->tingkat }} · Fase {{ $p->fase }}
                </span>
                <span style="font-size:11.5px; color:#64748b;">
                  Semester {{ $p->semester == 1 ? 'Ganjil' : 'Genap' }}
                </span>
              </div>
            </td>
            <td>
              <div style="font-weight:700; color:#1e293b;">{{ $p->guru?->nama }}</div>
              <div style="font-size:11px; color:#64748b;">NIP: {{ $p->guru?->nip ?? '-' }}</div>
            </td>
            <td>
              {{-- Indikator Kelengkapan 5 Komponen --}}
              <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:4px;">
                <span style="font-size:11px; font-weight:700; color:#475569;">Progress:</span>
                <span style="font-size:11px; font-weight:800; color:{{ $kel['persen'] == 100 ? '#059669' : ($kel['persen'] >= 60 ? '#d97706' : '#64748b') }};">
                  {{ $kel['persen'] }}%
                </span>
              </div>
              <div class="progress" style="height:6px; background:#e2e8f0; border-radius:3px; margin-bottom:6px;">
                <div class="progress-bar" style="width: {{ $kel['persen'] }}%; background-color: {{ $kel['persen'] == 100 ? '#10b981' : ($kel['persen'] >= 60 ? '#f59e0b' : '#3b82f6') }};"></div>
              </div>
              <div style="display:flex; gap:4px; font-size:10px;">
                <span title="Capaian Pembelajaran (CP)" style="padding:1px 5px; border-radius:3px; font-weight:700; {{ $kel['has_cp'] ? 'background:#ecfdf5; color:#065f46;' : 'background:#f1f5f9; color:#94a3b8;' }}">CP</span>
                <span title="Alur Tujuan Pembelajaran (ATP)" style="padding:1px 5px; border-radius:3px; font-weight:700; {{ $kel['has_atp'] ? 'background:#ecfdf5; color:#065f46;' : 'background:#f1f5f9; color:#94a3b8;' }}">ATP ({{ $p->atpItems->count() }})</span>
                <span title="Rincian Pekan Efektif / Prota Promes" style="padding:1px 5px; border-radius:3px; font-weight:700; {{ $kel['has_rpe'] ? 'background:#ecfdf5; color:#065f46;' : 'background:#f1f5f9; color:#94a3b8;' }}">RPE</span>
                <span title="Modul Ajar / RPP Merdeka" style="padding:1px 5px; border-radius:3px; font-weight:700; {{ $kel['has_modul'] ? 'background:#ecfdf5; color:#065f46;' : 'background:#f1f5f9; color:#94a3b8;' }}">Modul ({{ $p->modulAjars->count() }})</span>
                <span title="Kriteria Ketercapaian (KKTP)" style="padding:1px 5px; border-radius:3px; font-weight:700; {{ $kel['has_kktp'] ? 'background:#ecfdf5; color:#065f46;' : 'background:#f1f5f9; color:#94a3b8;' }}">KKTP</span>
              </div>
            </td>
            <td style="text-align:center;">
              <span class="badge" style="background:{{ $badge['bg'] }}; color:{{ $badge['color'] }}; border:1px solid {{ $badge['border'] }}; font-size:11.5px; padding:5px 9px; font-weight:700;">
                <i class="bi {{ $badge['icon'] }} me-1"></i> {{ $badge['label'] }}
              </span>
              @if($p->status === 'perlu_revisi' && $p->catatan_supervisi)
                <div style="font-size:10.5px; color:#b91c1c; margin-top:4px; max-width:160px; line-height:1.2;" title="{{ $p->catatan_supervisi }}">
                  Catatan: {{ Str::limit($p->catatan_supervisi, 35) }}
                </div>
              @endif
            </td>
            <td style="text-align:center;">
              @if($p->status === 'disahkan')
                <div style="font-size:11.5px; font-weight:700; color:#059669;">
                  <i class="bi bi-patch-check-fill text-success"></i> Disahkan
                </div>
                <div style="font-size:10px; color:#64748b;">
                  {{ $p->tanggal_pengesahan?->translatedFormat('d M Y') ?? '-' }}
                </div>
                <div style="font-size:9.5px; font-family:monospace; color:#475569; margin-top:2px;">
                  {{ $p->qr_token_pengesahan }}
                </div>
              @else
                <span style="font-size:11px; color:#94a3b8;">Belum Disahkan</span>
              @endif
            </td>
            <td style="text-align:center;">
              <div style="display:flex; justify-content:center; gap:6px;">
                <a href="{{ route('akademik.perangkat.show', $p->id) }}" class="ak-btn ak-btn-secondary" style="padding:4px 10px; font-size:12px;" title="Kelola & Buka Dokumen">
                  <i class="bi bi-pencil-square"></i>
                  <span>Buka</span>
                </a>
                @if($p->status === 'disahkan')
                  <a href="{{ route('akademik.perangkat.cetak-pengesahan', $p->id) }}" target="_blank" class="ak-btn ak-btn-secondary" style="padding:4px 8px; font-size:12px; color:#059669;" title="Cetak Lembar Pengesahan Resmi">
                    <i class="bi bi-printer"></i>
                  </a>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align:center; padding:40px 20px; color:#94a3b8;">
              <i class="bi bi-folder-x" style="font-size:40px; display:block; margin-bottom:8px; opacity:0.6;"></i>
              <div style="font-size:14px; font-weight:700; color:#475569;">Belum Ada Dokumen Perangkat Pembelajaran</div>
              <div style="font-size:12px;">Gunakan tombol "Buat Perangkat Ajar" di atas untuk membuat dokumen perangkat mengajar baru.</div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($perangkats->hasPages())
    <div style="padding:14px 20px; border-top:1px solid var(--ak-slate-200);">
      {{ $perangkats->links() }}
    </div>
  @endif
</div>

{{-- Modal Buat Perangkat Ajar Baru --}}
<div class="modal fade" id="modalBuatPerangkat" tabindex="-1" aria-labelledby="modalBuatPerangkatLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:12px; border:none; box-shadow:0 15px 35px rgba(0,0,0,0.15);">
      <div class="modal-header" style="border-bottom:1px solid var(--ak-slate-200); padding:16px 20px;">
        <h5 class="modal-title" id="modalBuatPerangkatLabel" style="font-size:15px; font-weight:800; color:var(--ak-dark);">
          <i class="bi bi-folder-plus text-primary me-1"></i> Buat Folder Perangkat Ajar
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="{{ route('akademik.perangkat.store') }}" method="POST">
        @csrf
        <div class="modal-body" style="padding:20px;">
          {{-- Guru --}}
          <div style="margin-bottom:14px;">
            <label class="ak-form-label">Guru Pengampu <span class="text-danger">*</span></label>
            @if($isGuru && !auth()->user()->isAdmin() && !auth()->user()->isWakaKurikulum())
              <input type="hidden" name="guru_id" value="{{ auth()->user()->guru_id }}">
              <input type="text" class="ak-input" value="{{ auth()->user()->guru?->nama }}" readonly style="background:#f1f5f9; font-weight:700;">
            @else
              <select name="guru_id" class="ak-select" required>
                <option value="">-- Pilih Guru Pengampu --</option>
                @foreach($gurus as $g)
                  <option value="{{ $g->id }}" {{ (auth()->user()->guru_id == $g->id) ? 'selected' : '' }}>{{ $g->nama }}</option>
                @endforeach
              </select>
            @endif
          </div>

          {{-- Mata Pelajaran --}}
          <div style="margin-bottom:14px;">
            <label class="ak-form-label">Mata Pelajaran <span class="text-danger">*</span></label>
            <select name="mata_pelajaran_id" class="ak-select" required>
              <option value="">-- Pilih Mata Pelajaran --</option>
              @foreach($mapels as $m)
                <option value="{{ $m->id }}">{{ $m->nama_mapel }} ({{ $m->kode_mapel }})</option>
              @endforeach
            </select>
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:14px;">
            {{-- Tingkat --}}
            <div>
              <label class="ak-form-label">Tingkat Kelas <span class="text-danger">*</span></label>
              <select name="tingkat" class="ak-select" required>
                <option value="X">Kelas X (Fase E)</option>
                <option value="XI">Kelas XI (Fase F)</option>
                <option value="XII">Kelas XII (Fase F)</option>
              </select>
            </div>

            {{-- Semester --}}
            <div>
              <label class="ak-form-label">Semester <span class="text-danger">*</span></label>
              <select name="semester" class="ak-select" required>
                <option value="1" {{ $semester == 1 ? 'selected' : '' }}>Semester Ganjil (1)</option>
                <option value="2" {{ $semester == 2 ? 'selected' : '' }}>Semester Genap (2)</option>
              </select>
            </div>
          </div>

          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:14px;">
            <div>
              <label class="ak-form-label">Pekan Efektif (RPE)</label>
              <input type="number" name="rpe_pekan_efektif" class="ak-input" value="18" min="1" max="26" required>
            </div>
            <div>
              <label class="ak-form-label">Pekan Cadangan/Ujian</label>
              <input type="number" name="rpe_pekan_cadangan" class="ak-input" value="2" min="0" max="10" required>
            </div>
          </div>
        </div>

        <div class="modal-footer" style="border-top:1px solid var(--ak-slate-200); padding:12px 20px;">
          <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="ak-btn ak-btn-primary">
            <i class="bi bi-check2-circle"></i>
            <span>Simpan &amp; Lanjutkan</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
