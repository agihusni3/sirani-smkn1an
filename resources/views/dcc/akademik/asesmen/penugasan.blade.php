@extends('dcc.akademik.layout')

@section('title', 'Langkah 3: Sesi Penugasan & Pengaturan Ujian')
@section('breadcrumb', 'Penugasan Asesmen')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Sesi Penugasan &amp; Jadwal Ujian</h1>
    <div class="akademik-page-desc">
      Langkah 3 dari 3: Tentukan target peserta (rombel/siswa remedial), alokasi waktu, token akses, dan anti-curang CBT.
    </div>
  </div>

  <div style="display:flex; gap:10px;">
    <a href="{{ route('akademik.asesmen.soal', $asesmen->id) }}" class="ak-btn ak-btn-secondary">
      <i class="bi bi-arrow-left"></i>
      <span>Kembali ke Butir Soal</span>
    </a>
    <a href="{{ route('akademik.asesmen.kerjakan', $asesmen->id) }}" class="ak-btn ak-btn-secondary" target="_blank">
      <i class="bi bi-phone"></i>
      <span>Simulasi CBT Mobile</span>
    </a>
  </div>
</div>

{{-- Step Indicator: Step 3 Aktif --}}
@include('dcc.akademik.asesmen.partials.wizard_steps', ['step' => 3])

{{-- Alert Ringkasan Paket Soal --}}
<div class="akademik-card" style="margin-bottom:20px; background:linear-gradient(135deg, #f8fafc, #eff6ff); border:1.5px solid #bfdbfe;">
  <div class="akademik-card-body" style="padding:16px 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
      <div>
        <span class="ak-badge ak-badge-primary" style="text-transform:uppercase; margin-bottom:6px;">{{ $asesmen->jenis_label }}</span>
        <h2 style="font-size:18px; font-weight:900; color:#0f172a; margin:4px 0;">{{ $asesmen->judul }}</h2>
        <div style="font-size:13px; color:#475569;">
          {{ $asesmen->distribusi?->mataPelajaran?->nama_mapel }} · Rombel Sasaran: <strong>{{ $asesmen->rombel_names }}</strong>
        </div>
      </div>
      <div style="display:flex; gap:12px; align-items:center;">
        <span class="ak-badge ak-badge-success" style="font-size:13px; padding:6px 14px;">
          <i class="bi bi-patch-check-fill me-1"></i> {{ $asesmen->soals->count() }} Butir Soal Siap (Bobot: {{ $asesmen->soals->sum('bobot') }})
        </span>
      </div>
    </div>
  </div>
</div>

<form action="{{ route('akademik.asesmen.penugasan.store', $asesmen->id) }}" method="POST" id="formPenugasan">
  @csrf

  <div style="display:grid; grid-template-columns: 2fr 1fr; gap:20px; align-items:start;">

    {{-- Main Left Column --}}
    <div style="display:flex; flex-direction:column; gap:20px;">

      {{-- Bagian 1: Sasaran Penugasan Peserta --}}
      <div class="akademik-card">
        <div class="akademik-card-header" style="background:#ffffff; border-bottom:1px solid #f1f5f9;">
          <h3 class="akademik-card-title">
            <span style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:6px; background:#eff6ff; color:#2563eb; font-size:12px; font-weight:800; margin-right:8px;">1</span>
            <span>Sasaran Penugasan Peserta Ujian</span>
          </h3>
        </div>
        <div class="akademik-card-body">
          
          {{-- Checklist Rombel Sasaran (Bisa memilih lebih dari 1 rombel) --}}
          <div style="margin-bottom:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; flex-wrap:wrap; gap:8px;">
              <label class="ak-form-label" style="font-weight:800; font-size:13px; margin-bottom:0;">
                Rombel / Kelas Sasaran Ujian (Checklist) <span class="text-danger">*</span>
              </label>
              <div style="display:flex; gap:6px;">
                <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:11px; padding:3px 8px;" onclick="pilihSemuaRombelPenugasan(true)">Pilih Semua</button>
                <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:11px;" onclick="pilihSemuaRombelPenugasan(false)">Bersihkan</button>
              </div>
            </div>

            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap:10px; padding:12px; background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:8px;">
              @foreach($availableRombels as $r)
                @php
                  $isRombelChecked = in_array((int)$r->id, $currentTargetRombelIds);
                @endphp
                <label style="display:flex; align-items:center; gap:8px; padding:8px 12px; background:{{ $isRombelChecked ? '#eff6ff' : '#ffffff' }}; border:1.5px solid {{ $isRombelChecked ? '#2563eb' : '#cbd5e1' }}; border-radius:6px; cursor:pointer; font-size:13px;" class="rombel-penugasan-card">
                  <input type="checkbox" name="target_rombel_ids[]" value="{{ $r->id }}" class="chk-penugasan-rombel" {{ $isRombelChecked ? 'checked' : '' }} 
                         style="accent-color:#2563eb; width:16px; height:16px;" onchange="onRombelPenugasanChange(this)">
                  <span style="font-weight:800; color:#0f172a;">{{ $r->nama_rombel }}</span>
                </label>
              @endforeach
            </div>
            <div style="font-size:11.5px; color:#64748b; margin-top:5px;">
              <i class="bi bi-info-circle me-1"></i> Asesmen ini akan aktif dan dapat diakses oleh seluruh rombel yang dicentang di atas.
            </div>
          </div>

          {{-- Mode Peserta --}}
          <div style="margin-bottom:16px;">
            <label class="ak-form-label" style="font-weight:800;">Target Peserta Ujian <span class="text-danger">*</span></label>
            <div style="display:flex; gap:20px; margin-top:8px; flex-wrap:wrap;">
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13.5px; font-weight:700; color:#1e293b;">
                <input type="radio" name="target_tipe" value="rombel" {{ ($asesmen->target_tipe ?? 'rombel') === 'rombel' ? 'checked' : '' }} onchange="toggleTargetPeserta(this.value)" style="accent-color:#2563eb; width:17px; height:17px;">
                <span>Seluruh Siswa di Rombel Kelas Terpilih</span>
              </label>
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13.5px; font-weight:700; color:#b45309;">
                <input type="radio" name="target_tipe" value="siswa_terpilih" {{ ($asesmen->target_tipe ?? '') === 'siswa_terpilih' ? 'checked' : '' }} onchange="toggleTargetPeserta(this.value)" style="accent-color:#2563eb; width:17px; height:17px;">
                <span>Siswa Tertentu Saja (Remedial / Ujian Susulan)</span>
              </label>
            </div>
          </div>

          {{-- Container Checklist Siswa Terpilih (Dikelompokkan per Rombel) --}}
          <div id="containerSiswaTerpilih" style="display:{{ ($asesmen->target_tipe ?? '') === 'siswa_terpilih' ? 'block' : 'none' }}; margin-top:16px; border-top:1.5px dashed #cbd5e1; padding-top:16px;">
            <div style="font-size:12.5px; color:#475569; font-weight:700; margin-bottom:10px;">
              Centang siswa yang wajib mengikuti sesi ujian / remedial ini:
            </div>

            @php
              $selectedIds = $asesmen->target_siswa_ids ?? [];
            @endphp

            <div style="display:flex; flex-direction:column; gap:12px;">
              @foreach($availableRombels as $r)
                @php
                  $rombelSiswas = $siswasPerRombel[$r->id] ?? collect();
                  $isRombelActive = in_array((int)$r->id, $currentTargetRombelIds);
                @endphp
                <div class="rombel-siswa-box" id="siswaBoxRombel{{ $r->id }}" style="display:{{ $isRombelActive ? 'block' : 'none' }}; border:1px solid #cbd5e1; border-radius:8px; padding:12px; background:#ffffff;">
                  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; flex-wrap:wrap; gap:8px;">
                    <div style="font-size:13px; font-weight:800; color:#1e3a8a;">
                      <i class="bi bi-people-fill me-1"></i> Kelas {{ $r->nama_rombel }} ({{ $rombelSiswas->count() }} Siswa)
                    </div>
                    <div style="display:flex; gap:6px;">
                      <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:11px; padding:2px 8px;" onclick="pilihSiswaByRombel({{ $r->id }}, true)">Pilih Semua</button>
                      <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:11px; padding:2px 8px;" onclick="pilihSiswaByRombel({{ $r->id }}, false)">Bersihkan</button>
                    </div>
                  </div>

                  <div style="max-height:180px; overflow-y:auto; display:grid; grid-template-columns: 1fr 1fr; gap:6px; padding-right:4px;">
                    @forelse($rombelSiswas as $s)
                      <label style="display:flex; align-items:center; gap:8px; padding:6px 10px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; cursor:pointer; font-size:12px;">
                        <input type="checkbox" name="target_siswa_ids[]" value="{{ $s->id }}" class="chk-siswa chk-siswa-rombel-{{ $r->id }}" {{ in_array($s->id, $selectedIds) ? 'checked' : '' }} style="accent-color:#2563eb;">
                        <div style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                          <strong style="color:#0f172a;">{{ $s->nama }}</strong>
                          <span style="font-size:10.5px; color:#64748b; margin-left:4px;">({{ $s->nisn ?: '-' }})</span>
                        </div>
                      </label>
                    @empty
                      <div style="color:#94a3b8; font-size:12px; grid-column:span 2; text-align:center; padding:8px;">
                        Tidak ada data siswa aktif pada kelas ini.
                      </div>
                    @endforelse
                  </div>
                </div>
              @endforeach
            </div>

            <div style="font-size:11.5px; color:#b45309; margin-top:8px;">
              <i class="bi bi-info-circle me-1"></i> Hanya siswa yang dicentang yang akan terdaftar dan diizinkan masuk ke lembar ujian di HP.
            </div>
          </div>
        </div>
      </div>

      {{-- Bagian 2: Sistem Anti-Kecurangan & Integritas CBT HP --}}
      <div class="akademik-card" style="border:1.5px solid #cbd5e1;">
        <div class="akademik-card-header" style="background:#f8fafc; display:flex; justify-content:space-between; align-items:center;">
          <h3 class="akademik-card-title">
            <span style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:6px; background:#fef2f2; color:#dc2626; font-size:12px; font-weight:800; margin-right:8px;">2</span>
            <span style="color:#0f172a;">Sistem Anti-Kecurangan &amp; Integritas CBT HP</span>
          </h3>
          <span class="ak-badge ak-badge-danger">
            <i class="bi bi-shield-lock-fill me-1"></i> Anti-Cheat Active
          </span>
        </div>
        <div class="akademik-card-body">
          <div style="background:#fff1f2; border:1px solid #fecdd3; border-radius:10px; padding:12px 16px; margin-bottom:18px; display:flex; gap:12px; align-items:center;">
            <i class="bi bi-info-circle-fill" style="color:#e11d48; font-size:20px;"></i>
            <div style="font-size:12.5px; color:#9f1239; line-height:1.5;">
              <strong>Dioptimalkan untuk Ujian Berbasis HP (Smartphone) & Komputer:</strong> Melacak perpindahan aplikasi (seperti membuka WhatsApp, Google Chrome, split-screen), memblokir seleksi tekan-lama untuk salin soal/Google Lens, serta mengacak butir soal & opsi per siswa.
            </div>
          </div>

          {{-- Master Switch & Token Group --}}
          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:18px;">
            <div style="background:#f8fafc; padding:14px; border-radius:10px; border:1px solid #e2e8f0;">
              <div style="font-weight:700; font-size:13px; color:#1e293b; margin-bottom:6px; display:flex; align-items:center; gap:8px;">
                <input type="checkbox" name="anti_cheat_mode" id="anti_cheat_mode" value="1" {{ $asesmen->anti_cheat_mode ? 'checked' : '' }} style="width:16px; height:16px; accent-color:#dc2626;">
                <label for="anti_cheat_mode" style="cursor:pointer; margin:0;">Aktifkan Mode Pengawasan Ketat (Anti-Cheat)</label>
              </div>
              <div style="font-size:11.5px; color:#64748b; margin-left:24px;">
                Mencatat log pelanggaran secara otomatis saat siswa beralih aplikasi atau keluar dari layar ujian.
              </div>
            </div>

            <div style="background:#f8fafc; padding:14px; border-radius:10px; border:1px solid #e2e8f0;">
              <div style="font-weight:700; font-size:13px; color:#1e293b; margin-bottom:6px;">Token Akses Ujian (Proctor Token)</div>
              <div style="display:flex; gap:6px;">
                <input type="text" name="token_ujian" id="inputToken" class="ak-input" style="font-family:monospace; font-weight:800; font-size:15px; letter-spacing:2px; text-transform:uppercase; text-align:center;" placeholder="MISAL: SMK1AN" maxlength="10" value="{{ $asesmen->token_ujian }}">
                <button type="button" class="ak-btn ak-btn-secondary" onclick="generateRandomToken()" title="Buat Token Acak Otomatis">
                  <i class="bi bi-arrow-repeat"></i>
                </button>
              </div>
              <div style="font-size:11px; color:#64748b; margin-top:4px;">Kosongkan jika siswa dapat langsung masuk tanpa input token pengawas.</div>
            </div>
          </div>

          {{-- Detail Security Rules --}}
          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:14px;">
            <label style="display:flex; align-items:flex-start; gap:10px; padding:12px; border:1px solid #e2e8f0; border-radius:8px; cursor:pointer; background:#ffffff;">
              <input type="checkbox" name="wajib_fullscreen" id="wajib_fullscreen" value="1" {{ $asesmen->wajib_fullscreen ? 'checked' : '' }} style="accent-color:#2563eb; margin-top:3px;">
              <div>
                <div style="font-size:13px; font-weight:700; color:#1e293b;">Kunci Layar Penuh & Fokus (Fullscreen Lock)</div>
                <div style="font-size:11.5px; color:#64748b;">Siswa wajib dalam mode layar penuh. Alarm peringatan berbunyi jika keluar atau menekan recent apps.</div>
              </div>
            </label>

            <label style="display:flex; align-items:flex-start; gap:10px; padding:12px; border:1px solid #e2e8f0; border-radius:8px; cursor:pointer; background:#ffffff;">
              <input type="checkbox" name="blokir_copy_paste" id="blokir_copy_paste" value="1" {{ $asesmen->blokir_copy_paste ? 'checked' : '' }} style="accent-color:#2563eb; margin-top:3px;">
              <div>
                <div style="font-size:13px; font-weight:700; color:#1e293b;">Blokir Salin Teks & Tekan-Lama (Anti Copy-Paste)</div>
                <div style="font-size:11.5px; color:#64748b;">Menonaktifkan seleksi teks tekan-lama di HP (cegah kirim ke WA/Google Lens) serta klik kanan.</div>
              </div>
            </label>

            <label style="display:flex; align-items:flex-start; gap:10px; padding:12px; border:1px solid #e2e8f0; border-radius:8px; cursor:pointer; background:#ffffff;">
              <input type="checkbox" name="acak_soal" id="acak_soal" value="1" {{ $asesmen->acak_soal ? 'checked' : '' }} style="accent-color:#2563eb; margin-top:3px;">
              <div>
                <div style="font-size:13px; font-weight:700; color:#1e293b;">Acak Urutan Butir Soal</div>
                <div style="font-size:11.5px; color:#64748b;">Setiap peserta menerima nomor soal dengan susunan berbeda (cegah lirik HP teman di sebelah).</div>
              </div>
            </label>

            <label style="display:flex; align-items:flex-start; gap:10px; padding:12px; border:1px solid #e2e8f0; border-radius:8px; cursor:pointer; background:#ffffff;">
              <input type="checkbox" name="acak_opsi" id="acak_opsi" value="1" {{ $asesmen->acak_opsi ? 'checked' : '' }} style="accent-color:#2563eb; margin-top:3px;">
              <div>
                <div style="font-size:13px; font-weight:700; color:#1e293b;">Acak Pilihan Opsi Ganda (A, B, C, D, E)</div>
                <div style="font-size:11.5px; color:#64748b;">Letak opsi diacak unik per siswa sehingga contekan kunci huruf A/B/C/D tidak berlaku.</div>
              </div>
            </label>
          </div>

          {{-- Batas Toleransi Keluar Layar --}}
          <div style="margin-top:16px; padding:12px 16px; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
            <div>
              <div style="font-size:13px; font-weight:700; color:#1e293b;">Batas Maksimal Toleransi Beralih Aplikasi / Keluar Layar</div>
              <div style="font-size:11.5px; color:#64748b;">Jika siswa membuka WA, split-screen, atau beralih aplikasi melebihi batas ini, ujian otomatis dikunci.</div>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
              <select name="max_toleransi_keluar" id="max_toleransi_keluar" class="ak-select" style="width:110px; font-weight:700;">
                <option value="1" {{ $asesmen->max_toleransi_keluar == 1 ? 'selected' : '' }}>1 Kali (Ketat)</option>
                <option value="2" {{ $asesmen->max_toleransi_keluar == 2 ? 'selected' : '' }}>2 Kali</option>
                <option value="3" {{ ($asesmen->max_toleransi_keluar ?? 3) == 3 ? 'selected' : '' }}>3 Kali (Standar)</option>
                <option value="5" {{ $asesmen->max_toleransi_keluar == 5 ? 'selected' : '' }}>5 Kali (Longgar)</option>
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
                <input type="number" name="durasi_menit" id="durasi_menit" class="ak-input" value="{{ $asesmen->durasi_menit ?? 60 }}" min="5" max="300" required>
                <span style="position:absolute; right:12px; top:50%; transform:translateY(-50%); font-size:12px; color:#94a3b8; font-weight:600;">Menit</span>
              </div>
            </div>
            <div>
              <label class="ak-form-label">Waktu Buka Ujian (Opsional)</label>
              <input type="datetime-local" name="dibuka_pada" class="ak-input" value="{{ $asesmen->dibuka_pada ? $asesmen->dibuka_pada->format('Y-m-d\TH:i') : '' }}">
              <div style="font-size:11px; color:#64748b; margin-top:3px;">Kosongkan jika dibuka kapan saja.</div>
            </div>
            <div>
              <label class="ak-form-label">Waktu Tutup Ujian (Opsional)</label>
              <input type="datetime-local" name="ditutup_pada" class="ak-input" value="{{ $asesmen->ditutup_pada ? $asesmen->ditutup_pada->format('Y-m-d\TH:i') : '' }}">
              <div style="font-size:11px; color:#64748b; margin-top:3px;">Tenggat waktu akhir pengerjaan.</div>
            </div>
          </div>
        </div>
      </div>

    </div>

    {{-- Right Column: KKM & Final Actions --}}
    <div style="display:flex; flex-direction:column; gap:20px;">

      {{-- KKM & Kebijakan Nilai --}}
      <div class="akademik-card">
        <div class="akademik-card-header" style="background:#ffffff; border-bottom:1px solid #f1f5f9;">
          <h3 class="akademik-card-title">
            <i class="bi bi-award text-primary"></i>
            <span>Standar Nilai (KKM)</span>
          </h3>
        </div>
        <div class="akademik-card-body">
          <div style="margin-bottom:16px;">
            <label class="ak-form-label">Batas Kelulusan Minimal (0 - 100) <span class="text-danger">*</span></label>
            <input type="number" name="passing_grade" class="ak-input" value="{{ $asesmen->passing_grade ?? 75 }}" min="0" max="100" required style="font-size:18px; font-weight:800; color:#0f172a;">
            <div style="font-size:11.5px; color:#64748b; margin-top:4px;">Siswa dengan skor di bawah nilai ini akan berstatus <strong>Remedial</strong>.</div>
          </div>

          <div style="border-top:1px solid #f1f5f9; padding-top:14px; display:flex; flex-direction:column; gap:10px;">
            <label class="ak-form-label" style="font-size:12px; margin-bottom:2px;">Kebijakan Hasil Siswa</label>
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:12.5px;">
              <input type="checkbox" name="tampilkan_nilai" value="1" {{ $asesmen->tampilkan_nilai ? 'checked' : '' }} style="accent-color:#2563eb;">
              <span>Tampilkan nilai langsung ke siswa</span>
            </label>
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:12.5px;">
              <input type="checkbox" name="tampilkan_pembahasan" value="1" {{ $asesmen->tampilkan_pembahasan ? 'checked' : '' }} style="accent-color:#2563eb;">
              <span>Tampilkan kunci &amp; pembahasan soal</span>
            </label>
          </div>
        </div>
      </div>

      {{-- Eksekusi Penerbitan / Penjadwalan --}}
      <div class="akademik-card" style="border:1.5px solid #2563eb;">
        <div class="akademik-card-header" style="background:#eff6ff;">
          <h3 class="akademik-card-title" style="color:#1d4ed8;">
            <i class="bi bi-send-check-fill"></i>
            <span>Penerbitan Sesi Ujian</span>
          </h3>
        </div>
        <div class="akademik-card-body">
          <p style="font-size:12.5px; color:#475569; margin-bottom:18px; line-height:1.5;">
            Paket butir soal telah <strong>tervalidasi</strong>. Anda dapat mengaktifkan ujian sekarang juga atau menyimpannya sebagai draft.
          </p>

          <button type="submit" name="aksi" value="publish" class="ak-btn ak-btn-primary" style="width:100%; justify-content:center; padding:12px; font-size:14px; font-weight:800; margin-bottom:10px;">
            <i class="bi bi-broadcast me-1"></i>
            <span>Terbitkan &amp; Aktifkan Ujian Sekarang</span>
          </button>

          <button type="submit" name="aksi" value="draft" class="ak-btn ak-btn-secondary" style="width:100%; justify-content:center; padding:10px; font-size:13px; font-weight:700;">
            <i class="bi bi-save me-1"></i>
            <span>Simpan sebagai Draft Terjadwal</span>
          </button>
        </div>
      </div>

    </div>

  </div>

</form>

<script>
  function toggleTargetPeserta(tipe) {
    const container = document.getElementById('containerSiswaTerpilih');
    if (tipe === 'siswa_terpilih') {
      container.style.display = 'block';
    } else {
      container.style.display = 'none';
    }
  }

  function onRombelPenugasanChange(chk) {
    const card = chk.closest('.rombel-penugasan-card');
    if (card) {
      card.style.borderColor = chk.checked ? '#2563eb' : '#cbd5e1';
      card.style.background = chk.checked ? '#eff6ff' : '#ffffff';
    }
    const box = document.getElementById('siswaBoxRombel' + chk.value);
    if (box) {
      box.style.display = chk.checked ? 'block' : 'none';
      if (!chk.checked) {
        // Uncheck siswa jika rombel dinonaktifkan
        box.querySelectorAll('.chk-siswa').forEach(cb => cb.checked = false);
      }
    }
  }

  function pilihSemuaRombelPenugasan(status) {
    document.querySelectorAll('.chk-penugasan-rombel').forEach(chk => {
      chk.checked = status;
      onRombelPenugasanChange(chk);
    });
  }

  function pilihSiswaByRombel(rombelId, status) {
    document.querySelectorAll('.chk-siswa-rombel-' + rombelId).forEach(cb => {
      cb.checked = status;
    });
  }

  function pilihSemuaSiswa(status) {
    document.querySelectorAll('.chk-siswa').forEach(cb => {
      cb.checked = status;
    });
  }

  function generateRandomToken() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let token = '';
    for (let i = 0; i < 6; i++) {
      token += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('inputToken').value = token;
  }

  document.getElementById('formPenugasan').addEventListener('submit', function(e) {
    const checkedRombels = document.querySelectorAll('.chk-penugasan-rombel:checked');
    if (checkedRombels.length === 0) {
      e.preventDefault();
      alert('Mohon centang minimal 1 Rombel / Kelas sasaran ujian!');
      return;
    }

    const modeSiswa = document.querySelector('input[name="target_tipe"]:checked');
    if (modeSiswa && modeSiswa.value === 'siswa_terpilih') {
      const checkedSiswa = document.querySelectorAll('.chk-siswa:checked');
      if (checkedSiswa.length === 0) {
        e.preventDefault();
        alert('Anda memilih mode "Siswa Tertentu", mohon centang minimal 1 siswa!');
        return;
      }
    }
  });
</script>
@endsection
