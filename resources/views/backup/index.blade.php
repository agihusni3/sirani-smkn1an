<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pencadangan &amp; Auto-Backup Database — SMKN 1 Air Naningan</title>
  @include('partials.styles')
  <style>
    .backup-stat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
      gap: 14px;
      margin-bottom: 24px;
    }
    .backup-stat-card {
      background: var(--bg-2);
      border: 1px solid var(--border-2);
      border-radius: var(--r-md);
      padding: 16px 18px;
      display: flex;
      align-items: center;
      gap: 14px;
      box-shadow: var(--shadow-sm);
    }
    .backup-stat-icon {
      width: 42px;
      height: 42px;
      border-radius: 10px;
      background: var(--bg-3);
      border: 1px solid var(--border-2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 19px;
      color: #000000;
      flex-shrink: 0;
    }
    .backup-stat-val {
      font-family: var(--font-mono);
      font-size: 20px;
      font-weight: 900;
      color: var(--text);
      line-height: 1.1;
    }
    .backup-stat-lbl {
      font-size: 11px;
      font-weight: 800;
      color: var(--text-3);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-top: 3px;
    }
    .backup-table-row:hover td {
      background: var(--bg-2);
    }
  </style>
</head>
<body>
<div class="app-container">
  @include('partials.sidebar')
  <main class="main-content">
    {{-- ULTRA COMPACT SLIM HEADER BAR --}}
    <div class="panel no-print" style="background:var(--bg-2); border:1px solid var(--border); padding:10px 16px; margin-bottom:12px; border-radius:var(--r-md); box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <h1 style="margin:0; font-size:16px; font-weight:900; color:var(--text); display:inline-flex; align-items:center; gap:6px;">
            <i class="bi bi-database-gear" style="color:#000000; font-size:16px;"></i> Pencadangan &amp; Backup Database
          </h1>
          <span style="color:var(--border-2); font-weight:300;">|</span>
          <span style="font-size:11.5px; color:var(--text-3);">
            Pencadangan otomatis &amp; pemulihan darurat database
          </span>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
          @include('partials.header_actions')
        </div>
      </div>
    </div>

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

    {{-- SUMMARY KPI CARDS --}}
    <div class="backup-stat-grid">
      <div class="backup-stat-card">
        <div class="backup-stat-icon">
          <i class="bi bi-people-fill"></i>
        </div>
        <div>
          <div class="backup-stat-val">{{ number_format($totalSiswa) }}</div>
          <div class="backup-stat-lbl">Total Siswa</div>
        </div>
      </div>

      <div class="backup-stat-card">
        <div class="backup-stat-icon">
          <i class="bi bi-person-badge-fill"></i>
        </div>
        <div>
          <div class="backup-stat-val">{{ number_format($totalGuru) }}</div>
          <div class="backup-stat-lbl">Guru &amp; Staf</div>
        </div>
      </div>

      <div class="backup-stat-card">
        <div class="backup-stat-icon">
          <i class="bi bi-door-open-fill"></i>
        </div>
        <div>
          <div class="backup-stat-val">{{ number_format($totalRombel) }}</div>
          <div class="backup-stat-lbl">Rombel Kelas</div>
        </div>
      </div>

      <div class="backup-stat-card">
        <div class="backup-stat-icon">
          <i class="bi bi-calendar2-check-fill"></i>
        </div>
        <div>
          <div class="backup-stat-val">{{ number_format($totalAbsensi) }}</div>
          <div class="backup-stat-lbl">Log Absensi</div>
        </div>
      </div>

      <div class="backup-stat-card">
        <div class="backup-stat-icon">
          <i class="bi bi-hdd-stack-fill"></i>
        </div>
        <div>
          <div class="backup-stat-val" style="font-size:16px;">{{ $fileSize }}</div>
          <div class="backup-stat-lbl">Ukuran Database</div>
        </div>
      </div>

      <div class="backup-stat-card">
        <div class="backup-stat-icon">
          <i class="bi bi-archive-fill"></i>
        </div>
        <div>
          <div class="backup-stat-val">{{ count($storedBackups) }} <span style="font-size:13px; font-weight:700; color:var(--text-3);">File</span></div>
          <div class="backup-stat-lbl">Arsip Cadangan</div>
        </div>
      </div>
    </div>

    {{-- AUTO-BACKUP STATUS & TRIGGER CARD --}}
    <div class="panel" style="padding:22px 24px; margin-bottom:24px; background:var(--bg-2); border-left:4px solid #000000; box-shadow:var(--shadow-sm);">
      <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
        <div style="flex:1; min-width:280px;">
          <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(0,0,0,0.06); color:#000000; border:1px solid rgba(0,0,0,0.12); padding:3px 10px; border-radius:20px; font-size:11px; font-weight:800; margin-bottom:8px;">
            <span class="pulse-dot" style="background:#000000; width:7px; height:7px;"></span> Auto-Backup Aktif
          </div>
          <h2 style="font-size:16px; font-weight:900; color:var(--text); margin-bottom:4px;">
            Pencadangan Otomatis Terjadwal (Daily Auto-Backup)
          </h2>
          <p style="font-size:12.5px; color:var(--text-2); line-height:1.5; margin:0;">
            Sistem mencadangkan database otomatis setiap malam pukul <strong>23:00 WIB</strong> dan mempertahankan <strong>14 arsip cadangan terakhir</strong>.
          </p>
        </div>

        <div>
          <form action="{{ route('admin.backup.auto-run') }}" method="POST">
            @csrf
            <button type="submit" class="btn" style="background:#000000; color:#FFFFFF; border:1.5px solid #000000; padding:10px 20px; font-weight:800; font-size:13px; border-radius:var(--r-sm); display:inline-flex; align-items:center; gap:8px; cursor:pointer;" title="Eksekusi pencadangan saat ini">
              <i class="bi bi-lightning-charge-fill" style="color:#FFFFFF;"></i>
              Jalankan Auto-Backup Sekarang
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- DAFTAR FILE CADANGAN TERSIMPAN DI SERVER --}}
    <div class="panel" style="padding:0; margin-bottom:24px; overflow:hidden; border:1px solid var(--border); border-radius:var(--r-md); background:var(--surface);">
      <div style="padding:16px 20px; border-bottom:1px solid var(--border); background:var(--bg-2); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div>
          <h3 style="font-size:15px; font-weight:900; color:var(--text); margin:0; display:flex; align-items:center; gap:8px;">
            <i class="bi bi-folder-check" style="color:#000000;"></i> Daftar Arsip Cadangan di Server
          </h3>
          <span style="font-size:11.5px; color:var(--text-3); font-family:var(--font-mono); margin-top:2px; display:block;">
            Direktori: <code>storage/app/backups/</code>
          </span>
        </div>
        <a href="{{ route('admin.backup.download') }}" class="btn btn-outline" style="height:36px; padding:0 14px; font-size:12.5px; font-weight:800; color:#000000; border:1.5px solid #000000; background:var(--surface); display:inline-flex; align-items:center; gap:6px; text-decoration:none;" title="Unduh snapshot database aktif saat ini">
          <i class="bi bi-download" style="color:#000000;"></i> Unduh Database Saat Ini
        </a>
      </div>

      @if(count($storedBackups) > 0)
        <div style="overflow-x:auto;">
          <table class="data-table" style="width:100%; border-collapse:collapse; font-size:12.5px; margin:0;">
            <thead>
              <tr style="background:var(--bg-3); border-bottom:1.5px solid var(--border-2); text-align:left; color:var(--text-3); font-size:11px; text-transform:uppercase; letter-spacing:0.4px;">
                <th style="padding:10px 14px; width:40px; text-align:center;">#</th>
                <th style="padding:10px 14px;">Nama File Cadangan</th>
                <th style="padding:10px 14px;">Waktu Pencadangan</th>
                <th style="padding:10px 14px; text-align:center;">Ukuran File</th>
                <th style="padding:10px 14px; text-align:right;">Aksi &amp; Pemulihan</th>
              </tr>
            </thead>
            <tbody>
              @foreach($storedBackups as $idx => $b)
                <tr class="backup-table-row" style="border-bottom:1px solid var(--border-2); transition:background .15s ease;">
                  <td style="padding:12px 14px; font-weight:700; color:var(--text-3); text-align:center;">{{ $idx + 1 }}</td>
                  <td style="padding:12px 14px; font-weight:800; font-family:var(--font-mono); color:var(--text);">
                    <i class="bi bi-file-earmark-binary" style="color:#000000; margin-right:6px;"></i>{{ $b['filename'] }}
                  </td>
                  <td style="padding:12px 14px; color:var(--text-2); font-size:12px;">{{ $b['time'] }}</td>
                  <td style="padding:12px 14px; font-weight:800; font-family:var(--font-mono); color:var(--text); text-align:center;">
                    <span class="badge" style="background:var(--bg-3); border:1px solid var(--border-2); font-size:11px; padding:2px 8px;">
                      {{ $b['size'] }}
                    </span>
                  </td>
                  <td style="padding:12px 14px; text-align:right;">
                    <div style="display:inline-flex; align-items:center; gap:6px; justify-content:flex-end;">
                      <a href="{{ route('admin.backup.download-saved', $b['filename']) }}" class="btn btn-outline" style="height:30px; padding:0 10px; font-size:11.5px; font-weight:800; text-decoration:none; display:inline-flex; align-items:center; gap:4px;" title="Unduh ke komputer">
                        <i class="bi bi-download"></i> Unduh
                      </a>

                      <form action="{{ route('admin.backup.restore-saved', $b['filename']) }}" method="POST" style="display:inline;" onsubmit="return confirm('PERINGATAN PEMULIHAN:\nApakah Anda yakin ingin memulihkan database dari file {{ $b['filename'] }}?\nDatabase saat ini akan digantikan secara instan.')">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="height:30px; padding:0 10px; font-size:11.5px; font-weight:800; color:#000000; border-color:#000000; background:var(--bg-2); display:inline-flex; align-items:center; gap:4px; cursor:pointer;" title="Pulihkan database instan">
                          <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
                        </button>
                      </form>

                      <form action="{{ route('admin.backup.delete-saved', $b['filename']) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus file cadangan {{ $b['filename'] }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline" style="height:30px; width:30px; padding:0; font-size:11.5px; color:var(--red); border-color:rgba(239,68,68,0.4); display:inline-flex; align-items:center; justify-content:center; cursor:pointer;" title="Hapus arsip">
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
        <div style="text-align:center; padding:36px 20px; color:var(--text-3); font-size:13px; background:var(--surface);">
          <i class="bi bi-folder2-open" style="font-size:32px; display:block; margin-bottom:8px; color:var(--text-3);"></i>
          Belum ada file cadangan yang diarsipkan di server. Klik tombol <strong>"Jalankan Auto-Backup Sekarang"</strong> di atas.
        </div>
      @endif
    </div>

    {{-- RESTORE DARI FILE EKSTERNAL (UPLOAD) --}}
    <div class="panel" style="padding:22px 24px; margin-bottom:24px; border:1.5px dashed var(--border-2); background:var(--bg-2); border-radius:var(--r-md);">
      <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:20px;">
        <div style="flex:1; min-width:280px;">
          <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(0,0,0,0.06); color:#000000; border:1px solid rgba(0,0,0,0.12); padding:3px 10px; border-radius:20px; font-size:11px; font-weight:800; margin-bottom:8px;">
            <i class="bi bi-upload"></i> Unggah File Cadangan Manual
          </div>
          <h2 style="font-size:16px; font-weight:900; color:var(--text); margin-bottom:4px;">
            Pulihkan dari File Luar (<em>Upload &amp; Restore</em>)
          </h2>
          <p style="font-size:12.5px; color:var(--text-2); line-height:1.5; max-width:540px; margin:0;">
            Gunakan panel ini jika Anda ingin mengunggah cadangan dari flashdisk atau komputer (format <code>.sqlite</code>, <code>.db</code>, atau <code>.sql</code>).
          </p>
        </div>

        <div style="min-width:300px; width:100%; max-width:400px;">
          <form action="{{ route('admin.backup.restore') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('PERINGATAN KRUSIAL:\nApakah Anda yakin ingin memulihkan database dari file yang diunggah?')">
            @csrf
            <div style="margin-bottom:10px;">
              <input type="file" name="backup_file" required accept=".sqlite,.db,.sql" class="input-field" style="width:100%; height:38px; padding:6px 10px; font-size:12px; background:var(--surface);" />
            </div>
            <button type="submit" class="btn" style="width:100%; height:38px; font-weight:800; display:inline-flex; align-items:center; justify-content:center; gap:8px; font-size:12.5px; background:var(--surface); color:#000000; border:1.5px solid #000000; border-radius:var(--r-sm); cursor:pointer;">
              <i class="bi bi-cloud-arrow-up-fill" style="font-size:15px; color:#000000;"></i>
              Mulai Unggah &amp; Pulihkan
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- PANDUAN RESTORE --}}
    <div class="panel" style="background:var(--bg); border:1px solid var(--border); padding:18px 20px;">
      <h3 style="font-size:13.5px; font-weight:800; color:var(--text); margin-bottom:10px; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-shield-check" style="color:#000000;"></i> Panduan Pemulihan Bencana (<em>Disaster Recovery Guide</em>)
      </h3>
      <div style="font-size:12.5px; color:var(--text-2); line-height:1.6;">
        <ol style="padding-left:18px; margin:0;">
          <li>Auto-backup berjalan otomatis setiap hari pukul <strong>23:00 WIB</strong> tanpa mengganggu operasional jam presensi sekolah.</li>
          <li>Untuk memulihkan database ke titik tanggal tertentu, Anda cukup memilih arsip pada tabel di atas dan mengklik tombol <strong>Pulihkan</strong>.</li>
          <li>Setiap kali proses pemulihan (restore) dijalankan, sistem secara otomatis membuat <em>Emergency Safety Snapshot</em> sebagai jaminan keamanan rollback.</li>
        </ol>
      </div>
    </div>
  </main>
</div>
</body>
</html>
