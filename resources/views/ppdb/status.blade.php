@extends('web.layouts.app')

@section('title', 'Cek Status Pendaftaran & Hasil Seleksi — PPDB 2026/2027')
@section('meta_description', 'Pantau status verifikasi berkas, jadwal tes, ruang CBT, dan pengumuman hasil kelulusan PPDB SMKN 1 Air Naningan secara mandiri.')

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 60px; max-width: 880px;">

    <div style="text-align: center; margin-bottom: 36px;">
        <span class="section-tag">Layanan Mandiri Calon Siswa</span>
        <h1 class="section-title-large">Status Pendaftaran &amp; Hasil Seleksi</h1>
        <p style="color: var(--text-body); font-size: 1rem; margin-top: 8px;">
            Masukkan Nomor Registrasi (contoh: <code>PPDB-2026-0001</code>) atau NISN Anda untuk melihat status seleksi, jadwal ujian, dan pengumuman kelulusan.
        </p>
    </div>

    <!-- Search Form -->
    <div class="bento-card" style="margin-bottom: 30px; padding: 24px;">
        <form action="{{ route('ppdb.status') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 260px;">
                <input type="text" name="keyword" value="{{ request('keyword') }}" required placeholder="Ketik Nomor Registrasi atau NISN..." style="width: 100%; padding: 13px 18px; font-size: 0.95rem; border: 1.5px solid #cbd5e1; border-radius: 8px;">
            </div>
            <button type="submit" class="btn-industrial btn-industrial-primary" style="padding: 13px 28px; font-size: 0.95rem; font-weight: 700;">
                Periksa Status
            </button>
        </form>
    </div>

    @if(request()->filled('keyword'))
        @if($pendaftar)
            <!-- Result Box -->
            <div class="bento-card" style="border-top: 4px solid var(--brand-blue, #2563eb); padding: clamp(24px, 4vw, 36px);">
                
                {{-- HEADER STATUS --}}
                <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 24px; padding-bottom: 18px; border-bottom: 1px solid var(--border-main, #e2e8f0);">
                    <div>
                        <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">Nomor Registrasi PPDB:</div>
                        <div style="font-family: monospace; font-size: 1.5rem; font-weight: 800; color: #2563eb; margin-top: 2px;">
                            {{ $pendaftar->no_pendaftaran ?? $pendaftar->nomor_pendaftaran }}
                        </div>
                    </div>
                    <div>
                        @if($pendaftar->status == 'diterima')
                            <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 8px 18px; border-radius: 20px; font-weight: 800; font-size: 0.88rem;">
                                RESMI DITERIMA
                            </span>
                        @elseif($pendaftar->status == 'cadangan')
                            <span style="background: #fffbeb; color: #d97706; border: 1px solid #fde68a; padding: 8px 18px; border-radius: 20px; font-weight: 800; font-size: 0.88rem;">
                                PESERTA CADANGAN
                            </span>
                        @elseif(in_array($pendaftar->status, ['terverifikasi', 'berkas_valid', 'siap_tes']))
                            <span style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 8px 18px; border-radius: 20px; font-weight: 800; font-size: 0.88rem;">
                                BERKAS TERVERIFIKASI
                            </span>
                        @elseif($pendaftar->status == 'ditolak')
                            <span style="background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 8px 18px; border-radius: 20px; font-weight: 800; font-size: 0.88rem;">
                                TIDAK LOLOS SELEKSI
                            </span>
                        @else
                            <span style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; padding: 8px 18px; border-radius: 20px; font-weight: 800; font-size: 0.88rem;">
                                MENUNGGU VERIFIKASI BERKAS
                            </span>
                        @endif
                    </div>
                </div>

                {{-- INFO IDENTITAS PENDAFTAR --}}
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 24px;">
                    <div style="background: #f8fafc; padding: 14px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <div style="font-size: 0.72rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Nama Calon Siswa</div>
                        <div style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ $pendaftar->nama_lengkap }}</div>
                    </div>
                    <div style="background: #f8fafc; padding: 14px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <div style="font-size: 0.72rem; color: #64748b; text-transform: uppercase; font-weight: 700;">NISN</div>
                        <div style="font-family: monospace; font-size: 1rem; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ $pendaftar->nisn }}</div>
                    </div>
                    <div style="background: #f8fafc; padding: 14px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <div style="font-size: 0.72rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Asal Sekolah</div>
                        <div style="font-size: 0.95rem; font-weight: 700; color: #0f172a; margin-top: 4px;">{{ $pendaftar->asal_sekolah }}</div>
                    </div>
                    <div style="background: #f8fafc; padding: 14px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <div style="font-size: 0.72rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Pilihan Jurusan</div>
                        <div style="font-size: 0.95rem; font-weight: 800; color: #2563eb; margin-top: 4px;">
                            1. {{ $pendaftar->jurusanPilihan1->nama_jurusan ?? '-' }}
                            @if($pendaftar->jurusanPilihan2)
                                <div style="font-size: 0.8rem; color: #64748b; font-weight: 600;">2. {{ $pendaftar->jurusanPilihan2->nama_jurusan }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- KONDISI 1: JIKA DITERIMA (PENGUMUMAN KELULUSAN & DAFTAR ULANG) --}}
                @if($pendaftar->status == 'diterima')
                    <div style="background: #ecfdf5; border: 1.5px solid #a7f3d0; border-radius: 12px; padding: 22px 24px; margin-bottom: 24px;">
                        <div style="font-size: 0.95rem; font-weight: 800; color: #065f46; margin-bottom: 4px;">
                            Selamat! Anda Dinyatakan Lulus &amp; Diterima di:
                        </div>
                        <div style="font-size: 1.35rem; font-weight: 900; color: #047857; margin-bottom: 12px;">
                            {{ $pendaftar->jurusanDiterima->nama_jurusan ?? ($pendaftar->jurusanPilihan1->nama_jurusan ?? '-') }}
                        </div>
                        <div style="display: flex; gap: 16px; flex-wrap: wrap; font-size: 0.85rem; color: #065f46; margin-bottom: 16px; padding-bottom: 14px; border-bottom: 1px solid #a7f3d0;">
                            <div><strong>Peringkat:</strong> {{ $pendaftar->peringkat_jurusan ? '#' . $pendaftar->peringkat_jurusan : 'Lolos Kuota' }}</div>
                            <div><strong>Nilai Akhir Terbobot:</strong> {{ number_format($pendaftar->nilai_akhir ?? 0, 2) }}</div>
                            <div><strong>Jalur:</strong> {{ ucfirst($pendaftar->jalur_pendaftaran) }}</div>
                        </div>

                        <div style="font-size: 0.85rem; color: #065f46; line-height: 1.6;">
                            <strong>Petunjuk Daftar Ulang (Re-Registrasi):</strong>
                            <ol style="margin: 6px 0 0 18px; padding: 0;">
                                <li>Cetak Kartu Tanda Bukti Diterima PPDB melalui tombol di bawah.</li>
                                <li>Datang ke Sekretariat PPDB SMKN 1 Air Naningan pada jam kerja (08.00 - 14.00 WIB).</li>
                                <li>Bawa dokumen asli dan fotokopi: Ijazah/SKL, Kartu Keluarga, Akta Kelahiran, dan Surat Keterangan Lulus.</li>
                            </ol>
                        </div>
                    </div>
                @endif

                {{-- KONDISI 2: JIKA BERKAS VALID (JADWAL UJIAN & TOMBOL CBT) --}}
                @if(in_array($pendaftar->status, ['terverifikasi', 'berkas_valid', 'siap_tes']))
                    <div style="background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 12px; padding: 22px 24px; margin-bottom: 24px;">
                        <div style="font-size: 0.95rem; font-weight: 800; color: #1e40af; margin-bottom: 6px;">
                            Jadwal Tes Seleksi CBT &amp; Wawancara Kejuruan
                        </div>
                        <div style="font-size: 0.85rem; color: #3b82f6; margin-bottom: 16px;">
                            Berkas pendaftaran Anda telah dinyatakan valid. Silakan ikuti tes sesuai jadwal berikut:
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px 16px; margin-bottom: 18px;">
                            <div>
                                <div style="font-size: 0.72rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Tanggal Ujian (Juknis)</div>
                                <div style="font-weight: 800; font-size: 0.92rem; color: #0f172a; margin-top: 2px;">
                                    {{ \Carbon\Carbon::parse($pendaftar->jadwal_tanggal_resmi)->translatedFormat('l, d F Y') }}
                                </div>
                            </div>
                            <div>
                                <div style="font-size: 0.72rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Sesi Waktu</div>
                                <div style="font-weight: 800; font-size: 0.92rem; color: #0f172a; margin-top: 2px;">
                                    {{ $pendaftar->jadwal_sesi_resmi }}
                                </div>
                            </div>
                            <div>
                                <div style="font-size: 0.72rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Ruang Lab Komputer</div>
                                <div style="font-weight: 800; font-size: 0.92rem; color: #0f172a; margin-top: 2px;">
                                    {{ $pendaftar->jadwal_ruang_resmi }}
                                </div>
                            </div>
                            <div>
                                <div style="font-size: 0.72rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Format Seleksi</div>
                                <div style="font-weight: 800; font-size: 0.92rem; color: #059669; margin-top: 2px;">
                                    1x Gelombang (Sesuai Juknis)
                                </div>
                            </div>
                        </div>

                        @php
                            $ujianPeserta = $pendaftar->ujianPeserta;
                            $sudahTes = $ujianPeserta && in_array($ujianPeserta->status_pengerjaan, ['selesai', 'selesai_menunggu_koreksi', 'selesai_dinilai']);
                        @endphp

                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                            @if($sudahTes)
                                <div style="color: #059669; font-weight: 800; font-size: 0.88rem;">
                                    &bull; Anda telah menyelesaikan Ujian CBT tertulis. Hasil seleksi sedang dalam proses perangkingan kelulusan.
                                </div>
                            @else
                                <div style="color: #1e40af; font-size: 0.82rem; font-weight: 600;">
                                    Pastikan koneksi internet stabil sebelum mulai mengerjakan ujian.
                                </div>
                                <a href="{{ route('ppdb.ujian.konfirmasi', $pendaftar->no_pendaftaran) }}" style="display: inline-block; background: #2563eb; color: #ffffff; font-weight: 800; padding: 10px 22px; border-radius: 8px; text-decoration: none; font-size: 0.9rem;">
                                    Masuk Ruang Ujian CBT &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- KONDISI 3: JIKA CADANGAN --}}
                @if($pendaftar->status == 'cadangan')
                    <div style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 12px; padding: 20px 22px; margin-bottom: 24px; color: #92400e; font-size: 0.88rem; line-height: 1.6;">
                        <div style="font-weight: 800; font-size: 1rem; color: #b45309; margin-bottom: 4px;">
                            Status Seleksi: Peserta Cadangan
                        </div>
                        Nilai seleksi Anda memenuhi syarat standar kelulusan, namun kuota daya tampung rombel kejuruan saat ini telah terpenuhi. Anda tercatat sebagai peserta cadangan dan akan dihubungi oleh panitia apabila terdapat calon siswa yang mengundurkan diri pada saat proses daftar ulang.
                    </div>
                @endif

                {{-- KONDISI 4: JIKA DITOLAK --}}
                @if($pendaftar->status == 'ditolak')
                    <div style="background: #fef2f2; border: 1.5px solid #fecaca; border-radius: 12px; padding: 20px 22px; margin-bottom: 24px; color: #991b1b; font-size: 0.88rem; line-height: 1.6;">
                        <div style="font-weight: 800; font-size: 1rem; color: #b91c1c; margin-bottom: 4px;">
                            Status Seleksi: Tidak Lolos
                        </div>
                        Terima kasih atas partisipasi dan minat Anda mendaftar di SMK Negeri 1 Air Naningan. Mohon maaf Anda belum memenuhi kriteria kelulusan seleksi PPDB tahun ini. Tetap semangat dan raih prestasi terbaik di tempat berikutnya.
                    </div>
                @endif

                @if($pendaftar->catatan_panitia || $pendaftar->catatan)
                    <div style="background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; padding: 14px 16px; margin-bottom: 24px; font-size: 0.85rem; color: #334155;">
                        <strong>Catatan Panitia PPDB:</strong> {{ $pendaftar->catatan_panitia ?: $pendaftar->catatan }}
                    </div>
                @endif

                {{-- FOOTER BUTTONS --}}
                <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 16px; border-top: 1px solid #e2e8f0; flex-wrap: wrap;">
                    <a href="{{ route('ppdb.cetak', $pendaftar->no_pendaftaran ?? $pendaftar->nomor_pendaftaran) }}" target="_blank" class="btn-industrial btn-industrial-primary" style="padding: 10px 22px; font-size: 0.88rem; font-weight: 700; text-decoration: none;">
                        @if($pendaftar->status == 'diterima')
                            Cetak Surat Bukti Diterima PPDB
                        @else
                            Cetak Kartu Tanda Peserta PPDB
                        @endif
                    </a>
                </div>
            </div>
        @else
            <div class="bento-card" style="text-align: center; padding: 48px 20px;">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 6px;">Data Pendaftaran Tidak Ditemukan</h3>
                <p style="color: #64748b; font-size: 0.88rem; max-width: 420px; margin: 0 auto; line-height: 1.5;">
                    Pastikan Anda memasukkan Nomor Registrasi resmi (contoh: <code>PPDB-2026-0001</code>) atau NISN yang tepat saat mendaftar online.
                </p>
            </div>
        @endif
    @endif

</div>
@endsection
