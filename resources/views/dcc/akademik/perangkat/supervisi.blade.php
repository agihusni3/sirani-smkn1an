@extends('dcc.akademik.layout')

@section('title', 'Meja Supervisi & Pengesahan Perangkat Pembelajaran')
@section('breadcrumb')
  <a href="{{ route('akademik.perangkat.supervisi-meja') }}">Perangkat Pembelajaran</a>
  <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
  <span>Supervisi &amp; Pengesahan</span>
@endsection

@section('content')
<div class="ak-page-header">
  <div class="ak-page-title">
    <div class="ak-page-icon" style="background:#fef3c7; color:#d97706;">
      <i class="bi bi-patch-check"></i>
    </div>
    <div>
      <h1 style="font-size:20px; font-weight:800; color:var(--ak-dark); margin:0;">Supervisi &amp; Pengesahan Perangkat</h1>
      <p style="font-size:12.5px; color:var(--ak-muted); margin:0;">Monitoring kelayakan telaah dokumen, verifikasi kurikulum, dan penerbitan legalitas QR Lembar Pengesahan</p>
    </div>
  </div>

  <div class="ak-page-actions">
    {{-- Switcher Semester --}}
    <div style="display:inline-flex; background:#f1f5f9; padding:3px; border-radius:8px; border:1px solid #e2e8f0;">
      <a href="{{ request()->fullUrlWithQuery(['semester' => 1]) }}" 
         style="padding:5px 12px; border-radius:6px; font-size:12px; font-weight:700; text-decoration:none; transition:all 0.15s; {{ $semester == 1 ? 'background:var(--ak-primary); color:#fff; box-shadow:0 1px 3px rgba(0,0,0,0.1);' : 'color:#64748b;' }}">
        Semester 1 (Ganjil)
      </a>
      <a href="{{ request()->fullUrlWithQuery(['semester' => 2]) }}" 
         style="padding:5px 12px; border-radius:6px; font-size:12px; font-weight:700; text-decoration:none; transition:all 0.15s; {{ $semester == 2 ? 'background:var(--ak-primary); color:#fff; box-shadow:0 1px 3px rgba(0,0,0,0.1);' : 'color:#64748b;' }}">
        Semester 2 (Genap)
      </a>
    </div>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:10px;">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:10px;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

{{-- Compliance Summary Metrics --}}
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:12px; margin-bottom:18px;">
  <div style="background:#fff; border:1px solid var(--ak-slate-200); border-radius:10px; padding:14px 16px; display:flex; align-items:center; gap:12px;">
    <div style="width:42px; height:42px; border-radius:9px; background:#eff6ff; color:#3b82f6; display:flex; align-items:center; justify-content:center; font-size:20px;">
      <i class="bi bi-folder2-open"></i>
    </div>
    <div>
      <div style="font-size:20px; font-weight:800; color:var(--ak-dark); line-height:1;">{{ $stats['total'] }}</div>
      <div style="font-size:11.5px; color:#64748b; font-weight:600; margin-top:2px;">Total Dokumen</div>
    </div>
  </div>

  <div style="background:#fff; border:1px solid #bbf7d0; border-radius:10px; padding:14px 16px; display:flex; align-items:center; gap:12px;">
    <div style="width:42px; height:42px; border-radius:9px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; font-size:20px;">
      <i class="bi bi-patch-check-fill"></i>
    </div>
    <div>
      <div style="font-size:20px; font-weight:800; color:#059669; line-height:1;">{{ $stats['disahkan'] }}</div>
      <div style="font-size:11.5px; color:#065f46; font-weight:600; margin-top:2px;">Sudah Disahkan</div>
    </div>
  </div>

  <div style="background:#fff; border:1px solid #fed7aa; border-radius:10px; padding:14px 16px; display:flex; align-items:center; gap:12px;">
    <div style="width:42px; height:42px; border-radius:9px; background:#fff7ed; color:#ea580c; display:flex; align-items:center; justify-content:center; font-size:20px;">
      <i class="bi bi-hourglass-split"></i>
    </div>
    <div>
      <div style="font-size:20px; font-weight:800; color:#ea580c; line-height:1;">{{ $stats['diajukan'] }}</div>
      <div style="font-size:11.5px; color:#9a3412; font-weight:600; margin-top:2px;">Menunggu Verifikasi</div>
    </div>
  </div>

  <div style="background:#fff; border:1px solid #fecaca; border-radius:10px; padding:14px 16px; display:flex; align-items:center; gap:12px;">
    <div style="width:42px; height:42px; border-radius:9px; background:#fef2f2; color:#dc2626; display:flex; align-items:center; justify-content:center; font-size:20px;">
      <i class="bi bi-exclamation-octagon"></i>
    </div>
    <div>
      <div style="font-size:20px; font-weight:800; color:#dc2626; line-height:1;">{{ $stats['revisi'] }}</div>
      <div style="font-size:11.5px; color:#991b1b; font-weight:600; margin-top:2px;">Perlu Revisi</div>
    </div>
  </div>

  <div style="background:#fff; border:1px solid var(--ak-slate-200); border-radius:10px; padding:14px 16px; display:flex; align-items:center; gap:12px;">
    <div style="width:42px; height:42px; border-radius:9px; background:#f8fafc; color:#64748b; display:flex; align-items:center; justify-content:center; font-size:20px;">
      <i class="bi bi-pencil"></i>
    </div>
    <div>
      <div style="font-size:20px; font-weight:800; color:#475569; line-height:1;">{{ $stats['draft'] }}</div>
      <div style="font-size:11.5px; color:#64748b; font-weight:600; margin-top:2px;">Masih Draft</div>
    </div>
  </div>
</div>

{{-- Filter & Search Form --}}
<div style="background:#fff; border:1px solid var(--ak-slate-200); border-radius:10px; padding:12px 16px; margin-bottom:16px;">
  <form method="GET" action="{{ route('akademik.perangkat.supervisi-meja') }}" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
    <input type="hidden" name="semester" value="{{ $semester }}">

    @if($isAdminOrWaka)
      <div style="min-width:200px; flex:1;">
        <select name="guru_id" class="ak-select" onchange="this.form.submit()" style="font-size:12.5px;">
          <option value="">-- Semua Guru Pengampu --</option>
          @foreach($gurus as $g)
            <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
          @endforeach
        </select>
      </div>
    @endif

    <div style="min-width:130px;">
      <select name="tingkat" class="ak-select" onchange="this.form.submit()" style="font-size:12.5px;">
        <option value="">Semua Tingkat</option>
        <option value="X" {{ request('tingkat') == 'X' ? 'selected' : '' }}>Kelas X (Fase E)</option>
        <option value="XI" {{ request('tingkat') == 'XI' ? 'selected' : '' }}>Kelas XI (Fase F)</option>
        <option value="XII" {{ request('tingkat') == 'XII' ? 'selected' : '' }}>Kelas XII (Fase F)</option>
      </select>
    </div>

    <div style="min-width:160px;">
      <select name="status" class="ak-select" onchange="this.form.submit()" style="font-size:12.5px;">
        <option value="">Semua Status Dokumen</option>
        <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>⏳ Menunggu Verifikasi</option>
        <option value="disahkan" {{ request('status') == 'disahkan' ? 'selected' : '' }}>✅ Sudah Disahkan</option>
        <option value="perlu_revisi" {{ request('status') == 'perlu_revisi' ? 'selected' : '' }}>⚠️ Perlu Revisi</option>
        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>📝 Draft</option>
      </select>
    </div>

    @if(request()->anyFilled(['guru_id', 'tingkat', 'status']))
      <a href="{{ route('akademik.perangkat.supervisi-meja', ['semester' => $semester]) }}" class="ak-btn ak-btn-secondary" style="padding:6px 12px; font-size:12px;">
        <i class="bi bi-arrow-counterclockwise"></i> Reset
      </a>
    @endif
  </form>
</div>

{{-- Main Table --}}
<div class="ak-card">
  <div style="overflow-x:auto;">
    <table class="ak-table" style="width:100%; border-collapse:collapse;">
      <thead>
        <tr>
          <th style="width:40px; text-align:center;">#</th>
          <th>Mata Pelajaran &amp; Tingkat</th>
          <th>Guru Pengampu</th>
          <th style="width:190px;">Kelengkapan</th>
          <th style="width:150px; text-align:center;">Status</th>
          <th style="width:160px; text-align:center;">Pengesahan / QR</th>
          <th style="width:180px; text-align:center;">Aksi Supervisi</th>
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
                <span class="badge" style="background:#e0e7ff; color:#3730a3; font-size:10.5px; font-weight:700;">
                  Kelas {{ $p->tingkat }} · Fase {{ $p->fase }}
                </span>
                <span style="font-size:11px; color:#64748b;">
                  Semester {{ $p->semester == 1 ? 'Ganjil' : 'Genap' }}
                </span>
              </div>
            </td>
            <td>
              <div style="font-weight:700; color:#1e293b; font-size:13px;">{{ $p->guru?->nama }}</div>
              <div style="font-size:11px; color:#64748b;">NIP: {{ $p->guru?->nip ?? '-' }}</div>
            </td>
            <td>
              <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:3px;">
                <span style="font-size:10.5px; font-weight:700; color:#475569;">Progress:</span>
                <span style="font-size:11px; font-weight:800; color:{{ $kel['persen'] == 100 ? '#059669' : ($kel['persen'] >= 60 ? '#d97706' : '#64748b') }};">
                  {{ $kel['persen'] }}%
                </span>
              </div>
              <div class="progress" style="height:5px; background:#e2e8f0; border-radius:3px; margin-bottom:5px;">
                <div class="progress-bar" style="width: {{ $kel['persen'] }}%; background-color: {{ $kel['persen'] == 100 ? '#10b981' : ($kel['persen'] >= 60 ? '#f59e0b' : '#3b82f6') }};"></div>
              </div>
              <div style="display:flex; gap:3px; font-size:9.5px;">
                <span title="Capaian Pembelajaran (CP)" style="padding:1px 4px; border-radius:3px; font-weight:700; {{ $kel['has_cp'] ? 'background:#ecfdf5; color:#065f46;' : 'background:#f1f5f9; color:#94a3b8;' }}">CP</span>
                <span title="Alur Tujuan Pembelajaran (ATP)" style="padding:1px 4px; border-radius:3px; font-weight:700; {{ $kel['has_atp'] ? 'background:#ecfdf5; color:#065f46;' : 'background:#f1f5f9; color:#94a3b8;' }}">ATP ({{ $p->atpItems->count() }})</span>
                <span title="Prota & Promes" style="padding:1px 4px; border-radius:3px; font-weight:700; {{ $kel['has_rpe'] ? 'background:#ecfdf5; color:#065f46;' : 'background:#f1f5f9; color:#94a3b8;' }}">Prota</span>
                <span title="Modul Ajar" style="padding:1px 4px; border-radius:3px; font-weight:700; {{ $kel['has_modul'] ? 'background:#ecfdf5; color:#065f46;' : 'background:#f1f5f9; color:#94a3b8;' }}">Modul ({{ $p->modulAjars->count() }})</span>
                <span title="Kriteria Ketercapaian (KKTP)" style="padding:1px 4px; border-radius:3px; font-weight:700; {{ $kel['has_kktp'] ? 'background:#ecfdf5; color:#065f46;' : 'background:#f1f5f9; color:#94a3b8;' }}">KKTP</span>
              </div>
            </td>
            <td style="text-align:center;">
              <span class="badge" style="background:{{ $badge['bg'] }}; color:{{ $badge['color'] }}; border:1px solid {{ $badge['border'] }}; font-size:11px; padding:4px 8px; font-weight:700;">
                <i class="bi {{ $badge['icon'] }} me-1"></i> {{ $badge['label'] }}
              </span>
              @if($p->catatan_supervisi)
                <div style="font-size:10.5px; color:#b91c1c; margin-top:4px; max-width:150px; line-height:1.2; cursor:pointer;" title="{{ $p->catatan_supervisi }}">
                  Catatan: {{ Str::limit($p->catatan_supervisi, 30) }}
                </div>
              @endif
            </td>
            <td style="text-align:center;">
              @if($p->status === 'disahkan')
                <div style="font-size:11px; font-weight:700; color:#059669;">
                  <i class="bi bi-patch-check-fill text-success"></i> Disahkan
                </div>
                <div style="font-size:10px; color:#64748b;">
                  {{ $p->tanggal_pengesahan?->translatedFormat('d M Y') ?? '-' }}
                </div>
                <div style="font-size:9px; font-family:monospace; color:#475569; margin-top:2px;">
                  {{ $p->qr_token_pengesahan }}
                </div>
              @else
                <span style="font-size:11px; color:#94a3b8;">Belum Disahkan</span>
              @endif
            </td>
            <td style="text-align:center;">
              <div style="display:flex; justify-content:center; gap:4px; flex-wrap:wrap;">
                {{-- Tombol Buka Detail --}}
                <a href="{{ route('akademik.perangkat.show', $p->id) }}" class="ak-btn ak-btn-secondary" style="padding:4px 8px; font-size:11.5px;" title="Buka Dokumen Lengkap">
                  <i class="bi bi-eye"></i>
                </a>

                {{-- Cetak Lembar Pengesahan jika sudah disahkan --}}
                @if($p->status === 'disahkan')
                  <a href="{{ route('akademik.perangkat.cetak-pengesahan', $p->id) }}" target="_blank" class="ak-btn ak-btn-secondary" style="padding:4px 8px; font-size:11.5px; color:#059669;" title="Cetak Lembar Pengesahan Ber-QR Code">
                    <i class="bi bi-printer"></i>
                  </a>
                @endif

                {{-- Aksi Guru Ajukan --}}
                @if(($p->status === 'draft' || $p->status === 'perlu_revisi') && ($p->guru_id === auth()->user()->guru_id || $isAdminOrWaka))
                  <form action="{{ route('akademik.perangkat.ajukan', $p->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Ajukan perangkat ini ke Waka Kurikulum / Kepala Sekolah untuk disupervisi?')">
                    @csrf
                    <button type="submit" class="ak-btn ak-btn-primary" style="padding:4px 8px; font-size:11.5px;" title="Ajukan Supervisi">
                      <i class="bi bi-send"></i>
                    </button>
                  </form>
                @endif

                {{-- Aksi Telaah / Supervisi oleh Admin / Waka / Kepsek --}}
                @if($isAdminOrWaka)
                  <button type="button" class="ak-btn ak-btn-secondary" style="padding:4px 8px; font-size:11.5px; color:#d97706;" 
                          onclick="openSupervisiModal({{ $p->id }}, '{{ $p->mataPelajaran?->nama_mapel }}', '{{ $p->status }}', '{{ addslashes($p->catatan_supervisi ?? '') }}')"
                          title="Telaah / Sahkan Dokumen">
                    <i class="bi bi-shield-check"></i>
                    <span>Telaah</span>
                  </button>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align:center; padding:40px 20px; color:#94a3b8;">
              <i class="bi bi-folder-x" style="font-size:40px; display:block; margin-bottom:8px; opacity:0.6;"></i>
              <div style="font-size:14px; font-weight:700; color:#475569;">Tidak ada data perangkat pada filter ini</div>
              <div style="font-size:12px;">Coba ubah kriteria pencarian atau pilih semester lainnya.</div>
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

{{-- Modal Supervisi Terpadu (Admin / Wakakur / Kepala Sekolah) --}}
@if($isAdminOrWaka)
<div class="modal fade" id="modalTelaahSupervisi" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:12px; border:none; box-shadow:0 15px 35px rgba(0,0,0,0.15);">
      <div class="modal-header" style="border-bottom:1px solid var(--ak-slate-200); padding:16px 20px;">
        <h5 class="modal-title" style="font-size:15px; font-weight:800; color:var(--ak-dark);">
          <i class="bi bi-shield-check text-primary me-1"></i> Telaah Supervisi &amp; Pengesahan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="formSupervisi" method="POST" action="">
        @csrf
        <div class="modal-body" style="padding:20px;">
          <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px 14px; margin-bottom:14px;">
            <div style="font-size:11px; color:#64748b; font-weight:600;">Mata Pelajaran:</div>
            <div id="supervisiMapelName" style="font-size:13.5px; font-weight:800; color:var(--ak-dark);">-</div>
          </div>

          <div style="margin-bottom:14px;">
            <label class="ak-form-label">Keputusan Validasi <span class="text-danger">*</span></label>
            <select name="status" id="supervisiStatusSelect" class="ak-select" required>
              <option value="disahkan">✅ Setujui &amp; Sahkan Resmi (Terbitkan QR Pengesahan)</option>
              <option value="perlu_revisi">⚠️ Perlu Revisi (Kirim Catatan ke Guru)</option>
              <option value="draft">📝 Kembalikan ke Draft</option>
            </select>
          </div>

          <div style="margin-bottom:14px;">
            <label class="ak-form-label">Catatan Telaah / Supervisi</label>
            <textarea name="catatan_supervisi" id="supervisiCatatan" class="ak-textarea" rows="4" placeholder="Tuliskan evaluasi ketepatan alokasi waktu JP, asesmen, atau petunjuk revisi perangkat ajar..."></textarea>
          </div>

          <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:10px 14px; font-size:12px; color:#166534;">
            <i class="bi bi-info-circle me-1"></i> Jika disahkan, sistem otomatis mencantumkan <strong>Legalitas QR Code Resmi</strong> pada Lembar Pengesahan Perangkat Ajar.
          </div>
        </div>

        <div class="modal-footer" style="border-top:1px solid var(--ak-slate-200); padding:12px 20px;">
          <button type="button" class="ak-btn ak-btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="ak-btn ak-btn-primary">
            <i class="bi bi-check2-circle"></i>
            <span>Simpan Keputusan Supervisi</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openSupervisiModal(perangkatId, mapelName, currentStatus, catatan) {
  const form = document.getElementById('formSupervisi');
  form.action = `/dcc/akademik/perangkat/${perangkatId}/supervisi`;
  document.getElementById('supervisiMapelName').innerText = mapelName;
  document.getElementById('supervisiStatusSelect').value = (currentStatus === 'diajukan' ? 'disahkan' : currentStatus);
  document.getElementById('supervisiCatatan').value = catatan;
  
  const modal = new bootstrap.Modal(document.getElementById('modalTelaahSupervisi'));
  modal.show();
}
</script>
@endif

@endsection
