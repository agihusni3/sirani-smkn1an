<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Official Portal SMKN 1 Air Naningan - Pusat Pendidikan Vokasi Unggulan berbasis Rekayasa Teknologi, Agro-Industri, dan Otomotif di Kabupaten Tanggamus.')">
    <title>@yield('title', 'SMKN 1 Air Naningan — Precision Vocational Academy')</title>
    
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <!-- Google Fonts: Plus Jakarta Sans & Space Grotesk -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            /* ── Clean Industrial Architectural Palette ── */
            --bg-body: #f8fafc;
            --bg-surface: #ffffff;
            --bg-surface-alt: #f1f5f9;
            --border-main: #e2e8f0;
            --border-hover: #cbd5e1;
            
            --text-dark: #090d16;
            --text-body: #334155;
            --text-muted: #64748b;
            --text-subtle: #94a3b8;
            
            /* Industrial Accents */
            --brand-navy: #0f172a;
            --brand-blue: #2563eb;
            --brand-blue-subtle: #eff6ff;
            --brand-amber: #b45309;
            --brand-amber-subtle: #fef3c7;
            --brand-emerald: #047857;
            --brand-emerald-subtle: #ecfdf5;
            
            --radius-xl: 20px;
            --radius-lg: 14px;
            --radius-md: 10px;
            --radius-sm: 6px;
            
            --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-tech: 'Space Grotesk', monospace, sans-serif;
            --shadow-subtle: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
            --shadow-card: 0 4px 20px -2px rgba(15, 23, 42, 0.06), 0 2px 6px -1px rgba(15, 23, 42, 0.04);
            --shadow-hover: 0 12px 30px -4px rgba(15, 23, 42, 0.12), 0 4px 10px -2px rgba(15, 23, 42, 0.06);
            --transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            overflow-x: hidden;
            max-width: 100%;
            width: 100%;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg-body);
            color: var(--text-body);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .container {
            max-width: 1360px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ── Micro Animations ── */
        @keyframes live-pulse {
            0% { transform: scale(0.9); opacity: 1; box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); opacity: 1; box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.9); opacity: 1; box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        @keyframes badge-glow {
            0%, 100% { box-shadow: 0 0 6px rgba(220, 38, 38, 0.4); }
            50% { box-shadow: 0 0 14px rgba(220, 38, 38, 0.85); }
        }
        @keyframes drawer-fade-down {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .live-status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
            animation: live-pulse 2s infinite ease-out;
            flex-shrink: 0;
            display: inline-block;
        }

        /* Main Navbar */
        .site-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-main);
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
            transition: var(--transition);
        }

        .navbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
            gap: 16px;
        }

        .brand-logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            flex-shrink: 0;
            min-width: 0;
        }

        .brand-logo-img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.08));
            transition: transform 0.25s ease;
            flex-shrink: 0;
        }

        .brand-logo-area:hover .brand-logo-img {
            transform: scale(1.05);
        }

        .brand-titles {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
        }

        .brand-titles h1 {
            font-size: 0.98rem;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.01em;
            line-height: 1.15;
            white-space: nowrap;
        }

        .brand-titles p {
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 0.05em;
            text-transform: uppercase;
            white-space: nowrap;
            font-family: var(--font-tech);
        }

        /* Nav Menu: Modern Frosted Pill Bar */
        .main-menu {
            display: flex;
            align-items: center;
            gap: 2px;
            list-style: none;
            background: rgba(241, 245, 249, 0.75);
            padding: 4px 6px;
            border-radius: 30px;
            border: 1px solid var(--border-main);
            flex-shrink: 1;
        }

        .main-menu li a {
            padding: 6px 9px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-body);
            border-radius: 20px;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 5px;
            position: relative;
            white-space: nowrap;
        }

        .main-menu li a:hover {
            color: var(--brand-blue);
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .main-menu li.active a {
            color: #ffffff;
            background: var(--brand-blue);
            font-weight: 700;
            box-shadow: 0 2px 10px rgba(37, 99, 235, 0.35);
        }

        .main-menu li.active a:hover {
            color: #ffffff;
            background: #1d4ed8;
        }

        .badge-live-ppdb {
            background: #dc2626;
            color: #ffffff;
            font-size: 0.62rem;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 12px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            animation: badge-glow 2s infinite;
        }

        .main-menu li.active .badge-live-ppdb {
            background: #ffffff;
            color: #dc2626;
            animation: none;
        }

        /* ── Dropdown Submenu System (Master Plan) ── */
        .nav-dropdown {
            position: relative;
        }

        .nav-dropdown::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            height: 12px;
        }

        .nav-dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%) translateY(8px);
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-main);
            border-radius: var(--radius-md);
            box-shadow: 0 20px 35px -5px rgba(15, 23, 42, 0.16), 0 8px 16px -2px rgba(15, 23, 42, 0.08);
            padding: 8px;
            min-width: 290px;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 1050;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .nav-dropdown-menu.wide {
            min-width: 320px;
        }

        .nav-dropdown:hover .nav-dropdown-menu {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateX(-50%) translateY(0);
        }

        .dropdown-item-card {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            padding: 8px 12px !important;
            border-radius: 8px !important;
            background: transparent !important;
            color: var(--text-dark) !important;
            box-shadow: none !important;
            transition: var(--transition) !important;
            text-align: left;
            width: 100%;
            box-sizing: border-box;
        }

        .dropdown-item-card:hover {
            background: var(--brand-blue-subtle) !important;
            color: var(--brand-blue) !important;
            transform: translateX(2px);
        }

        .dropdown-item-icon {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .dropdown-item-info {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .dropdown-item-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
        }

        .dropdown-item-card:hover .dropdown-item-title {
            color: var(--brand-blue);
        }

        .dropdown-item-sub {
            font-size: 0.68rem;
            color: var(--text-muted);
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .badge-subtle-status {
            font-family: var(--font-tech);
            font-size: 0.6rem;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 10px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .badge-subtle-status.aktif {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid rgba(5,150,105,0.25);
        }
        .badge-subtle-status.segera {
            background: #eff6ff;
            color: #4338ca;
            border: 1px solid rgba(67,56,202,0.25);
        }
        .badge-subtle-status.live {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid rgba(220,38,38,0.25);
        }

        .dropdown-header-label {
            font-family: var(--font-tech);
            font-size: 0.68rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 4px 10px 2px 10px;
        }

        .nav-right-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .btn-nav-presensi {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 13px;
            font-size: 0.8rem;
            font-weight: 700;
            border-radius: 24px;
            background: #ffffff;
            color: var(--text-dark);
            border: 1px solid var(--border-main);
            box-shadow: var(--shadow-subtle);
            transition: var(--transition);
            white-space: nowrap;
        }

        .btn-nav-presensi:hover {
            background: var(--bg-surface-alt);
            border-color: var(--border-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        .btn-nav-sirani {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 15px;
            font-size: 0.8rem;
            font-weight: 700;
            border-radius: 24px;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.18);
            transition: var(--transition);
            white-space: nowrap;
        }

        .btn-nav-sirani:hover {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.28);
            color: #ffffff;
        }

        .btn-industrial {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 0.86rem;
            font-weight: 700;
            border-radius: var(--radius-md);
            transition: var(--transition);
            cursor: pointer;
            border: 1px solid transparent;
        }

        .btn-industrial-dark {
            background: var(--brand-navy);
            color: #ffffff;
        }

        .btn-industrial-dark:hover {
            background: #1e293b;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
        }

        .btn-industrial-outline {
            background: #ffffff;
            color: var(--text-dark);
            border-color: var(--border-main);
        }

        .btn-industrial-outline:hover {
            background: var(--bg-surface-alt);
            border-color: var(--border-hover);
        }

        .btn-industrial-primary {
            background: var(--brand-blue);
            color: #ffffff;
        }

        .btn-industrial-primary:hover {
            background: #1d4ed8;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
        }

        /* Mobile Hamburger Toggle */
        .mobile-hamburger {
            display: none;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: var(--bg-surface-alt);
            border: 1px solid var(--border-main);
            color: var(--text-dark);
            font-size: 1.25rem;
            cursor: pointer;
            transition: var(--transition);
            flex-shrink: 0;
        }

        .mobile-hamburger:hover {
            background: var(--brand-blue-subtle);
            color: var(--brand-blue);
            border-color: var(--brand-blue);
        }

        .mobile-hamburger.is-active {
            background: var(--brand-blue);
            color: #ffffff;
            border-color: var(--brand-blue);
        }

        /* ── Mobile Drawer Sheet ── */
        .mobile-drawer {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-top: 1px solid var(--border-main);
            border-bottom: 2px solid var(--brand-blue);
            box-shadow: 0 25px 35px -10px rgba(15, 23, 42, 0.18);
            max-height: calc(100vh - 110px);
            overflow-y: auto;
            z-index: 999;
            animation: drawer-fade-down 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .mobile-drawer.open {
            display: block;
        }

        .mobile-drawer-inner {
            padding: 18px 20px 24px 20px;
            max-width: 720px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .drawer-announcement {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--brand-blue-subtle);
            border: 1px solid rgba(37, 99, 235, 0.15);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.76rem;
            font-weight: 700;
            color: var(--brand-blue);
        }

        .drawer-menu-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .drawer-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: var(--radius-md);
            background: #ffffff;
            border: 1px solid var(--border-main);
            transition: var(--transition);
        }

        .drawer-nav-item:hover, .drawer-nav-item.active {
            background: var(--brand-blue-subtle);
            border-color: var(--brand-blue);
        }

        .drawer-nav-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: var(--bg-surface-alt);
            color: var(--brand-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .drawer-nav-icon.ppdb-icon {
            background: #fee2e2;
            color: #dc2626;
        }

        .drawer-nav-text {
            flex: 1;
            min-width: 0;
        }

        .drawer-nav-title {
            display: block;
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.2;
        }

        .drawer-nav-desc {
            display: block;
            font-size: 0.72rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .drawer-chevron {
            font-size: 0.75rem;
            color: var(--text-subtle);
        }

        .drawer-gateways {
            padding-top: 8px;
            border-top: 1px solid var(--border-main);
        }

        .drawer-section-label {
            font-family: var(--font-tech);
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            margin-bottom: 10px;
        }

        .drawer-gateway-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .drawer-gateway-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-main);
            background: #ffffff;
            transition: var(--transition);
        }

        .drawer-gateway-card:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-card);
        }

        .gateway-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .gateway-icon-box.blue {
            background: var(--brand-blue-subtle);
            color: var(--brand-blue);
        }

        .gateway-icon-box.dark {
            background: var(--brand-navy);
            color: #60a5fa;
        }

        .gateway-card-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.2;
        }

        .gateway-card-sub {
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .drawer-helpdesk {
            padding-top: 4px;
        }

        .btn-drawer-whatsapp {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #059669;
            color: #ffffff;
            padding: 11px 16px;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 0.86rem;
            transition: var(--transition);
            text-align: center;
        }

        .btn-drawer-whatsapp:hover {
            background: #047857;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }

        /* ── Universal Responsive Layout Helpers ── */
        .contact-layout-grid {
            display: grid;
            grid-template-columns: 1fr 1.25fr;
            gap: 32px;
            align-items: stretch;
        }

        .jurusan-hero-grid {
            display: grid;
            grid-template-columns: 1.35fr 1fr;
            gap: 32px;
            align-items: center;
        }

        /* Main Section Divider */
        .section-header-clean {
            margin-bottom: 32px;
        }

        .section-tag {
            font-family: var(--font-tech);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--brand-blue);
            display: inline-block;
            margin-bottom: 8px;
        }

        .section-title-large {
            font-size: clamp(1.8rem, 3.2vw, 2.5rem);
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.03em;
            line-height: 1.2;
        }

        /* ── Universal Bento & Industrial Cards ── */
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 24px;
            margin-bottom: 40px;
        }

        .bento-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-main);
            border-radius: var(--radius-lg);
            padding: 28px;
            box-shadow: var(--shadow-card);
            position: relative;
            overflow: hidden;
            transition: var(--transition);
        }

        .bento-card:hover {
            box-shadow: var(--shadow-hover);
            border-color: var(--border-hover);
        }

        .btn-primary-glow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: var(--brand-blue);
            color: #ffffff;
            font-weight: 700;
            border-radius: var(--radius-md);
            transition: var(--transition);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
            border: 1px solid transparent;
            cursor: pointer;
        }

        .btn-primary-glow:hover {
            background: #1d4ed8;
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35);
            transform: translateY(-1px);
            color: #ffffff;
        }

        .badge-pulse {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background: var(--brand-blue-subtle);
            color: var(--brand-blue);
            font-family: var(--font-tech);
            font-size: 0.72rem;
            font-weight: 700;
            border-radius: 20px;
            border: 1px solid rgba(37, 99, 235, 0.2);
            letter-spacing: 0.05em;
        }

        /* Form Inputs */
        input[type="text"], input[type="email"], input[type="number"], input[type="date"], select, textarea {
            font-family: var(--font-main);
            color: var(--text-dark);
            background: #ffffff;
            border: 1px solid var(--border-main);
            border-radius: var(--radius-md);
            transition: var(--transition);
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="number"]:focus, input[type="date"]:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        @media (max-width: 900px) {
            .bento-grid {
                display: flex;
                flex-direction: column;
                gap: 20px;
            }
            .bento-card {
                grid-column: span 12 !important;
            }
        }

        /* ── Clean Light Minimalist Footer ── */
        .site-footer {
            margin-top: 100px;
            background: #ffffff;
            color: #475569;
            border-top: 1px solid #e2e8f0;
            box-shadow: 0 -4px 20px -5px rgba(15, 23, 42, 0.03);
            padding: 70px 0 32px 0;
            position: relative;
        }

        .site-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #1e3a8a 0%, #2563eb 50%, #38bdf8 100%);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.3fr;
            gap: 48px;
            margin-bottom: 50px;
        }

        .footer-brand-title {
            font-size: 1.12rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.01em;
            line-height: 1.25;
        }

        .footer-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            color: #334155;
            padding: 3px 10px;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            margin-top: 4px;
        }

        .footer-badge .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        }

        .footer-desc {
            font-size: 0.88rem;
            color: #64748b;
            line-height: 1.65;
            margin-top: 14px;
            margin-bottom: 22px;
        }

        .footer-social-links {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .footer-social-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none;
        }

        .footer-social-btn:hover {
            transform: translateY(-3px);
        }

        .footer-social-btn.fb:hover {
            background: #1877f2;
            color: #ffffff;
            border-color: #1877f2;
            box-shadow: 0 8px 16px -3px rgba(24, 119, 242, 0.35);
        }

        .footer-social-btn.ig:hover {
            background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 8px 16px -3px rgba(220, 39, 67, 0.35);
        }

        .footer-social-btn.yt:hover {
            background: #ef4444;
            color: #ffffff;
            border-color: #ef4444;
            box-shadow: 0 8px 16px -3px rgba(239, 68, 68, 0.35);
        }

        .footer-col h3 {
            font-size: 0.85rem;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 18px;
            position: relative;
            padding-bottom: 8px;
        }

        .footer-col h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 24px;
            height: 2px;
            background: var(--brand-blue);
            border-radius: 2px;
        }

        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
            font-size: 0.88rem;
            padding: 0;
            margin: 0;
        }

        .footer-col ul li a {
            color: #64748b;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .footer-col ul li a::before {
            content: '›';
            font-size: 1.1rem;
            line-height: 1;
            color: #94a3b8;
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .footer-col ul li a:hover {
            color: var(--brand-blue);
            transform: translateX(4px);
        }

        .footer-col ul li a:hover::before {
            color: var(--brand-blue);
        }

        .footer-contact-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .footer-contact-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 0.86rem;
            color: #475569;
            line-height: 1.55;
        }

        .footer-contact-icon {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
        }

        .footer-contact-icon.geo { background: #fef2f2; color: #ef4444; }
        .footer-contact-icon.tel { background: #eff6ff; color: #2563eb; }
        .footer-contact-icon.mail { background: #fffbeb; color: #d97706; }

        .footer-helpdesk-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            margin-top: 6px;
            transition: all 0.2s ease;
        }

        .footer-helpdesk-btn:hover {
            background: #059669;
            color: #ffffff;
            border-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px -2px rgba(5, 150, 105, 0.25);
        }

        .footer-bottom-bar {
            border-top: 1px solid #f1f5f9;
            padding-top: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            font-size: 0.82rem;
            color: #64748b;
        }

        .footer-bottom-bar strong {
            color: #1e293b;
            font-weight: 600;
        }

        .footer-badge-sys {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 4px;
            margin-left: 6px;
        }

        /* ── Responsive Media Queries ── */
        @media (max-width: 1220px) {
            .main-menu { display: none !important; }
            .desktop-nav-actions { display: none !important; }
            .mobile-hamburger { display: flex !important; }
        }

        @media (min-width: 1221px) {
            #navMobileDrawer { display: none !important; }
        }

        @media (max-width: 1024px) {
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .contact-layout-grid { grid-template-columns: 1fr !important; gap: 24px; }
            .jurusan-hero-grid { grid-template-columns: 1fr !important; gap: 24px; }
        }

        @media (max-width: 640px) {
            .container { padding: 0 12px; }
            .navbar-inner { height: 60px; gap: 8px; }
            .brand-logo-area { gap: 8px; max-width: calc(100% - 46px); flex-shrink: 1 !important; }
            .brand-logo-img { width: 34px; height: 34px; }
            .brand-titles h1 { font-size: 0.8rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .brand-titles p { font-size: 0.56rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .nav-right-actions { margin-left: auto; flex-shrink: 0; }
            .mobile-hamburger { width: 36px; height: 36px; font-size: 1.05rem; }
            .footer-grid { grid-template-columns: 1fr; }
            .footer-bottom-bar { flex-direction: column; gap: 10px; text-align: center; }
        }

        @media (max-width: 380px) {
            .brand-titles p { display: none; }
            .brand-titles h1 { font-size: 0.78rem; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sticky Header Navbar -->
    <header class="site-header">
        <div class="container">
            <div class="navbar-inner">
                <!-- Brand Identity -->
                <a href="{{ route('web.beranda') }}" class="brand-logo-area">
                    <img src="{{ asset('logo.png') }}" alt="Logo SMKN 1 Air Naningan" class="brand-logo-img" onerror="this.src='{{ asset('img/logo.png') }}'">
                    <div class="brand-titles">
                        <h1>SMK NEGERI 1 AIR NANINGAN</h1>
                        <p>Vocational Technical Academy • Tanggamus</p>
                    </div>
                </a>

                <!-- Desktop Nav Menu with Master Plan Ecosystem Submenus -->
                <ul class="main-menu">
                    <li class="{{ request()->routeIs('web.beranda') ? 'active' : '' }}">
                        <a href="{{ route('web.beranda') }}">Beranda</a>
                    </li>

                    <!-- Profil Dropdown -->
                    <li class="nav-dropdown {{ request()->routeIs('web.profil*') ? 'active' : '' }}">
                        <a href="{{ route('web.profil') }}" class="nav-dropdown-toggle">
                            Profil &amp; Lembaga <i class="fa-solid fa-chevron-down" style="font-size: 0.62rem; opacity: 0.7;"></i>
                        </a>
                        <div class="nav-dropdown-menu">
                            <div class="dropdown-header-label">Kelembagaan Kampus</div>
                            <a href="{{ route('web.profil') }}#visimisi" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: var(--brand-blue-subtle); color: var(--brand-blue);">
                                    <i class="fa-solid fa-bullseye"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Visi, Misi &amp; Sejarah</div>
                                    <div class="dropdown-item-sub">Landasan nilai &amp; komitmen mutu</div>
                                </div>
                            </a>
                            <a href="{{ route('web.profil') }}#fasilitas" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #fef3c7; color: #b45309;">
                                    <i class="fa-solid fa-building-columns"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Sarana, Prasarana &amp; Lab</div>
                                    <div class="dropdown-item-sub">Bengkel industri &amp; ruang praktik</div>
                                </div>
                            </a>
                            <a href="{{ route('web.profil') }}#gtk" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #ecfdf5; color: #047857;">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Pendidik &amp; Tenaga Kependidikan</div>
                                    <div class="dropdown-item-sub">Dewan guru kejuruan &amp; staf TU</div>
                                </div>
                            </a>
                        </div>
                    </li>

                    <!-- Konsentrasi Keahlian Dropdown -->
                    <li class="nav-dropdown {{ request()->routeIs('web.jurusan*') ? 'active' : '' }}">
                        <a href="{{ route('web.jurusan.index') }}" class="nav-dropdown-toggle">
                            Konsentrasi Keahlian <i class="fa-solid fa-chevron-down" style="font-size: 0.62rem; opacity: 0.7;"></i>
                        </a>
                        <div class="nav-dropdown-menu">
                            <div class="dropdown-header-label">3 Program Vokasi Unggulan</div>
                            <a href="{{ route('web.jurusan.show', 'rpl') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #eff6ff; color: #2563eb;">
                                    <i class="fa-solid fa-code"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Rekayasa Perangkat Lunak (RPL)</div>
                                    <div class="dropdown-item-sub">Coding, web app &amp; software engineer</div>
                                </div>
                            </a>
                            <a href="{{ route('web.jurusan.show', 'aphp') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #ecfdf5; color: #059669;">
                                    <i class="fa-solid fa-seedling"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Agribisnis Pengolahan Hasil Pertanian</div>
                                    <div class="dropdown-item-sub">Olahan pangan modern &amp; agroindustri</div>
                                </div>
                            </a>
                            <a href="{{ route('web.jurusan.show', 'tsm') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #fef3c7; color: #d97706;">
                                    <i class="fa-solid fa-motorcycle"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Teknik Sepeda Motor (TSM)</div>
                                    <div class="dropdown-item-sub">Mekanik otomotif standar pabrikan</div>
                                </div>
                            </a>
                            <a href="{{ route('web.jurusan.index') }}" class="dropdown-item-card" style="border-top: 1px solid var(--border-main);">
                                <div class="dropdown-item-icon" style="background: var(--bg-surface-alt); color: var(--text-dark);">
                                    <i class="fa-solid fa-certificate"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Sertifikasi Profesi LSP-P1 BNSP</div>
                                    <div class="dropdown-item-sub">Standar kompetensi uji kompetensi kejuruan</div>
                                </div>
                            </a>
                            <a href="{{ route('web.ekosistem.show', 'teaching-factory') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #fff7ed; color: #ea580c;">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Teaching Factory (TeFa) Produk</div>
                                    <div class="dropdown-item-sub">Showcase karya inovasi &amp; unit produksi siswa</div>
                                </div>
                            </a>
                        </div>
                    </li>

                    <!-- Ekosistem Digital (Master Plan) Mega Dropdown -->
                    <li class="nav-dropdown {{ request()->routeIs('web.ekosistem*') || request()->routeIs('ppdb*') ? 'active' : '' }}">
                        <a href="{{ route('web.ekosistem.index') }}" class="nav-dropdown-toggle">
                            <span>Ekosistem Digital</span>
                            <span class="badge-subtle-status live" style="font-size: 0.58rem; padding: 1px 5px;">Plan</span>
                            <i class="fa-solid fa-chevron-down" style="font-size: 0.62rem; opacity: 0.7;"></i>
                        </a>
                        <div class="nav-dropdown-menu wide">
                            <div class="dropdown-header-label">Modul Sistem Informasi Terpadu</div>

                            <!-- Modul Aktif: PPDB -->
                            <a href="{{ route('ppdb.index') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #fee2e2; color: #dc2626;">
                                    <i class="fa-solid fa-file-signature"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">
                                        <span>PPDB Online 2026/2027</span>
                                        <span class="badge-subtle-status live">Buka</span>
                                    </div>
                                    <div class="dropdown-item-sub">Pendaftaran mandiri siswa baru online</div>
                                </div>
                            </a>

                            <!-- Modul Aktif: Presensi Mandiri SIRANI -->
                            <a href="{{ route('portal.ortu.index') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: var(--brand-blue-subtle); color: var(--brand-blue);">
                                    <i class="fa-solid fa-id-card-clip"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">
                                        <span>Presensi Digital (SIRANI)</span>
                                        <span class="badge-subtle-status aktif">Aktif</span>
                                    </div>
                                    <div class="dropdown-item-sub">Kartu RFID &amp; gateway kehadiran orang tua</div>
                                </div>
                            </a>

                            <!-- Modul Roadmap: SIM-PKL -->
                            <a href="{{ route('web.ekosistem.show', 'sim-pkl') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #e0f2fe; color: #0284c7;">
                                    <i class="fa-solid fa-business-time"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">
                                        <span>SIM-PKL &amp; Magang Industri</span>
                                        <span class="badge-subtle-status segera">Segera</span>
                                    </div>
                                    <div class="dropdown-item-sub">GPS geotagging &amp; jurnal kegiatan siswa</div>
                                </div>
                            </a>

                            <!-- Modul Roadmap: Smart Toolman -->
                            <a href="{{ route('web.ekosistem.show', 'smart-toolman') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #fef3c7; color: #b45309;">
                                    <i class="fa-solid fa-wrench"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">
                                        <span>Smart Toolman RFID Bengkel</span>
                                        <span class="badge-subtle-status segera">Segera</span>
                                    </div>
                                    <div class="dropdown-item-sub">Peminjaman alat bengkel APHP, TSM, RPL</div>
                                </div>
                            </a>

                            <!-- Modul Roadmap: BKK Tracer -->
                            <a href="{{ route('web.ekosistem.show', 'bkk-tracer') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #ecfdf5; color: #059669;">
                                    <i class="fa-solid fa-user-tie"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">
                                        <span>BKK &amp; Tracer Study Alumni</span>
                                        <span class="badge-subtle-status segera">Segera</span>
                                    </div>
                                    <div class="dropdown-item-sub">Bursa kerja vokasi &amp; pelacakan karir BMW</div>
                                </div>
                            </a>

                            <!-- Modul Roadmap: E-Library -->
                            <a href="{{ route('web.ekosistem.show', 'e-library') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #f5f3ff; color: #7c3aed;">
                                    <i class="fa-solid fa-book-bookmark"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">
                                        <span>Perpustakaan Digital &amp; Modul</span>
                                        <span class="badge-subtle-status segera">Segera</span>
                                    </div>
                                    <div class="dropdown-item-sub">E-book kejuruan &amp; jobsheet instruksi bengkel</div>
                                </div>
                            </a>

                            <!-- Peta Arsitektur Link -->
                            <a href="{{ route('web.ekosistem.index') }}" class="dropdown-item-card" style="border-top: 1px solid var(--border-main); justify-content: center; background: var(--bg-surface-alt) !important; padding: 8px !important;">
                                <span style="font-family: var(--font-tech); font-size: 0.72rem; font-weight: 700; color: var(--brand-blue);">
                                    <i class="fa-solid fa-diagram-project" style="margin-right: 4px;"></i> Lihat Seluruh Peta Arsitektur Ekosistem &rarr;
                                </span>
                            </a>
                        </div>
                    </li>

                    <!-- Warta Dropdown -->
                    <li class="nav-dropdown {{ request()->routeIs('web.berita*') ? 'active' : '' }}">
                        <a href="{{ route('web.berita.index') }}" class="nav-dropdown-toggle">
                            Warta &amp; Prestasi <i class="fa-solid fa-chevron-down" style="font-size: 0.62rem; opacity: 0.7;"></i>
                        </a>
                        <div class="nav-dropdown-menu">
                            <a href="{{ route('web.berita.index') }}" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #eff6ff; color: #2563eb;">
                                    <i class="fa-solid fa-newspaper"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Kabar &amp; Pengumuman Sekolah</div>
                                    <div class="dropdown-item-sub">Agenda kegiatan resmi dinas &amp; sekolah</div>
                                </div>
                            </a>
                            <a href="{{ route('web.berita.index') }}?kategori=Prestasi" class="dropdown-item-card">
                                <div class="dropdown-item-icon" style="background: #fef3c7; color: #b45309;">
                                    <i class="fa-solid fa-trophy"></i>
                                </div>
                                <div class="dropdown-item-info">
                                    <div class="dropdown-item-title">Galeri Prestasi Siswa &amp; Guru</div>
                                    <div class="dropdown-item-sub">Raihan kejuaraan LKS &amp; inovasi vokasi</div>
                                </div>
                            </a>
                        </div>
                    </li>

                    <!-- Kontak -->
                    <li class="{{ request()->routeIs('web.kontak') ? 'active' : '' }}">
                        <a href="{{ route('web.kontak') }}">Kontak</a>
                    </li>
                </ul>

                <!-- Desktop Action Gateways & Mobile Hamburger -->
                <div class="nav-right-actions">
                    <div class="desktop-nav-actions">
                        <a href="{{ route('portal.ortu.index') }}" class="btn-nav-presensi" title="Cek Presensi Mandiri Siswa &amp; Orang Tua">
                            <i class="fa-solid fa-id-card-clip" style="color: var(--brand-blue);"></i>
                            <span>Cek Presensi</span>
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn-nav-sirani" title="Masuk Sistem SIRANI GTK &amp; Guru">
                            <i class="fa-solid fa-shield-halved" style="color: #60a5fa;"></i>
                            <span>SIRANI Hub</span>
                        </a>
                    </div>

                    <button class="mobile-hamburger" id="navToggleBtn" aria-label="Menu Navigasi Mobile">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- 3. Modern Responsive Mobile Drawer (Master Plan Structured) -->
        <div id="navMobileDrawer" class="mobile-drawer">
            <div class="mobile-drawer-inner">
                <!-- Status Badge -->
                <div class="drawer-announcement">
                    <span class="live-status-dot"></span>
                    <span>Master Plan Ekosistem Digital SMKN 1 Air Naningan</span>
                </div>

                <!-- Group 1: Navigasi Kampus Utama -->
                <div class="drawer-gateways" style="padding-top: 0; border-top: none;">
                    <div class="drawer-section-label">Navigasi Kampus Utama</div>
                    <div class="drawer-menu-grid">
                        <a href="{{ route('web.beranda') }}" class="drawer-nav-item {{ request()->routeIs('web.beranda') ? 'active' : '' }}">
                            <div class="drawer-nav-icon"><i class="fa-solid fa-house"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Beranda Utama</span>
                                <span class="drawer-nav-desc">Portal resmi kampus kejuruan</span>
                            </div>
                            <i class="fa-solid fa-chevron-right drawer-chevron"></i>
                        </a>

                        <a href="{{ route('web.profil') }}" class="drawer-nav-item {{ request()->routeIs('web.profil') ? 'active' : '' }}">
                            <div class="drawer-nav-icon"><i class="fa-solid fa-school"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Profil &amp; Fasilitas</span>
                                <span class="drawer-nav-desc">Visi misi, sarana &amp; bengkel industri</span>
                            </div>
                            <i class="fa-solid fa-chevron-right drawer-chevron"></i>
                        </a>

                        <a href="{{ route('web.jurusan.index') }}" class="drawer-nav-item {{ request()->routeIs('web.jurusan*') ? 'active' : '' }}">
                            <div class="drawer-nav-icon"><i class="fa-solid fa-microchip"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Konsentrasi Keahlian</span>
                                <span class="drawer-nav-desc">RPL • APHP • TSM • LSP BNSP</span>
                            </div>
                            <i class="fa-solid fa-chevron-right drawer-chevron"></i>
                        </a>

                        <a href="{{ route('web.berita.index') }}" class="drawer-nav-item {{ request()->routeIs('web.berita*') ? 'active' : '' }}">
                            <div class="drawer-nav-icon"><i class="fa-solid fa-newspaper"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Warta &amp; Agenda</span>
                                <span class="drawer-nav-desc">Informasi kegiatan dan prestasi</span>
                            </div>
                            <i class="fa-solid fa-chevron-right drawer-chevron"></i>
                        </a>

                        <a href="{{ route('web.kontak') }}" class="drawer-nav-item {{ request()->routeIs('web.kontak') ? 'active' : '' }}">
                            <div class="drawer-nav-icon"><i class="fa-solid fa-location-dot"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Kontak &amp; Peta Kampus</span>
                                <span class="drawer-nav-desc">Alamat, peta &amp; saluran telepon</span>
                            </div>
                            <i class="fa-solid fa-chevron-right drawer-chevron"></i>
                        </a>
                    </div>
                </div>

                <!-- Group 2: Modul Ekosistem Digital Master Plan -->
                <div class="drawer-gateways">
                    <div class="drawer-section-label" style="display: flex; align-items: center; justify-content: space-between;">
                        <span>Ekosistem Layanan (Master Plan)</span>
                        <a href="{{ route('web.ekosistem.index') }}" style="color: var(--brand-blue); text-transform: none; font-size: 0.68rem; font-weight: 700;">Lihat Peta Blueprint &rarr;</a>
                    </div>
                    <div class="drawer-menu-grid">
                        <a href="{{ route('ppdb.index') }}" class="drawer-nav-item {{ request()->routeIs('ppdb*') ? 'active' : '' }}">
                            <div class="drawer-nav-icon ppdb-icon"><i class="fa-solid fa-file-signature"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">PPDB Online 2026/2027</span>
                                <span class="drawer-nav-desc">Pendaftaran Online Bebas Biaya</span>
                            </div>
                            <span class="badge-subtle-status live">Buka</span>
                        </a>

                        <a href="{{ route('portal.ortu.index') }}" class="drawer-nav-item">
                            <div class="drawer-nav-icon" style="background: var(--brand-blue-subtle); color: var(--brand-blue);"><i class="fa-solid fa-id-card-clip"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Presensi Digital SIRANI</span>
                                <span class="drawer-nav-desc">Kartu RFID &amp; portal orang tua</span>
                            </div>
                            <span class="badge-subtle-status aktif">Aktif</span>
                        </a>

                        <a href="{{ route('web.ekosistem.show', 'sim-pkl') }}" class="drawer-nav-item">
                            <div class="drawer-nav-icon" style="background: #e0f2fe; color: #0284c7;"><i class="fa-solid fa-business-time"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">SIM-PKL Magang Industri</span>
                                <span class="drawer-nav-desc">Presensi GPS &amp; logbook praktik</span>
                            </div>
                            <span class="badge-subtle-status segera">Segera</span>
                        </a>

                        <a href="{{ route('web.ekosistem.show', 'smart-toolman') }}" class="drawer-nav-item">
                            <div class="drawer-nav-icon" style="background: #fef3c7; color: #b45309;"><i class="fa-solid fa-wrench"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">Smart Toolman RFID Bengkel</span>
                                <span class="drawer-nav-desc">Peminjaman alat lab &amp; bengkel</span>
                            </div>
                            <span class="badge-subtle-status segera">Segera</span>
                        </a>

                        <a href="{{ route('web.ekosistem.show', 'bkk-tracer') }}" class="drawer-nav-item">
                            <div class="drawer-nav-icon" style="background: #ecfdf5; color: #059669;"><i class="fa-solid fa-user-tie"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">BKK &amp; Tracer Study Alumni</span>
                                <span class="drawer-nav-desc">Bursa kerja &amp; survei lulusan BMW</span>
                            </div>
                            <span class="badge-subtle-status segera">Segera</span>
                        </a>

                        <a href="{{ route('web.ekosistem.show', 'e-library') }}" class="drawer-nav-item">
                            <div class="drawer-nav-icon" style="background: #f5f3ff; color: #7c3aed;"><i class="fa-solid fa-book-bookmark"></i></div>
                            <div class="drawer-nav-text">
                                <span class="drawer-nav-title">E-Perpustakaan &amp; Modul</span>
                                <span class="drawer-nav-desc">E-book &amp; jobsheet bengkel</span>
                            </div>
                            <span class="badge-subtle-status segera">Segera</span>
                        </a>
                    </div>
                </div>

                <!-- Group 3: Akses Gerbang Mandiri & Dasbor -->
                <div class="drawer-gateways">
                    <div class="drawer-section-label">Akses Gerbang Mandiri &amp; Dasbor</div>
                    <div class="drawer-gateway-grid">
                        <a href="{{ route('portal.ortu.index') }}" class="drawer-gateway-card">
                            <div class="gateway-icon-box blue">
                                <i class="fa-solid fa-id-card-clip"></i>
                            </div>
                            <div>
                                <div class="gateway-card-title">Cek Presensi Mandiri</div>
                                <div class="gateway-card-sub">Khusus Siswa &amp; Orang Tua</div>
                            </div>
                        </a>

                        <a href="{{ route('dashboard') }}" class="drawer-gateway-card">
                            <div class="gateway-icon-box dark">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <div class="gateway-card-title">SIRANI Hub System</div>
                                <div class="gateway-card-sub">Login GTK, Guru &amp; Presensi</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Group 4: WhatsApp Helpdesk -->
                <div class="drawer-helpdesk">
                    <a href="https://wa.me/6281234567890?text=Halo%20Admin%20SMKN%201%20Air%20Naningan,%20saya%20ingin%20bertanya%20seputar%20sekolah" target="_blank" class="btn-drawer-whatsapp">
                        <i class="fa-brands fa-whatsapp" style="font-size: 1.1rem;"></i> Chat Helpdesk WhatsApp Kampus
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <!-- 3. Clean Light Minimalist Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                        <img src="{{ asset('logo.png') }}" alt="Logo SMK" style="width: 44px; height: 44px; object-fit: contain;">
                        <div>
                            <div class="footer-brand-title">SMK NEGERI 1 AIR NANINGAN</div>
                            <div class="footer-badge">
                                <span class="status-dot"></span>
                                <span>Akreditasi B • NPSN: {{ $sekolah->npsn ?? '69888999' }}</span>
                            </div>
                        </div>
                    </div>
                    <p class="footer-desc">
                        Lembaga pendidikan kejuruan vokasi negeri unggulan di Kabupaten Tanggamus, berdedikasi melatih tenaga kerja terampil siap kerja berstandar industri dan mencetak wirausahawan mandiri.
                    </p>
                    <div class="footer-social-links">
                        <a href="https://facebook.com" target="_blank" rel="noopener" class="footer-social-btn fb" title="Facebook SMKN 1 Air Naningan"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://instagram.com" target="_blank" rel="noopener" class="footer-social-btn ig" title="Instagram SMKN 1 Air Naningan"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://youtube.com" target="_blank" rel="noopener" class="footer-social-btn yt" title="YouTube SMKN 1 Air Naningan"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

                <div class="footer-col">
                    <h3>Program Kejuruan</h3>
                    <ul>
                        <li><a href="{{ route('web.jurusan.show', 'rpl') }}">Rekayasa Perangkat Lunak (RPL)</a></li>
                        <li><a href="{{ route('web.jurusan.show', 'aphp') }}">Agribisnis Pengolahan Hasil (APHP)</a></li>
                        <li><a href="{{ route('web.jurusan.show', 'tsm') }}">Teknik Sepeda Motor (TSM)</a></li>
                        <li><a href="{{ route('web.jurusan.index') }}">Standar Kompetensi LSP BNSP</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h3>Layanan Mandiri</h3>
                    <ul>
                        <li><a href="{{ route('ppdb.index') }}">PPDB Online 2026/2027</a></li>
                        <li><a href="{{ route('ppdb.status') }}">Cek Status Seleksi</a></li>
                        <li><a href="{{ route('portal.ortu.index') }}">Portal Presensi Siswa</a></li>
                        <li><a href="{{ route('web.berita.index') }}">Warta &amp; Agenda Sekolah</a></li>
                        <li><a href="{{ route('login') }}">Masuk Dasbor SIRANI</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h3>Kampus &amp; Kontak</h3>
                    <div class="footer-contact-list">
                        <div class="footer-contact-item">
                            <span class="footer-contact-icon geo"><i class="fa-solid fa-location-dot"></i></span>
                            <span>{{ $sekolah->alamat ?? 'Jl. Raya Air Naningan, Kec. Air Naningan, Kab. Tanggamus, Lampung 35379' }}</span>
                        </div>
                        <div class="footer-contact-item">
                            <span class="footer-contact-icon tel"><i class="fa-solid fa-phone"></i></span>
                            <span>{{ $sekolah->telepon ?? '0812-3456-7890' }}</span>
                        </div>
                        <div class="footer-contact-item">
                            <span class="footer-contact-icon mail"><i class="fa-solid fa-envelope"></i></span>
                            <span>{{ $sekolah->email ?? 'info@smkn1airnaningan.sch.id' }}</span>
                        </div>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $sekolah->telepon ?? '6281234567890') }}?text=Halo%20Admin%20SMKN%201%20Air%20Naningan" target="_blank" rel="noopener" class="footer-helpdesk-btn">
                            <i class="fa-brands fa-whatsapp" style="font-size: 1.05rem;"></i> Helpdesk Layanan Cepat
                        </a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom-bar">
                <div>
                    &copy; {{ date('Y') }} <strong>SMKN 1 Air Naningan</strong>. Hak Cipta Dilindungi.
                    <span class="footer-badge-sys">SIRANI Integrated System</span>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const navBtn = document.getElementById('navToggleBtn');
        const navDrawer = document.getElementById('navMobileDrawer');
        if (navBtn && navDrawer) {
            navBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = navDrawer.classList.toggle('open');
                navBtn.classList.toggle('is-active', isOpen);
                const icon = navBtn.querySelector('i');
                if (icon) {
                    if (isOpen) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-xmark');
                    } else {
                        icon.classList.remove('fa-xmark');
                        icon.classList.add('fa-bars');
                    }
                }
            });

            // Close when clicking outside
            document.addEventListener('click', (e) => {
                if (navDrawer.classList.contains('open') && !navDrawer.contains(e.target) && !navBtn.contains(e.target)) {
                    navDrawer.classList.remove('open');
                    navBtn.classList.remove('is-active');
                    const icon = navBtn.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-xmark');
                        icon.classList.add('fa-bars');
                    }
                }
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
