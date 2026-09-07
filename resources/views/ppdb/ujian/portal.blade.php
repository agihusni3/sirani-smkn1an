@extends('web.layouts.app')

@section('title', 'Ruang Ujian CBT Seleksi PPDB 2026 — SMKN 1 Air Naningan')
@section('meta_description', 'Portal akses ujian seleksi tertulis online CBT PPDB SMKN 1 Air Naningan. Masukkan nomor pendaftaran resmi untuk memulai tes.')

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 60px; max-width: 860px;">

    {{-- HEADER --}}
    <div style="text-align: center; margin-bottom: 32px;">
        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 5px 14px; background: rgba(37, 99, 235, 0.1); border: 1px solid rgba(37, 99, 235, 0.25); border-radius: 30px; font-family: var(--font-tech); font-size: 0.78rem; font-weight: 700; color: #2563eb; margin-bottom: 14px;">
            <i class="fa-solid fa-laptop-code"></i>
            CBT ONLINE PPDB {{ $setting->tahun_ajaran ?? date('Y') . '/' . (date('Y') + 1) }}
        </div>
        <h1 class="section-title-large" style="margin-bottom: 10px;">
            Portal Masuk Ruang Ujian CBT
        </h1>
        <p style="color: var(--text-body); font-size: 1rem; max-width: 620px; margin: 0 auto; line-height: 1.6;">
            Silakan masukkan <strong>Nomor Pendaftaran</strong> resmi Anda (contoh: <code>PPDB-{{ date('Y') }}-0001</code>) untuk mengakses ruang ujian seleksi online.
        </p>
    </div>

    {{-- ALERTS --}}
    @if(session('error'))
        <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 12px; padding: 16px 20px; color: #991b1b; font-size: 0.9rem; font-weight: 600; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 12px; line-height: 1.5;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.2rem; color: #ef4444; margin-top: 2px; flex-shrink: 0;"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if(session('warning'))
        <div style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 12px; padding: 16px 20px; color: #92400e; font-size: 0.9rem; font-weight: 600; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 12px; line-height: 1.5;">
            <i class="fa-solid fa-circle-exclamation" style="font-size: 1.2rem; color: #f59e0b; margin-top: 2px; flex-shrink: 0;"></i>
            <div>{{ session('warning') }}</div>
        </div>
    @endif

    @if(session('info'))
        <div style="background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 12px; padding: 16px 20px; color: #1e40af; font-size: 0.9rem; font-weight: 600; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 12px; line-height: 1.5;">
            <i class="fa-solid fa-circle-info" style="font-size: 1.2rem; color: #3b82f6; margin-top: 2px; flex-shrink: 0;"></i>
            <div>{{ session('info') }}</div>
        </div>
    @endif

    {{-- SEARCH FORM CARD --}}
    <div class="bento-card" style="padding: clamp(24px, 4vw, 36px); margin-bottom: 32px; border-top: 4px solid #0284c7; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
        <form action="{{ route('ppdb.ujian.portal') }}" method="POST">
            @csrf
            <label style="display: block; font-size: 0.92rem; font-weight: 800; color: var(--text-dark, #0f172a); margin-bottom: 10px;">
                <i class="fa-solid fa-id-card" style="color: #0284c7; margin-right: 6px;"></i>
                Nomor Pendaftaran Siswa:
            </label>
            
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 260px; position: relative;">
                    <input type="text" 
                           name="no_pendaftaran" 
                           value="{{ old('no_pendaftaran', request('no_pendaftaran', request('keyword'))) }}" 
                           required 
                           autofocus
                           placeholder="Contoh: PPDB-{{ date('Y') }}-0001 atau NISN..." 
                           style="width: 100%; height: 50px; padding: 0 18px; font-size: 1.05rem; font-family: var(--font-mono, monospace); font-weight: 700; letter-spacing: 0.5px; border: 2px solid #cbd5e1; border-radius: 10px; text-transform: uppercase; box-sizing: border-box;">
                </div>
                <button type="submit" 
                        class="btn-industrial" 
                        style="background: #0284c7; color: #ffffff; border: 1px solid #0369a1; height: 50px; padding: 0 32px; font-size: 1rem; font-weight: 800; border-radius: 10px; cursor: pointer; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);">
                    <span>Masuk Ujian CBT</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>

            <div style="font-size: 0.8rem; color: #64748b; margin-top: 10px; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-circle-question" style="color: #94a3b8;"></i>
                <span>Tips: Anda juga dapat mengetikkan 4 digit terakhir nomor registrasi (misal: <code>0001</code>) atau nomor NISN Anda.</span>
            </div>
        </form>
    </div>

    {{-- INFORMASI KETENTUAN CBT --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 30px;">
        <div class="bento-card" style="padding: 20px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <div style="font-size: 0.95rem; font-weight: 800; color: var(--text-dark, #0f172a);">Struktur Soal</div>
            </div>
            <div style="font-size: 0.84rem; color: #64748b; line-height: 1.5;">
                {{ $setting->jumlah_soal_pg ?? 30 }} Butir Pilihan Ganda (PG) &amp; {{ $setting->jumlah_soal_esai ?? 5 }} Butir Esai Potensi Akademik dan Minat Vokasi.
            </div>
        </div>

        <div class="bento-card" style="padding: 20px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div style="font-size: 0.95rem; font-weight: 800; color: var(--text-dark, #0f172a);">Durasi Pengerjaan</div>
            </div>
            <div style="font-size: 0.84rem; color: #64748b; line-height: 1.5;">
                Waktu pengerjaan {{ $setting->durasi_menit ?? 60 }} menit dihitung otomatis oleh timer sistem sejak Anda menekan tombol mulai.
            </div>
        </div>

        <div class="bento-card" style="padding: 20px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div style="font-size: 0.95rem; font-weight: 800; color: var(--text-dark, #0f172a);">Syarat Peserta</div>
            </div>
            <div style="font-size: 0.84rem; color: #64748b; line-height: 1.5;">
                Hanya calon siswa yang berkas pendaftarannya telah <strong>lolos verifikasi</strong> oleh Panitia PPDB yang dapat memulai ujian.
            </div>
        </div>
    </div>

    {{-- QUICK NAVIGATION FOOTER --}}
    <div style="text-align: center; font-size: 0.88rem; color: #64748b; padding-top: 10px;">
        Lupa nomor pendaftaran? 
        <a href="{{ route('ppdb.status') }}" style="color: #2563eb; font-weight: 700; text-decoration: none; margin-left: 4px;">
            Cek Status Pendaftaran &rarr;
        </a>
        <span style="margin: 0 10px;">&bull;</span>
        Belum mendaftar? 
        <a href="{{ route('ppdb.formulir') }}" style="color: #059669; font-weight: 700; text-decoration: none; margin-left: 4px;">
            Isi Formulir Online &rarr;
        </a>
    </div>

</div>
@endsection
