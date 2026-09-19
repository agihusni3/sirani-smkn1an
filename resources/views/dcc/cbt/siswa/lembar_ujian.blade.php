<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $jadwal->nama_ujian }} — Lembar Ujian CBT Siswa</title>
  @include('partials.styles')
  <link rel="stylesheet" href="{{ asset('css/dcc-cbt.css') }}?v={{ filemtime(public_path('css/dcc-cbt.css')) }}">
</head>
<body class="cbt-app-body" style="background:#f4f6f9;">

  <!-- Sticky Exam Topbar Identik Kejar.id -->
  <header class="cbt-exam-header">
    <div style="display:flex; align-items:center; gap:12px;">
      <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width:32px; height:32px; object-fit:contain;" onerror="this.src='{{ asset('apple-touch-icon.png') }}'" />
      <div>
        <div style="font-weight:800; font-size:13.5px; color:#0f172a; line-height:1.2;">
          {{ $jadwal->nama_ujian }}
        </div>
        <div style="font-size:11px; color:#64748b;">
          {{ $siswa->nama_siswa }} ({{ $siswa->nisn }})
        </div>
      </div>
    </div>

    <!-- Countdown Timer Digital (Floating Pill) -->
    <div class="cbt-timer-pill" id="timer-container" title="Sisa Waktu Pengerjaan">
      <i class="bi bi-clock-fill text-primary"></i>
      <span id="cbt-timer">00:00:00</span>
    </div>

    <!-- Action & Drawer Toggle -->
    <div style="display:flex; align-items:center; gap:8px;">
      <span id="cbt-sync-status" style="font-size:11.5px; font-weight:700; color:#10b981; display:none; @media(min-width:640px){display:inline;}">
        Tersimpan
      </span>
      <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="offcanvas" data-bs-target="#drawerDaftarSoal" style="font-weight:700; font-size:12px; border-radius:8px; padding:6px 12px;">
        <i class="bi bi-grid-3x3-gap-fill me-1"></i> Daftar Soal
      </button>
      <button type="button" class="btn btn-sm btn-danger" onclick="engine.submitExam(false)" style="font-weight:800; font-size:12px; border-radius:8px; padding:6px 14px;">
        Selesai
      </button>
    </div>
  </header>

  <main class="cbt-container" style="max-width:880px; padding-top:16px;">

    <!-- Form Finish Exam (Hidden POST) -->
    <form id="form-finish-exam" action="{{ route('cbt.siswa.selesai', $jadwal->id) }}" method="POST" style="display:none;">
      @csrf
    </form>

    <!-- Wadah Butir Soal -->
    @foreach($soals as $index => $s)
      @php
        $jawabanUser = $jawabans->get($s->id);
        $currJawaban = $jawabanUser ? $jawabanUser->jawaban_siswa : '';
        $currRagu = $jawabanUser ? $jawabanUser->is_ragu : false;
      @endphp

      <div class="cbt-soal-box cbt-card" id="box-soal-{{ $index }}" style="{{ $index === 0 ? 'display:block;' : 'display:none;' }} margin-bottom:20px;">
        <div class="cbt-card-body" style="padding:26px;">

          <!-- Soal Header / Nomor -->
          <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:12px; margin-bottom:18px;">
            <div style="font-size:15px; font-weight:800; color:#0072bc;">
              Soal Nomor {{ $index + 1 }} <span style="font-size:13px; font-weight:600; color:#64748b;">dari {{ count($soals) }}</span>
            </div>
            <div>
              <span class="badge" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; font-size:11px;">
                Bobot: {{ $s->bobot_nilai }}
              </span>
            </div>
          </div>

          <!-- Stimulus Gambar (jika ada) -->
          @if($s->media_gambar)
            <div style="margin-bottom:16px; text-align:center;">
              <img src="{{ asset($s->media_gambar) }}" alt="Lampiran Soal" style="max-width:100%; max-height:360px; border-radius:10px; border:1px solid #cbd5e1; object-fit:contain;" />
            </div>
          @endif

          <!-- Pertanyaan Teks -->
          <div style="font-size:15.5px; line-height:1.75; color:#1e293b; margin-bottom:24px;">
            {!! nl2br(e($s->pertanyaan)) !!}
          </div>

          <!-- Opsi Jawaban PG (Kartu Interaktif Khas Kejar.id) -->
          @if($s->jenis_soal === 'pg')
            <div style="margin-bottom:20px;">
              @foreach(['A', 'B', 'C', 'D', 'E'] as $huruf)
                @php 
                  $opsiKey = 'opsi_' . strtolower($huruf); 
                  $isSelected = (strtoupper(trim($currJawaban)) === $huruf);
                @endphp
                @if(!empty($s->$opsiKey))
                  <div class="cbt-option-card {{ $isSelected ? 'selected' : '' }}" 
                       data-soal-id="{{ $s->id }}" 
                       data-huruf="{{ $huruf }}">
                    <div class="cbt-option-letter">{{ $huruf }}</div>
                    <div class="cbt-option-text">{{ $s->$opsiKey }}</div>
                  </div>
                @endif
              @endforeach
            </div>
          @elseif($s->jenis_soal === 'esai')
            <div style="margin-bottom:20px;">
              <label style="display:block; font-size:13px; font-weight:700; color:#334155; margin-bottom:6px;">Tuliskan Jawaban Uraian Anda di bawah ini:</label>
              <textarea rows="6" class="form-control input-esai" data-soal-id="{{ $s->id }}" placeholder="Ketikkan uraian jawaban Anda..." style="font-size:14px; border-radius:10px; line-height:1.6;">{{ $currJawaban }}</textarea>
            </div>
          @endif

          <!-- Bottom Navigation Bar (Sebelumnya - Ragu - Berikutnya) -->
          <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #e2e8f0; padding-top:18px; margin-top:10px;">
            <div>
              @if($index > 0)
                <button type="button" class="btn btn-outline-secondary" onclick="engine.prevSoal()" style="font-weight:700; font-size:13px; border-radius:8px; padding:8px 18px;">
                  <i class="bi bi-chevron-left"></i> Sebelumnya
                </button>
              @endif
            </div>

            <div>
              <label style="display:flex; align-items:center; gap:8px; background:#fffbeb; border:1px solid #fef3c7; color:#b45309; padding:8px 16px; border-radius:8px; cursor:pointer; font-weight:700; font-size:13px;">
                <input type="checkbox" id="ragu-{{ $s->id }}" onchange="engine.toggleRagu({{ $s->id }})" {{ $currRagu ? 'checked' : '' }} />
                <span>Ragu-Ragu</span>
              </label>
            </div>

            <div>
              @if($index < count($soals) - 1)
                <button type="button" class="btn btn-primary" onclick="engine.nextSoal()" style="background:#0072bc; border-color:#0072bc; font-weight:700; font-size:13px; border-radius:8px; padding:8px 20px;">
                  Berikutnya <i class="bi bi-chevron-right"></i>
                </button>
              @else
                <button type="button" class="btn btn-success" onclick="engine.submitExam(false)" style="font-weight:800; font-size:13px; border-radius:8px; padding:8px 22px;">
                  Kumpulkan Ujian <i class="bi bi-check-circle ms-1"></i>
                </button>
              @endif
            </div>
          </div>

        </div>
      </div>
    @endforeach

  </main>

  <!-- Offcanvas Drawer Daftar Nomor Soal (Palette) Identik Kejar.id -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="drawerDaftarSoal" aria-labelledby="drawerDaftarSoalLabel" style="width:340px;">
    <div class="offcanvas-header" style="border-bottom:1px solid #e2e8f0;">
      <h5 class="offcanvas-title" id="drawerDaftarSoalLabel" style="font-size:15px; font-weight:800; color:#0f172a;">
        <i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i> Nomor Soal Ujian
      </h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" style="padding:20px;">
      
      <!-- Petunjuk Warna -->
      <div style="display:flex; flex-direction:column; gap:6px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; margin-bottom:18px; font-size:11.5px;">
        <div style="display:flex; align-items:center; gap:8px;">
          <span style="width:14px; height:14px; border-radius:4px; background:#10b981; display:inline-block;"></span>
          <span style="color:#334155; font-weight:600;">Sudah Terjawab</span>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
          <span style="width:14px; height:14px; border-radius:4px; background:#f59e0b; display:inline-block;"></span>
          <span style="color:#334155; font-weight:600;">Ragu-ragu</span>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
          <span style="width:14px; height:14px; border-radius:4px; background:#ffffff; border:1px solid #cbd5e1; display:inline-block;"></span>
          <span style="color:#334155; font-weight:600;">Belum Dijawab</span>
        </div>
      </div>

      <!-- Grid Tombol Nomor -->
      <div class="cbt-number-grid">
        @foreach($soals as $idx => $s)
          @php
            $j = $jawabans->get($s->id);
            $hasJawab = ($j && !empty($j->jawaban_siswa));
            $isRagu = ($j && $j->is_ragu);
          @endphp
          <button type="button" 
                  id="nav-btn-{{ $s->id }}" 
                  class="cbt-num-btn {{ $hasJawab ? 'answered' : '' }} {{ $isRagu ? 'doubtful' : '' }} {{ $idx === 0 ? 'active' : '' }}" 
                  onclick="engine.showSoal({{ $idx }}); const bsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('drawerDaftarSoal')); if(bsOffcanvas) bsOffcanvas.hide();">
            {{ $idx + 1 }}
          </button>
        @endforeach
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/cbt-engine.js') }}?v={{ filemtime(public_path('js/cbt-engine.js')) }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      window.engine = new CbtExamEngine({
        pesertaId: {{ $peserta->id }},
        jadwalId: {{ $jadwal->id }},
        sisaDetik: {{ $sisaDetik }},
        totalSoal: {{ count($soals) }},
        autosaveUrl: '{{ route("cbt.siswa.autosave") }}',
        logViolationUrl: '{{ route("cbt.siswa.log_aktivitas") }}',
        finishUrl: '{{ route("cbt.siswa.hasil", $jadwal->id) }}',
        csrfToken: '{{ csrf_token() }}'
      });

      // Autosave listener for esai
      document.querySelectorAll('.input-esai').forEach(txt => {
        txt.addEventListener('input', (e) => {
          const soalId = txt.getAttribute('data-soal-id');
          const isRagu = document.getElementById(`ragu-${soalId}`) ? document.getElementById(`ragu-${soalId}`).checked : false;
          engine.recordAnswer(soalId, txt.value, isRagu);
        });
      });
    });
  </script>
</body>
</html>
