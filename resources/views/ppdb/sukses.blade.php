@extends('web.layouts.app')

@section('title', 'Pendaftaran Berhasil — PPDB SMKN 1 Air Naningan')

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 60px; max-width: 820px;">

    <div class="bento-card" style="text-align: center; padding: clamp(32px, 5vw, 54px); border-top: 5px solid var(--brand-emerald);">
        
        <div style="width: 76px; height: 76px; border-radius: 50%; background: var(--brand-emerald-subtle); color: var(--brand-emerald); font-size: 2.2rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; border: 2px solid rgba(4,120,87,0.3);">
            <i class="fa-solid fa-check"></i>
        </div>

        <span class="badge-pulse" style="background: var(--brand-emerald-subtle); color: var(--brand-emerald); border-color: rgba(4,120,87,0.3); margin-bottom: 14px; display: inline-block;">
            REGISTRASI BERHASIL TERSIMPAN
        </span>

        <h1 style="font-size: clamp(1.8rem, 3vw, 2.3rem); font-weight: 800; color: var(--text-dark); letter-spacing: -0.03em; margin-bottom: 12px;">
            Selamat, Pendaftaran Anda Telah Diterima Sistem!
        </h1>

        <p style="color: var(--text-body); font-size: 1rem; max-width: 600px; margin: 0 auto 28px auto; line-height: 1.6;">
            Simpan baik-baik Nomor Registrasi berikut. Nomor ini digunakan untuk mencetak bukti pendaftaran dan memeriksa pengumuman seleksi.
        </p>

        <!-- Nomor Pendaftaran Box -->
        <div style="background: var(--bg-surface-alt); border: 2px dashed var(--brand-blue); border-radius: var(--radius-md); padding: 24px; max-width: 480px; margin: 0 auto 32px auto;">
            <div style="font-family: var(--font-tech); font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px;">
                Nomor Registrasi Resmi PPDB:
            </div>
            <div style="font-family: var(--font-tech); font-size: 2.3rem; font-weight: 800; color: var(--brand-blue); letter-spacing: 0.04em;">
                {{ $pendaftar->nomor_pendaftaran }}
            </div>
            <div style="font-size: 0.88rem; color: var(--text-dark); margin-top: 8px;">
                Calon Siswa: <strong>{{ $pendaftar->nama_lengkap }}</strong> (NISN: {{ $pendaftar->nisn }})
            </div>
        </div>

        <!-- Ringkasan Singkat -->
        <div style="background: var(--bg-surface-alt); border-radius: var(--radius-sm); border: 1px solid var(--border-main); padding: 20px 24px; max-width: 520px; margin: 0 auto 36px auto; text-align: left; font-size: 0.9rem; display: flex; flex-direction: column; gap: 10px;">
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--text-muted);">Pilihan Kejuruan 1:</span>
                <span style="color: var(--text-dark); font-weight: 800;">{{ $pendaftar->jurusanPilihan1->nama_jurusan ?? '-' }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--text-muted);">Jalur Masuk:</span>
                <span style="color: var(--brand-blue); font-weight: 800; text-transform: uppercase;">{{ $pendaftar->jalur_pendaftaran }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--text-muted);">Status Berkas:</span>
                <span style="color: var(--brand-amber); font-weight: 800;">Menunggu Verifikasi Panitia</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 14px;">
            <a href="{{ route('ppdb.cetak', $pendaftar->nomor_pendaftaran) }}" target="_blank" class="btn-industrial btn-industrial-primary" style="padding: 12px 28px; font-size: 0.95rem;">
                <i class="fa-solid fa-print"></i> Cetak Kartu Bukti Pendaftaran
            </a>
            <a href="{{ route('ppdb.status') }}" class="btn-industrial btn-industrial-outline" style="padding: 12px 24px; font-size: 0.95rem;">
                <i class="fa-solid fa-magnifying-glass"></i> Cek Status Pendaftaran
            </a>
        </div>

    </div>

</div>
@endsection

