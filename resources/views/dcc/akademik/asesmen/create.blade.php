@extends('dcc.akademik.layout')

@section('title', 'Buat Asesmen Online Baru')
@section('breadcrumb', 'Buat Asesmen')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Buat Asesmen Penilaian Online Baru</h1>
    <div class="akademik-page-desc">
      Sistem CBT Cerdas &amp; Anti-Kecurangan Berstandar Kurikulum Merdeka SMKN 1 Air Naningan.
    </div>
  </div>

  <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary">
    <i class="bi bi-arrow-left"></i>
    <span>Kembali ke Daftar</span>
  </a>
</div>

{{-- Preset Shortcut Banner --}}
<div class="akademik-card" style="margin-bottom:20px; background:linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); border:1px solid #bfdbfe;">
  <div class="akademik-card-body" style="padding:16px 20px; display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:12px;">
    <div style="display:flex; align-items:center; gap:12px;">
      <div style="width:40px; height:40px; border-radius:10px; background:#3b82f6; color:#fff; display:flex; align-items:center; justify-content:center; font-size:18px;">
        <i class="bi bi-shield-check"></i>
      </div>
      <div>
        <div style="font-weight:800; font-size:14px; color:#1e3a8a;">Preset Cepat Konfigurasi Asesmen</div>
        <div style="font-size:12px; color:#475569;">Pilih format asesmen yang ingin Anda adakan untuk mengotomatiskan seluruh parameter di bawah:</div>
      </div>
    </div>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
      <button type="button" class="ak-btn ak-btn-sm ak-btn-secondary" onclick="terapkanPreset('kuis')">
        <i class="bi bi-lightning-charge"></i> Kuis Ringan (20m)
      </button>
      <button type="button" class="ak-btn ak-btn-sm ak-btn-secondary" onclick="terapkanPreset('uh')">
        <i class="bi bi-journal-check"></i> Ulangan Harian (60m)
      </button>
      <button type="button" class="ak-btn ak-btn-sm ak-btn-primary" onclick="terapkanPreset('resmi')">
        <i class="bi bi-shield-lock"></i> Ujian Ketat / PTS / PAS (90m)
      </button>
    </div>
  </div>
</div>

<form action="{{ route('akademik.asesmen.store') }}" method="POST" id="formBuatAsesmen">
  @csrf

  <div style="display:grid; grid-template-columns: 2fr 1fr; gap:20px; align-items:start;">
    
    {{-- Main Column: Logical 3-Phase Settings --}}
    <div style="display:flex; flex-direction:column; gap:20px;">

      {{-- Bagian 1: Identitas & Sasaran Pembelajaran --}}
      <div class="akademik-card">
        <div class="akademik-card-header" style="background:#ffffff; border-bottom:1px solid #f1f5f9;">
          <h3 class="akademik-card-title">
            <span style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:6px; background:#eff6ff; color:#2563eb; font-size:12px; font-weight:800; margin-right:8px;">1</span>
            <span>Identitas &amp; Sasaran Pembelajaran</span>
          </h3>
        </div>
        <div class="akademik-card-body">
          <div style="margin-bottom:16px;">
            <label class="ak-form-label">Mata Pelajaran &amp; Rombel Sasaran <span class="text-danger">*</span></label>
            <select name="distribusi_id" class="ak-select" required>
              <option value="">-- Pilih Rombel &amp; Mata Pelajaran --</option>
              @foreach($distribusis as $d)
                <option value="{{ $d->id }}">
                  {{ $d->rombel?->nama_rombel }} — {{ $d->mataPelajaran?->nama_mapel }} (Pengampu: {{ $d->guru?->nama }})
                </option>
              @endforeach
            </select>
          </div>

          <div style="display:grid; grid-template-columns: 2fr 1fr; gap:14px; margin-bottom:16px;">
            <div>
              <label class="ak-form-label">Judul Asesmen / Ujian <span class="text-danger">*</span></label>
              <input type="text" name="judul" id="inputJudul" class="ak-input" placeholder="Contoh: Ulangan Harian 1 - Pemrograman Web & Perangkat Bergerak" required>
            </div>
            <div>
              <label class="ak-form-label">Jenis Evaluasi <span class="text-danger">*</span></label>
              <select name="jenis" id="inputJenis" class="ak-select" required>
                <option value="ulangan_harian" selected>Ulangan Harian (UH)</option>
                <option value="kuis">Kuis / Latihan Harian</option>
                <option value="pts">PTS (Sumatif Tengah Semester)</option>
                <option value="pas">PAS (Sumatif Akhir Semester)</option>
                <option value="tugas">Tugas Daring Mandiri</option>
              </select>
            </div>
          </div>

          <div style="margin-bottom:16px;">
            <label class="ak-form-label">
              <span>Tujuan Pembelajaran (TP) / Capaian Pembelajaran (CP) Rujukan</span>
              <span class="ak-badge ak-badge-primary" style="font-size:10px; margin-left:6px;">Kurikulum Merdeka</span>
            </label>
            <textarea name="tujuan_pembelajaran" class="ak-textarea" rows="2" placeholder="Contoh: 10.1 Memahami sintaks dasar PHP dan kontrol percabangan logika dalam pemecahan algoritma."></textarea>
            <div style="font-size:11.5px; color:#64748b; margin-top:4px;">Menghubungkan asesmen dengan capaian kompetensi siswa pada Buku Rapor &amp; Leger Nilai.</div>
          </div>

          <div>
            <label class="ak-form-label">Petunjuk &amp; Tata Tertib Pengerjaan Siswa</label>
            <textarea name="deskripsi" id="inputDeskripsi" class="ak-textarea" rows="2" placeholder="Pilihlah salah satu opsi jawaban yang paling tepat. Dilarang membuka buku atau berpindah jendela aplikasi selama tes berlangsung..."></textarea>
          </div>
        </div>
      </div>

      {{-- Bagian 2: Sistem Anti-Kecurangan & Keamanan CBT --}}
      <div class="akademik-card" style="border:1.5px solid #cbd5e1;">
        <div class="akademik-card-header" style="background:#f8fafc; display:flex; justify-content:space-between; align-items:center;">
          <h3 class="akademik-card-title">
            <span style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:6px; background:#fef2f2; color:#dc2626; font-size:12px; font-weight:800; margin-right:8px;">2</span>
            <span style="color:#0f172a;">Sistem Anti-Kecurangan &amp; Integritas CBT</span>
          </h3>
          <span class="ak-badge ak-badge-danger">
            <i class="bi bi-shield-lock-fill me-1"></i> Anti-Cheat Active
          </span>
        </div>
        <div class="akademik-card-body">
          <div style="background:#fff1f2; border:1px solid #fecdd3; border-radius:10px; padding:12px 16px; margin-bottom:18px; display:flex; gap:12px; align-items:center;">
            <i class="bi bi-info-circle-fill" style="color:#e11d48; font-size:20px;"></i>
            <div style="font-size:12.5px; color:#9f1239; line-height:1.4;">
              Fitur anti-kecurangan secara aktif memonitor status layar, fokus jendela browser, melacak perpindahan tab, mengunci shortcut inspeksi (DevTools/F12), dan mengacak urutan butir soal serta pilihan opsi per siswa.
            </div>
          </div>

          {{-- Master Switch & Token Group --}}
          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:18px;">
            <div style="background:#f8fafc; padding:14px; border-radius:10px; border:1px solid #e2e8f0;">
              <div style="font-weight:700; font-size:13px; color:#1e293b; margin-bottom:6px; display:flex; align-items:center; gap:8px;">
                <input type="checkbox" name="anti_cheat_mode" id="anti_cheat_mode" value="1" checked style="width:16px; height:16px; accent-color:#dc2626;">
                <label for="anti_cheat_mode" style="cursor:pointer; margin:0;">Aktifkan Mode Keamanan Ketat (Strict Anti-Cheat)</label>
              </div>
              <div style="font-size:11.5px; color:#64748b; margin-left:24px;">
                Mengaktifkan pendeteksi pelanggaran realtime dan pencatatan audit log integritas siswa.
              </div>
            </div>

            <div style="background:#f8fafc; padding:14px; border-radius:10px; border:1px solid #e2e8f0;">
              <div style="font-weight:700; font-size:13px; color:#1e293b; margin-bottom:6px;">Token Akses Ujian (Proctor Token)</div>
              <div style="display:flex; gap:6px;">
                <input type="text" name="token_ujian" id="inputToken" class="ak-input" style="font-family:monospace; font-weight:800; font-size:15px; letter-spacing:2px; text-transform:uppercase; text-align:center;" placeholder="MISAL: SMK1AN" maxlength="10">
                <button type="button" class="ak-btn ak-btn-secondary" onclick="generateRandomToken()" title="Buat Token Acak Otomatis">
                  <i class="bi bi-arrow-repeat"></i>
                </button>
              </div>
              <div style="font-size:11px; color:#64748b; margin-top:4px;">Kosongkan jika siswa dapat langsung masuk tanpa input token.</div>
            </div>
          </div>

          {{-- Detail Security Rules --}}
          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:14px;">
            <label style="display:flex; align-items:flex-start; gap:10px; padding:12px; border:1px solid #e2e8f0; border-radius:8px; cursor:pointer; background:#ffffff;">
              <input type="checkbox" name="wajib_fullscreen" id="wajib_fullscreen" value="1" checked style="accent-color:#2563eb; margin-top:3px;">
              <div>
                <div style="font-size:13px; font-weight:700; color:#1e293b;">Wajib Layar Penuh (Fullscreen Lock)</div>
                <div style="font-size:11.5px; color:#64748b;">Siswa wajib masuk mode fullscreen. Peringatan keras muncul jika keluar.</div>
              </div>
            </label>

            <label style="display:flex; align-items:flex-start; gap:10px; padding:12px; border:1px solid #e2e8f0; border-radius:8px; cursor:pointer; background:#ffffff;">
              <input type="checkbox" name="blokir_copy_paste" id="blokir_copy_paste" value="1" checked style="accent-color:#2563eb; margin-top:3px;">
              <div>
                <div style="font-size:13px; font-weight:700; color:#1e293b;">Blokir Copy-Paste &amp; Shortcut DevTools</div>
                <div style="font-size:11.5px; color:#64748b;">Menonaktifkan klik kanan, Ctrl+C, Ctrl+V, F12, dan pintasan inspeksi.</div>
              </div>
            </label>

            <label style="display:flex; align-items:flex-start; gap:10px; padding:12px; border:1px solid #e2e8f0; border-radius:8px; cursor:pointer; background:#ffffff;">
              <input type="checkbox" name="acak_soal" id="acak_soal" value="1" checked style="accent-color:#2563eb; margin-top:3px;">
              <div>
                <div style="font-size:13px; font-weight:700; color:#1e293b;">Acak Urutan Butir Soal</div>
                <div style="font-size:11.5px; color:#64748b;">Setiap peserta menerima nomor butir soal dengan susunan berbeda.</div>
              </div>
            </label>

            <label style="display:flex; align-items:flex-start; gap:10px; padding:12px; border:1px solid #e2e8f0; border-radius:8px; cursor:pointer; background:#ffffff;">
              <input type="checkbox" name="acak_opsi" id="acak_opsi" value="1" checked style="accent-color:#2563eb; margin-top:3px;">
              <div>
                <div style="font-size:13px; font-weight:700; color:#1e293b;">Acak Pilihan Opsi Ganda (A, B, C, D, E)</div>
                <div style="font-size:11.5px; color:#64748b;">Letak opsi jawaban diacak acak unik per siswa mencegah saling mencontek.</div>
              </div>
            </label>
          </div>

          {{-- Batas Toleransi Keluar Layar --}}
          <div style="margin-top:16px; padding:12px 16px; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
            <div>
              <div style="font-size:13px; font-weight:700; color:#1e293b;">Batas Maksimal Toleransi Keluar Layar / Pindah Tab</div>
              <div style="font-size:11.5px; color:#64748b;">Jika pelanggaran mencapai batas ini, lembar jawaban otomatis dikunci / disubmit paksa.</div>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
              <select name="max_toleransi_keluar" id="max_toleransi_keluar" class="ak-select" style="width:110px; font-weight:700;">
                <option value="1">1 Kali (Ketat)</option>
                <option value="2">2 Kali</option>
                <option value="3" selected>3 Kali (Standar)</option>
                <option value="5">5 Kali (Longgar)</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      {{-- Bagian 3: Jadwal & Durasi Pengerjaan --}}
      <div class="akademik-card">
        <div class="akademik-card-header" style="background:#ffffff; border-bottom:1px solid #f1f5f9;">
          <h3 class="akademik-card-title">
            <span style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:6px; background:#eff6ff; color:#2563eb; font-size:12px; font-weight:800; margin-right:8px;">3</span>
            <span>Jadwal &amp; Alokasi Waktu Pengerjaan</span>
          </h3>
        </div>
        <div class="akademik-card-body">
          <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:14px;">
            <div>
              <label class="ak-form-label">Durasi Pengerjaan (Menit) <span class="text-danger">*</span></label>
              <div style="position:relative;">
                <input type="number" name="durasi_menit" id="durasi_menit" class="ak-input" value="60" min="5" max="300" required>
                <span style="position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:12px; color:#94a3b8; font-weight:600;">Menit</span>
              </div>
            </div>
            <div>
              <label class="ak-form-label">Waktu Buka Ujian (Opsional)</label>
              <input type="datetime-local" name="dibuka_pada" class="ak-input">
              <div style="font-size:11px; color:#64748b; margin-top:3px;">Kosongkan jika dibuka kapan saja.</div>
            </div>
            <div>
              <label class="ak-form-label">Waktu Tutup Ujian (Opsional)</label>
              <input type="datetime-local" name="ditutup_pada" class="ak-input">
              <div style="font-size:11px; color:#64748b; margin-top:3px;">Tenggat waktu akhir pengerjaan.</div>
            </div>
          </div>
        </div>
      </div>

    </div>

    {{-- Sidebar Column: Standar Kelulusan & Publikasi --}}
    <div style="display:flex; flex-direction:column; gap:20px;">

      <div class="akademik-card">
        <div class="akademik-card-header">
          <h3 class="akademik-card-title">
            <i class="bi bi-award text-primary"></i>
            <span>Standar Nilai (KKM)</span>
          </h3>
        </div>
        <div class="akademik-card-body">
          <div style="margin-bottom:16px;">
            <label class="ak-form-label">Batas Kelulusan Minimal (0 - 100) <span class="text-danger">*</span></label>
            <input type="number" name="passing_grade" id="passing_grade" class="ak-input" value="75" min="0" max="100" required style="font-size:18px; font-weight:800; color:#1e293b;">
            <div style="font-size:11.5px; color:#64748b; margin-top:4px;">Siswa dengan skor di bawah nilai ini akan berstatus <strong>Remedial</strong>.</div>
          </div>

          <div style="border-top:1px solid #f1f5f9; padding-top:14px;">
            <div style="font-size:12px; font-weight:700; color:#1e293b; margin-bottom:10px;">Kebijakan Hasil Siswa</div>
            
            <div style="display:flex; flex-direction:column; gap:10px;">
              <label style="display:flex; align-items:center; gap:8px; font-size:12.5px; cursor:pointer;">
                <input type="checkbox" name="tampilkan_nilai" id="tampilkan_nilai" value="1" checked>
                <span>Tampilkan nilai langsung ke siswa</span>
              </label>

              <label style="display:flex; align-items:center; gap:8px; font-size:12.5px; cursor:pointer;">
                <input type="checkbox" name="tampilkan_pembahasan" id="tampilkan_pembahasan" value="1">
                <span>Tampilkan kunci &amp; pembahasan soal</span>
              </label>
            </div>
          </div>
        </div>
      </div>

      {{-- Publication Card --}}
      <div class="akademik-card" style="background:#f8fafc; border:1px solid #cbd5e1;">
        <div class="akademik-card-header" style="background:#f1f5f9;">
          <h3 class="akademik-card-title">
            <i class="bi bi-rocket-takeoff text-primary"></i>
            <span>Status Publikasi</span>
          </h3>
        </div>
        <div class="akademik-card-body">
          <label style="display:flex; align-items:center; gap:10px; font-size:13px; cursor:pointer; font-weight:700; color:#0f172a; margin-bottom:12px;">
            <input type="checkbox" name="is_active" id="is_active" value="1" checked style="width:16px; height:16px; accent-color:#059669;">
            <span>Terbitkan Asesmen Ini (Aktif)</span>
          </label>
          <div style="font-size:11.5px; color:#64748b; line-height:1.4; margin-bottom:18px;">
            Jika dicentang, asesmen siap diakses siswa sesuai jadwal. Jika tidak dicentang, akan disimpan sebagai Draft.
          </div>

          <button type="submit" class="ak-btn ak-btn-primary" style="width:100%; justify-content:center; padding:12px; font-size:14px; font-weight:800;">
            <i class="bi bi-check2-circle me-1"></i> Simpan &amp; Lanjut Input Soal
          </button>
          
          <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary" style="width:100%; justify-content:center; margin-top:8px;">
            Batal
          </a>
        </div>
      </div>

    </div>

  </div>
</form>

<script>
  function generateRandomToken() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let res = '';
    for (let i = 0; i < 6; i++) {
      res += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('inputToken').value = res;
  }

  function terapkanPreset(tipe) {
    const judul = document.getElementById('inputJudul');
    const jenis = document.getElementById('inputJenis');
    const durasi = document.getElementById('durasi_menit');
    const kkm = document.getElementById('passing_grade');
    const antiCheat = document.getElementById('anti_cheat_mode');
    const fullscreen = document.getElementById('wajib_fullscreen');
    const copyPaste = document.getElementById('blokir_copy_paste');
    const acakSoal = document.getElementById('acak_soal');
    const acakOpsi = document.getElementById('acak_opsi');
    const toleransi = document.getElementById('max_toleransi_keluar');
    const tampilkanNilai = document.getElementById('tampilkan_nilai');
    const tampilkanPembahasan = document.getElementById('tampilkan_pembahasan');

    if (tipe === 'kuis') {
      if (!judul.value) judul.value = 'Kuis Singkat Pemahaman Materi';
      jenis.value = 'kuis';
      durasi.value = 20;
      kkm.value = 70;
      antiCheat.checked = true;
      fullscreen.checked = false;
      copyPaste.checked = true;
      acakSoal.checked = true;
      acakOpsi.checked = true;
      toleransi.value = '5';
      tampilkanNilai.checked = true;
      tampilkanPembahasan.checked = true;
      document.getElementById('inputToken').value = '';
    } else if (tipe === 'uh') {
      if (!judul.value) judul.value = 'Ulangan Harian (UH) Bab 1';
      jenis.value = 'ulangan_harian';
      durasi.value = 60;
      kkm.value = 75;
      antiCheat.checked = true;
      fullscreen.checked = true;
      copyPaste.checked = true;
      acakSoal.checked = true;
      acakOpsi.checked = true;
      toleransi.value = '3';
      tampilkanNilai.checked = true;
      tampilkanPembahasan.checked = false;
      generateRandomToken();
    } else if (tipe === 'resmi') {
      if (!judul.value) judul.value = 'Penilaian Tengah Semester (PTS) Ganjil';
      jenis.value = 'pts';
      durasi.value = 90;
      kkm.value = 75;
      antiCheat.checked = true;
      fullscreen.checked = true;
      copyPaste.checked = true;
      acakSoal.checked = true;
      acakOpsi.checked = true;
      toleransi.value = '2';
      tampilkanNilai.checked = false;
      tampilkanPembahasan.checked = false;
      generateRandomToken();
    }
  }
</script>
@endsection
