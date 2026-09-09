@extends('web.layouts.app')

@section('title', 'Konfirmasi Tes Seleksi CBT — PPDB SMKN 1 Air Naningan')

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 70px; max-width: 860px;">
    
    <div class="bento-card" style="padding: clamp(28px, 4vw, 48px); border-top: 5px solid var(--brand-blue, #2563eb);">
        
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
            <div style="width: 58px; height: 58px; border-radius: 14px; background: rgba(37,99,235,0.1); color: var(--brand-blue, #2563eb); font-size: 1.7rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fa-solid fa-laptop-code"></i>
            </div>
            <div>
                <span style="display: inline-block; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--brand-blue, #2563eb); margin-bottom: 4px;">
                    Portal Ujian Seleksi Masuk PPDB 2026
                </span>
                <h1 style="font-size: clamp(1.4rem, 2.5vw, 1.85rem); font-weight: 800; color: #000000; margin: 0; line-height: 1.2;">
                    {{ $setting->judul_ujian }}
                </h1>
            </div>
        </div>

        @if(session('error'))
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem;">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Card Identitas Peserta -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px 24px; margin-bottom: 28px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; font-size: 0.9rem;">
                <div>
                    <div style="color: #64748b; font-size: 0.78rem; font-weight: 700; text-transform: uppercase;">Nama Lengkap Peserta:</div>
                    <div style="color: #000000; font-weight: 800; font-size: 1.05rem; margin-top: 2px;">{{ $pendaftar->nama_lengkap }}</div>
                </div>
                <div>
                    <div style="color: #64748b; font-size: 0.78rem; font-weight: 700; text-transform: uppercase;">No. Registrasi / NISN:</div>
                    <div style="color: #000000; font-weight: 800; font-size: 1.05rem; margin-top: 2px;">
                        <span style="color: var(--brand-blue, #2563eb);">{{ $pendaftar->no_pendaftaran }}</span> / {{ $pendaftar->nisn }}
                    </div>
                </div>
                <div>
                    <div style="color: #64748b; font-size: 0.78rem; font-weight: 700; text-transform: uppercase;">Pilihan Jurusan:</div>
                    <div style="color: #000000; font-weight: 700; margin-top: 2px;">
                        1. {{ $pendaftar->jurusanPilihan1->nama_jurusan ?? '-' }}
                    </div>
                </div>
                <div>
                    <div style="color: #64748b; font-size: 0.78rem; font-weight: 700; text-transform: uppercase;">Jadwal / Ruang Sesi:</div>
                    <div style="color: #000000; font-weight: 700; margin-top: 2px;">
                        {{ \Carbon\Carbon::parse($pendaftar->jadwal_tanggal_resmi)->translatedFormat('d M Y') }} 
                        ({{ $pendaftar->jadwal_ruang_resmi }})
                    </div>
                </div>
                <div>
                    <div style="color: #64748b; font-size: 0.78rem; font-weight: 700; text-transform: uppercase;">Status Presensi Fisik:</div>
                    <div style="margin-top: 3px;">
                        @if($absensi)
                            <span style="display: inline-flex; align-items: center; gap: 5px; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 800;">
                                <i class="fa-solid fa-circle-check"></i> HADIR ({{ $absensi->waktu_hadir->format('H:i') }} WIB)
                            </span>
                        @else
                            <span style="display: inline-flex; align-items: center; gap: 5px; background: #fffbeb; color: #b45309; border: 1px solid #fde68a; padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 800;">
                                <i class="fa-solid fa-clock"></i> BELUM SCAN KARTU
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Spesifikasi Format Ujian -->
        <h2 style="font-size: 1.1rem; font-weight: 800; color: #000000; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-clipboard-list text-primary"></i> Format & Struktur Soal
        </h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 28px;">
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="font-size: 2rem; font-weight: 900; color: var(--brand-blue, #2563eb); line-height: 1;">
                    {{ $setting->jumlah_soal_pg ?: 30 }}
                </div>
                <div style="font-size: 0.9rem; font-weight: 800; color: #000000; margin-top: 6px;">Soal Pilihan Ganda</div>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">Nomor 1 s/d 30 (A, B, C, D, E)</div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="font-size: 2rem; font-weight: 900; color: #059669; line-height: 1;">
                    {{ $setting->jumlah_soal_esai ?: 5 }}
                </div>
                <div style="font-size: 0.9rem; font-weight: 800; color: #000000; margin-top: 6px;">Soal Esai / Uraian</div>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">Nomor 31 s/d 35 (Ketik di form)</div>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="font-size: 2rem; font-weight: 900; color: #d97706; line-height: 1;">
                    {{ $setting->durasi_menit ?: 60 }} <span style="font-size: 1.1rem; font-weight: 700;">Menit</span>
                </div>
                <div style="font-size: 0.9rem; font-weight: 800; color: #000000; margin-top: 6px;">Waktu Pengerjaan</div>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">Timer berjalan otomatis</div>
            </div>
        </div>

        <!-- Petunjuk Pengerjaan Split-Screen -->
        <h2 style="font-size: 1.1rem; font-weight: 800; color: #000000; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-circle-info text-primary"></i> Petunjuk Pelaksanaan Ujian Split-Screen
        </h2>

        <div style="background: #f1f5f9; border-radius: 12px; padding: 18px 22px; margin-bottom: 32px; font-size: 0.88rem; color: #334155; line-height: 1.6;">
            <ol style="margin: 0; padding-left: 20px; display: flex; flex-direction: column; gap: 8px;">
                <li>Layar ujian menggunakan metode <strong>Split-Screen</strong>: Naskah Soal PDF berada di sebelah <strong>Kiri</strong>, dan Lembar Jawab Digital berada di sebelah <strong>Kanan</strong>.</li>
                <li>Untuk <strong>Soal No. 1 s/d 30 (Pilihan Ganda)</strong>, klik salah satu opsi <strong>A, B, C, D, atau E</strong>. Jawaban otomatis tersimpan saat Anda memilih opsi.</li>
                <li>Untuk <strong>Soal No. 31 s/d 35 (Esai)</strong>, ketikkan penjelasan jawaban Anda pada kotak teks yang tersedia untuk masing-masing nomor. Sistem melakukan <em>auto-save</em> secara berkala.</li>
                <li>Waktu ujian akan terus berjalan mundur. Pastikan Anda menyelesaikan dan menekan tombol <strong>"Kumpulkan Ujian"</strong> sebelum waktu berakhir.</li>
                <li>Dilarang membuka tab lain, melakukan kecurangan, atau menyebarluaskan naskah soal seleksi.</li>
            </ol>
        </div>

        <!-- Tombol Aksi Mulai / Status Presensi Gatekeeper -->
        <div style="text-align: center; display: flex; flex-direction: column; align-items: center; gap: 14px;">
            @if($absensi)
                <a href="{{ route('ppdb.ujian.kerjakan', $pendaftar->no_pendaftaran) }}" class="btn-shine" style="padding: 16px 40px; font-size: 1.1rem; font-weight: 800; border-radius: 12px; display: inline-flex; align-items: center; gap: 12px; text-decoration: none; box-shadow: 0 8px 20px rgba(37,99,235,0.25);">
                    <span>MULAI MENGERJAKAN UJIAN</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            @else
                <div style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 12px; padding: 18px 20px; max-width: 600px; text-align: left; margin-bottom: 8px;">
                    <div style="display: flex; gap: 12px; align-items: flex-start;">
                        <i class="fa-solid fa-id-card-clip" style="font-size: 1.6rem; color: #d97706; margin-top: 2px;"></i>
                        <div>
                            <div style="font-weight: 800; font-size: 1rem; color: #92400e; margin-bottom: 4px;">
                                Akses Soal Ujian Terkunci (Belum Presensi Fisik)
                            </div>
                            <div style="font-size: 0.88rem; color: #78350f; line-height: 1.5;">
                                Untuk membuka soal ujian seleksi CBT ini, Anda wajib melakukan presensi fisik terlebih dahulu dengan menunjukkan <strong>Kartu Peserta (2D Barcode / QR)</strong> kepada Pengawas Ujian di ruang tes untuk dipindai.
                            </div>
                            <div style="margin-top: 12px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                <a href="{{ route('ppdb.cetak', ['nomor' => $pendaftar->no_pendaftaran]) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; background: #d97706; color: #fff; padding: 7px 14px; border-radius: 8px; font-weight: 700; font-size: 0.82rem; text-decoration: none;">
                                    <i class="fa-solid fa-qrcode"></i> Buka Kartu Ujian (2D Barcode)
                                </a>
                                <button type="button" onclick="window.location.reload()" style="display: inline-flex; align-items: center; gap: 6px; background: #ffffff; color: #92400e; border: 1px solid #fde68a; padding: 7px 14px; border-radius: 8px; font-weight: 700; font-size: 0.82rem; cursor: pointer;">
                                    <i class="fa-solid fa-rotate-right"></i> Refresh Halaman
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" disabled style="padding: 16px 36px; font-size: 1rem; font-weight: 800; border-radius: 12px; background: #e2e8f0; color: #94a3b8; border: 1px solid #cbd5e1; cursor: not-allowed; display: inline-flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-lock"></i>
                    <span>MENUNGGU PRESENSI PENGAWAS RUANG</span>
                </button>
            @endif

            <a href="{{ route('ppdb.status', ['keyword' => $pendaftar->no_pendaftaran]) }}" style="color: #64748b; font-size: 0.88rem; font-weight: 700; text-decoration: none;">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Status Pendaftaran
            </a>
        </div>

    </div>

</div>
@endsection
