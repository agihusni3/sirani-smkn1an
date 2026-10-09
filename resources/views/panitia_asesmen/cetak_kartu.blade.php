@php
  $withKop = false;
  $backUrl = route('panitia-asesmen.administrasi');
  $backLabel = 'Kembali ke Administrasi Ujian';
@endphp

@extends('layouts.dokumen_a4')

@section('title', 'Kartu Peserta Ujian - ' . ($periode->nama_event ?? 'Asesmen'))
@section('toolbar_title', 'Kartu Peserta Ujian / Asesmen Resmi (A4)')

@push('styles')
<style>
  .kartu-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-top: 5px;
  }
  .kartu-box {
    border: 1.5px solid #1e293b;
    border-radius: 6px;
    padding: 8px 10px;
    background: #ffffff;
    box-sizing: border-box;
    page-break-inside: avoid;
    font-size: 8.5pt;
    line-height: 1.35;
  }
  .kartu-header {
    display: flex;
    align-items: center;
    gap: 8px;
    border-bottom: 1.5px solid #1e293b;
    padding-bottom: 5px;
    margin-bottom: 6px;
  }
  .kartu-logo {
    width: 34px;
    height: 34px;
  }
  .kartu-title {
    text-align: center;
    flex: 1;
  }
  .kartu-title h6 {
    margin: 0;
    font-size: 8.5pt;
    font-weight: 800;
    text-transform: uppercase;
    line-height: 1.2;
  }
  .kartu-title small {
    font-size: 7pt;
    color: #475569;
  }
  .kartu-body {
    display: flex;
    gap: 8px;
  }
  .kartu-foto {
    width: 60px;
    height: 75px;
    border: 1px dashed #94a3b8;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 7.5pt;
    color: #94a3b8;
    text-align: center;
    border-radius: 4px;
    flex-shrink: 0;
  }
  .kartu-info {
    flex: 1;
  }
  .kartu-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 8.5pt;
  }
  .kartu-table td {
    vertical-align: top;
    padding: 1px 0;
  }
  .kartu-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-top: 6px;
    border-top: 1px solid #e2e8f0;
    padding-top: 4px;
    font-size: 7.5pt;
  }
</style>
@endpush

@section('content')
  <div class="kartu-grid">
    @forelse($siswas as $s)
    <div class="kartu-box">
      <div class="kartu-header">
        <img src="{{ asset('img/logo-provinsi.png') }}" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/b/b2/Lambang_Provinsi_Lampung.png'" class="kartu-logo" alt="Logo">
        <div class="kartu-title">
          <h6>SMK NEGERI 1 AIR NANINGAN</h6>
          <small>KARTU PESERTA {{ strtoupper($periode->nama_event ?? 'ASESMEN SEKOLAH') }}</small>
        </div>
      </div>

      <div class="kartu-body">
        <div class="kartu-foto">
          <span>Pas Foto<br>2 x 3</span>
        </div>
        <div class="kartu-info">
          <table class="kartu-table">
            <tr>
              <td style="width:75px; font-weight:600;">No. Peserta</td>
              <td style="width:8px;">:</td>
              <td style="font-weight:700; font-family:monospace; color:#0f172a;">{{ $s->nisn ?: ('PES-' . str_pad($s->id, 4, '0', STR_PAD_LEFT)) }}</td>
            </tr>
            <tr>
              <td style="font-weight:600;">Nama Siswa</td>
              <td>:</td>
              <td style="font-weight:700; text-transform:uppercase;">{{ $s->nama }}</td>
            </tr>
            <tr>
              <td style="font-weight:600;">NISN / NIS</td>
              <td>:</td>
              <td>{{ $s->nisn ?? '-' }} / {{ $s->nis ?? '-' }}</td>
            </tr>
            <tr>
              <td style="font-weight:600;">Kelas / Rombel</td>
              <td>:</td>
              <td style="font-weight:600;">{{ $s->rombel->nama_rombel ?? '-' }}</td>
            </tr>
            <tr>
              <td style="font-weight:600;">Username CBT</td>
              <td>:</td>
              <td style="font-family:monospace; font-weight:700; color:#1e40af;">{{ $s->nisn ?: $s->nis }}</td>
            </tr>
            <tr>
              <td style="font-weight:600;">Password CBT</td>
              <td>:</td>
              <td style="font-family:monospace; font-weight:700; color:#b91c1c;">123456</td>
            </tr>
          </table>
        </div>
      </div>

      <div class="kartu-footer">
        <div>
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data={{ urlencode('CBT-SMKN1AN-' . ($s->nisn ?? $s->id)) }}" style="width:36px; height:36px; display:block;" alt="QR">
        </div>
        <div style="text-align:center;">
          <div>Air Naningan, {{ date('d/m/Y') }}</div>
          <div style="font-weight:700;">Kepala Sekolah / Panitia,</div>
          <div style="height:25px;"></div>
          <div style="font-weight:700; text-decoration:underline;">Drs. H. Ahmad Fauzi, M.Pd.</div>
        </div>
      </div>
    </div>
    @empty
    <div style="grid-column: span 2; text-align:center; padding: 40px; color:#64748b;">
      Tidak ada data siswa pada filter rombel yang dipilih.
    </div>
    @endforelse
  </div>
@endsection
