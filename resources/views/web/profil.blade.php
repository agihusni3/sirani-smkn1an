@extends('web.layouts.app')

@section('title', 'Profil Institusi — SMKN 1 Air Naningan')
@section('meta_description', 'Profil resmi SMKN 1 Air Naningan: Visi, Misi, Identitas Pokok NPSN, Sarana Prasarana Praktik Kejuruan, dan Direktori Tenaga Pendidik.')

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 60px;">

    <!-- Header Profil -->
    <div style="max-width: 800px; margin-bottom: 36px;">
        <span class="section-tag">Profil Kelembagaan & Visi Vokasi</span>
        <h1 class="section-title-large">Membangun Keahlian Presisi Menuju Kemandirian Industri</h1>
        <p style="color: var(--text-body); font-size: 1.05rem; margin-top: 12px; line-height: 1.65;">
            SMK Negeri 1 Air Naningan adalah institusi pendidikan kejuruan negeri di bawah naungan Pemerintah Provinsi Lampung yang berfokus pada integrasi keterampilan rekayasa teknologi, agro-industri pangan terapan, dan keteknikan otomotif.
        </p>
    </div>

    <!-- Visual Banner Kampus -->
    <div style="border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 36px; max-height: 380px; border: 1px solid var(--border-main); position: relative; box-shadow: var(--shadow-card);">
        <img src="{{ asset('images/web/hero_kampus.jpg') }}" alt="Gedung Kampus SMKN 1 Air Naningan Tanggamus" style="width: 100%; height: 100%; object-fit: cover; display: block;">
        <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(15, 23, 42, 0.92), transparent); padding: 28px 32px; color: #ffffff;">
            <div style="font-family: var(--font-tech); font-size: 0.78rem; font-weight: 700; color: #60a5fa; text-transform: uppercase; letter-spacing: 0.05em;">Kampus Vokasi Air Naningan</div>
            <div style="font-size: 1.35rem; font-weight: 800; margin-top: 2px;">SMK Negeri 1 Air Naningan • Kabupaten Tanggamus, Lampung</div>
        </div>
    </div>

    <!-- Bento Grid Profil -->
    <div class="bento-grid">
        
        <!-- Visi (Span 6) -->
        <div class="bento-card" style="grid-column: span 6; border-top: 4px solid var(--brand-blue);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: var(--brand-blue-subtle); display: flex; align-items: center; justify-content: center; color: var(--brand-blue); font-size: 1.1rem; border: 1px solid rgba(37,99,235,0.2);">
                    <i class="fa-solid fa-compass"></i>
                </div>
                <div>
                    <span style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Arah Haluan</span>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--text-dark);">Visi Sekolah</h3>
                </div>
            </div>
            <p style="font-size: 1.05rem; font-weight: 600; color: var(--text-dark); line-height: 1.7; font-style: italic; background: var(--bg-surface-alt); padding: 18px 20px; border-radius: var(--radius-sm); border-left: 3px solid var(--brand-blue);">
                "Menjadi Lembaga Pendidikan Kejuruan yang Unggul, Berakhlak Mulia, Berbudaya Industri, dan Berwawasan Lingkungan Menuju Indonesia Emas."
            </p>
        </div>

        <!-- Misi (Span 6) -->
        <div class="bento-card" style="grid-column: span 6; border-top: 4px solid var(--brand-emerald);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: var(--brand-emerald-subtle); display: flex; align-items: center; justify-content: center; color: var(--brand-emerald); font-size: 1.1rem; border: 1px solid rgba(4,120,87,0.2);">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <div>
                    <span style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Strategi Nyata</span>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--text-dark);">Misi Sekolah</h3>
                </div>
            </div>
            <ul style="color: var(--text-body); font-size: 0.92rem; line-height: 1.7; padding-left: 18px; display: flex; flex-direction: column; gap: 8px;">
                <li>Menyelenggarakan proses pembelajaran berbasis kompetensi dan kemitraan link &amp; match dunia industri.</li>
                <li>Menanamkan nilai religius, budi pekerti luhur, dan integritas kehadiran tinggi melalui ekosistem digital SIRANI.</li>
                <li>Meningkatkan kapasitas sertifikasi kompetensi pendidik dan keahlian teknis siswa secara berkelanjutan.</li>
                <li>Mengembangkan unit Teaching Factory (TEFA) sebagai sarana inkubasi wirausaha mandiri (technopreneur &amp; agripreneur).</li>
            </ul>
        </div>

        <!-- Identitas Pokok Sekolah (Span 12) -->
        <div class="bento-card" style="grid-column: span 12;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--border-main);">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-shield-halved" style="color: var(--brand-blue); font-size: 1.2rem;"></i>
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--text-dark);">Identitas & Data Pokok Sekolah</h3>
                </div>
                <span style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; background: var(--bg-surface-alt); padding: 4px 10px; border-radius: 6px; border: 1px solid var(--border-main);">
                    DAPODIK KEMENDIKBUD
                </span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                <div style="background: var(--bg-surface-alt); padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-main);">
                    <div style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">NPSN Sekolah</div>
                    <div style="font-size: 1.15rem; font-weight: 800; color: var(--text-dark); margin-top: 4px;">{{ $sekolah->npsn ?? '69888999' }}</div>
                </div>
                <div style="background: var(--bg-surface-alt); padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-main);">
                    <div style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Status Kelembagaan</div>
                    <div style="font-size: 1.05rem; font-weight: 800; color: var(--brand-emerald); margin-top: 4px;">NEGERI (Pemprov Lampung)</div>
                </div>
                <div style="background: var(--bg-surface-alt); padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-main);">
                    <div style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Status Akreditasi</div>
                    <div style="font-size: 1.05rem; font-weight: 800; color: var(--brand-amber); margin-top: 4px;">Terakreditasi B (BAN-SM)</div>
                </div>
                <div style="background: var(--bg-surface-alt); padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-main);">
                    <div style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Pimpinan Institusi</div>
                    <div style="font-size: 0.95rem; font-weight: 800; color: var(--text-dark); margin-top: 4px;">{{ $sekolah->nama_kepala_sekolah ?? 'Drs. H. Ahmad Sudrajat, M.Pd.' }}</div>
                </div>
            </div>
        </div>

        <!-- Galeri Fasilitas Workshop & Laboratorium (Span 12) -->
        <div class="bento-card" style="grid-column: span 12;">
            <div style="margin-bottom: 24px;">
                <span class="section-tag">Sarana & Prasarana Praktik</span>
                <h3 style="font-size: 1.3rem; font-weight: 800; color: var(--text-dark);">
                    Workshop & Laboratorium Standar Industri
                </h3>
                <p style="font-size: 0.9rem; color: var(--text-body); margin-top: 4px;">
                    Setiap konsentrasi keahlian didukung fasilitas praktik mandiri untuk menunjang kurikulum berbasis rekayasa langsung.
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div style="background: var(--bg-surface); border: 1px solid var(--border-main); border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-subtle);">
                    <img src="{{ asset('images/web/jurusan_rpl.jpg') }}" alt="Laboratorium Rekayasa Perangkat Lunak" style="width: 100%; height: 180px; object-fit: cover;">
                    <div style="padding: 18px;">
                        <span style="font-family: var(--font-tech); font-size: 0.7rem; font-weight: 700; color: var(--brand-blue); text-transform: uppercase;">01 / Software Lab</span>
                        <h4 style="font-size: 1.05rem; font-weight: 800; color: var(--text-dark); margin: 4px 0 6px 0;">Lab Rekayasa Perangkat Lunak</h4>
                        <p style="font-size: 0.84rem; color: var(--text-body); line-height: 1.55;">PC workstation modern, gigabit network, cloud deployment workstation, dan IoT testbed untuk inovasi digital.</p>
                    </div>
                </div>

                <div style="background: var(--bg-surface); border: 1px solid var(--border-main); border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-subtle);">
                    <img src="{{ asset('images/web/jurusan_aphp.jpg') }}" alt="Unit Produksi Pengolahan Pangan" style="width: 100%; height: 180px; object-fit: cover;">
                    <div style="padding: 18px;">
                        <span style="font-family: var(--font-tech); font-size: 0.7rem; font-weight: 700; color: var(--brand-emerald); text-transform: uppercase;">02 / Agro-Tech Lab</span>
                        <h4 style="font-size: 1.05rem; font-weight: 800; color: var(--text-dark); margin: 4px 0 6px 0;">Workshop Agro-Teknologi Pangan</h4>
                        <p style="font-size: 0.84rem; color: var(--text-body); line-height: 1.55;">Peralatan olahan kopi Tanggamus, digital coffee roaster, grinder industri, packaging sealing, dan uji mutu higienis.</p>
                    </div>
                </div>

                <div style="background: var(--bg-surface); border: 1px solid var(--border-main); border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-subtle);">
                    <img src="{{ asset('images/web/jurusan_tsm.jpg') }}" alt="Bengkel Teknik Sepeda Motor" style="width: 100%; height: 180px; object-fit: cover;">
                    <div style="padding: 18px;">
                        <span style="font-family: var(--font-tech); font-size: 0.7rem; font-weight: 700; color: var(--brand-amber); text-transform: uppercase;">03 / Automotive Workshop</span>
                        <h4 style="font-size: 1.05rem; font-weight: 800; color: var(--text-dark); margin: 4px 0 6px 0;">Bengkel Otomotif Sepeda Motor</h4>
                        <p style="font-size: 0.84rem; color: var(--text-body); line-height: 1.55;">Standar bengkel resmi APM, hydraulic bike lift, diagnostic scanner EFI, tune-up station, dan SST toolkit presisi.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dewan Guru & Staf (Span 12) -->
        <div class="bento-card" style="grid-column: span 12;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; padding-bottom: 14px; border-bottom: 1px solid var(--border-main);">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-dark);">
                        Dewan Pendidik & Tenaga Kependidikan
                    </h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 2px;">
                        Total {{ $gurus->count() }} Guru dan Tenaga Kependidikan berkompeten di SMKN 1 Air Naningan
                    </p>
                </div>
                <span style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; background: var(--bg-surface-alt); padding: 4px 10px; border-radius: 6px; border: 1px solid var(--border-main);">
                    SDM BERPENGALAMAN
                </span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px;">
                @foreach($gurus as $guru)
                    <div style="background: var(--bg-surface-alt); border: 1px solid var(--border-main); border-radius: var(--radius-md); padding: 18px 14px; text-align: center; transition: var(--transition);">
                        <div style="width: 54px; height: 54px; border-radius: 50%; background: #ffffff; color: var(--brand-navy); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin: 0 auto 12px auto; border: 2px solid var(--border-main); box-shadow: var(--shadow-subtle);">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div style="font-size: 0.9rem; font-weight: 800; color: var(--text-dark); line-height: 1.3; margin-bottom: 4px;">
                            {{ $guru->nama }}
                        </div>
                        <div style="font-size: 0.76rem; font-weight: 600; color: var(--brand-blue);">
                            {{ $guru->mata_pelajaran ?? 'Pendidik Kejuruan' }}
                        </div>
                        @if($guru->nip)
                            <div style="font-family: var(--font-tech); font-size: 0.7rem; color: var(--text-muted); margin-top: 4px;">
                                NIP: {{ $guru->nip }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection

