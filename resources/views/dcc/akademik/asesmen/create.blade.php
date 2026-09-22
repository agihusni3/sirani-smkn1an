@extends('dcc.akademik.layout')

@section('title', 'Langkah 1: Tentukan Nama Paket Soal')
@section('breadcrumb', 'Buat Paket Asesmen')

@section('content')
<div class="akademik-page-head">
  <div>
    <h1 class="akademik-page-title">Buat Paket Asesmen Baru</h1>
    <div class="akademik-page-desc">
      Langkah 1 dari 3: Tentukan identitas paket soal, mata pelajaran, dan checklist rombel sasaran.
    </div>
  </div>

  <div>
    <a href="{{ route('akademik.asesmen.index') }}" class="ak-btn ak-btn-secondary">
      <i class="bi bi-arrow-left"></i>
      <span>Batal &amp; Kembali</span>
    </a>
  </div>
</div>

{{-- Step Indicator --}}
@include('dcc.akademik.asesmen.partials.wizard_steps', ['step' => 1])

<form action="{{ route('akademik.asesmen.store') }}" method="POST" id="formBuatPaket">
  @csrf

  <div style="max-width:880px; margin:0 auto;">
    <div class="akademik-card">
      <div class="akademik-card-header" style="background:#ffffff; border-bottom:1px solid #f1f5f9;">
        <h3 class="akademik-card-title">
          <i class="bi bi-folder-plus text-primary"></i>
          <span>Identitas &amp; Capaian Paket Soal</span>
        </h3>
        <span class="ak-badge ak-badge-primary">Tahap 1: Definisi Paket</span>
      </div>

      <div class="akademik-card-body" style="padding:24px 28px;">
        
        {{-- Mata Pelajaran --}}
        <div style="margin-bottom:20px;">
          <label class="ak-form-label" style="font-size:13px; font-weight:800; color:#0f172a;">
            Mata Pelajaran yang Diampu <span class="text-danger">*</span>
          </label>
          <select name="mata_pelajaran_id" id="selectMapel" class="ak-select" required style="font-size:14px; padding:10px 14px;" onchange="renderRombels(this.value)">
            <option value="">-- Pilih Mata Pelajaran --</option>
            @foreach($mapels as $m)
              <option value="{{ $m->id }}" {{ (old('mata_pelajaran_id') == $m->id || $mapels->count() === 1) ? 'selected' : '' }}>
                {{ $m->nama_mapel }}
              </option>
            @endforeach
          </select>
          <div style="font-size:12px; color:#64748b; margin-top:4px;">
            Pilih mata pelajaran yang Anda ampu untuk paket asesmen ini.
          </div>
        </div>

        {{-- Checklist Rombel Sasaran (Bisa memilih lebih dari 1 rombel) --}}
        <div style="margin-bottom:24px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; flex-wrap:wrap; gap:8px;">
            <label class="ak-form-label" style="font-size:13px; font-weight:800; color:#0f172a; margin-bottom:0;">
              Rombel / Kelas Sasaran (Opsi Checklist: Bisa Memilih Lebih dari 1 Rombel) <span class="text-danger">*</span>
            </label>
            <div style="display:flex; gap:6px;">
              <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:11px; padding:4px 10px;" onclick="pilihSemuaRombel(true)">
                <i class="bi bi-check-all me-1"></i>Pilih Semua
              </button>
              <button type="button" class="ak-btn ak-btn-secondary ak-btn-sm" style="font-size:11px; padding:4px 10px;" onclick="pilihSemuaRombel(false)">
                <i class="bi bi-x me-1"></i>Bersihkan
              </button>
            </div>
          </div>

          <div id="rombelChecklistContainer" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:10px; border:1.5px solid #e2e8f0; border-radius:10px; padding:16px; background:#f8fafc; min-height:80px;">
            {{-- Dimuat secara otomatis lewat JS berdasarkan mapel yang dipilih --}}
            <div style="color:#94a3b8; font-size:13px; grid-column:1/-1; text-align:center; padding:16px;">
              <i class="bi bi-arrow-up-circle me-1"></i> Silakan pilih Mata Pelajaran di atas untuk menampilkan daftar rombel.
            </div>
          </div>
          <div style="font-size:12px; color:#64748b; margin-top:6px;">
            <i class="bi bi-info-circle me-1"></i> Anda dapat mencentang beberapa kelas sekaligus jika ujian ini digunakan bersama untuk satu mata pelajaran.
          </div>
        </div>

        {{-- Judul & Jenis --}}
        <div style="display:grid; grid-template-columns: 2fr 1fr; gap:16px; margin-bottom:20px;">
          <div>
            <label class="ak-form-label" style="font-size:13px; font-weight:800; color:#0f172a;">
              Nama / Judul Paket Soal <span class="text-danger">*</span>
            </label>
            <input type="text" name="judul" id="inputJudul" class="ak-input" 
                   placeholder="Contoh: Ulangan Harian 1 - Pemrograman Web Dasar" 
                   value="{{ old('judul') }}" required style="font-size:14px; padding:10px 14px;">
          </div>
          <div>
            <label class="ak-form-label" style="font-size:13px; font-weight:800; color:#0f172a;">
              Jenis Evaluasi <span class="text-danger">*</span>
            </label>
            <select name="jenis" id="inputJenis" class="ak-select" required style="font-size:14px; padding:10px 14px;">
              <option value="ulangan_harian" selected>Ulangan Harian (UH)</option>
              <option value="kuis">Kuis / Latihan Harian</option>
              <option value="pts">PTS (Sumatif Tengah Semester)</option>
              <option value="pas">PAS (Sumatif Akhir Semester)</option>
              <option value="tugas">Tugas Daring Mandiri</option>
            </select>
          </div>
        </div>

        {{-- TP Rujukan Kurikulum Merdeka --}}
        <div style="margin-bottom:20px;">
          <label class="ak-form-label" style="font-size:13px; font-weight:800; color:#0f172a;">
            <span>Tujuan Pembelajaran (TP) / Capaian Rujukan</span>
            <span class="ak-badge ak-badge-primary" style="font-size:10px; margin-left:6px;">Kurikulum Merdeka</span>
          </label>
          <textarea name="tujuan_pembelajaran" class="ak-textarea" rows="2" 
                    placeholder="Contoh: 10.1 Memahami sintaks dasar PHP dan kontrol percabangan logika dalam pemecahan algoritma.">{{ old('tujuan_pembelajaran') }}</textarea>
          <div style="font-size:11.5px; color:#64748b; margin-top:4px;">
            Menghubungkan paket butir soal dengan capaian kompetensi siswa pada Buku Nilai &amp; Leger KBM.
          </div>
        </div>

        {{-- Petunjuk & Tata Tertib --}}
        <div style="margin-bottom:24px;">
          <label class="ak-form-label" style="font-size:13px; font-weight:800; color:#0f172a;">
            Petunjuk Umum Pengerjaan (Opsional)
          </label>
          <textarea name="deskripsi" id="inputDeskripsi" class="ak-textarea" rows="3" 
                    placeholder="Pilihlah salah satu opsi jawaban yang paling tepat. Dilarang membuka buku atau berpindah jendela aplikasi selama tes berlangsung...">{{ old('deskripsi') }}</textarea>
        </div>

        {{-- Tombol Lanjut ke Tahap 2 --}}
        <div style="border-top:1px solid #e2e8f0; padding-top:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
          <div style="font-size:12.5px; color:#64748b;">
            <i class="bi bi-info-circle me-1"></i> Setelah menyimpan paket, Anda akan langsung diarahkan untuk <strong>membuat butir soal &amp; validasi mutu (Langkah 2)</strong>.
          </div>
          <button type="submit" class="ak-btn ak-btn-primary" style="padding:12px 24px; font-size:14px; font-weight:800;">
            <span>Lanjut: Buat Butir Soal &amp; Validasi</span>
            <i class="bi bi-arrow-right ms-1"></i>
          </button>
        </div>

      </div>
    </div>
  </div>

</form>

<script>
  const mapelRombelsData = @json($mapelRombels);
  const oldTargetRombelIds = @json(old('target_rombel_ids', []));

  function renderRombels(mapelId) {
    const container = document.getElementById('rombelChecklistContainer');
    if (!mapelId || !mapelRombelsData[mapelId] || mapelRombelsData[mapelId].length === 0) {
      container.innerHTML = `
        <div style="color:#94a3b8; font-size:13px; grid-column:1/-1; text-align:center; padding:16px;">
          <i class="bi bi-exclamation-circle me-1"></i> Belum ada data rombel yang Anda ampu untuk mata pelajaran ini.
        </div>
      `;
      return;
    }

    const rombels = mapelRombelsData[mapelId];
    let html = '';

    rombels.forEach((r) => {
      const isChecked = oldTargetRombelIds.length > 0 
        ? oldTargetRombelIds.includes(r.id.toString()) || oldTargetRombelIds.includes(r.id)
        : true;

      html += `
        <label style="display:flex; align-items:center; gap:10px; padding:10px 14px; background:${isChecked ? '#f0f7ff' : '#ffffff'}; border:1.5px solid ${isChecked ? '#2563eb' : '#cbd5e1'}; border-radius:8px; cursor:pointer; transition:all 0.15s ease;" class="rombel-item-card">
          <input type="checkbox" name="target_rombel_ids[]" value="${r.id}" class="chk-rombel" ${isChecked ? 'checked' : ''} 
                 style="width:18px; height:18px; accent-color:#2563eb;" onchange="updateCardStyle(this)">
          <div style="flex:1; overflow:hidden;">
            <div style="font-weight:800; font-size:13.5px; color:#0f172a;">${r.nama}</div>
            <div style="font-size:11px; color:#64748b;">${r.siswa_count} Siswa Terdaftar</div>
          </div>
        </label>
      `;
    });

    container.innerHTML = html;
  }

  function updateCardStyle(chk) {
    const card = chk.closest('.rombel-item-card');
    if (card) {
      card.style.borderColor = chk.checked ? '#2563eb' : '#cbd5e1';
      card.style.background = chk.checked ? '#f0f7ff' : '#ffffff';
    }
  }

  function pilihSemuaRombel(status) {
    const checkboxes = document.querySelectorAll('.chk-rombel');
    checkboxes.forEach(chk => {
      chk.checked = status;
      updateCardStyle(chk);
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    const selectMapel = document.getElementById('selectMapel');
    if (selectMapel && selectMapel.value) {
      renderRombels(selectMapel.value);
    }
  });

  // Validasi sebelum submit: minimal 1 rombel dicentang
  document.getElementById('formBuatPaket').addEventListener('submit', function(e) {
    const checkedRombels = document.querySelectorAll('.chk-rombel:checked');
    if (checkedRombels.length === 0) {
      e.preventDefault();
      alert('Mohon centang minimal 1 Rombel / Kelas sasaran untuk paket soal ini.');
    }
  });
</script>
@endsection
