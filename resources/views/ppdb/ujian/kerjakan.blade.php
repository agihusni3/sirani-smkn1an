<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ujian CBT — {{ $pendaftar->nama_lengkap }} ({{ $pendaftar->no_pendaftaran }})</title>

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-subtle: #eff6ff;
            --success: #059669;
            --success-subtle: #ecfdf5;
            --warning: #d97706;
            --danger: #dc2626;
            --text-dark: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --border: #cbd5e1;
            --bg-page: #f8fafc;
            --font-main: 'Plus Jakarta Sans', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-main);
            color: var(--text-dark);
            background: var(--bg-page);
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            -webkit-user-select: none;
            user-select: none;
        }

        /* ══ TOPBAR ══ */
        .cbt-topbar {
            height: 64px;
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            flex-shrink: 0;
            z-index: 20;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .cbt-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cbt-logo-badge {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--primary-subtle);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            border: 1px solid rgba(37,99,235,0.2);
        }

        .cbt-user-info {
            display: flex;
            flex-direction: column;
        }

        .cbt-user-name {
            font-weight: 800;
            font-size: 0.95rem;
            color: #000000;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 260px;
        }

        .cbt-user-meta {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        /* TIMER BOX */
        .cbt-timer-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #0f172a;
            color: #ffffff;
            padding: 6px 16px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(15,23,42,0.15);
        }

        .cbt-timer-label {
            font-size: 0.72rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .cbt-timer-display {
            font-family: var(--font-mono);
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            color: #38bdf8;
        }

        .cbt-timer-display.warning {
            color: #fbbf24;
            animation: pulseWarning 1.5s infinite;
        }

        .cbt-timer-display.danger {
            color: #f87171;
            animation: pulseDanger 0.8s infinite;
        }

        @keyframes pulseWarning {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        @keyframes pulseDanger {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.05); }
        }

        /* ACTIONS */
        .cbt-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .cbt-save-indicator {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-finish {
            background: var(--success);
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 0.88rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-finish:hover {
            background: #047857;
            transform: translateY(-1px);
        }

        /* ══ SPLIT-SCREEN WORKSPACE ══ */
        .cbt-workspace {
            display: grid;
            grid-template-columns: 50% 50%;
            flex: 1;
            height: calc(100vh - 64px);
            overflow: hidden;
        }

        /* LEFT: PDF VIEWER */
        .cbt-pdf-pane {
            border-right: 2px solid var(--border);
            height: 100%;
            display: flex;
            flex-direction: column;
            background: #475569;
            position: relative;
        }

        .cbt-pdf-header {
            height: 38px;
            background: #1e293b;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 14px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .cbt-pdf-header a {
            color: #38bdf8;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
        }

        .cbt-pdf-frame {
            width: 100%;
            flex: 1;
            border: none;
            background: #ffffff;
        }

        /* RIGHT: ANSWER SHEET */
        .cbt-answer-pane {
            height: 100%;
            overflow-y: auto;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            padding: 20px 24px 60px 24px;
            scroll-behavior: smooth;
        }

        /* QUICK MAP */
        .cbt-quickmap-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .cbt-quickmap-title {
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cbt-map-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(36px, 1fr));
            gap: 6px;
        }

        .map-btn {
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-family: var(--font-mono);
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-dark);
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
        }

        .map-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .map-btn.answered {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        .map-btn.esai-btn {
            border-style: dashed;
        }

        .map-btn.esai-btn.answered {
            background: var(--success);
            border-color: var(--success);
        }

        /* SECTION HEADERS */
        .section-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }

        .section-badge-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--primary-subtle);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
        }

        .section-badge-title {
            font-weight: 800;
            font-size: 1.05rem;
            color: #000000;
        }

        .section-badge-sub {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-left: auto;
            font-weight: 700;
        }

        /* QUESTION ROW */
        .question-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .question-item:hover, .question-item:focus-within {
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(37,99,235,0.06);
        }

        .q-number {
            font-family: var(--font-mono);
            font-weight: 800;
            font-size: 0.95rem;
            color: #000000;
            width: 44px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .options-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .opt-label {
            position: relative;
            cursor: pointer;
        }

        .opt-label input[type="radio"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .opt-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 8px;
            border: 1.5px solid #cbd5e1;
            background: #ffffff;
            font-family: var(--font-mono);
            font-size: 0.95rem;
            font-weight: 800;
            color: #1e293b;
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .opt-label:hover .opt-pill {
            border-color: var(--primary);
            background: var(--primary-subtle);
            color: var(--primary);
            transform: translateY(-1px);
        }

        .opt-label input[type="radio"]:checked + .opt-pill {
            background: var(--primary);
            border-color: var(--primary);
            color: #ffffff;
            box-shadow: 0 3px 8px rgba(37,99,235,0.3);
            transform: scale(1.05);
        }

        /* ESSAY BOX */
        .essay-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 20px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .essay-item:focus-within {
            border-color: var(--success);
            box-shadow: 0 2px 8px rgba(5,150,105,0.08);
        }

        .essay-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .essay-title {
            font-weight: 800;
            font-size: 0.95rem;
            color: #000000;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .essay-counter {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .essay-textarea {
            width: 100%;
            min-height: 100px;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-family: var(--font-main);
            font-size: 0.9rem;
            color: #000000;
            background: #fdfdfe;
            resize: vertical;
            line-height: 1.5;
            outline: none;
            transition: border-color 0.15s;
        }

        .essay-textarea:focus {
            border-color: var(--success);
            background: #ffffff;
        }

        /* ══ MODAL KONFIRMASI SELESAI ══ */
        .cbt-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15,23,42,0.6);
            backdrop-filter: blur(4px);
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .cbt-modal-backdrop.active {
            display: flex;
        }

        .cbt-modal-card {
            background: #ffffff;
            border-radius: 16px;
            max-width: 480px;
            width: 100%;
            padding: 30px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: modalPop 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes modalPop {
            0% { transform: scale(0.9); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .cbt-workspace {
                grid-template-columns: 1fr;
                height: auto;
            }
            .cbt-pdf-pane {
                height: 50vh;
            }
            .cbt-answer-pane {
                height: calc(50vh - 64px);
            }
        }
    </style>
</head>
<body>

    <!-- TOPBAR -->
    <header class="cbt-topbar">
        <div class="cbt-brand">
            <div class="cbt-logo-badge">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div class="cbt-user-info">
                <span class="cbt-user-name">{{ $pendaftar->nama_lengkap }}</span>
                <span class="cbt-user-meta">No. Reg: {{ $pendaftar->no_pendaftaran }} | {{ $pendaftar->jurusanPilihan1->nama_jurusan ?? '-' }}</span>
            </div>
        </div>

        <!-- LIVE TIMER -->
        <div class="cbt-timer-box">
            <div class="cbt-timer-label">Sisa Waktu:</div>
            <div id="timerDisplay" class="cbt-timer-display">--:--:--</div>
        </div>

        <!-- ACTIONS -->
        <div class="cbt-actions">
            <div id="saveStatus" class="cbt-save-indicator">
                <i class="fa-solid fa-cloud-arrow-up text-muted"></i> Siap
            </div>
            <button type="button" class="btn-finish" onclick="openFinishModal()">
                <i class="fa-solid fa-circle-check"></i> Kumpulkan Ujian
            </button>
        </div>
    </header>

    <!-- FORM UTAMA -->
    <form id="ujianForm" action="{{ route('ppdb.ujian.selesai', $pendaftar->no_pendaftaran) }}" method="POST" style="flex: 1; display: flex; flex-direction: column; overflow: hidden;">
        @csrf

        <div class="cbt-workspace">
            
            <!-- LEFT PANE: NASKAH SOAL PDF -->
            <div class="cbt-pdf-pane">
                <div class="cbt-pdf-header">
                    <span><i class="fa-regular fa-file-pdf me-1 text-danger"></i> Dokumen Naskah Soal Terpadu</span>
                    @if($setting->file_pdf_soal)
                        <a href="{{ asset('storage/' . $setting->file_pdf_soal) }}" target="_blank">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Layar Penuh
                        </a>
                    @endif
                </div>

                @if($setting->file_pdf_soal)
                    <iframe src="{{ asset('storage/' . $setting->file_pdf_soal) }}#toolbar=0&navpanes=0" class="cbt-pdf-frame" title="Naskah Soal Ujian"></iframe>
                @else
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #ffffff; color: var(--text-muted); padding: 40px; text-align: center;">
                        <i class="fa-solid fa-file-circle-question" style="font-size: 3.5rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                        <h3 style="font-size: 1.15rem; font-weight: 800; color: #000000; margin-bottom: 6px;">Naskah Soal Sedang Dipersiapkan</h3>
                        <p style="font-size: 0.88rem; max-width: 400px; line-height: 1.5;">Panitia sedang mengunggah file naskah soal PDF resmi. Silakan hubungi pengawas jika naskah belum tampil.</p>
                    </div>
                @endif
            </div>

            <!-- RIGHT PANE: LEMBAR JAWAB DIGITAL -->
            <div class="cbt-answer-pane">
                
                <!-- QUICK NAVIGATOR MAP -->
                <div class="cbt-quickmap-card">
                    <div class="cbt-quickmap-title">
                        <span>Peta Nomor Soal</span>
                        <span id="progressText" style="color: var(--primary); font-family: var(--font-mono);">0 / {{ $jumlahPg + $jumlahEsai }} Terisi</span>
                    </div>

                    <!-- Grid 30 PG -->
                    <div style="font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">
                        Bagian I: Pilihan Ganda (1–{{ $jumlahPg }})
                    </div>
                    <div class="cbt-map-grid" style="margin-bottom: 14px;">
                        @for($i = 1; $i <= $jumlahPg; $i++)
                            @php
                                $sudahJawabPg = !empty($peserta->jawaban_pg[(string)$i]);
                            @endphp
                            <a href="#q_pg_{{ $i }}" id="nav_pg_{{ $i }}" class="map-btn {{ $sudahJawabPg ? 'answered' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor
                    </div>

                    <!-- Grid 5 Esai -->
                    <div style="font-size: 0.72rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">
                        Bagian II: Esai / Uraian ({{ $jumlahPg + 1 }}–{{ $jumlahPg + $jumlahEsai }})
                    </div>
                    <div class="cbt-map-grid">
                        @for($e = 1; $e <= $jumlahEsai; $e++)
                            @php
                                $nomorEsai = $jumlahPg + $e;
                                $sudahJawabEsai = !empty($peserta->jawaban_esai[(string)$nomorEsai]);
                            @endphp
                            <a href="#q_esai_{{ $nomorEsai }}" id="nav_esai_{{ $nomorEsai }}" class="map-btn esai-btn {{ $sudahJawabEsai ? 'answered' : '' }}">
                                {{ $nomorEsai }}
                            </a>
                        @endfor
                    </div>
                </div>

                <!-- SECTION 1: 30 SOAL PILIHAN GANDA -->
                <div class="section-badge">
                    <div class="section-badge-icon">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <div>
                        <div class="section-badge-title">Bagian I: 30 Butir Pilihan Ganda</div>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">Pilihlah salah satu opsi A, B, C, D, atau E sesuai naskah soal</div>
                    </div>
                    <div class="section-badge-sub">Bobot: {{ $setting->bobot_pg }}%</div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 32px;">
                    @for($i = 1; $i <= $jumlahPg; $i++)
                        @php
                            $currentJawab = $peserta->jawaban_pg[(string)$i] ?? null;
                        @endphp
                        <div class="question-item" id="q_pg_{{ $i }}">
                            <div class="q-number">
                                <span>{{ $i }}.</span>
                            </div>
                            
                            <div class="options-group">
                                @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
                                    <label class="opt-label">
                                        <input type="radio" 
                                               name="jawaban_pg[{{ $i }}]" 
                                               value="{{ $opt }}" 
                                               onchange="handlePgChange({{ $i }})"
                                               {{ $currentJawab === $opt ? 'checked' : '' }}>
                                        <span class="opt-pill">{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endfor
                </div>

                <!-- SECTION 2: 5 SOAL ESAI -->
                <div class="section-badge">
                    <div class="section-badge-icon" style="background: var(--success-subtle); color: var(--success);">
                        <i class="fa-solid fa-pen-fancy"></i>
                    </div>
                    <div>
                        <div class="section-badge-title">Bagian II: 5 Butir Soal Esai / Uraian</div>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">Ketik uraian jawaban secara runut dan lengkap di kotak teks</div>
                    </div>
                    <div class="section-badge-sub" style="color: var(--success);">Bobot: {{ $setting->bobot_esai }}%</div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 40px;">
                    @for($e = 1; $e <= $jumlahEsai; $e++)
                        @php
                            $nomorEsai = $jumlahPg + $e;
                            $currentEsai = $peserta->jawaban_esai[(string)$nomorEsai] ?? '';
                        @endphp
                        <div class="essay-item" id="q_esai_{{ $nomorEsai }}">
                            <div class="essay-header">
                                <span class="essay-title">
                                    <span style="background: var(--success-subtle); color: var(--success); font-family: var(--font-mono); padding: 2px 8px; border-radius: 6px; font-size: 0.85rem;">
                                        Soal No. {{ $nomorEsai }}
                                    </span>
                                    <span>Jawaban Esai {{ $e }}</span>
                                </span>
                                <span class="essay-counter" id="counter_{{ $nomorEsai }}">{{ strlen($currentEsai) }} Karakter</span>
                            </div>

                            <textarea name="jawaban_esai[{{ $nomorEsai }}]" 
                                      id="textarea_esai_{{ $nomorEsai }}"
                                      class="essay-textarea" 
                                      rows="4" 
                                      placeholder="Ketik uraian jawaban Anda untuk Soal Nomor {{ $nomorEsai }} di sini..."
                                      oninput="handleEsaiInput({{ $nomorEsai }})">{{ $currentEsai }}</textarea>
                        </div>
                    @endfor
                </div>

                <!-- FOOTER SUBMIT CALLOUT -->
                <div style="background: #ffffff; border: 1px solid var(--border); border-radius: 12px; padding: 24px; text-align: center;">
                    <h4 style="font-size: 1rem; font-weight: 800; color: #000000; margin-bottom: 6px;">Sudah Selesai Memeriksa Semua Jawaban?</h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 18px;">
                        Pastikan seluruh 30 soal Pilihan Ganda dan 5 soal Esai telah terjawab sebelum mengirim.
                    </p>
                    <button type="button" class="btn-finish" style="margin: 0 auto; padding: 12px 28px; font-size: 1rem;" onclick="openFinishModal()">
                        <i class="fa-solid fa-circle-check"></i> Kumpulkan & Selesaikan Ujian
                    </button>
                </div>

            </div>

        </div>

    </form>

    <!-- MODAL KONFIRMASI PENGIRIMAN -->
    <div id="finishModal" class="cbt-modal-backdrop">
        <div class="cbt-modal-card">
            <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--success-subtle); color: var(--success); font-size: 1.8rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
                <i class="fa-solid fa-cloud-arrow-up"></i>
            </div>
            
            <h3 style="font-size: 1.3rem; font-weight: 800; color: #000000; margin-bottom: 8px;">Kumpulkan Lembar Jawaban Ujian?</h3>
            <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 20px; line-height: 1.5;">
                Apakah Anda yakin ingin menyelesaikan ujian ini? Setelah dikirim, jawaban tidak dapat diubah kembali.
            </p>

            <!-- STAT REKAP -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; margin-bottom: 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; text-align: left; font-size: 0.85rem;">
                <div>
                    <span style="color: var(--text-muted);">Pilihan Ganda:</span>
                    <strong id="modalPgCount" style="display: block; color: var(--primary); font-size: 1.1rem;">0 / {{ $jumlahPg }}</strong>
                </div>
                <div>
                    <span style="color: var(--text-muted);">Soal Esai:</span>
                    <strong id="modalEsaiCount" style="display: block; color: var(--success); font-size: 1.1rem;">0 / {{ $jumlahEsai }}</strong>
                </div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: center;">
                <button type="button" onclick="closeFinishModal()" style="padding: 10px 20px; border: 1px solid #cbd5e1; background: #ffffff; color: #334155; border-radius: 8px; font-weight: 700; cursor: pointer;">
                    Kembali Periksa
                </button>
                <button type="button" onclick="submitFinalForm()" style="padding: 10px 24px; border: none; background: var(--success); color: #ffffff; border-radius: 8px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-check"></i> Ya, Kumpulkan Sekarang
                </button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT CBT ENGINE -->
    <script>
        const DRAFT_URL = "{{ route('ppdb.ujian.simpan_draft', $pendaftar->no_pendaftaran) }}";
        const TOTAL_PG = {{ $jumlahPg }};
        const TOTAL_ESAI = {{ $jumlahEsai }};
        let sisaDetik = {{ $sisaDetik }};
        let autoSaveTimer = null;
        let isSubmitting = false;

        // ══ 1. COUNTDOWN TIMER ══
        function updateTimer() {
            if (sisaDetik <= 0) {
                document.getElementById('timerDisplay').innerText = "00:00:00";
                autoSubmitWhenExpired();
                return;
            }

            const h = Math.floor(sisaDetik / 3600);
            const m = Math.floor((sisaDetik % 3600) / 60);
            const s = sisaDetik % 60;

            const format = (n) => String(n).padStart(2, '0');
            const display = document.getElementById('timerDisplay');
            display.innerText = `${format(h)}:${format(m)}:${format(s)}`;

            // Visual Warnings
            if (sisaDetik <= 180) { // Kurang dari 3 menit
                display.className = "cbt-timer-display danger";
            } else if (sisaDetik <= 600) { // Kurang dari 10 menit
                display.className = "cbt-timer-display warning";
            }

            sisaDetik--;
        }

        setInterval(updateTimer, 1000);
        updateTimer();

        function autoSubmitWhenExpired() {
            if (isSubmitting) return;
            isSubmitting = true;
            alert('Waktu pengerjaan ujian telah berakhir. Lembar jawaban Anda akan dikumpulkan secara otomatis.');
            document.getElementById('ujianForm').submit();
        }

        // ══ 2. INTERAKSI PG & ESAI ══
        function handlePgChange(number) {
            const navBtn = document.getElementById(`nav_pg_${number}`);
            if (navBtn) navBtn.classList.add('answered');
            updateProgress();
            triggerAutoSave();
        }

        function handleEsaiInput(number) {
            const textarea = document.getElementById(`textarea_esai_${number}`);
            const counter = document.getElementById(`counter_${number}`);
            const navBtn = document.getElementById(`nav_esai_${number}`);

            const len = textarea.value.trim().length;
            if (counter) counter.innerText = `${len} Karakter`;

            if (navBtn) {
                if (len > 0) {
                    navBtn.classList.add('answered');
                } else {
                    navBtn.classList.remove('answered');
                }
            }

            updateProgress();
            debouncedAutoSave();
        }

        function updateProgress() {
            let pgAnswered = 0;
            for (let i = 1; i <= TOTAL_PG; i++) {
                const checked = document.querySelector(`input[name="jawaban_pg[${i}]"]:checked`);
                if (checked) pgAnswered++;
            }

            let esaiAnswered = 0;
            for (let e = 1; e <= TOTAL_ESAI; e++) {
                const num = TOTAL_PG + e;
                const ta = document.getElementById(`textarea_esai_${num}`);
                if (ta && ta.value.trim().length > 0) esaiAnswered++;
            }

            const totalAnswered = pgAnswered + esaiAnswered;
            const maxTotal = TOTAL_PG + TOTAL_ESAI;

            const progressText = document.getElementById('progressText');
            if (progressText) progressText.innerText = `${totalAnswered} / ${maxTotal} Terisi`;

            const modalPg = document.getElementById('modalPgCount');
            if (modalPg) modalPg.innerText = `${pgAnswered} / ${TOTAL_PG}`;

            const modalEsai = document.getElementById('modalEsaiCount');
            if (modalEsai) modalEsai.innerText = `${esaiAnswered} / ${TOTAL_ESAI}`;
        }

        // ══ 3. AUTO-SAVE VIA AJAX ══
        function debouncedAutoSave() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(triggerAutoSave, 1500);
        }

        function triggerAutoSave() {
            const saveIndicator = document.getElementById('saveStatus');
            if (saveIndicator) {
                saveIndicator.innerHTML = '<i class="fa-solid fa-arrows-rotate fa-spin text-primary"></i> Menyimpan...';
            }

            const form = document.getElementById('ujianForm');
            const formData = new FormData(form);

            fetch(DRAFT_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (saveIndicator) {
                    saveIndicator.innerHTML = `<i class="fa-solid fa-cloud-check text-success"></i> Draft ${data.timestamp || 'Tersimpan'}`;
                }
            })
            .catch(err => {
                console.warn('Auto-save error:', err);
                if (saveIndicator) {
                    saveIndicator.innerHTML = '<i class="fa-solid fa-circle-exclamation text-danger"></i> Offline';
                }
            });
        }

        // Periodic auto-save every 20 seconds
        setInterval(triggerAutoSave, 20000);

        // ══ 4. MODAL FINISH ══
        function openFinishModal() {
            updateProgress();
            document.getElementById('finishModal').classList.add('active');
        }

        function closeFinishModal() {
            document.getElementById('finishModal').classList.remove('active');
        }

        function submitFinalForm() {
            if (isSubmitting) return;
            isSubmitting = true;
            document.getElementById('ujianForm').submit();
        }

        // Initial progress calculation
        updateProgress();
    </script>

</body>
</html>
