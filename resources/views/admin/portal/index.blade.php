<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Digital Command Center — SMKN 1 AN</title>
  @include('partials.styles')
  <style>
    :root {
      --dcc-bg: var(--bg);
      --dcc-card-bg: #ffffff;
      --dcc-card-border: rgba(15, 23, 42, 0.08);
      --dcc-card-hover: 0 20px 35px -10px rgba(15, 23, 42, 0.08), 0 0 1px 1px rgba(15, 23, 42, 0.04);
      --dcc-telemetry-bg: #f8fafc;
      --dcc-telemetry-border: #e2e8f0;
      --dcc-text-sub: #64748b;
    }

    [data-theme="dark"] {
      --dcc-bg: #090d16;
      --dcc-card-bg: #111827;
      --dcc-card-border: rgba(255, 255, 255, 0.08);
      --dcc-card-hover: 0 24px 45px -12px rgba(0, 0, 0, 0.6), 0 0 1px 1px rgba(255, 255, 255, 0.1);
      --dcc-telemetry-bg: rgba(255, 255, 255, 0.03);
      --dcc-telemetry-border: rgba(255, 255, 255, 0.06);
      --dcc-text-sub: #94a3b8;
    }

    body {
      background: var(--dcc-bg);
      background-image: 
        radial-gradient(circle at 10% 5%, rgba(56, 189, 248, 0.08), transparent 30%),
        radial-gradient(circle at 90% 15%, rgba(99, 102, 241, 0.08), transparent 35%);
      color: var(--text);
      font-family: var(--font-sans);
      margin: 0;
      padding: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      -webkit-font-smoothing: antialiased;
    }

    /* ─── Topbar Glassmorphic ─── */
    .portal-topbar {
      background: rgba(255, 255, 255, 0.85);
      border-bottom: 1px solid var(--border);
      position: sticky;
      top: 0;
      z-index: 100;
      backdrop-filter: blur(18px) saturate(180%);
      -webkit-backdrop-filter: blur(18px) saturate(180%);
      padding: 10px 28px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
    }

    [data-theme="dark"] .portal-topbar {
      background: rgba(9, 13, 22, 0.82);
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .portal-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      color: var(--text);
    }

    .portal-brand-logo-wrap {
      position: relative;
    }

    .portal-brand-logo {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      background: linear-gradient(135deg, #1e293b, #0f172a);
      border: 1px solid rgba(255, 255, 255, 0.15);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 5px;
      box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
    }

    .portal-brand-title {
      font-size: 16px;
      font-weight: 900;
      letter-spacing: -0.03em;
      line-height: 1.15;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .portal-brand-badge {
      font-size: 9.5px;
      font-weight: 800;
      letter-spacing: 0.05em;
      padding: 2px 6px;
      border-radius: 4px;
      background: rgba(2, 132, 199, 0.12);
      color: #0284c7;
      border: 1px solid rgba(2, 132, 199, 0.25);
    }

    .portal-brand-subtitle {
      font-size: 11px;
      color: var(--dcc-text-sub);
      font-weight: 600;
      letter-spacing: 0.01em;
      margin-top: 1px;
    }

    .portal-top-right {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .portal-status-beacon {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 700;
      background: rgba(16, 185, 129, 0.1);
      color: #10b981;
      border: 1px solid rgba(16, 185, 129, 0.25);
    }

    .pulse-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: #10b981;
      box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
      animation: pulseGreen 2s infinite;
    }

    @keyframes pulseGreen {
      0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); }
      70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
      100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    /* ─── Main Container ─── */
    .portal-container {
      max-width: 1320px;
      width: 100%;
      margin: 0 auto;
      padding: 24px 24px 60px;
      flex: 1;
      box-sizing: border-box;
    }

    /* ─── Hero Cockpit / Mission Control Banner ─── */
    .cockpit-hero {
      position: relative;
      border-radius: 24px;
      background: linear-gradient(135deg, #090d16 0%, #0f172a 45%, #1e1b4b 100%);
      border: 1px solid rgba(255, 255, 255, 0.12);
      box-shadow: 0 20px 45px -15px rgba(15, 23, 42, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.15);
      padding: 32px 36px;
      color: #ffffff;
      overflow: hidden;
      margin-bottom: 32px;
      display: grid;
      grid-template-columns: 1.4fr 1fr;
      gap: 32px;
      align-items: center;
    }

    .cockpit-hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image: radial-gradient(rgba(255, 255, 255, 0.07) 1px, transparent 1px);
      background-size: 20px 20px;
      opacity: 0.6;
      pointer-events: none;
    }

    .cockpit-hero::after {
      content: '';
      position: absolute;
      right: -80px;
      bottom: -80px;
      width: 320px;
      height: 320px;
      background: radial-gradient(circle, rgba(56, 189, 248, 0.2) 0%, rgba(99, 102, 241, 0.05) 50%, transparent 70%);
      border-radius: 50%;
      pointer-events: none;
    }

    .portal-hero-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.18);
      padding: 4px 14px;
      border-radius: 30px;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.05em;
      color: #38bdf8;
      margin-bottom: 12px;
    }

    .cockpit-title {
      font-size: 26px;
      font-weight: 900;
      letter-spacing: -0.03em;
      line-height: 1.25;
      margin: 0 0 10px;
    }

    .cockpit-desc {
      font-size: 13.5px;
      color: #cbd5e1;
      line-height: 1.55;
      margin: 0 0 18px;
    }

    .cockpit-meta-row {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }

    .cockpit-clock-badge {
      font-family: var(--font-mono);
      font-size: 11.5px;
      font-weight: 700;
      background: rgba(0, 0, 0, 0.35);
      border: 1px solid rgba(255, 255, 255, 0.1);
      padding: 5px 12px;
      border-radius: 8px;
      color: #e2e8f0;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    /* Right Visual Showcase Banner */
    .cockpit-banner-frame {
      position: relative;
      border-radius: 18px;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.16);
      box-shadow: 0 16px 36px -10px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.2);
      aspect-ratio: 16 / 9;
      max-height: 240px;
      width: 100%;
      background: #0f172a;
    }

    .cockpit-banner-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .cockpit-banner-frame:hover .cockpit-banner-img {
      transform: scale(1.05);
    }

    .cockpit-banner-scrim {
      position: absolute;
      inset: 0;
      background: linear-gradient(180deg, rgba(9, 13, 22, 0.15) 0%, rgba(9, 13, 22, 0.82) 100%);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 14px 16px;
      pointer-events: none;
    }

    .cockpit-banner-top-pill {
      align-self: flex-end;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(15, 23, 42, 0.75);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #38bdf8;
      font-size: 10px;
      font-weight: 800;
      padding: 4px 10px;
      border-radius: 20px;
      letter-spacing: 0.04em;
    }

    .cockpit-banner-caption {
      color: #ffffff;
    }

    .cockpit-banner-title {
      font-size: 13.5px;
      font-weight: 900;
      letter-spacing: -0.01em;
      margin-bottom: 2px;
      display: flex;
      align-items: center;
      gap: 6px;
      color: #ffffff;
    }

    .cockpit-banner-sub {
      font-size: 11px;
      color: #cbd5e1;
      font-weight: 500;
    }

    /* ─── Section Header ─── */
    .portal-section-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin: 32px 0 18px;
    }

    .portal-section-title {
      font-size: 17px;
      font-weight: 900;
      letter-spacing: -0.02em;
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 0;
    }

    .portal-section-title-icon {
      width: 28px;
      height: 28px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
    }

    .portal-section-badge {
      font-size: 11px;
      font-weight: 800;
      padding: 4px 10px;
      border-radius: 20px;
      background: var(--dcc-telemetry-bg);
      border: 1px solid var(--dcc-telemetry-border);
      color: var(--text-2);
      letter-spacing: 0.02em;
    }

    /* ─── Active Module Grid (The 3 Core Modules) ─── */
    .portal-active-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 24px;
    }

    .module-card {
      background: var(--dcc-card-bg);
      border: 1px solid var(--dcc-card-border);
      border-radius: 22px;
      padding: 24px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      position: relative;
      overflow: hidden;
    }

    .module-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: transparent;
      transition: background 0.25s ease;
    }

    .module-card.card-sirani::before {
      background: linear-gradient(90deg, #10b981, #059669);
    }

    .module-card.card-ppdb::before {
      background: linear-gradient(90deg, #f59e0b, #d97706);
    }

    .module-card.card-web::before {
      background: linear-gradient(90deg, #6366f1, #4f46e5);
    }

    .module-card:hover {
      transform: translateY(-4px);
      box-shadow: var(--dcc-card-hover);
    }

    .module-card.card-sirani:hover {
      border-color: rgba(16, 185, 129, 0.4);
    }

    .module-card.card-ppdb:hover {
      border-color: rgba(217, 119, 6, 0.4);
    }

    .module-card.card-web:hover {
      border-color: rgba(99, 102, 241, 0.4);
    }

    .module-card-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
    }

    .module-card-icon-halo {
      width: 50px;
      height: 50px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.06);
    }

    .module-live-pill {
      font-size: 11px;
      font-weight: 800;
      padding: 3px 10px;
      border-radius: 20px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      letter-spacing: 0.02em;
    }

    .access-badge {
      font-size: 10.5px;
      font-weight: 800;
      padding: 3px 8px;
      border-radius: 8px;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      letter-spacing: 0.02em;
    }

    .access-badge.allowed {
      background: rgba(16, 185, 129, 0.12);
      color: #10b981;
      border: 1px solid rgba(16, 185, 129, 0.25);
    }

    .access-badge.locked {
      background: rgba(239, 68, 68, 0.1);
      color: #ef4444;
      border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .module-card.is-locked {
      background: rgba(15, 23, 42, 0.02);
      border-style: dashed;
      border-color: rgba(148, 163, 184, 0.35);
    }

    .dark .module-card.is-locked {
      background: rgba(15, 23, 42, 0.4);
      border-color: rgba(255, 255, 255, 0.1);
    }

    .btn-launch-primary.btn-locked {
      background: #334155 !important;
      color: #94a3b8 !important;
      cursor: not-allowed !important;
      border: 1px dashed rgba(255, 255, 255, 0.12) !important;
      box-shadow: none !important;
      transform: none !important;
      filter: none !important;
    }

    .btn-launch-secondary.btn-locked {
      opacity: 0.45;
      cursor: not-allowed;
      pointer-events: none;
      border-style: dashed;
    }

    .module-card-name {
      font-size: 18px;
      font-weight: 900;
      margin: 0 0 3px;
      letter-spacing: -0.02em;
      color: var(--text);
    }

    .module-card-subtitle {
      font-size: 12px;
      color: var(--dcc-text-sub);
      font-weight: 600;
      margin: 0 0 18px;
    }

    /* Telemetry KPI Boxes */
    .kpi-row {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      background: var(--dcc-telemetry-bg);
      border: 1px solid var(--dcc-telemetry-border);
      border-radius: 14px;
      padding: 12px 14px;
      margin-bottom: 20px;
    }

    .kpi-item {
      display: flex;
      flex-direction: column;
    }

    .kpi-label {
      font-size: 10px;
      font-weight: 800;
      color: var(--dcc-text-sub);
      text-transform: uppercase;
      letter-spacing: 0.04em;
      margin-bottom: 3px;
    }

    .kpi-val {
      font-size: 20px;
      font-weight: 900;
      color: var(--text);
      font-family: var(--font-mono);
      line-height: 1.15;
    }

    .kpi-sub {
      font-size: 10.5px;
      color: var(--dcc-text-sub);
      margin-top: 2px;
    }

    /* Actions Deck */
    .module-actions {
      display: flex;
      flex-direction: column;
      gap: 8px;
      margin-top: auto;
    }

    .btn-launch-primary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      height: 42px;
      border-radius: 12px;
      font-size: 13px;
      font-weight: 800;
      text-decoration: none;
      color: #ffffff;
      border: none;
      cursor: pointer;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
      transition: all 0.2s ease;
    }

    .btn-launch-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.16);
      filter: brightness(1.05);
    }

    .btn-launch-secondary-row {
      display: flex;
      gap: 8px;
    }

    .btn-launch-secondary {
      flex: 1;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      height: 34px;
      border-radius: 10px;
      font-size: 11.5px;
      font-weight: 700;
      text-decoration: none;
      background: var(--dcc-card-bg);
      border: 1px solid var(--dcc-telemetry-border);
      color: var(--text-2);
      transition: all 0.15s ease;
    }

    .btn-launch-secondary:hover {
      background: var(--dcc-telemetry-bg);
      color: var(--text);
      border-color: var(--border-2);
    }

    /* ─── Future Roadmap Blueprint Cards ─── */
    .portal-roadmap-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 16px;
    }

    .roadmap-card {
      background: var(--dcc-card-bg);
      border: 1px solid var(--dcc-card-border);
      border-radius: 18px;
      padding: 18px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
      transition: all 0.2s ease;
    }

    .roadmap-card:hover {
      border-color: rgba(2, 132, 199, 0.4);
      transform: translateY(-3px);
      box-shadow: var(--dcc-card-hover);
    }

    .roadmap-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 12px;
    }

    .roadmap-icon-wrap {
      width: 40px;
      height: 40px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 19px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
    }

    .roadmap-badge {
      font-size: 9.5px;
      font-weight: 800;
      letter-spacing: 0.04em;
      padding: 3px 8px;
      border-radius: 12px;
      background: rgba(245, 158, 11, 0.12);
      color: #d97706;
      border: 1px solid rgba(245, 158, 11, 0.25);
    }

    .roadmap-title {
      font-size: 14.5px;
      font-weight: 900;
      margin: 0 0 2px;
      color: var(--text);
      letter-spacing: -0.01em;
    }

    .roadmap-sub {
      font-size: 11px;
      font-weight: 600;
      color: var(--dcc-text-sub);
      margin: 0 0 10px;
    }

    .roadmap-desc {
      font-size: 11.5px;
      line-height: 1.5;
      color: var(--text-2);
      margin: 0 0 14px;
      flex: 1;
    }

    .roadmap-progress-wrap {
      margin-bottom: 12px;
    }

    .roadmap-progress-bar {
      width: 100%;
      height: 4px;
      border-radius: 2px;
      background: var(--dcc-telemetry-border);
      overflow: hidden;
    }

    .roadmap-progress-fill {
      height: 100%;
      border-radius: 2px;
      background: linear-gradient(90deg, #0284c7, #38bdf8);
    }

    .roadmap-lead {
      font-size: 10px;
      font-weight: 700;
      color: var(--dcc-text-sub);
      background: var(--dcc-telemetry-bg);
      padding: 6px 10px;
      border-radius: 8px;
      border: 1px solid var(--dcc-telemetry-border);
      display: flex;
      align-items: center;
      gap: 6px;
    }

    /* ─── Bottom Infrastructure Console Strip ─── */
    .system-strip {
      margin-top: 36px;
      background: var(--dcc-card-bg);
      border: 1px solid var(--dcc-card-border);
      border-radius: 16px;
      padding: 16px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 14px;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.02);
    }

    .system-strip-left {
      display: flex;
      align-items: center;
      gap: 16px;
      font-size: 12px;
      color: var(--text-2);
      flex-wrap: wrap;
    }

    .system-strip-links {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .system-strip-link {
      font-size: 11.5px;
      font-weight: 800;
      color: var(--text-2);
      text-decoration: none;
      padding: 6px 12px;
      border-radius: 8px;
      background: var(--dcc-telemetry-bg);
      border: 1px solid var(--dcc-telemetry-border);
      transition: all 0.15s ease;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .system-strip-link:hover {
      color: var(--text);
      border-color: var(--border-2);
      transform: translateY(-1px);
    }

    /* Responsive */
    @media (max-width: 1200px) {
      .portal-roadmap-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    @media (max-width: 1024px) {
      .cockpit-hero {
        grid-template-columns: 1fr;
      }
      .portal-active-grid {
        grid-template-columns: 1fr;
      }
      .portal-roadmap-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 768px) {
      .portal-topbar {
        padding: 10px 16px;
      }
      .portal-container {
        padding: 16px 14px 40px;
      }
      .cockpit-hero {
        padding: 24px 20px;
      }
      .cockpit-title {
        font-size: 21px;
      }
      .portal-roadmap-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  {{-- Universal Top Navigation Bar --}}
  <header class="portal-topbar">
    <a href="{{ route('admin.portal') }}" class="portal-brand">
      <div class="portal-brand-logo-wrap">
        <div class="portal-brand-logo">
          <img src="/img/logo.png" alt="SMKN 1 AN" style="width:100%; height:100%; object-fit:contain;" />
        </div>
      </div>
      <div>
        <div class="portal-brand-title">
          <span>DCC SMKN 1 AN</span>
          <span class="portal-brand-badge">PRO v2.0</span>
        </div>
        <div class="portal-brand-subtitle">Digital Command Center · SMKN 1 Air Naningan</div>
      </div>
    </a>

    <div class="portal-top-right">
      <div class="portal-status-beacon" title="Koneksi seluruh modul & database normal">
        <span class="pulse-dot"></span>
        <span>SISTEM NORMAL</span>
      </div>
      @include('partials.header_actions')
    </div>
  </header>

  <main class="portal-container">

    {{-- Hero Cockpit / Mission Control Banner --}}
    <div class="cockpit-hero">
      <div>
        <div class="portal-hero-pill">
          <i class="bi bi-cpu-fill"></i> DCC · DIGITAL COMMAND CENTER
        </div>
        <h1 class="cockpit-title">
          Selamat Bertugas, {{ auth()->user()?->name ?? 'Administrator' }}
        </h1>
        <p class="cockpit-desc">
          Pintu gerbang manajemen terpadu SMKN 1 Air Naningan. Pilih modul operasional untuk mengelola absensi cerdas (SIRANI), seleksi siswa baru (PPDB 2026), maupun publikasi informasi publik (Web Profil).
        </p>
        <div class="cockpit-meta-row">
          <div class="cockpit-clock-badge" id="portalLiveClock">
            <i class="bi bi-clock-history" style="color:#38bdf8;"></i> Memuat waktu sistem...
          </div>
          <div class="cockpit-clock-badge" style="background:rgba(255,255,255,0.06);">
            <i class="bi bi-person-badge" style="color:#a78bfa;"></i> {{ auth()->user()?->role_display_name ?? 'Super Administrator' }}
          </div>
        </div>
      </div>

      {{-- Right: High-Tech Command Center Showcase Banner --}}
      <div class="cockpit-banner-frame">
        <img src="/images/web/dcc_command_center_banner.jpg" alt="DCC SMKN 1 AN Control Room" class="cockpit-banner-img" onerror="this.src='/images/web/hero_kampus.jpg';" />
        <div class="cockpit-banner-scrim">
          <div class="cockpit-banner-top-pill">
            <span class="pulse-dot" style="width:6px; height:6px;"></span>
            <span>DCC COMMAND CENTER</span>
          </div>
          <div class="cockpit-banner-caption">
            <div class="cockpit-banner-title">
              <i class="bi bi-shield-check" style="color:#38bdf8;"></i> Integrated Smart Campus
            </div>
            <div class="cockpit-banner-sub">
              Pusat Komando Vokasi Digital 4.0 · SMKN 1 Air Naningan
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Section 1: Modul Operasional Aktif --}}
    <div class="portal-section-head">
      <h2 class="portal-section-title">
        <div class="portal-section-title-icon" style="background:rgba(37,99,235,0.12); color:#2563eb;">
          <i class="bi bi-boxes"></i>
        </div>
        <span>Modul Sistem Aktif</span>
      </h2>
      <span class="portal-section-badge">3 Modul Siap Digunakan</span>
    </div>

    <div class="portal-active-grid">
      
      {{-- Card 1: SIRANI --}}
      <div class="module-card card-sirani {{ $canAccessSirani ? '' : 'is-locked' }}">
        <div>
          <div class="module-card-top">
            <div class="module-card-icon-halo" style="background:linear-gradient(135deg, rgba(16,185,129,0.2), rgba(5,150,105,0.08)); color:#10b981; border:1px solid rgba(16,185,129,0.25);">
              <i class="bi bi-fingerprint"></i>
            </div>
            <div style="display:flex; align-items:center; gap:6px;">
              @if($canAccessSirani)
                <span class="access-badge allowed"><i class="bi bi-shield-check"></i> Izin Aktif</span>
              @else
                <span class="access-badge locked"><i class="bi bi-lock-fill"></i> Akses Terbatas</span>
              @endif
              <span class="module-live-pill" style="background:rgba(16,185,129,0.1); color:#10b981; border:1px solid rgba(16,185,129,0.25);">
                <span class="pulse-dot"></span> Aktif
              </span>
            </div>
          </div>

          <h3 class="module-card-name">SIRANI</h3>
          <p class="module-card-subtitle">Sistem Absensi &amp; Ketertiban Siswa/Guru</p>

          <div class="kpi-row">
            <div class="kpi-item">
              <span class="kpi-label">Hadir Siswa Hari Ini</span>
              <span class="kpi-val" style="color:#10b981;">{{ $persenSiswaHadir }}%</span>
              <span class="kpi-sub">{{ $siswaHadirToday }} / {{ $totalSiswa }} Siswa</span>
            </div>
            <div class="kpi-item">
              <span class="kpi-label">Hadir Guru &amp; Pegawai</span>
              <span class="kpi-val" style="color:#2563eb;">{{ $guruHadirToday }}</span>
              <span class="kpi-sub">Dari {{ $totalGuru }} Guru Aktif</span>
            </div>
            <div class="kpi-item" style="margin-top:6px;">
              <span class="kpi-label">Kasus Disiplin Aktif</span>
              <span class="kpi-val" style="color:{{ $kasusDisiplinAktif > 0 ? '#ef4444' : '#10b981' }};">{{ $kasusDisiplinAktif }}</span>
              <span class="kpi-sub">Dalam Pembinaan</span>
            </div>
            <div class="kpi-item" style="margin-top:6px;">
              <span class="kpi-label">Smart Gate Gerbang</span>
              <span class="kpi-val" style="font-size:14px; color:{{ $isGerbangAktif ? '#10b981' : '#64748b' }};">
                {{ $isGerbangAktif ? 'ONLINE' : 'STANDBY' }}
              </span>
              <span class="kpi-sub">{{ $isGerbangAktif ? 'Sesi Presensi Buka' : 'Di Luar Jam Sesi' }}</span>
            </div>
          </div>
        </div>

        <div class="module-actions">
          @if($canAccessSirani)
            <a href="/dashboard" class="btn-launch-primary" style="background:linear-gradient(135deg, #10b981 0%, #059669 100%);">
              <i class="bi bi-speedometer2"></i> Buka Modul SIRANI <i class="bi bi-arrow-right-short" style="font-size:18px;"></i>
            </a>
            <div class="btn-launch-secondary-row">
              <a href="/smart-gate" target="_blank" class="btn-launch-secondary">
                <i class="bi bi-upc-scan" style="color:#10b981;"></i> Smart Gate
              </a>
              <a href="/laporan" class="btn-launch-secondary">
                <i class="bi bi-file-earmark-bar-graph" style="color:#0284c7;"></i> Laporan
              </a>
            </div>
          @else
            <button type="button" class="btn-launch-primary btn-locked" disabled title="Akses ditolak: Anda tidak memiliki wewenang untuk membuka Modul SIRANI">
              <i class="bi bi-lock-fill"></i> Akses Terbatas (Khusus Pendidik/Staf)
            </button>
            <div class="btn-launch-secondary-row">
              <span class="btn-launch-secondary btn-locked"><i class="bi bi-lock"></i> Smart Gate Terkunci</span>
              <span class="btn-launch-secondary btn-locked"><i class="bi bi-lock"></i> Laporan Terkunci</span>
            </div>
          @endif
        </div>
      </div>

      {{-- Card 2: PPDB ONLINE 2026 --}}
      <div class="module-card card-ppdb {{ $canAccessPpdb ? '' : 'is-locked' }}">
        <div>
          <div class="module-card-top">
            <div class="module-card-icon-halo" style="background:linear-gradient(135deg, rgba(245,158,11,0.2), rgba(217,119,6,0.08)); color:#d97706; border:1px solid rgba(245,158,11,0.25);">
              <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div style="display:flex; align-items:center; gap:6px;">
              @if($canAccessPpdb)
                <span class="access-badge allowed"><i class="bi bi-shield-check"></i> Izin Aktif</span>
              @else
                <span class="access-badge locked"><i class="bi bi-lock-fill"></i> Butuh Panitia</span>
              @endif
              <span class="module-live-pill" style="background:rgba(245,158,11,0.1); color:#d97706; border:1px solid rgba(245,158,11,0.25);">
                <span class="pulse-dot" style="background:#d97706; box-shadow:0 0 0 0 rgba(217,119,6,0.6);"></span> Aktif 2026
              </span>
            </div>
          </div>

          <h3 class="module-card-name">PPDB ONLINE 2026</h3>
          <p class="module-card-subtitle">Penerimaan Peserta Didik Baru Terpadu</p>

          <div class="kpi-row">
            <div class="kpi-item">
              <span class="kpi-label">Total Pendaftar</span>
              <span class="kpi-val" style="color:#d97706;">{{ $totalPendaftar }}</span>
              <span class="kpi-sub">+{{ $ppdbToday }} Pendaftar Hari Ini</span>
            </div>
            <div class="kpi-item">
              <span class="kpi-label">Menunggu Verifikasi</span>
              <span class="kpi-val" style="color:#ef4444;">{{ $ppdbMenunggu }}</span>
              <span class="kpi-sub">Perlu Dicek Panitia</span>
            </div>
            <div class="kpi-item" style="margin-top:6px;">
              <span class="kpi-label">Lolos / Diterima</span>
              <span class="kpi-val" style="color:#10b981;">{{ $ppdbDiterima }}</span>
              <span class="kpi-sub">Calon Siswa Resmi</span>
            </div>
            <div class="kpi-item" style="margin-top:6px;">
              <span class="kpi-label">Ditolak / Perbaikan</span>
              <span class="kpi-val" style="color:#64748b;">{{ $ppdbDitolak }}</span>
              <span class="kpi-sub">Berkas Tidak Sesuai</span>
            </div>
          </div>
        </div>

        <div class="module-actions">
          @if($canAccessPpdb)
            <a href="/admin/ppdb" class="btn-launch-primary" style="background:linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
              <i class="bi bi-people-fill"></i> Kelola PPDB 2026 <i class="bi bi-arrow-right-short" style="font-size:18px;"></i>
            </a>
            <div class="btn-launch-secondary-row">
              <a href="/admin/ppdb?status=menunggu" class="btn-launch-secondary">
                <i class="bi bi-clock" style="color:#d97706;"></i> Verifikasi Berkas
              </a>
              <a href="/ppdb" target="_blank" class="btn-launch-secondary">
                <i class="bi bi-box-arrow-up-right" style="color:#2563eb;"></i> Form Publik
              </a>
            </div>
          @else
            <button type="button" class="btn-launch-primary btn-locked" disabled title="Akses ditolak: Hanya untuk Panitia PPDB, Waka Kesiswaan, & Pimpinan">
              <i class="bi bi-lock-fill"></i> Akses Terbatas (Khusus Panitia PPDB)
            </button>
            <div class="btn-launch-secondary-row">
              <span class="btn-launch-secondary btn-locked"><i class="bi bi-lock"></i> Verifikasi Terkunci</span>
              <a href="/ppdb" target="_blank" class="btn-launch-secondary">
                <i class="bi bi-box-arrow-up-right" style="color:#2563eb;"></i> Form Publik
              </a>
            </div>
          @endif
        </div>
      </div>

      {{-- Card 3: WEB PROFIL & HUMAS --}}
      <div class="module-card card-web {{ $canAccessWeb ? '' : 'is-locked' }}">
        <div>
          <div class="module-card-top">
            <div class="module-card-icon-halo" style="background:linear-gradient(135deg, rgba(99,102,241,0.2), rgba(79,70,229,0.08)); color:#6366f1; border:1px solid rgba(99,102,241,0.25);">
              <i class="bi bi-globe-americas"></i>
            </div>
            <div style="display:flex; align-items:center; gap:6px;">
              @if($canAccessWeb)
                <span class="access-badge allowed"><i class="bi bi-shield-check"></i> Izin Aktif</span>
              @else
                <span class="access-badge locked"><i class="bi bi-lock-fill"></i> Butuh Humas</span>
              @endif
              <span class="module-live-pill" style="background:rgba(99,102,241,0.1); color:#6366f1; border:1px solid rgba(99,102,241,0.25);">
                <span class="pulse-dot" style="background:#6366f1; box-shadow:0 0 0 0 rgba(99,102,241,0.6);"></span> Publik
              </span>
            </div>
          </div>

          <h3 class="module-card-name">WEB PROFIL &amp; HUMAS</h3>
          <p class="module-card-subtitle">Etalase Publik &amp; Manajemen Publikasi</p>

          <div class="kpi-row">
            <div class="kpi-item">
              <span class="kpi-label">Total Berita &amp; Rilis</span>
              <span class="kpi-val" style="color:#6366f1;">{{ $totalBerita }}</span>
              <span class="kpi-sub">Artikel Terpublikasi</span>
            </div>
            <div class="kpi-item">
              <span class="kpi-label">Hero Banner Aktif</span>
              <span class="kpi-val" style="color:#0ea5e9;">{{ $totalBannerAktif }}</span>
              <span class="kpi-sub">Slide Halaman Depan</span>
            </div>
            <div class="kpi-item" style="grid-column: span 2; margin-top:6px;">
              <span class="kpi-label">Berita Terakhir</span>
              <span style="font-size:12.5px; font-weight:800; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block; margin-top:2px;">
                {{ $beritaTerbaru ? $beritaTerbaru->judul : 'Belum ada rilis berita' }}
              </span>
              <span class="kpi-sub">{{ $beritaTerbaru ? \Carbon\Carbon::parse($beritaTerbaru->created_at)->diffForHumans() : '-' }}</span>
            </div>
          </div>
        </div>

        <div class="module-actions">
          @if($canAccessWeb)
            <a href="/admin/berita" class="btn-launch-primary" style="background:linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
              <i class="bi bi-newspaper"></i> Kelola Berita &amp; Rilis <i class="bi bi-arrow-right-short" style="font-size:18px;"></i>
            </a>
            <div class="btn-launch-secondary-row">
              <a href="/admin/banner" class="btn-launch-secondary">
                <i class="bi bi-images" style="color:#0ea5e9;"></i> Banner Hero
              </a>
              <a href="/" target="_blank" class="btn-launch-secondary">
                <i class="bi bi-box-arrow-up-right" style="color:#10b981;"></i> Lihat Website
              </a>
            </div>
          @else
            <button type="button" class="btn-launch-primary btn-locked" disabled title="Akses ditolak: Hanya untuk Tim Humas, Webmaster, & Pimpinan">
              <i class="bi bi-lock-fill"></i> Akses Terbatas (Khusus Tim Humas)
            </button>
            <div class="btn-launch-secondary-row">
              <span class="btn-launch-secondary btn-locked"><i class="bi bi-lock"></i> Banner Terkunci</span>
              <a href="/" target="_blank" class="btn-launch-secondary">
                <i class="bi bi-box-arrow-up-right" style="color:#10b981;"></i> Lihat Website
              </a>
            </div>
          @endif
        </div>
      </div>

    </div>

    {{-- Section 2: Roadmap Modul Masa Depan --}}
    <div class="portal-section-head">
      <div>
        <h2 class="portal-section-title">
          <div class="portal-section-title-icon" style="background:rgba(245,158,11,0.12); color:#d97706;">
            <i class="bi bi-diagram-3-fill"></i>
          </div>
          <span>Roadmap Modul DCC SMKN 1 AN</span>
        </h2>
        <span style="font-size:12px; color:var(--dcc-text-sub); font-weight:600;">
          Modul yang telah dipetakan dalam arsitektur digital sekolah dan siap diaktifkan secara bertahap.
        </span>
      </div>
      <span class="portal-section-badge">5 Modul Rancang Bangun</span>
    </div>

    <div class="portal-roadmap-grid">
      @foreach($futureModules as $mod)
        <div class="roadmap-card">
          <div>
            <div class="roadmap-top">
              <div class="roadmap-icon-wrap" style="background:rgba(0,0,0,0.03); color:{{ $mod['color'] }};">
                <i class="bi {{ $mod['icon'] }}"></i>
              </div>
              <span class="roadmap-badge">{{ $mod['badge'] }}</span>
            </div>
            <h4 class="roadmap-title">{{ $mod['name'] }}</h4>
            <div class="roadmap-sub">{{ $mod['subtitle'] }}</div>
            <p class="roadmap-desc">{{ $mod['description'] }}</p>
          </div>
          
          <div>
            <div class="roadmap-progress-wrap">
              <div style="display:flex; justify-content:space-between; font-size:9.5px; font-weight:700; color:var(--dcc-text-sub); margin-bottom:4px;">
                <span>Tahap Desain</span>
                <span style="color:{{ $mod['color'] }};">Fase 1</span>
              </div>
              <div class="roadmap-progress-bar">
                <div class="roadmap-progress-fill" style="width: 35%; background:{{ $mod['color'] }};"></div>
              </div>
            </div>

            <div class="roadmap-lead">
              <i class="bi bi-person-check-fill" style="color:{{ $mod['color'] }}; font-size:11px;"></i>
              <span>PIC: {{ $mod['lead'] }}</span>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Section 3: System Status & Quick Links --}}
    <div class="system-strip">
      <div class="system-strip-left">
        <div style="display:inline-flex; align-items:center; gap:7px; font-weight:800; color:#10b981;">
          <span class="pulse-dot"></span> Infrastruktur Stabil
        </div>
        <div style="opacity:0.4;">|</div>
        <div>Core Engine: <strong>Laravel v{{ app()->version() }}</strong></div>
        <div style="opacity:0.4;">|</div>
        <div>Runtime: <strong>PHP v{{ PHP_VERSION }}</strong></div>
        <div style="opacity:0.4;">|</div>
        <div>Zona Waktu: <strong>Asia/Jakarta (WIB)</strong></div>
      </div>

      <div class="system-strip-links">
        <a href="/audit" class="system-strip-link" title="Audit Trail Log Keamanan">
          <i class="bi bi-shield-check" style="color:#64748b;"></i> Audit Log
        </a>
        <a href="/backup" class="system-strip-link" title="Manajemen Backup Database">
          <i class="bi bi-database-check" style="color:#0284c7;"></i> Backup DB
        </a>
        <a href="/pengaturan-sekolah" class="system-strip-link" title="Profil Identitas Sekolah">
          <i class="bi bi-building-gear" style="color:#10b981;"></i> Profil SMKN 1
        </a>
      </div>
    </div>

  </main>

  <script>
    // Live Clock WIB Script Digital Monospace
    function updatePortalClock() {
      const el = document.getElementById('portalLiveClock');
      if (!el) return;
      const now = new Date();
      const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        timeZone: 'Asia/Jakarta'
      };
      el.innerHTML = '<i class="bi bi-clock-history" style="color:#38bdf8;"></i> ' + now.toLocaleString('id-ID', options) + ' WIB';
    }
    updatePortalClock();
    setInterval(updatePortalClock, 1000);
  </script>

</body>
</html>
