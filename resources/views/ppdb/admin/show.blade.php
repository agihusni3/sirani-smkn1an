<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Verifikasi Pendaftar {{ $pendaftar->no_pendaftaran ?? $pendaftar->nomor_pendaftaran }} — PPDB 2026 SMKN 1 Air Naningan</title>
  @include('partials.styles')
</head>
<body>
<div class="app-container">
  @include('partials.sidebar_ppdb')
  <main class="main-content">
    
    {{-- HEADER BAR --}}
    <div class="panel no-print" style="background:var(--bg-2); border:1px solid var(--border); padding:12px 18px; margin-bottom:14px; border-radius:var(--r-md); box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <a href="{{ route('admin.ppdb.index') }}" class="btn btn-sm" style="background:var(--surface); border:1px solid var(--border); color:var(--text); padding:5px 10px; border-radius:6px; font-size:12px;">
            <i class="bi bi-arrow-left"></i> Kembali
          </a>
          <h1 style="margin:0; font-size:16px; font-weight:900; color:var(--text); display:inline-flex; align-items:center; gap:8px;">
            Verifikasi Calon Siswa: {{ $pendaftar->nama_lengkap }} ({{ $pendaftar->nomor_pendaftaran }})
          </h1>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
          <a href="{{ route('ppdb.cetak', $pendaftar->nomor_pendaftaran) }}" target="_blank" class="btn btn-sm" style="background:#0284c7; color:#fff; font-weight:700; border-radius:6px; font-size:12px; padding:6px 14px;">
            <i class="bi bi-printer-fill"></i> Cetak Kartu Pendaftaran
          </a>
        </div>
      </div>
    </div>

    @if(session('success'))
      <div class="panel" style="background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:12px 16px; margin-bottom:14px; border-radius:var(--r-sm); font-size:13px; font-weight:700;">
        <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i> {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="panel" style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; margin-bottom:14px; border-radius:var(--r-sm); font-size:13px; font-weight:700;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i> {{ session('error') }}
      </div>
    @endif

    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:16px;">
      
      {{-- KOLOM KIRI: BIODATA & BERKAS --}}
      <div style="display:flex; flex-direction:column; gap:16px;">
        
        {{-- BIODATA DETAIL --}}
        <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); border-radius:var(--r-sm); padding:16px;">
          <h3 style="font-size:14px; font-weight:800; margin-bottom:14px; color:var(--text); border-bottom:1px solid var(--border); padding-bottom:8px;">
            <i class="bi bi-person-lines-fill" style="color:#6366f1;"></i> Data Diri Calon Siswa
          </h3>

          <table style="width:100%; font-size:12.5px; border-collapse:collapse;">
            <tr>
              <td style="width:160px; padding:6px 0; color:var(--text-3);">Nomor Pendaftaran</td>
              <td style="padding:6px 0; font-family:var(--font-mono); font-weight:800; color:#4338ca;">{{ $pendaftar->nomor_pendaftaran }}</td>
            </tr>
            <tr>
              <td style="padding:6px 0; color:var(--text-3);">NISN</td>
              <td style="padding:6px 0; font-weight:700;">{{ $pendaftar->nisn }}</td>
            </tr>
            <tr>
              <td style="padding:6px 0; color:var(--text-3);">NIK</td>
              <td style="padding:6px 0;">{{ $pendaftar->nik ?? '-' }}</td>
            </tr>
            <tr>
              <td style="padding:6px 0; color:var(--text-3);">Nama Lengkap</td>
              <td style="padding:6px 0; font-weight:800; font-size:13.5px;">{{ $pendaftar->nama_lengkap }}</td>
            </tr>
            <tr>
              <td style="padding:6px 0; color:var(--text-3);">Jenis Kelamin</td>
              <td style="padding:6px 0;">{{ $pendaftar->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
              <td style="padding:6px 0; color:var(--text-3);">Tempat, Tanggal Lahir</td>
              <td style="padding:6px 0;">{{ $pendaftar->tempat_lahir }}, {{ \Carbon\Carbon::parse($pendaftar->tanggal_lahir)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
              <td style="padding:6px 0; color:var(--text-3);">Asal Sekolah (SMP)</td>
              <td style="padding:6px 0;">{{ $pendaftar->asal_sekolah }} (Lulus: {{ $pendaftar->tahun_lulus }})</td>
            </tr>
            <tr>
              <td style="padding:6px 0; color:var(--text-3);">Alamat Lengkap</td>
              <td style="padding:6px 0;">{{ $pendaftar->alamat_lengkap }}</td>
            </tr>
            <tr>
              <td style="padding:6px 0; color:var(--text-3);">Hobi Calon Siswa</td>
              <td style="padding:6px 0;"><strong>{{ $pendaftar->hobi ?: '-' }}</strong></td>
            </tr>
            <tr>
              <td style="padding:6px 0; color:var(--text-3);">Organisasi Diminati</td>
              <td style="padding:6px 0;"><span class="badge" style="background:#e0e7ff; color:#3730a3; padding:3px 8px; border-radius:4px;">{{ $pendaftar->organisasi_minat ?: '-' }}</span></td>
            </tr>
            <tr>
              <td style="padding:6px 0; color:var(--text-3);">No. WhatsApp Siswa (Notifikasi Seleksi)</td>
              <td style="padding:6px 0;">
                @if($pendaftar->no_hp_siswa)
                  {{ $pendaftar->no_hp_siswa }}
                  <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pendaftar->no_hp_siswa) }}" target="_blank" style="margin-left:6px; color:#16a34a; font-size:11px; text-decoration:none;">
                    <i class="bi bi-whatsapp"></i> Chat
                  </a>
                @else
                  -
                @endif
              </td>
            </tr>
          </table>
        </div>

        {{-- DATA ORANG TUA (AYAH & IBU TERPISAH) --}}
        <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); border-radius:var(--r-sm); padding:16px;">
          <h3 style="font-size:14px; font-weight:800; margin-bottom:14px; color:var(--text); border-bottom:1px solid var(--border); padding-bottom:8px;">
            <i class="bi bi-people-fill" style="color:#f59e0b;"></i> Data Orang Tua Calon Siswa (Ayah & Ibu)
          </h3>

          <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:14px; margin-bottom:14px;">
            {{-- Kartu Ayah --}}
            <div style="border:1px solid var(--border); border-radius:8px; padding:12px; background:var(--surface);">
              <div style="font-size:12px; font-weight:800; color:#2563eb; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
                <i class="bi bi-person-badge"></i> Data Ayah Kandung
              </div>
              <table style="width:100%; font-size:11.5px; border-collapse:collapse;">
                <tr>
                  <td style="width:110px; padding:4px 0; color:var(--text-3);">Nama Lengkap</td>
                  <td style="padding:4px 0; font-weight:700;">{{ $pendaftar->nama_ayah ?: '-' }}</td>
                </tr>
                <tr>
                  <td style="padding:4px 0; color:var(--text-3);">Pekerjaan</td>
                  <td style="padding:4px 0;">{{ $pendaftar->pekerjaan_ayah ?: '-' }}</td>
                </tr>
                <tr>
                  <td style="padding:4px 0; color:var(--text-3);">Pendidikan</td>
                  <td style="padding:4px 0;">{{ $pendaftar->pendidikan_ayah ?: '-' }}</td>
                </tr>
                <tr>
                  <td style="padding:4px 0; color:var(--text-3);">No HP / WhatsApp</td>
                  <td style="padding:4px 0;">
                    @if($pendaftar->no_hp_ayah)
                      <strong>{{ $pendaftar->no_hp_ayah }}</strong>
                      <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pendaftar->no_hp_ayah) }}" target="_blank" style="margin-left:6px; color:#16a34a; font-size:11px; text-decoration:none;">
                        <i class="bi bi-whatsapp"></i> Chat
                      </a>
                    @else
                      -
                    @endif
                  </td>
                </tr>
              </table>
            </div>

            {{-- Kartu Ibu --}}
            <div style="border:1px solid var(--border); border-radius:8px; padding:12px; background:var(--surface);">
              <div style="font-size:12px; font-weight:800; color:#db2777; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
                <i class="bi bi-person-heart"></i> Data Ibu Kandung
              </div>
              <table style="width:100%; font-size:11.5px; border-collapse:collapse;">
                <tr>
                  <td style="width:110px; padding:4px 0; color:var(--text-3);">Nama Lengkap</td>
                  <td style="padding:4px 0; font-weight:700;">{{ $pendaftar->nama_ibu ?: '-' }}</td>
                </tr>
                <tr>
                  <td style="padding:4px 0; color:var(--text-3);">Pekerjaan</td>
                  <td style="padding:4px 0;">{{ $pendaftar->pekerjaan_ibu ?: '-' }}</td>
                </tr>
                <tr>
                  <td style="padding:4px 0; color:var(--text-3);">Pendidikan</td>
                  <td style="padding:4px 0;">{{ $pendaftar->pendidikan_ibu ?: '-' }}</td>
                </tr>
                <tr>
                  <td style="padding:4px 0; color:var(--text-3);">No HP / WhatsApp</td>
                  <td style="padding:4px 0;">
                    @if($pendaftar->no_hp_ibu)
                      <strong>{{ $pendaftar->no_hp_ibu }}</strong>
                      <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pendaftar->no_hp_ibu) }}" target="_blank" style="margin-left:6px; color:#16a34a; font-size:11px; text-decoration:none;">
                        <i class="bi bi-whatsapp"></i> Chat
                      </a>
                    @else
                      -
                    @endif
                  </td>
                </tr>
              </table>
            </div>
          </div>

          {{-- Kontak Notifikasi Siswa --}}
          <div style="background:rgba(37, 99, 235, 0.06); border:1px solid rgba(37, 99, 235, 0.2); border-radius:6px; padding:10px 14px; display:flex; justify-content:space-between; align-items:center; font-size:12px;">
            <div>
              <span style="color:var(--text-3);">No. WhatsApp Siswa (Tujuan Notifikasi Seleksi / Kelulusan):</span>
              <strong style="margin-left:6px; color:#2563eb;">{{ $pendaftar->no_hp_siswa ?: ($pendaftar->no_hp_ortu ?: '-') }}</strong>
            </div>
            @php
              $targetWa = $pendaftar->no_hp_siswa ?: $pendaftar->no_hp_ortu;
            @endphp
            @if($targetWa)
              <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $targetWa) }}" target="_blank" class="btn btn-sm" style="background:#16a34a; color:#fff; font-size:11px; padding:4px 10px; border-radius:4px; text-decoration:none;">
                <i class="bi bi-whatsapp"></i> Chat WhatsApp Siswa
              </a>
            @endif
          </div>
        </div>

        {{-- BERKAS LAMPIRAN --}}
        <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); border-radius:var(--r-sm); padding:16px;">
          <h3 style="font-size:14px; font-weight:800; margin-bottom:14px; color:var(--text); border-bottom:1px solid var(--border); padding-bottom:8px;">
            <i class="bi bi-paperclip" style="color:#0ea5e9;"></i> Lampiran Berkas Persyaratan
          </h3>

          <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:14px;">
            {{-- Pas Foto --}}
            <div style="border:1px solid var(--border); border-radius:8px; padding:10px; text-align:center;">
              <div style="font-size:11px; font-weight:700; color:var(--text-3); margin-bottom:6px;">Pas Foto 3x4</div>
              @if($pendaftar->pas_foto)
                <img src="{{ asset('storage/' . $pendaftar->pas_foto) }}" alt="Pas Foto" style="width:100px; height:130px; object-fit:cover; border-radius:4px; border:1px solid var(--border); margin-bottom:6px;">
                <div><a href="{{ asset('storage/' . $pendaftar->pas_foto) }}" target="_blank" style="font-size:11px; color:#0284c7;">Lihat Foto Penuh</a></div>
              @else
                <div style="height:120px; background:var(--surface); display:flex; align-items:center; justify-content:center; color:var(--text-3); font-size:11px;">Belum Diunggah</div>
              @endif
            </div>

            {{-- Kartu Keluarga --}}
            <div style="border:1px solid var(--border); border-radius:8px; padding:10px; text-align:center;">
              <div style="font-size:11px; font-weight:700; color:var(--text-3); margin-bottom:6px;">Scan Kartu Keluarga</div>
              @if($pendaftar->scan_kk)
                <div style="height:120px; background:var(--surface); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px;">
                  <i class="bi bi-file-earmark-pdf-fill" style="font-size:32px; color:#ef4444;"></i>
                  <a href="{{ asset('storage/' . $pendaftar->scan_kk) }}" target="_blank" class="btn btn-sm" style="background:#0284c7; color:#fff; font-size:11px; padding:4px 8px;">Buka Dokumen KK</a>
                </div>
              @else
                <div style="height:120px; background:var(--surface); display:flex; align-items:center; justify-content:center; color:var(--text-3); font-size:11px;">Belum Diunggah</div>
              @endif
            </div>

            {{-- Ijazah / SKL --}}
            <div style="border:1px solid var(--border); border-radius:8px; padding:10px; text-align:center;">
              <div style="font-size:11px; font-weight:700; color:var(--text-3); margin-bottom:6px;">Scan Ijazah / SKL</div>
              @if($pendaftar->scan_ijazah_skl)
                <div style="height:120px; background:var(--surface); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px;">
                  <i class="bi bi-file-earmark-check-fill" style="font-size:32px; color:#10b981;"></i>
                  <a href="{{ asset('storage/' . $pendaftar->scan_ijazah_skl) }}" target="_blank" class="btn btn-sm" style="background:#0284c7; color:#fff; font-size:11px; padding:4px 8px;">Buka Dokumen SKL</a>
                </div>
              @else
                <div style="height:120px; background:var(--surface); display:flex; align-items:center; justify-content:center; color:var(--text-3); font-size:11px;">Belum Diunggah</div>
              @endif
            </div>

            {{-- KTP Orang Tua --}}
            <div style="border:1px solid var(--border); border-radius:8px; padding:10px; text-align:center;">
              <div style="font-size:11px; font-weight:700; color:var(--text-3); margin-bottom:6px;">Scan KTP Ortu (1 Saja)</div>
              @if($pendaftar->scan_ktp_ortu)
                <div style="height:120px; background:var(--surface); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px;">
                  <i class="bi bi-person-vcard-fill" style="font-size:32px; color:#6366f1;"></i>
                  <a href="{{ asset('storage/' . $pendaftar->scan_ktp_ortu) }}" target="_blank" class="btn btn-sm" style="background:#0284c7; color:#fff; font-size:11px; padding:4px 8px;">Buka Dokumen KTP</a>
                </div>
              @else
                <div style="height:120px; background:var(--surface); display:flex; align-items:center; justify-content:center; color:var(--text-3); font-size:11px;">Belum Diunggah</div>
              @endif
            </div>

            {{-- Akta Kelahiran --}}
            <div style="border:1px solid var(--border); border-radius:8px; padding:10px; text-align:center;">
              <div style="font-size:11px; font-weight:700; color:var(--text-3); margin-bottom:6px;">Scan Akta Kelahiran</div>
              @if($pendaftar->scan_akta)
                <div style="height:120px; background:var(--surface); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px;">
                  <i class="bi bi-file-earmark-text-fill" style="font-size:32px; color:#f59e0b;"></i>
                  <a href="{{ asset('storage/' . $pendaftar->scan_akta) }}" target="_blank" class="btn btn-sm" style="background:#0284c7; color:#fff; font-size:11px; padding:4px 8px;">Buka Akta Lahir</a>
                </div>
              @else
                <div style="height:120px; background:var(--surface); display:flex; align-items:center; justify-content:center; color:var(--text-3); font-size:11px;">Belum Diunggah</div>
              @endif
            </div>

            {{-- KIP / PIP --}}
            <div style="border:1px solid var(--border); border-radius:8px; padding:10px; text-align:center;">
              <div style="font-size:11px; font-weight:700; color:var(--text-3); margin-bottom:6px;">Kartu KIP / PIP</div>
              @if($pendaftar->scan_kip)
                <div style="height:120px; background:var(--surface); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px;">
                  <i class="bi bi-credit-card-2-front-fill" style="font-size:32px; color:#10b981;"></i>
                  <a href="{{ asset('storage/' . $pendaftar->scan_kip) }}" target="_blank" class="btn btn-sm" style="background:#0284c7; color:#fff; font-size:11px; padding:4px 8px;">Buka Kartu KIP</a>
                </div>
              @else
                <div style="height:120px; background:var(--surface); display:flex; align-items:center; justify-content:center; color:var(--text-3); font-size:11px;">Tidak Ada / Opsional</div>
              @endif
            </div>

            {{-- SKTM --}}
            <div style="border:1px solid var(--border); border-radius:8px; padding:10px; text-align:center;">
              <div style="font-size:11px; font-weight:700; color:var(--text-3); margin-bottom:6px;">Surat SKTM</div>
              @if($pendaftar->scan_sktm)
                <div style="height:120px; background:var(--surface); display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px;">
                  <i class="bi bi-file-earmark-medical-fill" style="font-size:32px; color:#ec4899;"></i>
                  <a href="{{ asset('storage/' . $pendaftar->scan_sktm) }}" target="_blank" class="btn btn-sm" style="background:#0284c7; color:#fff; font-size:11px; padding:4px 8px;">Buka Berkas SKTM</a>
                </div>
              @else
                <div style="height:120px; background:var(--surface); display:flex; align-items:center; justify-content:center; color:var(--text-3); font-size:11px;">Tidak Ada / Opsional</div>
              @endif
            </div>
          </div>
        </div>

      </div>

      {{-- KOLOM KANAN: FORM KEPUTUSAN & MUTASI --}}
      <div style="display:flex; flex-direction:column; gap:16px;">
        
        {{-- JURUSAN DITERIMA (highlight jika diterima) --}}
        @if($pendaftar->status === 'diterima' || $pendaftar->jurusan_diterima_id)
          @php $jd = $pendaftar->jurusanDiterima; @endphp
          <div class="panel" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7); border:1.5px solid #86efac; border-radius:var(--r-sm); padding:14px 16px;">
            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
              <div>
                <div style="font-size:11px; font-weight:700; color:#166534; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">
                  <i class="bi bi-diagram-3-fill"></i> Plotting Jurusan (Keputusan Akhir)
                </div>
                <div style="font-size:18px; font-weight:900; color:#15803d;">
                  {{ $jd ? $jd->kode_jurusan . ' — ' . $jd->nama_jurusan : '—' }}
                </div>
                @if($pendaftar->siswa_id)
                  <div style="font-size:11px; color:#16a34a; font-weight:700; margin-top:4px;"><i class="bi bi-check-circle-fill"></i> Sudah dimutasi ke SIRANI</div>
                @endif
              </div>
              {{-- Tombol koreksi jurusan --}}
              @if(!$pendaftar->siswa_id)
                <button type="button" onclick="document.getElementById('panelKoreksiJurusan').classList.toggle('hidden')"
                  style="background:#ffffff; color:#15803d; border:1.5px solid #86efac; font-weight:700; font-size:11.5px; padding:6px 12px; border-radius:6px; cursor:pointer; display:inline-flex; align-items:center; gap:5px;">
                  <i class="bi bi-pencil-square"></i> Koreksi Jurusan
                </button>
              @endif
            </div>

            {{-- Form Koreksi Jurusan (tersembunyi, toggle) --}}
            @if(!$pendaftar->siswa_id)
              <div id="panelKoreksiJurusan" class="hidden" style="margin-top:12px; padding-top:12px; border-top:1px solid #bbf7d0;">
                <form action="{{ route('admin.ppdb.koreksi_jurusan', $pendaftar->id) }}" method="POST">
                  @csrf
                  <div style="margin-bottom:8px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#166534; margin-bottom:4px;">Ganti Jurusan Diterima</label>
                    <select name="jurusan_diterima_id" required style="width:100%; padding:8px 10px; font-size:12px; border-radius:6px; border:1px solid #86efac; background:#fff; color:#0f172a;">
                      @foreach($jurusans as $j)
                        <option value="{{ $j->id }}" {{ $pendaftar->jurusan_diterima_id == $j->id ? 'selected' : '' }}>
                          {{ $j->kode_jurusan }} – {{ $j->nama_jurusan }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div style="margin-bottom:10px;">
                    <label style="display:block; font-size:11.5px; font-weight:700; color:#166534; margin-bottom:4px;">Alasan Koreksi (opsional)</label>
                    <input type="text" name="alasan_koreksi" placeholder="Mis: Revisi hasil wawancara..." style="width:100%; padding:7px 10px; font-size:12px; border-radius:6px; border:1px solid #86efac; background:#fff; color:#0f172a; box-sizing:border-box;">
                  </div>
                  <button type="submit" class="btn" style="width:100%; background:#16a34a; color:#fff; font-weight:800; font-size:12px; padding:8px; border-radius:6px; border:none; cursor:pointer;">
                    <i class="bi bi-check2-circle"></i> Simpan Koreksi Jurusan
                  </button>
                </form>
              </div>
            @endif
          </div>
        @endif

        {{-- FORM UPDATE STATUS --}}
        <div class="panel" style="background:var(--bg-2); border:1px solid var(--border); border-radius:var(--r-sm); padding:16px;">
          <h3 style="font-size:14px; font-weight:800; margin-bottom:14px; color:var(--text); border-bottom:1px solid var(--border); padding-bottom:8px;">
            <i class="bi bi-shield-check" style="color:#10b981;"></i> Keputusan Tim Verifikator
          </h3>

          <form action="{{ route('admin.ppdb.update_status', $pendaftar->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom:12px;">
              <label style="display:block; font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px;">Status Pendaftaran</label>
              <select name="status_pendaftaran" required style="width:100%; padding:8px 10px; font-size:12.5px; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:var(--text);">
                <option value="menunggu"     {{ in_array($pendaftar->status, ['menunggu','menunggu_verifikasi','draft']) ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="berkas_valid" {{ in_array($pendaftar->status, ['berkas_valid','terverifikasi']) ? 'selected' : '' }}>Berkas Valid (Lolos Administrasi &amp; Siap Tes)</option>
                <option value="diterima"     {{ $pendaftar->status == 'diterima' ? 'selected' : '' }}>DITERIMA (Lolos Seleksi)</option>
                <option value="cadangan"     {{ $pendaftar->status == 'cadangan' ? 'selected' : '' }}>CADANGAN (Menunggu Kuota)</option>
                <option value="ditolak"      {{ $pendaftar->status == 'ditolak' ? 'selected' : '' }}>DITOLAK (Tidak Memenuhi Syarat)</option>
              </select>
            </div>

            <div style="margin-bottom:12px;">
              <label style="display:block; font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px;">Tetapkan Jurusan Diterima</label>
              <select name="jurusan_diterima_id" style="width:100%; padding:8px 10px; font-size:12.5px; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:var(--text);">
                <option value="">-- Tetapkan Jurusan --</option>
                @foreach($jurusans as $j)
                  <option value="{{ $j->id }}" {{ $pendaftar->jurusan_diterima_id == $j->id || ($pendaftar->jurusan_id_1 == $j->id && !$pendaftar->jurusan_diterima_id) ? 'selected' : '' }}>
                    {{ $j->kode_jurusan }} – {{ $j->nama_jurusan }}
                  </option>
                @endforeach
              </select>
            </div>

            <div style="margin-bottom:14px;">
              <label style="display:block; font-size:11.5px; font-weight:700; color:var(--text-2); margin-bottom:4px;">Catatan untuk Calon Siswa</label>
              <textarea name="catatan" rows="3" placeholder="Misal: Berkas lengkap, silakan daftar ulang..." style="width:100%; padding:8px 10px; font-size:12px; border-radius:6px; border:1px solid var(--border); background:var(--surface); color:var(--text);">{{ $pendaftar->catatan_panitia }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; padding:9px; font-size:12.5px; font-weight:800; border-radius:6px;">
              Simpan Keputusan Verifikasi
            </button>
          </form>
        </div>

        {{-- MUTASI KE SIRANI --}}
        @if($pendaftar->status === 'diterima')
          <div class="panel" style="background:#f0fdf4; border:1px solid #86efac; border-radius:var(--r-sm); padding:16px;">
            <h3 style="font-size:14px; font-weight:800; margin-bottom:8px; color:#15803d;">
              <i class="bi bi-box-arrow-in-right"></i> Mutasi ke Siswa SIRANI
            </h3>
            
            @if($pendaftar->siswa_id)
              <div style="font-size:12px; color:#166534; margin-bottom:10px;">
                Calon siswa ini sudah resmi tercatat sebagai <strong>Siswa Aktif SIRANI</strong>.
              </div>
              <a href="/siswa" class="btn btn-sm" style="background:#16a34a; color:#fff; font-weight:700; font-size:11.5px; padding:6px 12px; border-radius:6px; display:inline-block;">
                Lihat di Master Siswa →
              </a>
            @else
              @php
                // Auto-detect rombel berdasarkan jurusan_diterima_id
                $jurusanId = $pendaftar->jurusan_diterima_id ?? $pendaftar->jurusan_id_1;
                $rombelAuto = $rombels->firstWhere('jurusan_id', $jurusanId);
              @endphp

              @if($rombelAuto)
                <div style="background:#dcfce7; border:1px solid #86efac; border-radius:8px; padding:10px 14px; margin-bottom:12px; font-size:12.5px; color:#15803d;">
                  <strong><i class="bi bi-arrow-right-circle-fill"></i> Auto-Routing:</strong>
                  Siswa ini akan ditempatkan di <strong>{{ $rombelAuto->nama_rombel }}</strong> sesuai jurusan diterima.
                </div>
              @endif

              <form action="{{ route('admin.ppdb.mutasi', $pendaftar->id) }}" method="POST">
                @csrf
                @if($rombelAuto)
                  <input type="hidden" name="rombel_id" value="{{ $rombelAuto->id }}">
                  <button type="submit" class="btn" onclick="return confirm('Mutasikan {{ $pendaftar->nama_lengkap }} ke {{ $rombelAuto->nama_rombel }}?')"
                    style="width:100%; background:#16a34a; color:#fff; font-weight:800; font-size:12.5px; padding:9px; border-radius:6px; border:none; cursor:pointer;">
                    <i class="bi bi-person-check-fill"></i> Mutasikan ke {{ $rombelAuto->nama_rombel }}
                  </button>
                @else
                  <p style="font-size:12px; color:#ef4444; margin-bottom:10px;">
                    <i class="bi bi-exclamation-triangle-fill"></i> Rombel Kelas X untuk jurusan ini belum ditemukan. Pilih manual:
                  </p>
                  <div style="margin-bottom:10px;">
                    <select name="rombel_id" required style="width:100%; padding:8px 10px; font-size:12px; border-radius:6px; border:1px solid #86efac; background:#fff;">
                      <option value="">-- Pilih Kelas X --</option>
                      @foreach($rombels as $r)
                        <option value="{{ $r->id }}">{{ $r->nama_rombel }}</option>
                      @endforeach
                    </select>
                  </div>
                  <button type="submit" class="btn" style="width:100%; background:#16a34a; color:#fff; font-weight:800; font-size:12.5px; padding:9px; border-radius:6px; border:none; cursor:pointer;">
                    <i class="bi bi-person-check-fill"></i> Mutasikan Calon Siswa Ini
                  </button>
                @endif
              </form>
            @endif
          </div>
        @endif

      </div>

    </div>

  </main>
</div>

<style>
  .hidden { display: none; }
</style>
</body>
</html>
