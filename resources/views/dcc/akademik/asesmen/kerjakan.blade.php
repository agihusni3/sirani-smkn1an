<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ujian CBT: {{ $asesmen->judul }} — SMKN 1 Air Naningan</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <link rel="stylesheet" href="{{ asset('css/akademik-app.css') }}?v={{ time() }}">
  <style>
    body { background: #f1f5f9; }
    .exam-wrapper { max-width: 900px; margin: 20px auto 60px; padding: 0 16px; }
  </style>
</head>
<body>

<div class="exam-wrapper">

  {{-- Sticky Header & Countdown Timer --}}
  <div class="exam-timer-bar">
    <div>
      <div style="font-size:15px; font-weight:800; color:var(--ak-dark);">{{ $asesmen->judul }}</div>
      <div style="font-size:12px; color:#64748b;">
        Peserta: <strong>{{ $siswa?->nama ?? $siswa?->nama_lengkap ?? 'Siswa Ujian' }}</strong> · {{ $asesmen->distribusi?->mataPelajaran?->nama_mapel }}
      </div>
    </div>
    <div style="text-align:right;">
      <div style="font-size:10.5px; font-weight:700; color:#64748b; text-transform:uppercase;">Sisa Waktu Pengerjaan</div>
      <div class="exam-timer-val" id="countdownTimer">
        <i class="bi bi-stopwatch"></i>
        <span id="timerDisplay">--:--</span>
      </div>
    </div>
  </div>

  @if($soals->isEmpty())
    <div class="akademik-card" style="padding:50px; text-align:center;">
      <i class="bi bi-exclamation-circle" style="font-size:40px; color:#d97706; display:block; margin-bottom:12px;"></i>
      <h3 style="font-weight:800;">Belum Ada Butir Soal</h3>
      <p style="color:#64748b; font-size:13px;">Asesmen ini belum memiliki butir pertanyaan yang dikonfigurasi oleh guru pengampu.</p>
      <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary">Kembali ke Modul</a>
    </div>
  @else
    <form action="{{ route('akademik.asesmen.submit', $asesmen->id) }}" method="POST" id="formExam">
      @csrf
      <input type="hidden" name="siswa_id" value="{{ $siswa?->id ?? \App\Models\Siswa::first()?->id }}">

      {{-- List of Questions --}}
      @foreach($soals as $idx => $soal)
        <div class="exam-soal-card" id="soal-card-{{ $idx + 1 }}">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <div class="exam-soal-no">{{ $idx + 1 }}</div>
            <span class="ak-badge ak-badge-secondary">Bobot: {{ $soal->bobot }} Poin</span>
          </div>

          <div style="font-size:15px; font-weight:700; color:var(--ak-dark); line-height:1.5; margin-bottom:14px;">
            {!! nl2br(e($soal->pertanyaan)) !!}
          </div>

          <div class="exam-opsi-list">
            @if($soal->opsi_a)
              <label class="exam-opsi-item">
                <input type="radio" name="jawaban[{{ $soal->id }}]" value="A">
                <span class="exam-opsi-text"><strong>A.</strong> {{ $soal->opsi_a }}</span>
              </label>
            @endif

            @if($soal->opsi_b)
              <label class="exam-opsi-item">
                <input type="radio" name="jawaban[{{ $soal->id }}]" value="B">
                <span class="exam-opsi-text"><strong>B.</strong> {{ $soal->opsi_b }}</span>
              </label>
            @endif

            @if($soal->opsi_c)
              <label class="exam-opsi-item">
                <input type="radio" name="jawaban[{{ $soal->id }}]" value="C">
                <span class="exam-opsi-text"><strong>C.</strong> {{ $soal->opsi_c }}</span>
              </label>
            @endif

            @if($soal->opsi_d)
              <label class="exam-opsi-item">
                <input type="radio" name="jawaban[{{ $soal->id }}]" value="D">
                <span class="exam-opsi-text"><strong>D.</strong> {{ $soal->opsi_d }}</span>
              </label>
            @endif

            @if($soal->opsi_e)
              <label class="exam-opsi-item">
                <input type="radio" name="jawaban[{{ $soal->id }}]" value="E">
                <span class="exam-opsi-text"><strong>E.</strong> {{ $soal->opsi_e }}</span>
              </label>
            @endif
          </div>
        </div>
      @endforeach

      {{-- Action Bar --}}
      <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:14px; padding:18px 24px; display:flex; justify-content:space-between; align-items:center; box-shadow:var(--ak-shadow-sm);">
        <div style="font-size:13px; color:#64748b;">
          Pastikan semua butir soal telah diperiksa sebelum menekan tombol Selesai.
        </div>
        <button type="submit" class="ak-btn ak-btn-primary" onclick="return confirm('Apakah Anda yakin ingin mengakhiri dan mengirim seluruh lembar jawaban ujian?')">
          <i class="bi bi-send-check"></i>
          <span>Kirim Lembar Jawaban (Selesai)</span>
        </button>
      </div>

    </form>
  @endif

</div>

<script>
  // Countdown Timer Logic
  const totalSeconds = {{ $asesmen->durasi_menit * 60 }};
  let timeLeft = totalSeconds;
  const timerDisplay = document.getElementById('timerDisplay');
  const formExam = document.getElementById('formExam');

  function updateTimer() {
    if (timeLeft <= 0) {
      clearInterval(timerInterval);
      timerDisplay.innerText = "00:00";
      alert("Waktu pengerjaan telah habis! Lembar jawaban Anda akan otomatis dikirim.");
      if (formExam) formExam.submit();
      return;
    }

    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    timerDisplay.innerText = 
      (minutes < 10 ? "0" : "") + minutes + ":" + 
      (seconds < 10 ? "0" : "") + seconds;

    if (timeLeft < 300) {
      document.getElementById('countdownTimer').style.color = '#dc2626';
    }

    timeLeft--;
  }

  updateTimer();
  const timerInterval = setInterval(updateTimer, 1000);
</script>

</body>
</html>
