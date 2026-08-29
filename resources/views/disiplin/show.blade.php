<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dossier Kesiswaan: {{ $siswa->nama }} — SIRANI</title>
  @include('partials.styles')
  <style>
    .dossier-hero {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      padding: 24px;
      margin-bottom: 24px;
      position: relative;
    }
    .dossier-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 24px;
    }
    @media (max-width: 1024px) {
      .dossier-grid { grid-template-columns: 1fr; }
    }

    /* Timeline Styling */
    .timeline-container {
      position: relative;
      padding-left: 28px;
    }
    .timeline-container::before {
      content: '';
      position: absolute;
      left: 10px;
      top: 0;
      bottom: 0;
      width: 2px;
      background: var(--border-2);
    }
    .timeline-item {
      position: relative;
      margin-bottom: 20px;
    }
    .timeline-dot {
      position: absolute;
      left: -28px;
      top: 4px;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      background: var(--gold);
      border: 3px solid var(--bg-2);
      box-shadow: 0 0 8px var(--gold-glow);
    }
    .timeline-card {
      background: var(--bg-2);
      border: 1px solid var(--border);
      border-radius: var(--r-sm);
      padding: 14px 16px;
      transition: all .2s ease;
    }
    .timeline-card:hover {
      border-color: var(--gold);
      box-shadow: var(--shadow-sm);
    }

    /* Evidence Vault Grid */
    .vault-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 14px;
    }
    .vault-card {
      background: var(--bg-3);
      border: 1px solid var(--border);
      border-radius: var(--r-sm);
      padding: 12px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: all .2s ease;
    }
    .vault-card:hover {
      border-color: var(--gold);
      transform: translateY(-2px);
    }
  </style>
</head>
<body>

<div class="app-container">
  @include('partials.sidebar')

  <main class="main-content">
    <header class="header" style="margin-bottom:20px;">
      <div class="header-title">
        <div style="display:flex; align-items:center; gap:8px; font-size:12px; font-weight:700; color:var(--text-3); margin-bottom:6px;">
          <a href="{{ route('admin.disiplin.index') }}" style="color:var(--text-2); text-decoration:none;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='var(--text-2)'">Buku Kasus</a>
          <i class="bi bi-chevron-right" style="font-size:10px; color:var(--text-3);"></i>
          <span style="color:#000000; font-weight:800;">Dossier Rekam Jejak Siswa</span>
        </div>
        <h1 style="margin:0; font-size:24px; font-weight:900; color:var(--text); display:flex; align-items:center; gap:10px;">
          <i class="bi bi-journal-bookmark-fill" style="color:#000000;"></i> {{ $siswa->nama }}
        </h1>
        <p style="margin-top:4px; font-size:13px; color:var(--text-2); font-weight:500;">
          Portofolio Digital Pembinaan Kedisiplinan &amp; Riwayat Interaksi Kesiswaan Terpadu.
        </p>
      </div>
      @include('partials.header_actions')
    </header>

    @if(session('success'))
      <div class="alert-success" style="margin-bottom:16px;">
        <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="alert-error" style="margin-bottom:16px;">
        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>{{ session('error') }}
      </div>
    @endif

    {{-- DOSSIER HERO CARD --}}
    @php
      $rombelAktif = $siswa->siswaRombels->where('status_keanggotaan', 'aktif')->first()?->rombel;
      $wali = $rombelAktif?->waliKelas;
      $cleanHpOrtu = $siswa->nomor_hp_ortu ? preg_replace('/[^0-9]/', '', $siswa->nomor_hp_ortu) : null;
      if ($cleanHpOrtu && str_starts_with($cleanHpOrtu, '0')) {
        $cleanHpOrtu = '62' . substr($cleanHpOrtu, 1);
      }
    @endphp
    <div class="dossier-hero" style="background:var(--bg-2); border:1px solid var(--border); border-radius:var(--r-md); padding:22px 24px; margin-bottom:24px; box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:18px;">
        <div style="display:flex; gap:18px; align-items:center; flex-wrap:wrap;">
          <div style="width:72px; height:72px; border-radius:20px; background:var(--bg-3); border:2px solid rgba(0,0,0,0.15); display:flex; align-items:center; justify-content:center; font-weight:900; font-size:26px; color:#000000; overflow:hidden; flex-shrink:0; box-shadow:0 4px 12px rgba(0,0,0,0.06);">
            @if($siswa->foto)
              <img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama }}" style="width:100%; height:100%; object-fit:cover;" />
            @else
              {{ substr($siswa->nama, 0, 1) }}
            @endif
          </div>

          <div>
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px; flex-wrap:wrap;">
              <h2 style="font-size:22px; font-weight:900; color:var(--text); margin:0;">{{ $siswa->nama }}</h2>
              {!! $kasus->badge_tahap !!}
            </div>
            
            <div style="font-size:13px; color:var(--text-2); display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:4px;">
              <span>NIS: <strong style="color:var(--text); font-family:var(--font-mono); font-weight:800;">{{ $siswa->nis }}</strong></span>
              <span style="color:var(--border-2);">•</span>
              <span>Kelas: <strong style="color:#000000; font-weight:800;">{{ $rombelAktif->nama_rombel ?? '-' }}</strong></span>
              <span style="color:var(--border-2);">•</span>
              <span>Wali Kelas: <strong style="color:var(--text); font-weight:800;">{{ $wali->nama ?? 'Belum Ditentukan' }}</strong></span>
            </div>

            <div style="font-size:12.5px; color:var(--text-2); display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
              <span>Wali Murid: <strong style="color:var(--text); font-weight:800;">{{ $siswa->nama_ortu ?: 'Bapak/Ibu Orang Tua' }}</strong></span>
              @if($cleanHpOrtu)
                <a href="https://wa.me/{{ $cleanHpOrtu }}" target="_blank" style="color:#16A34A; font-size:11.5px; font-weight:800; text-decoration:none; display:inline-flex; align-items:center; gap:4px; background:rgba(22,163,74,0.1); padding:2px 8px; border-radius:12px; border:1px solid rgba(22,163,74,0.25);" title="Chat WhatsApp Wali Murid">
                  <i class="bi bi-whatsapp"></i> {{ $siswa->nomor_hp_ortu }}
                </a>
              @endif
            </div>
          </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
          <button type="button" class="btn btn-outline" style="border-color:rgba(239,68,68,0.4); color:#DC2626; font-weight:800; font-size:12px; height:36px; padding:0 14px; display:inline-flex; align-items:center; gap:6px;" onclick="openModalPelanggaranSiswa()">
            <i class="bi bi-exclamation-triangle-fill"></i> + Catat Pelanggaran
          </button>
          <button type="button" class="btn btn-outline" style="border-color:rgba(34,197,94,0.4); color:#16A34A; font-weight:800; font-size:12px; height:36px; padding:0 14px; display:inline-flex; align-items:center; gap:6px;" onclick="openModalRewardSiswa()">
            <i class="bi bi-gift-fill"></i> + Beri Self-Reward
          </button>
          <button type="button" class="btn btn-gold" style="font-weight:800; font-size:12px; height:36px; padding:0 14px; display:inline-flex; align-items:center; gap:6px;" onclick="openModalLog()">
            <i class="bi bi-chat-left-text-fill"></i> + Catat Log
          </button>
          <button type="button" class="btn btn-outline" style="font-size:12px; font-weight:700; height:36px; padding:0 12px; display:inline-flex; align-items:center; gap:6px;" onclick="openModalUpload()">
            <i class="bi bi-cloud-arrow-up-fill"></i> + Upload Bukti
          </button>
          <button type="button" class="btn btn-outline" style="border-color:var(--border-2); color:var(--text); font-weight:700; font-size:12px; height:36px; padding:0 12px; display:inline-flex; align-items:center; gap:6px;" onclick="openModalTindakLanjutDossier()">
            <i class="bi bi-arrow-up-right-circle-fill" style="color:var(--navy);"></i> Eskalasi Tahap
          </button>
          <a href="{{ route('admin.disiplin.resume.cetak', $kasus->id) }}" target="_blank" class="btn btn-outline-mono" style="font-weight:800; font-size:12px; height:36px; padding:0 14px; display:inline-flex; align-items:center; gap:6px; text-decoration:none;">
            <i class="bi bi-printer-fill" style="color:#000000;"></i> Resume Kepsek A4
          </a>
        </div>
      </div>
    </div>

    {{-- MAIN DOSSIER GRID --}}
    <div class="dossier-grid">
      
      {{-- LEFT COLUMN: TIMELINE & EVIDENCE VAULT --}}
      <div>
        
        {{-- SECTION 1: TIMELINE KRONOLOGIS INTERAKSI --}}
        <div class="panel" style="margin-bottom: 24px;">
          <div class="panel-title" style="margin-bottom: 20px;">
            <span><i class="bi bi-clock-history" style="color:#000000;"></i> Timeline Kronologis Pembinaan (Audit Trail)</span>
            <button type="button" class="btn btn-sm btn-outline" onclick="openModalLog()">
              <i class="bi bi-plus"></i> Tambah Peristiwa
            </button>
          </div>

          <div class="timeline-container">
            @forelse($kasus->logs as $log)
              <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-card">
                  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; flex-wrap:wrap; gap:6px;">
                    <div>
                      <strong style="color:var(--text); font-size:14px;">{{ $log->judul_kegiatan }}</strong>
                      <span class="badge" style="background:var(--bg-3); font-size:10px; margin-left:6px;">
                        {{ str_replace('_', ' ', strtoupper($log->tahap)) }}
                      </span>
                    </div>
                    <div style="font-size:11px; font-family:var(--font-mono); color:var(--text-3); font-weight:700;">
                      <i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($log->tanggal_kegiatan)->translatedFormat('d F Y') }}
                    </div>
                  </div>

                  <p style="font-size:12.5px; color:var(--text-2); margin:0 0 8px 0; line-height:1.5;">
                    {{ $log->uraian_tindakan }}
                  </p>

                  <div style="display:flex; justify-content:space-between; align-items:center; font-size:11px; color:var(--text-3); border-top:1px solid var(--border); padding-top:6px;">
                    <div>
                      Dicatat oleh: <strong style="color:var(--text);">{{ $log->petugas_nama }}</strong> ({{ $log->petugas_role }})
                    </div>
                    @if($log->poin_perubahan != 0)
                      <span class="badge" style="background:{{ $log->poin_perubahan > 0 ? 'rgba(239,68,68,0.12)' : 'rgba(34,197,94,0.12)' }}; color:{{ $log->poin_perubahan > 0 ? '#DC2626' : '#16A34A' }}; font-weight:800;">
                        {{ $log->poin_perubahan > 0 ? '+' . $log->poin_perubahan : $log->poin_perubahan }} Poin
                      </span>
                    @endif
                  </div>
                </div>
              </div>
            @empty
              <div style="text-align:center; padding:30px; color:var(--text-3);">
                <i class="bi bi-chat-square-dots" style="font-size:32px; opacity:0.5;"></i>
                <div style="font-weight:700; margin-top:8px;">Belum ada riwayat interaksi yang dicatat</div>
                <p style="font-size:12px;">Gunakan tombol "+ Catat Log" di atas untuk mencatat panggilan telepon, home visit, atau konseling.</p>
              </div>
            @endforelse
          </div>
        </div>

        {{-- SECTION 2: BRANKAS BUKTI DIGITAL (DIGITAL EVIDENCE VAULT) --}}
        <div class="panel" style="margin-bottom: 24px;">
          <div class="panel-title" style="margin-bottom: 16px;">
            <span><i class="bi bi-shield-lock-fill" style="color:#000000;"></i> Brankas Bukti Digital (Surat Pernyataan &amp; Foto)</span>
            <button type="button" class="btn btn-sm btn-outline" onclick="openModalUpload()">
              <i class="bi bi-upload"></i> Unggah Berkas
            </button>
          </div>

          <div class="vault-grid">
            @forelse($kasus->dokumens as $dok)
              <div class="vault-card">
                <div>
                  <div style="margin-bottom:6px;">{!! $dok->kategori_badge !!}</div>
                  <strong style="color:var(--text); font-size:12.5px; display:block; margin-bottom:4px;">{{ $dok->judul_dokumen }}</strong>
                  <div style="font-size:10.5px; color:var(--text-3);">
                    {{ $dok->created_at->translatedFormat('d M Y - H:i') }}
                  </div>
                </div>

                <div style="margin-top:12px; display:flex; justify-content:space-between; align-items:center;">
                  <a href="{{ $dok->file_url }}" target="_blank" class="btn btn-sm btn-outline" style="font-size:11px; padding:3px 8px;">
                    <i class="bi bi-eye-fill"></i> Buka File
                  </a>
                  <form action="{{ route('admin.disiplin.dokumen.destroy', ['id' => $kasus->id, 'dokumenId' => $dok->id]) }}" method="POST" onsubmit="return confirm('Hapus berkas bukti ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" style="padding:3px 8px;" title="Hapus Dokumen">
                      <i class="bi bi-trash3-fill"></i>
                    </button>
                  </form>
                </div>
              </div>
            @empty
              <div style="grid-column:1/-1; text-align:center; padding:24px; color:var(--text-3);">
                <i class="bi bi-folder-x" style="font-size:32px; opacity:0.5;"></i>
                <div style="font-weight:700; margin-top:6px;">Brankas bukti digital masih kosong</div>
                <p style="font-size:11.5px;">Unggah scan surat pernyataan bermaterai, foto pertemuan di BK, atau foto home visit.</p>
              </div>
            @endforelse
          </div>
        </div>

        {{-- SECTION 3: RIWAYAT SELF-REWARD & POIN PEMULIHAN --}}
        <div class="panel" style="margin-bottom:24px;">
          <div class="panel-title" style="display:flex; justify-content:space-between; align-items:center;">
            <span><i class="bi bi-gift-fill" style="color:#16A34A;"></i> Riwayat Self-Reward &amp; Poin Pemulihan</span>
            <button type="button" class="btn btn-sm btn-outline" style="border-color:#16A34A; color:#16A34A; font-weight:800; font-size:11px;" onclick="openModalRewardSiswa()">
              <i class="bi bi-plus-lg"></i> + Beri Reward
            </button>
          </div>

          <div style="display:flex; flex-direction:column; gap:10px;">
            @forelse($kasus->rewards as $rew)
              <div style="background:var(--bg-3); border:1px solid rgba(34,197,94,0.3); border-radius:8px; padding:12px 14px; display:flex; justify-content:space-between; align-items:center;">
                <div>
                  <div style="display:flex; align-items:center; gap:8px;">
                    <span class="badge" style="background:rgba(34,197,94,0.15); color:#16A34A; font-weight:900; font-size:12px;">
                      -{{ $rew->poin_dikurangi }} Poin
                    </span>
                    <strong style="color:var(--text); font-size:13px;">{{ $rew->nama_tindakan }}</strong>
                    @if($rew->katalogReward)
                      <span class="badge" style="background:var(--bg-2); color:var(--text-3); font-size:10px; text-transform:uppercase;">
                        {{ $rew->katalogReward->kategori }}
                      </span>
                    @endif
                  </div>
                  <div style="font-size:11.5px; color:var(--text-2); margin-top:4px;">
                    {{ $rew->catatan ?: 'Reward dan pemulihan poin kedisiplinan siswa.' }}
                  </div>
                  <div style="font-size:10.5px; color:var(--text-3); margin-top:3px;">
                    <i class="bi bi-person-check-fill" style="color:#16A34A;"></i> {{ $rew->dicatat_oleh }} · <span style="font-family:var(--font-mono);">{{ \Carbon\Carbon::parse($rew->tanggal)->translatedFormat('d F Y') }}</span>
                  </div>
                </div>

                <form action="{{ route('admin.disiplin.reward.destroy', ['id' => $kasus->id, 'rewardId' => $rew->id]) }}" method="POST" onsubmit="return confirm('Batalkan reward ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline" style="color:var(--red); border-color:rgba(239,68,68,0.3); padding:3px 8px; font-size:11px;" title="Batalkan Reward">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            @empty
              <div style="text-align:center; padding:24px; color:var(--text-3);">
                <i class="bi bi-gift" style="font-size:32px; opacity:0.4; display:block; margin-bottom:6px;"></i>
                Belum ada catatan reward atau aksi pemulihan poin untuk siswa ini.
              </div>
            @endforelse
          </div>
        </div>

        {{-- SECTION 4: RIWAYAT PELANGGARAN MANUAL / TAMBAHAN --}}
        <div class="panel" style="margin-bottom:24px;">
          <div class="panel-title" style="display:flex; justify-content:space-between; align-items:center;">
            <span><i class="bi bi-exclamation-triangle-fill" style="color:#DC2626;"></i> Riwayat Pelanggaran Manual / Tambahan</span>
            <button type="button" class="btn btn-sm btn-outline" style="border-color:#DC2626; color:#DC2626; font-weight:800; font-size:11px;" onclick="openModalPelanggaranSiswa()">
              <i class="bi bi-plus-lg"></i> + Catat Pelanggaran
            </button>
          </div>

          <div style="display:flex; flex-direction:column; gap:10px;">
            @forelse($kasus->pelanggarans as $pel)
              <div style="background:var(--bg-3); border:1px solid rgba(239,68,68,0.3); border-radius:8px; padding:12px 14px; display:flex; justify-content:space-between; align-items:center;">
                <div>
                  <div style="display:flex; align-items:center; gap:8px;">
                    <span class="badge" style="background:rgba(239,68,68,0.15); color:#DC2626; font-weight:900; font-size:12px;">
                      +{{ $pel->poin_ditambah }} Poin
                    </span>
                    <strong style="color:var(--text); font-size:13px;">{{ $pel->nama_pelanggaran }}</strong>
                    @if($pel->katalogPelanggaran)
                      <span class="badge" style="background:var(--bg-2); color:var(--text-3); font-size:10px; text-transform:uppercase;">
                        {{ $pel->katalogPelanggaran->kategori }}
                      </span>
                    @endif
                  </div>
                  <div style="font-size:11.5px; color:var(--text-2); margin-top:4px;">
                    {{ $pel->catatan ?: 'Pelanggaran tata tertib / kedisiplinan sekolah.' }}
                  </div>
                  <div style="font-size:10.5px; color:var(--text-3); margin-top:3px;">
                    <i class="bi bi-person-exclamation" style="color:#DC2626;"></i> {{ $pel->dicatat_oleh }} · <span style="font-family:var(--font-mono);">{{ \Carbon\Carbon::parse($pel->tanggal)->translatedFormat('d F Y') }}</span>
                  </div>
                </div>

                <form action="{{ route('admin.disiplin.pelanggaran.destroy', ['id' => $kasus->id, 'pelanggaranId' => $pel->id]) }}" method="POST" onsubmit="return confirm('Hapus catatan pelanggaran ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline" style="color:var(--red); border-color:rgba(239,68,68,0.3); padding:3px 8px; font-size:11px;" title="Hapus Catatan Pelanggaran">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            @empty
              <div style="text-align:center; padding:24px; color:var(--text-3);">
                <i class="bi bi-shield-check" style="font-size:32px; opacity:0.4; display:block; margin-bottom:6px; color:#16A34A;"></i>
                Belum ada pelanggaran manual tambahan di luar presensi harian.
              </div>
            @endforelse
          </div>
        </div>

      </div>

      {{-- RIGHT COLUMN: SKOR POIN, REKAP PRESENSI & KONTAK ORTU --}}
      <div>
        
        {{-- CARD 1: SKOR & AKUMULASI POIN --}}
        <div class="panel" style="margin-bottom:20px; border:1px solid var(--border); border-radius:var(--r-md); padding:18px; background:var(--bg-2);">
          <div class="panel-title" style="margin-bottom:14px; display:flex; align-items:center; gap:8px;">
            <i class="bi bi-speedometer2" style="color:#000000; font-size:18px;"></i>
            <span style="font-weight:900; font-size:15px; color:var(--text);">Skor &amp; Kredit Disiplin</span>
          </div>

          <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:16px 14px; text-align:center; margin-bottom:14px;">
            <div style="font-size:11px; font-weight:800; color:var(--text-2); text-transform:uppercase; letter-spacing:0.5px;">Akumulasi Poin Bersih</div>
            <div style="font-size:44px; font-weight:900; font-family:var(--font-mono); line-height:1.1; margin:4px 0; color:{{ $kasus->poin_bersih >= $pengaturanDisiplin->ambang_tahap_4_kepsek ? '#DC2626' : ($kasus->poin_bersih >= $pengaturanDisiplin->ambang_tahap_3_wakasis ? '#EA580C' : ($kasus->poin_bersih >= $pengaturanDisiplin->ambang_tahap_2_bk ? '#2563EB' : ($kasus->poin_bersih >= $pengaturanDisiplin->ambang_tahap_1_wali ? '#CA8A04' : '#16A34A'))) }};">
              {{ $kasus->poin_bersih }}
            </div>
            <div style="font-size:11.5px; color:var(--text); font-weight:800; margin-top:2px;">
              @if($kasus->poin_bersih >= $pengaturanDisiplin->ambang_tahap_4_kepsek) 🚨 Tahap 4 – Kepala Sekolah
              @elseif($kasus->poin_bersih >= $pengaturanDisiplin->ambang_tahap_3_wakasis) ⚠️ Tahap 3 – Waka Kesiswaan
              @elseif($kasus->poin_bersih >= $pengaturanDisiplin->ambang_tahap_2_bk) 🔔 Tahap 2 – Guru BK
              @elseif($kasus->poin_bersih >= $pengaturanDisiplin->ambang_tahap_1_wali) 📓 Tahap 1 – Wali Kelas
              @else ✅ Bersih / Selesai Pembinaan
              @endif
            </div>
            <div style="font-size:11.5px; color:var(--text-2); margin-top:8px; border-top:1px solid var(--border); padding-top:8px;">
              Pelanggaran: <strong style="color:var(--text);">{{ $kasus->total_poin_pelanggaran }}</strong> &nbsp;|&nbsp; Pemulihan: <strong style="color:#16A34A; font-weight:800;">-{{ $kasus->total_poin_pemulihan }}</strong>
            </div>
          </div>

          {{-- Breakdown kontribusi poin per jenis --}}
          @php
            $tolPiket = (int) ($pengaturanDisiplin->toleransi_terlambat_piket ?? 2);
            $bLate = (int) ($pengaturanDisiplin->bobot_terlambat ?? 3);
            $bAlpha = (int) ($pengaturanDisiplin->bobot_alpha ?? 10);
            $bBolos = (int) ($pengaturanDisiplin->bobot_bolos ?? 15);
            $hitungLate = max(0, $kasus->total_terlambat - $tolPiket);
            $poinLate = $hitungLate * $bLate;
            $poinAlpha = $kasus->total_alpha * $bAlpha;
            $poinBolos = $kasus->total_bolos * $bBolos;
            $totalManualPelanggaran = $kasus->pelanggarans->sum('poin_ditambah');
          @endphp
          <div style="background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:12px 14px; margin-bottom:14px; font-size:12px;">
            <div style="font-weight:800; color:var(--text-2); font-size:11px; text-transform:uppercase; letter-spacing:0.4px; margin-bottom:8px;">Rincian Poin Pelanggaran</div>
            <div style="display:flex; justify-content:space-between; margin-bottom:5px; align-items:center;">
              <span style="color:var(--text-2); font-weight:600;"><i class="bi bi-x-circle-fill" style="color:#DC2626; margin-right:4px;"></i> Alpha ({{ $kasus->total_alpha }}x × {{ $bAlpha }}):</span>
              <strong style="color:#DC2626; font-family:var(--font-mono); font-weight:800;">{{ $poinAlpha }} poin</strong>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:5px; align-items:center;">
              <span style="color:var(--text-2); font-weight:600;"><i class="bi bi-door-open-fill" style="color:#D97706; margin-right:4px;"></i> Bolos ({{ $kasus->total_bolos }}x × {{ $bBolos }}):</span>
              <strong style="color:#D97706; font-family:var(--font-mono); font-weight:800;">{{ $poinBolos }} poin</strong>
            </div>
            <div style="display:flex; justify-content:space-between; border-top:1px solid var(--border); padding-top:5px; margin-top:4px; align-items:center;">
              <span style="color:var(--text-2); font-weight:600;"><i class="bi bi-clock-history" style="color:#2563EB; margin-right:4px;"></i> Terlambat ({{ $hitungLate }}x × {{ $bLate }}):</span>
              <strong style="color:#2563EB; font-family:var(--font-mono); font-weight:800;">{{ $poinLate }} poin</strong>
            </div>
            @if($totalManualPelanggaran > 0)
              <div style="display:flex; justify-content:space-between; border-top:1px solid var(--border); padding-top:5px; margin-top:4px; align-items:center;">
                <span style="color:var(--text-2); font-weight:600;"><i class="bi bi-exclamation-triangle-fill" style="color:#DC2626; margin-right:4px;"></i> Pelanggaran Manual ({{ $kasus->pelanggarans->count() }}x):</span>
                <strong style="color:#DC2626; font-family:var(--font-mono); font-weight:800;">+{{ $totalManualPelanggaran }} poin</strong>
              </div>
            @endif
            @if($kasus->total_poin_pemulihan > 0)
              <div style="display:flex; justify-content:space-between; border-top:1px solid rgba(34,197,94,0.3); padding-top:5px; margin-top:6px; align-items:center;">
                <span style="color:#16A34A; font-weight:700;"><i class="bi bi-gift-fill" style="margin-right:4px;"></i> Total Self-Reward:</span>
                <strong style="color:#16A34A; font-family:var(--font-mono); font-weight:800;">-{{ $kasus->total_poin_pemulihan }} poin</strong>
              </div>
            @endif
          </div>

          <div style="display:flex; flex-direction:column; gap:8px; font-size:12.5px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <span style="color:var(--text-2);"><i class="bi bi-x-circle-fill" style="color:#DC2626; margin-right:4px;"></i> Total Alpha:</span>
              <strong style="color:#DC2626; font-weight:800;">{{ $kasus->total_alpha }} Hari</strong>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <span style="color:var(--text-2);"><i class="bi bi-door-open-fill" style="color:#D97706; margin-right:4px;"></i> Total Bolos:</span>
              <strong style="color:#D97706; font-weight:800;">{{ $kasus->total_bolos }} Kali</strong>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <span style="color:var(--text-2);"><i class="bi bi-clock-history" style="color:#2563EB; margin-right:4px;"></i> Total Terlambat:</span>
              <strong style="color:#2563EB; font-weight:800;">{{ $kasus->total_terlambat }} Kali</strong>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border); padding-top:8px; margin-top:2px;">
              <span style="color:var(--text-2); font-weight:700;">Kehadiran Efektif:</span>
              <strong style="color:#000000; font-size:14px; font-weight:900;">{{ $persenKehadiran }}%</strong>
            </div>
          </div>
        </div>

        {{-- CARD 2: KONTAK ORANG TUA --}}
        <div class="panel" style="margin-bottom:20px; border:1px solid var(--border); border-radius:var(--r-md); padding:18px; background:var(--bg-2);">
          <div class="panel-title" style="margin-bottom:12px; display:flex; align-items:center; gap:8px;">
            <i class="bi bi-telephone-fill" style="color:#16A34A; font-size:16px;"></i>
            <span style="font-weight:900; font-size:15px; color:var(--text);">Komunikasi Orang Tua</span>
          </div>

          <div style="font-size:12.5px; margin-bottom:14px; background:var(--bg-3); border:1px solid var(--border-2); border-radius:var(--r-sm); padding:12px 14px;">
            <div style="color:var(--text-2); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px;">Nama Orang Tua / Wali:</div>
            <strong style="color:var(--text); font-size:14px; display:block; margin-top:2px;">{{ $siswa->nama_ortu ?: 'Bapak/Ibu Orang Tua' }}</strong>
            <div style="color:var(--text-2); font-size:12px; margin-top:4px; font-weight:600;">
              No. HP/WA: <span style="font-family:var(--font-mono); color:var(--text); font-weight:800;">{{ $siswa->nomor_hp_ortu ?: 'Belum terdata' }}</span>
            </div>
          </div>

          @if($siswa->nomor_hp_ortu)
            @php
              $cleanHp = preg_replace('/[^0-9]/', '', $siswa->nomor_hp_ortu);
              if (str_starts_with($cleanHp, '0')) {
                $cleanHp = '62' . substr($cleanHp, 1);
              }
            @endphp
            <a href="https://wa.me/{{ $cleanHp }}?text=Assalamu'alaikum%20Bapak/Ibu%20Wali%20dari%20{{ urlencode($siswa->nama) }},%20kami%20dari%20pihak%20sekolah%20SMKN%201%20Air%20Naningan%20ingin%20berkoordinasi%20mengenai%20kehadiran%20putra/putri%20Bapak/Ibu." target="_blank" class="btn btn-outline" style="width:100%; justify-content:center; border-color:rgba(22,163,74,0.4); color:#16A34A; font-weight:800; font-size:12.5px; height:38px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
              <i class="bi bi-whatsapp" style="font-size:16px;"></i> Hubungi WhatsApp Ortu
            </a>
          @endif
        </div>

        {{-- CARD 3: FORMAT SURAT RESMI 1-KLIK --}}
        <div class="panel" style="margin-bottom:20px; border:1px solid var(--border); border-radius:var(--r-md); padding:18px; background:var(--bg-2);">
          <div class="panel-title" style="margin-bottom:12px; display:flex; align-items:center; gap:8px;">
            <i class="bi bi-printer-fill" style="color:#000000; font-size:16px;"></i>
            <span style="font-weight:900; font-size:15px; color:var(--text);">Berkas Cetak Resmi</span>
          </div>

          <div style="display:flex; flex-direction:column; gap:8px;">
            <a href="{{ route('surat.cetak', ['siswa_id' => $siswa->id, 'kategori' => 'panggilan_ortu']) }}" target="_blank" class="btn btn-sm btn-outline-mono" style="justify-content:flex-start; text-align:left; font-size:12px; font-weight:800; height:36px; padding:0 12px; text-decoration:none;" title="Cetak Surat Panggilan Orang Tua">
              <i class="bi bi-envelope-paper-fill" style="color:#000000; margin-right:6px;"></i> Surat Panggilan Ortu (A4)
            </a>
            <a href="{{ route('surat.cetak', ['siswa_id' => $siswa->id, 'kategori' => 'berita_acara']) }}" target="_blank" class="btn btn-sm btn-outline-mono" style="justify-content:flex-start; text-align:left; font-size:12px; font-weight:800; height:36px; padding:0 12px; text-decoration:none;" title="Cetak Berita Acara Musyawarah">
              <i class="bi bi-file-earmark-ruled-fill" style="color:#000000; margin-right:6px;"></i> Berita Acara Musyawarah (A4)
            </a>
            <a href="{{ route('surat.cetak', ['siswa_id' => $siswa->id, 'kategori' => 'pembinaan']) }}" target="_blank" class="btn btn-sm btn-outline-mono" style="justify-content:flex-start; text-align:left; font-size:12px; font-weight:800; height:36px; padding:0 12px; text-decoration:none;" title="Cetak Surat Peringatan Siswa">
              <i class="bi bi-exclamation-triangle-fill" style="color:#000000; margin-right:6px;"></i> Surat Peringatan Siswa (SP)
            </a>
            <a href="{{ route('admin.disiplin.resume.cetak', $kasus->id) }}" target="_blank" class="btn btn-sm btn-gold" style="justify-content:flex-start; text-align:left; font-size:12px; font-weight:800; height:36px; padding:0 12px; text-decoration:none;" title="Cetak Resume Rekam Jejak Kepsek">
              <i class="bi bi-file-earmark-medical-fill" style="margin-right:6px;"></i> Resume Rekam Jejak Kepsek (A4)
            </a>
          </div>
        </div>

        {{-- CARD 4: RIWAYAT NOTIFIKASI WHATSAPP KASUS --}}
        <div class="panel" style="margin-top: 20px;">
          <div class="panel-title" style="margin-bottom: 12px;">
            <span><i class="bi bi-whatsapp" style="color:#22C55E;"></i> Log Notifikasi WhatsApp</span>
            <span class="badge" style="background:var(--bg-3); font-size:10px;">{{ count($notifikasiList ?? []) }} Pesan</span>
          </div>

          <div style="display:flex; flex-direction:column; gap:10px;">
            @forelse($notifikasiList ?? [] as $ntf)
              <div style="background:var(--bg-3); border:1px solid var(--border); border-radius:var(--r-sm); padding:10px 12px; font-size:11.5px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                  <strong style="color:var(--text); font-size:11.5px;">
                    {{ str_replace('_', ' ', ucwords($ntf->kategori)) }}
                  </strong>
                  @if($ntf->status === 'terkirim')
                    <span class="badge" style="background:rgba(34,197,94,0.15); color:#22C55E; font-size:10px; font-weight:800;">
                      <i class="bi bi-check2-all"></i> Terkirim
                    </span>
                  @elseif($ntf->status === 'pending')
                    <span class="badge" style="background:rgba(234,179,8,0.15); color:#CA8A04; font-size:10px; font-weight:800;">
                      <i class="bi bi-hourglass-split"></i> Pending
                    </span>
                  @elseif($ntf->status === 'dibatalkan')
                    <span class="badge" style="background:rgba(100,116,139,0.15); color:#64748B; font-size:10px;">
                      Dibatalkan
                    </span>
                  @else
                    <span class="badge" style="background:rgba(239,68,68,0.15); color:#DC2626; font-size:10px; font-weight:800;">
                      <i class="bi bi-x-circle-fill"></i> Gagal
                    </span>
                  @endif
                </div>

                <div style="color:var(--text-2); font-family:var(--font-mono); font-size:11px; margin-bottom:4px;">
                  <i class="bi bi-telephone-outbound"></i> {{ $ntf->no_tujuan ?: '(Tanpa No HP)' }}
                </div>

                <div style="color:var(--text-3); font-size:10.5px; line-height:1.4;">
                  {{ Str::limit($ntf->pesan, 70) }}
                </div>

                @if($ntf->status === 'gagal' && $ntf->catatan_error)
                  <div style="color:#DC2626; font-size:10px; margin-top:4px; font-weight:700;">
                    Error: {{ $ntf->catatan_error }}
                  </div>
                @endif

                <div style="font-size:10px; color:var(--text-3); margin-top:4px; text-align:right; font-family:var(--font-mono);">
                  {{ $ntf->created_at->format('d/m/Y H:i') }}
                </div>
              </div>
            @empty
              <div style="text-align:center; padding:14px; color:var(--text-3); font-size:11.5px;">
                Belum ada pengiriman notifikasi WhatsApp untuk kasus ini.
              </div>
            @endforelse
          </div>
        </div>

      </div>

    </div>
  </main>
</div>

{{-- MODAL TAMBAH LOG INTERAKSI --}}
<div class="modal-overlay" id="modalLog">
  <div class="modal-card" style="max-width:520px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h3 style="font-size:17px; font-weight:900; color:var(--text); margin:0;">
        <i class="bi bi-chat-left-text-fill" style="color:var(--gold);"></i> Catat Log Interaksi Pembinaan
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModalLog()"><i class="bi bi-x-lg"></i></button>
    </div>

    <form action="{{ route('admin.disiplin.log.store', $kasus->id) }}" method="POST">
      @csrf
      <div style="margin-bottom:12px;">
        <label class="form-label" style="font-weight:700; font-size:12.5px; display:block; margin-bottom:4px;">Judul Kegiatan / Bentuk Interaksi <span style="color:var(--red);">*</span></label>
        <input type="text" name="judul_kegiatan" class="input-field" placeholder="Misal: Panggilan Telepon Ortu / Home Visit / Konseling BK" required style="width:100%;" />
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
        <div>
          <label class="form-label" style="font-weight:700; font-size:12.5px; display:block; margin-bottom:4px;">Tahap Pelaksana <span style="color:var(--red);">*</span></label>
          <select name="tahap" class="input-field" style="width:100%;" required>
            <option value="tahap_1_wali_kelas" {{ $kasus->status_tahap === 'tahap_1_wali_kelas' ? 'selected' : '' }}>Tahap 1: Wali Kelas</option>
            <option value="tahap_2_bk" {{ $kasus->status_tahap === 'tahap_2_bk' ? 'selected' : '' }}>Tahap 2: Guru BK</option>
            <option value="tahap_3_wakasis" {{ $kasus->status_tahap === 'tahap_3_wakasis' ? 'selected' : '' }}>Tahap 3: Wakasis</option>
            <option value="tahap_4_kepsek" {{ $kasus->status_tahap === 'tahap_4_kepsek' ? 'selected' : '' }}>Tahap 4: Kepsek</option>
            <option value="home_visit">Home Visit (Kunjungan Rumah)</option>
            <option value="pemulihan_sikap">Pemulihan Sikap / Prestasi</option>
          </select>
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12.5px; display:block; margin-bottom:4px;">Tanggal Kegiatan <span style="color:var(--red);">*</span></label>
          <input type="date" name="tanggal_kegiatan" class="input-field" value="{{ \Carbon\Carbon::today()->toDateString() }}" required style="width:100%;" />
        </div>
      </div>

      <div style="margin-bottom:12px;">
        <label class="form-label" style="font-weight:700; font-size:12.5px; display:block; margin-bottom:4px;">Uraian Hasil Pembinaan / Pembicaraan <span style="color:var(--red);">*</span></label>
        <textarea name="uraian_tindakan" class="input-field" rows="4" placeholder="Tuliskan detail pembicaraan dengan orang tua, alasan siswa, atau komitmen yang disepakati..." required style="width:100%;"></textarea>
      </div>

      <div style="margin-bottom:18px;">
        <label class="form-label" style="font-weight:700; font-size:12.5px; display:block; margin-bottom:4px;">Penyesuaian Poin Disiplin</label>
        <input type="number" name="poin_perubahan" class="input-field" placeholder="0 (Gunakan tanda minus untuk poin pemulihan, misal: -10)" style="width:100%;" />
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModalLog()">Batal</button>
        <button type="submit" class="btn btn-gold">Simpan ke Timeline</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL UPLOAD BUKTI DOKUMEN --}}
<div class="modal-overlay" id="modalUpload">
  <div class="modal-card" style="max-width:500px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h3 style="font-size:17px; font-weight:900; color:var(--text); margin:0;">
        <i class="bi bi-cloud-arrow-up-fill" style="color:var(--gold);"></i> Unggah Bukti Fisik Digital
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModalUpload()"><i class="bi bi-x-lg"></i></button>
    </div>

    <form action="{{ route('admin.disiplin.dokumen.upload', $kasus->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div style="margin-bottom:12px;">
        <label class="form-label" style="font-weight:700; font-size:12.5px; display:block; margin-bottom:4px;">Nama / Judul Dokumen <span style="color:var(--red);">*</span></label>
        <input type="text" name="judul_dokumen" class="input-field" placeholder="Misal: Scan Surat Pernyataan SP 2 Bermaterai" required style="width:100%;" />
      </div>

      <div style="margin-bottom:12px;">
        <label class="form-label" style="font-weight:700; font-size:12.5px; display:block; margin-bottom:4px;">Kategori Berkas <span style="color:var(--red);">*</span></label>
        <select name="kategori" class="input-field" style="width:100%;" required>
          <option value="surat_pernyataan">Surat Pernyataan / Perjanjian SP</option>
          <option value="foto_dokumentasi">Foto Pertemuan / Home Visit</option>
          <option value="berita_acara">Berita Acara Pembinaan</option>
          <option value="surat_dokter">Surat Keterangan Dokter</option>
          <option value="lainnya">Berkas Lampiran Lainnya</option>
        </select>
      </div>

      <div style="margin-bottom:18px;">
        <label class="form-label" style="font-weight:700; font-size:12.5px; display:flex; justify-content:space-between; margin-bottom:4px;">
          <span>Pilih File (JPG, PNG, PDF - Max 5MB) <span style="color:var(--red);">*</span></span>
          <span style="color:var(--gold); font-size:11px;"><i class="bi bi-crop"></i> Auto-Crop Gambar</span>
        </label>
        <input type="file" name="file" id="inputDokumenDisiplin" class="input-field" accept=".jpg,.jpeg,.png,.webp,.pdf" onchange="initPhotoCrop(this, null, 'free', 'Sesuaikan & Potong Bukti Dokumen')" required style="width:100%;" />
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModalUpload()">Batal</button>
        <button type="submit" class="btn btn-gold">Unggah ke Brankas</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL TINDAK LANJUT / ESKALASI --}}
<div class="modal-overlay" id="modalEskalasiDossier">
  <div class="modal-card" style="max-width:520px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
      <h3 style="font-size:17px; font-weight:900; color:var(--text); margin:0;">
        <i class="bi bi-arrow-up-right-circle-fill" style="color:var(--gold);"></i> Eskalasi Tahap Pembinaan
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModalTindakLanjutDossier()"><i class="bi bi-x-lg"></i></button>
    </div>

    <form action="{{ route('admin.disiplin.tindak-lanjut', $kasus->id) }}" method="POST">
      @csrf
      <div style="margin-bottom:14px;">
        <label class="form-label" style="font-weight:700; font-size:12.5px; display:block; margin-bottom:4px;">Pilih Tahap Baru <span style="color:var(--red);">*</span></label>
        <select name="status_tahap_baru" class="input-field" style="width:100%;" required>
          <option value="tahap_1_wali_kelas" {{ $kasus->status_tahap === 'tahap_1_wali_kelas' ? 'selected' : '' }}>Tahap 1: Pembinaan Wali Kelas</option>
          <option value="tahap_2_bk" {{ $kasus->status_tahap === 'tahap_2_bk' ? 'selected' : '' }}>Tahap 2: Konseling &amp; Panggilan Guru BK</option>
          @if($user->isAdmin() || $user->isGuruBk() || $user->isWakaKesiswaan() || $user->isKepalaSekolah())
            <option value="tahap_3_wakasis" {{ $kasus->status_tahap === 'tahap_3_wakasis' ? 'selected' : '' }}>Tahap 3: Sidang &amp; Sanksi Waka Kesiswaan</option>
          @endif
          @if($user->isAdmin() || $user->isWakaKesiswaan() || $user->isKepalaSekolah())
            <option value="tahap_4_kepsek" {{ $kasus->status_tahap === 'tahap_4_kepsek' ? 'selected' : '' }}>Tahap 4: Keputusan Kepala Sekolah</option>
          @endif
          <option value="selesai_pembinaan">✅ Selesaikan Pembinaan</option>
        </select>
      </div>

      <div style="margin-bottom:14px;">
        <label class="form-label" style="font-weight:700; font-size:12.5px; display:block; margin-bottom:4px;">Catatan Tindakan <span style="color:var(--red);">*</span></label>
        <textarea name="catatan_tindakan" class="input-field" rows="3" placeholder="Catat alasan eskalasi atau sanksi yang ditetapkan..." required style="width:100%;"></textarea>
      </div>

      <div style="margin-bottom:18px;">
        <label class="form-label" style="font-weight:700; font-size:12.5px; display:block; margin-bottom:4px;">Sanksi Tambahan (Opsional)</label>
        <input type="text" name="sanksi_tambahan" class="input-field" placeholder="Misal: Penandatanganan SP 2 / Skorsing 3 hari" style="width:100%;" />
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModalTindakLanjutDossier()">Batal</button>
        <button type="submit" class="btn btn-gold">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL BERI SELF-REWARD & KURANGI POIN --}}
<div class="modal-overlay" id="modalRewardSiswa">
  <div class="modal-card" style="max-width:540px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:10px;">
      <h3 style="font-size:17px; font-weight:900; color:var(--text); margin:0; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-gift-fill" style="color:#16A34A;"></i> Berikan Self-Reward / Pengurangan Poin
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModalRewardSiswa()"><i class="bi bi-x-lg"></i></button>
    </div>

    <div style="background:var(--bg-3); border-radius:var(--r-sm); padding:10px 14px; margin-bottom:14px; font-size:12.5px;">
      Siswa: <strong style="color:var(--gold);">{{ $siswa->nama }}</strong> · Poin Bersih Saat Ini: <strong style="color:var(--text);">{{ $kasus->poin_bersih }} Poin</strong>
    </div>

    <form action="{{ route('admin.disiplin.reward.store', $kasus->id) }}" method="POST">
      @csrf
      <div style="margin-bottom:14px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Pilih dari Katalog Master Reward</label>
        <select id="selectKatalogReward" name="katalog_reward_id" class="input-field" style="width:100%;" onchange="autoFillReward(this)">
          <option value="">-- Tindakan Kustom / Manual --</option>
          @foreach($katalogRewards as $kr)
            <option value="{{ $kr->id }}" data-nama="{{ $kr->nama_reward }}" data-poin="{{ $kr->poin_deduksi }}" data-desc="{{ $kr->deskripsi }}">
              {{ $kr->nama_reward }} (-{{ $kr->poin_deduksi }} Poin) [{{ strtoupper($kr->kategori) }}]
            </option>
          @endforeach
        </select>
      </div>

      <div style="margin-bottom:14px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Nama Tindakan / Aksi Positif <span style="color:var(--red);">*</span></label>
        <input type="text" id="inputRewardNama" name="nama_tindakan" class="input-field" placeholder="Misal: Menyelesaikan Tugas Karakter / Juara Lomba" required style="width:100%;" />
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Poin Pengurangan <span style="color:var(--red);">*</span></label>
          <input type="number" id="inputRewardPoin" name="poin_dikurangi" class="input-field" value="5" min="1" max="200" required style="width:100%; font-family:var(--font-mono); font-weight:800;" />
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Tanggal Perolehan <span style="color:var(--red);">*</span></label>
          <input type="date" name="tanggal" class="input-field" value="{{ date('Y-m-d') }}" required style="width:100%; font-family:var(--font-mono);" />
        </div>
      </div>

      <div style="margin-bottom:18px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Catatan Tambahan / Hasil Pembinaan (Opsional)</label>
        <textarea id="inputRewardCatatan" name="catatan" class="input-field" rows="2" placeholder="Tuliskan apresiasi atau keterangan pelaksanaan reward..." style="width:100%;"></textarea>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModalRewardSiswa()">Batal</button>
        <button type="submit" class="btn btn-gold" style="background:#16A34A; border-color:#16A34A;">
          <i class="bi bi-gift-fill"></i> Simpan Reward &amp; Kurangi Poin
        </button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL CATAT PELANGGARAN MANUAL & TAMBAHAN --}}
<div class="modal-overlay" id="modalPelanggaranSiswa">
  <div class="modal-card" style="max-width:540px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--border); padding-bottom:10px;">
      <h3 style="font-size:17px; font-weight:900; color:var(--text); margin:0; display:flex; align-items:center; gap:8px;">
        <i class="bi bi-exclamation-triangle-fill" style="color:#DC2626;"></i> Catat Pelanggaran Kedisiplinan / Poin
      </h3>
      <button type="button" class="btn btn-sm btn-outline" onclick="closeModalPelanggaranSiswa()"><i class="bi bi-x-lg"></i></button>
    </div>

    <div style="background:var(--bg-3); border-radius:var(--r-sm); padding:10px 14px; margin-bottom:14px; font-size:12.5px;">
      Siswa: <strong style="color:var(--gold);">{{ $siswa->nama }}</strong> · Poin Bersih Saat Ini: <strong style="color:var(--text);">{{ $kasus->poin_bersih }} Poin</strong>
    </div>

    <form action="{{ route('admin.disiplin.pelanggaran.store', $kasus->id) }}" method="POST">
      @csrf
      <div style="margin-bottom:14px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Pilih dari Katalog Master Pelanggaran</label>
        <select id="selectKatalogPelanggaran" name="katalog_pelanggaran_id" class="input-field" style="width:100%;" onchange="autoFillPelanggaran(this)">
          <option value="">-- Tindakan Kustom / Manual --</option>
          @foreach($katalogPelanggarans as $kp)
            <option value="{{ $kp->id }}" data-nama="{{ $kp->nama_pelanggaran }}" data-poin="{{ $kp->poin_pelanggaran }}" data-desc="{{ $kp->deskripsi }}">
              {{ $kp->nama_pelanggaran }} (+{{ $kp->poin_pelanggaran }} Poin) [{{ strtoupper($kp->kategori) }}]
            </option>
          @endforeach
        </select>
      </div>

      <div style="margin-bottom:14px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Nama Pelanggaran / Tindakan <span style="color:var(--red);">*</span></label>
        <input type="text" id="inputPelanggaranNama" name="nama_pelanggaran" class="input-field" placeholder="Misal: Merokok / Atribut Tidak Lengkap / Membawa HP" required style="width:100%;" />
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:14px;">
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Bobot Poin Ditambah <span style="color:var(--red);">*</span></label>
          <input type="number" id="inputPelanggaranPoin" name="poin_ditambah" class="input-field" value="10" min="1" max="200" required style="width:100%; font-family:var(--font-mono); font-weight:800;" />
        </div>
        <div>
          <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Tanggal Pelanggaran <span style="color:var(--red);">*</span></label>
          <input type="date" name="tanggal" class="input-field" value="{{ date('Y-m-d') }}" required style="width:100%; font-family:var(--font-mono);" />
        </div>
      </div>

      <div style="margin-bottom:18px;">
        <label class="form-label" style="font-weight:700; font-size:12px; display:block; margin-bottom:4px;">Uraian / Kronologi Singkat (Opsional)</label>
        <textarea id="inputPelanggaranCatatan" name="catatan" class="input-field" rows="2" placeholder="Tuliskan keterangan detail kejadian atau barang bukti..." style="width:100%;"></textarea>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModalPelanggaranSiswa()">Batal</button>
        <button type="submit" class="btn btn-gold" style="background:#DC2626; border-color:#DC2626;">
          <i class="bi bi-exclamation-triangle-fill"></i> Simpan Pelanggaran &amp; Tambah Poin
        </button>
      </div>
    </form>
  </div>
</div>

@include('partials.crop_modal')

<script>
  function openModalLog() { document.getElementById('modalLog').classList.add('active'); }
  function closeModalLog() { document.getElementById('modalLog').classList.remove('active'); }

  function openModalUpload() { document.getElementById('modalUpload').classList.add('active'); }
  function closeModalUpload() { document.getElementById('modalUpload').classList.remove('active'); }

  function openModalTindakLanjutDossier() { document.getElementById('modalEskalasiDossier').classList.add('active'); }
  function closeModalTindakLanjutDossier() { document.getElementById('modalEskalasiDossier').classList.remove('active'); }

  function openModalRewardSiswa() { document.getElementById('modalRewardSiswa').classList.add('active'); }
  function closeModalRewardSiswa() { document.getElementById('modalRewardSiswa').classList.remove('active'); }

  function openModalPelanggaranSiswa() { document.getElementById('modalPelanggaranSiswa').classList.add('active'); }
  function closeModalPelanggaranSiswa() { document.getElementById('modalPelanggaranSiswa').classList.remove('active'); }

  function autoFillReward(select) {
    const selected = select.options[select.selectedIndex];
    if (selected && selected.value) {
      document.getElementById('inputRewardNama').value = selected.getAttribute('data-nama') || '';
      document.getElementById('inputRewardPoin').value = selected.getAttribute('data-poin') || 5;
      document.getElementById('inputRewardCatatan').value = selected.getAttribute('data-desc') || '';
    }
  }

  function autoFillPelanggaran(select) {
    const selected = select.options[select.selectedIndex];
    if (selected && selected.value) {
      document.getElementById('inputPelanggaranNama').value = selected.getAttribute('data-nama') || '';
      document.getElementById('inputPelanggaranPoin').value = selected.getAttribute('data-poin') || 10;
      document.getElementById('inputPelanggaranCatatan').value = selected.getAttribute('data-desc') || '';
    }
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeModalLog();
      closeModalUpload();
      closeModalTindakLanjutDossier();
      closeModalRewardSiswa();
      closeModalPelanggaranSiswa();
    }
  });
</script>

</body>
</html>
