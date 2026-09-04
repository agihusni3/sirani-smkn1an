@extends('web.layouts.app')

@section('title', 'Cek Status Pendaftaran & Hasil Seleksi — PPDB 2026/2027')
@section('meta_description', 'Pantau status verifikasi berkas dan pengumuman hasil seleksi PPDB SMKN 1 Air Naningan secara mandiri.')

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 60px; max-width: 880px;">

    <div style="text-align: center; margin-bottom: 36px;">
        <span class="section-tag">Layanan Mandiri Pendaftar</span>
        <h1 class="section-title-large">Status Pendaftaran &amp; Hasil Seleksi</h1>
        <p style="color: var(--text-body); font-size: 1rem; margin-top: 8px;">
            Masukkan Nomor Registrasi (contoh: <code>PPDB-2026-0001</code>) atau 10 digit NISN Anda untuk melihat status verifikasi berkas dan mencetak kartu peserta.
        </p>
    </div>

    <!-- Search Form -->
    <div class="bento-card" style="margin-bottom: 30px; padding: 24px;">
        <form action="{{ route('ppdb.status') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 260px;">
                <input type="text" name="keyword" value="{{ request('keyword') }}" required placeholder="Ketik Nomor Registrasi atau NISN..." style="width: 100%; padding: 13px 18px; font-size: 0.95rem;">
            </div>
            <button type="submit" class="btn-industrial btn-industrial-primary" style="padding: 13px 28px; font-size: 0.95rem;">
                <i class="fa-solid fa-magnifying-glass"></i> Periksa Status
            </button>
        </form>
    </div>

    @if(request()->filled('keyword'))
        @if($pendaftar)
            <!-- Result Box -->
            <div class="bento-card" style="border-top: 4px solid var(--brand-blue); padding: clamp(24px, 4vw, 36px);">
                <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 24px; padding-bottom: 18px; border-bottom: 1px solid var(--border-main);">
                    <div>
                        <div style="font-family: var(--font-tech); font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Nomor Registrasi PPDB:</div>
                        <div style="font-family: var(--font-tech); font-size: 1.5rem; font-weight: 800; color: var(--brand-blue); margin-top: 2px;">
                            {{ $pendaftar->nomor_pendaftaran }}
                        </div>
                    </div>
                    <div>
                        @if($pendaftar->status_pendaftaran == 'diterima')
                            <span style="background: var(--brand-emerald-subtle); color: var(--brand-emerald); border: 1px solid rgba(4,120,87,0.3); padding: 8px 18px; border-radius: 20px; font-weight: 800; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-circle-check"></i> RESMI DITERIMA
                            </span>
                        @elseif($pendaftar->status_pendaftaran == 'berkas_valid')
                            <span style="background: var(--brand-blue-subtle); color: var(--brand-blue); border: 1px solid rgba(37,99,235,0.3); padding: 8px 18px; border-radius: 20px; font-weight: 800; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-file-circle-check"></i> BERKAS TERVERIFIKASI
                            </span>
                        @elseif($pendaftar->status_pendaftaran == 'ditolak')
                            <span style="background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 8px 18px; border-radius: 20px; font-weight: 800; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-circle-xmark"></i> TIDAK LOLOS SELEKSI
                            </span>
                        @else
                            <span style="background: var(--brand-amber-subtle); color: var(--brand-amber); border: 1px solid rgba(180,83,9,0.3); padding: 8px 18px; border-radius: 20px; font-weight: 800; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-hourglass-half"></i> MENUNGGU VERIFIKASI BERKAS
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Info Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 24px;">
                    <div style="background: var(--bg-surface-alt); padding: 14px 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-main);">
                        <div style="font-family: var(--font-tech); font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">Nama Calon Siswa</div>
                        <div style="font-size: 1rem; font-weight: 800; color: var(--text-dark); margin-top: 4px;">{{ $pendaftar->nama_lengkap }}</div>
                    </div>
                    <div style="background: var(--bg-surface-alt); padding: 14px 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-main);">
                        <div style="font-family: var(--font-tech); font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">NISN</div>
                        <div style="font-family: var(--font-tech); font-size: 1rem; font-weight: 800; color: var(--text-dark); margin-top: 4px;">{{ $pendaftar->nisn }}</div>
                    </div>
                    <div style="background: var(--bg-surface-alt); padding: 14px 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-main);">
                        <div style="font-family: var(--font-tech); font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">Asal SMP/MTs</div>
                        <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-dark); margin-top: 4px;">{{ $pendaftar->asal_sekolah }}</div>
                    </div>
                    <div style="background: var(--bg-surface-alt); padding: 14px 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-main);">
                        <div style="font-family: var(--font-tech); font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">Pilihan Kejuruan 1</div>
                        <div style="font-size: 0.95rem; font-weight: 800; color: var(--brand-blue); margin-top: 4px;">{{ $pendaftar->jurusanPilihan1->nama_jurusan ?? '-' }}</div>
                    </div>
                </div>

                @if($pendaftar->status_pendaftaran == 'diterima' && $pendaftar->jurusanDiterima)
                    <div style="background: var(--brand-emerald-subtle); border: 1px solid rgba(4,120,87,0.3); border-radius: var(--radius-sm); padding: 18px 20px; margin-bottom: 24px;">
                        <div style="font-size: 0.92rem; font-weight: 800; color: var(--brand-emerald); margin-bottom: 4px;">
                            🎉 Selamat! Anda Dinyatakan Diterima di Konsentrasi Keahlian:
                        </div>
                        <div style="font-size: 1.2rem; font-weight: 800; color: var(--text-dark);">
                            {{ $pendaftar->jurusanDiterima->nama_jurusan }} ({{ $pendaftar->jurusanDiterima->kode }})
                        </div>
                    </div>
                @endif

                @if($pendaftar->catatan)
                    <div style="background: var(--bg-surface-alt); border-radius: var(--radius-sm); border: 1px solid var(--border-main); padding: 14px 16px; margin-bottom: 24px; font-size: 0.88rem; color: var(--text-body);">
                        <strong>Catatan Panitia PPDB:</strong> {{ $pendaftar->catatan }}
                    </div>
                @endif

                <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 16px; border-top: 1px solid var(--border-main);">
                    <a href="{{ route('ppdb.cetak', $pendaftar->nomor_pendaftaran) }}" target="_blank" class="btn-industrial btn-industrial-primary" style="padding: 11px 24px; font-size: 0.9rem;">
                        <i class="fa-solid fa-print"></i> Cetak Kartu Tanda Peserta
                    </a>
                </div>
            </div>
        @else
            <div class="bento-card" style="text-align: center; padding: 48px 20px;">
                <div style="font-size: 2.4rem; color: #ef4444; margin-bottom: 12px;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-dark);">Data Pendaftaran Tidak Ditemukan</h3>
                <p style="color: var(--text-muted); font-size: 0.88rem; margin-top: 6px;">
                    Pastikan Anda mengetikkan Nomor Registrasi atau NISN yang benar saat mendaftar online.
                </p>
            </div>
        @endif
    @endif

</div>
@endsection

