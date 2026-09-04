@extends('web.layouts.app')

@section('title', 'Penerimaan Peserta Didik Baru (PPDB) 2026/2027 — SMKN 1 Air Naningan')
@section('meta_description', 'Portal resmi PPDB Online SMKN 1 Air Naningan Tahun Pelajaran 2026/2027. Pendaftaran mandiri mudah, transparan, dan bebas biaya.')

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 60px;">

    <!-- PPDB Hero -->
    <div class="bento-card" style="background: var(--brand-navy); color: #ffffff; border-color: transparent; margin-bottom: 36px; padding: clamp(28px, 4vw, 44px);">
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 28px;">
            <div style="max-width: 700px;">
                <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; background: rgba(37, 99, 235, 0.25); border: 1px solid rgba(147, 197, 253, 0.3); border-radius: 30px; font-family: var(--font-tech); font-size: 0.78rem; font-weight: 700; color: #93c5fd; margin-bottom: 16px;">
                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #34d399;"></span>
                    PORTAL PENDAFTARAN ONLINE TP {{ $tahunAjaran }}
                </div>
                <h1 style="font-size: clamp(2rem, 3.5vw, 2.8rem); font-weight: 800; color: #ffffff; letter-spacing: -0.03em; line-height: 1.15; margin-bottom: 14px;">
                    Pintu Masuk Calon Ahli Madya &amp; Teknisi Industri
                </h1>
                <p style="font-size: 1.05rem; color: #cbd5e1; line-height: 1.65; margin-bottom: 26px;">
                    SMKN 1 Air Naningan membuka penerimaan calon peserta didik baru untuk Program Keahlian Rekayasa Perangkat Lunak, Agro-Teknologi Pangan, dan Teknik Sepeda Motor tanpa dipungut biaya pendaftaran.
                </p>
                <div style="display: flex; flex-wrap: wrap; gap: 14px;">
                    <a href="{{ route('ppdb.formulir') }}" class="btn-industrial btn-industrial-primary" style="padding: 12px 26px; font-size: 0.92rem;">
                        <i class="fa-solid fa-file-signature"></i> Isi Formulir Pendaftaran Online
                    </a>
                    <a href="{{ route('ppdb.status') }}" class="btn-industrial" style="background: rgba(255,255,255,0.1); color: #ffffff; border: 1px solid rgba(255,255,255,0.2); font-size: 0.92rem;">
                        <i class="fa-solid fa-id-badge" style="color: #fbbf24;"></i> Cek Status &amp; Cetak Bukti
                    </a>
                </div>
            </div>

            <!-- Stats Pendaftar Widget -->
            <div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: var(--radius-md); padding: 28px 36px; min-width: 240px; text-align: center;">
                <div style="font-family: var(--font-tech); font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.05em;">Total Pendaftar Masuk</div>
                <div style="font-family: var(--font-tech); font-size: 3.4rem; font-weight: 800; color: #ffffff; line-height: 1;">{{ $totalPendaftar }}</div>
                <div style="font-size: 0.78rem; color: #34d399; margin-top: 10px; font-weight: 700;">
                    <i class="fa-solid fa-circle-check"></i> Server Aktif 24 Jam
                </div>
            </div>
        </div>
    </div>

    <!-- Bento Grid Info PPDB -->
    <div class="bento-grid">
        
        <!-- 3 Jalur Pendaftaran (Span 7) -->
        <div class="bento-card" style="grid-column: span 7;">
            <div style="margin-bottom: 22px; padding-bottom: 14px; border-bottom: 1px solid var(--border-main);">
                <span class="section-tag">Pilihan Jalur Masuk</span>
                <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-dark);">
                    3 Jalur Seleksi Penerimaan Siswa Baru
                </h3>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div style="background: var(--bg-surface-alt); border: 1px solid var(--border-main); border-radius: var(--radius-sm); padding: 18px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <h4 style="font-size: 1rem; font-weight: 800; color: var(--text-dark);">1. Jalur Reguler (Umum)</h4>
                        <span style="font-family: var(--font-tech); font-size: 0.7rem; color: var(--brand-blue); font-weight: 700; background: var(--brand-blue-subtle); padding: 3px 10px; border-radius: 20px;">Kuota Utama</span>
                    </div>
                    <p style="font-size: 0.88rem; color: var(--text-body); line-height: 1.55;">
                        Terbuka bagi seluruh lulusan SMP/MTs/Sederajat dengan seleksi berdasarkan nilai rapor semester 1-5 dan verifikasi kelengkapan berkas administrasi.
                    </p>
                </div>

                <div style="background: var(--bg-surface-alt); border: 1px solid var(--border-main); border-radius: var(--radius-sm); padding: 18px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <h4 style="font-size: 1rem; font-weight: 800; color: var(--text-dark);">2. Jalur Prestasi (Akademik &amp; Non-Akademik)</h4>
                        <span style="font-family: var(--font-tech); font-size: 0.7rem; color: var(--brand-amber); font-weight: 700; background: var(--brand-amber-subtle); padding: 3px 10px; border-radius: 20px;">Prioritas Khusus</span>
                    </div>
                    <p style="font-size: 0.88rem; color: var(--text-body); line-height: 1.55;">
                        Diperuntukkan bagi siswa peraih peringkat kelas atau sertifikat kejuaraan lomba sains, olahraga, seni, maupun tahfidz Al-Qur'an (minimal tingkat kecamatan).
                    </p>
                </div>

                <div style="background: var(--bg-surface-alt); border: 1px solid var(--border-main); border-radius: var(--radius-sm); padding: 18px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <h4 style="font-size: 1rem; font-weight: 800; color: var(--text-dark);">3. Jalur Afirmasi (Keluarga Prasejahtera)</h4>
                        <span style="font-family: var(--font-tech); font-size: 0.7rem; color: var(--brand-emerald); font-weight: 700; background: var(--brand-emerald-subtle); padding: 3px 10px; border-radius: 20px;">Bantuan Pendidikan</span>
                    </div>
                    <p style="font-size: 0.88rem; color: var(--text-body); line-height: 1.55;">
                        Khusus calon peserta didik pemegang Kartu Indonesia Pintar (KIP), Program Keluarga Harapan (PKH), atau Kartu Perlindungan Sosial (KPS).
                    </p>
                </div>
            </div>
        </div>

        <!-- Alur & Syarat Berkas (Span 5) -->
        <div class="bento-card" style="grid-column: span 5; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="margin-bottom: 22px; padding-bottom: 14px; border-bottom: 1px solid var(--border-main);">
                    <span class="section-tag">Berkas Pendaftaran</span>
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-dark);">
                        Dokumen Yang Disiapkan
                    </h3>
                </div>

                <ul style="color: var(--text-body); font-size: 0.9rem; line-height: 1.8; list-style: none; display: flex; flex-direction: column; gap: 12px;">
                    <li style="display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fa-solid fa-circle-check" style="color: var(--brand-emerald); margin-top: 5px; font-size: 0.95rem;"></i>
                        <span>Nomor Induk Siswa Nasional (NISN) aktif 10 digit.</span>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fa-solid fa-circle-check" style="color: var(--brand-emerald); margin-top: 5px; font-size: 0.95rem;"></i>
                        <span>Scan / Foto Asli Kartu Keluarga (KK).</span>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fa-solid fa-circle-check" style="color: var(--brand-emerald); margin-top: 5px; font-size: 0.95rem;"></i>
                        <span>Scan Surat Keterangan Lulus (SKL) / Ijazah SMP.</span>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fa-solid fa-circle-check" style="color: var(--brand-emerald); margin-top: 5px; font-size: 0.95rem;"></i>
                        <span>Pas foto formal 3x4 (latar merah atau biru).</span>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fa-solid fa-circle-check" style="color: var(--brand-emerald); margin-top: 5px; font-size: 0.95rem;"></i>
                        <span>Nomor WhatsApp Aktif Calon Siswa &amp; Orang Tua.</span>
                    </li>
                </ul>
            </div>

            <div style="margin-top: 24px; padding: 16px 18px; background: var(--brand-blue-subtle); border-radius: var(--radius-sm); border: 1px solid rgba(37,99,235,0.2);">
                <div style="font-family: var(--font-tech); font-size: 0.75rem; font-weight: 700; color: var(--brand-blue); margin-bottom: 4px; text-transform: uppercase;">
                    <i class="fa-solid fa-shield-halved"></i> 100% Bebas Biaya Pendaftaran
                </div>
                <div style="font-size: 0.82rem; color: var(--text-body);">
                    Seluruh tahapan pendaftaran online dan verifikasi berkas tidak dipungut biaya apapun (Gratis).
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

