@extends('dcc.akademik.layout')

@section('title', 'Kelola Butir Soal Asesmen')
@section('breadcrumb', 'Butir Soal Asesmen')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">{{ $asesmen->judul }}</h1>
    <div class="akademik-page-desc">
      {{ $asesmen->distribusi?->mataPelajaran?->nama_mapel }} · {{ $asesmen->distribusi?->rombel?->nama_rombel }} · Durasi: {{ $asesmen->durasi_menit }} Menit · KKM: {{ $asesmen->passing_grade }}
    </div>
  </div>

  <div style="display:flex; gap:10px; align-items:center;">
    <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary">
      <i class="bi bi-arrow-left"></i>
      <span>Kembali</span>
    </a>
    <a href="{{ route('akademik.asesmen.kerjakan', $asesmen->id) }}" class="ak-btn ak-btn-secondary" target="_blank" title="Simulasi Ujian CBT">
      <i class="bi bi-phone"></i>
      <span>Simulasi CBT</span>
    </a>
    @if($auditKelayakan['is_valid'])
      <a href="{{ route('akademik.asesmen.penugasan', $asesmen->id) }}" class="ak-btn ak-btn-primary" style="font-weight:800;">
        <span>Lanjut ke Sesi Penugasan</span>
        <i class="bi bi-arrow-right ms-1"></i>
      </a>
    @else
      <span class="ak-btn ak-btn-secondary" style="opacity:0.6; cursor:not-allowed;" title="Selesaikan minimal 1 butir soal yang valid untuk membuka sesi penugasan">
        <span>Lanjut ke Sesi Penugasan</span>
        <i class="bi bi-lock-fill ms-1"></i>
      </span>
    @endif
  </div>
</div>

{{-- Step Indicator: Step 2 Aktif --}}
@include('dcc.akademik.asesmen.partials.wizard_steps', ['step' => 2])

{{-- Panel Audit Kelayakan & Validasi Mutu Soal --}}
<div class="akademik-card" style="margin-bottom:20px; border-left:4px solid {{ $auditKelayakan['is_valid'] ? '#10b981' : '#f59e0b' }};">
  <div class="akademik-card-body" style="padding:16px 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:42px; height:42px; border-radius:10px; background:{{ $auditKelayakan['is_valid'] ? '#ecfdf5' : '#fffbeb' }}; color:{{ $auditKelayakan['is_valid'] ? '#059669' : '#d97706' }}; display:flex; align-items:center; justify-content:center; font-size:22px;">
          <i class="bi {{ $auditKelayakan['is_valid'] ? 'bi-shield-check' : 'bi-shield-exclamation' }}"></i>
        </div>
        <div>
          <div style="display:flex; align-items:center; gap:8px;">
            <strong style="font-size:14.5px; color:#0f172a;">Audit Kelayakan &amp; Validasi Butir Soal</strong>
            @if($asesmen->status_validasi === 'siap_diujikan')
              <span class="ak-badge ak-badge-success"><i class="bi bi-check-circle-fill me-1"></i> Terverifikasi &amp; Siap Diujikan</span>
            @elseif($asesmen->status_validasi === 'perlu_revisi')
              <span class="ak-badge ak-badge-danger"><i class="bi bi-exclamation-octagon-fill me-1"></i> Perlu Revisi</span>
            @else
              <span class="ak-badge ak-badge-warning"><i class="bi bi-pencil-square me-1"></i> Draft Penyusunan</span>
            @endif
          </div>
          <div style="font-size:12px; color:#64748b; margin-top:2px;">
            @if($auditKelayakan['is_valid'])
              Semua <strong>{{ $auditKelayakan['total_soal'] }} butir soal</strong> memenuhi standar teknis integritas CBT (Kunci valid &amp; opsi lengkap).
            @else
              Ditemukan <strong>{{ count($auditKelayakan['errors']) }} kendala</strong> yang perlu diselesaikan sebelum ujian dapat diaktifkan.
            @endif
          </div>
        </div>
      </div>

      {{-- KPI Mini Bar --}}
      <div style="display:flex; gap:16px; align-items:center;">
        <div style="text-align:right;">
          <div style="font-size:11px; color:#64748b; text-transform:uppercase; font-weight:700;">Total Soal</div>
          <div style="font-size:18px; font-weight:900; color:#0f172a;">{{ $auditKelayakan['total_soal'] }} Butir</div>
        </div>
        <div style="height:32px; width:1px; background:#e2e8f0;"></div>
        <div style="text-align:right;">
          <div style="font-size:11px; color:#64748b; text-transform:uppercase; font-weight:700;">Total Bobot</div>
          <div style="font-size:18px; font-weight:900; color:#2563eb;">{{ $auditKelayakan['total_bobot'] }} Poin</div>
        </div>
      </div>
    </div>

    {{-- Error Checklist (Jika ada) --}}
    @if(!empty($auditKelayakan['errors']))
      <div style="margin-top:14px; padding:10px 14px; background:#fef2f2; border:1px solid #fecaca; border-radius:8px; font-size:12px; color:#991b1b;">
        <div style="font-weight:800; margin-bottom:4px;"><i class="bi bi-x-circle me-1"></i> Wajib Diperbaiki Sebelum Sesi Diaktifkan:</div>
        <ul style="margin:0; padding-left:18px;">
          @foreach($auditKelayakan['errors'] as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- Warning Checklist (Saran Mutu) --}}
    @if(!empty($auditKelayakan['warnings']))
      <div style="margin-top:10px; padding:10px 14px; background:#fffbeb; border:1px solid #fde68a; border-radius:8px; font-size:12px; color:#92400e;">
        <div style="font-weight:800; margin-bottom:4px;"><i class="bi bi-exclamation-triangle me-1"></i> Catatan Mutu Butir Soal:</div>
        <ul style="margin:0; padding-left:18px;">
          @foreach($auditKelayakan['warnings'] as $warn)
            <li>{{ $warn }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if($asesmen->catatan_validasi)
      <div style="margin-top:10px; padding:8px 12px; background:#f8fafc; border-left:3px solid #64748b; font-size:12px; color:#334155;">
        <strong>Catatan Telaah Pengawas / Waka Kurikulum:</strong> {{ $asesmen->catatan_validasi }}
      </div>
    @endif
  </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">

  {{-- Left: Form Tambah Soal --}}
  <div class="akademik-card">
    <div class="akademik-card-header">
      <h3 class="akademik-card-title">
        <i class="bi bi-plus-circle text-primary"></i>
        <span>Tambah Butir Pertanyaan No. {{ $nomorBerikutnya }}</span>
      </h3>
      <span class="ak-badge ak-badge-primary">Pilihan Ganda</span>
    </div>
    <div class="akademik-card-body">
      <form action="{{ route('akademik.asesmen.soal.store', $asesmen->id) }}" method="POST" id="formTambahSoal" onsubmit="return validasiFormSoal(event)">
        @csrf
        <input type="hidden" name="tipe" value="pilihan_ganda">

        <div style="margin-bottom:14px;">
          <label class="ak-form-label">Teks Pertanyaan / Soal <span class="text-danger">*</span></label>
          <textarea name="pertanyaan" id="inputPertanyaan" class="ak-textarea" rows="4" placeholder="Tuliskan butir soal di sini..." required>{{ old('pertanyaan') }}</textarea>
        </div>

        <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:14px;">
          <div>
            <label class="ak-form-label">Pilihan Jawaban A <span class="text-danger">*</span></label>
            <input type="text" name="opsi_a" id="opsi_A" class="ak-input" placeholder="Opsi A" value="{{ old('opsi_a') }}" required oninput="cekKesesuaianKunci()">
          </div>
          <div>
            <label class="ak-form-label">Pilihan Jawaban B <span class="text-danger">*</span></label>
            <input type="text" name="opsi_b" id="opsi_B" class="ak-input" placeholder="Opsi B" value="{{ old('opsi_b') }}" required oninput="cekKesesuaianKunci()">
          </div>
          <div>
            <label class="ak-form-label">Pilihan Jawaban C</label>
            <input type="text" name="opsi_c" id="opsi_C" class="ak-input" placeholder="Opsi C" value="{{ old('opsi_c') }}" oninput="cekKesesuaianKunci()">
          </div>
          <div>
            <label class="ak-form-label">Pilihan Jawaban D</label>
            <input type="text" name="opsi_d" id="opsi_D" class="ak-input" placeholder="Opsi D" value="{{ old('opsi_d') }}" oninput="cekKesesuaianKunci()">
          </div>
          <div>
            <label class="ak-form-label">Pilihan Jawaban E</label>
            <input type="text" name="opsi_e" id="opsi_E" class="ak-input" placeholder="Opsi E" value="{{ old('opsi_e') }}" oninput="cekKesesuaianKunci()">
          </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:14px; margin-bottom:14px;">
          <div>
            <label class="ak-form-label">
              Kunci Jawaban Benar <span class="text-danger">*</span>
              <span id="kunciWarning" style="color:#dc2626; font-size:11px; font-weight:700; display:none; margin-left:6px;">⚠️ Opsi Kosong!</span>
            </label>
            <select name="kunci_jawaban" id="selectKunci" class="ak-select" required onchange="cekKesesuaianKunci()">
              <option value="A" {{ old('kunci_jawaban') == 'A' ? 'selected' : '' }}>A</option>
              <option value="B" {{ old('kunci_jawaban') == 'B' ? 'selected' : '' }}>B</option>
              <option value="C" {{ old('kunci_jawaban') == 'C' ? 'selected' : '' }}>C</option>
              <option value="D" {{ old('kunci_jawaban') == 'D' ? 'selected' : '' }}>D</option>
              <option value="E" {{ old('kunci_jawaban') == 'E' ? 'selected' : '' }}>E</option>
            </select>
          </div>
          <div>
            <label class="ak-form-label">Bobot Nilai Soal</label>
            <input type="number" name="bobot" class="ak-input" value="{{ old('bobot', 1) }}" min="1" required>
          </div>
        </div>

        <div style="margin-bottom:16px;">
          <label class="ak-form-label">Pembahasan / Penjelasan (Opsional)</label>
          <textarea name="pembahasan" class="ak-textarea" rows="2" placeholder="Tampilkan sebagai feedback setelah siswa menjawab...">{{ old('pembahasan') }}</textarea>
        </div>

        <button type="submit" class="ak-btn ak-btn-primary" style="width:100%;">
          <i class="bi bi-save"></i>
          <span>Validasi &amp; Simpan Butir Soal</span>
        </button>
      </form>
    </div>
  </div>

  {{-- Right: Daftar Butir Soal Tersimpan --}}
  <div class="akademik-card">
    <div class="akademik-card-header">
      <h3 class="akademik-card-title">
        <i class="bi bi-card-checklist text-primary"></i>
        <span>Bank Soal Tersimpan ({{ $asesmen->soals->count() }} Soal)</span>
      </h3>
      <span class="ak-badge ak-badge-success">Total Bobot: {{ $totalBobot }}</span>
    </div>
    <div class="akademik-card-body" style="padding:16px 20px; max-height:680px; overflow-y:auto;">
      @if($asesmen->soals->isEmpty())
        <div style="padding:40px 20px; text-align:center; color:#64748b;">
          <i class="bi bi-journal-plus" style="font-size:36px; opacity:0.35; display:block; margin-bottom:8px;"></i>
          Belum ada butir soal yang ditambahkan. Gunakan formulir di sebelah kiri untuk menambah soal.
        </div>
      @else
        <div style="display:flex; flex-direction:column; gap:16px;">
          @foreach($asesmen->soals as $idx => $soal)
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px;">
              <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                <span class="ak-badge ak-badge-primary">No. {{ $idx + 1 }} · Bobot: {{ $soal->bobot }}</span>
                <form action="{{ route('akademik.asesmen.soal.destroy', [$asesmen->id, $soal->id]) }}" method="POST" onsubmit="return confirm('Hapus butir soal no {{ $idx + 1 }}?')" style="margin:0;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="ak-btn ak-btn-secondary ak-btn-sm text-danger" title="Hapus Soal">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>

              <div style="font-size:13px; font-weight:700; color:var(--ak-dark); margin-bottom:10px;">
                {{ $soal->pertanyaan }}
              </div>

              <div style="display:flex; flex-direction:column; gap:4px; font-size:12px; color:#475569;">
                <div style="{{ $soal->kunci_jawaban == 'A' ? 'font-weight:700; color:#059669;' : '' }}">
                  <strong>A.</strong> {{ $soal->opsi_a }} {!! $soal->kunci_jawaban == 'A' ? '✓ (Kunci)' : '' !!}
                </div>
                <div style="{{ $soal->kunci_jawaban == 'B' ? 'font-weight:700; color:#059669;' : '' }}">
                  <strong>B.</strong> {{ $soal->opsi_b }} {!! $soal->kunci_jawaban == 'B' ? '✓ (Kunci)' : '' !!}
                </div>
                @if($soal->opsi_c)
                  <div style="{{ $soal->kunci_jawaban == 'C' ? 'font-weight:700; color:#059669;' : '' }}">
                    <strong>C.</strong> {{ $soal->opsi_c }} {!! $soal->kunci_jawaban == 'C' ? '✓ (Kunci)' : '' !!}
                  </div>
                @endif
                @if($soal->opsi_d)
                  <div style="{{ $soal->kunci_jawaban == 'D' ? 'font-weight:700; color:#059669;' : '' }}">
                    <strong>D.</strong> {{ $soal->opsi_d }} {!! $soal->kunci_jawaban == 'D' ? '✓ (Kunci)' : '' !!}
                  </div>
                @endif
                @if($soal->opsi_e)
                  <div style="{{ $soal->kunci_jawaban == 'E' ? 'font-weight:700; color:#059669;' : '' }}">
                    <strong>E.</strong> {{ $soal->opsi_e }} {!! $soal->kunci_jawaban == 'E' ? '✓ (Kunci)' : '' !!}
                  </div>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
</div>

<script>
  function cekKesesuaianKunci() {
    const kunci = document.getElementById('selectKunci').value;
    const inputOpsi = document.getElementById('opsi_' + kunci);
    const warningEl = document.getElementById('kunciWarning');

    if (inputOpsi && inputOpsi.value.trim() === '') {
      if (warningEl) {
        warningEl.style.display = 'inline';
        warningEl.innerText = '⚠️ Opsi ' + kunci + ' masih kosong!';
      }
      return false;
    } else {
      if (warningEl) {
        warningEl.style.display = 'none';
      }
      return true;
    }
  }

  function validasiFormSoal(e) {
    const isKunciValid = cekKesesuaianKunci();
    if (!isKunciValid) {
      const kunci = document.getElementById('selectKunci').value;
      alert("Peringatan Validasi: Anda memilih Kunci Jawaban '" + kunci + "', tetapi teks Pilihan Jawaban " + kunci + " masih kosong. Harap isi teks opsi tersebut sebelum menyimpan.");
      const inputOpsi = document.getElementById('opsi_' + kunci);
      if (inputOpsi) inputOpsi.focus();
      return false;
    }

    // Cek duplikasi opsi A dan B
    const opsiA = document.getElementById('opsi_A').value.trim();
    const opsiB = document.getElementById('opsi_B').value.trim();
    if (opsiA && opsiB && opsiA.toLowerCase() === opsiB.toLowerCase()) {
      alert("Peringatan Validasi: Pilihan Jawaban A dan B memiliki teks yang sama persis!");
      return false;
    }

    return true;
  }

  // Initial check
  document.addEventListener('DOMContentLoaded', cekKesesuaianKunci);
</script>
@endsection
