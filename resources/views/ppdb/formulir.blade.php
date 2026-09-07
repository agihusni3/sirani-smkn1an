@extends('web.layouts.app')

@section('title', 'Formulir Pendaftaran Siswa Baru — PPDB 2026/2027')
@section('meta_description', 'Isi data diri dan berkas persyaratan pendaftaran calon peserta didik baru SMKN 1 Air Naningan.')

@section('content')
<div class="container" style="padding-top: 40px; padding-bottom: 60px; max-width: 980px;">

    <!-- Breadcrumb -->
    <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--text-muted);">
        <a href="{{ route('web.beranda') }}" style="color: var(--brand-blue); font-weight: 600;">Beranda</a>
        <span style="font-size: 0.85rem;">/</span>
        <a href="{{ route('ppdb.index') }}" style="color: var(--brand-blue); font-weight: 600;">PPDB 2026</a>
        <span style="font-size: 0.85rem;">/</span>
        <span style="color: var(--text-dark); font-weight: 700;">Formulir Pendaftaran</span>
    </div>

    <div class="bento-card" style="padding: clamp(24px, 4vw, 44px);">
        <div style="margin-bottom: 32px; padding-bottom: 20px; border-bottom: 1px solid var(--border-main);">
            <span class="badge-pulse" style="margin-bottom: 10px; display: inline-block;">FORMULIR RESMI PPDB ONLINE</span>
            <h1 style="font-size: clamp(1.8rem, 3vw, 2.3rem); font-weight: 800; color: var(--text-dark); letter-spacing: -0.03em;">
                Pendaftaran Calon Siswa Baru TP {{ $tahunAjaran }}
            </h1>
            <p style="color: var(--text-body); font-size: 0.95rem; margin-top: 8px; line-height: 1.6;">
                Pastikan data yang diinputkan sesuai dengan berkas kependudukan resmi (Kartu Keluarga, Akta Kelahiran, dan Ijazah/SKL SMP/MTs).
            </p>
        </div>

        @if($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: var(--radius-sm); padding: 18px 20px; margin-bottom: 28px;">
                <div style="font-size: 0.92rem; font-weight: 700; color: #b91c1c; margin-bottom: 8px;">
                    Terdapat kendala pada data formulir Anda:
                </div>
                <ul style="color: #991b1b; font-size: 0.88rem; padding-left: 20px; line-height: 1.6;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('ppdb.simpan') }}" method="POST" enctype="multipart/form-data" id="formPpdb">
            @csrf

            <!-- 1. JALUR & PILIHAN KEAHLIAN -->
            <div style="margin-bottom: 36px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px;">
                    <span style="width: 28px; height: 28px; border-radius: 50%; background: var(--brand-blue); color: white; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 800;">1</span>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-dark);">
                        Jalur Masuk & Pilihan Kejuruan
                    </h3>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px;">
                            Jalur Pendaftaran <span style="color: #ef4444;">*</span>
                        </label>
                        <select name="jalur_pendaftaran" required style="width: 100%; padding: 11px 14px; font-size: 0.9rem;">
                            <option value="reguler" {{ old('jalur_pendaftaran') == 'reguler' ? 'selected' : '' }}>Jalur Reguler (Nilai Rapor & Umum)</option>
                            <option value="prestasi" {{ old('jalur_pendaftaran') == 'prestasi' ? 'selected' : '' }}>Jalur Prestasi (Peringkat / Sertifikat)</option>
                            <option value="afirmasi" {{ old('jalur_pendaftaran') == 'afirmasi' ? 'selected' : '' }}>Jalur Afirmasi (KIP / PKH / KPS)</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px;">
                            Pilihan 1 (Prioritas Utama) <span style="color: #ef4444;">*</span>
                        </label>
                        <select name="jurusan_pilihan_1_id" required style="width: 100%; padding: 11px 14px; font-size: 0.9rem;">
                            <option value="">-- Pilih Konsentrasi Keahlian --</option>
                            @foreach($jurusans as $j)
                                <option value="{{ $j->id }}" {{ old('jurusan_pilihan_1_id') == $j->id ? 'selected' : '' }}>
                                    {{ $j->kode }} — {{ $j->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px;">
                            Pilihan 2 (Cadangan)
                        </label>
                        <select name="jurusan_pilihan_2_id" style="width: 100%; padding: 11px 14px; font-size: 0.9rem;">
                            <option value="">-- Alternatif Cadangan (Opsional) --</option>
                            @foreach($jurusans as $j)
                                <option value="{{ $j->id }}" {{ old('jurusan_pilihan_2_id') == $j->id ? 'selected' : '' }}>
                                    {{ $j->kode }} — {{ $j->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- 2. BIODATA CALON SISWA -->
            <div style="margin-bottom: 36px; padding-top: 24px; border-top: 1px solid var(--border-main);">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px;">
                    <span style="width: 28px; height: 28px; border-radius: 50%; background: var(--brand-emerald); color: white; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 800;">2</span>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-dark);">
                        Biodata Calon Peserta Didik
                    </h3>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px;">
                            NISN (Nomor Induk Siswa Nasional) <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" name="nisn" maxlength="30" value="{{ old('nisn') }}" required placeholder="Masukkan NISN Anda..." style="width: 100%; padding: 11px 14px; font-size: 0.9rem;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px;">
                            NIK (Nomor Induk Kependudukan)
                        </label>
                        <input type="text" name="nik" maxlength="16" value="{{ old('nik') }}" placeholder="16 digit sesuai Kartu Keluarga" style="width: 100%; padding: 11px 14px; font-size: 0.9rem;">
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px;">
                            Nama Lengkap Calon Siswa (Sesuai Ijazah SMP) <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required placeholder="Nama Lengkap Huruf Kapital" style="width: 100%; padding: 11px 14px; font-size: 0.9rem;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px;">
                            Jenis Kelamin <span style="color: #ef4444;">*</span>
                        </label>
                        <select name="jenis_kelamin" required style="width: 100%; padding: 11px 14px; font-size: 0.9rem;">
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px;">
                            Agama <span style="color: #ef4444;">*</span>
                        </label>
                        <select name="agama" required style="width: 100%; padding: 11px 14px; font-size: 0.9rem;">
                            <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px;">
                            Tempat Lahir <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" required placeholder="Contoh: Tanggamus" style="width: 100%; padding: 11px 14px; font-size: 0.9rem;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px;">
                            Tanggal Lahir <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required style="width: 100%; padding: 11px 14px; font-size: 0.9rem;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px;">
                            Asal Sekolah (SMP/MTs) <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah') }}" required placeholder="Contoh: SMPN 1 Air Naningan" style="width: 100%; padding: 11px 14px; font-size: 0.9rem;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px;">
                            Tahun Kelulusan <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="number" name="tahun_lulus" value="{{ old('tahun_lulus', date('Y')) }}" required min="2020" max="{{ date('Y') + 1 }}" style="width: 100%; padding: 11px 14px; font-size: 0.9rem;">
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px;">
                            Nomor WhatsApp Siswa (Penerima Notifikasi Hasil Seleksi / Kelulusan) <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text" name="no_hp_siswa" value="{{ old('no_hp_siswa') }}" required placeholder="08xxxxxxxxxx (Pengumuman diterima / tidak akan dikirim langsung ke nomor WhatsApp ini)" style="width: 100%; padding: 11px 14px; font-size: 0.9rem;">
                    </div>
                </div>

                <div style="margin-top: 18px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px;">
                        Alamat Tempat Tinggal Lengkap <span style="color: #ef4444;">*</span>
                    </label>
                    <textarea name="alamat_lengkap" required rows="2" placeholder="Nama Jalan, RT/RW, Dusun, Pekon / Desa, Kecamatan, Kabupaten..." style="width: 100%; padding: 11px 14px; font-size: 0.9rem;">{{ old('alamat_lengkap') }}</textarea>
                </div>

                {{-- Hobi & Minat Organisasi --}}
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; margin-top: 18px; padding-top: 18px; border-top: 1px dashed var(--border-main);">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px;">
                            Hobi Calon Siswa
                        </label>
                        <input type="text" name="hobi" value="{{ old('hobi') }}" placeholder="Contoh: Membaca, Sepak Bola, Komputer, Musik, dll." style="width: 100%; padding: 11px 14px; font-size: 0.9rem;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px;">
                            Organisasi yang Diminati
                        </label>
                        <input type="text" name="organisasi_minat" value="{{ old('organisasi_minat') }}" placeholder="Contoh: OSIS, Pramuka, PMR, Paskibra, Rohis, Seni, Olahraga" style="width: 100%; padding: 11px 14px; font-size: 0.9rem;">
                    </div>
                </div>
            </div>

            <!-- 3. DATA ORANG TUA -->
            <div style="margin-bottom: 36px; padding-top: 24px; border-top: 1px solid var(--border-main);">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px;">
                    <span style="width: 28px; height: 28px; border-radius: 50%; background: var(--brand-amber); color: white; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 800;">3</span>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-dark);">
                        Data Orang Tua (Ayah & Ibu)
                    </h3>
                </div>

                {{-- DUA KARTU: DATA AYAH & DATA IBU --}}
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
                    
                    {{-- KARTU DATA AYAH KANDUNG --}}
                    <div style="background: var(--bg-surface-alt, #f8fafc); border: 1px solid var(--border-main); border-radius: var(--radius-sm); padding: 18px;">
                        <div style="margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid var(--border-main);">
                            <h4 style="font-size: 0.95rem; font-weight: 800; color: var(--text-dark); margin: 0;">Data Ayah Kandung</h4>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <div>
                                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                                    Nama Lengkap Ayah
                                </label>
                                <input type="text" name="nama_ayah" value="{{ old('nama_ayah') }}" placeholder="Nama lengkap sesuai KTP" style="width: 100%; padding: 10px 12px; font-size: 0.88rem;">
                            </div>

                            <div>
                                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                                    Pekerjaan Ayah
                                </label>
                                <select name="pekerjaan_ayah" style="width: 100%; padding: 10px 12px; font-size: 0.88rem;">
                                    <option value="">-- Pilih Pekerjaan Ayah --</option>
                                    <option value="Petani / Pekebun" {{ old('pekerjaan_ayah') == 'Petani / Pekebun' ? 'selected' : '' }}>Petani / Pekebun</option>
                                    <option value="Wiraswasta / Pedagang" {{ old('pekerjaan_ayah') == 'Wiraswasta / Pedagang' ? 'selected' : '' }}>Wiraswasta / Pedagang</option>
                                    <option value="Karyawan Swasta" {{ old('pekerjaan_ayah') == 'Karyawan Swasta' ? 'selected' : '' }}>Karyawan Swasta</option>
                                    <option value="PNS / TNI / Polri" {{ old('pekerjaan_ayah') == 'PNS / TNI / Polri' ? 'selected' : '' }}>PNS / TNI / Polri</option>
                                    <option value="Buruh Harian / Tukang" {{ old('pekerjaan_ayah') == 'Buruh Harian / Tukang' ? 'selected' : '' }}>Buruh Harian / Tukang</option>
                                    <option value="Pensiunan" {{ old('pekerjaan_ayah') == 'Pensiunan' ? 'selected' : '' }}>Pensiunan</option>
                                    <option value="Tidak Bekerja / Meninggal" {{ old('pekerjaan_ayah') == 'Tidak Bekerja / Meninggal' ? 'selected' : '' }}>Tidak Bekerja / Meninggal</option>
                                    <option value="Lainnya" {{ old('pekerjaan_ayah') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>

                            <div>
                                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                                    Pendidikan Terakhir Ayah
                                </label>
                                <select name="pendidikan_ayah" style="width: 100%; padding: 10px 12px; font-size: 0.88rem;">
                                    <option value="">-- Pilih Pendidikan Terakhir --</option>
                                    <option value="SD / Sederajat" {{ old('pendidikan_ayah') == 'SD / Sederajat' ? 'selected' : '' }}>SD / Sederajat</option>
                                    <option value="SMP / Sederajat" {{ old('pendidikan_ayah') == 'SMP / Sederajat' ? 'selected' : '' }}>SMP / Sederajat</option>
                                    <option value="SMA / SMK / Sederajat" {{ old('pendidikan_ayah') == 'SMA / SMK / Sederajat' ? 'selected' : '' }}>SMA / SMK / Sederajat</option>
                                    <option value="D1 / D2 / D3" {{ old('pendidikan_ayah') == 'D1 / D2 / D3' ? 'selected' : '' }}>D1 / D2 / D3</option>
                                    <option value="S1 / D4" {{ old('pendidikan_ayah') == 'S1 / D4' ? 'selected' : '' }}>S1 / D4</option>
                                    <option value="S2 / S3" {{ old('pendidikan_ayah') == 'S2 / S3' ? 'selected' : '' }}>S2 / S3</option>
                                    <option value="Tidak Sekolah" {{ old('pendidikan_ayah') == 'Tidak Sekolah' ? 'selected' : '' }}>Tidak Sekolah</option>
                                </select>
                            </div>

                            <div>
                                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                                    Nomor WhatsApp / HP Ayah (Opsional)
                                </label>
                                <input type="text" name="no_hp_ayah" value="{{ old('no_hp_ayah') }}" placeholder="08xxxxxxxxxx" style="width: 100%; padding: 10px 12px; font-size: 0.88rem;">
                            </div>
                        </div>
                    </div>

                    {{-- KARTU DATA IBU KANDUNG --}}
                    <div style="background: var(--bg-surface-alt, #f8fafc); border: 1px solid var(--border-main); border-radius: var(--radius-sm); padding: 18px;">
                        <div style="margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid var(--border-main);">
                            <h4 style="font-size: 0.95rem; font-weight: 800; color: var(--text-dark); margin: 0;">Data Ibu Kandung</h4>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <div>
                                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                                    Nama Lengkap Ibu <span style="color: #ef4444;">*</span>
                                </label>
                                <input type="text" name="nama_ibu" value="{{ old('nama_ibu') }}" required placeholder="Nama lengkap sesuai KTP / KK" style="width: 100%; padding: 10px 12px; font-size: 0.88rem;">
                            </div>

                            <div>
                                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                                    Pekerjaan Ibu
                                </label>
                                <select name="pekerjaan_ibu" style="width: 100%; padding: 10px 12px; font-size: 0.88rem;">
                                    <option value="">-- Pilih Pekerjaan Ibu --</option>
                                    <option value="Ibu Rumah Tangga (IRT)" {{ old('pekerjaan_ibu') == 'Ibu Rumah Tangga (IRT)' ? 'selected' : '' }}>Ibu Rumah Tangga (IRT)</option>
                                    <option value="Petani / Pekebun" {{ old('pekerjaan_ibu') == 'Petani / Pekebun' ? 'selected' : '' }}>Petani / Pekebun</option>
                                    <option value="Wiraswasta / Pedagang" {{ old('pekerjaan_ibu') == 'Wiraswasta / Pedagang' ? 'selected' : '' }}>Wiraswasta / Pedagang</option>
                                    <option value="Karyawan Swasta" {{ old('pekerjaan_ibu') == 'Karyawan Swasta' ? 'selected' : '' }}>Karyawan Swasta</option>
                                    <option value="PNS / TNI / Polri" {{ old('pekerjaan_ibu') == 'PNS / TNI / Polri' ? 'selected' : '' }}>PNS / TNI / Polri</option>
                                    <option value="Buruh Harian" {{ old('pekerjaan_ibu') == 'Buruh Harian' ? 'selected' : '' }}>Buruh Harian</option>
                                    <option value="Tidak Bekerja / Meninggal" {{ old('pekerjaan_ibu') == 'Tidak Bekerja / Meninggal' ? 'selected' : '' }}>Tidak Bekerja / Meninggal</option>
                                    <option value="Lainnya" {{ old('pekerjaan_ibu') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>

                            <div>
                                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                                    Pendidikan Terakhir Ibu
                                </label>
                                <select name="pendidikan_ibu" style="width: 100%; padding: 10px 12px; font-size: 0.88rem;">
                                    <option value="">-- Pilih Pendidikan Terakhir --</option>
                                    <option value="SD / Sederajat" {{ old('pendidikan_ibu') == 'SD / Sederajat' ? 'selected' : '' }}>SD / Sederajat</option>
                                    <option value="SMP / Sederajat" {{ old('pendidikan_ibu') == 'SMP / Sederajat' ? 'selected' : '' }}>SMP / Sederajat</option>
                                    <option value="SMA / SMK / Sederajat" {{ old('pendidikan_ibu') == 'SMA / SMK / Sederajat' ? 'selected' : '' }}>SMA / SMK / Sederajat</option>
                                    <option value="D1 / D2 / D3" {{ old('pendidikan_ibu') == 'D1 / D2 / D3' ? 'selected' : '' }}>D1 / D2 / D3</option>
                                    <option value="S1 / D4" {{ old('pendidikan_ibu') == 'S1 / D4' ? 'selected' : '' }}>S1 / D4</option>
                                    <option value="S2 / S3" {{ old('pendidikan_ibu') == 'S2 / S3' ? 'selected' : '' }}>S2 / S3</option>
                                    <option value="Tidak Sekolah" {{ old('pendidikan_ibu') == 'Tidak Sekolah' ? 'selected' : '' }}>Tidak Sekolah</option>
                                </select>
                            </div>

                            <div>
                                <label style="display: block; font-size: 0.82rem; font-weight: 700; color: var(--text-dark); margin-bottom: 6px;">
                                    Nomor WhatsApp / HP Ibu (Opsional)
                                </label>
                                <input type="text" name="no_hp_ibu" value="{{ old('no_hp_ibu') }}" placeholder="08xxxxxxxxxx" style="width: 100%; padding: 10px 12px; font-size: 0.88rem;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. UPLOAD BERKAS PENDUKUNG -->
            <div style="margin-bottom: 36px; padding-top: 24px; border-top: 1px solid var(--border-main);">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px;">
                    <span style="width: 28px; height: 28px; border-radius: 50%; background: #0f172a; color: white; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 800;">4</span>
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-dark);">
                        Unggah Berkas Persyaratan Digital (Foto / PDF)
                    </h3>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px;">
                    <div style="background: var(--bg-surface-alt); border: 1.5px dashed var(--border-main); border-radius: var(--radius-sm); padding: 18px;">
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">
                            Pas Foto 3x4
                        </label>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 12px;">Format JPG/PNG, Maksimal 2MB</p>
                        <input type="file" name="pas_foto" accept="image/*" style="font-size: 0.82rem; color: var(--text-body);">
                    </div>

                    <div style="background: var(--bg-surface-alt); border: 1.5px dashed var(--border-main); border-radius: var(--radius-sm); padding: 18px;">
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">
                            Scan Kartu Keluarga (KK)
                        </label>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 12px;">Format PDF/JPG, Maksimal 3MB</p>
                        <input type="file" name="scan_kk" accept="image/*,application/pdf" style="font-size: 0.82rem; color: var(--text-body);">
                    </div>

                    <div style="background: var(--bg-surface-alt); border: 1.5px dashed var(--border-main); border-radius: var(--radius-sm); padding: 18px;">
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">
                            Scan SKL / Ijazah SMP
                        </label>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 12px;">Format PDF/JPG, Maksimal 3MB</p>
                        <input type="file" name="scan_ijazah_skl" accept="image/*,application/pdf" style="font-size: 0.82rem; color: var(--text-body);">
                    </div>

                    <div style="background: var(--bg-surface-alt); border: 1.5px dashed var(--border-main); border-radius: var(--radius-sm); padding: 18px;">
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">
                            Scan KTP Orang Tua (1 Saja)
                        </label>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 12px;">KTP Ayah / Ibu / Wali (JPG/PDF, Maks 3MB)</p>
                        <input type="file" name="scan_ktp_ortu" accept="image/*,application/pdf" style="font-size: 0.82rem; color: var(--text-body);">
                    </div>

                    <div style="background: var(--bg-surface-alt); border: 1.5px dashed var(--border-main); border-radius: var(--radius-sm); padding: 18px;">
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">
                            Scan Akta Kelahiran
                        </label>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 12px;">Format PDF/JPG, Maksimal 3MB</p>
                        <input type="file" name="scan_akta" accept="image/*,application/pdf" style="font-size: 0.82rem; color: var(--text-body);">
                    </div>

                    <div style="background: var(--bg-surface-alt); border: 1.5px dashed var(--border-main); border-radius: var(--radius-sm); padding: 18px;">
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">
                            Scan Kartu Indonesia Pintar (KIP)
                        </label>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 12px;">KIP / PIP (Opsional / Jalur Afirmasi, Maks 3MB)</p>
                        <input type="file" name="scan_kip" accept="image/*,application/pdf" style="font-size: 0.82rem; color: var(--text-body);">
                    </div>

                    <div style="background: var(--bg-surface-alt); border: 1.5px dashed var(--border-main); border-radius: var(--radius-sm); padding: 18px;">
                        <label style="display: block; font-size: 0.88rem; font-weight: 700; color: var(--text-dark); margin-bottom: 4px;">
                            Scan Surat Ket. Tidak Mampu (SKTM)
                        </label>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 12px;">Dari Kelurahan / Pekon (Opsional / Afirmasi, Maks 3MB)</p>
                        <input type="file" name="scan_sktm" accept="image/*,application/pdf" style="font-size: 0.82rem; color: var(--text-body);">
                    </div>
                </div>
            </div>

            <!-- Tombol Kirim -->
            <div style="padding-top: 24px; border-top: 1px solid var(--border-main); display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px;">
                <div style="font-size: 0.85rem; color: var(--text-muted);">
                    Seluruh data terenkripsi dan disimpan di server mandiri sekolah.
                </div>
                <button type="submit" class="btn-industrial btn-industrial-primary" style="padding: 13px 36px; font-size: 0.95rem;">
                    Kirim Formulir Pendaftaran
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

