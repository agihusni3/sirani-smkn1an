@php
  $withKop = false;
  $backUrl = route('situan.rapat.show', $rapat->id);
  $backLabel = 'Kembali ke Rapat';
  $tglSurat = $rapat->tanggal_rapat->subDays(2);
  $tgl = $rapat->tanggal_rapat;
  $tahunAngka = $tgl->year;
  $bulanText = $tgl->translatedFormat('F');
  $hariText = $tgl->translatedFormat('l');
  $tglAngka = $tgl->day;
@endphp

@extends('layouts.dokumen_a4')

@section('title', 'Paket Lengkap Administrasi Rapat - ' . $rapat->judul_rapat)
@section('toolbar_title', 'Paket Lengkap 4-in-1: Undangan + Daftar Hadir + Notula + Berita Acara')

@push('styles')
<style>
  .paket-page {
    page-break-after: always;
    margin-bottom: 25px;
    padding-bottom: 15px;
  }
  @media print {
    .paket-page {
      page-break-after: always !important;
      margin-bottom: 0 !important;
      padding-bottom: 0 !important;
    }
  }

  .table-presensi {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    font-size: 10pt;
  }
  .table-presensi th, .table-presensi td {
    border: 1px solid #1e293b;
    padding: 6px 8px;
  }
  .table-presensi th {
    background-color: #f1f5f9;
    text-align: center;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 9.5pt;
  }
  .ttd-col-1, .ttd-col-2 {
    width: 95px;
    vertical-align: top;
    padding-left: 6px;
    font-size: 9.5pt;
  }

  .notula-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
    font-size: 10.5pt;
  }
  .notula-table td {
    vertical-align: top;
    padding: 3px 0;
  }
  .notula-label {
    width: 155px;
    font-weight: 600;
  }
  .notula-sep {
    width: 15px;
    text-align: center;
  }
  .section-title {
    font-size: 11pt;
    font-weight: 700;
    text-transform: uppercase;
    margin-top: 12px;
    margin-bottom: 6px;
    border-bottom: 1.5px solid #0f172a;
    padding-bottom: 2px;
  }
  .section-content {
    font-size: 10.5pt;
    line-height: 1.6;
    text-align: justify;
    margin-bottom: 10px;
  }

  .ba-body {
    font-size: 11pt;
    line-height: 1.6;
    text-align: justify;
  }
  .ba-body p {
    text-indent: 36px;
    margin-bottom: 12px;
  }
</style>
@endpush

@section('content')

  {{-- ══════════════════════════════════════════════════════════════
       LEMBAR 1: SURAT UNDANGAN RESMI
       ══════════════════════════════════════════════════════════════ --}}
  <div class="paket-page">
    @include('partials.kop_surat')

    <table style="width:100%; border-collapse:collapse; margin-top:8px; margin-bottom:16px; font-size:11pt; line-height:1.5;">
      <tr>
        <td style="width:58%; vertical-align:top;">
          <table style="width:100%; border-collapse:collapse;">
            <tr>
              <td style="width:85px; vertical-align:top;">Nomor</td>
              <td style="width:12px; vertical-align:top;">:</td>
              <td style="vertical-align:top;"><strong>{{ $rapat->nomor_surat }}</strong></td>
            </tr>
            <tr>
              <td style="vertical-align:top;">Sifat</td>
              <td style="vertical-align:top;">:</td>
              <td style="vertical-align:top;">Penting / Dinas</td>
            </tr>
            <tr>
              <td style="vertical-align:top;">Lampiran</td>
              <td style="vertical-align:top;">:</td>
              <td style="vertical-align:top;">-</td>
            </tr>
            <tr>
              <td style="vertical-align:top;">Perihal</td>
              <td style="vertical-align:top;">:</td>
              <td style="vertical-align:top;"><strong>Undangan {{ $rapat->judul_rapat }}</strong></td>
            </tr>
          </table>
        </td>
        <td style="width:42%; vertical-align:top; padding-left:15px;">
          <div>{{ $sekolah->kecamatan ?? 'Air Naningan' }}, {{ $tglSurat->translatedFormat('d F Y') }}</div>
          <div style="margin-top:10px;">
            Kepada Yth.<br />
            <strong>
              @if($rapat->peserta_tipe == 'guru')
                Bapak/Ibu Dewan Guru
              @elseif($rapat->peserta_tipe == 'tendik')
                Bapak/Ibu Tenaga Kependidikan (TU)
              @else
                Bapak/Ibu Pendidik & Tenaga Kependidikan
              @endif
            </strong><br />
            SMK Negeri 1 Air Naningan<br />
            di — Tempat
          </div>
        </td>
      </tr>
    </table>

    <div style="font-size:11pt; line-height:1.6; text-align:justify; margin-bottom:16px;">
      <p style="text-indent: 36px; margin-bottom:10px;">
        Dengan hormat, sehubungan dengan agenda kerja kedinasan di lingkungan SMK Negeri 1 Air Naningan, mengharap kehadiran Bapak/Ibu pada rapat dinas yang akan diselenggarakan pada:
      </p>

      <table style="width:100%; border-collapse:collapse; margin:10px 0 12px 28px; font-size:11pt; line-height:1.6;">
        <tr>
          <td style="width:120px; font-weight:600;">Hari / Tanggal</td>
          <td style="width:15px; text-align:center;">:</td>
          <td><strong>{{ $rapat->tanggal_rapat->translatedFormat('l, d F Y') }}</strong></td>
        </tr>
        <tr>
          <td style="font-weight:600;">Waktu</td>
          <td style="text-align:center;">:</td>
          <td>Pukul {{ $rapat->jam_mulai }} WIB @if($rapat->jam_selesai) s.d. {{ $rapat->jam_selesai }} WIB @else s.d. Selesai @endif</td>
        </tr>
        <tr>
          <td style="font-weight:600;">Tempat</td>
          <td style="text-align:center;">:</td>
          <td>{{ $rapat->tempat }}</td>
        </tr>
        <tr>
          <td style="font-weight:600; vertical-align:top;">Agenda Rapat</td>
          <td style="text-align:center; vertical-align:top;">:</td>
          <td style="vertical-align:top;">
            <strong>{{ $rapat->judul_rapat }}</strong>
            @if($rapat->agenda)
              <div style="margin-top:4px; font-size:10.5pt; color:#334155;">
                {!! nl2br(e($rapat->agenda)) !!}
              </div>
            @endif
          </td>
        </tr>
        <tr>
          <td style="font-weight:600;">Pimpinan Rapat</td>
          <td style="text-align:center;">:</td>
          <td>{{ $rapat->pimpinan_display }} ({{ $rapat->pimpinan_jabatan ?? 'Pimpinan Rapat' }})</td>
        </tr>
      </table>

      <p style="text-indent: 36px; margin-bottom:10px;">
        Mengingat pentingnya agenda pembahasan ini demi kelancaran manajemen dan mutu pendidikan sekolah, kami mengharapkan kehadiran Bapak/Ibu tepat pada waktunya dan tidak mewakilkan tanpa izin pimpinan.
      </p>
      <p style="text-indent: 36px; margin-bottom:0;">
        Demikian surat undangan ini kami sampaikan, atas perhatian dan kerja sama yang baik diucapkan terima kasih.
      </p>
    </div>

    <table style="width:100%; border-collapse:collapse; font-size:11pt; margin-top:20px;">
      <tr>
        <td style="width:55%;"></td>
        <td style="width:45%; text-align:center;">
          <div>Air Naningan, {{ $tglSurat->translatedFormat('d F Y') }}</div>
          <div style="margin-top:2px; font-weight:600;">Kepala SMK Negeri 1 Air Naningan,</div>
          <div class="ttd-digital-only" style="margin:8px auto; width:68px; height:68px;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('VALID-UNDANGAN-SMKN1AN-' . $rapat->nomor_surat) }}" alt="QR Keabsahan" style="width:68px; height:68px; display:block; margin:0 auto;" />
          </div>
          <div class="ttd-basah-only ttd-space" style="height:55px; display:none;"></div>
          <div style="font-weight:bold; text-decoration:underline;">{{ $kepsek->nama ?? 'Kepala Sekolah' }}</div>
          <div style="font-size:10pt;">NIP. {{ $kepsek->nip ?? '-' }}</div>
        </td>
      </tr>
    </table>
  </div>

  {{-- ══════════════════════════════════════════════════════════════
       LEMBAR 2: DAFTAR HADIR PESERTA RAPAT
       ══════════════════════════════════════════════════════════════ --}}
  <div class="paket-page">
    @include('partials.kop_surat')

    <div style="text-align:center; margin-bottom:12px;">
      <h3 style="margin:0; font-size:12pt; font-weight:800; text-transform:uppercase; text-decoration:underline; letter-spacing:0.5px;">
        DAFTAR HADIR PESERTA RAPAT
      </h3>
      <div style="font-size:10.5pt; font-weight:600; margin-top:3px; color:#1e293b;">
        {{ $rapat->judul_rapat }}
      </div>
    </div>

    <table style="width:100%; border-collapse:collapse; margin-bottom:10px; font-size:10pt; line-height:1.4;">
      <tr>
        <td style="width:90px; font-weight:600;">Hari / Tanggal</td>
        <td style="width:12px; text-align:center;">:</td>
        <td style="width:40%;">{{ $rapat->tanggal_rapat->translatedFormat('l, d F Y') }}</td>
        <td style="width:60px; font-weight:600;">Waktu</td>
        <td style="width:12px; text-align:center;">:</td>
        <td>{{ $rapat->jam_mulai }} WIB @if($rapat->jam_selesai) s.d. {{ $rapat->jam_selesai }} WIB @else s.d. Selesai @endif</td>
      </tr>
      <tr>
        <td style="font-weight:600;">Tempat</td>
        <td style="text-align:center;">:</td>
        <td>{{ $rapat->tempat }}</td>
        <td style="font-weight:600;">Pimpinan</td>
        <td style="text-align:center;">:</td>
        <td>{{ $rapat->pimpinan_display }}</td>
      </tr>
    </table>

    <table class="table-presensi">
      <thead>
        <tr>
          <th style="width:35px;">No</th>
          <th>Nama Lengkap & NIP</th>
          <th style="width:150px;">Jabatan / Tugas</th>
          <th colspan="2" style="width:190px;">Tanda Tangan</th>
          <th style="width:80px;">Ket.</th>
        </tr>
      </thead>
      <tbody>
        @forelse($pesertas as $index => $peserta)
          @php
            $no = $index + 1;
            $isOdd = ($no % 2 !== 0);
          @endphp
          <tr>
            <td style="text-align:center; font-weight:600;">{{ $no }}</td>
            <td>
              <div style="font-weight:700; color:#0f172a;">{{ $peserta->nama }}</div>
              <div style="font-size:8.5pt; color:#475569;">NIP: {{ $peserta->nip ?? '-' }}</div>
            </td>
            <td style="font-size:9pt;">{{ $peserta->jabatan ?? 'Guru / Staf' }}</td>
            <td class="ttd-col-1">
              @if($isOdd)
                <span>{{ $no }}. .....................</span>
              @endif
            </td>
            <td class="ttd-col-2">
              @if(!$isOdd)
                <span>{{ $no }}. .....................</span>
              @endif
            </td>
            <td style="text-align:center; font-size:9pt;"></td>
          </tr>
        @empty
          @for($i = 1; $i <= 15; $i++)
          <tr>
            <td style="text-align:center;">{{ $i }}</td>
            <td></td>
            <td></td>
            <td class="ttd-col-1">@if($i % 2 !== 0) {{ $i }}. ......... @endif</td>
            <td class="ttd-col-2">@if($i % 2 === 0) {{ $i }}. ......... @endif</td>
            <td></td>
          </tr>
          @endfor
        @endforelse
      </tbody>
    </table>

    <table style="width:100%; border-collapse:collapse; font-size:10.5pt; margin-top:15px;">
      <tr>
        <td style="width:50%; text-align:center;">
          <div>Mengetahui / Memimpin,</div>
          <div style="font-weight:600;">{{ $rapat->pimpinan_jabatan ?? 'Pimpinan Rapat' }}</div>
          <div class="ttd-digital-only" style="margin:6px auto; width:64px; height:64px;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('VALID-PRESENSI-PIMPINAN-' . $rapat->id) }}" alt="QR Keabsahan" style="width:64px; height:64px; display:block; margin:0 auto;" />
          </div>
          <div class="ttd-basah-only ttd-space" style="height:50px; display:none;"></div>
          <div style="font-weight:700; text-decoration:underline;">{{ $rapat->pimpinan_display }}</div>
          <div style="font-size:9.5pt;">NIP. {{ $rapat->pimpinan->nip ?? '-' }}</div>
        </td>
        <td style="width:50%; text-align:center;">
          <div>Air Naningan, {{ $rapat->tanggal_rapat->translatedFormat('d F Y') }}</div>
          <div style="font-weight:600;">Notulis Rapat,</div>
          <div class="ttd-digital-only" style="margin:6px auto; width:64px; height:64px;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('VALID-PRESENSI-NOTULIS-' . $rapat->id) }}" alt="QR Keabsahan" style="width:64px; height:64px; display:block; margin:0 auto;" />
          </div>
          <div class="ttd-basah-only ttd-space" style="height:50px; display:none;"></div>
          <div style="font-weight:700; text-decoration:underline;">{{ $rapat->notulis_display }}</div>
          <div style="font-size:9.5pt;">NIP. {{ $rapat->notulis->nip ?? '-' }}</div>
        </td>
      </tr>
    </table>
  </div>

  {{-- ══════════════════════════════════════════════════════════════
       LEMBAR 3: NOTULA RAPAT DINAS
       ══════════════════════════════════════════════════════════════ --}}
  <div class="paket-page">
    @include('partials.kop_surat')

    <div style="text-align:center; margin-bottom:14px;">
      <h3 style="margin:0; font-size:13pt; font-weight:800; text-transform:uppercase; text-decoration:underline; letter-spacing:0.5px;">
        NOTULA RAPAT DINAS
      </h3>
      <div style="font-size:10.5pt; font-weight:700; margin-top:3px; color:#1e293b;">
        {{ $rapat->judul_rapat }}
      </div>
      <div style="font-size:9.5pt; color:#475569; margin-top:1px;">
        Nomor Agenda: {{ $rapat->nomor_surat ?? '-' }}
      </div>
    </div>

    <table class="notula-table">
      <tr>
        <td class="notula-label">Hari / Tanggal</td>
        <td class="notula-sep">:</td>
        <td><strong>{{ $rapat->tanggal_rapat->translatedFormat('l, d F Y') }}</strong></td>
      </tr>
      <tr>
        <td class="notula-label">Waktu Rapat</td>
        <td class="notula-sep">:</td>
        <td>Pukul {{ $rapat->jam_mulai }} WIB @if($rapat->jam_selesai) s.d. {{ $rapat->jam_selesai }} WIB @else s.d. Selesai @endif</td>
      </tr>
      <tr>
        <td class="notula-label">Tempat Rapat</td>
        <td class="notula-sep">:</td>
        <td>{{ $rapat->tempat }}</td>
      </tr>
      <tr>
        <td class="notula-label">Pimpinan Rapat</td>
        <td class="notula-sep">:</td>
        <td>{{ $rapat->pimpinan_display }} ({{ $rapat->pimpinan_jabatan ?? 'Pimpinan' }})</td>
      </tr>
      <tr>
        <td class="notula-label">Notulis Rapat</td>
        <td class="notula-sep">:</td>
        <td>{{ $rapat->notulis_display }}</td>
      </tr>
      <tr>
        <td class="notula-label">Kehadiran Peserta</td>
        <td class="notula-sep">:</td>
        <td>
          Hadir: <strong>{{ $rapat->jumlah_hadir ?? '-' }}</strong> orang &nbsp;|&nbsp; 
          Tidak Hadir: <strong>{{ $rapat->jumlah_tidak_hadir ?? 0 }}</strong> orang
        </td>
      </tr>
      <tr>
        <td class="notula-label">Agenda / Acara</td>
        <td class="notula-sep">:</td>
        <td>{{ $rapat->agenda ?: $rapat->judul_rapat }}</td>
      </tr>
    </table>

    <div class="section-title">I. Jalannya Rapat & Risalah Pembahasan</div>
    <div class="section-content">
      @if($rapat->jalannya_acara)
        {!! nl2br(e($rapat->jalannya_acara)) !!}
      @else
        <ol style="margin-left: 20px; padding-left: 0; margin-top: 4px; margin-bottom: 4px;">
          <li>Pembukaan oleh pembawa acara / notulis pada pukul {{ $rapat->jam_mulai }} WIB.</li>
          <li>Pengarahan dan pemaparan materi inti rapat oleh Pimpinan Rapat ({{ $rapat->pimpinan_display }}).</li>
          <li>Sesi diskusi dan saran masukan peserta forum rapat dinas.</li>
          <li>Perumusan kesepakatan bersama dan penutupan rapat.</li>
        </ol>
      @endif
    </div>

    <div class="section-title">II. Hasil Keputusan & Kesimpulan Rapat</div>
    <div class="section-content">
      @if($rapat->hasil_keputusan)
        {!! nl2br(e($rapat->hasil_keputusan)) !!}
      @else
        <div style="font-style: italic; color:#64748b; padding: 6px 0;">
          (Hasil keputusan rapat dicatat dalam lampiran/dokumen berita acara).
        </div>
      @endif
    </div>

    @if($rapat->tindak_lanjut || $rapat->catatan_khusus)
    <div class="section-title">III. Rencana Tindak Lanjut & Catatan</div>
    <div class="section-content">
      @if($rapat->tindak_lanjut)
        <div><strong>Tindak Lanjut:</strong> {!! nl2br(e($rapat->tindak_lanjut)) !!}</div>
      @endif
      @if($rapat->catatan_khusus)
        <div style="margin-top:4px;"><strong>Catatan Khusus:</strong> {!! nl2br(e($rapat->catatan_khusus)) !!}</div>
      @endif
    </div>
    @endif

    <table style="width:100%; border-collapse:collapse; font-size:10.5pt; margin-top:15px;">
      <tr>
        <td style="width:50%; text-align:center;">
          <div>Mengetahui,</div>
          <div style="font-weight:600;">{{ $rapat->pimpinan_jabatan ?? 'Pimpinan Rapat' }}</div>
          <div class="ttd-digital-only" style="margin:6px auto; width:64px; height:64px;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('VALID-NOTULA-PIMPINAN-' . $rapat->id) }}" alt="QR Keabsahan" style="width:64px; height:64px; display:block; margin:0 auto;" />
          </div>
          <div class="ttd-basah-only ttd-space" style="height:50px; display:none;"></div>
          <div style="font-weight:700; text-decoration:underline;">{{ $rapat->pimpinan_display }}</div>
          <div style="font-size:9.5pt;">NIP. {{ $rapat->pimpinan->nip ?? '-' }}</div>
        </td>
        <td style="width:50%; text-align:center;">
          <div>Air Naningan, {{ $rapat->tanggal_rapat->translatedFormat('d F Y') }}</div>
          <div style="font-weight:600;">Notulis Rapat,</div>
          <div class="ttd-digital-only" style="margin:6px auto; width:64px; height:64px;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('VALID-NOTULA-NOTULIS-' . $rapat->id) }}" alt="QR Keabsahan" style="width:64px; height:64px; display:block; margin:0 auto;" />
          </div>
          <div class="ttd-basah-only ttd-space" style="height:50px; display:none;"></div>
          <div style="font-weight:700; text-decoration:underline;">{{ $rapat->notulis_display }}</div>
          <div style="font-size:9.5pt;">NIP. {{ $rapat->notulis->nip ?? '-' }}</div>
        </td>
      </tr>
    </table>
  </div>

  {{-- ══════════════════════════════════════════════════════════════
       LEMBAR 4: BERITA ACARA RAPAT DINAS
       ══════════════════════════════════════════════════════════════ --}}
  <div class="paket-page">
    @include('partials.kop_surat')

    <div style="text-align:center; margin-bottom:18px;">
      <h3 style="margin:0; font-size:13pt; font-weight:800; text-transform:uppercase; text-decoration:underline; letter-spacing:1px;">
        BERITA ACARA
      </h3>
      <div style="font-size:10.5pt; font-weight:700; margin-top:3px; color:#1e293b;">
        PELAKSANAAN {{ strtoupper($rapat->judul_rapat) }}
      </div>
      <div style="font-size:10pt; font-family:monospace; margin-top:2px;">
        Nomor: 421.5/BA-{{ str_pad($rapat->id, 3, '0', STR_PAD_LEFT) }}/V.01/DP.2/{{ $tahunAngka }}
      </div>
    </div>

    <div class="ba-body">
      <p>
        Pada hari ini <strong>{{ $hariText }}</strong>, tanggal <strong>{{ $tglAngka }}</strong> bulan <strong>{{ $bulanText }}</strong> tahun <strong>{{ $tahunAngka }}</strong>, bertempat di <strong>{{ $rapat->tempat }}</strong> SMK Negeri 1 Air Naningan, telah diselenggarakan rapat dinas resmi dengan agenda pokok mengenai:
      </p>

      <div style="text-align:center; font-weight:700; font-size:11.5pt; margin:8px 0 14px 0; padding:6px; background:#f8fafc; border:1px solid #cbd5e1; border-radius:6px;">
        "{{ $rapat->judul_rapat }}"
      </div>

      <p>
        Rapat tersebut dipimpin langsung oleh <strong>{{ $rapat->pimpinan_display }}</strong> selaku {{ $rapat->pimpinan_jabatan ?? 'Pimpinan Rapat' }} serta dihadiri oleh Dewan Guru dan Tenaga Kependidikan SMK Negeri 1 Air Naningan sebanyak <strong>{{ $rapat->jumlah_hadir ?? count($pesertas) }}</strong> orang <em>(daftar hadir terlampir sebagai bagian yang tidak terpisahkan dari berita acara ini)</em>.
      </p>

      <p>
        Setelah mendengar pemaparan, pengarahan pimpinan, serta mempertimbangkan saran, masukan, dan hasil musyawarah mufakat seluruh peserta rapat, maka dengan ini <strong>DITETAPKAN DAN DISEPAKATI</strong> hal-hal sebagai berikut:
      </p>

      <div style="margin: 12px 0 16px 20px;">
        @if($rapat->hasil_keputusan)
          <div style="white-space: pre-line; line-height:1.6; font-weight:500;">{{ $rapat->hasil_keputusan }}</div>
        @else
          <ol style="margin-left: 20px; padding-left: 0;">
            <li>Menerima dan menyetujui seluruh rangkaian materi pembahasan rapat dinas secara bulat dan mufakat.</li>
            <li>Menetapkan keputusan rapat sebagai pedoman resmi operasional kedinasan di SMK Negeri 1 Air Naningan.</li>
            <li>Menginstruksikan kepada seluruh unsur terkait untuk menindaklanjuti hasil keputusan sesuai dengan tanggung jawab masing-masing.</li>
          </ol>
        @endif
      </div>

      <p>
        Demikian Berita Acara ini dibuat dan ditandatangani oleh pimpinan rapat serta perwakilan peserta rapat pada hari dan tanggal sebagaimana tersebut di atas, dengan penuh tanggung jawab untuk dipergunakan sebagaimana mestinya.
      </p>
    </div>

    <table style="width:100%; border-collapse:collapse; font-size:10.5pt; margin-top:20px;">
      <tr>
        <td style="width:50%; text-align:center;">
          <div>Notulis / Saksi,</div>
          <div style="font-weight:600;">Guru / Staf Pelaksana</div>
          <div class="ttd-digital-only" style="margin:6px auto; width:64px; height:64px;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('VALID-BA-SAKSI-' . $rapat->id) }}" alt="QR Keabsahan" style="width:64px; height:64px; display:block; margin:0 auto;" />
          </div>
          <div class="ttd-basah-only ttd-space" style="height:50px; display:none;"></div>
          <div style="font-weight:700; text-decoration:underline;">{{ $rapat->notulis_display }}</div>
          <div style="font-size:9.5pt;">NIP. {{ $rapat->notulis->nip ?? '-' }}</div>
        </td>
        <td style="width:50%; text-align:center;">
          <div>Air Naningan, {{ $tgl->translatedFormat('d F Y') }}</div>
          <div style="font-weight:600;">{{ $rapat->pimpinan_jabatan ?? 'Pimpinan Rapat' }},</div>
          <div class="ttd-digital-only" style="margin:6px auto; width:64px; height:64px;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('VALID-BA-PIMPINAN-' . $rapat->id) }}" alt="QR Keabsahan" style="width:64px; height:64px; display:block; margin:0 auto;" />
          </div>
          <div class="ttd-basah-only ttd-space" style="height:50px; display:none;"></div>
          <div style="font-weight:700; text-decoration:underline;">{{ $rapat->pimpinan_display }}</div>
          <div style="font-size:9.5pt;">NIP. {{ $rapat->pimpinan->nip ?? '-' }}</div>
        </td>
      </tr>
    </table>
  </div>

@endsection
