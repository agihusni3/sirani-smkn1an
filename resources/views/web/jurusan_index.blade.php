@extends('web.layouts.app')

@section('title', 'Konsentrasi Keahlian Vokasi — SMKN 1 Air Naningan')
@section('meta_description', 'Tiga program keahlian kejuruan: Rekayasa Perangkat Lunak (RPL), Agro-Teknologi Pangan (APHP), dan Teknik Otomotif Sepeda Motor (TSM).')

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 60px;">
    
    <!-- Header Jurusan -->
    <div style="max-width: 760px; margin-bottom: 36px;">
        <span class="section-tag">Program Keahlian Vokasi</span>
        <h1 class="section-title-large">3 Konsentrasi Keahlian Standar Industri</h1>
        <p style="color: var(--text-body); font-size: 1.05rem; margin-top: 12px; line-height: 1.65;">
            Kurikulum dirancang dengan rasio praktik 70% di workshop dan lab modern, selaras dengan Standar Kompetensi Kerja Nasional Indonesia (SKKNI) dan lisensi BNSP.
        </p>
    </div>

    <div class="bento-grid">
        @foreach($jurusans as $j)
            @php
                $meta = $j->getProfilMetadata();
                $accentColors = [
                    'rpl' => ['border' => 'var(--brand-blue)', 'bg' => 'var(--brand-blue-subtle)', 'code' => '01 / SOFTWARE'],
                    'aphp' => ['border' => 'var(--brand-emerald)', 'bg' => 'var(--brand-emerald-subtle)', 'code' => '02 / AGRO-TECH'],
                    'tsm' => ['border' => 'var(--brand-amber)', 'bg' => 'var(--brand-amber-subtle)', 'code' => '03 / AUTOMOTIVE'],
                ];
                $acc = $accentColors[strtolower($j->kode)] ?? ['border' => 'var(--brand-blue)', 'bg' => 'var(--brand-blue-subtle)', 'code' => 'SPEC'];
            @endphp
            <div class="bento-card" style="grid-column: span 4; border-top: 4px solid {{ $acc['border'] }}; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="overflow: hidden; border-radius: var(--radius-md); margin-bottom: 18px; height: 180px; border: 1px solid var(--border-main);">
                        <img src="{{ asset('images/web/jurusan_' . strtolower($j->kode) . '.jpg') }}" alt="{{ $j->nama_jurusan }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;" onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                        <span style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; background: {{ $acc['bg'] }}; color: {{ $acc['border'] }}; border: 1px solid rgba(0,0,0,0.06);">
                            {{ $acc['code'] }}
                        </span>
                        <div style="font-size: 1.15rem; color: {{ $acc['border'] }};">
                            <i class="{{ $meta['icon'] ?? 'fa-solid fa-graduation-cap' }}"></i>
                        </div>
                    </div>

                    <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-dark); margin-bottom: 8px; line-height: 1.25;">
                        {{ $j->nama_jurusan }}
                    </h2>

                    <p style="font-size: 0.88rem; color: var(--text-body); line-height: 1.6; margin-bottom: 20px;">
                        {{ $meta['deskripsi'] ?? 'Program keahlian unggulan di SMKN 1 Air Naningan yang memadukan teori dan praktik intensif.' }}
                    </p>

                    <div style="margin-bottom: 22px; background: var(--bg-surface-alt); padding: 14px 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-main);">
                        <div style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.05em;">
                            Kompetensi Inti:
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 7px;">
                            @foreach($meta['kompetensi'] ?? [] as $komp)
                                <div style="font-size: 0.82rem; font-weight: 600; color: var(--text-dark); display: flex; align-items: center; gap: 8px;">
                                    <i class="fa-solid fa-check" style="color: {{ $acc['border'] }}; font-size: 0.75rem;"></i>
                                    {{ $komp }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div style="padding-top: 18px; border-top: 1px solid var(--border-main); display: flex; justify-content: space-between; align-items: center;">
                    <a href="{{ route('web.jurusan.show', strtolower($j->kode)) }}" style="font-size: 0.88rem; font-weight: 700; color: {{ $acc['border'] }}; display: inline-flex; align-items: center; gap: 6px;">
                        Detail Silabus <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem;"></i>
                    </a>
                    <a href="{{ route('ppdb.formulir') }}" class="btn-industrial btn-industrial-dark" style="font-size: 0.76rem; padding: 6px 14px;">
                        Daftar Jurusan
                    </a>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection

