<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ujian CBT — {{ $pendaftar->nama_lengkap }} ({{ $pendaftar->no_pendaftaran }})</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">

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
            --border: #e2e8f0;
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-user-select: none;
            user-select: none;
        }

        /* ══ TOPBAR ══ */
        .cbt-topbar {
            position: sticky;
            top: 0;
            height: 64px;
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 50;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .cbt-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cbt-badge {
            padding: 6px 12px;
            border-radius: 8px;
            background: var(--primary-subtle);
            color: var(--primary);
            font-weight: 800;
            font-size: 0.85rem;
            letter-spacing: 0.02em;
        }

        .cbt-user-info {
            display: flex;
            flex-direction: column;
        }

        .cbt-user-name {
            font-weight: 800;
            font-size: 0.95rem;
            color: var(--text-dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 280px;
        }

        .cbt-user-meta {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        /* TIMER */
        .cbt-timer-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #0f172a;
            color: #ffffff;
            padding: 6px 18px;
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
            font-size: 1.2rem;
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
            transition: all 0.2s;
        }

        .btn-finish:hover {
            background: #047857;
            transform: translateY(-1px);
        }

        /* ══ MAIN LAYOUT ══ */
        .cbt-container {
            max-width: 1280px;
            width: 100%;
            margin: 0 auto;
            padding: 24px 20px 80px 20px;
            display: grid;
            grid-template-columns: 1fr 310px;
            gap: 24px;
            align-items: start;
        }

        /* SOAL STREAM */
        .cbt-questions-col {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* QUESTION CARD */
        .question-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 22px 24px;
            scroll-margin-top: 84px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .question-card:hover {
            border-color: #cbd5e1;
        }

        .q-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .q-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .q-badge-num {
            background: #0f172a;
            color: #ffffff;
            font-family: var(--font-mono);
            font-weight: 800;
            font-size: 0.85rem;
            padding: 3px 10px;
            border-radius: 6px;
        }

        .q-badge-type {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .q-text {
            font-size: 1rem;
            line-height: 1.6;
            color: #1e293b;
            font-weight: 600;
            margin-bottom: 18px;
            white-space: pre-line;
        }

        /* OPTIONS LIST */
        .options-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .opt-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            background: #fdfdfe;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .opt-row:hover {
            border-color: var(--primary);
            background: var(--primary-subtle);
        }

        .opt-row input[type="radio"] {
            margin-top: 3px;
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
            flex-shrink: 0;
        }

        .opt-letter {
            font-family: var(--font-mono);
            font-weight: 800;
            font-size: 0.95rem;
            color: var(--text-dark);
            width: 24px;
            flex-shrink: 0;
        }

        .opt-text {
            font-size: 0.92rem;
            line-height: 1.5;
            color: #334155;
            font-weight: 500;
            flex: 1;
        }

        .opt-row.selected {
            background: #eff6ff;
            border-color: var(--primary);
        }

        .opt-row.selected .opt-letter {
            color: var(--primary);
        }

        .opt-row.selected .opt-text {
            color: #1e3a8a;
            font-weight: 600;
        }

        /* TEXTAREA ESAI */
        .essay-textarea {
            width: 100%;
            min-height: 120px;
            padding: 14px;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-family: var(--font-main);
            font-size: 0.92rem;
            line-height: 1.6;
            color: #0f172a;
            background: #ffffff;
            resize: vertical;
            outline: none;
            transition: border-color 0.15s;
        }

        .essay-textarea:focus {
            border-color: var(--success);
            box-shadow: 0 0 0 3px rgba(5,150,105,0.1);
        }

        .essay-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        /* ══ SIDEBAR PETA SOAL ══ */
        .cbt-sidebar {
            position: sticky;
            top: 84px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .map-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .map-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .map-card-title {
            font-size: 0.85rem;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .map-progress {
            font-family: var(--font-mono);
            font-size: 0.82rem;
            font-weight: 800;
            color: var(--primary);
        }

        .map-section-label {
            font-size: 0.72rem;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 12px 0 6px;
        }

        .map-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 6px;
        }

        .map-btn {
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-family: var(--font-mono);
            font-size: 0.85rem;
            font-weight: 700;
            color: #1e293b;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
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
            color: #ffffff;
        }

        .map-legend {
            display: flex;
            gap: 12px;
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--text-muted);
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid #f1f5f9;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .legend-box {
            width: 12px;
            height: 12px;
            border-radius: 3px;
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
            border-radius: 14px;
            max-width: 460px;
            width: 100%;
            padding: 28px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: modalPop 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes modalPop {
            0% { transform: scale(0.92); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        @media (max-width: 992px) {
            .cbt-container {
                grid-template-columns: 1fr;
            }
            .cbt-sidebar {
                position: static;
                order: -1;
            }
        }
    </style>
</head>
<body>

    <!-- TOPBAR -->
    <header class="cbt-topbar">
        <div class="cbt-brand">
            <div class="cbt-badge">CBT SMKN 1 AN</div>
            <div class="cbt-user-info">
                <span class="cbt-user-name">{{ $pendaftar->nama_lengkap }}</span>
                <span class="cbt-user-meta">No. Reg: {{ $pendaftar->no_pendaftaran }} &bull; {{ $pendaftar->jurusanPilihan1->nama_jurusan ?? '-' }}</span>
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
                Draft tersimpan
            </div>
            <button type="button" class="btn-finish" onclick="openFinishModal()">
                Kumpulkan Ujian
            </button>
        </div>
    </header>

    <!-- FORM UTAMA UJIAN -->
    <form id="ujianForm" action="{{ route('ppdb.ujian.selesai', $pendaftar->no_pendaftaran) }}" method="POST">
        @csrf

        <div class="cbt-container">
            
            <!-- KOLOM DAFTAR SOAL -->
            <div class="cbt-questions-col">
                
                @if(count($soalPg) === 0 && count($soalEsai) === 0)
                    <div style="background: #ffffff; border: 1px solid var(--border); border-radius: 12px; padding: 40px; text-align: center; color: var(--text-muted);">
                        <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 6px;">Soal Ujian Belum Tersedia</h3>
                        <p style="font-size: 0.88rem; max-width: 440px; margin: 0 auto; line-height: 1.5;">
                            Panitia belum menambahkan butir soal ke dalam bank soal CBT. Silakan laporkan kepada pengawas ujian.
                        </p>
                    </div>
                @endif

                {{-- BAGIAN I: SOAL PILIHAN GANDA --}}
                @if(count($soalPg) > 0)
                    <div style="margin-bottom: 6px;">
                        <h2 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0 0 4px;">
                            Bagian I: Soal Pilihan Ganda ({{ count($soalPg) }} Butir)
                        </h2>
                        <div style="font-size: 0.82rem; color: var(--text-muted);">
                            Pilihlah salah satu opsi jawaban yang paling tepat. Bobot: {{ $setting->bobot_pg }}%.
                        </div>
                    </div>

                    @foreach($soalPg as $idx => $soal)
                        @php
                            $dispNum = $idx + 1;
                            $savedAnswer = $peserta->jawaban_pg[$soal->id] ?? $peserta->jawaban_pg[$soal->nomor_urut] ?? null;
                            $opsiArray = $soal->getOpsiArray();
                        @endphp
                        <div class="question-card" id="q_pg_{{ $dispNum }}">
                            <div class="q-header">
                                <div class="q-badge">
                                    <span class="q-badge-num">{{ $dispNum }}</span>
                                    <span class="q-badge-type">Pilihan Ganda</span>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">
                                    Soal {{ $dispNum }} dari {{ count($soalPg) }}
                                </div>
                            </div>

                            <div class="q-text">{!! nl2br(e($soal->pertanyaan)) !!}</div>

                            <div class="options-list">
                                @foreach(['A', 'B', 'C', 'D', 'E'] as $optKey)
                                    @if(isset($opsiArray[$optKey]))
                                        @php
                                            $isSelected = ($savedAnswer === $optKey);
                                        @endphp
                                        <label class="opt-row {{ $isSelected ? 'selected' : '' }}" id="row_opt_{{ $dispNum }}_{{ $optKey }}">
                                            <input type="radio" 
                                                   name="jawaban_pg[{{ $soal->id }}]" 
                                                   value="{{ $optKey }}"
                                                   data-disp="{{ $dispNum }}"
                                                   {{ $isSelected ? 'checked' : '' }}
                                                   onchange="handlePgSelect({{ $dispNum }}, '{{ $optKey }}')">
                                            <span class="opt-letter">{{ $optKey }}.</span>
                                            <span class="opt-text">{{ $opsiArray[$optKey] }}</span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- BAGIAN II: SOAL ESAI --}}
                @if(count($soalEsai) > 0)
                    <div style="margin-top: 16px; margin-bottom: 6px;">
                        <h2 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0 0 4px;">
                            Bagian II: Soal Esai / Uraian ({{ count($soalEsai) }} Butir)
                        </h2>
                        <div style="font-size: 0.82rem; color: var(--text-muted);">
                            Ketikkan uraian jawaban Anda secara jelas dan lengkap. Bobot: {{ $setting->bobot_esai }}%.
                        </div>
                    </div>

                    @foreach($soalEsai as $eIdx => $soal)
                        @php
                            $dispNumEsai = count($soalPg) + $eIdx + 1;
                            $savedEsai = $peserta->jawaban_esai[$soal->id] ?? $peserta->jawaban_esai[$soal->nomor_urut] ?? '';
                        @endphp
                        <div class="question-card" id="q_esai_{{ $dispNumEsai }}">
                            <div class="q-header">
                                <div class="q-badge">
                                    <span class="q-badge-num">{{ $dispNumEsai }}</span>
                                    <span class="q-badge-type">Esai / Uraian</span>
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">
                                    Esai {{ $eIdx + 1 }} dari {{ count($soalEsai) }}
                                </div>
                            </div>

                            <div class="q-text">{!! nl2br(e($soal->pertanyaan)) !!}</div>

                            <textarea name="jawaban_esai[{{ $soal->id }}]" 
                                      id="textarea_esai_{{ $dispNumEsai }}"
                                      class="essay-textarea" 
                                      placeholder="Tuliskan uraian jawaban Anda di sini..."
                                      oninput="handleEsaiInput({{ $dispNumEsai }})">{{ $savedEsai }}</textarea>

                            <div class="essay-footer">
                                <span id="counter_{{ $dispNumEsai }}">{{ strlen(trim($savedEsai)) }} Karakter</span>
                                <span>Jawaban tersimpan otomatis</span>
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- SUBMIT FOOTER --}}
                @if(count($soalPg) > 0 || count($soalEsai) > 0)
                    <div style="background: #ffffff; border: 1px solid var(--border); border-radius: 12px; padding: 24px; text-align: center; margin-top: 10px;">
                        <h4 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 6px;">Sudah Selesai Memeriksa Seluruh Jawaban?</h4>
                        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 18px;">
                            Pastikan Anda telah mengisi seluruh butir soal sebelum mengumpulkan ujian.
                        </p>
                        <button type="button" class="btn-finish" style="padding: 10px 24px; font-size: 0.95rem;" onclick="openFinishModal()">
                            Kumpulkan &amp; Selesaikan Ujian
                        </button>
                    </div>
                @endif

            </div>

            <!-- SIDEBAR: PETA NOMOR SOAL (STICKY) -->
            <div class="cbt-sidebar">
                <div class="map-card">
                    <div class="map-card-header">
                        <span class="map-card-title">Peta Soal</span>
                        <span id="progressText" class="map-progress">0 / {{ count($soalPg) + count($soalEsai) }}</span>
                    </div>

                    {{-- Map Pilihan Ganda --}}
                    @if(count($soalPg) > 0)
                        <div class="map-section-label">Pilihan Ganda</div>
                        <div class="map-grid">
                            @foreach($soalPg as $idx => $soal)
                                @php
                                    $dNum = $idx + 1;
                                    $isAnswered = !empty($peserta->jawaban_pg[$soal->id] ?? $peserta->jawaban_pg[$soal->nomor_urut] ?? null);
                                @endphp
                                <a href="#q_pg_{{ $dNum }}" id="nav_btn_{{ $dNum }}" class="map-btn {{ $isAnswered ? 'answered' : '' }}">
                                    {{ $dNum }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Map Esai --}}
                    @if(count($soalEsai) > 0)
                        <div class="map-section-label">Esai / Uraian</div>
                        <div class="map-grid">
                            @foreach($soalEsai as $eIdx => $soal)
                                @php
                                    $dNumEsai = count($soalPg) + $eIdx + 1;
                                    $isAnswered = !empty(trim($peserta->jawaban_esai[$soal->id] ?? $peserta->jawaban_esai[$soal->nomor_urut] ?? ''));
                                @endphp
                                <a href="#q_esai_{{ $dNumEsai }}" id="nav_btn_{{ $dNumEsai }}" class="map-btn esai-btn {{ $isAnswered ? 'answered' : '' }}">
                                    {{ $dNumEsai }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Legend --}}
                    <div class="map-legend">
                        <div class="legend-item">
                            <div class="legend-box" style="background: var(--primary);"></div>
                            <span>PG Terisi</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-box" style="background: var(--success);"></div>
                            <span>Esai Terisi</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-box" style="background: #ffffff; border: 1px solid #cbd5e1;"></div>
                            <span>Belum</span>
                        </div>
                    </div>
                </div>

                {{-- Petunjuk Singkat --}}
                <div style="background: #ffffff; border: 1px solid var(--border); border-radius: 12px; padding: 16px; font-size: 0.8rem; color: var(--text-muted); line-height: 1.5;">
                    <div style="font-weight: 800; color: #0f172a; margin-bottom: 4px;">Informasi Ujian:</div>
                    Urutan soal teracak otomatis. Jawaban tersimpan otomatis ke server. Anda dapat berpindah soal dengan mengklik nomor di peta soal.
                </div>
            </div>

        </div>
    </form>

    <!-- MODAL KONFIRMASI PENGIRIMAN -->
    <div id="finishModal" class="cbt-modal-backdrop">
        <div class="cbt-modal-card">
            <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 8px;">Kumpulkan Lembar Jawaban?</h3>
            <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 20px; line-height: 1.5;">
                Setelah dikirim, seluruh jawaban Anda akan terkunci dan langsung dinilai oleh sistem.
            </p>

            <!-- STAT REKAP -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; margin-bottom: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; text-align: left; font-size: 0.85rem;">
                <div>
                    <span style="color: var(--text-muted);">Pilihan Ganda:</span>
                    <strong id="modalPgCount" style="display: block; color: var(--primary); font-size: 1.1rem; margin-top: 2px;">0 / {{ count($soalPg) }}</strong>
                </div>
                <div>
                    <span style="color: var(--text-muted);">Soal Esai:</span>
                    <strong id="modalEsaiCount" style="display: block; color: var(--success); font-size: 1.1rem; margin-top: 2px;">0 / {{ count($soalEsai) }}</strong>
                </div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: center;">
                <button type="button" onclick="closeFinishModal()" style="padding: 10px 18px; border: 1px solid #cbd5e1; background: #ffffff; color: #334155; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 0.88rem;">
                    Periksa Kembali
                </button>
                <button type="button" onclick="submitFinalForm()" style="padding: 10px 22px; border: none; background: var(--success); color: #ffffff; border-radius: 8px; font-weight: 800; cursor: pointer; font-size: 0.88rem;">
                    Ya, Kumpulkan Sekarang
                </button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT CBT ENGINE -->
    <script>
        const DRAFT_URL = "{{ route('ppdb.ujian.simpan_draft', $pendaftar->no_pendaftaran) }}";
        const TOTAL_PG = {{ count($soalPg) }};
        const TOTAL_ESAI = {{ count($soalEsai) }};
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
            if (sisaDetik <= 180) {
                display.className = "cbt-timer-display danger";
            } else if (sisaDetik <= 600) {
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

        // ══ 2. INTERAKSI PG ══
        function handlePgSelect(dispNum, optKey) {
            // Update UI highlight
            ['A', 'B', 'C', 'D', 'E'].forEach(letter => {
                const row = document.getElementById(`row_opt_${dispNum}_${letter}`);
                if (row) {
                    if (letter === optKey) {
                        row.classList.add('selected');
                    } else {
                        row.classList.remove('selected');
                    }
                }
            });

            // Update navigator button
            const navBtn = document.getElementById(`nav_btn_${dispNum}`);
            if (navBtn) navBtn.classList.add('answered');

            updateProgress();
            triggerAutoSave();
        }

        // ══ 3. INTERAKSI ESAI ══
        function handleEsaiInput(dispNum) {
            const ta = document.getElementById(`textarea_esai_${dispNum}`);
            const counter = document.getElementById(`counter_${dispNum}`);
            const navBtn = document.getElementById(`nav_btn_${dispNum}`);

            const len = ta ? ta.value.trim().length : 0;
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

        // ══ 4. UPDATE PROGRESS ══
        function updateProgress() {
            let pgAnswered = 0;
            const pgInputs = document.querySelectorAll('input[type="radio"]:checked');
            pgAnswered = pgInputs.length;

            let esaiAnswered = 0;
            for (let i = 1; i <= TOTAL_ESAI; i++) {
                const dispNum = TOTAL_PG + i;
                const ta = document.getElementById(`textarea_esai_${dispNum}`);
                if (ta && ta.value.trim().length > 0) {
                    esaiAnswered++;
                }
            }

            const totalAnswered = pgAnswered + esaiAnswered;
            const totalSoal = TOTAL_PG + TOTAL_ESAI;

            const progressElem = document.getElementById('progressText');
            if (progressElem) {
                progressElem.innerText = `${totalAnswered} / ${totalSoal}`;
            }

            const mPg = document.getElementById('modalPgCount');
            if (mPg) mPg.innerText = `${pgAnswered} / ${TOTAL_PG}`;

            const mEsai = document.getElementById('modalEsaiCount');
            if (mEsai) mEsai.innerText = `${esaiAnswered} / ${TOTAL_ESAI}`;
        }

        // ══ 5. AUTO SAVE ENGINE ══
        function debouncedAutoSave() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(triggerAutoSave, 1200);
        }

        function triggerAutoSave() {
            const form = document.getElementById('ujianForm');
            const formData = new FormData(form);

            // Serialize data
            const payload = {
                jawaban_pg: {},
                jawaban_esai: {}
            };

            for (let [key, val] of formData.entries()) {
                const matchPg = key.match(/^jawaban_pg\[(\d+)\]$/);
                if (matchPg) {
                    payload.jawaban_pg[matchPg[1]] = val;
                }
                const matchEsai = key.match(/^jawaban_esai\[(\d+)\]$/);
                if (matchEsai) {
                    payload.jawaban_esai[matchEsai[1]] = val;
                }
            }

            const statusElem = document.getElementById('saveStatus');
            if (statusElem) statusElem.innerText = "Menyimpan...";

            fetch(DRAFT_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (statusElem) {
                    statusElem.innerText = "Draft tersimpan (" + (data.timestamp || 'otomatis') + ")";
                }
            })
            .catch(() => {
                if (statusElem) statusElem.innerText = "Gagal simpan (koneksi)";
            });
        }

        // ══ 6. MODAL SELESAI ══
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

        // Jalankan initial progress count
        updateProgress();
    </script>
</body>
</html>
