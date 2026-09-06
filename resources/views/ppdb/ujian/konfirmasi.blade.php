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
                        {{ $pendaftar->jadwal_tes_tanggal ? \Carbon\Carbon::parse($pendaftar->jadwal_tes_tanggal)->translatedFormat('d M Y') : 'Sesi Terbuka' }} 
                        ({{ $pendaftar->jadwal_tes_ruang ?? 'Lab Komputer' }})
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

        <!-- Tombol Aksi Mulai -->
        <div style="text-align: center; display: flex; flex-direction: column; align-items: center; gap: 14px;">
            <a href="{{ route('ppdb.ujian.kerjakan', $pendaftar->no_pendaftaran) }}" class="btn-shine" style="padding: 16px 40px; font-size: 1.1rem; font-weight: 800; border-radius: 12px; display: inline-flex; align-items: center; gap: 12px; text-decoration: none; box-shadow: 0 8px 20px rgba(37,99,235,0.25);">
                <span>MULAI MENGERJAKAN UJIAN</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>

            <a href="{{ route('ppdb.status', ['keyword' => $pendaftar->no_pendaftaran]) }}" style="color: #64748b; font-size: 0.88rem; font-weight: 700; text-decoration: none;">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Status Pendaftaran
            </a>
        </div>

    </div>

</div>
@endsection
