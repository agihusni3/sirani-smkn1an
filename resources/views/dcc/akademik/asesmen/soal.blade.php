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

  <div style="display:flex; gap:10px;">
    <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary">
      <i class="bi bi-arrow-left"></i>
      <span>Kembali</span>
    </a>
    <a href="{{ route('akademik.asesmen.kerjakan', $asesmen->id) }}" class="ak-btn ak-btn-primary" target="_blank">
      <i class="bi bi-play-circle"></i>
      <span>Simulasi Kerjakan (CBT)</span>
    </a>
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
      <form action="{{ route('akademik.asesmen.soal.store', $asesmen->id) }}" method="POST">
        @csrf
        <input type="hidden" name="tipe" value="pilihan_ganda">

        <div style="margin-bottom:14px;">
          <label class="ak-form-label">Teks Pertanyaan / Soal <span class="text-danger">*</span></label>
          <textarea name="pertanyaan" class="ak-textarea" rows="4" placeholder="Tuliskan butir soal di sini..." required></textarea>
        </div>

        <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:14px;">
          <div>
            <label class="ak-form-label">Pilihan Jawaban A <span class="text-danger">*</span></label>
            <input type="text" name="opsi_a" class="ak-input" placeholder="Opsi A" required>
          </div>
          <div>
            <label class="ak-form-label">Pilihan Jawaban B <span class="text-danger">*</span></label>
            <input type="text" name="opsi_b" class="ak-input" placeholder="Opsi B" required>
          </div>
          <div>
            <label class="ak-form-label">Pilihan Jawaban C</label>
            <input type="text" name="opsi_c" class="ak-input" placeholder="Opsi C">
          </div>
          <div>
            <label class="ak-form-label">Pilihan Jawaban D</label>
            <input type="text" name="opsi_d" class="ak-input" placeholder="Opsi D">
          </div>
          <div>
            <label class="ak-form-label">Pilihan Jawaban E</label>
            <input type="text" name="opsi_e" class="ak-input" placeholder="Opsi E">
          </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:14px; margin-bottom:14px;">
          <div>
            <label class="ak-form-label">Kunci Jawaban Benar <span class="text-danger">*</span></label>
            <select name="kunci_jawaban" class="ak-select" required>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
              <option value="E">E</option>
            </select>
          </div>
          <div>
            <label class="ak-form-label">Bobot Nilai Soal</label>
            <input type="number" name="bobot" class="ak-input" value="1" min="1" required>
          </div>
        </div>

        <div style="margin-bottom:16px;">
          <label class="ak-form-label">Pembahasan / Penjelasan (Opsional)</label>
          <textarea name="pembahasan" class="ak-textarea" rows="2" placeholder="Tampilkan sebagai feedback setelah siswa menjawab..."></textarea>
        </div>

        <button type="submit" class="ak-btn ak-btn-primary" style="width:100%;">
          <i class="bi bi-save"></i>
          <span>Simpan Butir Soal</span>
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

</div>
@endsection
