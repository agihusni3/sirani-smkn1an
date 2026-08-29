<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pencadangan &amp; Auto-Backup Database — SMKN 1 Air Naningan</title>
  @include('partials.styles')
</head>
<body>
<div class="app-container">
  @include('partials.sidebar')
  <main class="main-content">
    <header class="header">
      <div class="header-title">
        <h1><i class="bi bi-database-gear" style="color:var(--gold); margin-right:8px;"></i>Pencadangan &amp; Auto-Backup Database</h1>
        <p>Pencadangan otomatis terjadwal dan pemulihan darurat (*disaster recovery*) untuk seluruh database SIRANI SMKN 1 Air Naningan.</p>
      </div>
      @include('partials.header_actions')
    </header>

    @if(session('success'))
      <div class="alert-success" style="margin-bottom:20px;">
        <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="alert-error" style="margin-bottom:20px;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}
      </div>
    @endif

    {{-- SUMMARY CARDS --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(170px, 1fr)); gap:14px; margin-bottom:24px;">
      <div class="panel" style="padding:16px; text-align:center;">
        <div style="font-size:11px; font-weight:800; color:var(--text-3); text-transform:uppercase;">Total Siswa</div>
        <div style="font-family:var(--font-mono); font-size:22px; font-weight:900; color:var(--text); margin-top:4px;">{{ number_format($totalSiswa) }}</div>
      </div>

      <div class="panel" style="padding:16px; text-align:center;">
        <div style="font-size:11px; font-weight:800; color:var(--text-3); text-transform:uppercase;">Total Guru &amp; Staf</div>
        <div style="font-family:var(--font-mono); font-size:22px; font-weight:900; color:var(--text); margin-top:4px;">{{ number_format($totalGuru) }}</div>
      </div>

      <div class="panel" style="padding:16px; text-align:center;">
        <div style="font-size:11px; font-weight:800; color:var(--text-3); text-transform:uppercase;">Rombel / Kelas</div>
        <div style="font-family:var(--font-mono); font-size:22px; font-weight:900; color:var(--text); margin-top:4px;">{{ number_format($totalRombel) }}</div>
      </div>

      <div class="panel" style="padding:16px; text-align:center;">
        <div style="font-size:11px; font-weight:800; color:var(--text-3); text-transform:uppercase;">Log Record Absensi</div>
        <div style="font-family:var(--font-mono); font-size:22px; font-weight:900; color:var(--text); margin-top:4px;">{{ number_format($totalAbsensi) }}</div>
      </div>

      <div class="panel" style="padding:16px; text-align:center;">
        <div style="font-size:11px; font-weight:800; color:var(--text-3); text-transform:uppercase;">Ukuran Database</div>
        <div style="font-family:var(--font-mono); font-size:22px; font-weight:900; color:var(--gold); margin-top:4px;">{{ $fileSize }}</div>
      </div>

      <div class="panel" style="padding:16px; text-align:center;">
        <div style="font-size:11px; font-weight:800; color:var(--text-3); text-transform:uppercase;">Arsip Tersimpan</div>
        <div style="font-family:var(--font-mono); font-size:22px; font-weight:900; color:var(--green); margin-top:4px;">{{ count($storedBackups) }} File</div>
      </div>
    </div>

    {{-- AUTO-BACKUP STATUS & TRIGGER CARD --}}
    <div class="panel" style="padding:24px; margin-bottom:24px; background:linear-gradient(135deg, var(--bg-2) 0%, rgba(202,138,4,0.06) 100%); border-left:4px solid var(--gold);">
      <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
        <div style="flex:1; min-width:280px;">
          <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(34,197,94,0.15); color:var(--green); border:1px solid rgba(34,197,94,0.3); padding:4px 10px; border-radius:20px; font-size:11.5px; font-weight:800; margin-bottom:8px;">
            <i class="bi bi-clock-history"></i> Auto-Backup Aktif
          </div>
          <h2 style="font-size:17.5px; font-weight:900; color:var(--text); margin-bottom:4px;">
            Pencadangan Otomatis Terjadwal (Daily Auto-Backup)
          </h2>
          <p style="font-size:13px; color:var(--text-2); line-height:1.5; margin:0;">
            Sistem otomatis mencadangkan seluruh database setiap malam pukul <strong>23:00 WIB</strong> dan mempertahankan <strong>14 arsip cadangan terakhir</strong> secara cerdas.
          </p>
        </div>

        <div>
          <form action="{{ route('admin.backup.auto-run') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-gold" style="padding:12px 24px; font-weight:800; font-size:13.5px; display:inline-flex; align-items:center; gap:8px;">
              <i class="bi bi-lightning-charge-fill"></i>
              Jalankan Auto-Backup Sekarang
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- DAFTAR FILE CADANGAN TERSIMPAN DI SERVER --}}
    <div class="panel" style="padding:24px; margin-bottom:24px;">
      <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
        <div>
          <h3 style="font-size:16px; font-weight:900; color:var(--text); margin:0;">
            <i class="bi bi-folder-check" style="color:var(--gold); margin-right:6px;"></i>Daftar Arsip Cadangan di Server
          </h3>
          <span style="font-size:12px; color:var(--text-3);">Tersimpan di direktori server <code>storage/app/backups/</code></span>
        </div>
        <a href="{{ route('admin.backup.download') }}" class="btn btn-outline" style="font-size:12.5px; font-weight:800; display:inline-flex; align-items:center; gap:6px;">
          <i class="bi bi-download"></i> Unduh Database Saat Ini
        </a>
      </div>

      @if(count($storedBackups) > 0)
        <div style="overflow-x:auto;">
          <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
              <tr style="border-bottom:1.5px solid var(--border-2); text-align:left; color:var(--text-3); font-size:11.5px; text-transform:uppercase;">
                <th style="padding:10px 12px;">#</th>
                <th style="padding:10px 12px;">Nama File Cadangan</th>
                <th style="padding:10px 12px;">Waktu Pencadangan</th>
                <th style="padding:10px 12px;">Ukuran File</th>
                <th style="padding:10px 12px; text-align:right;">Aksi &amp; Pemulihan</th>
              </tr>
            </thead>
            <tbody>
              @foreach($storedBackups as $idx => $b)
                <tr style="border-bottom:1px solid var(--border-2);">
                  <td style="padding:12px; font-weight:700; color:var(--text-3);">{{ $idx + 1 }}</td>
                  <td style="padding:12px; font-weight:800; font-family:var(--font-mono); color:var(--text);">
                    <i class="bi bi-file-earmark-binary" style="color:var(--gold); margin-right:6px;"></i>{{ $b['filename'] }}
                  </td>
                  <td style="padding:12px; color:var(--text-2);">{{ $b['time'] }}</td>
                  <td style="padding:12px; font-weight:800; font-family:var(--font-mono); color:var(--text);">{{ $b['size'] }}</td>
                  <td style="padding:12px; text-align:right;">
                    <div style="display:inline-flex; align-items:center; gap:6px; justify-content:flex-end;">
                      <a href="{{ route('admin.backup.download-saved', $b['filename']) }}" class="btn btn-outline" style="padding:6px 12px; font-size:11.5px; font-weight:800; text-decoration:none;" title="Unduh ke komputer">
                        <i class="bi bi-download"></i> Unduh
                      </a>

                      <form action="{{ route('admin.backup.restore-saved', $b['filename']) }}" method="POST" style="display:inline;" onsubmit="return confirm('PERINGATAN PEMULIHAN:\nApakah Anda yakin ingin memulihkan database dari file {{ $b['filename'] }}?\nDatabase saat ini akan digantikan secara instan.')">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="padding:6px 12px; font-size:11.5px; font-weight:800; color:var(--green); border-color:var(--green);" title="Pulihkan langsung">
                          <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
                        </button>
                      </form>

                      <form action="{{ route('admin.backup.delete-saved', $b['filename']) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus file cadangan {{ $b['filename'] }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline" style="padding:6px 10px; font-size:11.5px; color:var(--red); border-color:var(--red);" title="Hapus arsip">
                          <i class="bi bi-trash-fill"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div style="text-align:center; padding:30px; color:var(--text-3); font-size:13px; background:var(--bg); border-radius:10px;">
          <i class="bi bi-folder2-open" style="font-size:32px; display:block; margin-bottom:8px; color:var(--text-3);"></i>
          Belum ada file cadangan yang diarsipkan di server. Klik tombol <strong>"Jalankan Auto-Backup Sekarang"</strong> di atas.
        </div>
      @endif
    </div>

    {{-- RESTORE DARI FILE EKSTERNAL (UPLOAD) --}}
    <div class="panel" style="padding:24px; margin-bottom:24px; border:1.5px dashed rgba(202,138,4,0.4); background:var(--bg-2);">
      <div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:20px;">
        <div style="flex:1; min-width:280px;">
          <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(34,197,94,0.1); color:var(--green); border:1px solid rgba(34,197,94,0.3); padding:4px 10px; border-radius:20px; font-size:11.5px; font-weight:800; margin-bottom:8px;">
            <i class="bi bi-upload"></i> Unggah File Cadangan Manual
          </div>
          <h2 style="font-size:17px; font-weight:900; color:var(--text); margin-bottom:4px;">
            Pulihkan dari File Luar (*Upload &amp; Restore*)
          </h2>
          <p style="font-size:13px; color:var(--text-2); line-height:1.5; max-width:560px; margin:0;">
            Gunakan panel ini jika Anda ingin memulihkan database dari flashdisk atau komputer lokal (format <code>.sqlite</code>, <code>.db</code>, atau <code>.sql</code>).
          </p>
        </div>

        <div style="min-width:320px; width:100%; max-width:420px;">
          <form action="{{ route('admin.backup.restore') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('PERINGATAN KRUSIAL:\nApakah Anda yakin ingin memulihkan database dari file yang diunggah?')">
            @csrf
            <div style="margin-bottom:10px;">
              <input type="file" name="backup_file" required accept=".sqlite,.db,.sql" class="input-field" style="width:100%; padding:8px; font-size:12px;" />
            </div>
            <button type="submit" class="btn btn-outline" style="width:100%; height:40px; font-weight:800; color:var(--gold); border-color:var(--gold); display:inline-flex; align-items:center; justify-content:center; gap:8px; font-size:13px;">
              <i class="bi bi-cloud-arrow-up-fill" style="font-size:16px;"></i>
              Mulai Unggah &amp; Pulihkan
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- PANDUAN RESTORE --}}
    <div class="panel" style="background:var(--bg);">
      <h3 style="font-size:14px; font-weight:800; color:var(--text); margin-bottom:10px; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-info-circle-fill" style="color:var(--gold);"></i> Panduan Pemulihan Bencana (*Disaster Recovery Guide*)
      </h3>
      <div style="font-size:13px; color:var(--text-2); line-height:1.6;">
        <ol style="padding-left:20px; margin:0;">
          <li>Auto-backup berjalan otomatis setiap hari pukul <strong>23:00 WIB</strong> tanpa mengganggu operasional jam presensi sekolah.</li>
          <li>Untuk memulihkan database ke titik tanggal tertentu, Anda cukup memilih arsip pada tabel di atas dan mengklik tombol <strong>Pulihkan</strong>.</li>
          <li>Setiap kali proses pemulihan (restore) dijalankan, sistem secara otomatis membuat *Emergency Safety Snapshot* sebagai jaminan keamanan rollback.</li>
        </ol>
      </div>
    </div>
  </main>
</div>
</body>
</html>
