@extends('web.layouts.app')

@section('title', 'Ujian Berhasil Dikirim — PPDB SMKN 1 Air Naningan')

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 70px; max-width: 820px;">

    <div class="bento-card" style="text-align: center; padding: clamp(32px, 5vw, 54px); border-top: 5px solid var(--brand-emerald, #059669);">
        
        <div style="width: 76px; height: 76px; border-radius: 50%; background: rgba(5,150,105,0.1); color: var(--brand-emerald, #059669); font-size: 2.2rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; border: 2px solid rgba(5,150,105,0.3);">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <span class="badge-pulse" style="background: rgba(5,150,105,0.1); color: var(--brand-emerald, #059669); border: 1px solid rgba(5,150,105,0.3); margin-bottom: 14px; display: inline-block; padding: 4px 14px; border-radius: 20px; font-weight: 800; font-size: 0.78rem;">
            LEMBAR JAWABAN RESMI DITERIMA
        </span>

        <h1 style="font-size: clamp(1.6rem, 3vw, 2.1rem); font-weight: 800; color: #000000; letter-spacing: -0.02em; margin-bottom: 10px;">
            Alhamdulillah, Ujian Seleksi Berhasil Dikirim!
        </h1>

        <p style="color: #64748b; font-size: 0.95rem; max-width: 600px; margin: 0 auto 28px auto; line-height: 1.6;">
            Seluruh lembar jawaban Pilihan Ganda dan uraian Esai Anda telah tersimpan secara permanen di basis data panitia seleksi PPDB 2026.
        </p>

        @if(session('success'))
            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 12px 18px; border-radius: 10px; margin-bottom: 24px; font-size: 0.9rem; text-align: left;">
                <i class="fa-solid fa-check-double me-2"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Card Identitas & Waktu Selesai -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 22px 26px; max-width: 620px; margin: 0 auto 28px auto; text-align: left;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; font-size: 0.88rem;">
                <div>
                    <span style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Peserta Ujian:</span>
                    <strong style="display: block; color: #000000; font-size: 1rem; margin-top: 2px;">{{ $pendaftar->nama_lengkap }}</strong>
                    <span style="color: #64748b; font-size: 0.8rem;">NISN: {{ $pendaftar->nisn }}</span>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Nomor Registrasi:</span>
                    <strong style="display: block; color: var(--brand-blue, #2563eb); font-size: 1rem; margin-top: 2px; font-family: monospace;">{{ $pendaftar->no_pendaftaran }}</strong>
                    <span style="color: #64748b; font-size: 0.8rem;">Jurusan: {{ $pendaftar->jurusanPilihan1->nama_jurusan ?? '-' }}</span>
                </div>
                <div style="grid-column: 1 / -1; border-top: 1px dashed #cbd5e1; padding-top: 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                    <span style="color: #64748b;">Waktu Selesai Diterima Server:</span>
                    <strong style="color: #000000; font-family: monospace;">
                        {{ $peserta->waktu_selesai ? \Carbon\Carbon::parse($peserta->waktu_selesai)->translatedFormat('d F Y, H:i:s') . ' WIB' : now()->translatedFormat('d F Y, H:i:s') . ' WIB' }}
                    </strong>
                </div>
            </div>
        </div>

        <!-- Rekapitulasi Hasil Sementara (Transparansi Akuntabel) -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 22px; max-width: 620px; margin: 0 auto 34px auto; text-align: left;">
            <h3 style="font-size: 0.95rem; font-weight: 800; color: #000000; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-chart-simple text-primary"></i> Ringkasan Hasil Pengerjaan
            </h3>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 16px; text-align: center;">
                <div style="background: #ecfdf5; border-radius: 10px; padding: 12px; border: 1px solid #d1fae5;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #065f46; text-transform: uppercase;">PG Benar</span>
                    <div style="font-size: 1.5rem; font-weight: 900; color: #059669; font-family: monospace;">{{ $peserta->jumlah_pg_benar ?? 0 }}</div>
                </div>
                <div style="background: #fef2f2; border-radius: 10px; padding: 12px; border: 1px solid #fee2e2;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #991b1b; text-transform: uppercase;">PG Salah</span>
                    <div style="font-size: 1.5rem; font-weight: 900; color: #dc2626; font-family: monospace;">{{ $peserta->jumlah_pg_salah ?? 0 }}</div>
                </div>
                <div style="background: #f8fafc; border-radius: 10px; padding: 12px; border: 1px solid #e2e8f0;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">PG Kosong</span>
                    <div style="font-size: 1.5rem; font-weight: 900; color: #64748b; font-family: monospace;">{{ $peserta->jumlah_pg_kosong ?? 0 }}</div>
                </div>
            </div>

            <div style="background: #f1f5f9; border-radius: 10px; padding: 14px 16px; font-size: 0.85rem; color: #334155; line-height: 1.5;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span>Skor Pilihan Ganda (Bobot {{ $peserta->setting->bobot_pg ?? 70 }}%):</span>
                    <strong style="color: var(--brand-blue, #2563eb); font-family: monospace;">{{ number_format($peserta->nilai_pg ?? 0, 2) }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Koreksi Esai (Bobot {{ $peserta->setting->bobot_esai ?? 30 }}%):</span>
                    @if($peserta->status_pengerjaan === 'selesai_dinilai')
                        <strong style="color: #059669; font-family: monospace;">{{ number_format($peserta->nilai_esai ?? 0, 2) }} (Sudah Dinilai)</strong>
                    @else
                        <span style="color: #d97706; font-weight: 700;"><i class="fa-solid fa-clock me-1"></i> Sedang Proses Koreksi Penguji</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('ppdb.status', ['keyword' => $pendaftar->no_pendaftaran]) }}" class="btn-shine" style="padding: 12px 28px; font-weight: 800; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <span>Cek Status & Jadwal Wawancara</span>
            </a>

            <a href="{{ route('ppdb.cetak', $pendaftar->no_pendaftaran) }}" target="_blank" style="padding: 12px 22px; background: #ffffff; border: 1.5px solid #cbd5e1; color: #334155; font-weight: 700; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Kartu Ujian</span>
            </a>
        </div>

    </div>

</div>
@endsection
