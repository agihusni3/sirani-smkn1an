@extends('layouts.app_situan')

@section('title', 'Detail & Notula Rapat - SITUAN')

@section('content')
<div class="space-y-6">
  <!-- Top Bar & Breadcrumb -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
    <div class="flex items-center gap-3">
      <a href="{{ route('situan.rapat.index') }}" class="p-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 shadow-sm transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
      </a>
      <div>
        <div class="flex items-center gap-2">
          <span class="px-2.5 py-0.5 rounded text-xs font-semibold uppercase tracking-wider
            @if($rapat->tipe_rapat == 'dinas') bg-indigo-100 text-indigo-700
            @elseif($rapat->tipe_rapat == 'pleno_kelulusan' || $rapat->tipe_rapat == 'pleno_kenaikan') bg-rose-100 text-rose-700
            @elseif($rapat->tipe_rapat == 'komite') bg-amber-100 text-amber-700
            @else bg-blue-100 text-blue-700 @endif">
            {{ $rapat->tipe_label }}
          </span>
          <span class="px-2.5 py-0.5 rounded text-xs font-semibold
            @if($rapat->status == 'selesai') bg-emerald-100 text-emerald-700
            @elseif($rapat->status == 'dibatalkan') bg-red-100 text-red-700
            @else bg-amber-100 text-amber-700 @endif">
            {{ ucfirst($rapat->status) }}
          </span>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 mt-1">{{ $rapat->judul_rapat }}</h1>
        <p class="text-sm text-slate-500">Nomor Agenda / Undangan: <span class="font-mono font-medium text-slate-700">{{ $rapat->nomor_surat ?? '-' }}</span></p>
      </div>
    </div>

    <!-- Actions / Print Dropdown -->
    <div class="flex flex-wrap items-center gap-2">
      <button type="button" onclick="document.getElementById('modal-edit-rapat').classList.remove('hidden')" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 shadow-sm flex items-center gap-2 transition">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        Edit Agenda
      </button>

      <!-- Dropdown Cetak Dokumen Resmi -->
      <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" type="button" class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow flex items-center gap-2 transition">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
          <span>Cetak Dokumen Resmi A4</span>
          <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>

        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-2 z-50">
          <div class="px-4 py-1.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Lembar Terpisah</div>
          <a href="{{ route('situan.rapat.cetak.undangan', $rapat->id) }}" target="_blank" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">
            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            1. Surat Undangan Resmi
          </a>
          <a href="{{ route('situan.rapat.cetak.daftar-hadir', $rapat->id) }}" target="_blank" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">
            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            2. Daftar Hadir Peserta
          </a>
          <a href="{{ route('situan.rapat.cetak.notula', $rapat->id) }}" target="_blank" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            3. Lembar Notula Rapat
          </a>
          <a href="{{ route('situan.rapat.cetak.berita-acara', $rapat->id) }}" target="_blank" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">
            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            4. Lembar Berita Acara
          </a>
          <div class="border-t border-slate-100 my-1"></div>
          <a href="{{ route('situan.rapat.cetak.paket', $rapat->id) }}" target="_blank" class="flex items-center gap-3 px-4 py-2 text-sm font-semibold text-indigo-700 bg-indigo-50/70 hover:bg-indigo-100">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
            Cetak Paket Lengkap (1-Klik)
          </a>
        </div>
      </div>
    </div>
  </div>

  @if(session('success'))
  <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-sm">
    <div class="flex items-center gap-2">
      <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
      <span>{{ session('success') }}</span>
    </div>
  </div>
  @endif

  <!-- Grid: Informasi Rapat & Rekap Kehadiran -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Kolom Kiri: Ringkasan Rapat -->
    <div class="lg:col-span-1 space-y-6">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 space-y-4">
        <h3 class="text-base font-bold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2">
          <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          Informasi Pelaksanaan
        </h3>

        <div class="space-y-3 text-sm">
          <div>
            <div class="text-xs text-slate-400 font-medium uppercase">Hari, Tanggal</div>
            <div class="font-semibold text-slate-800">{{ $rapat->tanggal_rapat->translatedFormat('l, d F Y') }}</div>
          </div>
          <div>
            <div class="text-xs text-slate-400 font-medium uppercase">Waktu / Jam</div>
            <div class="font-semibold text-slate-800">
              {{ $rapat->jam_mulai }} WIB @if($rapat->jam_selesai) s/d {{ $rapat->jam_selesai }} WIB @else s/d Selesai @endif
            </div>
          </div>
          <div>
            <div class="text-xs text-slate-400 font-medium uppercase">Tempat / Ruangan</div>
            <div class="font-semibold text-slate-800">{{ $rapat->tempat }}</div>
          </div>
          <div>
            <div class="text-xs text-slate-400 font-medium uppercase">Pimpinan Rapat</div>
            <div class="font-semibold text-slate-800">{{ $rapat->pimpinan_display }}</div>
            <div class="text-xs text-slate-500">{{ $rapat->pimpinan_jabatan ?? 'Pimpinan' }}</div>
          </div>
          <div>
            <div class="text-xs text-slate-400 font-medium uppercase">Notulis Rapat</div>
            <div class="font-semibold text-slate-800">{{ $rapat->notulis_display }}</div>
          </div>
          <div>
            <div class="text-xs text-slate-400 font-medium uppercase">Sasaran Peserta</div>
            <div class="font-semibold text-slate-800">
              @if($rapat->peserta_tipe == 'semua_gtk') Seluruh Pendidik & Tenaga Kependidikan
              @elseif($rapat->peserta_tipe == 'guru') Seluruh Dewan Guru
              @elseif($rapat->peserta_tipe == 'tendik') Seluruh Staf Tenaga Kependidikan (TU)
              @else Peserta Khusus / Terpilih ({{ count($pesertas) }} Orang)
              @endif
            </div>
            <div class="text-xs text-slate-500 mt-0.5">Total terdata: {{ count($pesertas) }} Orang</div>
          </div>
          @if($rapat->agenda)
          <div>
            <div class="text-xs text-slate-400 font-medium uppercase">Pokok Pembahasan / Agenda</div>
            <div class="text-slate-700 whitespace-pre-line text-xs bg-slate-50 p-2.5 rounded-lg border border-slate-100 mt-1">{{ $rapat->agenda }}</div>
          </div>
          @endif
        </div>
      </div>

      <!-- Card Shortcut Dokumen Cepat -->
      <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl border border-indigo-100 p-5">
        <h4 class="font-bold text-indigo-900 text-sm mb-2 flex items-center gap-2">
          <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          Prosedur Dokumen Dinas TU
        </h4>
        <ul class="text-xs text-indigo-800/80 space-y-1.5 list-disc list-inside">
          <li>Cetak <strong class="text-indigo-900">Undangan</strong> 2-3 hari sebelum rapat dinas.</li>
          <li>Cetak <strong class="text-indigo-900">Daftar Hadir</strong> di meja registrasi rapat.</li>
          <li>Notulis mencatat risalah di form ini lalu cetak <strong class="text-indigo-900">Notula</strong>.</li>
          <li>Gunakan <strong class="text-indigo-900">Paket Lengkap</strong> untuk arsip berkas dinas.</li>
        </ul>
      </div>
    </div>

    <!-- Kolom Kanan: Form Notula & Hasil Rapat -->
    <div class="lg:col-span-2 space-y-6">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
          <div>
            <h2 class="text-lg font-bold text-slate-800">Form Notula & Risalah Rapat Dinas</h2>
            <p class="text-xs text-slate-500">Isikan hasil pembahasan, keputusan, dan rekapitulasi kehadiran dinas</p>
          </div>
          <span class="text-xs font-medium px-2 py-1 bg-slate-100 rounded text-slate-600">Standard Tata Naskah Kemdikbud</span>
        </div>

        <form action="{{ route('situan.rapat.notula', $rapat->id) }}" method="POST" class="space-y-5">
          @csrf

          <!-- Rekapitulasi Presensi & Status Rapat -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status Rapat</label>
              <select name="status" class="w-full text-sm rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 font-medium">
                <option value="dijadwalkan" {{ old('status', $rapat->status) == 'dijadwalkan' ? 'selected' : '' }}>⏳ Dijadwalkan / Berlangsung</option>
                <option value="selesai" {{ old('status', $rapat->status) == 'selesai' ? 'selected' : '' }}>✅ Selesai</option>
                <option value="dibatalkan" {{ old('status', $rapat->status) == 'dibatalkan' ? 'selected' : '' }}>❌ Dibatalkan</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jumlah Hadir (Orang)</label>
              <div class="flex items-center gap-2">
                <input type="number" name="jumlah_hadir" id="input_jumlah_hadir" value="{{ old('jumlah_hadir', $rapat->jumlah_hadir ?? count($pesertas)) }}" min="0" class="w-full text-sm rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-emerald-700">
                <button type="button" onclick="document.getElementById('input_jumlah_hadir').value = {{ count($pesertas) }}" title="Set semua hadir" class="px-2 py-1.5 text-xs bg-emerald-100 text-emerald-800 rounded font-medium hover:bg-emerald-200">Semua</button>
              </div>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jumlah Tidak Hadir</label>
              <input type="number" name="jumlah_tidak_hadir" value="{{ old('jumlah_tidak_hadir', $rapat->jumlah_tidak_hadir ?? 0) }}" min="0" class="w-full text-sm rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-rose-700">
            </div>
          </div>

          <!-- Jalannya Acara / Risalah Rapat -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
              Jalannya Acara / Risalah Pembahasan Rapat <span class="text-rose-500">*</span>
            </label>
            <p class="text-xs text-slate-400 mb-2">Uraikan secara kronologis: pembukaan, sambutan, pengarahan pimpinan, materi pembahasan, tanggapan/masukan peserta.</p>
            <textarea name="jalannya_acara" rows="6" placeholder="1. Pembukaan oleh pembawa acara / notulis pada pukul {{ $rapat->jam_mulai }} WIB.
2. Pengarahan oleh Pimpinan Rapat ({{ $rapat->pimpinan_display }}): ...
3. Pembahasan agenda rapat: ...
4. Sesi tanya jawab dan tanggapan peserta: ..." class="w-full text-sm rounded-xl border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 font-sans leading-relaxed">{{ old('jalannya_acara', $rapat->jalannya_acara) }}</textarea>
          </div>

          <!-- Hasil Keputusan / Kesimpulan Rapat -->
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
              Hasil Keputusan & Kesimpulan Rapat <span class="text-rose-500">*</span>
            </label>
            <p class="text-xs text-slate-400 mb-2">Poin-poin mutlak yang disepakati bersama dalam forum rapat.</p>
            <textarea name="hasil_keputusan" rows="5" placeholder="Berdasarkan hasil musyawarah mufakat, disepakati bahwa:
1. ...
2. ...
3. ..." class="w-full text-sm rounded-xl border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 font-sans leading-relaxed">{{ old('hasil_keputusan', $rapat->hasil_keputusan) }}</textarea>
          </div>

          <!-- Tindak Lanjut & Penanggung Jawab (PIC) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Rencana Tindak Lanjut & Target</label>
              <textarea name="tindak_lanjut" rows="3" placeholder="Rencana aksi setelah rapat dan tenggat waktu pelaksanaan..." class="w-full text-sm rounded-xl border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 font-sans">{{ old('tindak_lanjut', $rapat->tindak_lanjut) }}</textarea>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Tambahan / Khusus</label>
              <textarea name="catatan_khusus" rows="3" placeholder="Hal-hal lain yang perlu dicatat..." class="w-full text-sm rounded-xl border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 font-sans">{{ old('catatan_khusus', $rapat->catatan_khusus) }}</textarea>
            </div>
          </div>

          <!-- Tombol Simpan -->
          <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md flex items-center gap-2 transition">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <span>Simpan Notula & Hasil Rapat</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Card Daftar Peserta Terdata -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
          <div>
            <h3 class="text-base font-bold text-slate-800">Daftar Peserta Terdata ({{ count($pesertas) }} Orang)</h3>
            <p class="text-xs text-slate-500">Nama-nama yang tercantum pada lembar Daftar Hadir resmi</p>
          </div>
          <a href="{{ route('situan.rapat.cetak.daftar-hadir', $rapat->id) }}" target="_blank" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Pratinjau Presensi
          </a>
        </div>
        <div class="max-h-96 overflow-y-auto">
          <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 sticky top-0">
              <tr>
                <th class="py-2.5 px-4 w-12 text-center">No</th>
                <th class="py-2.5 px-4">Nama Lengkap</th>
                <th class="py-2.5 px-4">Jabatan</th>
                <th class="py-2.5 px-4">NIP / NUPTK</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              @forelse($pesertas as $idx => $p)
              <tr class="hover:bg-slate-50">
                <td class="py-2 px-4 text-center font-medium text-slate-500">{{ $idx + 1 }}</td>
                <td class="py-2 px-4 font-semibold text-slate-800">{{ $p->nama }}</td>
                <td class="py-2 px-4 text-xs text-slate-600">{{ $p->jabatan ?? '-' }}</td>
                <td class="py-2 px-4 text-xs font-mono text-slate-500">{{ $p->nip ?? ($p->nuptk ?? '-') }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="py-4 text-center text-slate-400 italic">Belum ada peserta terdaftar.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Agenda Rapat -->
<div id="modal-edit-rapat" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
  <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl p-6">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
      <h3 class="text-lg font-bold text-slate-900">Edit Agenda Rapat</h3>
      <button type="button" onclick="document.getElementById('modal-edit-rapat').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
      </button>
    </div>

    <form action="{{ route('situan.rapat.update', $rapat->id) }}" method="POST" class="space-y-4">
      @csrf
      @method('PUT')

      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Judul Rapat <span class="text-rose-500">*</span></label>
        <input type="text" name="judul_rapat" value="{{ old('judul_rapat', $rapat->judul_rapat) }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tipe / Kategori Rapat <span class="text-rose-500">*</span></label>
          <select name="tipe_rapat" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="dinas" {{ $rapat->tipe_rapat == 'dinas' ? 'selected' : '' }}>Rapat Dinas Bulanan / Rutin</option>
            <option value="pleno_kelulusan" {{ $rapat->tipe_rapat == 'pleno_kelulusan' ? 'selected' : '' }}>Rapat Pleno Kelulusan</option>
            <option value="pleno_kenaikan" {{ $rapat->tipe_rapat == 'pleno_kenaikan' ? 'selected' : '' }}>Rapat Pleno Kenaikan Kelas</option>
            <option value="kombel" {{ $rapat->tipe_rapat == 'kombel' ? 'selected' : '' }}>Komunitas Belajar (Kombel) / MGMP</option>
            <option value="komite" {{ $rapat->tipe_rapat == 'komite' ? 'selected' : '' }}>Rapat Pleno Komite & Wali Murid</option>
            <option value="evaluasi" {{ $rapat->tipe_rapat == 'evaluasi' ? 'selected' : '' }}>Rapat Koordinasi & Evaluasi Program</option>
            <option value="lainnya" {{ $rapat->tipe_rapat == 'lainnya' ? 'selected' : '' }}>Rapat Khusus Lainnya</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Surat Undangan</label>
          <input type="text" name="nomor_surat" value="{{ old('nomor_surat', $rapat->nomor_surat) }}" class="w-full text-sm rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 font-mono">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal <span class="text-rose-500">*</span></label>
          <input type="date" name="tanggal_rapat" value="{{ old('tanggal_rapat', $rapat->tanggal_rapat->format('Y-m-d')) }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jam Mulai <span class="text-rose-500">*</span></label>
          <input type="text" name="jam_mulai" value="{{ old('jam_mulai', $rapat->jam_mulai) }}" required placeholder="08:30" class="w-full text-sm rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jam Selesai</label>
          <input type="text" name="jam_selesai" value="{{ old('jam_selesai', $rapat->jam_selesai) }}" placeholder="Selesai / 12:00" class="w-full text-sm rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tempat / Ruang <span class="text-rose-500">*</span></label>
        <input type="text" name="tempat" value="{{ old('tempat', $rapat->tempat) }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pimpinan Rapat</label>
          <select name="pimpinan_rapat_id" class="w-full text-sm rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Ketik Manual / Pilih Guru --</option>
            @foreach($gurus as $g)
            <option value="{{ $g->id }}" {{ $rapat->pimpinan_rapat_id == $g->id ? 'selected' : '' }}>{{ $g->nama }} ({{ $g->jabatan ?? 'Guru' }})</option>
            @endforeach
          </select>
          <input type="text" name="pimpinan_nama" value="{{ old('pimpinan_nama', $rapat->pimpinan_nama) }}" placeholder="Atau ketik nama jika pimpinan luar..." class="w-full text-xs rounded-lg border-slate-300 mt-1">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Notulis Rapat</label>
          <select name="notulis_id" class="w-full text-sm rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Ketik Manual / Pilih Guru --</option>
            @foreach($gurus as $g)
            <option value="{{ $g->id }}" {{ $rapat->notulis_id == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
            @endforeach
          </select>
          <input type="text" name="notulis_nama" value="{{ old('notulis_nama', $rapat->notulis_nama) }}" placeholder="Atau ketik nama notulis..." class="w-full text-xs rounded-lg border-slate-300 mt-1">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Peserta Sasaran <span class="text-rose-500">*</span></label>
        <select name="peserta_tipe" class="w-full text-sm rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
          <option value="semua_gtk" {{ $rapat->peserta_tipe == 'semua_gtk' ? 'selected' : '' }}>Seluruh GTK (Guru & Tenaga Kependidikan)</option>
          <option value="guru" {{ $rapat->peserta_tipe == 'guru' ? 'selected' : '' }}>Hanya Seluruh Dewan Guru</option>
          <option value="tendik" {{ $rapat->peserta_tipe == 'tendik' ? 'selected' : '' }}>Hanya Tenaga Kependidikan (TU & Pegawai)</option>
        </select>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Agenda Pembahasan</label>
        <textarea name="agenda" rows="3" class="w-full text-sm rounded-lg border-slate-300">{{ old('agenda', $rapat->agenda) }}</textarea>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status Agenda</label>
        <select name="status" class="w-full text-sm rounded-lg border-slate-300">
          <option value="dijadwalkan" {{ $rapat->status == 'dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
          <option value="selesai" {{ $rapat->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
          <option value="dibatalkan" {{ $rapat->status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
      </div>

      <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
        <button type="button" onclick="document.getElementById('modal-edit-rapat').classList.add('hidden')" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">Batal</button>
        <button type="submit" class="px-5 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Perbarui Agenda</button>
      </div>
    </form>
  </div>
</div>
@endsection
