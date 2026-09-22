@extends('dcc.akademik.layout')

@section('title', 'Supervisi & Pengesahan Perangkat Pembelajaran')
@section('breadcrumb')
  <a href="{{ route('akademik.perangkat.supervisi-meja') }}">Perangkat Pembelajaran</a>
  <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
  <span>Supervisi &amp; Pengesahan</span>
@endsection

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">
      <i class="bi bi-patch-check-fill text-primary me-2"></i>
      Supervisi &amp; Pengesahan Perangkat
    </h1>
    <div class="akademik-page-desc">
      Monitoring kelayakan telaah dokumen kurikulum dan penerbitan legalitas QR pengesahan resmi.
    </div>
  </div>

  <div class="btn-group" role="group">
    <a href="{{ request()->fullUrlWithQuery(['semester' => 1]) }}"
       class="ak-btn {{ $semester == 1 ? 'ak-btn-primary' : 'ak-btn-secondary' }}"
       style="font-size:12px; font-weight:700; padding:6px 14px;">
      Sem 1 (Ganjil)
    </a>
    <a href="{{ request()->fullUrlWithQuery(['semester' => 2]) }}"
       class="ak-btn {{ $semester == 2 ? 'ak-btn-primary' : 'ak-btn-secondary' }}"
       style="font-size:12px; font-weight:700; padding:6px 14px;">
      Sem 2 (Genap)
    </a>
  </div>
</div>

{{-- KPI Stats --}}
<div class="akademik-kpi-grid" style="margin-bottom:18px;">
  <div class="akademik-kpi-card">
    <div>
      <div class="akademik-kpi-val">{{ $stats['total'] }}</div>
      <div class="akademik-kpi-label">Total Dokumen</div>
    </div>
    <div class="akademik-kpi-icon icon-indigo">
      <i class="bi bi-folder2-open"></i>
    </div>
  </div>

  <div class="akademik-kpi-card">
    <div>
      <div class="akademik-kpi-val" style="color:#059669;">{{ $stats['disahkan'] }}</div>
      <div class="akademik-kpi-label">Sudah Disahkan (Ber-QR)</div>
    </div>
    <div class="akademik-kpi-icon icon-emerald">
      <i class="bi bi-patch-check-fill"></i>
    </div>
  </div>

  <div class="akademik-kpi-card">
    <div>
      <div class="akademik-kpi-val" style="color:#ea580c;">{{ $stats['diajukan'] }}</div>
      <div class="akademik-kpi-label">Menunggu Telaah</div>
    </div>
    <div class="akademik-kpi-icon icon-amber">
      <i class="bi bi-hourglass-split"></i>
    </div>
  </div>

  <div class="akademik-kpi-card">
    <div>
      <div class="akademik-kpi-val" style="color:#dc2626;">{{ $stats['revisi'] + $stats['draft'] }}</div>
      <div class="akademik-kpi-label">{{ $stats['revisi'] }} Perlu Revisi · {{ $stats['draft'] }} Draft</div>
    </div>
    <div class="akademik-kpi-icon" style="background:#fee2e2; color:#dc2626;">
      <i class="bi bi-pencil-square"></i>
    </div>
  </div>
</div>

{{-- INLINE FORM TELAAH SUPERVISI (NON-POPUP) --}}
@if($isAdminOrWaka)
  <div class="akademik-card" id="panelTelaahSupervisi" style="display:none; border:2px solid #3b82f6; border-radius:14px; margin-bottom:20px; box-shadow:0 4px 14px rgba(37, 99, 235, 0.08);">
    <div class="akademik-card-header" style="background:linear-gradient(135deg, #eff6ff 0%, #ffffff 100%); border-bottom:1px solid #dbeafe; display:flex; justify-content:space-between; align-items:center; padding:14px 20px;">
      <h3 class="akademik-card-title" style="font-weight:800; font-size:15px; color:#1e3a8a; margin:0;">
        <i class="bi bi-shield-check text-primary me-2"></i>
        <span>Formulir Telaah &amp; Pengesahan Perangkat</span>
      </h3>
      <button type="button" class="btn btn-sm btn-outline-secondary" onclick="tutupTelaahPanel()" style="border-radius:8px; padding:4px 12px; font-size:12px; font-weight:700;">
        <i class="bi bi-x-lg me-1"></i> Tutup
      </button>
    </div>

    <div class="akademik-card-body" style="padding:20px;">
      <form id="formSupervisi" method="POST" action="">
        @csrf
        
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px 16px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
          <div>
            <div style="font-size:11px; color:#64748b; font-weight:700; text-transform:uppercase;">Mata Pelajaran &amp; Guru:</div>
            <div id="supervisiMapelName" style="font-size:14px; font-weight:800; color:var(--ak-dark);">-</div>
          </div>
          <a id="linkLihatDokumen" href="#" target="_blank" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:12px;">
            <i class="bi bi-box-arrow-up-right me-1"></i> Buka Dokumen Lengkap
          </a>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 2fr; gap:16px; margin-bottom:16px;">
          <div>
            <label class="ak-form-label">Keputusan Supervisi <span class="text-danger">*</span></label>
            <select name="status" id="supervisiStatusSelect" class="ak-select" required style="font-weight:700;">
              <option value="disahkan">✅ Sahkan Resmi (Terbitkan QR)</option>
              <option value="perlu_revisi">⚠️ Perlu Revisi (Kirim Catatan)</option>
              <option value="draft">📝 Kembalikan ke Draft</option>
            </select>
          </div>
          <div>
            <label class="ak-form-label">Catatan Evaluasi / Arahan Revisi</label>
            <textarea name="catatan_supervisi" id="supervisiCatatan" class="ak-textarea" rows="3" placeholder="Tuliskan catatan evaluasi ketepatan alokasi JP, materi pokok, atau instruksi perbaikan..."></textarea>
          </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #f1f5f9; padding-top:14px;">
          <div style="font-size:12px; color:#059669;">
            <i class="bi bi-info-circle me-1"></i> Pengesahan resmi akan otomatis menerbitkan tanda tangan digital &amp; QR Code.
          </div>
          <div style="display:flex; gap:10px;">
            <button type="button" class="ak-btn ak-btn-secondary" onclick="tutupTelaahPanel()">Batal</button>
            <button type="submit" class="ak-btn ak-btn-primary" style="padding:9px 22px;">
              <i class="bi bi-check2-circle me-1"></i> Simpan Keputusan
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
@endif

{{-- Filter Toolbar --}}
<div class="akademik-card" style="margin-bottom:16px;">
  <div class="akademik-card-body" style="padding:12px 18px;">
    <form method="GET" action="{{ route('akademik.perangkat.supervisi-meja') }}" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
      <input type="hidden" name="semester" value="{{ $semester }}">

      @if($isAdminOrWaka)
        <div style="min-width:220px; flex:1;">
          <select name="guru_id" class="ak-select" onchange="this.form.submit()" style="font-size:12.5px;">
            <option value="">Semua Guru Pengampu</option>
            @foreach($gurus as $g)
              <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
            @endforeach
          </select>
        </div>
      @endif

      <div style="min-width:140px;">
        <select name="tingkat" class="ak-select" onchange="this.form.submit()" style="font-size:12.5px;">
          <option value="">Semua Tingkat</option>
          <option value="X" {{ request('tingkat') == 'X' ? 'selected' : '' }}>Kelas X (Fase E)</option>
          <option value="XI" {{ request('tingkat') == 'XI' ? 'selected' : '' }}>Kelas XI (Fase F)</option>
          <option value="XII" {{ request('tingkat') == 'XII' ? 'selected' : '' }}>Kelas XII (Fase F)</option>
        </select>
      </div>

      <div style="min-width:160px;">
        <select name="status" class="ak-select" onchange="this.form.submit()" style="font-size:12.5px;">
          <option value="">Semua Status</option>
          <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>⏳ Menunggu Telaah</option>
          <option value="disahkan" {{ request('status') == 'disahkan' ? 'selected' : '' }}>✅ Sudah Disahkan</option>
          <option value="perlu_revisi" {{ request('status') == 'perlu_revisi' ? 'selected' : '' }}>⚠️ Perlu Revisi</option>
          <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>📝 Draft</option>
        </select>
      </div>

      @if(request()->anyFilled(['guru_id', 'tingkat', 'status']))
        <a href="{{ route('akademik.perangkat.supervisi-meja', ['semester' => $semester]) }}" class="ak-btn ak-btn-secondary ak-btn-sm">
          <i class="bi bi-x-circle"></i> Reset
        </a>
      @endif
    </form>
  </div>
</div>

{{-- Main Table --}}
<div class="akademik-card">
  <div class="akademik-card-body" style="padding:0;">
    <div class="akademik-table-wrap">
      <table class="akademik-table">
        <thead>
          <tr>
            <th style="width:40px; text-align:center;">#</th>
            <th>Mata Pelajaran &amp; Tingkat</th>
            <th>Guru Pengampu</th>
            <th style="width:220px;">Kelengkapan</th>
            <th style="width:160px; text-align:center;">Status Dokumen</th>
            <th style="width:160px; text-align:center;">Aksi Supervisi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($perangkats as $idx => $p)
            @php
              $kel = $p->kelengkapan;
              $badge = $p->status_badge;
            @endphp
            <tr>
              <td style="text-align:center; color:#94a3b8; font-weight:700;">
                {{ $perangkats->firstItem() + $idx }}
              </td>
              <td>
                <div style="font-weight:800; color:var(--ak-dark); font-size:14px;">
                  <a href="{{ route('akademik.perangkat.show', $p->id) }}" style="color:inherit; text-decoration:none;">
                    {{ $p->mataPelajaran?->nama_mapel }}
                  </a>
                </div>
                <div style="font-size:11.5px; color:#64748b; margin-top:2px;">
                  Kelas {{ $p->tingkat }} · Fase {{ $p->fase }} · Semester {{ $p->semester == 1 ? 'Ganjil' : 'Genap' }}
                </div>
              </td>
              <td>
                <div style="font-weight:700; color:#1e293b;">{{ $p->guru?->nama }}</div>
                <div style="font-size:11px; color:#64748b;">NIP: {{ $p->guru?->nip ?? '-' }}</div>
              </td>
              <td>
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:3px;">
                  <span style="font-size:11px; font-weight:700; color:#475569;">Progress:</span>
                  <span style="font-size:11.5px; font-weight:800; color:{{ $kel['persen'] == 100 ? '#059669' : ($kel['persen'] >= 60 ? '#d97706' : '#64748b') }};">
                    {{ $kel['persen'] }}%
                  </span>
                </div>
                <div class="progress" style="height:5px; background:#e2e8f0; border-radius:3px; margin-bottom:6px;">
                  <div class="progress-bar" style="width:{{ $kel['persen'] }}%; background-color:{{ $kel['persen'] == 100 ? '#10b981' : ($kel['persen'] >= 60 ? '#f59e0b' : '#3b82f6') }};"></div>
                </div>
                <div style="display:flex; gap:3px; font-size:9.5px;">
                  <span style="padding:1px 4px; border-radius:3px; font-weight:700; {{ $kel['has_cp'] ? 'background:#ecfdf5; color:#065f46;' : 'background:#f1f5f9; color:#94a3b8;' }}">CP</span>
                  <span style="padding:1px 4px; border-radius:3px; font-weight:700; {{ $kel['has_atp'] ? 'background:#ecfdf5; color:#065f46;' : 'background:#f1f5f9; color:#94a3b8;' }}">ATP ({{ $p->atpItems->count() }})</span>
                  <span style="padding:1px 4px; border-radius:3px; font-weight:700; {{ $kel['has_rpe'] ? 'background:#ecfdf5; color:#065f46;' : 'background:#f1f5f9; color:#94a3b8;' }}">Prota</span>
                  <span style="padding:1px 4px; border-radius:3px; font-weight:700; {{ $kel['has_modul'] ? 'background:#ecfdf5; color:#065f46;' : 'background:#f1f5f9; color:#94a3b8;' }}">Modul ({{ $p->modulAjars->count() }})</span>
                  <span style="padding:1px 4px; border-radius:3px; font-weight:700; {{ $kel['has_kktp'] ? 'background:#ecfdf5; color:#065f46;' : 'background:#f1f5f9; color:#94a3b8;' }}">KKTP</span>
                </div>
              </td>

              {{-- Status Dokumen & Pengesahan QR --}}
              <td style="text-align:center;">
                @if($p->status === 'disahkan')
                  <span class="ak-badge ak-badge-success">
                    <i class="bi bi-patch-check-fill me-1"></i> Disahkan Resmi
                  </span>
                  <div style="font-size:10.5px; color:#059669; font-weight:700; margin-top:3px;">
                    QR: <code>{{ $p->qr_token_pengesahan }}</code>
                  </div>
                @elseif($p->status === 'diajukan')
                  <span class="ak-badge ak-badge-warning">
                    <i class="bi bi-hourglass-split me-1"></i> Menunggu Telaah
                  </span>
                @elseif($p->status === 'perlu_revisi')
                  <span class="ak-badge ak-badge-danger">
                    <i class="bi bi-exclamation-octagon me-1"></i> Perlu Revisi
                  </span>
                  @if($p->catatan_supervisi)
                    <div style="font-size:10.5px; color:#b91c1c; margin-top:2px;" title="{{ $p->catatan_supervisi }}">
                      {{ Str::limit($p->catatan_supervisi, 25) }}
                    </div>
                  @endif
                @else
                  <span class="ak-badge ak-badge-secondary">Draft Guru</span>
                @endif
              </td>

              {{-- Aksi Supervisi Tunggal & Jelas --}}
              <td style="text-align:center;">
                <div style="display:flex; justify-content:center; gap:6px; flex-wrap:wrap;">
                  @if($isAdminOrWaka)
                    <button type="button" class="ak-btn ak-btn-primary ak-btn-sm"
                            onclick="bukaTelaahPanel({{ $p->id }}, '{{ addslashes($p->mataPelajaran?->nama_mapel) }} ({{ addslashes($p->guru?->nama) }})', '{{ $p->status }}', '{{ addslashes($p->catatan_supervisi ?? '') }}', '{{ route('akademik.perangkat.show', $p->id) }}')"
                            title="Telaah &amp; Beri Keputusan">
                      <i class="bi bi-shield-check me-1"></i> Telaah
                    </button>
                  @endif

                  @if($p->status === 'disahkan')
                    <a href="{{ route('akademik.perangkat.cetak-pengesahan', $p->id) }}" target="_blank" class="ak-btn ak-btn-secondary ak-btn-sm" title="Cetak Lembar Pengesahan QR">
                      <i class="bi bi-printer"></i>
                    </a>
                  @endif

                  @if(($p->status === 'draft' || $p->status === 'perlu_revisi') && $p->guru_id === auth()->user()->guru_id)
                    <form action="{{ route('akademik.perangkat.ajukan', $p->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Ajukan perangkat ini ke Waka Kurikulum / Kepala Sekolah untuk disupervisi?')">
                      @csrf
                      <button type="submit" class="ak-btn ak-btn-primary ak-btn-sm">
                        <i class="bi bi-send me-1"></i> Ajukan
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align:center; padding:40px 20px; color:#94a3b8;">
                <i class="bi bi-folder-x" style="font-size:36px; display:block; margin-bottom:8px; opacity:0.5;"></i>
                <div style="font-weight:700; color:#475569;">Tidak ada data perangkat pembelajaran</div>
                <div style="font-size:12px; margin-top:2px;">Coba ubah kriteria filter atau pilih semester lainnya.</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($perangkats->hasPages())
      <div style="padding:14px 20px; border-top:1px solid #e2e8f0;">
        {{ $perangkats->links() }}
      </div>
    @endif
  </div>
</div>

<script>
  function bukaTelaahPanel(id, mapelName, currentStatus, catatan, linkDoc) {
    const panel = document.getElementById('panelTelaahSupervisi');
    if (!panel) return;

    const form = document.getElementById('formSupervisi');
    form.action = `/dcc/akademik/perangkat/${id}/supervisi`;
    document.getElementById('supervisiMapelName').innerText = mapelName;
    document.getElementById('supervisiStatusSelect').value = (currentStatus === 'diajukan' ? 'disahkan' : currentStatus);
    document.getElementById('supervisiCatatan').value = catatan || '';
    document.getElementById('linkLihatDokumen').href = linkDoc;

    panel.style.display = 'block';
    panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function tutupTelaahPanel() {
    const panel = document.getElementById('panelTelaahSupervisi');
    if (panel) panel.style.display = 'none';
  }
</script>
@endsection
